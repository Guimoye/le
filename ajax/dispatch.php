<?php
include("_base.php");
global $db,$user,$rsp;

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
	
	case 'get_task':
		$id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            $SQL = "SELECT ta.*,
                           dr.name dr_name,
                           dr.surname dr_surname
                    FROM tasks ta
                      LEFT JOIN drivers dr ON dr.id = ta.id_driver
                    WHERE ta.id = $id";
            $task = $db->o($SQL);
            if($task){

                // CLIENTE
                if($task->id_client > 0){
                    $task->client = $db->o('clients', $task->id_client);
                }

                // CONDUCTOR
                if($task->id_driver > 0){
                    $task->driver = [
                        'id'        => $task->id_driver,
                        'name'      => $task->dr_name,
                        'surname'   => $task->dr_surname
                    ];
                }
                
                // Hora programado
                $time = strtotime($task->date_prog);
                $task->prog_date = date('Y-m-d', $time);
                $task->prog_time = date('H:i', $time);

                $rsp['task'] = $task;
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'No se pudo reconocer';
        } else $rsp['msg'] = 'Identificador inválido';
		break;
	
}

echo json_encode($rsp);