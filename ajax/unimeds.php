<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('unimeds');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['name']       = $_POST['name'];
        $data['state']      = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($db->update('unimeds', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('unimeds', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa un nombre';
        break;

	case 'remove':
        checkEditPerm('unimeds');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('unimeds', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;

    case 'add_rel':
        checkEditPerm('unimeds');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_unimed_org'] = $_POST['id_unimed_org'];
        $data['id_unimed_dst'] = $_POST['id_unimed_dst'];
        $data['quantity']       = $_POST['quantity'];
        $data['state']      = 1; // Activo
        if(!empty($data['quantity'])){
            if($isEdit){
                if($db->update('unimeds_rel', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('unimeds_rel', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Indica la cantidad';
        break;

    case 'remove_rel':
        checkEditPerm('unimeds');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('unimeds_rel', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
        break;
}

function saveList($list, $parent_id = 0, &$m_order = 0){
    global $db;
    foreach($list as $item) {
        $m_order++;

        $db->update('unimeds', ['id_parent'=>$parent_id,'sort'=>$m_order], $item["id"]);

        if(array_key_exists("children", $item)){
            saveList($item["children"], $item["id"], $m_order);
        }
    }
}

echo json_encode($rsp);