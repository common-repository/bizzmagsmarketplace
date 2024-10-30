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
<h2><?php esc_html_e('Categories','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
    $totals=$emag->getTotalsProductEmagCategory();
?>
<p><?php esc_html_e('Here you can link products with Emag categories, firstly click the link to open a new page in your browser (you need to be logged in the Emag marketplace dashboard), then paste the content in the textarea and save, these are the category suggestions that we will use in the Category tab','bizzmagsmarketplace');?></p>
<p><?php esc_html_e('Total linked','bizzmagsmarketplace');?> <?php echo esc_html($totals->linked);?> <?php esc_html_e('Total missing','bizzmagsmarketplace');?> <?php echo esc_html($totals->missing);?></p>
<?php

$product=$emag->getNextProductMissingCategory();
?>
<div class="container emag-no-pad">
<div class="emag-dash-row">
<?php
if(isset($product->id))
{
    $product_title=$product->title;
    $product_title=$emag->substrKeepWordsOnly($product_title,130);

?>
    <div class="row">
    <div class="col mb-2">
        <a id="emag_prod_cat_link" href="<?php echo esc_url("https://marketplace.emag.ro/product-categories/api/v1/search?productName=".urlencode($product_title));?>" target="_blank"><?php echo esc_html($product->title);?></a>
    </div>
    <div class="col mb-2">
        <textarea name="content"></textarea>
    </div>
    <div class="col mb-2">
        <input type="submit" disabled="disabled" value="<?php echo esc_attr(__("Save Pro","bizzmagsmarketplace"));?>" class="button-primary" />
    </div>
    </div>
<?php
}
?>
</div>
</div>
<?php
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>