<?php
namespace SquareServerless;

class SquareCheckout {

    private $amount = 1;
    private $uuid;
    private $locid;

    public function charge() : string
    {
        if (!isset($this->uuid)) {
            $this->uuid = uniqid();
        }
        if (!isset($this->locid)) {
            $ssLocations = new \SquareServerless\Locations();
            $locations = $ssLocations->getLocations();
            /* @var \SquareConnect\Model\Location $location */
            $location = $locations[0];
            $this->locid = $location->getId();
        }
        $sqSecret = new SquareSecrets();
        $sqSecret->setAccessToken();
        $checkout_api = new \SquareConnect\Api\CheckoutApi();
        $request_body = new \SquareConnect\Model\CreateCheckoutRequest(
          [
              "idempotency_key" => $this->uuid,
              "order" => [
                  "line_items" => [
                      [
                          "name" => "Quick Checkout",
                          "quantity" => "1",
                          "base_price_money" => [
                              "amount" => intval($this->amount * 100),
                              "currency" => "USD" // todo: use an environment variable or something better than hard-coding this
                          ]
                      ]
                  ]
              ]
          ]
        );
        // todo: do not use the first location_id automatically
        $response = $checkout_api->createCheckout($this->locid, $request_body);
        return $response->getCheckout()->getCheckoutPageUrl();
    }

    /**
     * @return int
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return mixed
     */
    public function getLocid()
    {
        return $this->locid;
    }

    /**
     * @param mixed $locid
     */
    public function setLocid($locid): void
    {
        $this->locid = $locid;
    }

}