<?php
/**
 * Crawler File Doc Comment
 *
 * @category Class
 * @package  Chougron\AliexpressCrawler
 * @author   Camille Hougron <camille.hougron@gmail.com>
 * @license  https://github.com/chougron/aliexpress-crawler/blob/master/LICENSE MIT
 * @link     https://github.com/chougron/aliexpress-crawler
 */
namespace Chougron\AliexpressCrawler;
/**
 * Class Crawler
 *
 * @category Class
 * @package  Chougron\AliexpressCrawler
 * @author   Camille Hougron <camille.hougron@gmail.com>
 * @license  https://github.com/chougron/aliexpress-crawler/blob/master/LICENSE MIT
 * @link     https://github.com/chougron/aliexpress-crawler
 */
class Crawler
{

    /**
     * Crawl an item from a given URL
     *
     * @param string $itemUrl The url of the item to crawl
     *
     * @return Item\Crawler
     */
    public function crawlItem($itemUrl)
    {
        $itemCrawler = new \Chougron\AliexpressCrawler\Item\Crawler($itemUrl);
        $itemCrawler->crawl();
        return $itemCrawler;
    }
}