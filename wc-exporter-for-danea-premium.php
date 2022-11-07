<?php
/**
 * Plugin Name: WC Exporter for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/
 * Description: If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Exporter for Danea - Premium!
 * You'll be able to export suppliers, products, clients and orders.
 * Author: ilGhera
 * Version: 1.4.8
 * Author URI: https://ilghera.com
 * Requires at least: 4.0
 * Tested up to: 6.0
 * WC tested up to: 6
 * Text Domain: wcexd
 */


/*Evito accesso diretto*/
if ( !defined( 'ABSPATH' ) ) exit;


function load_wc_exporter_for_danea_premium() {

	/*Function check */
	if ( !function_exists( 'is_plugin_active' ) ) {
    	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
 	}

 	/*Disattiva il plugin free se presente*/
	if( is_plugin_active('wc-exporter-for-danea/wc-exporter-for-danea.php') || function_exists('load_wc_exporter_for_danea') ) {
		deactivate_plugins('wc-exporter-for-danea/wc-exporter-for-danea.php');
	    remove_action( 'plugins_loaded', 'load_wc_exporter_for_danea' );
	    wp_redirect(admin_url('plugins.php?plugin_status=all&paged=1&s'));

	}

	/*Dichiarazioni costanti*/
	define('WCEXD_DIR', plugin_dir_path(__FILE__));
	define('WCEXD_URI', plugin_dir_url(__FILE__));
	define('WCEXD_INCLUDES', WCEXD_DIR . 'includes/');

	/*Database update*/
	if(get_option('wcexd-database-version') < '0.9.6') {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE " . $wpdb->prefix . "woocommerce_order_itemmeta SET
			meta_key = REPLACE(meta_key, '_wcifd_item_discount', '_wcexd_item_discount')
			"
		);

		update_option('wcexd-database-version', '0.9.6');
	}

	/*Internationalization*/
	load_plugin_textdomain('wcexd', false, basename( dirname( __FILE__ ) ) . '/languages' );

	/*Richiamo file necessari*/
	require( WCEXD_INCLUDES . 'wcexd-admin-functions.php');
	require( WCEXD_INCLUDES . 'wcexd-functions.php');
	require( WCEXD_INCLUDES . 'wcexd-suppliers-download.php');
	require( WCEXD_INCLUDES . 'wcexd-products-download.php');
	require( WCEXD_INCLUDES . 'wcexd-clients-download.php');
	require( WCEXD_INCLUDES . 'wcexd-orders.php');
	require( WCEXD_INCLUDES . 'wc-checkout-fields/class-wcexd-checkout-fields.php');

}
add_action( 'plugins_loaded', 'load_wc_exporter_for_danea_premium', 1 );	


/*Richiamo "Update-Checker"*/
require( plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php');
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$wcexdUpdateChecker = PucFactory::buildUpdateChecker(
    'https://www.ilghera.com/wp-update-server-2/?action=get_metadata&slug=wc-exporter-for-danea-premium',
    __FILE__,
    'wc-exporter-for-danea-premium'
);

$wcexdUpdateChecker->addQueryArgFilter('wcexd_secure_update_check');
function wcexd_secure_update_check($queryArgs) {
    $key = base64_encode( get_option('wcexd-premium-key') );

    if($key) {
        $queryArgs['premium-key'] = $key;
    }
    return $queryArgs;
}
