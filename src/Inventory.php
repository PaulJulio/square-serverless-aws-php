<?php
namespace SquareServerless;

class Inventory {

    public function fetch($types) : array
    {
        $results = [];

        $sqSecret = new SquareSecrets();
        $sqSecret->setAccessToken();
        $catalog_api = new \SquareConnect\Api\CatalogApi();
        $response = $catalog_api->listCatalog(null, $types);
        // todo: check for response->getCursor() and implement paging
        $objects = $response->getObjects();
        /* @var \SquareConnect\Model\CatalogObject $object */
        foreach ($objects as $object) {
            $results[] = $object;
        }
        return $results;
    }

    public function fetchKiosk() : array
    {
        // need a separate fetch to get image urls
        // todo: cache these or find some way to not need to get the entire image collection
        $objects = $this->fetch('IMAGE');
        $imageUrls = [];
        /* @var \SquareConnect\Model\CatalogObject $object */
        /* @var \SquareConnect\Model\CatalogImage $item */
        foreach ($objects as $object) {
            $item = $object->getImageData();
            $imageUrls[$object->getId()] = $item->getUrl();
        }
        // todo: filter for the Kiosk category
        $results = [];
        $objects = $this->fetch('ITEM');
        /* @var \SquareConnect\Model\CatalogObject $object */
        /* @var \SquareConnect\Model\CatalogItem $item */
        foreach ($objects as $object) {
            $item = $object->getItemData();
            $data = [];
            $data['id'] = $object->getId();
            $data['name'] = $item->getName();
            $data['description'] = $item->getDescription();
            $data['category_id'] = $item->getCategoryId();
            $data['image_url'] = $imageUrls[$object->getImageId()];
            $data['variations'] = [];
            $variationObjects = $item->getVariations();
            /* @var \SquareConnect\Model\CatalogItemVariation $variation */
            foreach ($variationObjects as $variationObject) {
                $variation = $variationObject->getItemVariationData();
                $itemVariation = [];
                $itemVariation['item_id'] = $variationObject->getId();
                $itemVariation['name'] = $variation->getName();
                $itemVariation['sku'] = $variation->getSku();
                $itemVariation['price'] = $variation->getPriceMoney()->getAmount() / 100;
                $data['variations'][] = $itemVariation;
            }
            $results[] = $data;
        }

        return $results;
    }
}