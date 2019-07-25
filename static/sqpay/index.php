<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$secrets = new \SquareServerless\SquareSecrets();
$appId = $secrets->getClientID();
$locations = new \SquareServerless\Locations();
// todo: don't use the first location automatically
$locationId = $locations->getAsArray()[0]['id'];
$src = file_get_contents(__DIR__ . '/../sqpay.js');
$output = strtr($src, ['%sq-app-id%' => $appId, '%sq-location-id%' => $locationId]);
header("Content-type: application/javascript");
echo $output;