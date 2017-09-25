<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('categories');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['name']       = $_POST['name'];
        $data['color']      = $_POST['color'];
        $data['sort']       = $_POST['sort'];
        $data['in_deli']    = isset($_POST['in_deli']) ? 1 : 0;
        $data['favorite']   = isset($_POST['favorite']) ? 1 : 0;
        $data['state']      = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($db->update('categories', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('categories', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa un nombre';
        break;

	case 're_sort':
        checkEditPerm('categories');
        saveList($_POST['list']);
        $rsp['ok'] = true;
		break;

	case 'remove':
        checkEditPerm('categories');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('categories', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;
}

function saveList($list, $parent_id = 0, &$m_order = 0){
    global $db;
    foreach($list as $item) {
        $m_order++;

        $db->update('categories', ['id_parent'=>$parent_id,'sort'=>$m_order], $item["id"]);

        if(array_key_exists("children", $item)){
            saveList($item["children"], $item["id"], $m_order);
        }
    }
}

echo json_encode($rsp);