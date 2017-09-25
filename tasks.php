<?php
include('_base.php');
include("inc/arrays.php");

$smarty->assign('page_title', 'Despachos');
$smarty->assign('st_driver', $st_driver);

$smarty->display(PAGE.'.tpl');