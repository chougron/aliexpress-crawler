<?php

namespace Chougron\AliexpressCrawler\Item;

class Crawler {
    /**
     * @var string
     */
    protected $url;
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
        $this->url = $url;
    }

    /**
     * Crawl the page of the item
     * @throws \Exception
     */
    public function crawl()
    {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->url);
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
     * Get the ID of the item
     * @return string
     */
    public function getId()
    {
        $idQuery = $this->xPath->query("//input[@name='objectId']/@value");
        return $idQuery->item(0)->textContent;
    }

    /**
     * Get the URL of the item
     * @return string
     */
    public function getUrl()
    {
        $urlQuery = $this->xPath->query("//link[@rel='canonical']/@href");
        return $urlQuery->item(0)->textContent;
    }

    /**
     * Get the URL of the gallery images
     * @return array
     */
    public function getGalleryImages()
    {
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
        return $galleryImgs;
    }

    /**
     * Get the name of the item
     * @return string
     */
    public function getName()
    {
        $nameQuery =  $this->xPath->query("//h1[@class='product-name']");
        return $nameQuery->item(0)->textContent;
    }

    /**
     * Get the name of the shop
     * @return string
     */
    public function getShopName()
    {
        $shopNameQuery = $this->xPath->query("//span[@class='shop-name']/a");
        return $shopNameQuery->item(0)->textContent;
    }

    /**
     * Get the URL of the shop
     * @return string
     */
    public function getShopUrl()
    {

        $shopUrlQuery = $this->xPath->query("//span[@class='shop-name']/a/@href");
        return 'https:'.$shopUrlQuery->item(0)->textContent;
    }

    /**
     * Get the description of the item
     * @return string
     */
    public function getDescription()
    {
        $description = "";
        $descUrlMatches = [];
        preg_match('/window\.runParams\.detailDesc="(.*)"/', $this->html, $descUrlMatches);
        if(count($descUrlMatches) == 2){
            $descUrl = $descUrlMatches[1];
            $description = file_get_contents($descUrl);
        }
        return $description;
    }

    /**
     * Get the cost of the item
     * @return mixed|string
     */
    public function getCost()
    {
        $cost = "";
        $costMatches = [];
        preg_match('/window\.runParams\.actMinPrice="(.*)"/', $this->html, $costMatches);
        if(count($costMatches) == 2){
            $cost = $costMatches[1];
        }
        return $cost;
    }
}