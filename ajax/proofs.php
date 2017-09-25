<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('proofs');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        //$data['id_branch']  = $user->id_branch;
        $data['code']       = isset($_POST['code']) ? $_POST['code'] : '';
        $data['name']       = isset($_POST['name']) ? $_POST['name'] : '';
        $data['state']      = 1; // Activo
        //if(!empty($data['id_branch'])){
            if(!empty($data['name'])){
                if($isEdit){
                    if($db->update('proofs', $data, $id)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: UPDATE';
                } else {
                    if($db->insert('proofs', $data)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: INSERT';
                }
            } else $rsp['msg'] = 'Ingresa un nombre.';
        //} else $rsp['msg'] = 'Especifica a qué sucursal pertenece.';
        break;

	case 'remove':
        checkEditPerm('proofs');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('proofs', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;
}

echo json_encode($rsp);