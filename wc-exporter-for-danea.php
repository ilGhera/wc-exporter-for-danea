<?php
/**
 * Plugin Name: WC Exporter for Danea
 * Plugin URI: https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/
 * Description: If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need WooCommerce Exporter for Danea!
 * You'll be able to export suppliers, products, clients and orders.
 * Author: ilGhera
 * Version: 1.5.1
 * Author URI: https://ilghera.com
 * Requires at least: 4.0
 * Tested up to: 6.3
 * WC tested up to: 8
 * Text Domain: wc-exporter-for-danea 
 *
 * Package: wc-exporter-for-danea
 */


/*Evito accesso diretto*/
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Attivazione plugin
 */
function load_wc_exporter_for_danea() {

	/*Dichiarazioni costanti*/
	define('WCEXD_DIR', plugin_dir_path(__FILE__));
	define('WCEXD_URI', plugin_dir_url(__FILE__));
	define('WCEXD_INCLUDES', WCEXD_DIR . 'includes/');

	/*Internationalization*/
	load_plugin_textdomain('wc-exporter-for-danea', false, basename( dirname( __FILE__ ) ) . '/languages' );

	/*Richiamo file necessari*/
	include( WCEXD_INCLUDES . 'wcexd-admin-functions.php');
	include( WCEXD_INCLUDES . 'wcexd-functions.php');
	include( WCEXD_INCLUDES . 'wcexd-suppliers-download.php');
	include( WCEXD_INCLUDES . 'wcexd-products-download.php');
	require( WCEXD_INCLUDES . 'wc-checkout-fields/class-wcexd-checkout-fields.php');

}
add_action( 'plugins_loaded', 'load_wc_exporter_for_danea', 100 );	

