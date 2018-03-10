<?php

namespace Chougron\AliexpressCrawler\Item;

class Sku {
    public $id;
    public $price;
    public $qty;
    public $model = null;

    public function __construct($object, $modelList)
    {
        $this->id = $object->skuPropIds;
        $this->price = $object->skuVal->actSkuCalPrice ? $object->skuVal->actSkuCalPrice : $object->skuVal->skuCalPrice;
        $this->qty = $object->skuVal->availQuantity;
        $this->model = $modelList[$this->id];
    }
}