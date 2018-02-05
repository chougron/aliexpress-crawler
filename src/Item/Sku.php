<?php

namespace Chougron\AliexpressCrawler\Item;

class Sku {
    public $id;
    public $price;
    public $qty;

    public function __construct($object)
    {
        $this->id = $object->skuPropIds;
        $this->price = $object->skuVal->actSkuCalPrice;
        $this->qty = $object->skuVal->availQuantity;
    }
}