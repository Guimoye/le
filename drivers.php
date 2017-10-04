<?php
include('_base.php');
include("inc/arrays.php");
global $db;

$smarty->assign('page_title', 'Conductores');
$smarty->assign('st_driver', $st_driver);
$smarty->assign('brands', $db->arr("SELECT * FROM vh_brands WHERE state = 1"));

$smarty->display(PAGE.'.tpl');