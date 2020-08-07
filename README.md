# wek-product-importer
## Usage

```mysql
 CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `external_id` varchar(500) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `manufacturer_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `manufacturer_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `warranty` int NOT NULL,
  `warranty_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vat_percent` int NOT NULL,
  `product_category_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  key (external_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
```