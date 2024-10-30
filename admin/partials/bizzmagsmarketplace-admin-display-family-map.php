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
?>

<h2><?php esc_html_e('Family Map','bizzmagsmarketplace');?></h2>
<?php
if($emag->config->emag_api_user!="" && $emag->config->emag_api_password!="" && $emag->config->emag_categ_cnt>0)
{
?>
<p><?php esc_html_e('Here you can map eMag product families with WC product attributes (variations), this map is not used for the products CSV Feed, it is used only for sending products via the eMag API','bizzmagsmarketplace');?></p>
<?php
$data=$emag->getFamilyMap();
$attributes=$data->attributes;
$families=$data->families;
?>
<div class="container emag-no-pad">
<div class="emag-dash-row">
<?php
if(count($families)>0)
{
    ?>
    <div class="row mb-2">
    <div class="col mb-2">
        <strong><?php esc_html_e("WC Attribute","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col mb-2">
        <strong><?php esc_html_e("eMag Family","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col mb-2">
        <strong><?php esc_html_e("Characteristics","bizzmagsmarketplace");?></strong>
    </div>
    <div class="col mb-2">
        <strong><?php esc_html_e("Save","bizzmagsmarketplace");?></strong>
    </div>
    </div>
    <?php
    foreach($families as $fam)
    {
        ?>
        <div class="row mb-2">
        <div class="col mb-2">
            <?php
            $tmp=str_ireplace("(hidden)","",$fam->name);
            $tmp=str_ireplace("(visible)","",$tmp);
            $tmp=str_ireplace(" ","",$tmp);
            $tarr=explode("-",$tmp);

            $multiple='';
            if(is_array($tarr) && count($tarr)>1)
                $multiple=' multiple="multiple"';

            if(count($attributes)>0)
            {
                ?>
                <select onChange="javascript:saveFamilyAttribute(<?php echo esc_js($fam->id);?>);" id="family_attributes_<?php echo esc_attr($fam->id);?>" class="button"<?php echo esc_html($multiple);?> style="width:100%;">
                <?php
                if(is_array($tarr) && count($tarr)==1)
                {
                    ?>
                    <option value="">-</option>
                    <?php
                }
                foreach($attributes as $attr)
                {
                    ?>
                    <option <?php echo ((in_array($attr->attribute,$fam->attributes))?'selected="selected"':'');?> value="<?php echo esc_attr($attr->attribute);?>"><?php echo esc_html($attr->name);?></option>
                    <?php
                }
                ?>
                </select>
                <?php
            }
            else
            {
                esc_html_e("No attributes defined","bizzmagsmarketplace");
            }
            ?>
        </div>
        <div class="col mb-2">
            [<?php echo esc_html($fam->id);?>] <?php echo esc_html($fam->name);?>
        </div>
        <div class="col mb-2">
            <?php
            if(count($fam->chars)>0)
            {
                foreach($fam->chars as $char)
                {
                    ?>
                    [<?php echo esc_html($char->id);?>] <?php echo esc_html($char->name);?> <br />
                    <?php
                }
            }
            ?>
        </div>
        <div class="col mb-2">
            <input type="button" onClick="javascript:saveFamilyAttribute(<?php echo esc_js($fam->id);?>);" class="button button-secondary" id="save_btn_<?php echo esc_attr($fam->id);?>" value="<?php echo esc_attr(__("Save","bizzmagsmarketplace"));?>" />
        </div>
        </div>
        <?php
    }
    $nonce_1=wp_create_nonce( 'bizzmagsmarketplace_save_family_map_nonce' );
    $data_js="
function saveFamilyAttribute(fam_id)
{
    if(parseInt(fam_id)>0)
    {
        attributes=jQuery('#family_attributes_'+fam_id).val();
        jQuery('#save_btn_'+fam_id).val('...');
        jQuery.ajax({
            method: 'POST',
            data: { 
                'action': 'bizzmagsmarketplace_save_family_map', 
                'security': '".esc_js($nonce_1)."',
                'fam_id': fam_id,
                'attributes': attributes,
            },
            url: '".esc_url(admin_url('admin-ajax.php'))."',
            async: true
        })
        .done(function( msg ) {
            jQuery('#save_btn_'+fam_id).val(msg);
            jQuery('#save_btn_'+fam_id).removeClass('button button-secondary');
            jQuery('#save_btn_'+fam_id).addClass('button button-primary');
        });
    }
    else
    {
        alert('".esc_js(__("Something is wrong, did not receive all data","bizzmagsmarketplace"))."');
    }
}
";
    if($data_js!='')
    {
        wp_register_script( 'bizzmagsmarketplace_fam_map_page_inline_js', '' );
        wp_enqueue_script( 'bizzmagsmarketplace_fam_map_page_inline_js' );
        wp_add_inline_script("bizzmagsmarketplace_fam_map_page_inline_js",$data_js);
    }
}
else
{
?>
    <div class="row mb-2">
    <div class="col mb-2">
        <?php esc_html_e("No records","bizzmagsmarketplace");?>
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