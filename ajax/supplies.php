<?php
include("_base.php");
global $db,$user,$rsp;

$_POST = $_GET;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('supplies');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']  = $user->id_branch;
        $data['id_unimed']  = $_POST['id_unimed'];
        $data['name']       = $_POST['name'];
        $data['cost']       = $_POST['cost'];
        $data['cost_max']   = $_POST['cost_max'];
        $data['stock_min']  = $_POST['stock_min'];
        $data['tipo_adq']   = $_POST['tipo_adq'];
        $data['state']      = 1; // Activo
        if(!empty($data['name'])){
            if(!empty($data['id_unimed'])){
                if(is_numeric($data['tipo_adq']) && $data['tipo_adq'] >= 0){
                    if($isEdit){
                        if($db->update('supplies', $data, $id)){
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: UPDATE';
                    } else {
                        if($db->insert('supplies', $data)){
                            $id = $db->lastID();
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: INSERT';
                    }

                    // Actualizar stock
                    /*$db->query("DELETE FROM stocks WHERE id_supply = $id");
                    $os = $db->get("SELECT * FROM storages WHERE id_branch = $user->id_branch AND state = 1");
                    while($o = $os->fetch_object()){
                        $stock = @$_POST['storage_'.$o->id] ?: 0;
                        $db->insert('stocks', ['id_supply'=>$id, 'id_storage'=>$o->id, 'stock'=>$stock]);
                    }*/
                    $os = getStocks($id);
                    foreach($os as $o){
                        $stock = @$_POST['storage_'.$o->id] ?: 0;
                        $data = [
                            'id_supply'=>$id,
                            'id_storage'=>$o->id,
                            'stock'=>$stock
                        ];
                        if(isset($o->id_stock) && $o->id_stock > 0){
                            $db->update('stocks', $data, $o->id_stock);
                        } else {
                            $db->insert('stocks', $data);
                        }
                    };

                } else $rsp['msg'] = 'Tipo de adquisiciòn.';
            } else $rsp['msg'] = 'Especifica la unidad de medida.';
        } else $rsp['msg'] = 'Ingresa un nombre.';
        break;

	case 'remove':
        checkEditPerm('supplies');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('supplies', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;

	case 'get_storages':
        $id_supply = isset($_POST['id_supply']) && is_numeric($_POST['id_supply']) ? $_POST['id_supply'] : 0;
        $rsp['items'] = getStocks($id_supply);
		break;

    case 'autocomplete':
        $term = isset($_GET['term']) ? trim($_GET['term']) : '';
        $term = empty($term) ? '' : '%'.str_replace(' ', '%', $term).'%';
        $rsp = $db->arr("SELECT * FROM supplies WHERE name LIKE '$term' AND id_branch = $user->id_branch AND state = 1");
        break;

    case 'get_all':
        $SQL = "SELECT su.*,
                       un.name un_name,
                       COALESCE(SUM(sk.stock),0) stock
                FROM supplies su
                  LEFT JOIN unimeds un ON un.id = su.id_unimed
                  LEFT JOIN stocks sk ON sk.id_supply = su.id
                WHERE su.id_branch = $user->id_branch AND su.state = 1
                GROUP BY su.id
                ORDER BY su.name";
        $items = $db->arr($SQL);
        if($items){
            $rsp['items'] = $items;
            $rsp['ok'] = true;
        }
        break;
}

function getStocks($id_supply){
    global $db,$user;
    $items = [];

    if($id_supply == 0){
        $SQL = "SELECT *, 0 stock FROM storages WHERE id_branch = $user->id_branch AND state = 1";
    } else {
        $SQL = "SELECT st.*,
                   sk.id id_stock,
                   IFNULL(sk.stock, 0) stock
            FROM storages st
              LEFT JOIN stocks sk ON sk.id_storage = st.id AND sk.id_supply = $id_supply
            WHERE st.id_branch = $user->id_branch AND st.state = 1
            ORDER BY st.name";
    }
    $os = $db->get($SQL);
    while($o = $os->fetch_object()){
        $items[] = $o;
    }

    return $items;
}

echo json_encode($rsp);