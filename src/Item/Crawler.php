<?php
/**
 * Crawler File Doc Comment
 *
 * @category Class
 * @package  Chougron\AliexpressCrawler\Item
 * @author   Camille Hougron <camille.hougron@gmail.com>
 * @license  https://github.com/chougron/aliexpress-crawler/blob/master/LICENSE MIT
 * @link     https://github.com/chougron/aliexpress-crawler
 */
namespace Chougron\AliexpressCrawler\Item;
/**
 * Class Crawler
 *
 * @category Class
 * @package  Chougron\AliexpressCrawler\Item
 * @author   Camille Hougron <camille.hougron@gmail.com>
 * @license  https://github.com/chougron/aliexpress-crawler/blob/master/LICENSE MIT
 * @link     https://github.com/chougron/aliexpress-crawler
 */
class Crawler
{
    /**
     * The url of the item being crawled
     *
     * @var string
     */
    protected $itemUrl;
    /**
     * The html of the item page crawled
     *
     * @var string
     */
    protected $html;
    /**
     * The DomDocument of the item page crawled
     *
     * @var \DOMDocument
     */
    protected $dom;
    /**
     * The xPath of the item page crawled
     *
     * @var \DOMXPath
     */
    protected $xPath;

    /**
     * Crawler constructor.
     *
     * @param string $url The Url of the item to crawl
     */
    public function __construct($url)
    {
        $this->itemUrl = $url;
    }

    /**
     * Crawl the page of the item
     *
     * @throws \Exception
     * @return void
     */
    public function crawl()
    {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->itemUrl);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $this->html = curl_exec($ch);
            curl_close($ch);

            $this->dom = new \DOMDocument();
            @$this->dom->loadHTML($this->html);
            $this->xPath = new \DOMXPath($this->dom);
        } catch (\Exception $e){
            throw new \Exception("Error while getting the data : ".$e->getMessage());
        }
    }

    /**
     * Get the type of the item
     *
     * @return string
     */
    public function getType()
    {
        $skus = $this->getSkus();
        if (count($skus) == 1) {
            return "simple";
        } else {
            return "configurable";
        }
    }

    /**
     * Get the Image of a SKU
     *
     * @param Sku $sku The Sku from which we want the image
     *
     * @return string
     */
    public function getSkuImage(Sku $sku)
    {
        $id = $sku->id;
        $imageQuery = $this->xPath->query("//a[@data-sku-id='$id']/img/@bigpic");
        return $imageQuery->item(0)->textContent;
    }

    /**
     * Get the different models of the skus
     *
     * @return string[]
     */
    public function getModelList()
    {
        if(!is_null($this->modelList)){
            return $this->modelList;
        }
        $models = [];
        $modelListQuery = $this->xPath->query("//li[@class='item-sku-image']/a");
        foreach($modelListQuery as $modelElement){
            foreach($modelElement->attributes as $attribute)
            {
                if($attribute->name == "data-sku-id"){
                    $id = $attribute->value;
                }
                if($attribute->name == "title"){
                    $name = $attribute->value;
                }
            }
            $models[$id] = $name;
        }
        $this->modelList = $models;
        return $models;
    }
    /**
     * Do not fetch them more than once
     *
     * @var string[]
     */
    private $modelList = null;

    /**
     * Get the different Skus of the item
     *
     * @return Sku[]
     */
    public function getSkus()
    {
        if (!is_null($this->_skus)) {
            return $this->_skus;
        }
        $skus = [];
        $skuMatches = [];
        preg_match('/var skuProducts=(\[.*\]);/', $this->html, $skuMatches);
        if (count($skuMatches) == 2) {
            $json = $skuMatches[1];
            $codedSkus = json_decode($json);
            foreach ($codedSkus as $codedSku) {
                $skus[] = new Sku($codedSku, $this->getModelList());
            }
        }
        $this->_skus = $skus;
        return $skus;
    }
    /**
     * Do not fetch them more than once
     *
     * @var Sku[]
     */
    private $_skus = null;

    /**
     * Get the ID of the item
     *
     * @return string
     */
    public function getId()
    {
        if (!is_null($this->_id)) {
            return $this->_id;
        }
        $idQuery = $this->xPath->query("//input[@name='objectId']/@value");
        $this->_id = $idQuery->item(0)->textContent;
        return $this->_id;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_id = null;

    /**
     * Get the URL of the item
     *
     * @return string
     */
    public function getUrl()
    {
        if (!is_null($this->_url)) {
            return $this->_url;
        }
        $urlQuery = $this->xPath->query("//link[@rel='canonical']/@href");
        $this->_url = $urlQuery->item(0)->textContent;
        return $this->_url;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_url = null;

    /**
     * Get the URL of the gallery images
     *
     * @return array
     */
    public function getGalleryImages()
    {
        if (!is_null($this->_galleryImgs)) {
            return $this->_galleryImgs;
        }
        $galleryImgs = [];
        $galleryImgMatches = [];
        preg_match('/window\.runParams\.imageBigViewURL=\[(.*)\];/is', $this->html, $galleryImgMatches);
        if (count($galleryImgMatches) == 2) {
            $galleryImgsString = str_replace('"', '', $galleryImgMatches[1]);
            $galleryImgsNotTrimmed = explode(',', $galleryImgsString);
            foreach ($galleryImgsNotTrimmed as $galleryImg) {
                $galleryImgs[] = trim($galleryImg);
            }
        }
        $this->_galleryImgs = $galleryImgs;
        return $galleryImgs;
    }
    /**
     * Do not fetch more than once
     *
     * @var string[]
     */
    private $_galleryImgs = null;

    /**
     * Get the name of the item
     *
     * @return string
     */
    public function getName()
    {
        if (!is_null($this->_name)) {
            return $this->_name;
        }
        $nameQuery =  $this->xPath->query("//h1[@class='product-name']");
        $this->_name = $nameQuery->item(0)->textContent;
        return $this->_name;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_name = null;

    /**
     * Get the name of the shop
     *
     * @return string
     */
    public function getShopName()
    {
        if (!is_null($this->_shopName)) {
            return $this->_shopName;
        }
        $shopNameQuery = $this->xPath->query("//span[@class='shop-name']/a");
        $this->_shopName = $shopNameQuery->item(0)->textContent;
        return $this->_shopName;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_shopName = null;

    /**
     * Get the URL of the shop
     *
     * @return string
     */
    public function getShopUrl()
    {
        if (!is_null($this->_shopUrl)) {
            return $this->_shopUrl;
        }
        $shopUrlQuery = $this->xPath->query("//span[@class='shop-name']/a/@href");
        $this->_shopUrl = 'https:'.$shopUrlQuery->item(0)->textContent;
        return $this->_shopUrl;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_shopUrl = null;

    /**
     * Get the description of the item
     *
     * @return string
     */
    public function getDescription()
    {
        if (!is_null($this->_description)) {
            return $this->_description;
        }
        $description = "";
        $descUrlMatches = [];
        preg_match('/window\.runParams\.detailDesc="(.*)"/', $this->html, $descUrlMatches);
        if (count($descUrlMatches) == 2) {
            $descUrl = $descUrlMatches[1];
            $description = file_get_contents($descUrl);
        }
        $this->_description = $description;
        return $description;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_description = null;

    /**
     * Get the cost of the item
     *
     * @return mixed|string
     */
    public function getCost()
    {
        if (!is_null($this->_cost)) {
            return $this->_cost;
        }
        $cost = "";
        $costMatches = [];
        preg_match('/window\.runParams\.actMinPrice="(.*)"/', $this->html, $costMatches);
        if (count($costMatches) == 2) {
            $cost = $costMatches[1];
        }
        $this->_cost = $cost;
        return $cost;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_cost = null;

    /**
     * Get the max cost of the item
     *
     * @return mixed|string
     */
    public function getMaxCost()
    {
        if (!is_null($this->_maxCost)) {
            return $this->_maxCost;
        }
        $cost = "";
        $costMatches = [];
        preg_match('/window\.runParams\.actMaxPrice="(.*)"/', $this->html, $costMatches);
        if (count($costMatches) == 2) {
            $cost = $costMatches[1];
        }
        $this->_maxCost = $cost;
        return $cost;
    }
    /**
     * Do not fetch more than once
     *
     * @var string
     */
    private $_maxCost = null;
}