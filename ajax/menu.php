<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add_menu':
        checkEditPerm('modules');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['name']   = @$_POST['name'] ?: '';
        if(isset($_POST['url'])){
            $data['url'] = $_POST['url'];
        }
        $data['icon']   = @$_POST['icon'] ?: '';
        $data['state']  = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($db->update('menu', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('menu', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa un nombre';
        break;

	case 're_sort':
        checkEditPerm('modules');
        saveList($_POST['list']);
        $rsp['ok'] = true;
		break;

	case 'remove_menu':
        checkEditPerm('modules');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            $menu = $db->o('menu',$id);
            if($menu){
                if($menu->root != 1){
                    if($db->query("DELETE FROM menu WHERE id = $id")){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: DB';
                } else $rsp['msg'] = 'No puedes eliminar este item';
            } else $rsp['msg'] = 'No se pudo reconocer';
        } else $rsp['msg'] = 'ID inválido';
		break;

    case 'add_level':
        checkEditPerm('modules');
        $id = isset($_POST['id']) ? $_POST['id'] : 0;
        $id_menu_home = isset($_POST['home']) ? $_POST['home'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_menu'] = $id_menu_home;
        $data['name'] = $_POST['name'];
        if(!empty($data['name'])){
            if(is_numeric($data['id_menu']) && $data['id_menu'] > 0){
                if($isEdit){
                    if($db->update('levels', $data, $id)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: UPDATE';
                } else {
                    if($db->insert('levels', $data)){
                        $id = $db->lastID();
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: INSERT';
                }

                $perms = [];
                $see = isset($_POST['see']) && is_array($_POST['see']) ? $_POST['see'] : [];
                $edit = isset($_POST['edit']) && is_array($_POST['edit']) ? $_POST['edit'] : [];
                $shortcut = isset($_POST['shortcut']) && is_array($_POST['shortcut']) ? $_POST['shortcut'] : [];
                foreach($see as $id_menu){
                    $perms[$id_menu]['see'] = true;
                }
                foreach($edit as $id_menu){
                    $perms[$id_menu]['edit'] = true;
                }
                foreach($shortcut as $id_menu){
                    $perms[$id_menu]['shortcut'] = true;
                }
                $rsp['perms'] = $perms;
                $db->query("DELETE FROM perms WHERE id_level = $id");
                foreach($perms as $id_menu => $v){
                    $data = [];
                    $data['id_level'] = $id;
                    $data['id_menu'] = $id_menu;
                    $data['see'] = isset($v['see']) && $v['see'] ? 1 : 0;
                    $data['edit'] = isset($v['edit']) && $v['edit'] ? 1 : 0;
                    $data['shortcut'] = isset($v['shortcut']) && $v['shortcut'] ? 1 : 0;
                    $data['home'] = $id_menu_home == $id_menu ? 1 : 0;
                    $db->insert('perms', $data);
                }
                /*//TODO: por acciones
                $db->query("DELETE FROM perms WHERE id_level = $id");
                foreach($see as $id_menu){
                    $data = [];
                    $data['id_level'] = $id;
                    $data['id_menu'] = $id_menu;
                    $db->insert('perms', $data);
                }*/

                if($isEdit){
                    /*if($db->update('menu', $data, $id)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: UPDATE';*/
                } else {
                    /*if($db->insert('menu', $data)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: INSERT';*/
                }
            } else $rsp['msg'] = 'Debes elegir la pagina de inicio';
        } else $rsp['msg'] = 'Ingresa un nombre';
        break;
}

function saveList($list, $parent_id = 0, &$m_order = 0){
    global $db;
    foreach($list as $item) {
        $m_order++;

        $db->update('menu', ['id_parent'=>$parent_id,'sort'=>$m_order], $item["id"]);

        if(array_key_exists("children", $item)){
            saveList($item["children"], $item["id"], $m_order);
        }
    }
}

echo json_encode($rsp);