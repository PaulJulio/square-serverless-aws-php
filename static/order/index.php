<?php
require_once __DIR__ . '/../../vendor/autoload.php';
try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $order = new \SquareServerless\Order();
    $order->build($data);
    $response = $order->submit();
    $order = $response->getOrder();
    $charge = new \SquareServerless\Charge();
    $chargeResponse = $charge->chargeOrder($order, $data['nonce']);
    $transaction = $chargeResponse->getTransaction();
    $id = $transaction->getId();
    // todo: sort out the actual status from the chargeresponse
    $status = 'SUCCESS';
} catch (\Exception $e) {
    $status = $e->getMessage();
} catch (\Throwable $e) {
    $status = $e->getMessage();
}
header("Content-type: application/json");
echo json_encode([
    'id' => $id,
    'status' => $status,
]);