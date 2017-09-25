<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

	// Ajustes generales
	case 'general':
        checkEditPerm('settings');
		$data = [];
		$data['brand']              = @$_POST['brand'] ?: '';
		$data['coin']               = @$_POST['coin'] ?: '';
		$data['tc']                 = @$_POST['tc'] ?: '';
		$data['ip_local_server']    = @$_POST['ip_local_server'] ?: '';
		$data['comp_name']          = @$_POST['comp_name'] ?: '';
		$data['comp_ruc']           = @$_POST['comp_ruc'] ?: '';
		if(update($data)){
			$rsp['ok'] = true;
		} else $rsp['msg'] = 'Erroe interno::DB';
		break;

	// IGV
	case 'igv':
        checkEditPerm('igv');
		$data = [];
		$data['igv'] = $_POST['igv'];
		if(update($data)){
			$rsp['ok'] = true;
		} else $rsp['msg'] = 'Erroe interno::DB';
		break;
}

// Guardar ajustes
function update($data){
	global $db;
	$ok = true;
	foreach($data as $key => $val){
		if(!$db->query("UPDATE settings SET value = '$val' WHERE name = '$key'")){
			$ok = false;
		}
	}
	return $ok;
}

echo json_encode($rsp);