<?php
//exit(md5('demo'));
include('_base.php');

$return = 'index.php';
if(isset($_GET['r'])){
	//TODO: Regresar
    //$return = base64_decode($_GET['r']);
}

if($user->isLogged()){
	header('Location: '.$return);
}

$error = false;

if(isset($_POST["username"])){
	$username = addslashes(trim($_POST["username"]));
	$password = md5(addslashes(trim($_POST["password"])));

	if($user->login($_POST['username'], $_POST['password'])){
		header('Location: '.$return);
	} else {
		$error = true;
	}
}

$smarty->assign('error', $error);
$smarty->display('login.tpl');