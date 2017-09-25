<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Almacenes');

$smarty->assign('can_storages', $user->can('storages'));

$smarty->assign('storages', $db->arr("SELECT * FROM storages WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));
$smarty->assign('areas', $db->arr("SELECT * FROM areas WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));

$smarty->display(PAGE.'.tpl');