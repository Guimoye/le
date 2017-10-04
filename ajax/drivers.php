<?php
include("_base.php");
include("../inc/arrays.php");
global $db,$user,$rsp,$st_driver;

$action = _POST('action');
$id 	= _POST('id',0);

switch($action){
	case 'add':
		$isEdit = (is_numeric($id) && $id > 0);

		$data = array();
		$data['name'] 			    = _POST('name');
		$data['surname'] 			= _POST('surname');
		$data['date_birth'] 		= _POST('date_birth');
		$data['dni'] 			    = _POST('dni');
		$data['ruc'] 			    = _POST('ruc');
		$data['driver_licence'] 	= _POST('driver_licence');
		$data['city'] 			    = _POST('city');
		$data['district'] 			= _POST('district');
		$data['phone_cell'] 		= _POST('phone_cell');
		$data['phone_house'] 		= _POST('phone_house');
		$data['email'] 			    = _POST('email');
		$data['civil_status'] 		= _POST('civil_status');
		$data['wife_name'] 			= _POST('wife_name');
		$data['wife_dni'] 			= _POST('wife_dni');
		$data['bank_name'] 			= _POST('bank_name');
		$data['bank_account'] 		= _POST('bank_account');

		$data['gt_name'] 			= _POST('gt_name');
		$data['gt_dni'] 			= _POST('gt_dni');
		$data['gt_city'] 		    = _POST('gt_city');
		$data['gt_district'] 		= _POST('gt_district');
		$data['gt_address'] 		= _POST('gt_address');
		$data['gt_phone'] 			= _POST('gt_phone');
		$data['gt_email'] 			= _POST('gt_email');
		$data['gt_job_place'] 		= _POST('gt_job_place');
		$data['gt_job_role'] 		= _POST('gt_job_role');
		$data['gt_job_address'] 	= _POST('gt_job_address');
		$data['gt_job_city'] 	    = _POST('gt_job_city');
		$data['gt_job_district'] 	= _POST('gt_job_district');
		$data['gt_job_phone'] 		= _POST('gt_job_phone');
		$data['gt_job_boss_name'] 	= _POST('gt_job_boss_name');
		$data['gt_job_boss_role'] 	= _POST('gt_job_boss_role');
		$data['gt_job_boss_email'] 	= _POST('gt_job_boss_email');

		$data['id_brand'] 			= _POST('id_brand');
		$data['id_model'] 			= _POST('id_model');
		$data['vh_plate'] 			= _POST('vh_plate');
		$data['vh_year'] 			= _POST('vh_year');
		$data['vh_color'] 			= _POST('vh_color');
		$data['vh_engine_number'] 	= _POST('vh_engine_number');
		$data['vh_serial_chassis'] 	= _POST('vh_serial_chassis');
		$data['vh_fuel'] 			= _POST('vh_fuel');
		$data['vh_gps_number'] 		= _POST('vh_gps_number');
		$data['state']			    = _POST('state', 1);

		if(empty($data['name'])){
			$rsp['msg'] = '<b>Nombre</b> incorrecto';

		} else if(empty($data['surname'])){
			$rsp['msg'] = '<b>Apellido</b> incorrecto';

		} else if(strlen($data['dni']) != 8){
			$rsp['msg'] = '<b>DNI</b> incorrecto';

		} else if(strlen($data['driver_licence']) < 8){
			$rsp['msg'] = 'Número de <b>Licencia de conducir</b> incorrecta';

		} else {
			if($isEdit){
				if($db->update('drivers', $data, $id)){
					$rsp['ok'] = true;
					$rsp['id'] = $id;
				} else {
					$rsp['msg'] = 'Se produjo un error al editar';
				}
			} else {
				$data['id_user'] 	= $user->id;
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
			$WHERE .= " AND (CONCAT(name,surname) LIKE '$word')";
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
						<td>
						    <a href="'.$link.'">'.$o->name.' '.$o->surname.'</a>
						    <div style="font-size:12px;color:red">Mant. 20,000km</div>
						</td>
						<td> '.$o->vh_plate.' </td>
						<td> --- </td>
						<td> '.$o->date_added.' </td>
						<td> --- </td>
						<td> '.$stg->coin.' -.-- </td>
						<td class="nowrap">
						    <a href="'.$link.'" class="btn btn-outline btn-circle dark btn-sm font-md"><i class="fa fa-eye"></i></a>
						    <a href="dues_rental.php?id='.$o->id.'" class="btn btn-outline btn-circle dark btn-sm font-md"><i class="fa fa-bar-chart"></i></a>
						    
							<span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MDriver.edit(Pager.items['.$o->id.']);">
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