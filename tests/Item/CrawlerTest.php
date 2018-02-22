<?php

namespace Chougron\AliexpressCrawler\Item;

final class CrawlerTest extends \PHPUnit\Framework\TestCase
{
    public function testCrawlingSimple()
    {
        $crawler = $this->getCrawlerObject("simple.html");
        $this->assertEquals($crawler->getType(),"simple");
        $this->assertEquals($crawler->getName(), "Nice coat");
        $this->assertEquals($crawler->getUrl(), "https://github.com/chougron/aliexpress-crawler");
        $this->assertEquals($crawler->getShopName(), "The Nice Coat Shop");
        $this->assertEquals($crawler->getShopUrl(), "https://shop-name.test");
        $this->assertEquals($crawler->getId(), "154040");
        $this->assertEquals($crawler->getCost(), "3.37");
        $this->assertEquals($crawler->getMaxCost(), "3.37");
        $this->assertEquals($crawler->getGalleryImages(), [
            "https://images.test/1.jpg",
            "https://images.test/2.jpg",
            "https://images.test/3.jpg"
        ]);
        $sku = new Sku(json_decode('{"skuPropIds":"","skuAttr":"","skuVal":{"actSkuCalPrice":"3.37","availQuantity":"56"}}'));
        $this->assertEquals($crawler->getSkus(), [$sku]);
    }

    public function testCrawlingConfigurable()
    {
        $crawler = $this->getCrawlerObject("configurable.html");
        $this->assertEquals($crawler->getType(),"configurable");
        $this->assertEquals($crawler->getName(), "T-Shirt for male");
        $this->assertEquals($crawler->getUrl(), "https://github.com/chougron/aliexpress-crawler");
        $this->assertEquals($crawler->getShopName(), "The Nice Coat Shop");
        $this->assertEquals($crawler->getShopUrl(), "https://shop-name.test");
        $this->assertEquals($crawler->getId(), "24898791");
        $this->assertEquals($crawler->getCost(), "9.99");
        $this->assertEquals($crawler->getMaxCost(), "15.49");
        $this->assertEquals($crawler->getGalleryImages(), [
            "https://images.test/1.jpg",
            "https://images.test/2.jpg",
            "https://images.test/3.jpg"
        ]);
        $sku1 = new Sku(json_decode('{"skuPropIds":"29","skuAttr":"14:29#S","skuVal":{"actSkuCalPrice":"9.99","availQuantity":"56"}}'));
        $sku2 = new Sku(json_decode('{"skuPropIds":"30","skuAttr":"14:30#M","skuVal":{"actSkuCalPrice":"12.99","availQuantity":"45"}}'));
        $sku3 = new Sku(json_decode('{"skuPropIds":"31","skuAttr":"14:31#L","skuVal":{"actSkuCalPrice":"15.49","availQuantity":"45"}}'));
        $this->assertEquals($crawler->getSkus(), [$sku1,$sku2,$sku3]);

        $this->assertEquals($crawler->getSkuImage($sku1),"https://images.test/S.jpg");
        $this->assertEquals($crawler->getSkuImage($sku2),"https://images.test/M.jpg");
        $this->assertEquals($crawler->getSkuImage($sku3),"https://images.test/L.jpg");
    }

    /**
     * @param string $file
     * @return Crawler
     */
    private function getCrawlerObject($file = "simple.html")
    {
        $crawler = new Crawler("");
        $reflection = new \ReflectionClass($crawler);

        $html_value = $this->getFixtureHtml($file);
        $html_property = $reflection->getProperty("html");
        $html_property->setAccessible(true);
        $html_property->setValue($crawler,$html_value);

        $dom_value = new \DOMDocument();
        @$dom_value->loadHTML($html_value);
        $dom_property = $reflection->getProperty("dom");
        $dom_property->setAccessible(true);
        $dom_property->setValue($crawler,$dom_value);

        $xPath_value = new \DOMXPath($dom_value);
        $xPath_property = $reflection->getProperty("xPath");
        $xPath_property->setAccessible(true);
        $xPath_property->setValue($crawler,$xPath_value);

        return $crawler;
    }

    /**
     * @param string $file
     * @return string
     */
    private function getFixtureHtml($file = "simple.html")
    {
        $path = __DIR__ . "/fixtures/".$file;
        if(!file_exists($path))
        {
            return "";
        }
        return file_get_contents($path);
    }
}