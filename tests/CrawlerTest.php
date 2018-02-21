<?php

namespace Chougron\AliexpressCrawler;

final class CrawlerTest extends \PHPUnit\Framework\TestCase
{
    public function testConstructor()
    {
        $crawler = new \Chougron\AliexpressCrawler\Crawler();
        $this->assertInstanceOf(
            \Chougron\AliexpressCrawler\Crawler::class,
            $crawler
        );
    }
}