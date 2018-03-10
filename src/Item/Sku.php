<?php
/**
 * Sku File Doc Comment
 *
 * @category Class
 * @package  Chougron\AliexpressCrawler\Item
 * @author   Camille Hougron <camille.hougron@gmail.com>
 * @license  https://github.com/chougron/aliexpress-crawler/blob/master/LICENSE MIT
 * @link     https://github.com/chougron/aliexpress-crawler
 */
namespace Chougron\AliexpressCrawler\Item;
/**
 * Class Sku
 *
 * @category Class
 * @package  Chougron\AliexpressCrawler\Item
 * @author   Camille Hougron <camille.hougron@gmail.com>
 * @license  https://github.com/chougron/aliexpress-crawler/blob/master/LICENSE MIT
 * @link     https://github.com/chougron/aliexpress-crawler
 */
class Sku
{
    public $id;
    public $price;
    public $qty;
    public $model = null;

    /**
     * Sku constructor.
     *
     * @param \stdClass $object The object with information
     */
    public function __construct($object, $modelList)
    {
        $this->id = $object->skuPropIds;
        $this->price = $object->skuVal->actSkuCalPrice ? $object->skuVal->actSkuCalPrice : $object->skuVal->skuCalPrice;
        $this->qty = $object->skuVal->availQuantity;
        $this->model = $modelList[$this->id];
    }
}