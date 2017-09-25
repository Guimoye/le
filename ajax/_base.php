<?php
usleep(300000);
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('../inc/util.php');
include('../inc/mysql.php');
include('../inc/user.php');
$uu		= new Util();
$db 	= new MySQL();
$user 	= new User();
$stg 	= $db->getSettings();

$user->loadPerms(); // Cargar los permisos de este usuario

$rsp = array();
$rsp['ok']  = false;
$rsp['msg'] = '---';

// Verificar si es usuario logeado
// Y que esté habilitado
if(!$user->isLogged() || $user->state != 1){
	$rsp['msg'] = 'Se ha producido un error de sesión. Actualiza la página para continuar..';
	exit(json_encode($rsp));
}

// Verificar permisos para accion Modificar
function checkEditPerm($action_code){
    global $user;
    if(!$user->can($action_code)){
        $rsp['msg'] = 'No hay permisos para esto';
        exit(json_encode($rsp));
    }
}

header('Content-Type: application/json');