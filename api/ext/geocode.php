<?php
include('_base.php');
/**
 * Geocodificacion
 * -address: obtener coordenadas de una direccion
 * -latlng: obtener direccion de coordenadas
 */
$lat    	= isset($_GET['lat']) ? $_GET['lat'] : '';
$lng    	= isset($_GET['lng']) ? $_GET['lng'] : '';
$address	= isset($_GET['adr']) ? str_replace(' ', '+', $_GET['adr']) : '';

$param = '';
if(!empty($address)){
	$param .= '&address='.$address.'&components=country:PE';
} else if(is_numeric($lat) && $lat != 0){
	$param .= '&latlng='.$lat.','.$lng;
}

$url = 'https://maps.googleapis.com/maps/api/geocode/json?'.$param.'&key='.$stg->key_maps;

//exit($url);

$json = @file_get_contents($url);
$items = @json_decode($json);


$response['lat'] = 0;
$response['lng'] = 0;
$response['adr'] = '';

if($items){
	$results = $items->results;
	if(count($results) > 0){
		$item = $results[0];
		$response['lat'] = $item->geometry->location->lat;
		$response['lng'] = $item->geometry->location->lng;
		$response['adr'] = $item->formatted_address;
	}
}

echo json_encode($response);