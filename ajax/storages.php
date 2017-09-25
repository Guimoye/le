<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('storages');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']  = $user->id_branch;
        $data['name']       = $_POST['name'];
        $data['id_area']    = isset($_POST['id_area']) ? $_POST['id_area'] : '';
        $data['state']      = 1; // Activo
        if(!empty($data['id_branch'])){
            if(!empty($data['name'])){
                if(is_numeric($data['id_area']) && $data['id_area'] > 0){
                    if($isEdit){
                        if($db->update('storages', $data, $id)){
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: UPDATE';
                    } else {
                        if($db->insert('storages', $data)){
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: INSERT';
                    }
                } else $rsp['msg'] = 'Elegir área de producción.';
            } else $rsp['msg'] = 'Ingresa un nombre.';
        } else $rsp['msg'] = 'Especifica a qué sucursal pertenece.';
        break;

	case 'remove':
        checkEditPerm('storages');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('storages', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;
}

echo json_encode($rsp);