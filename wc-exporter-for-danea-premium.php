<?php
/**
 * Plugin Name: WC Exporter for Danea - Premium
 * Plugin URI: http://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/
 * Description: If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Exporter for Danea - Premium!
 * You'll be able to export suppliers, products, clients and orders.
 * Author: ilGhera
 * Version: 0.9.6
 * Author URI: http://ilghera.com 
 * Requires at least: 4.0
 * Tested up to: 4.7.4
 */


 //EVITO ACCESSO DIRETTO
if ( !defined( 'ABSPATH' ) ) exit;


add_action( 'plugins_loaded', 'load_wc_exporter_for_danea_premium', 1 );	

function load_wc_exporter_for_danea_premium() {

	//FUNCTION CHECK 
	if ( !function_exists( 'is_plugin_active' ) ) {
    	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
 	}

 	//OFF THE FREE ONE
	if( is_plugin_active('wc-exporter-for-danea/wc-exporter-for-danea.php') || function_exists('load_wc_exporter_for_danea') ) {
		deactivate_plugins('wc-exporter-for-danea/wc-exporter-for-danea.php');
	    remove_action( 'plugins_loaded', 'load_wc_exporter_for_danea' );
	    wp_redirect(admin_url('plugins.php?plugin_status=all&paged=1&s'));

	}

	//DATABASE UPDATE
	if(get_option('wcexd-database-version') < '0.9.6') {
		global $wpdb;
		$wpdb->query(
			"
			UPDATE " . $wpdb->prefix . "woocommerce_order_itemmeta SET
			meta_key = REPLACE(meta_key, '_wcifd_item_discount', '_wcexd_item_discount')
			"
		);

		//UPDATE DATABASE VERSION
		update_option('wcexd-database-version', '0.9.6');
	}

	//INTERNATIONALIZATION
	load_plugin_textdomain('wcexd', false, basename( dirname( __FILE__ ) ) . '/languages' );

	//RICHIAMO FILE NECESSARI
	include( plugin_dir_path( __FILE__ ) . 'includes/wcexd-admin-functions.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcexd-functions.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcexd-suppliers-download.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcexd-products-download.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcexd-clients-download.php');
	include( plugin_dir_path( __FILE__ ) . 'includes/wcexd-orders.php');

}


//RICHIAMO "UPDATE-CHECKER"
require( plugin_dir_path( __FILE__ ) . 'wcexd-update/plugin-update-checker.php');
$key = get_option('wcexd-premium-key');
$MyUpdateChecker = new PluginUpdateChecker_2_1('http://www.ilghera.com/wp-update-server/?key=' . $key, __FILE__, 'wc-exporter-for-danea-premium');