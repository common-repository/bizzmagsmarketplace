<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

?>

<h2><?php esc_html_e('Help','bizzmagsmarketplace');?></h2>

<div class="container emag-no-pad">
<div class="emag-dash-row">

<div class="row">
    <div class="col">
        <h1><?php esc_html_e("Free Version","bizzmagsmarketplace");?></h1>
        <h3><?php esc_html_e("Dashboard","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("The plugin works with the eMag Romania, Hungary and Bulgaria Marketplaces, set the API Endpoint and add your API user name and password to get the plugin set up, the API Endpoint URL is found in the API documentation PDF, your API username is found in the marketplace account under Account > Profile > Technical details tab in the section of IP Addresses there is a username column. Your API password is the same as one with which you log in in the marketplace account, if it seams that the password is wrong try this little trick, log out from the marketplace and when you see your password autocompleted by the browser right click on it, inspect element and delete the type=\"password\" attribute so it gets displayed","bizzmagsmarketplace");?>.</p>
        <p><?php esc_html_e("After your account is set up click the Import Categories button, the plugin will import the marketplace categories for future use, the process is in the background, you can leave the page and it will still continue to import","bizzmagsmarketplace");?>.</p>
        <p><strong><?php esc_html_e("Please note that the plugin is meant to work with only one country, if you switch countries by changing the API Endpint Url you would need to reimport the categories and redo the category, characteristics and family maps","bizzmagsmarketplace");?>.</strong></p>
        <p><?php esc_html_e("After you import the categories the next step is to configure the plugin which is done in the Configuration page, after the configuration is done you can send products to the marketplace from the WC product list page with the Bulk action: Sen to eMag, this action will add the selected products into the queue. When there are products to be sent to the marketplace in the queue in the Dashboard section after the Import Categories you can select the options for the products and trigger the Send to eMag process which runs in the background","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Configuration","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Here you need to set up the system of sending products to the marketplace, firstly you should load the vat rate, handling times and save your desired options. Make sure you set the correct percentage of 'Emag product price alteration' this is the percent added to the product sale price excluding tax that will be sent to the marketplace, configure the other marketplace related prices if needed. The Brand and Ean that get sent to the marketplace can be selected from the product postmetas select boxes, you can add multiple if you use different sources for your products, the plugin will use the postmeta found with a value. At the bottom there is the functionality to select image sizes for categories, this is used if there are categories in marketplace that need a different image aspect ratio, in example the Fashion category","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Sending products to the marketplace","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("After the configuration is done you can send products to the marketplace from the WC products page using the Bulk action 'Send to eMag' which will add the products into the queue and from the 'Dashboard' page you can trigger the sending process","bizzmagsmarketplace");?>.</p>

        <p><strong><?php esc_html_e("Dropshipping","bizzmagsmarketplace");?></strong> <?php esc_html_e("system will make visible three new tabs 'Categories', 'Category', 'Exclude', make visible the CSV Feed link, and enable other options in 'Configuration' these are used when you want to send many products to the marketplace, have a system that updates/add products to your WC shop and you do not use product variations (the marketplace feed doesn't have the possibility of creating families), you should use this plugin like this only if you do not use product variations, these features are only available in the Pro version","bizzmagsmarketplace");?>.</p>
        <p><?php esc_html_e("After you have set up the plugin and tested the output to be sent to the marketplace set the 'Send to eMag Live' to Yes, if this option is set to No the Send to eMag process will only save the output in Logs","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Prices","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("This page is used for real time BuyButton check and price override for product offers in the marketplace, this is a Pro version feature only","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Category Map","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("In this page you need to set up the WC Category and the marketplace categories associations, if you do not have access to a category you would need to request it from the marketplace, in the Access column you can see the access status, from the last Categories import from Dashboard","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Characteristics Map","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Here you need to associate the WC Attributes and Custom Attributes with the marketplace characteristics, there is an auto load functions 'Load defaults' button that will search for same names of attributes and characteristics, you can also add multiple attributes to a marketplace characteristic, this is used when you have a WC attribute let's say Color and you associate it with a Color marketplace characteristic, but you also have a WC custom attribute that needs to be associated with the same marketplace characteristic. Please note that you need to click on the 'Toggle Optional' text to see the optional characteristics","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Family Map","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("If you are using WC product variations you need to associate them with the marketplace families, associate here the WC product attributes with the marketplace family, being it a single or multiple type, you can also see here what characteristics are required for the family","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Logs","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Here you can see various system logs and also the output to be sent to the marketplace when using the Send to eMag function, before setting the plugin in live mode inspect that the data to be sent is correct, you can clear logs manually from the 'Clear logs button, otherwise logs get deleted after 60 days","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Need more help","bizzmagsmarketplace");?>?</h3>
        <p><a href="https://bizzmags.ro/contact/" target="_blank"><?php esc_html_e("Contact us","bizzmagsmarketplace");?></a><br /><br /><a href="https://bizzmags.ro/bizzmags-marketplace-wordpress-woocommerce-plugin/" target="_blank"><?php esc_html_e("Get the Pro version of this plugin","bizzmagsmarketplace");?></a></p>

    </div>
    <div class="col">
        <h1><?php esc_html_e("Pro Extra Features","bizzmagsmarketplace");?></h1>

        <h3><?php esc_html_e("Prices","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Use the marketplace commissions to calculate the exact price to be used thus after the commissions you get the same profit as on your shop with the possibility to add an extra percent to the profit. Use the 'Prices' page to override prices in real time and get the current Buy Button Rank. Use the Update CSV Feed, in the case you want to mass update product prices and stock","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Import products","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Import the products data from the marketplace to be used on various features like commissions, Buy Button Rank log, handle product ID conflicts in the case you used a third party system to send your products to the marketplace and it used other IDs and other features that require the remote data to be at hand. Create products from the marketplace in your WC installation, useful when you already have data in the marketplace and you want to create a website to sell your products","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Import and Cancel orders","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Import orders from the marketplace and manage the stock, manually and via the marketplace New Order Callback. Cancel orders on WC and update the stock, set 0 shipping for imported orders by the marketplace shipping method, option to disable the voucher values import into the order","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Dropshipping system","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Use the plugin in dropshipping mode to send thousands of products to the marketplace using the CSV Feed, update stock and prices using the Update CSV Feed, options to do not override data sent to the marketplace, get the marketplace category suggestions for products (this can be automized by using a program like TinyTask), associate the products with the categories to be used in the CSV Feed. Options to exclude by setting stock 0 by SKU, Weight, Price and Stock. This system should not be used if you are using product variations because the marketplace does not have this function for the Feed","bizzmagsmarketplace");?></p>

        <h3><?php esc_html_e("Updates","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Free updates of the Pro version of the plugin for one year","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Support","bizzmagsmarketplace");?></h3>
        <p><?php esc_html_e("Free support for the Pro version for one year","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Why Pro","bizzmagsmarketplace");?>?</h3>
        <p><?php esc_html_e("Better than using a third party service on which you pay monthly, pay for this plugin once at the price of oher parties two months fee, after one year buy it again only if you need to get the latest version of the plugin, in the case something major changed","bizzmagsmarketplace");?>.</p>

        <h3><?php esc_html_e("Need more help","bizzmagsmarketplace");?>?</h3>
        <p><a href="https://bizzmags.ro/contact/" target="_blank"><?php esc_html_e("Contact us","bizzmagsmarketplace");?></a><br /><br /><a href="https://bizzmags.ro/bizzmags-marketplace-wordpress-woocommerce-plugin/" target="_blank"><?php esc_html_e("Go Pro","bizzmagsmarketplace");?></a></p>

    </div>
</div>

</div>
</div>
