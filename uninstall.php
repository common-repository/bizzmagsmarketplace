<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

require_once plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace-activator.php';
$activator=new BizzmagsMarketplace\BizzmagsMarketplace_Activator;
$activator->deleteTables();

$bizzmags_hooks=array("bizzmagsmarketplace_import_categories_hook","bizzmagsmarketplace_import_products_hook","bizzmagsmarketplace_send_to_emag_batch_reschedule_hook","bizzmagsmarketplace_send_to_emag_hook");
if(isset($bizzmags_hooks) && is_array($bizzmags_hooks)  && count($bizzmags_hooks)>0)
{
    foreach($bizzmags_hooks as $hook)
    {
        if(function_exists("as_unschedule_all_actions"))
            as_unschedule_all_actions(sanitize_text_field($hook),array(),"bizzmagsmarketplace");
    }
}