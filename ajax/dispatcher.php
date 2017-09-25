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
		$data['id_price'] 	= isset($_POST['id_price']) 	? $_POST['id_price'] 	: '';
		$data['price_base'] = isset($_POST['price_base'])	? $_POST['price_base']	: '';
		$data['type'] 		= isset($_POST['type']) 		? $_POST['type'] 		: '';
		$data['date_prog']	= $isProg ? $_POST['prog_date'].' '.$_POST['prog_time']	: '';
		$data['promo'] 		= isset($_POST['promo']) 		? $_POST['promo'] 		: '';
		$data['notes'] 		= isset($_POST['notes']) 		? $_POST['notes'] 		: '';
		$data['ids_rr'] 	= '[]';
		$data['ids_aa'] 	= '[]';
		$data['state'] 		= $isProg ? 1 : 0;

		if(!is_numeric($data['id_client']) || $data['id_client'] <= 0){
			$rsp['msg'] = 'Elige el cliente';

		} else if((!isset($org->lat) || !is_numeric($org->lat)) || (!isset($org->lng) || !is_numeric($org->lng)) || !isset($org->adr)){
			$rsp['msg'] = 'Origen incorrecto';

		} else if((!isset($dst->lat) || !is_numeric($dst->lat)) || (!isset($dst->lng) || !is_numeric($dst->lng)) || !isset($dst->adr)){
			$rsp['msg'] = 'Destino incorrecto';

		} else if(!is_numeric($data['price_base'])){
			$rsp['msg'] = 'Precio incorrecto';

		} else if(!is_numeric($data['type']) || $data['type'] < 0 || $data['type'] > 3){
			$rsp['msg'] = 'Tipo de servicio incorrecto';

		} else {
			$data['org_lat'] = $org->lat;
			$data['org_lng'] = $org->lng;
			$data['org_adr'] = $org->adr;
			$data['dst_lat'] = $dst->lat;
			$data['dst_lng'] = $dst->lng;
			$data['dst_adr'] = $dst->adr;

			if(is_numeric($id) && $id > 0){
				if($db->update('races', $data, $id)){
					$rsp['ok'] = true;
					$rsp['id'] = $id;
				} else {
					$rsp['msg'] = 'DB:UPDATE : Error interno';
				}
			} else {
				if($db->insert('races', $data)){
					$rsp['ok'] = true;
					$rsp['id'] = $db->lastID();
				} else {
					$rsp['msg'] = 'DB:INSERT : Error interno';
				}
			}

		}

		$rsp['data'] = $data;
		break;
	
	case 'get_cost':
		$org = isset($_POST['org']) ? $_POST['org'] : '';
		$dst = isset($_POST['dst']) ? $_POST['dst'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		if(is_array($org)){
			if(is_array($dst)){
				if(is_numeric($type)){
					include('../../inc/estimator.php');
					$fe = new Estimator();
					$fe->setOrg($org['lat'], $org['lng']);
					$fe->setDst($dst['lat'], $dst['lng']);
					$fe->setType($type);
					$price = $fe->estimate(true);
					if($price['id'] > 0){
						$rsp['ok'] = true;
						$rsp['price'] = $price;
					}
				}
			}
		}
		break;
	
}

echo json_encode($rsp);