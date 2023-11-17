<?php
/**
 * Plugin Name: WC Exporter for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need WooCommerce Exporter for Danea - Premium!
 * You'll be able to export suppliers, products, clients and orders.
 * Author: ilGhera
 * Version: 1.5.1
 * Author URI: https://ilghera.com
 * Requires at least: 4.0
 * Tested up to: 6.3
 * WC tested up to: 8
 * Text Domain: wc-exporter-for-danea 
 *
 * Package: wc-exporter-for-danea-premium
 */

/*Evito accesso diretto*/
if ( !defined( 'ABSPATH' ) ) exit;

function load_wc_exporter_for_danea_premium() {

	if ( !function_exists( 'is_plugin_active' ) ) {
    	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
 	}

    /* Deactivate the free version of the plugin */
	if( function_exists('load_wc_exporter_for_danea') ) {
		deactivate_plugins('wc-exporter-for-danea/wc-exporter-for-danea.php');
	    remove_action( 'plugins_loaded', 'load_wc_exporter_for_danea' );
	    wp_redirect(admin_url('plugins.php?plugin_status=all&paged=1&s'));
	}

    /* Constant variables */
	define('WCEXD_DIR', plugin_dir_path(__FILE__));
	define('WCEXD_URI', plugin_dir_url(__FILE__));
	define('WCEXD_ADMIN', WCEXD_DIR . 'admin/');
	define('WCEXD_INCLUDES', WCEXD_DIR . 'includes/');
    define( 'WCEXD_VERSION', '1.5.1' );


	/* Internationalization */
	load_plugin_textdomain('wc-exporter-for-danea', false, basename( dirname( __FILE__ ) ) . '/languages' );

	require( WCEXD_ADMIN . 'class-wcexd-admin.php');
	require( WCEXD_INCLUDES . 'class-wcexd-functions.php');
	require( WCEXD_INCLUDES . 'class-wcexd-currency-exchange.php');
	require( WCEXD_INCLUDES . 'class-wcexd-users-download.php');
	require( WCEXD_INCLUDES . 'class-wcexd-products-download.php');
	require( WCEXD_INCLUDES . 'class-wcexd-orders.php');
	require( WCEXD_INCLUDES . 'ilghera-notice/class-ilghera-notice.php');
	require( WCEXD_INCLUDES . 'wc-checkout-fields/class-wcexd-checkout-fields.php');

}
add_action( 'plugins_loaded', 'load_wc_exporter_for_danea_premium', 1 );	

/**
 * HPOS compatibility
 */
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );


/**
 * Plugin Update Checker
 */
require( plugin_dir_path( __FILE__ ) . 'vendor/plugin-update-checker/plugin-update-checker.php');
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

