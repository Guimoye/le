<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

define('PAGE', 	basename($_SERVER["SCRIPT_FILENAME"], '.php')); // Pagina actual
define('PAGE_FILE', basename($_SERVER["SCRIPT_FILENAME"])); // Pagina actual con extension

include('inc/util.php');
include('inc/mysql.php');
include('inc/user.php');
require('inc/smarty/Smarty.class.php');
$uu 	= new Util();
$db 	= new MySQL();
$user	= new User();
$stg 	= $db->getSettings();

$stg->page 	    = PAGE;
$stg->page_file = PAGE_FILE;
$stg->url_cms   = URL_CMS;

$user->loadPerms(true, PAGE_FILE);

// Verificar si es usuario logeado
if(PAGE != "login" && !$user->isLogged()){
	header("Location: login.php?r=".base64_encode($_SERVER['REQUEST_URI']));
	exit;
}

$smarty = new Smarty;
$smarty->setCompileDir('inc/smarty/templates_c');
$smarty->assign('stg', $stg);
$smarty->assign('u', $user->getInfo()); // Informacion del usuario
$smarty->assign('v', '0.0.8'); // Version (para borrar cache de css/js)

$smarty->assign('url_home', $user->getHome());
$smarty->assign('menu', $user->getMenu());

if(PAGE != "login" && $user->state != 1){
	$smarty->display('e403.tpl');
	exit;
}

if(PAGE != "login" && !$user->see(PAGE_FILE)){
    $smarty->display('e404.tpl');
    exit;
}