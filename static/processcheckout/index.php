<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$checkout = new \SquareServerless\SquareCheckout();
$body = [
    'url' => '',
    'error' => '',
];
try {
    $amount = floatval($data['amount']);
    if ($amount < 1) {
        throw new \Exception('Amount must be numeric and greater than 1');
    }
    $checkout->setAmount($amount);
    $body['url'] = $checkout->charge();
} catch (Exception $e) {
    $body['error'] = $e->getMessage();
}
header('Content-type: application/json');
echo json_encode($body);
