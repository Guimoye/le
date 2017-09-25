<?php
include("_base.php");
include("../inc/arrays.php");
global $db,$user,$rsp,$st_driver;

$action = isset($_POST['action']) 	? $_POST['action'] 	: '';
$id 	= (isset($_POST['id']) 		? $_POST['id'] 		: 0 );

switch($action){
	case 'add':
        checkEditPerm('clients');
		$isEdit = (is_numeric($id) && $id > 0);

		$data = array();
		$data['name']       = isset($_POST['name']) 	? trim($_POST['name']) 		: '';
		$data['dni'] 		= isset($_POST['dni']) 		? trim($_POST['dni']) 		: '';
		$data['address']    = isset($_POST['address'])  ? trim($_POST['address'])   : '';
		$data['phone']      = isset($_POST['phone']) 	? trim($_POST['phone']) 	: '';
		$data['email']      = isset($_POST['email'])	? trim($_POST['email'])		: '';
		$data['state']      = 1;

		if(!empty($data['name'])){
            if($isEdit){
                if($db->update('clients', $data, $id)){
                    $rsp['ok'] = true;
                    $rsp['id'] = $id;
                    $rsp['client'] = $db->o('clients', $id);
                } else {
                    $rsp['msg'] = 'Se produjo un error al editar';
                }
            } else {
                $data['id_user'] = $user->id;
                if($db->insert('clients', $data)){
                    $new_id = $db->lastID();
                    $rsp['ok'] = true;
                    $rsp['id'] = $new_id;
                    $rsp['client'] = $db->o('clients', $new_id);
                } else {
                    $rsp['msg'] = 'Se produjo un error al registrar';
                }
            }
		} else $rsp['msg'] = '<b>Nombre</b> incorrecto';
		break;
	
	case 'remove':
        checkEditPerm('clients');
		if(is_numeric($id) && $id > 0){
			if($db->query("UPDATE clients SET state = 0 WHERE id = $id")){
				$rsp['ok'] = true;
			} else $rsp['msg'] = 'Error DB :: No se pudo eliminar';
		} else $rsp['msg'] = 'No se puede reconocer';
		break;
	
	case 'pager':
		$max 		= isset($_POST['max']) 		&& is_numeric($_POST['max'])	? $_POST['max'] 	: 10;
		$page 		= isset($_POST['page'])		&& is_numeric($_POST['page'])	? $_POST['page']	: 1;
		$date_from	= isset($_POST['date_from'])	? trim($_POST['date_from'])	: '';
		$date_to 	= isset($_POST['date_to']) 		? trim($_POST['date_to'])	: '';
		$word		= isset($_POST['word'])			? trim($_POST['word'])		: '';
		$state		= isset($_POST['state'])		? trim($_POST['state'])		: '';

		$offset = ($page - 1) * $max; // Offet

		$rsp['total'] = 0;

		$WHERE = "state > 0";

		if(!empty($date_from) && !empty($date_to)){
			$WHERE .= " AND DATE(date_added) between '$date_from' and '$date_to'";
		}
		if(!empty($word)){
			$word = '%'.str_replace(' ', '%', $word).'%';
			$WHERE .= " AND (CONCAT(name,surname,email,phone) LIKE '$word')";
		}
		if(is_numeric($state)){
			$WHERE .= " AND state = $state";
		}

		$SQL = "SELECT * FROM clients WHERE $WHERE ORDER BY id DESC LIMIT $offset,$max";
		$os = $db->get($SQL);

		$table = '';
		$items = [];

        $canEdit = $user->can('add_clients');

		if($os){
			$rsp['total_items'] = $os->num_rows;
			while($o = $os->fetch_object()){
				$link = 'driver.php?id='.$o->id;

				$items[''.$o->id] = $o;

				switch($o->state){
					case 1:	$estado = 'success'; break;
					case 2: $estado = 'danger'; break;
					default:$estado = 'default';
				}

                $table .= '<tr>';
                $table .= '<td> '.$o->id.' </td>';
                $table .= '<td> <div style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis"> '.$o->name.' </div></td>';
                $table .= '<td> '.$o->dni.' </td>';
                $table .= '<td> '.$o->email.' </td>';
                $table .= '<td> '.$o->phone.' </td>';
                $table .= '<td width="155px"> '.$o->date_added.' </td>';
                //$table .= '<td> <span class="label label-sm label-'.$estado.'"> '.$st_client[$o->state].' </span> </td>';
                $table .= '<td>';
                if($canEdit){
                    $table .= '<span class="btn btn-outline btn-circle dark btn-sm" onclick="MClient.edit(Pager.items['.$o->id.']);"><i class="fa fa-pencil"></i></span>';
                }
                $table .= '</td>';
                $table .= '</tr>';
			}
		}

		$rsp['data'] = $table;
		$rsp['items'] = $items;
		break;

	case 'autocomplete':
		$term = isset($_GET['term']) ? trim($_GET['term']) : '';
		$term = empty($term) ? '' : '%'.str_replace(' ', '%', $term).'%';
		$rsp = $db->arr("SELECT * FROM clients WHERE CONCAT(name, phone) LIKE '$term' AND state = 1");
		break;
}

echo json_encode($rsp);