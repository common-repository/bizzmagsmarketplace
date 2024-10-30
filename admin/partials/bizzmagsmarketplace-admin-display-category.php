<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

$emag->createDemoCategoryRecords();
?>
<h2><?php esc_html_e('Category','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
?>
<p><?php esc_html_e('Here you can select which category to assign to a product, if you do not have access to a category click on the category link and a new page opens in a new tab showing if you requested access to the category, or if already have access, if you get the message that no direct access is allowed just close the tab and try again','bizzmagsmarketplace');?></p>

<?php
$products=$emag->getProductsToPushToEmag();

$totals=$emag->getEmagProductsPushTotals();
$allowed_selected=0;
$allowed_limit=10;
$show_later=0;
?>
<div class="container emag-no-pad">
<div class="emag-dash-row">

<div class="row mb-2">
<div class="col mb-2">
    <label for="show_only_allowed"><?php echo esc_html__("Show allowed only","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </label>
    <input<?php echo (($allowed_selected==1)?' checked="checked"':'');?> onClick="javascript:toggleAllowed();" type="checkbox" id="show_only_allowed" disabled="disabled" />
</div>
<div class="col mb-2">
    <label for="show_later"><?php echo esc_html__("Only Later","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </label>
    <input<?php echo (($show_later==1)?' checked="checked"':'');?> onClick="javascript:toggleAllowed();" type="checkbox" id="show_later" disabled="disabled" />
</div>
<div class="col mb-2">
    <label for="show_limit"><?php echo esc_html__("Limit records to","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </label>
    <select id="show_limit" onChange="javascript:toggleAllowed();" disabled="disabled">
        <option<?php echo (($allowed_limit==10)?' selected="selected"':'');?> value="10">10</option>
        <option<?php echo (($allowed_limit==25)?' selected="selected"':'');?> value="25">25</option>
        <option<?php echo (($allowed_limit==50)?' selected="selected"':'');?> value="50">50</option>
        <option<?php echo (($allowed_limit==100)?' selected="selected"':'');?> value="100">100</option>
        <option<?php echo (($allowed_limit==250)?' selected="selected"':'');?> value="250">250</option>
    </select>
</div>
</div>

<?php
$prod_cnt=0;
if(is_array($products) && count($products)>0)
{
    ?>
<div class="row mb-2">
<div class="col mb-2">
    <strong><?php echo esc_html__("Product","bizzmagsmarketplace");?></strong>
</div>
<div class="col mb-2">
    <strong><?php echo esc_html__("Emag Category","bizzmagsmarketplace");?></strong>
</div>
<div class="col mb-2">
    <strong><?php echo esc_html__("Emag Full Category","bizzmagsmarketplace");?></strong>
</div>
<div class="col mb-2">
    <strong><?php echo esc_html__("Skip Product/Category","bizzmagsmarketplace");?></strong>
</div>
<div class="col mb-2">
    <strong><?php echo esc_html__("Later","bizzmagsmarketplace");?></strong>
</div>
</div>
    <?php
    $count=array();
    foreach($products as $prod)
    {
        if(!isset($count[$prod->prod_id]))
            $prod_cnt++;
        if(!isset($count[$prod->prod_id]))
            $count[$prod->prod_id]=0;
        $count[$prod->prod_id]++;
        $product_url=get_permalink($prod->prod_id);
        ?>
<div class="row">
<div class="col mb-2">
    <span  class="prod_<?php echo esc_attr($prod->prod_id);?>"><?php echo esc_html($prod_cnt);?></span> <a href="<?php echo esc_url($product_url);?>" target="_blank"><?php echo esc_html($prod->post_title);?></a>
</div>
<div class="col mb-2">
    <span class="prod_<?php echo esc_attr($prod->prod_id);?>">
        <?php
            echo esc_html($prod->title);
            esc_html_e(" (Demo)","bizzmagsmarketplace");
        ?>
    </span>
</div>
<div class="col mb-2">
    <?php
    echo esc_html($prod->path);
    esc_html_e(" (Demo)","bizzmagsmarketplace");
    ?>
</div>
<div class="col mb-2">
    <input checked="checked" type="radio" class="skip_no_radio_<?php echo esc_attr($prod->prod_id);?>" name="skip[<?php echo esc_attr($prod->prod_id."_".$prod->template);?>]" id="skip_<?php echo esc_attr($prod->prod_id."_".$prod->cat_id);?>_no" value="0" />
    <label style="color:green;" for="skip_<?php echo esc_attr($prod->prod_id."_".$prod->cat_id);?>_no"><?php echo esc_html(__("No","bizzmagsmarketplace"));?></label>

    <input type="radio" class="skip_yes_radio_<?php echo esc_attr($prod->prod_id);?>" name="skip[<?php echo esc_attr($prod->prod_id."_".$prod->template);?>]" id="skip_<?php echo esc_attr($prod->prod_id."_".$prod->cat_id);?>_yes" value="1" />
    <label style="color:red;" for="skip_<?php echo esc_attr($prod->prod_id."_".$prod->cat_id);?>_yes"><?php echo esc_html(__("Yes","bizzmagsmarketplace"));?></label>

</div>
<div class="col mb-2">
    <input<?php echo (($prod->later==1)?' checked="checked"':'');?> class="product_later_<?php echo esc_attr($prod->prod_id);?>"  value="later_<?php echo esc_attr($prod->prod_id."_".$prod->cat_id);?>_yes" type="checkbox" name="later[]" />
</div>
</div>
        <?php
    }
}
else
    echo esc_html__("No Records","bizzmagsmarketplace");
?>
<div class="row mt-2">
<div class="col mt-2">
    <input type="submit" class="button-primary" disabled="disabled" value="<?php echo esc_attr(__("Save Products for sending to Emag Pro","bizzmagsmarketplace"));?>" />
</div>
</div>


</div>
</div>
<?php
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>