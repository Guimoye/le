<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Contabilidad');
$smarty->assign('sidebar_closed', true);

$smarty->assign('can_accounting', $user->can('accounting'));

$smarty->assign('accounting', $db->arr("SELECT * FROM accounting WHERE state = 1"));

$smarty->display(PAGE.'.tpl');