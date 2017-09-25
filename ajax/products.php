<?php
include("_base.php");
global $db,$user,$rsp;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('products');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']      = $user->id_branch;
        $data['id_area']        = @$_POST['id_area'];
        $data['id_category']    = @$_POST['id_category'];
        $data['id_unimed']      = @$_POST['id_unimed'];
        $data['name']           = @$_POST['name'];
        $data['description']    = @$_POST['description'];
        $data['notes']          = @$_POST['notes'];
        $data['state']          = 1; // Activo
        if(!empty($data['name'])){
            if(is_numeric($data['id_area']) && $data['id_area'] > 0){
                if(is_numeric($data['id_category']) && $data['id_category'] > 0){
                    if(is_numeric($data['id_unimed']) && $data['id_unimed'] > 0){
                        if($isEdit){
                            if($db->update('products', $data, $id)){
                                $rsp['ok'] = true;
                                $rsp['id'] = $id;
                            } else $rsp['msg'] = 'Error interno :: UPDATE';
                        } else {
                            if($db->insert('products', $data)){
                                $rsp['ok'] = true;
                                $rsp['id'] = $db->lastID();
                            } else $rsp['msg'] = 'Error interno :: INSERT';
                        }
                        if($rsp['ok']){
                            $rsp['product'] = $db->o('products', $rsp['id']);
                        }
                    } else $rsp['msg'] = 'Elige la unidad de medida.';
                } else $rsp['msg'] = 'Elige la categoría.';
            } else $rsp['msg'] = 'Elige un area de producción.';
        } else $rsp['msg'] = 'Ingresa un nombre.';
        break;

	case 'remove':
        checkEditPerm('products');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('products', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;

	case 'get_dropdowns':
        include('controller.php');
        $cc = new Controller();
        $cc->getAreas();
        $cc->getCategories();
        $cc->getUnimeds();
		break;

    /**
     * PROPRES
     */
    case 'add_propre':
        checkEditPerm('products');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $supplies = getValidSupplies(@$_POST['supplies'], @$_POST['quantities'], @$_POST['id_unimeds']);

        $data = [];
        $data['id_product']     = @$_POST['id_product']         ?: '';
        $data['name']           = @$_POST['name']               ?: '';
        $data['cost']           = @$_POST['cost']               ?: 0;
        $data['price']          = @$_POST['price']              ?: 0;
        $data['price']          = @$_POST['price']              ?: 0;
        $data['commission']     = @$_POST['commission']         ?: '';
        $data['in_deli']        = isset($_POST['in_deli'])      ? 1 : 0;
        $data['has_stock']      = isset($_POST['has_stock'])    ? 1 : 0;
        $data['has_discounts']  = isset($_POST['has_discounts'])? 1 : 0;
        $data['has_supply']     = isset($_POST['has_supply'])   ? 1 : 0;
        $data['state']          = 1; // Activo
        $rsp['data'] = $data;
        if(is_numeric($data['id_product']) && $data['id_product'] > 0){
            if(!empty($data['name'])){
                if($data['has_supply'] == 0 || ($supplies && is_array($supplies))){

                    if($isEdit){
                        if($db->update('propres', $data, $id)){
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: UPDATE';
                    } else {
                        if($db->insert('propres', $data)){
                            $id = $db->lastID();
                            $rsp['ok'] = true;
                            $rsp['propres'] = getPropres($data['id_product']);
                        } else $rsp['msg'] = 'Error interno :: INSERT';
                    }

                    // Si lleva control de stock, agregamos
                    $db->query("DELETE FROM stocks WHERE id_propre = $id");
                    if($rsp['ok'] && $data['has_stock'] == 1){
                        $os = getStocks($id);
                        foreach($os as $o){
                            $stock = @$_POST['storage_'.$o->id] ?: 0;
                            $db->insert('stocks', [
                                'id_propre'=>$id,
                                'id_storage'=>$o->id,
                                'stock'=>$stock
                            ]);
                        };
                    }
                    // Si lleva ingredientes, agregamos
                    $db->query("DELETE FROM propresups WHERE id_propre = $id");
                    if($rsp['ok'] && $data['has_supply'] == 1){

                        foreach($supplies as $s){
                            $db->insert('propresups', [
                                'id_propre' => $id,
                                'id_supply' => $s['id'],
                                'id_unimed' => $s['id_unimed'],
                                'quantity'  => $s['quantity']
                            ]);
                        }

                    }

                } else $rsp['msg'] = 'Compruebe que las insumos se han introducido correctamente.';
            } else $rsp['msg'] = 'Ingresa un nombre.';
        } else $rsp['msg'] = 'No se reconoce el producto.';
        break;

    case 'remove_propre':
        checkEditPerm('products');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $id_product = isset($_POST['id_product']) ? $_POST['id_product'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('propres', ['state'=>0], $id)){
                $rsp['ok'] = true;
                if(is_numeric($id_product) && $id_product > 0){
                    $rsp['propres'] = getPropres($id_product);
                }
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
        break;

    case 'remove_propresup':
        checkEditPerm('products');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->query("DELETE FROM propresups WHERE id = $id")){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
        break;

    // Obtener propres
    case 'get_propres':
        $id_product = isset($_POST['id_product']) ? $_POST['id_product'] : '';
        if(is_numeric($id_product) && $id_product > 0){
            $rsp['ok'] = true;
            $rsp['propres'] = getPropres($id_product);

        } else $rsp['msg'] = 'ID Inválido.';
        break;
}

function getPropres($id_product){
    global $db,$user;
    $propres = [];

    $os = $db->get("SELECT * FROM propres WHERE id_product = $id_product AND state = 1");
    while($o = $os->fetch_object()){


        $SQL = "SELECT su.*,pr.id id_propresup, pr.id_unimed, pr.quantity
                        FROM propresups pr
                          LEFT JOIN supplies su ON su.id = pr.id_supply
                        WHERE pr.id_propre = $o->id
                        ORDER BY su.name";

        $supplies = $db->arr($SQL);

        $o->supplies = $supplies;

        $SQL = "SELECT st.*,
                   sk.id id_stock,
                   IFNULL(sk.stock, 0) stock
            FROM storages st
              LEFT JOIN stocks sk ON sk.id_storage = st.id AND sk.id_propre = $o->id
            WHERE st.id_branch = $user->id_branch AND st.state = 1";
        $stocks = $db->arr($SQL);

        $o->stocks = $stocks;

        $propres[] = $o;
    }
    return $propres;
}

function getValidSupplies($supplies,$quantities,$id_unimeds){
    $arr = [];

    if(is_array($supplies) && is_array($quantities) && is_array($id_unimeds)){

        for($i=0; $i<count($quantities); $i++){
            $id         = @$supplies[$i];
            $quantity   = @$quantities[$i];
            $id_unimed  = @$id_unimeds[$i];
            if(is_numeric($id) && $id > 0){
                if(is_numeric($quantity) && $quantity > 0){
                    if(is_numeric($id_unimed) && $id_unimed > 0){
                        $arr[] = [
                            'id' => $id,
                            'quantity' => $quantity,
                            'id_unimed' => $id_unimed
                        ];
                    } else return false;
                } else return false;
            } else return false;
        }

    } else return false;

    return $arr;
}


function getStocks($id_propre){
    global $db,$user;
    $items = [];

    $SQL = "SELECT st.*,
                   sk.id id_stock,
                   IFNULL(sk.stock, 0) stock
            FROM storages st
              LEFT JOIN stocks sk ON sk.id_storage = st.id AND sk.id_propre = $id_propre
            WHERE st.id_branch = $user->id_branch AND st.state = 1
            ORDER BY st.name";
    $os = $db->get($SQL);
    while($o = $os->fetch_object()){
        $items[] = $o;
    }

    return $items;
}

echo json_encode($rsp);