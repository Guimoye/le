<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('areas');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']  = $user->id_branch;
        $data['name']       = $_POST['name'];
        $data['state']      = 1; // Activo
        if(!empty($data['name'])){
            if($isEdit){
                if($db->update('areas', $data, $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: UPDATE';
            } else {
                if($db->insert('areas', $data)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: INSERT';
            }
        } else $rsp['msg'] = 'Ingresa un nombre.';
        break;

	case 'remove':
        checkEditPerm('areas');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('areas', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;

	case 'get_storages':
        $id_supply = isset($_POST['id_supply']) && is_numeric($_POST['id_supply']) ? $_POST['id_supply'] : 0;
        $rsp['items'] = getStocks($id_supply);
		break;
}

function getStocks($id_supply){
    global $db,$user;
    $items = [];

    $SQL = "SELECT st.*,
                   sk.id id_stock,
                   IFNULL(sk.stock, 0) stock
            FROM storages st
              LEFT JOIN stocks sk ON sk.id_storage = st.id AND sk.id_supply = $id_supply
            WHERE st.id_branch = $user->id_branch AND st.state = 1";
    $os = $db->get($SQL);
    while($o = $os->fetch_object()){
        $items[] = $o;
    }

    return $items;
}

echo json_encode($rsp);