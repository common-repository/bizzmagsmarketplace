=== Bizzmags Marketplace ===
Contributors: @claudiumaftei
Donate link: https://bizzmags.ro/bizzmags-marketplace-wordpress-woocommerce-plugin/
Tags: woocommerce, emag, marketplace, api, feed
Requires at least: 6.2
Tested up to: 6.6
Requires PHP: 7.2
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect your WC store with marketplaces, compatible with eMag Marketplace Romania, Hungary, Bulgaria, send your products to the marketplace.

== Description ==

What is the Bizzmags Marketplace plugin?

The plugin aims to connect your shop with marketplaces, compatible with eMag Marketplace Romania, Hungary and Bulgaria, with the perspective of growing compatibilities with other marketplaces.

Basic Features:

* Import Categories for linking with local categories, characteristics and families.
* Category Map to link the marketplace categories with the WC product categories.
* Characteristics Map for associating the marketplace characteristics with the WC attributes and custom attributes.
* Family Map for linking the marketplace families with the WC attributes (product variants).
* Send or update products to the marketplace.
* Price percent alteration for the products sent to the marketplace.
* Background processing for the import categories and sending products to the marketplace.
* Multiple Category specific image sizes, useful for categories which require a specific image ratio.
* Multiple Brand and Ean linkage by product postmetas.

Pro Features:

* All Basic features.
* Import Products data locally to be used over the system.
* Create Products in your shop from the marketplace, in the case you want to create a new online presence.
* Subtract a price percentage for the created products in your shop from the marketplace.
* Import orders from the marketplace and manage stock.
* Cancel orders from the marketplace and manage the stock.
* Import order manually for testing.
* Option to set price 0 in imported orders for marketplace shipping methods.
* Option to do not add the voucher values in the imported orders.
* Log product Buy Button ranking from the marketplace.
* Bulk update products stock and prices using the Update CSV Feed.
* Product commission usage on prices sent to the marketplace.
* Multiple pricing options for the products, including the exact profit after commissions as on your shop.
* Real time price update and Buy Button rank checker with a fail safe price calculation.
* Batch or full import for products and sending products to marketplace, full is faster, but you need to be able to set memory and time limits in your server.
* Generate image size for categories if missing for the multiple category specific image sizes, useful for categories which require a specific image ratio.
* Dropshipping system for simple products usage, CSV Feed and Update CSV Feed, exclude by setting stock 0 by sku, weight, price and stock.
* Manual or semi automatized product and marketplace suggested category for dropshipping system.
* Association of products to the marketplace suggested categories.
* Do not override product data for postmeta, useful when your products get overridden by synchronization with the dropshipper.

The pro version of the plugin provides all functionalities you need to connect your shop with the marketplace, it can also be used in two scenarios: reliant of manual sending and updating products to the marketplace and the dropshipping system which uses the bulk functionalities of sending simple products.

This plugin is intended to be used by the marketplace account owner, you will need a API user and password and the API endpoint URL.

If you are using the eMag Marketplace API with this plugin you need to already have a marketplace account and need to download the API documentation fromt the academy to retrieve the API url endpoint, also having an account you already agreed with the terms and conditions of the marketplace usage.

https://marketplace.emag.ro/ - the eMag marketplace website
https://marketplace.emag.ro/infocenter/emag-academy/ - the eMag academy page
https://marketplace.emag.ro/infocenter/politica-de-confidentialitate/ - privacy policy

== Installation ==
1. Upload the Bizzmags Marketplace plugin folder to the /wp-content/plugins/ directory
2. Activate the plugin through the Plugins menu in WordPress
3. Configure the plugin
4. Log in to the API
5. Enjoy

== Frequently Asked Questions ==

= Does it work with eMag Marketplace? =

It works with eMag Marketplace Romania, Bulgaria and Hungary

= Can I upload products to the marketplace? =

You can create and update products to the marketplace, other features are in the Pro version

= What if I already have products uploaded from other sources =

If you already have products in the marketplace and the product IDs differ from your shop it will cause conflicts, use with care, only the paid version has an ID conflict solution

= Does it work WP Multisite? =

Yes, it is built to work with WP MU

== Screenshots ==

1. Dashboard
2. Config page
3. Prices
4. Category map
5. Characteristics map
6. Family map
7. Bulk send to marketplace
8. Sending to marketplace progress

== Dependencies ==
1. WooCommerce
2. PHP 7.2
3. Action Scheduler

== Changelog ==

= 1.0.7 =
* Removed force image update because of api limit that triggers error if this option is used more than once