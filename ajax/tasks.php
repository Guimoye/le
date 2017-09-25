<?php
include("_base.php");
include("../inc/arrays.php");
global $db,$user,$rsp,$st_driver;

$action = isset($_POST['action']) 	? $_POST['action'] 	: '';
$id 	= (isset($_POST['id']) 		? $_POST['id'] 		: 0 );

switch($action){
	
	case 'remove':
		if(is_numeric($id) && $id > 0){
			if($db->query("UPDATE tasks SET state = 0 WHERE id = $id")){
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

		$WHERE = "ta.state > 0";

		if(!empty($date_from) && !empty($date_to)){
			$WHERE .= " AND DATE(ta.date_added) between '$date_from' and '$date_to'";
		}
		if(!empty($word)){
			$word = '%'.str_replace(' ', '%', $word).'%';
			$WHERE .= " AND ta.name LIKE '$word'";
		}
		if(is_numeric($state)){
			$WHERE .= " AND ta.state = $state";
		}

		$SQL = "SELECT ta.*,
                       DATE_FORMAT(ta.date_prog, '%d-%m-%Y %h:%i %p') date_prog,
                       cl.name cl_name,
                       cl.surname cl_surname,
                       dr.name dr_name,
                       dr.surname dr_surname
                FROM tasks ta
                  LEFT JOIN clients cl ON cl.id = ta.id_client
                  LEFT JOIN drivers dr ON dr.id = ta.id_driver
                WHERE $WHERE
                ORDER BY ta.date_prog ASC LIMIT $offset,$max";
		$os = $db->get($SQL);

		$table = '';
		$items = [];

		if($os){

			while($o = $os->fetch_object()){
				$link = 'driver.php?id='.$o->id;

				$items[''.$o->id] = $o;

				switch($o->state){
					case 1:	$estado = 'warning'; break;
					case 2: $estado = 'success'; break;
					case 3: $estado = 'primary'; break;
                    default:$estado = 'default';
				}

				$table .= '
					<tr>
						<td> '.$o->id.' </td>
						<td> '.$o->name.' </td>
						<td> '.$o->cl_name.' '.$o->cl_surname.' </td>
						<td> '.$o->dr_name.' '.$o->dr_surname.' </td>
						<td> '.$o->date_prog.' </td>
						<td> <span class="label label-sm label-'.$estado.'"> '.$st_task[$o->state].' </span> </td>
						<td>
							<span class="btn btn-outline btn-circle dark btn-sm" onclick="Task.edit('.$o->id.');">
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
}

echo json_encode($rsp);