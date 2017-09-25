<?php
include('_base.php');
global $uu, $db, $user;

$smarty->assign('page_title', 'Categorías');

$smarty->assign('can_categories', $user->can('categories'));

$smarty->assign('categories', $uu->ordMenu($db->arr("SELECT * FROM categories WHERE state = 1 ORDER BY sort")));

$smarty->display(PAGE.'.tpl');