<?php
include('_base.php');
global $db, $user;

$smarty->assign('page_title', 'Modulos');

$smarty->assign('menu_all', $user->getMenuAll());

$smarty->display(PAGE.'.tpl');