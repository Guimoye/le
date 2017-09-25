<?php
include("_base.php");
include("../inc/uprint.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'pay':
        $id_order = isset($_POST['id_order']) ? $_POST['id_order'] : '';
        $data = [];
        //$data['id_order']   = isset($_POST['id_order'])     ? $_POST['id_order']    : '';
        $data['id_branch']  = $user->id_branch;
        $data['id_ref']     = $id_order;
        $data['id_ref2']    = isset($_POST['ia'])           ? $_POST['ia']          : '';
        $data['type']       = 1; // Pago de orden
        $data['id_regbox']  = isset($_POST['id_regbox'])    ? $_POST['id_regbox']   : '';
        $data['id_promo']   = isset($_POST['id_promo'])     ? $_POST['id_promo']    : '';
        $data['id_client']  = isset($_POST['id_client'])    ? $_POST['id_client']   : '';
        $data['id_proof']   = isset($_POST['id_proof'])     ? $_POST['id_proof']    : '';
        $data['tip']        = isset($_POST['tip'])          ? $_POST['tip']         : '';
        $data['promo']      = isset($_POST['promo'])        ? $_POST['promo']       : '';
        $data['total']      = isset($_POST['total'])        ? $_POST['total']       : '';
        $data['with_card']  = isset($_POST['with_card'])    ? 1 : 0;
        $data['card']       = isset($_POST['card'])         ? $_POST['card']        : '';
        $data['cash']       = isset($_POST['cash'])         ? $_POST['cash']        : '';
        $data['state']      = 1;

        $ordpros            = isset($_POST['ordpros']) && is_array($_POST['ordpros']) ? $_POST['ordpros']   :  [];

        if(!is_array($ordpros) || count($ordpros) <= 0){
            $rsp['msg'] = 'No se puede pagar una cuenta sin productos.';

        } else if(!is_numeric($data['id_regbox']) || $data['id_regbox'] <= 0){
            $rsp['msg'] = 'No hay cajas habiertas.';

        } else if(!is_numeric($id_order) || $id_order <= 0){
            $rsp['msg'] = 'No se puede reconocer el ID de pedido';

        } else if(!is_numeric($data['id_proof']) || $data['id_proof'] <= 0){
            $rsp['msg'] = 'No se reconoce el comprobante de pago';

        } else if(($data['card']+$data['cash']) != $data['total']){
            $rsp['msg'] = 'El monto en efectivo y/o tarjeta no es igual al importe';

        } else {
            $proof = $db->o('proofs', $data['id_proof']);

            // Cuando el comprobante de pago es FACTURA, se requiere datos del cliente
            if($proof->code == '01' && $data['id_client'] <= 0){
                $rsp['msg'] = 'Para emitir una factura, se requieren datos del cliente';

            } else {
                $total = $data['total'];
                $igv = ($total * $stg->igv) / 100;
                $base = $total - $igv;

                $data['base'] = $base;
                $data['igv'] = $igv;

                if($db->insert('transactions', $data)){
                    $id_transaction = $db->lastID();

                    if($db->update('orders', ['state'=>3], $id_order)){
                        $rsp['transactions'] = getBoxTransactions($data['id_regbox']);
                        $rsp['ok'] = true;

                        // Asociar transaccion al producto de una orden
                        foreach($ordpros as $id_ordpro){
                            $db->update('ordpros', ['id_transaction'=>$id_transaction], $id_ordpro);
                        }

                        //TODO: Imprimir
                        $rsp['data_print'] = UPrint::transaction($id_transaction);

                    } else $rsp['msg'] = 'Error interno :: UPDATE:orders';
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        }
        break;

    case 'open':
    case 'close':
        $id_box = @$_POST['id_box'];
        $id_turn= @$_POST['id_turn'];
        $amount = @$_POST['amount'] ?: 0;
        $notes  = @$_POST['notes'] ?: '';
        if(is_numeric($id_box) && $id_box > 0){
            if(is_numeric($id_turn) && $id_turn > 0){
                if(is_numeric($amount) && $amount >= 0){
                    $SQL = "SELECT * FROM regboxes
                            WHERE id_branch = $user->id_branch AND id_box = $id_box AND id_turn = $id_turn AND state = 1";
                    $rb = $db->o($SQL);

                    if($action == 'close'){
                        if($rb){
                            $data = [];
                            $data['state'] = 2;
                            $data['date_closed'] = 'NOW()';
                            if($db->update('regboxes', $data, $rb->id)){
                                $rsp['ok'] = true;
                            } else $rsp['msg'] = 'Error interno :: UPDATE';
                        } else $rsp['msg'] = 'La caja no ha sido habierta en este turno';
                    } else {
                        if(!$rb){
                            $data = [];
                            $data['id_branch'] = $user->id_branch;
                            $data['id_user'] = $user->id;
                            $data['id_box'] = $id_box;
                            $data['id_turn']= $id_turn;
                            $data['amount'] = $amount;
                            $data['notes'] = $notes;
                            $data['state'] = 1;
                            if($db->insert('regboxes', $data)){
                                $rsp['ok'] = true;
                            } else $rsp['msg'] = 'Error interno :: INTERT';
                        } else $rsp['msg'] = 'Esta caja ya ha sido abierta';
                    }

                } else $rsp['msg'] = 'Monto incorrecto';
            } else $rsp['msg'] = 'Turno incorrecto';
        } else $rsp['msg'] = 'Caja incorrecta';
        break;

    case 'get_tables':
        $rooms = [];

        $SQL = "SELECT ta.*,
                       od.id od_id
                FROM tables ta
                  INNER JOIN rooms ro ON ro.id = ta.id_room
                  INNER JOIN orders od ON od.id_table = ta.id AND (od.state = 1 OR od.state = 2)
                WHERE ro.id_branch = $user->id_branch AND ta.state = 1
                GROUP BY ta.id
                ORDER BY ta.name";
        $os = $db->get($SQL);
        while($o = $os->fetch_object()){
            $o->state = 2;
            $o->total_items = $db->total("SELECT id FROM ordpros WHERE id_order = $o->od_id AND state != 0");
            $rooms[$o->id_room][] = $o;
        }
        $rsp['rooms'] = $rooms;
        $rsp['ok'] = true;
        break;

    // Eliminar producto de una orden
    case 'remove_ordpro':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('ordpros', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: UPDATE';
        } else $rsp['msg'] = 'ID inválido';
        break;

    // Obtener informacion de una mesa, pedidos
    case 'get_table':
        $id = @$_POST['id'] ?: ''; // ID del pedido
        if(is_numeric($id) && $id > 0){
            $rsp['ok'] = true;
            $rsp['order'] = getOrderByTable($id);
        }
        break;

    // Obtener transacciones para esta caja
    case 'get_transactions':
        $id_regbox = isset($_POST['id_regbox']) ? $_POST['id_regbox'] : '';
        if(is_numeric($id_regbox) && $id_regbox > 0){
            $rsp['items'] = getBoxTransactions($id_regbox);
            $rsp['ok'] = true;
        } else $rsp['msg'] = 'No se ha especificado la caja';
        break;

    // Anular transaccion
    case 'annul_transaction':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
        if(is_numeric($id) && $id > 0){
            $o = $db->o('transactions', $id);
            if($o){
                if($db->update('transactions', ['state'=>0,'annul_reason'=>$reason], $id)){
                    $rsp['items'] = getBoxTransactions($o->id_regbox);
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else $rsp['msg'] = 'No se reconoce la transacción';
        } else $rsp['msg'] = 'ID inválido';
        break;

    // Eliminar transaccion
    case 'remove_transaction':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('transactions', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: UPDATE';
        } else $rsp['msg'] = 'ID inválido';
        break;

    // Registrar ingreso
    case 'register_income':
        checkEditPerm('incomes');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['type']   = 2;
        $data['notes']  = @$_POST['notes'];
        $data['total']  = @$_POST['total'];
        $data['coin']   = @$_POST['coin'];
        $data['state']  = 1; // Activo
        if(!empty($data['total'])){
            if($isEdit){
                if($db->update('transactions', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('transactions', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa una cantidad';
        break;

    // Registrar ingreso
    case 'register_expense':
        checkEditPerm('expenses');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['type']   = 3;
        $data['notes']  = @$_POST['notes'];
        $data['total']  = @$_POST['total'];
        $data['coin']   = @$_POST['coin'];
        $data['state']  = 1; // Activo
        if(!empty($data['total'])){
            if($isEdit){
                if($db->update('transactions', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('transactions', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa una cantidad';
        break;

    // Obtener Ordpros de una transaccion o cuenta
    case 'get_ordpros_transaction':
        $id_transaction = isset($_POST['id_transaction']) ? $_POST['id_transaction'] : '';
        if(is_numeric($id_transaction) && $id_transaction > 0){
            $SQL = "SELECT op.*,
                           pr.name product,
                           pp.name propre
                    FROM ordpros op
                      LEFT JOIN propres pp ON pp.id = op.id_propre
                        LEFT JOIN products pr ON pr.id = pp.id_product
                    WHERE id_transaction = $id_transaction";
            $items = $db->arr($SQL);
            if($items){
                $rsp['items'] = $items;
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error al recuperar';
        } else $rsp['msg'] = 'ID inválido';
        break;
}

function getBoxTransactions($id_regbox){
    global $db;
    $SQL = "SELECT tr.*,
                           cl.name client,
                           pr.name proof
                    FROM transactions tr
                      LEFT JOIN clients cl ON cl.id = tr.id_client
                      LEFT JOIN proofs pr ON pr.id = tr.id_proof
                    WHERE tr.id_regbox = $id_regbox AND tr.state = 1
                    ORDER BY tr.date_added DESC";
    return $db->arr($SQL);
}

function getOrderByTable($id_table){
    global $db;
    $SQL = "SELECT od.*,
                   us.name user
            FROM orders od
              LEFT JOIN users us ON us.id = od.id_user
            WHERE od.id_table = $id_table AND (od.state = 1 OR od.state = 2)";
    $order = $db->o($SQL);
    if($order){
        $SQL = "SELECT op.*,
                       pr.name product,
                       pp.name propre
                FROM ordpros op
                  LEFT JOIN propres pp ON pp.id = op.id_propre
                    LEFT JOIN products pr ON pr.id = pp.id_product
                WHERE op.id_order = $order->id AND op.state != 0";
        $os = $db->get($SQL);
        $ordpros = [];
        while($o = $os->fetch_object()){
            $ordpros[$o->id] = $o;
        }
        $order->ordpros = (Object) $ordpros;
        return $order;
    } else return null;
}

echo json_encode($rsp);