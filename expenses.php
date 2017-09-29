<?php
//TODO: Test
//$_POST['action']    = 'set_free_days';
//$_POST['id']        = 1;
//$_POST['days']      = [0,1];

$action = isset($_POST['action'])   ? $_POST['action']  : '';
$id     = isset($_POST['id'])       ? $_POST['id']      : 0;

if(empty($action)){
    include('_base.php');
    global $db;
    $id_driver = _GET_INT('id');

    $driver = $db->o('drivers', $id_driver);

    if($driver == FALSE){
        $smarty->display('e404.tpl');
        exit;
    }

    $items = [];
    $total_amount = 0;

    $os = $db->get("SELECT * FROM expenses WHERE id_driver = $driver->id AND state = 1");
    if($os){
        while($o = $os->fetch_assoc()){
            $total_amount += $o['amount'];

            if($o['date_paid']){
                $o['pay_state'] = 'paid';

            } else if(strtotime($o['date_pay']) > time()) {
                $o['pay_state'] = 'pending';

            } else {
                $o['pay_state'] = 'expired';

            }

            $items[] = $o;
        }
    }

    $smarty->assign('page_title', 'Gastos');
    $smarty->assign('driver', $driver);
    $smarty->assign('items', $items);
    $smarty->assign('total_amount_due', $total_amount);

    $smarty->display(PAGE.'.tpl');

} else {
    include('ajax/_base.php');
    global $db, $rsp;

    switch($action){

        case 'add':
            checkEditPerm('drivers');

            $isEdit = (is_numeric($id) && $id > 0);

            $data = [];
            $data['id_driver']      = _POST_INT('id_driver');
            $data['description']    = _POST('description');
            $data['amount']         = _POST_INT('amount');
            $data['date_pay']       = _POST('date_pay');
            $data['state']          = 1;

            if($data['id_driver'] <= 0){
                $rsp['msg'] = 'No se reconoce el conductor';

            } else if(empty($data['description'])){
                $rsp['msg'] = 'Indica el tipo de gasto';

            } else if($data['amount'] <= 0){
                $rsp['msg'] = 'Indica el monto';

            } else if(!$uu->isDate($data['date_pay'])){
                $rsp['msg'] = 'Indica la fecha';

            } else {

                if($isEdit){
                    if($db->update('expenses', $data, $id)){
                        $rsp['ok'] = true;

                    } else {
                        $rsp['msg'] = 'Error interno :: DB :: '.$action;
                    }

                } else {
                    if($db->insert('expenses', $data)){
                        $rsp['ok'] = true;

                    } else {
                        $rsp['msg'] = 'Error interno :: DB :: '.$action;
                    }

                }

            }
            break;

        case 'set_paid':
            checkEditPerm('drivers');

            $due = $db->o('expenses', $id);

            if(!$due){
                $rsp['msg'] = 'No se reconoce el registro';

            } else {

                $data = [];
                $data['date_paid'] = 'NOW()';
                if($db->update('expenses', $data, $id)){
                    $rsp['ok'] = true;
                } else {
                    $rsp['msg'] = 'Error interno :: DB';
                }

            }
            break;

        case 'set_unpaid':
            if($db->query("UPDATE expenses SET date_paid = NULL WHERE id = $id")){
                $rsp['ok'] = true;
            } else {
                $rsp['msg'] = 'Error interno :: DB';
            }
            break;

        case 'remove':
            if(is_numeric($id) && $id > 0){
                if($db->query("UPDATE expenses SET state = 0 WHERE id = $id")){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error DB :: No se pudo eliminar';
            } else $rsp['msg'] = 'No se puede reconocer';
            break;
    }

    echo json_encode($rsp);
}