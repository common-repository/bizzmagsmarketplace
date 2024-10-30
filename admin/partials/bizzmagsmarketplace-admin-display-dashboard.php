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
if($action=="emag_save_api_credentials")
{
    check_admin_referer( 'bizzmagsmarketplace_save_api_credentials' );
    $msg=$emag->saveEmagApiCredentials();
}
if($action=="emag_import_categories")
{
    check_admin_referer( 'bizzmagsmarketplace_import_emag_categories' );
    $msg=$emag->importEmagCategoriesHook();
}
if($action=="emag_send_to_emag")
{
    check_admin_referer( 'bizzmagsmarketplace_send_to_emag' );
    $msg=$emag->sendToEmagHook();
}

?>
<h2><?php esc_html_e('Dashboard','bizzmagsmarketplace');?></h2>
<p><strong><?php esc_html_e("This free version of the plugin is used to send/update products to the marketplace, it does not handle product ID conflicts, if you already have products in the marketplace and your local shop has different IDs for products, conflicts may appear where you could override your existing marketplace products, otherwise if you have the same product IDs the plugin will update the products. The paid version of this plugin handles ID conflicts and has many other functions","bizzmagsmarketplace");?>.</strong></p>
<p><strong><?php esc_html_e('Steps for usage:','bizzmagsmarketplace');?></strong> 1 - <?php esc_html_e('Read the Help page','bizzmagsmarketplace');?> 2 - <?php esc_html_e('Import Categories','bizzmagsmarketplace');?> 3 - <?php esc_html_e('Configure the system','bizzmagsmarketplace');?> 4 - <?php esc_html_e('Send products to marketplace','bizzmagsmarketplace');?></p>
<p><a href="https://bizzmags.ro/bizzmags-marketplace-wordpress-woocommerce-plugin/" target="_blank"><?php esc_html_e('Get the Pro version of this plugin Free for a limited period of time','bizzmagsmarketplace');?></a></p>
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
        <p><?php echo esc_html($msg['msg']);?></p>
    </div>
    <?php
    }
}
$have_errors=false;
$php_version_good=false;
if(version_compare( PHP_VERSION, '7.2' ) >= 0)
    $php_version_good=true;

if(!$emag->is_woocommerce_activated())
{
    ?>
    <div class="emag-error-div">
        <?php esc_html_e("WooCommerce is not installed or activated, please install or activate it in order to continue","bizzmagsmarketplace");?>.
    </div>
    <?php
    $have_errors=true;
}
if(!$php_version_good)
{
    ?>
    <div class="emag-error-div">
        <?php esc_html_e("PHP version must be at least 7.2","bizzmagsmarketplace");?>.
    </div>
    <?php
    $have_errors=true;
}
if(function_exists('WC') && !is_file(WC()->plugin_path()."/packages/action-scheduler/action-scheduler.php"))
{
    ?>
    <div class="emag-error-div">
        <?php esc_html_e("Action Scheduler doesn't seam to be installed, this is required for the cron jobs","bizzmagsmarketplace");?>.
    </div>
    <?php
    $have_errors=true;
}
if(!$have_errors)
{
?>
<div class="container emag-no-pad">
<div class="emag-dash-row">
<form method="post" action="" autocomplete="off" name="save_api_details">
<?php $nonce=wp_create_nonce( 'bizzmagsmarketplace_save_api_credentials' );?>
<input type="hidden" name="action" value="emag_save_api_credentials" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
<div class="row">
    <div class="col-2">
        <?php esc_html_e("eMag API Endpoint Url","bizzmagsmarketplace");?>
    </div>
    <div class="col-10">
        <input type="text" placeholder="<?php echo esc_attr(__("Find the API Endpoint in the API documentation","bizzmagsmarketplace"));?>" id="emag_api_endpoint" name="emag_api_endpoint" class="button" value="<?php echo esc_attr($emag->config->emag_api_endpoint);?>" style="width:100%;" />
    </div>
</div>
<div class="row mt-3">
    <div class="col">
        <label for="emag_api_user"><?php esc_html_e("Emag API User","bizzmagsmarketplace");?></label>
    </div>
    <div class="col">
        <input autocomplete="new-password" autocomplete="off" name="api_user" id="emag_api_user" type="text" class="button" value="<?php echo esc_attr($emag->config->emag_api_user);?>" />
    </div>
    <div class="col">
        <label for="emag_api_password"><?php esc_html_e("Emag API Password","bizzmagsmarketplace");?></label>
    </div>
    <div class="col">
        <input autocomplete="new-password" autocomplete="off" name="api_password" id="emag_api_password" type="password" class="button" value="<?php echo esc_attr($emag->config->emag_api_password);?>" />
    </div>
    <div class="col">
        <input type="submit" class="button-primary" value="<?php echo esc_attr(__("Save",'bizzmagsmarketplace'));?>" />
    </div>
</div>
</form>
</div>

<?php
}
if($emag->config->emag_api_user!="" && $emag->config->emag_api_user!="" && $emag->config->emag_categ_cnt>0)
{
?>
<div class="emag-dash-row">
<form method="post" action="" autocomplete="off">
<?php $nonce=wp_create_nonce( 'bizzmagsmarketplace_import_emag_categories' );?>
<input type="hidden" name="action" value="emag_import_categories" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
<div class="row">
    <div class="col">
        <?php echo esc_html((int)$emag->config->emag_categ_cnt)." ".esc_html__("Remote Emag Categories","bizzmagsmarketplace");?><br />
        <small><?php echo esc_html(__("As provided by the API","bizzmagsmarketplace"));?></small>
    </div>
    <div class="col">
        <input type="button" class="button-secondary" value="<?php echo esc_attr(__("Check Again",'bizzmagsmarketplace'));?>" onClick="javascript:document.save_api_details.submit();" />
    </div>
    <div class="col">
        <?php echo esc_html((int)$emag->getCategoriesCount())." ".esc_html__("Local Emag Categories","bizzmagsmarketplace");?><br />
        <small><?php echo esc_html(__("As imported through the API","bizzmagsmarketplace"));?></small>
    </div>
    <div class="col">
        <?php
        if($emag->config->emag_cat_import_started!=1)
        {
        ?>
        <input type="submit" class="button-primary" value="<?php echo esc_attr(__("Import Categories",'bizzmagsmarketplace'));?>" />
        <?php
        }
        else
        {
            $nonce=wp_create_nonce( 'bizzmagsmarketplace_get_import_categ_status_nonce' );
            $data_js='
            jQuery(document).ready(function() {
                setInterval(function(){
                    jQuery.ajax({
                        method: "POST",
                        data: { 
                            "action": "bizzmagsmarketplace_get_import_categ_status", 
                            "security": \''.esc_js($nonce).'\',
                        },
                        url: \''.esc_url(admin_url('admin-ajax.php')).'\',
                        async: true
                    })
                    .done(function( msg ) {
                        if(msg==1)
                        {
                            location.reload();
                        }
                        else
                        {
                            jQuery(\'#emag_import_categ_status\').html(msg);
                        }
                    });
                }, 5000);
            });
            ';
            wp_register_script( 'bizzmagsmarketplace_get_import_categ_status_js', '' );
            wp_enqueue_script( 'bizzmagsmarketplace_get_import_categ_status_js' );
            wp_add_inline_script("bizzmagsmarketplace_get_import_categ_status_js",$data_js);

            $imported=$emag->getCategoriesCount("importing");
            $import_percent=intval(($imported*100)/$emag->config->emag_categ_cnt);
            ?>
            <div id="emag_import_categ_status">
            <?php
            //echo esc_html__("Import running","bizzmagsmarketplace")." ";
            ?>
            <div class="progress-bar">
                <span class="progress-bar-fill" style="width: <?php echo esc_attr($import_percent);?>%;"></span>
            </div>
            <?php
            echo esc_html($imported)."/".esc_html($emag->config->emag_categ_cnt);
            ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    if($emag->config->emag_cat_import_started==1)
    {
    ?>
    <div class="col">
        <input type="hidden" name="emag_cat_import_cancel" value="1" />
        <input type="submit" class="button-secondary" value="<?php echo esc_attr(__("Cancel",'bizzmagsmarketplace'));?>" />
    </div>
    <?php
    }
    ?>
    <?php
    if($emag->config->emag_cat_import_started==0)
    {
        ?>
        <div class="col">
        <?php
        echo (int)$emag->getCategoriesCountAllowed()." ";
        esc_html_e("Categories with access","bizzmagsmarketplace");
        ?>
        </div>
        <?php
    }
    ?>
</div>
</form>
</div>
<?php
$send_to_emag_cnt=$emag->getSendToEmagCount();
?>

<div class="emag-dash-row">
<form method="post" action="" autocomplete="off">
<?php $nonce=wp_create_nonce( 'bizzmagsmarketplace_send_to_emag' );?>
<input type="hidden" name="action" value="emag_send_to_emag" />
<input type="hidden" name="_wpnonce" value="<?php echo esc_attr($nonce);?>" />
<div class="row">
    <?php
    if($emag->config->emag_send_to_emag_started!=1)
    {
        ?>
    <div class="col">
        <?php echo esc_html($send_to_emag_cnt);?> <?php esc_html_e("products in queue to Send to eMag","bizzmagsmarketplace");?>
    </div>
    <?php
    }
    ?>
    <div class="col">
        <?php
        if($emag->config->emag_send_to_emag_live=='no')
            esc_html_e("Send to eMag Live set to NO, change it to Yes in configuration when ready, read in logs what data is to be sent","bizzmagsmarketplace");
        else
        {
            ?>
            <strong>
            <?php esc_html_e("Send to eMag Live set to YES","bizzmagsmarketplace");?>
            </strong>
            <?php
        }
        ?>
    </div>
    <div class="col" style="display:none;">
        <?php esc_html_e("Force Update images","bizzmagsmarketplace");?>
        <select <?php echo (($emag->config->emag_send_to_emag_started==1)?'disabled="disabled"':'');?> name="send_to_emag_update_images" id="send_to_emag_update_images">
            <option value="0" selected="selected"><?php echo esc_html_e("No","bizzmagsmarketplace");?></option>
            <option value="1"><?php echo esc_html_e("Yes","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <div class="col">
        <?php esc_html_e("Status","bizzmagsmarketplace");?>
        <select <?php echo (($emag->config->emag_send_to_emag_started==1)?'disabled="disabled"':'');?> name="send_to_emag_status" id="send_to_emag_status">
            <option value="0"><?php echo esc_html_e("Inactive","bizzmagsmarketplace");?></option>
            <option value="1" selected="selected"><?php echo esc_html_e("Active","bizzmagsmarketplace");?></option>
        </select>
    </div>
    <?php
    if($emag->config->emag_send_to_emag_started!=1)
    {
        $disable_btn=0;
        if((int)$emag->getCategoriesCount()==0 || $send_to_emag_cnt==0)
            $disable_btn=1;
        ?>
    <div class="col">
        <input <?php echo (($disable_btn==1)?'disabled="disabled"':'');?> type="submit" class="button button-primary" value="<?php echo esc_attr(__("Send to eMag","bizzmagsmarketplace"));?>" />
    </div>
        <?php
    }
    else
    {
        ?>
        <div class="col">
        <?php
        $nonce=wp_create_nonce( 'bizzmagsmarketplace_get_send_to_emag_status_nonce' );
        $data_js='
        jQuery(document).ready(function() {
            setInterval(function(){
                jQuery.ajax({
                    method: "POST",
                    data: { 
                        "action": "bizzmagsmarketplace_get_send_to_emag_status", 
                        "security": \''.esc_js($nonce).'\',
                    },
                    url: \''.esc_url(admin_url('admin-ajax.php')).'\',
                    async: true
                })
                .done(function( msg ) {
                    if(msg==1)
                    {
                        location.reload();
                    }
                    else
                    {
                        jQuery(\'#emag_send_to_emag_status\').html(msg);
                    }
                });
            }, 5000);
        });
        ';
        wp_register_script( 'bizzmagsmarketplace_send_to_emag_status_js', '' );
        wp_enqueue_script( 'bizzmagsmarketplace_send_to_emag_status_js' );
        wp_add_inline_script("bizzmagsmarketplace_send_to_emag_status_js",$data_js);

        $imported=0;
        $import_percent=intval(($imported*100)/$emag->config->emag_send_to_emag_cnt);
        ?>
        <div id="emag_send_to_emag_status">
        <div class="progress-bar">
            <span class="progress-bar-fill" style="width: <?php echo esc_attr($import_percent);?>%;"></span>
        </div>
        <?php
        echo esc_html($imported)."/".esc_html($emag->config->emag_send_to_emag_cnt);
        ?>
        </div>
        </div>
        <?php
    }

    if($emag->config->emag_send_to_emag_started==1)
    {
    ?>
    <div class="col">
        <input type="hidden" name="send_to_emag_cancel" value="1" />
        <input type="submit" class="button-secondary" value="<?php echo esc_attr(__("Cancel",'bizzmagsmarketplace'));?>" />
    </div>
    <?php
    }
    ?>
</div>
</form>
</div>
<?php

if($emag->getCategoriesCount() > 0)
{
?>
<div class="emag-dash-row">
<?php
$bb1=$emag->getBuyButtonProductsCount(1,1);
$bb2=$emag->getBuyButtonProductsCount(2,2);
$bb3=$emag->getBuyButtonProductsCount(3,3);
$bb_other=$emag->getBuyButtonProductsCount(4,100);
?>
<div class="row">
    <div class="col">
        <?php echo esc_html((int)$bb1)." ".esc_html__("Buy Button 1","bizzmagsmarketplace");?><br />
    </div>
    <div class="col">
        <?php echo esc_html((int)$bb2)." ".esc_html__("Buy Button 2","bizzmagsmarketplace");?>
    </div>
    <div class="col">
        <?php echo esc_html((int)$bb3)." ".esc_html__("Buy Button 3","bizzmagsmarketplace");?>
    </div>
    <div class="col">
        <?php
        if($emag->config->emag_prod_import_started!=1)
        {
        ?>
        <input type="submit" disabled="disabled" class="button-primary" value="<?php echo esc_attr(__("Import Products Pro",'bizzmagsmarketplace'));?>" />
        <?php
        if($emag->config->emag_dropshipping=='yes')
        {
        ?>
        <br />
        <input class="mt-2" disabled="disabled" id="create_missing" type="checkbox" value="" />
        <label class="mt-2" for="create_missing"><?php esc_html_e("Create missing",'bizzmagsmarketplace');?>
            <span style="color:green;">(<?php esc_html_e("Pro","bizzmagsmarketplace");?>)</span>
        </label>
        <?php
        }
        }
        ?>
    </div>
</div>
</div>
<?php
}
if($emag->config->emag_rank_log!='')
{
    ?>
    <div class="emag-dash-row">
        <div class="col">
            <strong><?php esc_html_e("Buy Button Rank Changes from the last import","bizzmagsmarketplace");?></strong>
        </div>
        <div class="col mt-2">
            <?php
            echo nl2br(esc_html($emag->config->emag_rank_log));
            ?>
        </div>
    </div>
    <?php
}
}

?>

</div>
