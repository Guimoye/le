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
    $total_amount_due = 0;

    $os = $db->get("SELECT * FROM dues_rental WHERE id_driver = $driver->id");
    if($os){
        while($o = $os->fetch_assoc()){
            $o['amount_total'] = $o['amount_due']+
                $o['amount_penalty'] +
                $o['amount_previous'];

            $total_amount_due += $o['amount_due'];

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
    $smarty->assign('total_amount_due', $total_amount_due);

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

                if($db->update('drivers',['rental_dues'=>$dues, 'rental_amount'=>$amount, 'rental_date'=>$date], $id_driver)){
                    $rsp['ok'] = true;

                } else {
                    $rsp['msg'] = 'Error interno :: DB';
                }

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
                    $nextO = $db->o("SELECT * FROM dues_rental WHERE date_due > d$due->date_dueate_due LIMIT 1");
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

        case 'set_free_days':
            $days = _POST_ARR('days');

            $SQL = "SELECT du.*,
                           dr.rental_amount,
                           dr.rental_dues
                    FROM dues_rental du
                    INNER JOIN drivers dr ON dr.id = du.id_driver
                    WHERE du.id = $id";

            $due = $db->o($SQL);

            if(!$due){
                $rsp['msg'] = 'No se reconoce el registro';

            } else if(count($days) > 6){
                $rsp['msg'] = 'Solo se puede elegir 6 dias';

            } else {

                // Solo aplicar a dias no asignados
                $duplicate = false;
                $free_days = array_filter(explode(',', $due->free_days), 'strlen');
                foreach($free_days as $d){
                    if(in_array($d,$days)){ // Si este dia ya fue descontado, no se descuenta nuevamente
                        unset($days[array_search($d, $days)]);
                    }
                }

                $free_days  = array_merge($free_days, $days);

                $amount     = (int) $due->rental_amount;
                //$amount     = (int) $due->amount_due;
                $daily      = ($amount / 6);
                $num_days   = count($days);
                //$num_days   = count($days);
                $discount   = ($daily * $num_days);
                $new_amount = ($due->amount_due - $discount);

                $rsp['$duplicate']  = $duplicate;
                $rsp['$free_days']  = $free_days;
                $rsp['$days']       = $days;

                $rsp['num_days']    = $num_days;
                $rsp['diario']      = $daily;
                $rsp['discount']    = $discount;
                $rsp['new_amount']  = $new_amount;

                if($discount > 0){
                    $data = [];
                    $data['amount_due'] = $new_amount;
                    $data['free_days']  = implode(',', $free_days);

                    if($db->update('dues_rental', $data, $id)){
                        $rsp['ok'] = true;

                    } else {
                        $rsp['msg'] = 'Error interno :: DB';
                    }

                    // Obtener la ultima cuota
                    // En caso que la cuota sea menor, aplicamos, caso contrario creamos nueva
                    $SQL = "SELECT * FROM dues_rental
                            WHERE id_driver = $due->id_driver
                            ORDER BY date_due DESC LIMIT 1";
                    $ld = $db->o($SQL);

                    if(($ld->id != $due->id) && $ld->amount_due < $due->rental_amount){

                        $amount_due = $ld->amount_due + $discount;
                        $amount_due_dif = $amount_due - $due->rental_amount;
                        $amount_due_dif = $amount_due_dif>0?$amount_due_dif:0;

                        $data = [];
                        $data['amount_due'] = $amount_due - $amount_due_dif;
                        $db->update('dues_rental', $data, $ld->id);

                        if($amount_due_dif > 0){
                            $lastTime = strtotime($ld->date_due);
                            $day_nam = date("l", $lastTime);
                            $nextTime = strtotime('next '.$day_nam, $lastTime);
                            $date_due = date("Y-m-d", $nextTime);

                            $data = [];
                            $data['id_driver'] = $ld->id_driver;
                            $data['amount_due'] = $amount_due_dif;
                            $data['date_due'] = $date_due;
                            $db->insert('dues_rental', $data);

                        }

                    } else {
                        $lastTime = strtotime($ld->date_due);
                        $day_nam = date("l", $lastTime);
                        $nextTime = strtotime('next '.$day_nam, $lastTime);
                        $date_due = date("Y-m-d", $nextTime);


                        $data = [];
                        $data['id_driver'] = $ld->id_driver;
                        $data['amount_due'] = $discount;
                        $data['date_due'] = $date_due;
                        $db->insert('dues_rental', $data);

                    }
                    $rsp['$lastD'] = $ld;

                } else {
                    $rsp['msg'] = 'No se ha descontado';
                }
                $rsp['amount'] = $amount;
                $rsp['data'] = $days;

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