<?php
include("_base.php");
include("../inc/stock.php");
global $db,$user,$rsp;

$_POST = $_GET;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('purchases');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $supplies = isset($_POST['supplies']) && is_array($_POST['supplies']) ? $_POST['supplies'] : [];
        $isEdit = is_numeric($id) && $id > 0;

        $data = [];
        $data['id_branch']      = $user->id_branch;
        $data['id_user']        = $user->id;
        $data['id_storage']     = isset($_POST['id_storage']) ? $_POST['id_storage'] : '';
        $data['id_provider']    = isset($_POST['id_provider']) ? $_POST['id_provider'] : '';
        $data['id_proof']       = isset($_POST['id_proof']) ? $_POST['id_proof'] : '';
        $data['num_doc']        = isset($_POST['num_doc']) ? $_POST['num_doc'] : '';
        $data['isc']            = isset($_POST['isc']) ? $_POST['isc'] : '';
        $data['glosa']          = isset($_POST['glosa']) ? $_POST['glosa'] : '';
        $data['condicion']      = isset($_POST['condicion']) ? $_POST['condicion'] : '';
        $data['total']          = isset($_POST['total']) ? $_POST['total'] : '';
        $data['total_items']    = count($supplies);
        $data['state']          = 1; // Activo
        if(is_numeric($data['id_storage']) && $data['id_storage'] > 0){
            if(!empty($data['glosa'])){
                if(is_numeric($data['total']) && $data['total'] > 0){
                    if(!empty($data['id_proof'])){

                        if($isEdit){
                            if($db->update('purchases', $data, $id)){
                                $rsp['ok'] = true;
                            } else $rsp['msg'] = 'Error interno :: UPDATE';
                        } else {
                            if($db->insert('purchases', $data)){
                                $id = $db->lastID();
                                $rsp['ok'] = true;
                            } else $rsp['msg'] = 'Error interno :: INSERT';
                        }

                        if($rsp['ok']){
                            $stk = new Stock();
                            if(is_array($supplies) && count($supplies) > 0){
                                foreach($supplies as $s){
                                    $dtps = [];
                                    $dtps['id_purchase'] = $id;
                                    $dtps['id_supply'] = $s['id'];
                                    $dtps['quantity'] = $s['quantity'];
                                    $dtps['price'] = $s['price'];
                                    $dtps['total'] = $s['price']*$s['quantity'];
                                    $dtps['state'] = 1;
                                    if($db->insert('purchases_sup', $dtps)){

                                        $stk->bySupply($s['id'], $data['id_storage'], $dtps['quantity']);

                                        $stk->kardex(
                                            $s['id'],
                                            1,
                                            $s['quantity'],
                                            $s['price'],
                                            'Compra '.$data['glosa'],
                                            $id);
                                    }
                                }
                            }
                        }

                    } else $rsp['msg'] = 'Comprobante de pago inválido.';
                } else $rsp['msg'] = 'Monto total inválido.';
            } else $rsp['msg'] = 'Campo "glosa" necesario.';
        } else $rsp['msg'] = 'Se debe elegir un almacén.';
        break;

	case 'remove':
        checkEditPerm('purchases');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('purchases', ['state'=>0], $id)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error interno :: DB';
        } else $rsp['msg'] = 'ID inválido';
		break;

    // anular compra
	case 'annul':
        checkEditPerm('annul_purchases');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $reason = isset($_POST['reason']) ? $_POST['reason'] : '';
        if(is_numeric($id) && $id > 0){
            if($db->update('purchases', ['reason_annul'=>$reason,'state'=>0], $id)){
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
        $rsp = $db->arr("SELECT * FROM purchases WHERE name LIKE '$term' AND id_branch = $user->id_branch AND state = 1");
        break;

    case 'get_all':
        $SQL = "SELECT su.*,
                       un.name un_name,
                       COALESCE(SUM(sk.stock),0) stock
                FROM purchases su
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

    // Obtener insumos de una compra
    case 'get_supplies':
        $id_purchase = isset($_POST['id_purchase']) ? $_POST['id_purchase'] : '';
        if(is_numeric($id_purchase) && $id_purchase > 0){
            $SQL = "SELECT ps.*,
                           su.name su_name,
                           un.name un_name
                FROM purchases_sup ps
                  INNER JOIN supplies su ON su.id = ps.id_supply
                  LEFT JOIN unimeds un ON un.id = su.id_unimed
                WHERE ps.id_purchase = $id_purchase AND ps.state = 1";
            $items = $db->arr($SQL);
            if($items){
                $rsp['items'] = $items;
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Error al recuperar registros';
        } else $rsp['msg'] = 'ID inválido';
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