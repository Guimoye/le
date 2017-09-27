<?php
include('_base.php');
global $db;

$driver = $db->o('drivers', _GET_INT('id'));

if($driver == FALSE){
    $smarty->display('e404.tpl');
    exit;
}

$smarty->assign('page_title', 'Conductor');
$smarty->assign('driver', $driver);

$smarty->display(PAGE.'.tpl');