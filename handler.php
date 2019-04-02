<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');

function listLocations($eventData) : array
{
	$locations = new \SquareServerless\Locations();
	return [
		"body" => [
			'locations' => $locations->getAsArray(),
			'error' => $locations->getError(),
		],
		"statusCode" => 200,
	];
}
