<?php
include("_base.php");
global $db,$user,$rsp;

$_POST = $_GET;

$action = isset($_POST['action']) 	? $_POST['action'] 	: '';

switch($action){
	case 'request':
		$id 	= isset($_POST['id']) ? $_POST['id'] : ''; // ID De la carrera
		$org	= (object) $_POST['org'];
		$dst 	= (object) $_POST['dst'];

		$isProg = (isset($_POST['prog_date']) && !empty($_POST['prog_date']) && isset($_POST['prog_time']) && !empty($_POST['prog_time']));

		$data = [];
		$data['id_user'] 	= $user->id;
		$data['id_client']	= isset($_POST['id_client']) 	? $_POST['id_client'] 	: '';
		$data['id_driver']	= isset($_POST['id_driver']) 	? $_POST['id_driver'] 	: '';
        $data['name'] 		= isset($_POST['name']) 		? $_POST['name'] 		: '';
		$data['date_prog']	= $isProg ? $_POST['prog_date'].' '.$_POST['prog_time']	: '';
		$data['notes'] 		= isset($_POST['notes']) 		? $_POST['notes'] 		: '';
		$data['state'] 		= 1;

		if(!is_numeric($data['id_client']) || $data['id_client'] <= 0){
			$rsp['msg'] = 'Elige el cliente';

		} else if(!is_numeric($data['id_driver']) || $data['id_driver'] <= 0){
			$rsp['msg'] = 'Elige el conductor';

		} else if(empty($data['name'])){
            $rsp['msg'] = 'Nombre de tarea incorrecto';

        } else if((!isset($org->lat) || !is_numeric($org->lat)) || (!isset($org->lng) || !is_numeric($org->lng)) || !isset($org->adr)){
			$rsp['msg'] = 'Origen incorrecto';

		} else if((!isset($dst->lat) || !is_numeric($dst->lat)) || (!isset($dst->lng) || !is_numeric($dst->lng)) || !isset($dst->adr)){
			$rsp['msg'] = 'Destino incorrecto';

		} else if(!$isProg){
            $rsp['msg'] = 'Fecha incorrecta';

        } else {
			$data['org_lat'] = $org->lat;
			$data['org_lng'] = $org->lng;
			$data['org_adr'] = $org->adr;
			$data['dst_lat'] = $dst->lat;
			$data['dst_lng'] = $dst->lng;
			$data['dst_adr'] = $dst->adr;

			if(is_numeric($id) && $id > 0){
				if($db->update('tasks', $data, $id)){
					$rsp['ok'] = true;
					$rsp['id'] = (int) $id;
				} else {
					$rsp['msg'] = 'DB:UPDATE : Error interno';
				}
			} else {
				if($db->insert('tasks', $data)){
					$rsp['ok'] = true;
					$rsp['id'] = (int) $db->lastID();
				} else {
					$rsp['msg'] = 'DB:INSERT : Error interno';
				}
			}

		}

		$rsp['data'] = $data;
		break;
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

			// Agregar productos a la orden
            if($rsp['ok'] && is_array($ordpros)){
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
			            $db->insert('ordpros', $dtop);
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

	case 'get_orders':
        $id_area = @$_POST['id_area'] ?: ''; // ID del pedido
	    if(is_numeric($id_area) && $id_area > 0){

	        $ordpros = [];

	        $SQL = "SELECT op.*,
                           DATE_FORMAT(op.date_added, '%d-%m-%Y %h:%i %p') date_added,
                           DATE_FORMAT(op.date_dispatched, '%d-%m-%Y %h:%i %p') date_dispatched,
                           pr.name pr_name,
                           pp.name pp_name,
                           us.name us_name,
                           ta.name ta_name
                    FROM ordpros op
                      INNER JOIN orders od ON od.id = op.id_order
                        LEFT JOIN tables ta ON ta.id = od.id_table
                      LEFT JOIN propres pp ON pp.id = op.id_propre
                        LEFT JOIN products pr ON pr.id = pp.id_product
                      INNER JOIN users us ON us.id = od.id_user
                    WHERE pr.id_area = $id_area AND od.state = 1";

	        $os = $db->get($SQL);
	        while($o = $os->fetch_object()){
                $o->time_trans = ($o->state==2) ? (time() - strtotime($o->date_preparing))/60 : 0;
	            $ordpros[$o->id] = $o;
            }

            $rsp['ordpros'] = (Object) $ordpros;
            $rsp['ok'] = true;

        } else $rsp['msg'] = 'Se debe elegir un área de producción';
		break;

    // ***
    case 'prepare':
        $id = @$_POST['id'] ?: ''; // ID del pedido
        $time = @$_POST['time'] ?: ''; // ID del pedido
        if(is_numeric($id) && $id > 0){
            $data = [];
            $data['state'] = 2;
            $data['date_preparing'] = 'NOW()';
            if(is_numeric($time) && $time >= 0) {
                $data['time'] = $time;
            }

            if($db->update('ordpros', $data, $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: UPDATE';
        } else $rsp['msg'] = 'ID Inválido';
        break;
    case 'dispatch':
        $id = @$_POST['id'] ?: ''; // ID del pedido
        if(is_numeric($id) && $id > 0){
            if($db->update('ordpros', ['state'=>3,'date_dispatched'=>'NOW()'], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: UPDATE';
        } else $rsp['msg'] = 'ID Inválido';
        break;

    // Obtener informacion de una mesa, pedidos
    case 'enable_table':
        $id_order = @$_POST['id_order'] ?: ''; // ID del pedido
        if(is_numeric($id_order) && $id_order > 0){
            $rsp['ok'] = true;
            if($db->query("UPDATE orders SET state = 2 WHERE id = $id_order")){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error al liberar mesa';
        }
        break;

    case 'remove_ordpro':
        $id = @$_POST['id'] ?: ''; // ID del pedido
        if(is_numeric($id) && $id > 0){
            if($db->update('ordpros', ['state'=>0], $id)){
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
                WHERE op.id_order = $order->id AND op.state = 1";
        $os = $db->get($SQL);
        $ordpros = [];
        while($o = $os->fetch_object()){
            $ordpros[$o->id_propre] = $o;
        }
        $order->ordpros = (Object) $ordpros;
        return $order;
    } else return null;
}

echo json_encode($rsp);