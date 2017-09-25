<?php
header('Content-Type: application/json');
/**
 * Detalle de una direccion
 */
$ref = isset($_GET['ref']) ? $_GET['ref'] : '';

$json = file_get_contents("https://www.uber.com/api/address-details?reference=".$ref."&type=GOOGLE_PLACES");

$obj = json_decode($json);

$response['lat'] = $obj->latitude;
$response['lng'] = $obj->longitude;

echo json_encode($response);