<?php
/*
WOOCOMMERCE EXPORTER FOR DANEA - PREMIUM | FUNZIONI
*/


//EVITO ACCESSO DIRETTO
if ( !defined( 'ABSPATH' ) ) exit;


class WCtoDanea {

	//RECUPERO GLI ORDINI
	public static function get_orders() {

		$orders_status = get_option('wcexd-orders-status');
		$query_part = ($orders_status) ? "AND (t2.post_status = '" . $orders_status . "')" : "";
		global $wpdb;
		$query_str = "
						SELECT t1.*, t2.*
						FROM " . $wpdb->prefix . "woocommerce_order_items t1, $wpdb->posts t2
						WHERE
						(t1.order_id = t2.ID )
						" . $query_part . "
						GROUP BY t2.ID
						ORDER BY t2.ID
						DESC 
						LIMIT 200
					  ";
		 
		$orders = $wpdb->get_results($query_str, OBJECT);
	  
		return $orders;
		
	}
	
	//DETTAGLI SINGOLO ORDINE
	public static function order_details($order_ID, $campo) {
	
		global $wpdb;
		$query_str_details = "SELECT *
					  FROM $wpdb->postmeta
					  WHERE
					  post_id = $order_ID
					  
					";
		 $order_details = $wpdb->get_results($query_str_details);	
		 
		 foreach($order_details as $single_details) {
			if($single_details->meta_key === $campo) {
				
			  return htmlspecialchars($single_details->meta_value);
			
			}
		 }
		 
	}
	
	//RECUPERO IL VALORE DELL'IVA
	public static function get_tax_rate($product_id) {

		$tax_class = get_post_meta($product_id, '_tax_class', true);
		global $wpdb;
		$query = "
			SELECT tax_rate FROM " . $wpdb->prefix . "woocommerce_tax_rates WHERE tax_rate_class = '" . $tax_class . "'
		";

		$results = $wpdb->get_results($query, ARRAY_A);
		return intval($results[0]['tax_rate']);
	
	}
	
	//RECUPERO ITEMS PER ORDINE	
	public static function get_order_items($order_id) {
	
	  global $wpdb;
	  $query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id = $order_id AND order_item_type = 'line_item'";	
	  $items = $wpdb->get_results($query, ARRAY_A);
	  $order_items = array();
		foreach($items as $item) {
			array_push($order_items, $item);
		}
		
		return $order_items;
		
	}

	//RECUPERO IL NOME DEL METODO DI SPEDIZIONE
	public static function get_shipping_method_name($order_id) {
	
	  global $wpdb;
	  $query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id = $order_id AND order_item_type = 'shipping'";	
	  $items = $wpdb->get_results($query, ARRAY_A);
	  return $items[0]['order_item_name'];
		
	}
	
	//DETTAGLI SINGOLO ITEM ORDINE	
	public static function item_info($item, $meta_key) {
	
	  global $wpdb;
	  $query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_order_itemmeta WHERE order_item_id = $item";
	  $get_info = $wpdb->get_results($query, ARRAY_A);
		foreach($get_info as $info) {
		  if($info['meta_key'] === $meta_key) {
				return $info['meta_value'];
			}
	   }
	   
	}
	
	//OTTENGO LA CATEGORIA DI APPARTENENZA DEL PRODOTTO - NEW
	function get_product_category_name($product_id) {
		$parent = array();
		$child  = array();
		$product_cat = get_the_terms($product_id, 'product_cat');
		if( $product_cat != null ) {
			foreach ($product_cat as $cat) {
				if($cat->parent != 0) {
					$child[] = $cat->slug; 
					$get_parent = get_term_by('id', $cat->parent, 'product_cat');
				} else {
					$parent[] = $cat->slug;
				}
			}
			if($child) {
				$cat_name = array('cat' => $get_parent->slug, 'sub' => $cat->slug);	
			} else {
				$cat_name = array('cat' => $parent[0], 'sub' => $child[0]);						
			}
		} else {
			$cat_name = null;	
		}
		
		return $cat_name;
		
	}
		
	//URL IMMAGINE PRODOTTO
	public static function get_image_product() {
			
		$thumb_id = get_post_thumbnail_id();
		$thumb_url = wp_get_attachment_image_src($thumb_id, 200, true);
		return $thumb_url[0];
		
	}
	
	
	//RECUPERO L'AUTORE DEL CORSO SENSEI LEGATO AL PRODOTTO WOOCOMMERCE
	public static function get_sensei_author($product_id) {
	
		global $wpdb;
		$query_course = "SELECT post_id
						  FROM $wpdb->postmeta
						  WHERE
						  meta_key = '_course_woocommerce_product'
						  AND meta_value = $product_id
						  ";
						  
		$courses = $wpdb->get_results($query_course);
		if($courses != null) {
		  $course_id = get_object_vars($courses[0]);
		  $author = get_post_field( 'post_author', $course_id['post_id']);
		  return $author;
		}
		
	}


	//VERIFICO IL PLUGIN INSTALLATO PER RECUPERARE P.IVA E C.FISCALE
	public static function get_italian_tax_fields_names($field) {

		//WooCommerce Aggiungere CF e P.IVA
		if(class_exists('WC_BrazilianCheckoutFields')) {
			$cf_name = 'billing_cpf';
			$pi_name = 'billing_cnpj';
		} 
		//WooCommerce P.IVA e Codice Fiscale per Italia
		elseif(class_exists('WooCommerce_Piva_Cf_Invoice_Ita')) {
			$cf_name = 'billing_cf';
			$pi_name = 'billing_piva';	
		} 
		//YITH WooCommerce Checkout Manager
		elseif(function_exists('ywccp_init')) {
			$cf_name = 'billing_Codice_Fiscale';
			$pi_name = 'billing_Partita_IVA';
		} 
		//WOO Codice Fiscale
		elseif(function_exists('woocf_on_checkout')) {
			$cf_name = 'billing_CF';
			$pi_name = 'billing_iva';	
		}
		
		if($field == 'cf_name') {
			return $cf_name;
		} else {
			return $pi_name;
		}
	} 
	
	//OTTENDO IL NOME DELLE COLONNE LISTINO IN BASE ALL'INCLUSIONE O MENO DELL'IVA
	public static function get_prices_col_name($n) {
		$include_tax = get_option('woocommerce_prices_include_tax');
		if($include_tax == 'yes') {
			return 'Listino ' . $n . ' (ivato)';
		} else {
			return 'Listino ' . $n;
		}
	}

	//OTTENGO GLI ATTRIBUTI DELLA SINGOLA VARIABILE DI PRODOTTO
	public static function get_product_notes() {
		$parent = wp_get_post_parent_id(get_the_ID());
		if($parent) {
			$parent_sku = get_post_meta($parent, '_sku', true);
			$output = array('parent_id ' => $parent, 'parent_sku' => $parent_sku);
	        $attributes = get_post_meta($parent, '_product_attributes', true);
	        $var_attributes = array();
	        foreach ($attributes as $key => $value) {
	            $meta = 'attribute_' . $key;
	            $meta_val = get_post_meta(get_the_ID(), $meta, true);
	            if($meta_val) {
	            	$name = $value['name'];
		            $var_attributes[$name] = $meta_val;       	
	            }
	        }
	        $output['var_attributes'] = $var_attributes;
	        return json_encode($output);
		} else {
			$args = array(
				'post_parent' => get_the_ID(), 
				'post_type' => 'product_variation', 
				'post_status' => 'publish'
			);
			$output = array();
			if(get_children($args)) {
				$output['product_type'] = 'variable';
				$get_attributes = get_post_meta(get_the_ID(), '_product_attributes', true);
				if($get_attributes) {
					$attributes = array();
					foreach ($get_attributes as $key => $value) {
						$terms = wp_get_object_terms(get_the_ID(), $key, array('fields' =>'slugs'));
						$attributes[$key] = $terms;
					}
					$output['attributes'] = $attributes;
				}
				return json_encode($output);
			} 

		}
	}
	
} //CHIUSURA WCtoDanea


//HIDE ITEM DISCOUNT 
function wcexd_hide_item_discount($array) {
	$array[] = '_wcexd_item_discount';
	return $array;
}
add_filter( 'woocommerce_hidden_order_itemmeta', 'wcexd_hide_item_discount');


//GET PRODUCT DISCOUNT - TEMP
function wcifd_add_item_details($order_id) {		
	$order = new WC_Order($order_id);
	foreach($order->get_items() as $key => $item) {
		if($item['type'] == 'line_item') {
			if($item['variation_id'] != 0) {
				$regular_price = get_post_meta($item['variation_id'], '_regular_price', true);
				$price = get_post_meta($item['variation_id'], '_price', true);
			} else {
				$regular_price = get_post_meta($item['product_id'], '_regular_price', true);
				$price = get_post_meta($item['product_id'], '_price', true);
			}
			if($price) {
				$math = $price * 100 / $regular_price;
				$discount = number_format(100 - $math);
				wc_add_order_item_meta($key, '_wcexd_item_discount', $discount);
			}
		}
	}
}
add_action('woocommerce_thankyou', 'wcifd_add_item_details', 10, 1);


// MODIFICO IL NOME DEL LINK DI VERIFICA AGGIORNAMENTO
function wcexd_check_update() {
	return __('Verifica aggiornamenti', 'wcexd');	
}
add_filter('puc_manual_check_link-wc-exporter-for-danea-premium', 'wcexd_check_update');


//MODIFICO IL MESSAGGIO DEL RISULTATO AGGIORNAMENTO
function wcexd_update_message($message = '', $status = '') {
	
	if ( $status == 'no_update' ) {
		$message = __('E\' installata l\'ultima versione di <strong>Woocommerce Exporter for Danea - Premium</strong>', 'wcexd'); 
	} else if ( $status == 'update_available' ) {
		$message = __('E\' disponibile una nuova versione di <strong>Woocommerce Exporter for Danea - Premium</strong>', 'wcexd'); 
	} else {
		$message = __('Si è verificato un errore sconosciuto, si prega di riprovare più tardi.', 'wcexd');	
	}
	
	return $message;

}
add_filter('puc_manual_check_message-wc-exporter-for-danea-premium', 'wcexd_update_message', 10, 2);