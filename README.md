# Aliexpress Crawler

## Usage

Get the item crawler by calling 

```
$item = new \Chougron\AliexpressCrawler\Crawler()->crawlItem($itemUrl);
```

Then, you can access the following functions to get the data you are interested in :

```
getId()
getUrl()
getGalleryImages()
getName()
getShopName()
getShopUrl()
getDescription()
getCost()
```