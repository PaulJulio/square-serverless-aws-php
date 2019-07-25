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
    if (!is_numeric($data['amount'])) {
        throw new \Exception('Amount must be numeric');
    }
    $checkout->setAmount($data['amount']);
    $body['url'] = $checkout->charge();
} catch (Exception $e) {
    $body['error'] = $e->getMessage();
}
echo json_encode($body);
