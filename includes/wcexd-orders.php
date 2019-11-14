<?php
/**
 * Esportazione degli ordini
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 * @version 1.2.1
 */

/*Creazione feed per prodotti e ordini*/
function addFeedInit() {
	$premium_key = strtolower(get_option('wcexd-premium-key'));
	$url_code = strtolower(get_option('wcexd-url-code'));
	$feed_name = $premium_key . $url_code;
	add_feed($feed_name, 'addOrdersFeed');

	/*Update permalinks*/
	global $wp_rewrite;
	$wp_rewrite->flush_rules();	
}
add_action('init', 'addFeedInit');


/*Callback creazione feed per ordini*/
function addOrdersFeed() { 
	header("Content-Type: application/rss+xml; charset=UTF-8");
	header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<EasyfattDocuments AppVersion=\"2\">\n"; 
	echo "<Documents>";

	$orders = WCtoDanea::get_orders();

	if ( is_array( $orders ) && ! empty( $orders ) ) {

		$include_tax = get_option('woocommerce_prices_include_tax');
			
		foreach($orders as $order) { 
			if($order->post_status != 'trash') {
				
				/*Richiamo il singolo "document"*/
				require( 'wcexd-single-order.php');
			}
		}
		
	}
	echo "\n</Documents>\n";
	echo"</EasyfattDocuments>";
}