<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('boxes');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']              = $user->id_branch;
        $data['name']                   = $_POST['name'];
        $data['printer_ip']             = $_POST['printer_ip'];
        $data['printer_name']           = $_POST['printer_name'];
        $data['printer_serial']         = $_POST['printer_serial'];
        $data['printer_line_letters']   = $_POST['printer_line_letters'];
        $data['printer2_ip']            = $_POST['printer2_ip'];
        $data['printer2_name']          = $_POST['printer2_name'];
        $data['printer2_serial']        = $_POST['printer2_serial'];
        $data['printer2_line_letters']  = $_POST['printer2_line_letters'];
        $data['state']      = 1; // Activo
        if(!empty($data['id_branch'])){
            if(!empty($data['name'])){
                if($isEdit){
                    if($db->update('boxes', $data, $id)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: UPDATE';
                } else {
                    if($db->insert('boxes', $data)){
                        $rsp['ok'] = true;
                    } else $rsp['msg'] = 'Error interno :: INSERT';
                }
            } else $rsp['msg'] = 'Ingresa un nombre.';
        } else $rsp['msg'] = 'Especifica a qué sucursal pertenece.';
        break;

	case 'remove':
        checkEditPerm('boxes');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('boxes', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;
}

echo json_encode($rsp);