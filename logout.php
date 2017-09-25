<?php
session_start();

$_SESSION['id_user'] = 0;
unset($_SESSION['id_user']);
session_destroy();

if(isset($_SERVER["HTTP_REFERER"])){
    header('Location: login.php?r='.base64_encode($_SERVER["HTTP_REFERER"]));
} else {
    header('Location: login.php');
}