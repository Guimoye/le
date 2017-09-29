<?php
$action = isset($_POST['action'])   ? $_POST['action']  : '';
$id     = isset($_POST['id'])       ? $_POST['id']      : 0;

if(empty($action)){
    include('_base.php');
    global $db,$user;

    $smarty->assign('page_title', 'Opciones de sistema');

    $smarty->assign('can_settings', $user->can('settings'));

    $smarty->display(PAGE.'.tpl');

} else {
    include('ajax/_base.php');
    global $db, $rsp;

    // Guardar ajustes
    function update($data){
        global $db;
        $ok = true;
        foreach($data as $key => $val){
            if(!$db->query("UPDATE settings SET value = '$val' WHERE name = '$key'")){
                $ok = false;
            }
        }
        return $ok;
    }

    switch($action){

        // Ajustes generales
        case 'general':
            checkEditPerm('settings');
            $data = [];
            $data['brand']              = @$_POST['brand'] ?: '';
            $data['coin']               = @$_POST['coin'] ?: '';
            $data['tc']                 = @$_POST['tc'] ?: '';
            $data['igv']                = @$_POST['igv'] ?: '';
            $data['comp_name']          = @$_POST['comp_name'] ?: '';
            $data['comp_ruc']           = @$_POST['comp_ruc'] ?: '';
            if(update($data)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Erroe interno::DB';
            break;

        // IGV
        case 'igv':
            checkEditPerm('igv');
            $data = [];
            $data['igv'] = $_POST['igv'];
            if(update($data)){
                $rsp['ok'] = true;
            } else $rsp['msg'] = 'Erroe interno::DB';
            break;
    }

    echo json_encode($rsp);
}