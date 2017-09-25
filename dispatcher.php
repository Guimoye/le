<?php
include('_base.php');
global $db;

$id_fare = isset($_GET['id_fare']) ? $_GET['id_fare'] : $stg->id_fare;

$fare = $db->o("SELECT * FROM fares WHERE id = $id_fare");
if(!$fare){
    $smarty->display('e404.tpl');
    exit;
}

$stg->ext_id_fare = $id_fare;

$smarty->assign('page_title', 'Despachador');
$smarty->assign('zone', $fare);

$smarty->display(PAGE.'.tpl');