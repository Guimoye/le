<?php
$action = isset($_POST['action'])   ? $_POST['action']  : '';
$id     = isset($_POST['id'])       ? $_POST['id']      : 0;

//TODO: Test
/*$action = 'add';
$_POST['id_driver'] = 1;
$_POST['dues'] = 5;
$_POST['amount'] = 350;
$_POST['date'] = '2017-09-27';*/
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

    $os = $db->get("SELECT * FROM dues_rental WHERE id_driver = $driver->id");
    if($os){
        while($o = $os->fetch_assoc()){
            $o['amount_total'] = $o['amount_due']+
                $o['amount_penalty'] +
                $o['amount_previous'];

            if($o['amount_paid'] > 0){
                $o['pay_state'] = 'paid';

            } else if(strtotime($o['date_due']) > time()) {
                $o['pay_state'] = 'pending';

            } else {
                $o['pay_state'] = 'expired';

            }

            $items[] = $o;
        }
    }

    $smarty->assign('page_title', 'Cronograma de alquiler');
    $smarty->assign('driver', $driver);
    $smarty->assign('items', $items);

    $smarty->display(PAGE.'.tpl');

} else {
    include('ajax/_base.php');
    global $db, $rsp;

    switch($action){

        case 'add':
            checkEditPerm('drivers');

            $id_driver  = _POST_INT('id_driver');
            $dues       = _POST_INT('dues');
            $amount     = _POST_INT('amount');
            $date       = _POST('date');

            if($id_driver <= 0){
                $rsp['msg'] = 'No se reconoce el conductor';

            } else if($db->has('dues_rental', 'id_driver', $id_driver)){
                $rsp['msg'] = 'El programa de alquiler para este conductor se generó previamente.';

            } else if($dues <= 0){
                $rsp['msg'] = 'Indica el número de cuotas';

            } else if($amount <= 0){
                $rsp['msg'] = 'Indica el monto de cuota';

            } else if(!$uu->isDate($date)){
                $rsp['msg'] = 'Indica la fecha de inicio';

            } else {

                $lastTime = strtotime($date);
                for($i = 1; $i <= $dues; $i++){

                    $date_due = date("Y-m-d", $lastTime);
                    $day_num = date("w", $lastTime);
                    $day_nam = date("l", $lastTime);

                    $rsp['dayy'] = date("w", $lastTime);

                    $rsp['dues'][] = [
                        'due' => $i,
                        'date_due' => $date_due,
                        'day_num' => $day_num,
                        'day_nam' => $day_nam
                    ];

                    $db->insert('dues_rental', [
                        'id_driver' => $id_driver,
                        'amount_due' => $amount,
                        'date_due' => $date_due
                    ]);

                    $lastTime = strtotime('next '.$day_nam, $lastTime);
                }

                $rsp['ok'] = true;

            }
            break;

        case 'set_due_paid':
            checkEditPerm('drivers');

            $amount_total = _POST_INT('amount_total');
            $amount = _POST_INT('amount');

            $due = $db->o('dues_rental', $id);

            if(!$due){
                $rsp['msg'] = 'No se reconoce el registro';

            } else if($amount <= 0){
                $rsp['msg'] = 'Debe ingresar un monto válido';

            } else {

                // La siguiente cuota
                if($amount < $amount_total){
                    $next_amount = $amount_total - $amount; // A pagar el proximo mes

                    // Obtener la siguiente cuota
                    $nextO = $db->o("SELECT * FROM dues_rental WHERE date_due > '$due->date_due' LIMIT 1");
                    if($nextO){
                        $db->update('dues_rental', ['amount_previous' => $next_amount], $nextO->id);
                    }
                }

                $data = [];
                $data['amount_paid'] = $amount;
                $data['date_paid'] = 'NOW()';
                if($db->update('dues_rental', $data, $id)){
                    $rsp['ok'] = true;
                } else {
                    $rsp['msg'] = 'Error interno :: DB';
                }

            }
            break;

        case 'set_due_unpaid':
            $data = [];
            $data['amount_paid'] = 0;
            $data['date_paid'] = '';
            if($db->query("UPDATE dues_rental SET amount_paid = 0, date_paid = NULL WHERE id = $id")){
                $rsp['ok'] = true;
            } else {
                $rsp['msg'] = 'Error interno :: DB';
            }
            break;

        case 'upload_voucher':
            $photo = (isset($_FILES['photo']) ? $_FILES['photo'] : '' );
            if($id <= 0){
                $rsp['msg'] = 'No se reconoce el registro';

            } else if(empty($photo['name'])){
                $rsp['msg'] = 'No se ha seleccionado la imágen';

            } else {
                require('inc/plugins/ImageResize.php');
                $pic_voucher = md5(uniqid($id));

                $image = new ImageResize($photo['tmp_name']);
                $image->width(600);
                $image->height(600);
                $image->resize();
                if($image->save('uploads/'.$pic_voucher.'.jpg')){
                    if($db->update('dues_rental', ['pic_voucher'=>$pic_voucher], $id)){
                        $rsp['pic_voucher'] = $pic_voucher;
                        $rsp['ok'] = true;

                    } else {
                        $rsp['msg'] = 'Error interno :: DB';
                    }
                } else {
                    $rsp['msg'] = 'Error al guardar la imágen';
                }

            }

            break;
    }

    echo json_encode($rsp);
}