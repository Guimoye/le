<?php
include("_base.php");
global $rsp, $dvr;

$api = $_GET['api'];

switch($api){
	
	case 'check':
		$rsp = array_merge($rsp, $dvr->check($_GET['v']));
		break;

	case 'set_firebase':
		$rsp['msg'] = $dvr->setFirebase($_GET['token']);
		break;

	case 'set_location':
		$dvr->setLocation($_GET['lat'], $_GET['lng'], $_GET['course'], $_GET['speed'], $_GET['id_race']);
		break;

	case 'set_state':
		$rsp['ok'] = $dvr->setState($_GET['state']);
		break;

	case 'accept_race':
		$rsp['ok'] = $dvr->acceptRace($_GET['id']);
		break;

}

echo json_encode($rsp);