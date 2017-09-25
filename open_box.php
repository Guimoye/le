<?php
include('_base.php');
global $db, $user;

$stg->return = !empty($_GET['r']) ? $_GET['r'].'.php' : '';

$smarty->assign('page_title', 'Apertura/Cierre de caja');

$smarty->assign('boxes', $db->arr("SELECT * FROM boxes WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));
$smarty->assign('turns', $db->arr("SELECT * FROM turns WHERE state = 1"));

$smarty->display(PAGE.'.tpl');