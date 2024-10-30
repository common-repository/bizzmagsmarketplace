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
<h2><?php esc_html_e('Prices','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
?>
<p><?php esc_html_e('Here you can update product prices in real time and save it as a price override for the feeds','bizzmagsmarketplace');?>
    <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
</p>

<p><?php esc_html_e('Total products','bizzmagsmarketplace');?> 0 <?php esc_html_e('Total with price overriden','bizzmagsmarketplace');?> 0</p>

<div class="container emag-no-pad">
<div class="emag-dash-row">

<div class="row">
    <div class="col mb-2">
        <?php esc_html_e("Buy Button","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <select disabled="disabled" disabled="disabled" id="buy_button_select" class="button">
            <option value="1">1</option>
        </select>
    </div>
    <div class="col mb-2">
        <label for="prices_pages"><?php esc_html_e("Page","bizzmagsmarketplace");?></label>
    </div>
    <div class="col mb-2">
        <select disabled="disabled" id="prices_pages" class="form-control">
            <option {{ selected }} value="1">1</option>
        </select>
    </div>
    <div class="col mb-2">
        <label for="prices_per_page"><?php esc_html_e("Per Page","bizzmagsmarketplace");?></label>
    </div>
    <div class="col mb-2">
        <select disabled="disabled" id="prices_per_page" class="form-control">
            <option value="10">10</option>
        </select>
    </div>
</div>

<div class="row">
  <div class="col-4 mb-3">
    <input style="width:100%;" disabled="disabled" name="override_search" type="search" autocomplete="off" value="" class="button" />
  </div>
  <div class="col-2 mb-1">
    <input disabled="disabled" type="submit" value="<?php echo esc_attr(__("Search","bizzmagsmarketplace"));?>" class="button" />
  </div>
  <div class="col-4 mb-2">
    <?php esc_html_e("Search by PS ID, eMag ID, PNK, EAN, Reference","bizzmagsmarketplace");?>
  </div>
  <div class="col-2 mb-2">
    <input disabled="disabled" type="button" value="<?php echo esc_attr(__("Reset","bizzmagsmarketplace"));?>" class="button" />
  </div>
</div>

<div class="row">
    <div class="col-3 mb-2">
        <strong><?php echo esc_html("#");?></strong> <strong><?php esc_html_e("Product","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-2 mb-2">
        <strong><?php esc_html_e("SKU/PNK","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Price","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-2 mb-2">
        <strong><?php esc_html_e("Override","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Buy Button","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Refresh BB","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Save","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col-1 mb-2">
        <strong><?php esc_html_e("Remove","bizzmagsmarketplace");?></strong>
    </div>
</div>

    <div class="row">
        <div class="col-3 mb-2">
           1 <?php echo esc_html("Demo Product Name");?>
        </div>
        <div class="col-2 mb-2">
           <?php echo esc_html("SKU");?>-123<br /><?php echo esc_html("PNK");?>123
        </div>
        <div class="col-1 mb-2">
            50.69
        </div>
        <div class="col-2 mb-2">
            <input disabled="disabled" type="number" style="" min="10" class="button" value="50.69" />
        </div>
        <div class="col-1 mb-2>">
            <span >1</span> - 4
        </div>
        <div class="col-1 mb-2">
            <input disabled="disabled" type="button" class="button-secondary" value="<?php echo esc_attr(__("Refresh","bizzmagsmarketplace"));?>" />
        </div>
        <div class="col-1 mb-2">
            <input disabled="disabled" type="button" class="button-secondary" value="<?php echo esc_attr(__("Save","bizzmagsmarketplace"));?>" />
        </div>
        <div class="col-1 mb-2">
            <input disabled="disabled" type="button" class="button-secondary" value="<?php echo esc_attr(__("Remove","bizzmagsmarketplace"));?>" />
        </div>
    </div>


    <div class="row mt-2">
        <div class="col">
            <input disabled="disabled" type="submit" value="<?php echo esc_attr(__("Remove All price overrides from this page Pro","bizzmagsmarketplace"));?>" class="button-primary" />
        </div>
    </div>


</div>
</div>
<?php
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>