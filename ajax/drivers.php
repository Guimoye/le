<?php
include("_base.php");
include("../inc/arrays.php");
global $db,$user,$rsp,$st_driver;

$action = isset($_POST['action']) 	? $_POST['action'] 	: '';
$id 	= (isset($_POST['id']) 		? $_POST['id'] 		: 0 );

switch($action){
	case 'add':
		$isEdit = (is_numeric($id) && $id > 0);

		$data = array();
		$data['name'] 			= isset($_POST['name']) 		? trim($_POST['name']) 			: '';
		$data['surname'] 		= isset($_POST['surname']) 		? trim($_POST['surname']) 		: '';
		$data['email'] 			= isset($_POST['email']) 		? trim($_POST['email']) 		: '';
		$data['password'] 		= isset($_POST['password']) 	? trim($_POST['password']) 		: '';
		$data['phone'] 			= isset($_POST['phone']) 		? trim($_POST['phone']) 		: '';
		$data['state']			= isset($_POST['state'])		? trim($_POST['state'])			: 1; // Activo

		if(empty($data['name'])){
			$rsp['msg'] = '<b>Nombre</b> incorrecto';

		} else if(empty($data['surname'])){
			$rsp['msg'] = '<b>Apellido</b> incorrecto';

		} else if(!$uu->isEmail($data['email'])){
			$rsp['msg'] = '<b>Email</b> incorrecto';

		} else if($db->has("SELECT * FROM drivers WHERE email = '".$data['email']."' AND id != '$id'")){
			$rsp['msg'] = '<b>Email</b> ya está en uso';

		} else if(!$isEdit && empty($data['password'])){
			$rsp['msg'] = '<b>Contraseña</b> incorrecta';

		} else if(empty($data['phone'])){
			$rsp['msg'] = '<b>Teléfono</b> incorrecto';

		} else if(!is_numeric($data['state'])){
			$rsp['msg'] = '<b>Estado</b> inválido';

		} else {
			if($isEdit){
				if(empty($data['password'])){
					unset($data['password']);
				} else {
					$data['password'] = md5($data['password']);
				}
				if($db->update('drivers', $data, $id)){
					$rsp['ok'] = true;
					$rsp['id'] = $id;
				} else {
					$rsp['msg'] = 'Se produjo un error al editar';
				}
			} else {
				$data['id_user'] 	= $user->id;
				$data['password'] 	= md5($data['password']);
				if($db->insert('drivers', $data)){
					$rsp['ok'] = true;
					$rsp['id'] = $db->lastID();
				} else {
					$rsp['msg'] = 'Se produjo un error al registrar';
				}
			}
		}

		break;
	
	case 'remove':
		if(is_numeric($id) && $id > 0){
			if($db->query("UPDATE drivers SET state = 0 WHERE id = $id")){
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

		$SQL = "SELECT * FROM drivers WHERE $WHERE ORDER BY id DESC LIMIT $offset,$max";
		$os = $db->get($SQL);

		$table = '';
		$items = [];

		if($os){
			$rsp['total_items'] = $os->num_rows;
			while($o = $os->fetch_object()){
				$link = 'driver.php?id='.$o->id;

				$items[''.$o->id] = $o;

				switch($o->state){
					case 1:	$estado = 'success'; break;
					case 2: $estado = 'default'; break;
					case 3: $estado = 'danger'; break;
					default:$estado = 'default';
				}

				$table .= '
					<tr>
						<td> '.$o->id.' </td>
						<td> <div style="white-space: nowrap; overflow: hidden; text-overflow:ellipsis"> '.$o->name.' '.$o->surname.' </div></td>
						<td> '.$o->email.' </td>
						<td> '.$o->phone.' </td>
						<td> '.$o->date_added.' </td>
						<td> <span class="label label-sm label-'.$estado.'"> '.$st_driver[$o->state].' </span> </td>
						<td> <a href="'.$link.'" class="btn btn-outline btn-circle dark btn-sm"><i class="fa fa-user"></i></a> </td>
						<td>
							<span class="btn btn-outline btn-circle dark btn-sm" onclick="MDriver.edit(Pager.items['.$o->id.']);">
								<i class="fa fa-pencil"></i>
							</span>
						</td>
					</tr>
				';
			}
		}

		$rsp['data'] = $table;
		$rsp['items'] = $items;
		break;

    case 'autocomplete':
        $term = isset($_GET['term']) ? trim($_GET['term']) : '';
        $term = empty($term) ? '' : '%'.str_replace(' ', '%', $term).'%';
        $rsp = $db->arr("SELECT * FROM drivers WHERE CONCAT(name, surname, phone) LIKE '$term' AND state = 1");
        break;
}

echo json_encode($rsp);