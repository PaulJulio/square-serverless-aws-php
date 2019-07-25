<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$order = new \SquareServerless\Order();
$order->build($data);
$response = $order->submit();
$order = $response->getOrder();
$charge = new \SquareServerless\Charge();
$chargeResponse = $charge->chargeOrder($order, $data['nonce']);
$transaction = $chargeResponse->getTransaction();
// todo: sort out the actual status from the chargeresponse
$status = 'SUCCESS';
header("Content-type: application/json");
echo json_encode([
    'id' => $transaction->getId(),
    'status' => $status,
]);