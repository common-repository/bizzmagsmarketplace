<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
namespace BizzmagsMarketplace;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BizzmagsMarketplace_Activator {

    public $emagTables = array();
    public $sql=array();

    public function __construct()
    {
        global $wpdb;

        $this->emagTables[] = 'bizzmags_emag_mktpl_category';
        $this->emagTables[] = 'bizzmags_emag_mktpl_category_char';
        $this->emagTables[] = 'bizzmags_emag_mktpl_category_char_rel';
        $this->emagTables[] = 'bizzmags_emag_mktpl_category_fam';
        $this->emagTables[] = 'bizzmags_emag_mktpl_category_fam_char';
        $this->emagTables[] = 'bizzmags_emag_mktpl_category_fam_rel';
        $this->emagTables[] = 'bizzmags_config';
        $this->emagTables[] = 'bizzmags_emag_mktpl_logs';
        $this->emagTables[] = 'bizzmags_emag_mktpl_product_category';
        $this->emagTables[] = 'bizzmags_emag_mktpl_product_price_override';
        $this->emagTables[] = 'bizzmags_emag_mktpl_push_product';
        $this->emagTables[] = 'bizzmags_emag_mktpl_push_product_char';
        $this->emagTables[] = 'bizzmags_emag_mktpl_imported_orders';
        $this->emagTables[] = 'bizzmags_emag_mktpl_prod_id_rel';
        $this->emagTables[] = 'bizzmags_emag_mktpl_category_map';
        $this->emagTables[] = 'bizzmags_emag_mktpl_push_product_skip';
        $this->emagTables[] = 'bizzmags_emag_mktpl_push_product_commission';
        $this->emagTables[] = 'bizzmags_emag_mktpl_rank_log';
        $this->emagTables[] = 'bizzmags_emag_mktpl_char_map';
        $this->emagTables[] = 'bizzmags_emag_mktpl_push_product_fam';
        $this->emagTables[] = 'bizzmags_emag_mktpl_fam_map';
        $this->emagTables[] = 'bizzmags_emag_mktpl_send_to_emag';

        $this->sql[]=$wpdb->prepare("insert INTO `".$wpdb->prefix."bizzmags_config` 
        (`config_name`, `config_value`, `show_front`, `mdate`) VALUES
        ('delete_tables_upon_uninstall', 'no', '1', 1),
        ('emag_api_user', '', 1, 1),
        ('emag_api_password', '', 1, 1),
        ('emag_api_country', '1', 1, 1),
        ('emag_api_start', '1', 0, 1),
        ('emag_swagg_api_start', '1', 0, 1),
        ('emag_categ_cnt', '0', 0, 1),
        ('emag_cat_import_started', '0', 0, 1),
        ('emag_vat_id', '5', 0, 1),
        ('emag_handling_time', '0', 0, 1),
        ('emag_resupply_days', '14', 0, 1),
        ('emag_warranty_months', '24', 0, 1),
        ('emag_product_status', '1', 0, 1),
        ('emag_vat_rate', '0', 0, 1),
        ('emag_product_price_alter', 'addition', 0, 1),
        ('emag_product_price_alter_value', '33', 0, 1),
        ('emag_product_price_alter_type', 'percent', 0, 1),
        ('emag_product_price_alter_formula', 'excluded', 0, 1),
        ('emag_min_sale_price', '10', 0, 1),
        ('emag_max_sale_price', '25', 0, 1),
        ('emag_recommended_price', '45', 0, 1),
        ('emag_prod_import_started', '0', 0, 1),
        ('emag_prod_cnt', '0', 0, 1),
        ('emag_product_price_alter_value_commission_vat', '19', 0, 1),
        ('emag_product_price_max_commission', '23', 0, 1),
        ('emag_use_product_commission', 'no', 0, 1),
        ('emag_sync_product_commission', 'no', 0, 1),
        ('emag_stock_zero_sku', '', 0, 1),
        ('emag_stock_zero_weight', '4000', 0, 1),
        ('emag_stock_zero_price', '10', 0, 1),
        ('emag_stock_zero_stock_lower', '4', 0, 1),
        ('emag_max_percent_substract_from_price', '10', 0, 1),
        ('emag_rank_log', '', 0, 1),
        ('emag_add_percent_before_commission', '0', 0, 1),
        ('emag_import_order_status', '2', 0, 1),
        ('emag_categ_img_category', '', 0, 1),
        ('emag_categ_img_size', '', 0, 1),
        ('emag_create_product_price_minus_percent', '40', '0', '1'),
        ('emag_dropshipping', 'no', '0', '1'),
        ('emag_order_carrier_free', '', '0', '1'),
        ('emag_order_voucher_zero', 'no', '0', '1'),
        ('emag_prod_batch_import', 'no', '0', '1'),
        ('emag_prod_batch_import_start_time', '0', '0', '1'),
        ('emag_prod_batch_import_started', 'no', '0', '1'),
        ('emag_prod_batch_import_nr', '100', '0', '1'),
        ('emag_prod_batch_import_page', '0', '0', '1'),
        ('emag_prod_batch_import_create_products', '0', '0', '1'),
        ('emag_api_req_sec', '3', '0', '1'),
        ('emag_swagg_api_req_sec', '3', '0', '1'),
        ('emag_api_sercret', '', '0', '1'),
        ('emag_send_to_emag_started', '0', '0', '1'),
        ('emag_send_to_emag_update_products', '0', '0', '1'),
        ('emag_send_to_emag_force_update_images', '0', '0', '1'),
        ('emag_send_to_emag_product_status', '0', '0', '1'),
        ('emag_send_to_emag_live', 'no', '0', '1'),
        ('emag_feed_override_product_data', '', '0', '1'),
        ('emag_product_ean_meta', '', '0', '1'),
        ('emag_prod_batch_send_to_emag', 'yes', '0', '1'),
        ('emag_prod_batch_send_to_emag_nr', '25', '0', '1'),
        ('emag_send_to_emag_cnt', '0', '0', '1'),
        ('emag_api_endpoint', '', '0', '1'),
        ('emag_product_brand_meta', '', '0', '1')
        ON DUPLICATE KEY UPDATE mdate=%d",time());
    }

	public function activate() {
        $this->runFunctionsForMultiOrSingleBlog("createTables");
	}

    public function createTables(){
        global $wpdb;

        require_once( ABSPATH . "wp-admin/includes/upgrade.php" );
        if(get_option('BIZZMAGSMARKETPLACE_VERSION')==false)
            update_option('BIZZMAGSMARKETPLACE_VERSION',BIZZMAGSMARKETPLACE_VERSION);

        $table_name=$wpdb->prefix.'bizzmags_config';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_config` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `config_name` varchar(255) NOT NULL,
                `config_value` text NOT NULL DEFAULT '',
                `show_front` tinyint(1) NOT NULL,
                `mdate` int(11) NOT NULL, 
                PRIMARY KEY (`id`),
                UNIQUE KEY config_name (`config_name`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category` (
                `id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL DEFAULT '',
                `full_path` varchar(512) NOT NULL,
                `name_index` varchar(512) NOT NULL,
                `requested` tinyint(1) NOT NULL DEFAULT 0,
                `scm_id` int(11) NOT NULL,
                `parent_id` int(11) NOT NULL,
                `is_ean_mandatory` tinyint(1) NOT NULL DEFAULT 0,
                `is_warranty_mandatory` tinyint(1) NOT NULL DEFAULT 0,
                `is_allowed` tinyint(1) NOT NULL DEFAULT 0,
                `imported` tinyint(1) NOT NULL DEFAULT 0,
                `mdate` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_parent` (`parent_id`),
                KEY `idx_allowed` (`is_allowed`),
                KEY `idx_name` (`name`),
                KEY `idx_imported` (`imported`),
                FULLTEXT KEY `idx_name_index` (`name_index`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category_char';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category_char` (
                `id` int(11) NOT NULL,
                `cat_id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL DEFAULT '',
                `type_id` int(11) NOT NULL,
                `display_order` int(11) NOT NULL,
                `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
                `is_mandatory_for_mktp` tinyint(1) NOT NULL DEFAULT 0,
                `allow_new_value` tinyint(1) NOT NULL DEFAULT 0,
                `is_filter` tinyint(1) NOT NULL DEFAULT 0,
                `tags` varchar(512) NOT NULL,
                `value_tags` varchar(512) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`id`,`cat_id`),
                KEY `idx_name` (`name`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category_char_rel';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category_char_rel` (
                `cat_id` int(11) NOT NULL,
                `char_id` int(11) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_cat_char` (`cat_id`,`char_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category_fam';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category_fam` (
                `id` int(11) NOT NULL,
                `cat_id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL DEFAULT '',
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`id`,`cat_id`),
                KEY `idx_name` (`name`) USING BTREE
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category_fam_char';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_char` (
                `fam_id` int(11) NOT NULL,
                `cat_id` int(11) NOT NULL,
                `characteristic_id` int(11) NOT NULL,
                `characteristic_type` int(11) NOT NULL,
                `characteristic_family_type_id` int(11) NOT NULL,
                `is_foldable` tinyint(1) NOT NULL DEFAULT 0,
                `display_order` int(11) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`fam_id`,`cat_id`,`characteristic_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category_fam_rel';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category_fam_rel` (
               `cat_id` int(11) NOT NULL,
                `fam_id` int(11) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_cat_fam` (`cat_id`,`fam_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_logs';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_logs` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `user_id` int(11) NOT NULL,
                `log` text NOT NULL,
                `mdate` double NOT NULL,
                PRIMARY KEY (`id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_product_category';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_product_category` (
                `prod_id` int(11) NOT NULL DEFAULT 0,
                `later` tinyint(1) NOT NULL DEFAULT 0,
                `product_title` varchar(512) NOT NULL DEFAULT '',
                `id` int(11) NOT NULL DEFAULT 0,
                `template` int(11) NOT NULL DEFAULT 0,
                `title` varchar(512) NOT NULL DEFAULT '',
                `path` varchar(512) NOT NULL DEFAULT '',
                `example_title` varchar(512) NOT NULL DEFAULT '',
                `example_image` varchar(512) NOT NULL DEFAULT '',
                `mdate` int(11) NOT NULL DEFAULT 0,
                UNIQUE KEY `idx_unique` (`prod_id`,`template`),
                KEY `idx_later` (`later`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_product_price_override';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_product_price_override` (
                `prod_id` int(11) NOT NULL DEFAULT 0,
                `attribute_id` INT(11) NOT NULL DEFAULT 0,
                `price_override` varchar(64) NOT NULL DEFAULT '',
                `mdate` int(11) NOT NULL DEFAULT 0,
                UNIQUE KEY `prod_id` (`prod_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_push_product';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_push_product` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `prod_id` int(11) NOT NULL,
                `emag_id` INT(11) NOT NULL,
                `attribute_id` INT(11) NOT NULL DEFAULT 0,
                `cat_id` int(11) NOT NULL,
                `pushed` tinyint(1) NOT NULL DEFAULT 0,
                `skip` tinyint(1) NOT NULL DEFAULT 0,
                `commission` int(2) NOT NULL,
                `category_id` int(11) NOT NULL,
                `sale_price` varchar(64) NOT NULL DEFAULT '',
                `buy_button_rank` tinyint(3) NOT NULL DEFAULT 0,
                `number_of_offers` tinyint(3) NOT NULL DEFAULT 0,
                `stock` int(11) NOT NULL DEFAULT 0,
                `status` tinyint(1) NOT NULL DEFAULT 0,
                `recommended_price` varchar(64) NOT NULL DEFAULT '',
                `ownership` tinyint(1) NOT NULL DEFAULT 0,
                `real_ownership` tinyint(1) NOT NULL DEFAULT 0,
                `best_offer_sale_price` varchar(64) NOT NULL DEFAULT '',
                `best_offer_recommended_price` varchar(64) NOT NULL DEFAULT '',
                `brand` varchar(255) NOT NULL DEFAULT '',
                `part_number_key` varchar(128) NOT NULL DEFAULT '',
                `part_number` varchar(255) NOT NULL DEFAULT '',
                `ean` varchar(255) NOT NULL DEFAULT '',
                `name` varchar(512) NOT NULL DEFAULT '',
                `emag_name` VARCHAR(512) NOT NULL DEFAULT '',
                `url` varchar(512) NOT NULL DEFAULT '',
                `offers_check` tinyint(1) NOT NULL DEFAULT 0,
                `mdate` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_prod_id` (`prod_id`),
                KEY `idx_push` (`pushed`),
                KEY `idx_skip` (`skip`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_push_product_char';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_push_product_char` (
                `prod_id` int(11) NOT NULL,
                `emag_id` INT(11) NOT NULL,
                `attribute_id` INT(11) NOT NULL,
                `char_id` int(11) NOT NULL,
                `char_val` varchar(255) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`prod_id`,`emag_id`,`char_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_imported_orders';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_imported_orders` (
                `emag_id` int(11) NOT NULL,
                `wc_id` int(11) NOT NULL,
                `mdate` int(11) NOT NULL,
                KEY `idx_emag_id` (`emag_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_prod_id_rel';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_prod_id_rel` (
                `emag_prod_id` int(11) NOT NULL,
                `wc_prod_id` int(11) NOT NULL,
                `wc_attribute_id` int(11) NOT NULL DEFAULT 0,
                `ean` text NOT NULL DEFAULT '',
                `reference` text NOT NULL DEFAULT '',
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`emag_prod_id`,`wc_prod_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_category_map';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_category_map` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wc_cat_id` int(11) NOT NULL,
                `emag_cat_id` int(11) NOT NULL DEFAULT 0,
                `auto` tinyint(1) NOT NULL DEFAULT 0,
                `mdate` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_unique` (`wc_cat_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_push_product_skip';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_push_product_skip` (
                `emag_prod_id` int(11) NOT NULL,
                `ean` VARCHAR(255) NOT NULL DEFAULT '',
                `reference` VARCHAR(255) NOT NULL DEFAULT '',
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`emag_prod_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_push_product_commission';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_push_product_commission` (
                `emag_prod_id` int(11) NOT NULL,
                `commission` int(11) NOT NULL DEFAULT 0,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`emag_prod_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_rank_log';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_rank_log` (
                `emag_prod_id` int(11) NOT NULL,
                `ownership` tinyint(1) NOT NULL DEFAULT 0,
                `buy_button_rank` tinyint(3) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`emag_prod_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_char_map';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_char_map` (
                `char_id` int(11) NOT NULL,
                `attribute` varchar(255) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`char_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_push_product_fam';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_push_product_fam` (
                `prod_id` int(11) NOT NULL,
                `emag_id` int(11) NOT NULL,
                `attribute_id` int(11) NOT NULL,
                `fam_id` int(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `family_type_id` int(11) NOT NULL,
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`prod_id`,`emag_id`,`fam_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_fam_map';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_fam_map` (
                `fam_id` int(11) NOT NULL,
                `attribute` VARCHAR(255) NOT NULL DEFAULT '',
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`fam_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        $table_name=$wpdb->prefix.'bizzmags_emag_mktpl_send_to_emag';
        if( $wpdb->get_var( $wpdb->prepare("show tables like %s", $table_name)) != $table_name ) {
            $sql=$wpdb->prepare("CREATE TABLE `".$wpdb->prefix."bizzmags_emag_mktpl_send_to_emag` (
                `prod_id` int(11) NOT NULL,
                `sent` tinyint(1) NOT NULL DEFAULT '0',
                `mdate` int(11) NOT NULL,
                UNIQUE KEY `idx_unique` (`prod_id`)
            )
            DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
            dbDelta( $sql );
        }

        dbDelta( $this->sql );
    }
    public function runFunctionsForMultiOrSingleBlog($the_function=""){
        global $wpdb;
        if($the_function!=""){
            if ( is_multisite() ) {
                    // Get all blogs in the network and activate plugin on each one
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                    foreach ( $blog_ids as $blog_id ) {
                        switch_to_blog( $blog_id );
                        $this->$the_function();
                        restore_current_blog();
                    }
                } else {
                    $this->$the_function();
            }
        }
    }
    // Creating table whenever a new blog is created
    function on_create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        if ( is_plugin_active_for_network( 'bizzmagsmarketplace/bizzmagsmarketplace.php' ) ) {
            switch_to_blog( $blog_id );
            $this->createTables();
            restore_current_blog();
        }
    }
    // Deleting the table whenever a blog is deleted
    function on_delete_blog( $tables ) {
        global $wpdb;
        $current_blog_tables=array();
        foreach ($this->honeybadgerTables as $table) {
            $current_blog_tables[]=$wpdb->prefix.$table;
        }
        $tables=array_merge($tables,$current_blog_tables);
        return $tables;
    }
    function deleteBizzmagsmMarketplaceTables(){
        global $wpdb;
        $result=$wpdb->get_row($wpdb->prepare("select config_value from ".$wpdb->prefix."bizzmags_config where config_name='delete_tables_upon_uninstall'"));
        if(isset($result->config_value) && $result->config_value=='yes')
        {
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category_char' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category_char_rel' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category_fam' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category_fam_char' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category_fam_rel' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_config' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_logs' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_product_category' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_product_price_override' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_push_product' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_push_product_char' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_imported_orders' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_prod_id_rel' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_category_map' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_push_product_skip' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_push_product_commission' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_rank_log' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_char_map' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_push_product_fam' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_fam_map' ));
            $wpdb->query( $wpdb->prepare('DROP TABLE IF EXISTS ' . $wpdb->prefix.'bizzmags_emag_mktpl_send_to_emag' ));
        }
        delete_option('BIZZMAGSMARKETPLACE_VERSION');
    }
    function deleteTables(){
        $this->runFunctionsForMultiOrSingleBlog("deleteBizzmagsmMarketplaceTables");
    }
    function versionChanges(){
        $this->runFunctionsForMultiOrSingleBlog("doVersionChanges");
    }
    function doVersionChanges(){
        global $wpdb;
        $current_version=get_option('BIZZMAGSMARKETPLACE_VERSION');
        if (BIZZMAGSMARKETPLACE_VERSION !== $current_version){
            update_option('BIZZMAGSMARKETPLACE_VERSION',$current_version);
        }
    }
}
