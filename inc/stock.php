<?php
class Stock{

    public $is_debug = false;

    public function __construct(){}

    /**
     * Cambiar el stock
     */
    public function byOrdpro($id_ordpro, $sumar = false){
        global $db;
        $SQL = "SELECT op.quantity op_quantity,
                       op.id id_ordpro,
                       op.id_propre id_propre,
                       pp.*,
                       pr.name pr_name,
                       pr.id_unimed pr_id_unimed,
                       un.name un_name
                FROM ordpros op
                  INNER JOIN propres pp ON pp.id = op.id_propre
                    INNER JOIN products pr ON pr.id = pp.id_product
                      INNER JOIN unimeds un ON un.id = pr.id_unimed
                WHERE op.id = $id_ordpro";
        $o = $db->o($SQL);
        if($o){

            $this->log('<h1>'.$o->pr_name.' - '.$o->name.'</h1>');

            if($o->has_supply == 1){
                $this->ordproHasSupply($o, $sumar);
            }

            /*if($o->has_stock == 1){
                $this->ordproHasStock($o);
            }*/

        } else {
            $this->log('<h1>Ordpro no existe</h1>');
        }
    }

    // Ordpro lleva ingredientes
    private function ordproHasSupply($o, $sumar = false){
        global $db;
        $this->log('<h3 style="background:gold;padding:5px">Lleva ingredientes</h3>');
        $pss = $db->get("SELECT ps.*,
                            su.name su_name,
                            su.cost su_cost,
                            un.name un_name
                     FROM propresups ps
                      INNER JOIN supplies su ON su.id = ps.id_supply
                      INNER JOIN unimeds un ON un.id = ps.id_unimed
                     WHERE id_propre = $o->id_propre");
        while($ps = $pss->fetch_object()){
            $this->log('<hr>');
            $this->log('<b> - '.$ps->su_name.':</b> ');
            $this->log($ps->quantity.' '.$ps->un_name);
            $this->log('<div style="margin-left:10px">');

            // Obtener stock por almacen para este producto, el primero que tenga stock lo usamos para descontar
            $st = $db->o("SELECT st.*,
                                sg.name sg_name,
                                un.id un_id,
                                un.name un_name
                         FROM stocks st
                            INNER JOIN storages sg ON sg.id = st.id_storage
                            INNER JOIN supplies su ON su.id = st.id_supply
                              INNER JOIN unimeds un ON un.id = su.id_unimed
                         WHERE st.id_supply = $ps->id_supply 
                         ORDER BY st.stock DESC
                         LIMIT 1");
            if($st){
                $this->log('<br>Almacen: '.$st->sg_name);
                $this->log('<br>stock: <b>'.$st->stock.'</b> '.$st->un_name);

                $SQL = "SELECT *
                    FROM unimeds_rel
                    WHERE (id_unimed_org = $st->un_id AND id_unimed_dst = $ps->id_unimed)
                        AND state = 1";
                $ur = $db->o($SQL);

                if($ur){

                    $total_rel      = $st->stock * $ur->quantity;

                    $new_total_rel  = $sumar ? ($total_rel + $ps->quantity) : ($total_rel - $ps->quantity);

                    $new_stock      = $new_total_rel / $ur->quantity;

                    // Nuevo stock no debe ser menor a 0
                    if($new_stock < 0){
                        $new_stock = 0;
                        $this->log('<br><font color="red">Nuevo stock negativo</font>');
                    }

                    $this->log('<br>total_rel:      '.$total_rel);
                    $this->log('<br>new_total_rel:  '.$new_total_rel);
                    $this->log('<br>new_stock:      '.$new_stock);
                    $this->log('<br>C. equivalente: '.$ur->quantity);

                    if($db->update('stocks', ['stock'=>$new_stock], $st->id)){
                        $this->log('<br><font color="green">Stock actualizado ('.$st->id.')</font>');
                    } else {
                        $this->log('<br><font color="red">Error al actualizar stock</font>');
                    }

                    //TODO: Kardex
                    $qtt_kardex = ($ps->quantity/$ur->quantity) * $o->op_quantity;
                    if($sumar){
                        $this->kardex($ps->id_supply, 1, $qtt_kardex, $ps->su_cost, 'Pedido anulado '.$o->pr_name, 0, $o->id_ordpro);
                    } else {
                        $this->kardex($ps->id_supply, 2, $qtt_kardex, $ps->su_cost, 'Pedido '.$o->pr_name, 0, $o->id_ordpro);
                    }

                } else {
                    $this->log('<br><font color="red">No hay relacion de unidad de medida</font>');
                }

                //$this->log('<br>'.$st->un_id.' - '.$ps->id_unimed);

            } else {
                $this->log('<b style="color:red">No hay stock en nignun almacen</b>');
            }

            $this->log('</div>');
        }
    }

    // Ordpro lleva control stock
    /*private function ordproHasStock($o){
        global $db;
        $this->log('<h3 style="background:aqua;padding:5px">Lleva control de stock</h3>');
        $this->log('<b> Cantidad:</b>  '.$o->op_quantity.' '.$o->un_name);

        // Obtener stock por almacen para este producto, el primero que tenga stock lo usamos para descontar
        $st = $db->o("SELECT st.*,
                            sg.name sg_name
                     FROM stocks st
                        INNER JOIN storages sg ON sg.id = st.id_storage
                     WHERE st.id_propre = $o->id_propre AND st.stock > 0
                     ORDER BY st.stock DESC
                     LIMIT 1");
        if($st){
            $new_stock = $st->stock - $o->op_quantity;

            $this->log('<br>Almacen: '.$st->sg_name);
            $this->log('<br>stock: <b>'.$st->stock.'</b> '.$o->un_name);
            $this->log('<br>new_stock:      '.$new_stock);

            if($db->update('stocks', ['stock'=>$new_stock], $st->id)){
                $this->log('<br><font color="green">Stock actualizado ('.$st->id.')</font>');
            } else {
                $this->log('<br><font color="red">Error al actualizar stock</font>');
            }

        } else {
            $this->log('<h1>No hay stock en nignun almacen</h1>');
        }
    }*/

    // Regresar stock
    public function backStock($id_ordpro){
        $this->byOrdpro($id_ordpro, true);
    }

    /**
     * Asignar stock por Supply
     */
    public function bySupply($id_supply, $id_storage, $quantity){
        global $db;
        // Agregar/Actualizar stock para este insumo insumo
        if(!is_numeric($id_supply)){
            $this->log('<font color="red">:id_supply no es valido</font>');

        } else if(!is_numeric($id_storage)){
            $this->log('<font color="red">:id_storage no es valido</font>');

        } else if(!is_numeric($quantity)){
            $this->log('<font color="red">:quantity no es valido</font>');

        } else {
            $st = $db->o("SELECT * FROM stocks WHERE id_supply = $id_supply AND id_storage = $id_storage");
            if($st){
                if($db->update('stocks', ['stock' => $st->stock + $quantity], $st->id)){
                    $this->log('<font color="green">Actualizado correctamente</font>');
                    return true;
                } else {
                    $this->log('<font color="red">Error al actualizar</font>');
                }
            } else {
                if($db->insert('stocks', [
                    'id_supply' => $id_supply,
                    'id_storage'=> $id_storage,
                    'stock'     => $quantity
                ])){
                    $this->log('<font color="green">Insertado correctamente</font>');
                    return true;
                } else {
                    $this->log('<font color="red">Error al insertar</font>');
                }
            }
        }
        return false;
    }


    /**
     * Kardex
     */
    public function kardex($id_supply, $type, $quantity, $v_unit, $description, $id_purchase=0,$id_ordpro=0){
        global $db;

        // Obtener ultimo valance
        $o = $db->o("SELECT balance FROM kardex WHERE id_supply = $id_supply ORDER BY id DESC LIMIT 1");
        $balance = $o ? $o->balance : 0;

        switch($type){
            case 1: $balance += $quantity; break; // Ingreso
            case 2: $balance -= $quantity; break; // Salida
            case 3: $balance = $quantity;  break; // Saldo
        }

        // No menos a 0
        if($balance < 0){
            $balance = 0;
        }

        $id_parent = 0;

        // Si es type=2(salida), Aplicar metodo PEPS
        if($type == 2){
            $kdx = $this->discStockKardex($id_supply, $quantity);
            if($kdx){
                $id_parent = $kdx->id;
            } else {
                // No hay stocks
            }
        }

        $stock = ($type==1) ? $quantity : 0;

        $db->insert('kardex', [
            'id_purchase' => $id_purchase,

            'id_supply' => $id_supply,
            'type' => $type,
            'description' => $description,
            'quantity' => $quantity,
            'v_unit' => $v_unit,
            'v_total' => $v_unit*$quantity,
            'balance' => $balance,

            'id_parent' => $id_parent,
            'id_ordpro' => $id_ordpro,

            'stock' => $stock
        ]);
    }

    private function discStockKardex($id_supply, $quantity){
        global $db;
        $kdx = FALSE;
        $os = $db->get("SELECT * FROM kardex WHERE id_supply = $id_supply AND type = 1 AND stock > 0 ORDER BY id");
        while($o = $os->fetch_object()){
            $kdx = $o;

            //echo '<b>id:</b> '.$o->id.' - ';
            //echo '<b>stock:</b> '.$o->stock.' - ';
            //echo '<b>$quantity:</b> '.$quantity.' - ';

            $rest = ($quantity - $o->stock);
            // 1.5 - 2

            if($rest > 0){ // No hay suficiente stock, pero descontamos
                //echo 'No hay sufi stock';
                $db->update('kardex', ['stock'=>0], $o->id);
            } else { // Aca si se encontro el stock requerido
                $db->update('kardex', ['stock'=>($o->stock-$quantity)], $o->id);
                //echo 'si desconto';
                break;
            }

            $quantity = $rest;

            //echo '<hr>';
        }
        return $kdx;
    }

    //TODO: Logs
    private function log($msg){
        if($this->is_debug){
            echo $msg;
        }
    }

}
/**
 * Convertir unida de medida
 *
 * 1kg = 1000gr
 *
 * uso: 180gr
 *
 * equivalente: 180gr/1000gr = 0.18kg
 *
 *
 */


/**
 * Proceso para descuento de stock
 *
 * 1 kg = 1000 gr
 *
 * uso: 200gr
 *
 * convertir a gramos
 *
 * 7kg * 1000gr = 7000gr
 *
 * 7000gr - 200gr = 6800gr
 *
 * 6800gr / 1000gr
 *
 */

/*
 * Proceso descuento stock inverso
 *
 * 1 doc = 12 und
 *
 * uso: 2 doc
 *
 * convertir
 *
 * 80 und / 12 und = 6.666666666666667 doc
 *
 * 6.666666666666667 doc - 2 doc = 4.666666666666667
 *
 * 4.666666666666667 doc * 12 und = 56 und
 *
 */