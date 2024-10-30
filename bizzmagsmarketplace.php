<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bizzmags.ro
 * @since             1.0.0
 * @package           BizzmagsMarketplace
 *
 * @wordpress-plugin
 * Plugin Name:       Bizzmags Marketplace
 * Plugin URI:        https://bizzmags.ro
 * Description:       Connect your WC shop with eMag Marketplace.
 * Version:           1.0.7
 * Author:            Claudiu Maftei
 * Author URI:        https://bizzmags.ro/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bizzmagsmarketplace
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BIZZMAGSMARKETPLACE_VERSION', '1.0.7' );
define( 'BIZZMAGSMARKETPLACE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BIZZMAGSMARKETPLACE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bizzmagsmarketplace-activator.php
 */
function bizzmagsmarketplace_activate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace-activator.php';
    $activator = new BizzmagsMarketplace\BizzmagsMarketplace_Activator;
    $activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bizzmagsmarketplace-deactivator.php
 */
function bizzmagsmarketplace_deactivate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace-deactivator.php';
    $deactivator = new BizzmagsMarketplace\BizzmagsMarketplace_Deactivator;
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'bizzmagsmarketplace_activate' );
register_deactivation_hook( __FILE__, 'bizzmagsmarketplace_deactivate' );

// Creating table whenever a new blog is created
function bizzmagsmarketplace_new_blog_plugin_check($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace-activator.php';
    $activator=new BizzmagsMarketplace\BizzmagsMarketplace_Activator;
    $activator->on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta);
}
add_action( 'wpmu_new_blog', 'bizzmagsmarketplace_new_blog_plugin_check', 10, 6 );

// Deleting the table whenever a blog is deleted
function bizzmagsmarketplace_on_delete_blog_bizzmagsmarketplace_plugin_check( $tables ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace-activator.php';
    $activator=new BizzmagsMarketplace\BizzmagsMarketplace_Activator;
    return $activator->on_delete_blog($tables);
}
add_filter( 'wpmu_drop_tables', 'bizzmagsmarketplace_on_delete_blog_bizzmagsmarketplace_plugin_check' );

function bizzmagsmarketplace_check_version_plugin_check() {
    if (BIZZMAGSMARKETPLACE_VERSION !== get_option('BIZZMAGSMARKETPLACE_VERSION')){
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace-activator.php';
        $activator=new BizzmagsMarketplace\BizzmagsMarketplace_Activator;
        $activator->versionChanges();
    }
}
add_action('plugins_loaded', 'bizzmagsmarketplace_check_version_plugin_check');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bizzmagsmarketplace.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function bizzmagsmarketplace_plugin_start_run() {

    $plugin = new BizzmagsMarketplace\BizzmagsMarketplace();
    $plugin->run();

}
bizzmagsmarketplace_plugin_start_run();

function bizzmagsmarketplace_plugin_admin_main_page(){
    require_once dirname( __FILE__ )  . '/admin/partials/bizzmagsmarketplace-admin-display.php';
}
add_action('admin_menu', 'bizzmagsmarketplace_plugin_admin_menu_items');
function bizzmagsmarketplace_plugin_admin_menu_items()
{
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    require_once dirname( __FILE__ )  . '/admin/partials/bizzmagsmarketplace_svg.php';
    add_menu_page( "Bizzmags", "Bizzmags", "administrator", "bizzmagsmarketplace", "bizzmagsmarketplace_plugin_admin_main_page", $bizzmagsmarketplace_icon, 54.9);
    add_submenu_page( "bizzmagsmarketplace", __('Dashboard','bizzmagsmarketplace'), __('Dashboard','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace","bizzmagsmarketplace_plugin_admin_main_page",1);
    if($emag->config->emag_dropshipping=='yes')
    {
        add_submenu_page( "bizzmagsmarketplace", __('Categories','bizzmagsmarketplace'), __('Categories','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-categories","bizzmagsmarketplace_plugin_admin_main_page",2);
        add_submenu_page( "bizzmagsmarketplace", __('Category','bizzmagsmarketplace'), __('Category','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-category","bizzmagsmarketplace_plugin_admin_main_page",3);
    }
    add_submenu_page( "bizzmagsmarketplace", __('Configuration','bizzmagsmarketplace'), __('Configuration','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-configuration","bizzmagsmarketplace_plugin_admin_main_page",4);
    add_submenu_page( "bizzmagsmarketplace", __('Prices','bizzmagsmarketplace'), __('Prices','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-prices","bizzmagsmarketplace_plugin_admin_main_page",4);
    add_submenu_page( "bizzmagsmarketplace", __('Category Map','bizzmagsmarketplace'), __('Category Map','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-category-map","bizzmagsmarketplace_plugin_admin_main_page",5);
    add_submenu_page( "bizzmagsmarketplace", __('Characteristics Map','bizzmagsmarketplace'), __('Characteristics Map','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-characteristics-map","bizzmagsmarketplace_plugin_admin_main_page",6);
    add_submenu_page( "bizzmagsmarketplace", __('Family Map','bizzmagsmarketplace'), __('Family Map','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-family-map","bizzmagsmarketplace_plugin_admin_main_page",7);
    
    if($emag->config->emag_dropshipping=='yes')
    {
    add_submenu_page( "bizzmagsmarketplace", __('Excludes','bizzmagsmarketplace'), __('Excludes','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-excludes","bizzmagsmarketplace_plugin_admin_main_page",8);
    }

    add_submenu_page( "bizzmagsmarketplace", __('Logs','bizzmagsmarketplace'), __('Logs','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-logs","bizzmagsmarketplace_plugin_admin_main_page",9);
    add_submenu_page( "bizzmagsmarketplace", __('Help','bizzmagsmarketplace'), __('Help','bizzmagsmarketplace'), "administrator", "bizzmagsmarketplace-help","bizzmagsmarketplace_plugin_admin_main_page",10);
}

add_filter( 'plugin_action_links', 'bizzmagsmarketplace_show_plugin_admin_settings_link', 10, 2 );

function bizzmagsmarketplace_show_plugin_admin_settings_link( $links, $file ) 
{
    if ( $file == plugin_basename(dirname(__FILE__) . '/bizzmagsmarketplace.php') ) 
    {
        $links = array_merge(array('<a href="'.esc_url(admin_url().'admin.php?page=bizzmagsmarketplace').'">'.__('Dashboard','bizzmagsmarketplace').'</a>'),$links);
    }
    return $links;
}

add_action( 'bizzmagsmarketplace_send_to_emag_batch_reschedule_hook', 'bizzmagsmarketplace_send_to_emag_batch_reschedule', 10, 2 );
function bizzmagsmarketplace_send_to_emag_batch_reschedule() {
    $args = array(
        'role'    => 'administrator',
        'number' => 1
    );
    $users = get_users( $args );
    if(is_array($users) && count($users)==1)
        wp_set_current_user($users[0]->ID);
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->sendToEmagBatchReschedule();
    wp_set_current_user(0);
}

add_action( 'bizzmagsmarketplace_import_categories_hook', 'bizzmagsmarketplace_import_categories', 10, 2 );
function bizzmagsmarketplace_import_categories() {
    $args = array(
        'role'    => 'administrator',
        'number' => 1
    );
    $users = get_users( $args );
    if(is_array($users) && count($users)==1)
        wp_set_current_user($users[0]->ID);
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->importEmagCategories();
    wp_set_current_user(0);
}

add_action( 'wp_ajax_bizzmagsmarketplace_get_import_categ_status', 'bizzmagsmarketplace_get_import_categ_status' );
function bizzmagsmarketplace_get_import_categ_status() {
    check_ajax_referer( 'bizzmagsmarketplace_get_import_categ_status_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->getImportCategStatusAjax();
    wp_die();
}


add_action( 'bizzmagsmarketplace_send_to_emag_hook', 'bizzmagsmarketplace_send_to_emag', 10, 2 );
function bizzmagsmarketplace_send_to_emag() {
    $args = array(
        'role'    => 'administrator',
        'number' => 1
    );
    $users = get_users( $args );
    if(is_array($users) && count($users)==1)
        wp_set_current_user($users[0]->ID);
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->sendToEmag();
    wp_set_current_user(0);
}

add_action( 'wp_ajax_bizzmagsmarketplace_get_send_to_emag_status', 'bizzmagsmarketplace_get_send_to_emag_status' );
function bizzmagsmarketplace_get_send_to_emag_status() {
    check_ajax_referer( 'bizzmagsmarketplace_get_send_to_emag_status_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->getSendToEmagStatusAjax();
    wp_die();
}

add_action( 'wp_ajax_bizzmagsmarketplace_get_emag_vat_rates', 'bizzmagsmarketplace_get_emag_vat_rates' );
function bizzmagsmarketplace_get_emag_vat_rates() {
    check_ajax_referer( 'bizzmagsmarketplace_get_emag_vat_rate_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->getEmagVatRates();
    wp_die();
}

add_action( 'wp_ajax_bizzmagsmarketplace_get_emag_handling_times', 'bizzmagsmarketplace_get_emag_handling_times' );
function bizzmagsmarketplace_get_emag_handling_times() {
    check_ajax_referer( 'bizzmagsmarketplace_get_emag_handling_times_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->getEmagHandlingTimes();
    wp_die();
}

add_action( 'wp_ajax_bizzmagsmarketplace_emag_marketplace_cat_result_search', 'bizzmagsmarketplace_emag_marketplace_cat_result_search' );
function bizzmagsmarketplace_emag_marketplace_cat_result_search() {
    check_ajax_referer( 'bizzmagsmarketplace_emag_marketplace_cat_result_search_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->emagCatResultSearch();
    wp_die();
}

add_action( 'wp_ajax_bizzmagsmarketplace_emag_marketplace_remove_category_map', 'bizzmagsmarketplace_emag_marketplace_remove_category_map' );
function bizzmagsmarketplace_emag_marketplace_remove_category_map() {
    check_ajax_referer( 'bizzmagsmarketplace_emag_marketplace_remove_category_map_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->removeEmagCategoryMap();
    wp_die();
}

add_action( 'wp_ajax_bizzmagsmarketplace_save_characteristic_map', 'bizzmagsmarketplace_save_characteristic_map' );
function bizzmagsmarketplace_save_characteristic_map() {
    check_ajax_referer( 'bizzmagsmarketplace_save_characteristic_map_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->saveEmagCharacteristicMap();
    wp_die();
}

add_action( 'wp_ajax_bizzmagsmarketplace_save_family_map', 'bizzmagsmarketplace_save_family_map' );
function bizzmagsmarketplace_save_family_map() {
    check_ajax_referer( 'bizzmagsmarketplace_save_family_map_nonce', 'security' );
    require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
    $emag=new BizzmagsMarketplace\emag;
    $emag->saveEmagFamilyMap();
    wp_die();
}

function bizzmagsmarketplace_bulk_action_edit_product($bulk_actions) {
    $bulk_actions['bizzmags_send_to_emag_bulk_action'] = __('Send to eMag', 'bizzmagsmarketplace');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-product', 'bizzmagsmarketplace_bulk_action_edit_product');

function bizzmagsmarketplace_bulk_action_handle_edit_product($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'bizzmags_send_to_emag_bulk_action') {
        return $redirect_to;
    }
    //check if running
    $sending_to_emag_running=0;
    if($sending_to_emag_running==0)
    {
        require_once plugin_dir_path( __FILE__ )  . 'includes/emag.php';
        $emag=new BizzmagsMarketplace\emag;
        $emag->addSendtoEmagQueue($post_ids);
        $redirect_to = add_query_arg(array(
            'send_to_emag_action_done' => count($post_ids),
            'selected_products' => implode(',', $post_ids)
        ), $redirect_to);
    }

    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-product', 'bizzmagsmarketplace_bulk_action_handle_edit_product', 10, 3);

function bizzmagsmarketplace_bulk_action_admin_notice() {
    if (!empty($_REQUEST['send_to_emag_action_done'])) {
        $count = intval($_REQUEST['send_to_emag_action_done']);
        $selected_products = isset($_REQUEST['selected_products']) ? sanitize_text_field($_REQUEST['selected_products']) : '';
        if($count>0)
        {
            //check if running
            $sending_to_emag_running=0;
            if($sending_to_emag_running==1)
            {
                ?>
                <div class="error notice is-dismissible">
                    <p>
                <?php esc_html_e("Cannot add in Send to eMag queue whilst the sending to eMag process is running","bizzmagsmarketplace");?>
                    </p>
                </div>
                <?php
            }
            else
            {
                ?>
                <div class="updated notice is-dismissible">
                    <p>
                <?php esc_html_e("Added the products in the Send to eMag queue, continue the process in","bizzmagsmarketplace");?> <a href="<?php echo esc_js(admin_url('admin.php'))."?page=bizzmagsmarketplace";?>" target="_blank"><?php esc_html_e("Bizzmagsmarketplace Dashboard","bizzmagsmarketplace");?></a>
                    </p>
                </div>
                <?php
            }
            ?>
                
            <?php
            $data_js = '
                document.addEventListener("DOMContentLoaded", function() {
                    if (history.pushState) {
                        var newUrl = window.location.href.split("&send_to_emag_action_done")[0].split("&selected_products")[0];
                        history.pushState({}, null, newUrl);
                    }
                    var selectedProducts = "' . esc_js($selected_products) . '".split(",");
                    selectedProducts.forEach(function(id) {
                        var row = document.querySelector("#post-" + id);
                        if (row) {
                            row.style.backgroundColor = "#ffffe0";
                        }
                    });
                });
            ';
            wp_register_script( 'bizzmagsmarketplace_send_to_emag_products_inline_js', '' );
            wp_enqueue_script( 'bizzmagsmarketplace_send_to_emag_products_inline_js' );
            wp_add_inline_script("bizzmagsmarketplace_send_to_emag_products_inline_js",$data_js);
        }
    }
}
add_action('admin_notices', 'bizzmagsmarketplace_bulk_action_admin_notice');