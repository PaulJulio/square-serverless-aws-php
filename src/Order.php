<?php
namespace SquareServerless;

class Order {

    private $orderRequest;
    private $order;
    private $locationId;

    public function __construct()
    {
        $this->api = new \SquareConnect\Api\OrdersApi();
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