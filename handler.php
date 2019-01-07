<?php
function hello($eventData) : array
{
	return [
		"body" => "hello from PHP " . PHP_VERSION,
		"statusCode" => 200,
	];
}
