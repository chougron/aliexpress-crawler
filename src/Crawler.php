<?php

namespace Chougron\AliexpressCrawler;

class Crawler {

    public function crawlItem($itemUrl){
        $itemCrawler = new \Chougron\AliexpressCrawler\Item\Crawler($itemUrl);
        $itemCrawler->crawl();
        return $itemCrawler;
    }
}