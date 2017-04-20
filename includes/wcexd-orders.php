<?php
/*
WOOCOMMERCE EXPORTER FOR DANEA - PREMIUM | ORDERS
*/


//HOOK INIT PER CREAZIONE FEED
add_action('init', 'addFeedInit');


//HOOK CREAZIONE FEED PER PRODOTTI E ORDINI
function addFeedInit() {
	add_feed(get_option('wcexd-orders-name'), 'addOrdersFeed');
}


//FUNZIONE CREAZIONE FEED PER ORDINI
function addOrdersFeed() { 
header("Content-Type: application/rss+xml; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<EasyfattDocuments AppVersion=\"2\">\n"; 
echo "<Documents>";

	$orders = WCtoDanea::get_orders();
	$include_tax = get_option('woocommerce_prices_include_tax');
		
	foreach($orders as $order) { 
		if($order->post_status != 'trash') {
			//RICHIAMO IL SINGOLO "DOCUMENT"
			require( 'wcexd-single-order.php');
		}
	}
	
echo "\n</Documents>\n";
echo"</EasyfattDocuments>";
}