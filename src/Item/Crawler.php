<?php

namespace Chougron\AliexpressCrawler\Item;

class Crawler {
    /**
     * @var string
     */
    protected $_url;
    /**
     * @var string
     */
    protected $html;
    /**
     * @var \DOMDocument
     */
    protected $dom;
    /**
     * @var \DOMXPath
     */
    protected $xPath;

    public function __construct($url)
    {
        $this->_url = $url;
    }

    /**
     * Crawl the page of the item
     * @throws \Exception
     */
    public function crawl()
    {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->_url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
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
     * @return string
     */
    public function getType()
    {
        $skus = $this->getSkus();
        if(count($skus) == 1){
            return "simple";
        } else {
            return "configurable";
        }
    }

    /**
     * Get the Image of a SKU
     * @param Sku $sku
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
    /** @var string[] Do not fetch them more than once*/
    private $modelList = null;

    /**
     * Get the different Skus of the item
     * @return Sku[]
     */
    public function getSkus()
    {
        if(!is_null($this->skus)){
            return $this->skus;
        }
        $skus = [];
        $skuMatches = [];
        preg_match('/var skuProducts=(\[.*\]);/', $this->html, $skuMatches);
        if(count($skuMatches) == 2){
            $json = $skuMatches[1];
            $codedSkus = json_decode($json);
            foreach($codedSkus as $codedSku){
                $skus[] = new Sku($codedSku, $this->getModelList());
            }
        }
        $this->skus = $skus;
        return $skus;
    }
    /** @var Sku[] Do not fetch them more than once*/
    private $skus = null;

    /**
     * Get the ID of the item
     * @return string
     */
    public function getId()
    {
        if(!is_null($this->id)){
            return $this->id;
        }
        $idQuery = $this->xPath->query("//input[@name='objectId']/@value");
        $this->id = $idQuery->item(0)->textContent;
        return $this->id;
    }
    /** @var string Do not fetch more than once*/
    private $id = null;

    /**
     * Get the URL of the item
     * @return string
     */
    public function getUrl()
    {
        if(!is_null($this->url)){
            return $this->url;
        }
        $urlQuery = $this->xPath->query("//link[@rel='canonical']/@href");
        $this->url = $urlQuery->item(0)->textContent;
        return $this->url;
    }
    /** @var string Do not fetch more than once*/
    private $url = null;

    /**
     * Get the URL of the gallery images
     * @return array
     */
    public function getGalleryImages()
    {
        if(!is_null($this->galleryImgs)){
            return $this->galleryImgs;
        }
        $galleryImgs = [];
        $galleryImgMatches = [];
        preg_match('/window\.runParams\.imageBigViewURL=\[(.*)\];/is', $this->html, $galleryImgMatches);
        if(count($galleryImgMatches) == 2){
            $galleryImgsString = str_replace('"','',$galleryImgMatches[1]);
            $galleryImgsNotTrimmed = explode(',',$galleryImgsString);
            foreach($galleryImgsNotTrimmed as $galleryImg){
                $galleryImgs[] = trim($galleryImg);
            }
        }
        $this->galleryImgs = $galleryImgs;
        return $galleryImgs;
    }
    /** @var string[] Do not fetch more than once*/
    private $galleryImgs = null;

    /**
     * Get the name of the item
     * @return string
     */
    public function getName()
    {
        if(!is_null($this->name)){
            return $this->name;
        }
        $nameQuery =  $this->xPath->query("//h1[@class='product-name']");
        $this->name = $nameQuery->item(0)->textContent;
        return $this->name;
    }
    /** @var string Do not fetch more than once*/
    private $name = null;

    /**
     * Get the name of the shop
     * @return string
     */
    public function getShopName()
    {
        if(!is_null($this->shopName)){
            return $this->shopName;
        }
        $shopNameQuery = $this->xPath->query("//span[@class='shop-name']/a");
        $this->shopName = $shopNameQuery->item(0)->textContent;
        return $this->shopName;
    }
    /** @var string Do not fetch more than once*/
    private $shopName = null;

    /**
     * Get the URL of the shop
     * @return string
     */
    public function getShopUrl()
    {
        if(!is_null($this->shopUrl)){
            return $this->shopUrl;
        }
        $shopUrlQuery = $this->xPath->query("//span[@class='shop-name']/a/@href");
        $this->shopUrl = 'https:'.$shopUrlQuery->item(0)->textContent;
        return $this->shopUrl;
    }
    /** @var string Do not fetch more than once*/
    private $shopUrl = null;

    /**
     * Get the description of the item
     * @return string
     */
    public function getDescription()
    {
        if(!is_null($this->description)){
            return $this->description;
        }
        $description = "";
        $descUrlMatches = [];
        preg_match('/window\.runParams\.detailDesc="(.*)"/', $this->html, $descUrlMatches);
        if(count($descUrlMatches) == 2){
            $descUrl = $descUrlMatches[1];
            $description = file_get_contents($descUrl);
        }
        $this->description = $description;
        return $description;
    }
    /** @var string Do not fetch more than once*/
    private $description = null;

    /**
     * Get the cost of the item
     * @return mixed|string
     */
    public function getCost()
    {
        if(!is_null($this->cost)){
            return $this->cost;
        }
        $cost = "";
        $costMatches = [];
        preg_match('/window\.runParams\.actMinPrice="(.*)"/', $this->html, $costMatches);
        if(count($costMatches) == 2){
            $cost = $costMatches[1];
        }
        $this->cost = $cost;
        return $cost;
    }
    /** @var string Do not fetch more than once*/
    private $cost = null;

    /**
     * Get the max cost of the item
     * @return mixed|string
     */
    public function getMaxCost()
    {
        if(!is_null($this->maxCost)){
            return $this->maxCost;
        }
        $cost = "";
        $costMatches = [];
        preg_match('/window\.runParams\.actMaxPrice="(.*)"/', $this->html, $costMatches);
        if(count($costMatches) == 2){
            $cost = $costMatches[1];
        }
        $this->maxCost = $cost;
        return $cost;
    }
    /** @var string Do not fetch more than once*/
    private $maxCost = null;
}