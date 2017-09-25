<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('providers');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['name']       = $_POST['name'];
        $data['ruc']        = $_POST['ruc'];
        $data['email']      = $_POST['email'];
        $data['phone']      = $_POST['phone'];
        $data['address']    = $_POST['address'];
        $data['state']      = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($db->update('providers', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('providers', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa un nombre';
        break;

	case 'remove':
        checkEditPerm('providers');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('providers', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;
}

echo json_encode($rsp);