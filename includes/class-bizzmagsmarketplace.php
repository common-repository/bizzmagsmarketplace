<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
namespace BizzmagsMarketplace;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class BizzmagsMarketplace {

	protected $loader;
	protected $plugin_name;
	protected $version;
	public function __construct() {
		if ( defined( 'BIZZMAGSMARKETPLACE_VERSION' ) ) {
			$this->version = BIZZMAGSMARKETPLACE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'bizzmagsmarketplace';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bizzmagsmarketplace-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bizzmagsmarketplace-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bizzmagsmarketplace-admin.php';
		$this->loader = new BizzmagsMarketplace_Loader();
	}

	private function set_locale() {

		$plugin_i18n = new BizzmagsMarketplace_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new BizzmagsMarketplace_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
