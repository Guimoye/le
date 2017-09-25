<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Proveedores');

$smarty->assign('can_providers', $user->can('providers'));

$smarty->assign('providers', $db->arr("SELECT * FROM providers WHERE state = 1"));

$smarty->display(PAGE.'.tpl');