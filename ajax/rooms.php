<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('rooms');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']      = $user->id_branch;
        $data['name']           = $_POST['name'];
        $data['description']    = $_POST['description'];
        $data['state']          = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($db->update('rooms', $data, $id)){
                    $rsp['ok'] = true;
                    $rsp['id'] = $id;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('rooms', $data)){
                    $rsp['ok'] = true;
                    $rsp['id'] = $db->lastID();
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa un nombre';
        break;

	case 'remove':
        checkEditPerm('rooms');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('rooms', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;

    /**
     * Mesas
     */
    case 'add_table':
        checkEditPerm('rooms');
        $id = @$_POST['id'];
        $isEdit = is_numeric($id) && $id > 0;
        
        $data = [];
        $data['id_room']    = @$_POST['id_room'];
        $data['name']       = @$_POST['name'];
        $data['state']      = 1;
        if(is_numeric($data['id_room']) && $data['id_room'] > 0){
            if(!$isEdit || !empty($data['name'])){
                if(empty($data['name'])){ // Si no ingreso nombre, creamod uno correlativo
                    $tt = $db->total("SELECT * FROM tables WHERE id_room = ".$data['id_room']);
                    $data['name'] = sprintf("M%02d", $tt+1);
                }
                $rsp['isEdit'] = $isEdit;
                if($isEdit){
                    if($db->update('tables', $data, $id)){
                        $rsp['ok'] = true;
                        $rsp['table'] = $db->o('tables',$id);
                    } else $rsp['msg'] = 'Error interno :: UPDATE';
                } else {
                    if($db->insert('tables', $data)){
                        $rsp['ok'] = true;
                        $rsp['table'] = $db->o('tables',$db->lastID());
                    } else $rsp['msg'] = 'Error interno :: INSERT';
                }
            } else $rsp['msg'] = 'Ingrese un nombre';
        } else $rsp['msg'] = 'ID sala inválido';
        break;
    case 'remove_table':
        checkEditPerm('rooms');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('tables', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
        break;
}

echo json_encode($rsp);