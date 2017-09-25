<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('inc/util.php');
include('inc/mysql.php');
include('inc/user.php');
$uu		= new Util();
$db 	= new MySQL();
$user 	= new User();
$stg 	= $db->getSettings();

$id_ordpro = 145;
//$id_ordpro = 39;

include('inc/stock.php');

$stk = new Stock();
$stk->is_debug = true;

//$stk->byOrdpro($id_ordpro);
//$stk->backStock($id_ordpro);
$stk->bySupply(17,2,1);
