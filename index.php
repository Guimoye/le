<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include('controllers/_base.php');
include('inc/router.php');

$route = new Route();
//$route->add('driver/:num/dues-rental', 'dues_rental', 'item');
$route->add(':any', '%', '%');
$route->send();