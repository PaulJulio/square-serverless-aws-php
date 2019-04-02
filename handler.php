<?php
require_once realpath(__DIR__ . '/vendor/autoload.php');

function listLocations($eventData) : array
{
	$locations = new \SquareServerless\Locations();
	return [
		"body" => json_encode([
			'locations' => $locations->getAsArray(),
			'error' => $locations->getError(),
		]),
		"statusCode" => 200,
        "headers" => [
            "Content-type" => "application/json",
        ],
	];
}
function staticCheckout($eventData) : array
{
    return [
        "body" => file_get_contents(__DIR__ . "/static/checkout.html"),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "text/html",
        ],
    ];
}
function processCheckout($eventData) : array
{
    $eventArray = json_decode($eventData, true);
    $data = json_decode($eventArray['body'], true);
    $checkout = new \SquareServerless\SquareCheckout();
    $body = [
        'url' => '',
        'error' => '',
    ];
    try {
        if (!is_numeric($data['amount'])) {
            throw new \Exception('Amount must be numeric');
        }
        $checkout->setAmount($data['amount']);
        $body['url'] = $checkout->charge();
    } catch (Exception $e) {
        $body['error'] = $e->getMessage();
    }
    return [
        "body" => json_encode($body),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "application/json",
        ],
    ];
}
