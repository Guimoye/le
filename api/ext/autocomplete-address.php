<?php
header('Content-Type: application/json');
/**
 * Autocompletar direccion
 */
$lat    = isset($_GET['lat']) ? $_GET['lat'] : '';
$lng    = isset($_GET['lng']) ? $_GET['lng'] : '';
$query  = isset($_GET['query']) ? $_GET['query'] : '';
$query  = str_replace(' ', '+', $query);

$response = [];

$json = file_get_contents('https://www.uber.com/api/autocomplete-address?latitude='.$lat.'&longitude='.$lng.'&query='.$query);
$items = json_decode($json);

foreach($items as $item){
	$prediction['title'] = $item->title;
	$prediction['subtitle'] = $item->subtitle;
	$prediction['content'] = $item->content;
	$prediction['reference'] = $item->reference;
	$response[] = $prediction;
}

echo json_encode($response);