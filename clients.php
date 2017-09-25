<?php
include('_base.php');
include("inc/arrays.php");
global $db,$user;

$smarty->assign('page_title', 'Clientes');
$smarty->assign('st_client', $st_client);
$smarty->assign('can_clients', $user->can('clients'));

$smarty->display(PAGE.'.tpl');