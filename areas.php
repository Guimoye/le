<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Areas de producción');

$smarty->assign('can_areas', $user->can('areas'));

$SQL = "SELECT * FROM areas WHERE id_branch = $user->id_branch AND state = 1";
//print_r($db->arr($SQL));exit;
$smarty->assign('areas', $db->arr($SQL));

$smarty->display(PAGE.'.tpl');