<?php
namespace SquareServerless;

use SquareConnect\Model\OrderFulfillment;
use SquareConnect\Model\OrderFulfillmentPickupDetails;
use SquareConnect\Model\OrderFulfillmentRecipient;
use SquareConnect\ApiClient;

class Order {

    private $orderRequest;
    private $order;
    private $locationId;

    public function __construct()
    {
        $apiClient = new ApiClient();
        $apiClient->getConfig()->setHost('https://connect.squareupsandbox.com');
        $this->api = new \SquareConnect\Api\OrdersApi($apiClient);
        $this->order = new \SquareConnect\Model\Order();
        $this->orderRequest = new \SquareConnect\Model\CreateOrderRequest();
        $sqSecrets = new SquareSecrets();
        $sqSecrets->setAccessToken();
        $loc = new Locations();
        $loc->fetch();
        // todo: do not use first location automatically
        $this->locationId = $loc->getAsArray()[0]['id'];
    }

    public function build($data) : void
    {
        // get inventory, create a hashmap with the info we need for order creation
        $inv = new Inventory();
        $objects = $inv->fetch('ITEM');
        $lineItemData = [];
        /* @var \SquareConnect\Model\CatalogObject $itemObject */
        foreach ($objects as $itemObject) {
            /* @var \SquareConnect\Model\CatalogItem $item */
            $item = $itemObject->getItemData();
            $variations = $item->getVariations();
            foreach ($variations as $variationObject) {
                /* @var \SquareConnect\Model\CatalogItemVariation $variation */
                $variation = $variationObject->getItemVariationData();
                $variationId = $variationObject->getId();
                $variationMoney = $variation->getPriceMoney();
                $lineItemData[$variationId] = ['money' => $variationMoney];
            }
        }
        $lineItems = [];
        foreach($data['items'] as $submittedItem) {
            $id = $submittedItem['itemId'];
            if (isset($lineItemData[$id])) {
                $lineItem = new \SquareConnect\Model\OrderLineItem();
                $lineItem->setQuantity("1");
                $lineItem->setCatalogObjectId($id);
                $lineItem->setTotalMoney($lineItemData[$id]);
                $lineItems[] = $lineItem;
            }
        }
        $this->order->setLineItems($lineItems);

        // add fulfillment info in order to get this order into the seller dashboard
        $now = time();
        $fullfillment = new OrderFulfillment();
        $fullfillment->setType('PICKUP');
        $fullfillment->setState('PROPOSED');
        $recipient = new OrderFulfillmentRecipient();
        $recipient->setDisplayName('Kiosk Customer');
        $pickup = new OrderFulfillmentPickupDetails();
        $pickup->setRecipient($recipient);
        $pickup->setExpiresAt(date('Y-m-d\TH:i:s\Z', $now + 3600));
        $pickup->setAutoCompleteDuration('P0DT1H0S');
        $pickup->setScheduleType('SCHEDULED');
        $pickup->setPickupAt(date('Y-m-d\TH:i:s\Z', $now + 600));
        $pickup->setPickupWindowDuration('PT1H0S');
        $pickup->setPrepTimeDuration('PT10M');
        $fullfillment->setPickupDetails($pickup);
        $this->order->setFulfillments([$fullfillment]);

        // done creating the order, set up the request
        $this->orderRequest->setOrder($this->order);
        $this->orderRequest->setIdempotencyKey(uniqid());
    }

    public function submit() : \SquareConnect\Model\CreateOrderResponse
    {
        /* @var \SquareConnect\Model\CreateOrderResponse $result */
        $result = $this->api->createOrder($this->locationId, $this->orderRequest);
        // do stuff?
        return $result;
    }
}