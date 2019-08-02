<?php
namespace SquareServerless;

use SquareConnect\Api\CatalogApi;
use SquareConnect\Model\BatchUpsertCatalogObjectsRequest;
use SquareConnect\Model\CatalogObject;
use SquareConnect\Model\CatalogObjectBatch;
use SquareConnect\Model\Money;
use SquareConnect\ApiClient;

class CatalogSeed
{
    private $api;

    public function __construct() {
        $sqSecret = new SquareSecrets();
        $sqSecret->setAccessToken();
        $apiClient = new ApiClient();
        $apiClient->getConfig()->setHost('https://connect.squareupsandbox.com');
        $this->api = new CatalogApi($apiClient);
    }

    public function empty()
    {
        $response = $this->api->listCatalog();
        $objects = $response->getObjects();
        foreach ($objects as $object) {
            $this->api->deleteCatalogObject($object->getId());
        }
    }
    public function seed() : array
    {
        $batch = new CatalogObjectBatch(["objects" => $this->getSeedObjects()]);
        $request = new BatchUpsertCatalogObjectsRequest(['idempotency_key' => uniqid(), 'batches'=>[$batch]]);
        $response = $this->api->batchUpsertCatalogObjects($request);
        $errors = $response->getErrors();
        $objects = $response->getObjects();
        return ['errors'=>$errors, 'objects' => count($objects)];
    }

    private function getSeedObjects() : array
    {
        $l= new Locations();
        $locations = $l->getLocations();
        $location = $locations[0]; // todo: don't just use the first location
        /* @var \SquareConnect\Model\Location $location */
        $currency = $location->getCurrency();
        $catalog = [
            'Food' => [
                'Hamburger' => [
                    'Description' => 'Angus Beef with Organic Toppings',
                    'variations' => [
                        'Single Patty' => [
                            'price' => 1,
                        ],
                        'With Cheese' => [
                            'price' => 1.5,
                        ],
                        'Royale' => [
                            'price' => 2,
                        ]
                    ]
                ],
                'Fries' => [
                    'Description' => 'Premium Yukon Gold Spuds',
                    'variations' => [
                        'Small' => [
                            'price' => 1,
                        ],
                        'Medium' => [
                            'price' => 1.25,
                        ],
                        'Large' => [
                            'price' => 1.5,
                        ],
                        'Extra Large' => [
                            'price' => 1.75,
                        ],
                    ]
                ],
            ],
            'Beverage' => [
                'Soda' => [
                    'Description' => 'With pure cane sugar',
                    'variations' => [
                        'Small' => [
                            'price' => 2,
                        ],
                        'Medium' => [
                            'price' => 2.25,
                        ],
                        'Large' => [
                            'price' => 2.5,
                        ],
                        'Extra Large' => [
                            'price' => 2.75,
                        ],
                    ],
                ],
                'Shake' => [
                    'Description' => 'Happy cows make happy shakes',
                    'variations' => [
                        'Vanilla' => [
                            'price' => 3,
                        ],
                        'Strawberry' => [
                            'price' => 3,
                        ],
                        'Chocolate' => [
                            'price => 3,'
                        ],
                    ],
                ],
            ]
        ];
        $objects = [];
        foreach ($catalog as $category => $items) {
            $objects[] = new CatalogObject([
                'type' => 'CATEGORY',
                'id' => '#category_' . $category,
                'category_data' => ['name' => $category],
            ]);
            foreach ($items as $item => $itemData) {
                # todo: add item image
                $objects[] = new CatalogObject([
                    'type' => 'ITEM',
                    'id' => '#item_' . $item,
                    'item_data' => ['name' => $item, 'description' => $itemData['Description'], 'category_id' => '#category_' . $category],
                ]);
                foreach ($itemData['variations'] as $variation => $details) {
                    $objects[] = new CatalogObject([
                        'type' => 'ITEM_VARIATION',
                        'id' => '#variation_' . uniqid(),
                        'item_variation_data' => [
                            'item_id' => '#item_' . $item,
                            'name' => $variation,
                            'pricing_type' => 'FIXED_PRICING',
                            'price_money' => new Money([
                                'amount' => $details['price'] * 100,
                                'currency' => $currency,
                            ]),
                        ],
                    ]);
                }
            }
        }
        return $objects;
    }
}
