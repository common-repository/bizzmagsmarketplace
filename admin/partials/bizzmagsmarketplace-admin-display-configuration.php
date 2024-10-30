<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

$msg="";
$action=isset($_POST['action'])?sanitize_text_field($_POST['action']):"";
if($action=="wc_emag_save_configuration")
{
    check_admin_referer( 'wc_emag_save_emag_configuration' );
    $msg=$emag->saveEmagConfiguration();
}
?>

<h2><?php esc_html_e('Configuration','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
?>
<p><?php esc_html_e('Here you can configure how the system works','bizzmagsmarketplace');?></p>
<p>
    <?php 
    $chk=md5("asdlksaffsa sfda poisfa sdf".wp_parse_url( get_home_url(), PHP_URL_HOST ));
    if($emag->config->emag_dropshipping=='yes')
    {
    echo esc_html__("Rest API Feed url","bizzmagsmarketplace");?> <a href="<?php echo esc_url(get_home_url().'/wp-json/bizzmagsmarketplace/v1/generate-csv-feed?key='.$chk);?>" target="_blank"><?php echo esc_html(get_home_url().'/wp-json/bizzmagsmarketplace/v1/generate-csv-feed?key='.$chk);?></a> <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    <br />
    <?php
    }
    echo esc_html__("Rest API Feed Update url","bizzmagsmarketplace");?> <a href="<?php echo esc_url(get_home_url().'/wp-json/bizzmagsmarketplace/v1/generate-csv-feed-update?key='.$chk);?>" target="_blank"><?php echo esc_html(get_home_url().'/wp-json/bizzmagsmarketplace/v1/generate-csv-feed-update?key='.$chk);?></a> <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    <br />
    <?php
    echo esc_html__("Rest API New Emag Order","bizzmagsmarketplace");?> <a href="<?php echo esc_url(get_home_url().'/wp-json/bizzmagsmarketplace/v1/new_emag_order_callback?key='.$chk);?>" target="_blank"><?php echo esc_html(get_home_url().'/wp-json/bizzmagsmarketplace/v1/new_emag_order_callback?key='.$chk);?>&order_id=</a> <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    <br />
    <?php
    echo esc_html__("Rest API Cancelled Emag Order","bizzmagsmarketplace");?> <a href="<?php echo esc_url(get_home_url().'/wp-json/bizzmagsmarketplace/v1/new_emag_cancel_order_callback?key='.$chk);?>" target="_blank"><?php echo esc_html(get_home_url().'/wp-json/bizzmagsmarketplace/v1/new_emag_cancel_order_callback?key='.$chk);?>&order_id=</a> <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
</p>
<?php
if($msg!="")
{
    if(is_array($msg['msg']))
    {
        foreach($msg['msg'] as $m)
        {
            ?>
            <div class="<?php echo esc_attr($msg['status']);?> notice is-dismissible inline">
                <p><?php echo esc_html($m);?></p>
            </div>
            <?php
        }
    }
    else
    {
    ?>
    <div class="<?php echo esc_attr($msg['status']);?> notice is-dismissible inline">
        <p>
            <?php
                if($msg['msg']==__("Settings updated, reloading page","bizzmagsmarketplace"))
                {
                    esc_html_e("Settings updated","bizzmagsmarketplace");
                    ?>, <a href="javascript:window.location.href=window.location.href;"><?php esc_html_e("please reload the page","bizzmagsmarketplace");?></a>
                    <?php
                }
                else
                   echo esc_html($msg['msg']); 
            ?>
        </p>
    </div>
    <?php
        if($msg['msg']==__("Settings updated, reloading page","bizzmagsmarketplace"))
        {

        }
    }
}
$product_metas=$emag->getProductMetas();

$emag_product_brand_meta=array();
$emag_product_ean_meta=array();
$emag_product_brand_meta_str=$emag->config->emag_product_brand_meta;
if($emag_product_brand_meta_str!="")
    $emag_product_brand_meta=json_decode($emag_product_brand_meta_str);
$emag_product_ean_meta_str=$emag->config->emag_product_ean_meta;
if($emag_product_ean_meta_str!="")
    $emag_product_ean_meta=json_decode($emag_product_ean_meta_str);
$brand=array();
$ean=array();
if(is_array($emag_product_brand_meta))
{
    foreach($emag_product_brand_meta as $emag_prod_meta)
        $brand[]=$emag_prod_meta;
}
if(is_array($emag_product_ean_meta))
{
    foreach($emag_product_ean_meta as $emag_prod_meta)
        $ean[]=$emag_prod_meta;
}
$next_brand_ean_cnt=count($brand);
if($next_brand_ean_cnt<1)
    $next_brand_ean_cnt=1;

$image_sizes_arr=array();
$image_sizes_names_arr=array();
$default_sizes = array(
    'thumbnail' => array(
        'width' => get_option('thumbnail_size_w'),
        'height' => get_option('thumbnail_size_h'),
        'crop' => get_option('thumbnail_crop'),
    ),
    'medium' => array(
        'width' => get_option('medium_size_w'),
        'height' => get_option('medium_size_h'),
        'crop' => false,
    ),
    'medium_large' => array(
        'width' => get_option('medium_large_size_w'),
        'height' => get_option('medium_large_size_h'),
        'crop' => false,
    ),
    'large' => array(
        'width' => get_option('large_size_w'),
        'height' => get_option('large_size_h'),
        'crop' => false,
    ),
);

// Add default sizes to the names array
foreach ($default_sizes as $size => $details) {
    $width = $details['width'];
    $height = $details['height'];
    $crop = $details['crop'] ? 'true' : 'false';
    $label = "({$width}x{$height}, Crop: {$crop})";
    $image_sizes_names_arr[$size] = $label;
}
$sizes = get_intermediate_image_sizes();
$size_details = wp_get_additional_image_sizes();
if(is_array($sizes))
{
    foreach ($sizes as $size)
    {
        
        $image_sizes_arr[]=$size;
        $width = isset($size_details[$size]['width']) ? $size_details[$size]['width'] : '';
        $height = isset($size_details[$size]['height']) ? $size_details[$size]['height'] : '';
        $crop = isset($size_details[$size]['crop']) ? ($size_details[$size]['crop'] ? 'true' : 'false') : 'false';
        $label = "({$width}x{$height}, Crop: {$crop})";
        if(!isset($image_sizes_names_arr[$size]))
            $image_sizes_names_arr[$size]=$label;
    }
}
/*
$product_categories_arr=array();
$product_categories_names_arr=array();
$product_categories = get_terms( array(
    'taxonomy'   => 'product_cat', // WooCommerce product category taxonomy
    'hide_empty' => false, // Set to false to also retrieve empty categories
) );
if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) )
{
    foreach ( $product_categories as $category )
    {
        $product_categories_arr[]=$category->term_id;
        $product_categories_names_arr[$category->term_id]=$category->name;
    }
}
*/
$product_categories_arr=$emag->getAllWcProductCategories();

$emag_categ_img_category=array();
$emag_categ_img_size=array();
$emag_categ_img_category_str=$emag->config->emag_categ_img_category;
if($emag_categ_img_category_str!="")
    $emag_categ_img_category=json_decode($emag_categ_img_category_str);
$emag_categ_img_size_str=$emag->config->emag_categ_img_size;
if($emag_categ_img_size_str!="")
    $emag_categ_img_size=json_decode($emag_categ_img_size_str);
$categ_img_category=array();
$categ_img_size=array();
if(is_array($emag_categ_img_category))
{
    foreach($emag_categ_img_category as $emag_prod_meta)
        $categ_img_category[]=$emag_prod_meta;
}
if(is_array($emag_categ_img_size))
{
    foreach($emag_categ_img_size as $emag_prod_meta)
        $categ_img_size[]=$emag_prod_meta;
}

$next_categ_img_cnt=count($categ_img_category);
if($next_categ_img_cnt<1)
    $next_categ_img_cnt=1;

$nonce_1=wp_create_nonce( 'bizzmagsmarketplace_get_emag_vat_rate_nonce' );
$nonce_2=wp_create_nonce( 'bizzmagsmarketplace_get_emag_handling_times_nonce' );
$nonce_3=wp_create_nonce( 'bizzmagsmarketplace_import_order_nonce' );
$data_js="
function requestEmagVatRates()
{
    jQuery('#ajax_vat_result').html('".esc_js(__("loading","bizzmagsmarketplace"))."...');
    jQuery.ajax({
        method: 'POST',
        data: { 
            'action': 'bizzmagsmarketplace_get_emag_vat_rates', 
            'security': '".esc_js($nonce_1)."',
        },
        url: '".esc_url(admin_url('admin-ajax.php'))."',
        async: true
    })
    .done(function( msg ) {
        jQuery('#ajax_vat_result').html(msg);
    });
}
function requestEmagHandlingTimes()
{
    jQuery('#ajax_handling_result').html('".esc_js(__("loading","bizzmagsmarketplace"))."...');
    jQuery.ajax({
        method: 'POST',
        data: { 
            'action': 'bizzmagsmarketplace_get_emag_handling_times', 
            'security': '".esc_js($nonce_2)."',
        },
        url: '".esc_url(admin_url('admin-ajax.php'))."',
        async: true
    })
    .done(function( msg ) {
        jQuery('#ajax_handling_result').html(msg);
    });
}
var brand_ean_cnt=".(int)$next_brand_ean_cnt.";
function addNewBrandEanRow()
{
    let content='';
    content+='<div class=\"row mb-2 brand_ean_cnt_cls\" brand_ean_cnt=\"'+brand_ean_cnt+'\" id=\"brand_ean_cnt_'+brand_ean_cnt+'\">';
    content+='<div class=\"col mb-2\">';
    content+='".esc_js(__("Brand","bizzmagsmarketplace"))."';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='<select name=\"emag_product_brand_meta[]\" id=\"emag_product_brand_meta_'+brand_ean_cnt+'\" class=\"button\">';
    content+='</select>';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='".esc_js(__("EAN","bizzmagsmarketplace"))."';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='<select name=\"emag_product_ean_meta[]\" id=\"emag_product_ean_meta_'+brand_ean_cnt+'\" class=\"button\">';
    content+='</select>';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='<input onClick=\"javascript:removeBrandEanRow('+brand_ean_cnt+');\" type=\"button\" class=\"button-secondary\" value=\"-\" />';
    content+='</div>';
    content+='</div>';
    var before_brand_ean_cnt=0;
    jQuery('.brand_ean_cnt_cls').each(function(index,val){
        let cnt=jQuery(val).attr('brand_ean_cnt');
        if(parseInt(cnt)>parseInt(before_brand_ean_cnt))
        {
            before_brand_ean_cnt=cnt;
        }
    });
    jQuery('#brand_ean_cnt_'+before_brand_ean_cnt).after(content);
    
    jQuery('#emag_product_brand_meta_0 option').each(function(){
        var option = jQuery(this).clone();
        jQuery('#emag_product_brand_meta_'+brand_ean_cnt).append(option);
    });
    jQuery('#emag_product_ean_meta_0 option').each(function(){
        var option = jQuery(this).clone();
        jQuery('#emag_product_ean_meta_'+brand_ean_cnt).append(option);
    });

    brand_ean_cnt++;
}
function removeBrandEanRow(cnt)
{
    jQuery('#brand_ean_cnt_'+cnt).remove();
    jQuery('.brand_ean_cnt_cls').each(function(index,val){
        let cnt=jQuery(val).attr('brand_ean_cnt');
        if(cnt>brand_ean_cnt)
        {
            brand_ean_cnt=cnt;
        }
    });
}
var categ_img_cnt=".(int)$next_categ_img_cnt.";
function addNewCatImgRow()
{
    let content='';
    content+='<div class=\"row mb-2 categ_img_cnt_cls\" categ_img_cnt=\"'+categ_img_cnt+'\" id=\"categ_img_cnt_'+categ_img_cnt+'\">';
    content+='<div class=\"col mb-2\">';
    content+='".esc_js(__("Category","bizzmagsmarketplace"))."';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='<select name=\"emag_categ_img_category[]\" id=\"emag_categ_img_category_'+categ_img_cnt+'\" class=\"button\">';
    content+='</select>';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='".esc_js(__("Size","bizzmagsmarketplace"))."';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='<select name=\"emag_categ_img_size[]\" id=\"emag_categ_img_size_'+categ_img_cnt+'\" class=\"button\">';
    content+='</select>';
    content+='</div>';
    content+='<div class=\"col mb-2\">';
    content+='<input onClick=\"javascript:removeCatImgRow('+categ_img_cnt+');\" type=\"button\" class=\"button-secondary\" value=\"-\" />';
    content+='</div>';
    content+='</div>';
    var before_categ_img_cnt=0;
    jQuery('.categ_img_cnt_cls').each(function(index,val){
        let cnt=jQuery(val).attr('categ_img_cnt');
        if(parseInt(cnt)>parseInt(before_categ_img_cnt))
        {
            before_categ_img_cnt=cnt;
        }
    });
    jQuery('#categ_img_cnt_'+before_categ_img_cnt).after(content);

    jQuery('#emag_categ_img_category_0 option').each(function(){
        var option = jQuery(this).clone();
        jQuery('#emag_categ_img_category_'+categ_img_cnt).append(option);
    });
    jQuery('#emag_categ_img_size_0 option').each(function(){
        var option = jQuery(this).clone();
        jQuery('#emag_categ_img_size_'+categ_img_cnt).append(option);
    });

    categ_img_cnt++;
}
function removeCatImgRow(cnt)
{
    jQuery('#categ_img_cnt_'+cnt).remove();
    jQuery('.categ_img_cnt_cls').each(function(index,val){
        let cnt=jQuery(val).attr('categ_img_cnt');
        if(cnt>categ_img_cnt)
        {
            categ_img_cnt=cnt;
        }
    });
}
";
if($data_js!='')
{
    wp_register_script( 'bizzmagsmarketplace_product_page_inline_js', '' );
    wp_enqueue_script( 'bizzmagsmarketplace_product_page_inline_js' );
    wp_add_inline_script("bizzmagsmarketplace_product_page_inline_js",$data_js);
}
?>
<div class="container emag-no-pad">
<div class="emag-dash-row">
<form method="post" action="" autocomplete="off" onSubmit="if(jQuery('#emag_send_to_emag_live').val()=='yes'){alert('<?php echo esc_js(__("Please note that sending products to eMag is live now","bizzmagsmarketplace"))?>!');}">
<?php $nonce=wp_create_nonce( 'wc_emag_save_emag_configuration' );?>
<input type="hidden" name="action" value="wc_emag_save_configuration" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
    <div class="row mb-2">
    <div class="col mb-2">
        <input onClick="javascript:requestEmagVatRates();" type="button" class="button-secondary" value="<?php echo esc_attr(__("Load Emag Vat rates","bizzmagsmarketplace"));?>" />
    </div>
    <div class="col mb-2">
        <label><?php echo esc_html__("Select your Vat rate","bizzmagsmarketplace");?></label>
    </div>
    <div class="col mb-2" id="ajax_vat_result">
        <select id="emag_vat_select" class="button"><option><?php echo esc_html__("Please load Vat rates","bizzmagsmarketplace");?></option></select>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Current Vat rate","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html($emag->config->emag_vat_rate);?>%
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">
    <input onClick="javascript:requestEmagHandlingTimes();" type="button" class="button-secondary" value="<?php echo esc_attr(__("Load Emag Handling times","bizzmagsmarketplace"));?>" />
    </div>
    <div class="col mb-2">
        <label><?php echo esc_html__("Select Handling time","bizzmagsmarketplace");?></label>
    </div>
    <div class="col mb-2" id="ajax_handling_result">
        <select id="emag_handling_select" class="button"><option><?php echo esc_html__("Please load Handling times","bizzmagsmarketplace");?></option></select>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Current Handling time","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html($emag->config->emag_handling_time);?> <?php echo esc_html__("days","bizzmagsmarketplace");?>
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">
        <label><?php echo esc_html__("Resupply days","bizzmagsmarketplace");?></label>
    </div>
    <div class="col mb-2">
        <select id="emag_resupply_days" name="emag_resupply_days" class="button">
            <?php
            $emag_resupply_days=array(2, 3, 5, 7, 14, 30, 60, 90,120);
            foreach($emag_resupply_days as $day)
            {
                ?>
                <option<?php echo (($day==$emag->config->emag_resupply_days)?' selected="selected"':"");?> vlaue="<?php echo esc_attr($day);?>"><?php echo esc_html($day);?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="col mb-2">
        <label><?php echo esc_html__("Warranty months","bizzmagsmarketplace");?></label>
    </div>
    <div class="col mb-2">
        <input type="number" min="0" max="255" class="button" id="emag_warranty_months"  name="emag_warranty_months" value="<?php echo esc_attr($emag->config->emag_warranty_months);?>" /> 
    </div>
    <div class="col mb-2">
        <select name="emag_product_status" class="button">
            <option<?php echo (($emag->config->emag_product_status==0)?' selected="selected"':"");?> value="0"><?php echo esc_html__("Inactive","bizzmagsmarketplace");?></option>
            <option<?php echo (($emag->config->emag_product_status==1)?' selected="selected"':"");?> value="1"><?php echo esc_html__("Active","bizzmagsmarketplace");?></option>
        </select>
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Emag product price alteration","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Addition","bizzmagsmarketplace");?>
        <input type="hidden" name="emag_product_price_alter" value="addition" />
    </div>
    <div class="col mb-2">
        <input type="number" min="0" class="button" name="emag_product_price_alter_value" value="<?php echo esc_attr($emag->config->emag_product_price_alter_value);?>" /> 
    </div>
    <div class="col mb-2">
        <input type="hidden" name="emag_product_price_alter_type" value="percent" />
        <?php echo esc_html__("Percent","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <input type="hidden" name="emag_product_price_alter_formula" value="excluded" />
        <?php echo esc_html__("Percent Excluded in WC price (exc. VAT)","bizzmagsmarketplace");?>
    </div>
    </div>


    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Emag max commission","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <input disabled="disabled" type="number" min="0" class="button" name="emag_product_price_max_commission" value="<?php echo esc_attr($emag->config->emag_product_price_max_commission);?>" /> 
    </div>

    <div class="col mb-2">
        <?php echo esc_html__("Commission VAT","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <input disabled="disabled" type="number" min="0" class="button" name="emag_product_price_alter_value_commission_vat" value="<?php echo esc_attr($emag->config->emag_product_price_alter_value_commission_vat);?>" /> 
    </div>

    <div class="col mb-2">
        <?php echo esc_html__("Use Emag Commission","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <select disabled="disabled" name="emag_use_product_commission" class="button">
            <option<?php echo (($emag->config->emag_use_product_commission=='yes')?' selected="selected"':"");?> value="yes"><?php echo esc_html__("Yes","bizzmagsmarketplace");?></option>
            <option<?php echo (($emag->config->emag_use_product_commission=='no')?' selected="selected"':"");?> value="no"><?php echo esc_html__("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    </div>


    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Add","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <input disabled="disabled" type="number" min="0" class="button" name="emag_add_percent_before_commission" value="<?php echo esc_attr($emag->config->emag_add_percent_before_commission);?>" /> 
    </div>

    <div class="col mb-2">
        <?php echo esc_html__("percent","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("before the commissions","bizzmagsmarketplace");?>
    </div>

    <div class="col mb-2">
        <?php echo esc_html__("Add a extra price here, it could be your VAT if you have the prices exclisive of tax WC","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Initial price 100","bizzmagsmarketplace");?>
    </div>
    </div>





    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Price simulator","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php
        $wc_price=100;
        if((int)$emag->config->emag_add_percent_before_commission>0)
        {
            $diff=$wc_price*((int)$emag->config->emag_add_percent_before_commission/100);
            $wc_price=$wc_price+$diff;
            $wc_price=number_format(round($wc_price,2),2,".","");
        }
        $emag_price=0;
        $alter_value=$emag->config->emag_product_price_alter_value;
        $emag_product_price_max_commission=$emag->config->emag_product_price_max_commission;
        $commission_vat=$emag->config->emag_product_price_alter_value_commission_vat;
        echo esc_html($wc_price);

        if($emag->config->emag_product_price_alter_type=='percent' && $emag->config->emag_product_price_alter=='addition' && $alter_value>0)
        {
            if($emag->config->emag_product_price_alter_formula=='excluded')
            {
                $emag_price=$wc_price;
                $emag_price=number_format(round($emag_price,2),2,".","");
                if($emag->config->emag_use_product_commission=='yes')
                    $emag_commission_percent=$emag->calculateEmagCommissionPercent($emag_product_price_max_commission,$commission_vat);
                else
                    $emag_commission_percent=$alter_value;
                $emag_commission=$emag_price*($emag_commission_percent/100);
                $emag_commission=number_format(round($emag_commission,2),2,".","");
                $emag_price=$emag_price+$emag_commission;
                $emag_price=number_format(round($emag_price,2),2,".","");
                if($emag->config->emag_use_product_commission=='yes')
                    $alter_value=$emag_commission_percent;
                echo " + ".esc_html($alter_value)."% = ".esc_html($emag_price);
            }
        }
        ?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("WC prices used exclude VAT","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        
    </div>
    <div class="col mb-2">
        
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Min Sale Price(%)","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <input type="number" name="emag_min_sale_price" min="0" value="<?php echo esc_attr($emag->config->emag_min_sale_price);?>" class="button" />
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Max Sale Price(%)","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <input type="number" name="emag_max_sale_price" min="0" value="<?php echo esc_attr($emag->config->emag_max_sale_price);?>" class="button" />
    </div>
    <div class="col mb-2">
        
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">

    </div>
    <div class="col mb-2">
        <?php
        $diff=$emag_price*$emag->config->emag_min_sale_price/100;
        $new_emag_price=$emag_price-$diff;
        echo esc_html($emag_price." - ".$emag->config->emag_min_sale_price."% = ".number_format(round($new_emag_price,2),2,".",""));
        ?>
    </div>
    <div class="col mb-2">
    </div>
    <div class="col mb-2">
        <?php
        $diff=$emag_price*$emag->config->emag_max_sale_price/100;
        $new_emag_price=$emag_price+$diff;
        echo esc_html($emag_price." + ".$emag->config->emag_max_sale_price."% = ".number_format(round($new_emag_price,2),2,".",""));
        ?>
    </div>
    <div class="col mb-2">
        
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Recommended Price(%)","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <input type="number" name="emag_recommended_price" min="0" value="<?php echo esc_attr($emag->config->emag_recommended_price);?>" class="button" />
    </div>
    <div class="col mb-2">
        <?php
        $diff=$emag_price*$emag->config->emag_recommended_price/100;
        $new_emag_price=$emag_price+$diff;
        echo esc_html($emag_price." + ".$emag->config->emag_recommended_price."% = ".number_format(round($new_emag_price,2),2,".",""));
        ?>
    </div>
    <div class="col mb-2">
        
    </div>
    <div class="col mb-2">
        
    </div>
    </div>

    <?php

    if(is_array($brand) && count($brand)>0)
    {
        for($metas_cnt=0;$metas_cnt<count($brand);$metas_cnt++)
        {
        ?>
        <div class="row mb-2 brand_ean_cnt_cls" brand_ean_cnt="<?php echo esc_attr($metas_cnt);?>" id="brand_ean_cnt_<?php echo esc_attr($metas_cnt);?>">
        <div class="col mb-2">
            <?php echo esc_html__("Brand","bizzmagsmarketplace");?>
        </div>
        <div class="col mb-2">
            <select name="emag_product_brand_meta[]" id="emag_product_brand_meta_<?php echo esc_attr($metas_cnt);?>" class="button">
                <option value=""> - </option>
                <?php
                foreach($product_metas as $pm)
                {
                    ?>
                    <option<?php echo (($brand[$metas_cnt]==$pm->meta_key)?' selected="selected"':"");?> value="<?php echo esc_attr($pm->meta_key);?>"><?php echo esc_html($pm->meta_key);?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col mb-2">
            <?php echo esc_html__("EAN","bizzmagsmarketplace");?>
        </div>
        <div class="col mb-2">
            <select name="emag_product_ean_meta[]" id="emag_product_ean_meta_<?php echo esc_attr($metas_cnt);?>" class="button">
                <option value=""> - </option>
                <?php
                foreach($product_metas as $pm)
                {
                    ?>
                    <option<?php echo (($ean[$metas_cnt]==$pm->meta_key)?' selected="selected"':"");?> value="<?php echo esc_attr($pm->meta_key);?>"><?php echo esc_html($pm->meta_key);?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col mb-2">
            <?php
            if($metas_cnt==0)
            {
            ?>
            <input onClick="javascript:addNewBrandEanRow();" type="button" class="button-secondary" value="+" />
            <?php
            }
            else
            {
                ?>
                <input onClick="javascript:removeBrandEanRow('<?php echo esc_js($metas_cnt);?>');" type="button" class="button-secondary" value="-" />
                <?php
            }
            ?>
        </div>
        </div>
        <?php
        }
    }
    else
    {
    ?>
    <div class="row mb-2" id="brand_ean_cnt_0">
    <div class="col mb-2">
        <?php echo esc_html__("Brand","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <select name="emag_product_brand_meta[]" id="emag_product_brand_meta_0" class="button">
            <option value=""> - </option>
            <?php
            foreach($product_metas as $pm)
            {
                ?>
                <option value="<?php echo esc_attr($pm->meta_key);?>"><?php echo esc_html($pm->meta_key);?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("EAN","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <select name="emag_product_ean_meta[]" id="emag_product_ean_meta_0" class="button">
            <option value=""> - </option>
            <?php
            foreach($product_metas as $pm)
            {
                ?>
                <option value="<?php echo esc_attr($pm->meta_key);?>"><?php echo esc_html($pm->meta_key);?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="col mb-2">
        <input onClick="javascript:addNewBrandEanRow();" type="button" class="button-secondary" value="+" />
    </div>
    </div>
    <?php
    }
    ?>


    <div class="row mb-2">
    <div class="col-2 mb-2">
        <?php echo esc_html__("Sync product commission","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <select disabled="disabled" name="emag_sync_product_commission" class="button">
            <option<?php echo (($emag->config->emag_sync_product_commission=='yes')?' selected="selected"':"");?> value="yes"><?php echo esc_html__("Yes","bizzmagsmarketplace");?></option>
            <option<?php echo (($emag->config->emag_sync_product_commission=='no')?' selected="selected"':"");?> value="no"><?php echo esc_html__("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col-8 mb-2">
        <?php esc_html_e("Set to yes when you get access to new categories or if category commissions change","bizzmagsmarketplace");?>
    </div>
    </div>

    <div class="row mb-2">
    <div class="col-2 mb-2">
        <?php esc_html_e("Batch import products","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <select disabled="disabled" name="emag_prod_batch_import" class="button">
            <option <?php echo (($emag->config->emag_prod_batch_import == 'yes')?' selected="selected"':'');?> value="yes"><?php esc_html_e("Yes","bizzmagsmarketplace");?></option>
            <option <?php echo (($emag->config->emag_prod_batch_import == 'no')?' selected="selected"':'');?> value="no"><?php esc_html_e("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col-2 mb-2">
        <?php esc_html_e("Batch limit","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <input disabled="disabled" type="number" name="emag_prod_batch_import_nr" min="10" max="100" value="<?php echo esc_attr($emag->config->emag_prod_batch_import_nr);?>" class="button" />
    </div>
    <div class="col-4 mb-2">
        <?php esc_html_e("Set to yes when you want to import products in batches if your hosting does not allow a long execution time","bizzmagsmarketplace");?>
    </div>
    </div>

    <div class="row mb-2">
    <div class="col-2 mb-2">
        <?php esc_html_e("Batch send to eMag","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <select disabled="disabled" name="emag_prod_batch_send_to_emag" class="button">
            <option <?php echo (($emag->config->emag_prod_batch_send_to_emag == 'yes')?' selected="selected"':'');?> value="yes"><?php esc_html_e("Yes","bizzmagsmarketplace");?></option>
            <option <?php echo (($emag->config->emag_prod_batch_send_to_emag == 'no')?' selected="selected"':'');?> value="no"><?php esc_html_e("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col-2 mb-2">
        <?php esc_html_e("Batch limit","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <input disabled="disabled" type="number" name="emag_prod_batch_send_to_emag_nr" min="10" max="100" value="<?php echo esc_attr($emag->config->emag_prod_batch_send_to_emag_nr);?>" class="button" />
    </div>
    <div class="col-4 mb-2">
        <?php esc_html_e("Set to yes when you want to send products to eMag in batches if your hosting does not allow a long execution time","bizzmagsmarketplace");?>
    </div>
    </div>

    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Order import state","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <?php
        if($emag->is_woocommerce_activated())
        {
            $statuses = wc_get_order_statuses();
            ?>
            <select disabled="disabled" id="emag_import_order_status" name="emag_import_order_status" class="button">
                <?php
                foreach($statuses as $status_slug => $status_name)
                {
                    ?>
                    <option <?php echo (($emag->config->emag_import_order_status==$status_slug)?'selected="selected"':'');?> value="<?php echo esc_attr($status_slug);?>"><?php echo esc_html($status_name);?></option>
                    <?php
                }
                ?>
            </select>
            <?php
        }
        ?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Emag Order ID","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>

    <div class="col mb-2">
        <input disabled="disabled" type="text" id="emag_import_order_id" value="" class="button" />
    </div>
    <div class="col mb-2">
         <input disabled="disabled" type="button" class="button-secondary" value="<?php echo esc_attr(__("Import Order","bizzmagsmarketplace"));?>" />
    </div>

    <div class="col mb-2">
        <span id="emag_import_order_result"></span>
    </div>
    
    </div>

    <?php
    if($emag->config->emag_dropshipping=='yes')
    {
    ?>
    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Stock 0 for sku (,)","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <textarea disabled="disabled" name="emag_stock_zero_sku"></textarea>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Stock 0 for weight bigger than (g)","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>

    <div class="col mb-2">
        <input disabled="disabled" type="number" name="emag_stock_zero_weight" min="0" value="<?php echo esc_attr($emag->config->emag_stock_zero_weight);?>" class="button" />
    </div>
    <div class="col mb-2">
         <?php echo esc_html__("Stock 0 for WC price (exc vat) less than","bizzmagsmarketplace");?>
         <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>

    <div class="col mb-2">
        <input disabled="disabled" type="number" name="emag_stock_zero_price" min="0" value="<?php echo esc_attr($emag->config->emag_stock_zero_price);?>" class="button" />
    </div>
    
    </div>
    <?php
    }
    ?>
    <div class="row mb-2">
        <?php
        if($emag->config->emag_dropshipping=='yes')
        {
        ?>
        <div class="col-2 mb-2">
         <?php echo esc_html__("Stock 0 for stock lower than","bizzmagsmarketplace");?>
         <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
        </div>

        <div class="col-2 mb-2">
            <input disabled="disabled" type="number" name="emag_stock_zero_stock_lower" min="0" value="<?php echo esc_attr($emag->config->emag_stock_zero_stock_lower);?>" class="button" />
        </div>
        <?php
        }
        ?>

        <div class="col<?php echo (($emag->config->emag_dropshipping=='yes')?"-2":"-6");?> mb-2">
            <?php echo esc_html__("Maximum percent included in WC price that can be subtracted in Prices page to lower Emag price","bizzmagsmarketplace");?>
            <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
        </div>
        <div class="col-2 mb-2">
            <input disabled="disabled" type="number" name="emag_max_percent_substract_from_price" min="0" value="<?php echo esc_attr($emag->config->emag_max_percent_substract_from_price);?>" class="button" />
        </div>
        <div class="col-2 mb-2">
            <?php
            $wc_price=100;
            $diff=$wc_price/(1+($emag->config->emag_max_percent_substract_from_price/100));
            $new_price=$wc_price-$diff;
            $new_price=number_format(round($new_price,2),2,".","");
            $diff=number_format(round($diff,2),2,".","");
            ?>
            <?php echo esc_html($wc_price);?> - <?php echo esc_html($new_price);?>(<?php echo esc_html($emag->config->emag_max_percent_substract_from_price);?>%) = <?php echo esc_html($diff);?>
            <br />
            <?php echo esc_html($diff);?> + <?php esc_html_e("emag commission + vat on commission","bizzmagsmarketplace");?>
            = <?php esc_html_e("new emag price","bizzmagsmarketplace");?>
        </div>
        <div class="col-2 mb-2">
            <?php esc_html_e("This is used on prices page to have a limit on how much you can lower a price so you do not end up on loss and can win a BB","bizzmagsmarketplace");?>
        </div>
    </div>
    <?php
    if($emag->config->emag_dropshipping=='yes')
    {
    ?>
    <div class="row mb-2">
    <div class="col-2 mb-2">
        <?php echo esc_html__("Do not override product data for postmeta (,)","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <textarea disabled="disabled" name="emag_feed_override_product_data"></textarea>
    </div>
    <div class="col-8 mb-2">
        <?php echo esc_html__("Useful when having a dropshipping system and your products get overridden by synchronization with the dropshipper, set here postmetas of products to exclude data override if you are the emag product owner","bizzmagsmarketplace");?>
    </div>
    </div>
    <?php
    }
    ?>


    <div class="row mb-2">
    <div class="col-2 mb-2">
        <?php echo esc_html__("Dropshipping system","bizzmagsmarketplace");?>
    </div>
    <div class="col-2 mb-2">
        <select name="emag_dropshipping" class="button">
            <option<?php echo (($emag->config->emag_dropshipping == 'yes' )?' selected="selected"':"");?> value="yes"><?php echo esc_html__("Yes","bizzmagsmarketplace");?></option>
            <option<?php echo (($emag->config->emag_dropshipping == 'no' )?' selected="selected"':"");?> value="no"><?php echo esc_html__("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col-2 mb-2">
        <?php echo esc_html__("Import product %","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col-2 mb-2">
        <input disabled="disabled" type="number" name="emag_create_product_price_minus_percent" min="0" value="<?php echo esc_attr($emag->config->emag_create_product_price_minus_percent);?>" class="button" />
    </div>
    <div class="col-4 mb-2">
        <?php echo esc_html__("When importing products from emag subtract this percentage from the price (percent included, meaning the price result x .percent set + price result = the price from emag)","bizzmagsmarketplace");?>
    </div>
    </div>
    
    <div class="row mb-2">
    <div class="col mb-2">
        <?php echo esc_html__("Carrier price 0 (,)","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
        <textarea disabled="disabled" name="emag_order_carrier_free" class="button"><?php echo esc_html($emag->config->emag_order_carrier_free);?></textarea>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("When importing orders from eMag set 0 cost for the carriers from the textarea","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Add eMag negative vouchers","bizzmagsmarketplace");?>
        <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
    </div>
    <div class="col mb-2">
         <select disabled="disabled" name="emag_order_voucher_zero" class="button">
            <option <?php echo (($emag->config->emag_order_voucher_zero == 'yes' )?'selected="selected"':"");?> value="yes"><?php echo esc_html__("Yes","bizzmagsmarketplace");?></option>
            <option <?php echo (($emag->config->emag_order_voucher_zero == 'no' )?'selected="selected"':"");?> value="no"><?php echo esc_html__("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("When importing orders from eMag add the negative vouchers to the order","bizzmagsmarketplace");?>
    </div>
    </div>

    <div class="row mb-2">
    <div class="col-2 mb-2">
        <?php echo esc_html__("Send to eMag Live","bizzmagsmarketplace");?>
    </div>
    
    <div class="col-2 mb-2">
        <select name="emag_send_to_emag_live" id="emag_send_to_emag_live" class="button">
            <option <?php echo (($emag->config->emag_send_to_emag_live == 'yes' )?'selected="selected"':"");?> value="yes"><?php echo esc_html__("Yes","bizzmagsmarketplace");?></option>
            <option <?php echo (($emag->config->emag_send_to_emag_live == 'no' )?'selected="selected"':"");?> value="no"><?php echo esc_html__("No","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col-8 mb-2">
        <?php echo esc_html__("Set to yes after you tested the data sent to emag and are happy with it, this is also used for the Pro version when sending/updating products from the product list page bulk action send to emag, on stock update and product save","bizzmagsmarketplace");?>
    </div>
    </div>


<div class="row mb-2">
    <div class="col mb-2">
        <?php esc_html_e("Set what image size to send to eMag by category","bizzmagsmarketplace");?>
    </div>
</div>



    <?php

    if(is_array($categ_img_category) && count($categ_img_category)>0)
    {
        for($ci_cnt=0;$ci_cnt<count($categ_img_category);$ci_cnt++)
        {
        ?>
        <div class="row mb-2 categ_img_cnt_cls" categ_img_cnt="<?php echo esc_attr($ci_cnt);?>" id="categ_img_cnt_<?php echo esc_attr($ci_cnt);?>">
        <div class="col mb-2">
            <?php echo esc_html__("Category","bizzmagsmarketplace");?>
        </div>
        <div class="col mb-2">
            <select name="emag_categ_img_category[]" id="emag_categ_img_category_<?php echo esc_attr($ci_cnt);?>" class="button">
                <option value=""> - </option>
                <?php
                foreach($product_categories_arr as $cat)
                {
                    ?>
                    <option<?php echo (($categ_img_category[$ci_cnt]==$cat->id_category)?' selected="selected"':"");?> value="<?php echo esc_attr($cat->id_category);?>"><?php echo esc_html($cat->name);?> [<?php echo esc_html($cat->id_category);?>]</option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col mb-2">
            <?php echo esc_html__("Size","bizzmagsmarketplace");?>
        </div>
        <div class="col mb-2">
            <select name="emag_categ_img_size[]" id="emag_categ_img_size_<?php echo esc_attr($ci_cnt);?>" class="button">
                <option value=""> - </option>
                <?php
                foreach($image_sizes_arr as $size)
                {
                    $size_name=isset($image_sizes_names_arr[$size])?$image_sizes_names_arr[$size]:"N/A";
                    ?>
                    <option<?php echo (($categ_img_size[$ci_cnt]==$size)?' selected="selected"':"");?> value="<?php echo esc_attr($size);?>"><?php echo esc_html($size)." - ".esc_html($size_name);?></option>
                    <?php
                }
                ?>
            </select>
        </div>
        <div class="col mb-2">
            <?php
            if($ci_cnt==0)
            {
            ?>
            <input onClick="javascript:addNewCatImgRow();" type="button" class="button-secondary" value="+" />
            <?php
            }
            else
            {
                ?>
                <input onClick="javascript:removeCatImgRow('<?php echo esc_js($ci_cnt);?>');" type="button" class="button-secondary" value="-" />
                <?php
            }
            ?>
        </div>
        </div>
        <?php
        }
    }
    else
    {
    ?>
    <div class="row mb-2" id="categ_img_cnt_0">
    <div class="col mb-2">
        <?php echo esc_html__("Category","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <select name="emag_categ_img_category[]" id="emag_categ_img_category_0" class="button">
            <option value=""> - </option>
            <?php
            foreach($product_categories_arr as $cat)
            {
                ?>
                <option value="<?php echo esc_attr($cat->id_category);?>"><?php echo esc_html($cat->name);?> [<?php echo esc_html($cat->id_category);?>]</option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="col mb-2">
        <?php echo esc_html__("Size","bizzmagsmarketplace");?>
    </div>
    <div class="col mb-2">
        <select name="emag_categ_img_size[]" id="emag_categ_img_size_0" class="button">
            <option value=""> - </option>
            <?php
            foreach($image_sizes_arr as $size)
            {
                $size_name=isset($image_sizes_names_arr[$size])?$image_sizes_names_arr[$size]:"N/A";
                ?>
                <option value="<?php echo esc_attr($size);?>"><?php echo esc_html($size)." - ".esc_html($size_name);?></option>
                <?php
            }
            ?>
        </select>
    </div>
    <div class="col mb-2">
        <input onClick="javascript:addNewCatImgRow();" type="button" class="button-secondary" value="+" />
    </div>
    </div>
    <?php
    }
    ?>

    <div class="row mb-2">
    <div class="col mb-2">
        <input type="submit" value="<?php echo esc_attr(__("Save Settings","bizzmagsmarketplace"));?>" class="button-primary" />
    </div>
    </div>

</form>



</div>
</div>


<?php
}
else
    esc_html_e("Please set up the credentials first","bizzmagsmarketplace");
?>