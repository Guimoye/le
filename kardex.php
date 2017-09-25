<?php
include('_base.php');
global $db,$user;
$id_supply = @$_GET['id_supply'] ?: 12;

$smarty->assign('page_title', 'Kardex');

$smarty->assign('can_inventory', $user->can('inventory'));

//$id_supply = 12; // Aceite

$supply = $db->o('supplies', $id_supply);

/*if(!$supply){
    exit('not found');
}*/

$smarty->assign('id_supply', $id_supply);
$smarty->assign('supplies', $db->arr("SELECT * FROM supplies WHERE state != 0 ORDER BY name"));
$smarty->assign('kardexs', $db->arr("SELECT ka.* FROM kardex ka WHERE ka.id_supply = $id_supply"));
$smarty->assign('supply', $supply);

$smarty->display(PAGE.'.tpl');