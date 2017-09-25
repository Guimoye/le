<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Cajas');

$smarty->assign('can_boxes', $user->can('boxes'));

$smarty->assign('boxes', $db->arr("SELECT * FROM boxes WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));

$smarty->display(PAGE.'.tpl');