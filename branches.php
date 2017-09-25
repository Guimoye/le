<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Sucursales');

$smarty->assign('can_branches', $user->can('branches'));

$smarty->assign('branches', $db->arr("SELECT * FROM branches WHERE state = 1"));

$smarty->display(PAGE.'.tpl');