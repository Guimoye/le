<?php
include("_base.php");
include("../inc/uprint.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) 	? $_POST['action'] 	: '';

switch($action){

    case 'precuenta':
        $id_order   = @$_POST['id_order'] ?: '';
        $total      = @$_POST['total'] ?: '';
        $ordpros    = @$_POST['ordpros'] ?: '';

        $rsp['ok'] = true;
        $rsp['data'] = UPrint::precuenta($id_order,$total,$ordpros);
        //$rsp['data'] = UPrint::boleta(87);
        break;

    case 'transaction':
        $id_transaction = @$_POST['id_transaction'] ?: '';
        $rsp['ok'] = true;
        $rsp['data'] = UPrint::transaction($id_transaction);
        break;
}

echo json_encode($rsp);