<?php

namespace Chougron\AliexpressCrawler\Item;

final class SkuTest extends \PHPUnit\Framework\TestCase
{
    const TEST_ID = "14";
    const TEST_PRICE = "10.43";
    const TEST_QTY = "30";
    const TEST_MODEL = "14:10#3124B";

    public function testConstructor()
    {
        $object = $this->prepareObject();
        $sku = new Sku($object);

        $this->assertEquals(
            $sku->id,
            self::TEST_ID
        );
        $this->assertEquals(
            $sku->price,
            self::TEST_PRICE
        );
        $this->assertEquals(
            $sku->qty,
            self::TEST_QTY
        );
        $this->assertEquals(
            $sku->model,
            '3124B'
        );
    }

    private function prepareObject()
    {
        $object = new \stdClass();
        $object->skuPropIds = self::TEST_ID;

        $skuVal = new \stdClass();
        $skuVal->actSkuCalPrice = self::TEST_PRICE;
        $skuVal->availQuantity = self::TEST_QTY;

        $object->skuVal = $skuVal;
        $object->skuAttr = self::TEST_MODEL;

        return $object;
    }
}