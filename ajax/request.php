<?php
include("_base.php");
include("../inc/stock.php");
global $db,$user,$rsp;

//$_POST = $_GET;

$action = isset($_POST['action']) 	? $_POST['action'] 	: '';

switch($action){
	case 'send':
		$id 	    = @$_POST['id']         ?: ''; // ID del pedido
		$ordpros 	= @$_POST['ordpros']    ?: ''; // ...

        $isEdit     = (is_numeric($id) && $id > 0);

		$data = [];
		$data['id_user'] 	= $user->id;
		$data['id_table']	= @$_POST['id_table'] ?: '';
		$data['price']	    = @$_POST['price'] ?: '';
		$data['notes']	    = @$_POST['notes'] ?: '';
		$data['state'] 		= 1;

		if(!is_numeric($data['id_table']) || $data['id_table'] <= 0){
			$rsp['msg'] = 'Elige una mesa';

		} else if(!is_numeric($data['price']) || $data['price'] < 0){
			$rsp['msg'] = 'Precio incorrecto';

        } else {

			if($isEdit){
				if($db->update('orders', $data, $id)){
					$rsp['ok'] = true;
					$rsp['id'] = (int) $id;
				} else {
					$rsp['msg'] = 'DB:UPDATE : Error interno';
				}
			} else {
				if($db->insert('orders', $data)){
					$rsp['ok'] = true;
					$rsp['id'] = (int) $db->lastID();
				} else {
					$rsp['msg'] = 'DB:INSERT : Error interno';
				}
			}

            if($rsp['ok'] && is_array($ordpros)){
                $stk = new Stock();

                // Agregar productos a la orden
			    foreach($ordpros as $k => $v){
			        $dtop = [];
			        $dtop['id_order']   = $rsp['id'];
			        $dtop['id_propre']  = $v['id_propre'];
			        $dtop['quantity']   = $v['quantity'];
			        $dtop['price_unit'] = $v['price_unit'];
			        $dtop['price_total']= $v['price_total'];
			        $dtop['notes']      = $v['notes'];
			        if(isset($v['id']) && is_numeric($v['id']) && $v['id'] > 0){
			            $db->update('ordpros', $dtop, $v['id']);
                    } else {
                        $dtop['state'] = 1;
                        if($db->insert('ordpros', $dtop)){
                            $stk->byOrdpro($db->lastID()); //TODO: Descontamos el stock
                        }
                    }
                };

                $rsp['order'] = getOrderByTable($data['id_table']);
            }

		}

		$rsp['data'] = $data;
		break;

	case 'search_products':
		$id_category = isset($_POST['id_category']) ? $_POST['id_category'] : '';
		$query = isset($_POST['query']) ? $_POST['query'] : '';
        if((is_numeric($id_category) && $id_category > 0) || !empty($query)){
            $WHERE = "";
            if(is_numeric($id_category) && $id_category > 0)
                $WHERE .= " AND id_category = $id_category";
            else {
                $q = '%'.str_replace(' ', '%', $query).'%';
                $WHERE .= " AND name LIKE '$q'";
            }

            $products = [];
            $os = $db->get("SELECT * FROM products WHERE id_branch = $user->id_branch $WHERE AND state = 1 LIMIT 5");
            while($o = $os->fetch_object()){
                $o->propres = $db->arr("SELECT * FROM propres WHERE id_product = $o->id AND state = 1");
                $products[] = $o;
            }
            $rsp['products'] = $products;
            $rsp['ok'] = true;
        } else $rsp['msg'] = 'Identificador inválido';
		break;

	case 'get_tables':
        $rooms = [];

        $SQL = "SELECT ta.*,
                       od.id od_id
                FROM tables ta
                  INNER JOIN rooms ro ON ro.id = ta.id_room
                  LEFT JOIN orders od ON od.id_table = ta.id AND od.state = 1
                WHERE ro.id_branch = $user->id_branch AND ta.state = 1
                GROUP BY ta.id
                ORDER BY ta.name";
        $os = $db->get($SQL);
        while($o = $os->fetch_object()){
            $total_items = 0;
            if(is_numeric($o->od_id) && $o->od_id > 0){
                $o->state = 2;
                $total_items = $db->total("SELECT id FROM ordpros WHERE id_order = $o->od_id AND state != 0");
            }
            $o->total_items = $total_items;
            $rooms[$o->id_room][] = $o;
        }
        $rsp['rooms'] = $rooms;
        $rsp['ok'] = true;
		break;

    // Obtener informacion de una mesa, pedidos
    case 'get_table':
        $id = @$_POST['id'] ?: ''; // ID del pedido
        if(is_numeric($id) && $id > 0){
            $rsp['ok'] = true;
            $rsp['order'] = getOrderByTable($id);
        }
        break;

    // Liberar mesa
    case 'enable_table':
        $id_order = @$_POST['id_order'] ?: ''; // ID del pedido
        if(is_numeric($id_order) && $id_order > 0){
            $rsp['ok'] = true;
            if($db->query("UPDATE orders SET state = 2 WHERE id = $id_order")){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error al liberar mesa';
        } else $rsp['msg'] = 'ID_table invalido';
        break;

    case 'remove_ordpro':
        $id = @$_POST['id'] ?: ''; // ID del pedido
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('ordpros', ['state'=>0,'annul_reason'=>$reason], $id)){

                if(isset($_POST['back_stock'])){
                    $stk = new Stock();
                    $stk->backStock($id); //TODO: Regresamos el stock
                }

                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno';
        } else $rsp['msg'] = 'ID Inválido';
        break;
	
}

function getOrderByTable($id_table){
    global $db;
    $SQL = "SELECT od.*,
                   us.name 'user'
            FROM orders od
              LEFT JOIN users us ON us.id = od.id_user
            WHERE od.id_table = $id_table AND od.state = 1";
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
            $ordpros[] = $o;
        }
        $order->ordpros = $ordpros;
        return $order;
    } else return null;
}

echo json_encode($rsp);