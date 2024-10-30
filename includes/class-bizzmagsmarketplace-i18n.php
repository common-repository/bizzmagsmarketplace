<?php
/**
 * @package    BizzmagsMarketplace
 * @author     Claudiu Maftei <admin@bizzmags.ro>
 */
namespace BizzmagsMarketplace;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class BizzmagsMarketplace_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bizzmagsmarketplace',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
	
}
