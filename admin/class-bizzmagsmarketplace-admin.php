<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
namespace BizzmagsMarketplace;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class BizzmagsMarketplace_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles($hook) {

		if($hook && in_array($hook,array('toplevel_page_bizzmagsmarketplace','bizzmags_page_bizzmagsmarketplace-categories','bizzmags_page_bizzmagsmarketplace-category','bizzmags_page_bizzmagsmarketplace-configuration','bizzmags_page_bizzmagsmarketplace-prices','bizzmags_page_bizzmagsmarketplace-excludes','bizzmags_page_bizzmagsmarketplace-category-map','bizzmags_page_bizzmagsmarketplace-characteristics-map','bizzmags_page_bizzmagsmarketplace-family-map','bizzmags_page_bizzmagsmarketplace-logs','bizzmags_page_bizzmagsmarketplace-help')))
		{
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bizzmagsmarketplace-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name."-bootstrap", plugin_dir_url( __FILE__ ) . 'css/grid.css', array(), $this->version, 'all' );
		}

	}

	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bizzmagsmarketplace-admin.js', array( 'jquery' ), $this->version, false );

	}

}
