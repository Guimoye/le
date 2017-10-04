<?php
session_start();
include('inc/util.php');
include('inc/mysql.php');
include('inc/user.php');
$uu 	= new Util();
$db 	= new MySQL();
$user	= new User();
$user->loadPerms(true);

header('Location: '.$user->getHome());