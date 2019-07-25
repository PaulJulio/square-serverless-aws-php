<?php
namespace SquareServerless;

use SquareConnect\Model\ChargeRequest;
use SquareConnect\Model\ChargeResponse;
use SquareConnect\ApiClient;

class Charge {

    private $api;
    private $locationId;

    public function __construct()
    {
        $apiClient = new ApiClient();
        $apiClient->getConfig()->setHost('https://connect.squareupsandbox.com');
        $this->api = new \SquareConnect\Api\TransactionsApi($apiClient);
        $loc = new Locations();
        $loc->fetch();
        // todo: do not use first location automatically
        $this->locationId = $loc->getAsArray()[0]['id'];
    }

    public function chargeOrder(\SquareConnect\Model\Order $order, string $nonce) : ChargeResponse
    {
        $payload = new ChargeRequest();
        $payload->setIdempotencyKey(uniqid());
        $payload->setAmountMoney($order->getTotalMoney());
        $payload->setCardNonce($nonce);
        $payload->setOrderId($order->getId());
        $response = $this->api->charge($this->locationId, $payload);
        return $response;
    }
}