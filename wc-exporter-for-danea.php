<?php
/**
 * Plugin Name: WC Exporter for Danea - Premium
 * Plugin URI: https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need WooCommerce Exporter for Danea - Premium!
 * You'll be able to export suppliers, products, clients and orders.
 * Version: 1.6.7
 * Author: ilGhera
 * Author URI: https://ilghera.com
 * Text Domain: wc-exporter-for-danea
 * Domain Path: /languages/
 * Requires at least: 4.0
 * Tested up to: 6.7
 * WC tested up to: 9
 *
 * @package wc-exporter-for-danea-premium
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin activation
 *
 * @return void
 */
function load_wc_exporter_for_danea_premium() {

	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}

	/* Deactivate the free version of the plugin */
	if ( function_exists( 'load_wc_exporter_for_danea' ) ) {
		deactivate_plugins( 'wc-exporter-for-danea/wc-exporter-for-danea.php' );
		remove_action( 'plugins_loaded', 'load_wc_exporter_for_danea' );
		wp_safe_redirect( admin_url( 'plugins.php?plugin_status=all&paged=1&s' ) );
	}

	/* Constant variables */
	define( 'WCEXD_DIR', plugin_dir_path( __FILE__ ) );
	define( 'WCEXD_URI', plugin_dir_url( __FILE__ ) );
	define( 'WCEXD_ADMIN', WCEXD_DIR . 'admin/' );
	define( 'WCEXD_INCLUDES', WCEXD_DIR . 'includes/' );
	define( 'WCEXD_VERSION', '1.6.7' );

	require WCEXD_ADMIN . 'class-wcexd-admin.php';
	require WCEXD_INCLUDES . 'class-wcexd-functions.php';
	require WCEXD_INCLUDES . 'class-wcexd-currency-exchange.php';
	require WCEXD_INCLUDES . 'class-wcexd-users-download.php';
	require WCEXD_INCLUDES . 'class-wcexd-products-download.php';
	require WCEXD_INCLUDES . 'class-wcexd-orders.php';
	require WCEXD_INCLUDES . 'ilghera-notice/class-ilghera-notice.php';
	require WCEXD_INCLUDES . 'wc-checkout-fields/class-wcexd-checkout-fields.php';

}
add_action( 'plugins_loaded', 'load_wc_exporter_for_danea_premium', 1 );

/**
 * Load the plugin text-domain
 *
 * @return void
 */
function wcexd_load_textdomain() {

	load_plugin_textdomain( 'wc-exporter-for-danea', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'wcexd_load_textdomain' );

/**
 * HPOS compatibility
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Plugin Update Checker
 *
 * @return void
 */
require plugin_dir_path( __FILE__ ) . 'vendor/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$wcexd_update_checker = PucFactory::buildUpdateChecker(
	'https://www.ilghera.com/wp-update-server-2/?action=get_metadata&slug=wc-exporter-for-danea-premium',
	__FILE__,
	'wc-exporter-for-danea-premium'
);

$wcexd_update_checker->addQueryArgFilter( 'wcexd_secure_update_check' );

/**
 * Use the premium key with PUC
 *
 * @param array $args the query args.
 *
 * @return array
 */
function wcexd_secure_update_check( $args ) {
	$key = base64_encode( get_option( 'wcexd-premium-key' ) );

	if ( $key ) {
		$args['premium-key'] = $key;
	}
	return $args;
}

