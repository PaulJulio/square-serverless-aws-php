<?php
require_once __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('UTC');

function listLocations($eventData = '') : array
{
    try {
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
    } catch (\Exception $e) {
        return [
            "body" => $e->getMessage(),
            "statusCode" => 500,
        ];
    } catch (\Throwable $e) {
        return [
            "body" => $e->getMessage(),
            "statusCode" => 500,
        ];
    }
}
function staticCheckout($eventData = '') : array
{
    return [
        "body" => file_get_contents(__DIR__ . "/static/checkout.html"),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "text/html",
        ],
    ];
}
function staticKiosk($eventData = '') : array
{
    return [
        "body" => file_get_contents(__DIR__ . "/static/kiosk.html"),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "text/html",
        ],
    ];
}
function staticJs($eventData = '') : array
{
    // todo: check that asset exists
    $data = json_decode($eventData, true);
    $asset = end(explode('/', $data['path']));
    return [
        "body" => file_get_contents(__DIR__ . "/static/js/" . $asset),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "application/javascript",
        ]
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
function inventory($eventData) : array
{
    $inventory = new \SquareServerless\Inventory();
    $body = $inventory->fetchKiosk();
    return [
        "body" => json_encode($body),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "application/json",
        ]
    ];
}
function sqpayjs($eventData) : array
{
    $secrets = new \SquareServerless\SquareSecrets();
    $appId = $secrets->getClientID();
    $locations = new \SquareServerless\Locations();
    // todo: don't use the first location automatically
    $locationId = $locations->getAsArray()[0]['id'];
    $src = file_get_contents(__DIR__ . '/static/sqpay.js');
    $output = strtr($src, ['%sq-app-id%' => $appId, '%sq-location-id%' => $locationId]);
    return [
        "body" => $output,
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "application/javascript",
        ]
    ];
}
function order($eventData) : array
{
    $alldata = json_decode($eventData, true);
    // data['body'] will be set if this is from the browser, but not if invoked directly
    if (isset($alldata['body'])) {
        $data = json_decode($alldata['body'], true);
    } else {
        $data = $alldata;
    }
    $order = new \SquareServerless\Order();
    $order->build($data);
    $response = $order->submit();
    $order = $response->getOrder();
    $charge = new \SquareServerless\Charge();
    $chargeResponse = $charge->chargeOrder($order, $data['nonce']);
    $transaction = $chargeResponse->getTransaction();
    // todo: sort out the actual status from the chargeresponse
    $status = 'SUCCESS';
    return [
        "body" => json_encode([
            'id' => $transaction->getId(),
            'status' => $status,
        ]),
        "statusCode" => 200,
        "headers" => [
            "Content-type" => "application/json",
        ],
    ];
}

function emptyCatalog($eventData) : array
{
    $data = json_decode($eventData, true);
    try {
        $cs = new \SquareServerless\CatalogSeed();
        $cs->empty();
        $body = "done";
    } catch (\Exception $e) {
        $body = $e->getMessage();
    } catch (\Throwable $e) {
        $body = $e->getMessage();
    }
    return [
        "body" => $body,
        "statusCode" => 200,
    ];
}

function seed($eventData) : array
{
    $data = json_decode($eventData, true);
    try {
        $cs = new \SquareServerless\CatalogSeed();
        $results = $cs->seed();
        $body = json_encode($results, JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        $body = $e->getMessage();
    } catch (\Throwable $e) {
        $body = $e->getMessage();
    }
    return [
        "body" => $body,
        "statusCode" => 200,
    ];
}