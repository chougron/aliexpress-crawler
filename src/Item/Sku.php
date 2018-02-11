<?php

namespace Chougron\AliexpressCrawler\Item;

class Sku {
    public $id;
    public $price;
    public $qty;
    public $model = null;

    public function __construct($object)
    {
        $this->id = $object->skuPropIds;
        $this->price = $object->skuVal->actSkuCalPrice;
        $this->qty = $object->skuVal->availQuantity;

        if($object->skuAttr){
            //skuAttr is in the form "14:10#3124B" with model being 3124B
            $exploded = explode("#", $object->skuAttr);
            $this->model = count($exploded) == 2 ? $exploded[1] : null;
        }
    }
}