<?php
define('__ROOT', dirname(__DIR__));

//sleep(1);
//usleep(500000);

include(__DIR__."/../../inc/util.php");
include(__DIR__."/../../inc/mysql.php");
$db = new MySQL();
$stg = $db->getSettings();

// Respuesta JSON
$response = array();
$response['IError'] = false;
$response['IMessage'] = '---';

header('Content-Type: application/json');