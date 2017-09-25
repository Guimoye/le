<?php
include("_base.php");
include("../inc/arrays.php");
global $db,$user,$rsp;



// Conductores
$dvrs_total 	= 0;
$dvrs_active 	= 0;
$dvrs_busy 		= 0;
$dvrs_inactive	= 0;
$dvrs_list 		= [];

$SQL = "SELECT * FROM drivers WHERE lat != 0 AND lng != 0 AND state = 1";
$os = $db->get($SQL);
while($o = $os->fetch_object()){
	$o->pic 			= empty($o->pic) ? 'img/ph_person.png' : URL_CDN.'drivers/small/'.$o->pic.'.jpg';
	$o->date_tracked	= ($o->tracked > 0) ? date("d/m/Y g:i a", $o->tracked) : 'Sin datos';

	++$dvrs_total;

	if($o->tracked > time()){
		if($o->id_race == 0){
			$o->state = 'active';
			++$dvrs_active;
		} else {
			$o->state = 'busy';
			++$dvrs_busy;
		}
	} else {
		$o->state = 'inactive';
		++$dvrs_inactive;
	}

	$dvrs_list[] = $o;
}

$rsp['drivers'] = [
	'total' 	=> $dvrs_total,
	'active' 	=> $dvrs_active,
	'busy' 		=> $dvrs_busy,
	'inactive'	=> $dvrs_inactive,
	'list' 		=> $dvrs_list
];

echo json_encode($rsp);