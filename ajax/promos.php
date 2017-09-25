<?php
include("_base.php");
global $db,$user,$rsp,$uu;

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch($action){

    case 'add':
        checkEditPerm('promos');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $isEdit = is_numeric($id) && $id > 0;

        $date = isset($_POST['date']) ? $_POST['date'] : '';
        $time = isset($_POST['time']) ? $_POST['time'] : '';

        $date_end = $date.' '.$time;

        $data = [];
        $data['code']       = $_POST['code'];
        $data['percent']    = $_POST['percent'];
        $data['max_value']  = $_POST['max_value'];
        $data['name']       = $_POST['name'];
        $data['state']      = 1; // Activo
        if(!empty($data['code'])){
            if(!$db->has("SELECT * FROM promos WHERE id != '$id' AND code = '".$data['code']."'")){
                if(is_numeric($data['percent']) && $data['percent'] > 0){

                    if($uu->isDateTime($date_end))
                        $data['date_end'] = $date_end;
                    else $data['date_end'] = 'NULL';

                    if($isEdit){
                        if($db->update('promos', $data, $id)){
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: UPDATE';
                    } else {
                        if($db->insert('promos', $data)){
                            $rsp['ok'] = true;
                        } else $rsp['msg'] = 'Error interno :: INSERT';
                    }
                } else $rsp['msg'] = 'Ingresa un porcentaje válido.';
            } else $rsp['msg'] = 'El código ya se encuentra registrado.';
        } else $rsp['msg'] = 'Ingresa un código.';
        break;

	case 'remove':
        checkEditPerm('promos');
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        if(is_numeric($id) && $id > 0){
            $o = $db->o('promos',$id);
            if($o){
                if($db->update('promos', ['code'=>'deprecated.'.$o->code,'state'=>0], $id)){
                    $rsp['ok'] = true;
                } else $rsp['msg'] = 'Error interno :: DB';
            } else $rsp['msg'] = 'No se reconoce.';
        } else $rsp['msg'] = 'ID inválido';
		break;

    case 'check_code':
        $code   = isset($_POST['code'])  ? $_POST['code']  : '';
        $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
        if(!empty($code)){
            if(is_numeric($amount)){
                $promo = $db->o("SELECT * FROM promos WHERE code = '$code' AND state = 1");
                if($promo){
                    $applyPromo = true;

                    // Verificar si la promo tiene fecha de vencimiento o ya vencio
                    if($promo->date_end != null){ // tiene fecha de vencimiento
                        $time = time();
                        $time_end = strtotime($promo->date_end);
                        if($time > $time_end){
                            $applyPromo = false;
                            $rsp['msg'] = 'La promoción expiró el <b>'.$promo->date_end.'</b>';
                        }
                    }

                    if($applyPromo){
                        $discount_amount = ($amount*$promo->percent)/100;

                        // Saber si la promo tiene monto limite
                        if($promo->max_value > 0 && $promo->max_value < $discount_amount){
                            $discount_amount = $promo->max_value;
                        }

                        $rsp['ok'] = true;
                        $rsp['id'] = $promo->id;
                        $rsp['discount_amount'] = (float) $discount_amount;
                    }

                    $rsp['promo'] = $promo;
                } else $rsp['msg'] = 'La promoción no existe';
            } else $rsp['msg'] = 'Debe especificar el importe al que se aplica la promoción';
        } else $rsp['msg'] = 'Ingrese el código promocional';
        break;
}

echo json_encode($rsp);