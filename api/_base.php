<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//sleep(1);
//usleep(1500000);
include("../../inc/util.php");
include("../../inc/mysql.php");
include("driver.php");
$uu  = new Util();
$db  = new MySQL();
$stg = $db->getSettings();

$dvr = new Driver($_GET['token']);

// Respuesta del servidor
$rsp = array();
$rsp['ok'] = true;
$rsp['msg'] = '---';

header('Content-Type: application/json');

// Si no esta conectado
if(!$dvr->isLogged()){
	$rsp['ok'] = false;
	$rsp['msg'] = 'Usuario no conectado';
	exit(json_encode($rsp));
}