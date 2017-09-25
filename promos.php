<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Ofertas');

$smarty->assign('can_promos', $user->can('promos'));

$smarty->assign('promos', $db->arr("SELECT * FROM promos WHERE state = 1"));

$smarty->display(PAGE.'.tpl');