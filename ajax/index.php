<?php
include('_base.php');
include("controller.php");
global $rsp;

$cc = new Controller();

//$_POST = $_GET;

$api = isset($_POST['api']) ? $_POST['api'] : '';

switch($api) {

	case 'get_levels':      $cc->getLevels();   break;
	case 'get_branches':    $cc->getBranches(); break;
	case 'get_unimeds':     $cc->getUnimeds();  break;
	case 'get_storages':    $cc->getStorages(); break;
	case 'get_areas':       $cc->getAreas(); break;
	case 'get_categories':  $cc->getCategories(); break;

	case 'set_local_branch':    $cc->setLocalBranch(@$_POST['id_branch']); break;

}

header('Content-Type: application/json');
echo json_encode($rsp);