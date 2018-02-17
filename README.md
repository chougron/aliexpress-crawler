# Aliexpress Crawler

## Usage

Get the item crawler by calling 

```
$crawler = new \Chougron\AliexpressCrawler\Crawler();
$item = $crawler->crawlItem($itemUrl);
```

## Available methods for the item crawler

### `getId() : string`
Returns the ID of the item

### `getUrl() : string`
Returns the canonical URL of the item

### `getGalleryImages() : string[]`
Returns an array containing the gallery images of the item

### `getName() : string`
Returns the name of the item

### `getShopName() : string`
Returns the name of the shop selling the item

### `getShopUrl() : string`
Returns the URL of the shop selling the item

### `getDescription() : string`
Returns the description of the item

### `getCost() : string`
Returns the cost of the item

### `getMaxCost() : string`
Returns the maximum cost of the item

### `getType() : string`
Returns the type of the item. **simple** if there is no variation or **configurable** if there are some.

### `getSkus() : Sku[]`
Returns an array containing the different Skus of the item. If the item is a **simple** item, the array will have only one element.

### `getSkuImage(Sku $sku) : string`
Returns the image identifying the given Sku.

## The Sku Object

The Sku Object correspond to the different variations of the item sold on the same page.
It can have a different price, a different quantity of inventory, and a different image.

### Available variables for the Sku object

#### `id : string`
The ID of the item Sku

#### `price : string`
The price of the Sku

#### `quantity : string`
The quantity remaining for the Sku