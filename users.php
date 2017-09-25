<?php
include('_base.php');
include("inc/arrays.php");

$smarty->assign('page_title', 'Usuarios');
$smarty->assign('st_user', $st_user);

$smarty->assign('can_users', $user->can('users'));

$smarty->display(PAGE.'.tpl');