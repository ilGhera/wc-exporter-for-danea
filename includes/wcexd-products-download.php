<?php
/**
 * Template csv prodotti
 * @author ilGhera
 * @package wc-exporter-for-danea/includes
 * @since 1.0.0
 */

add_action('admin_init', 'wcexd_products_download');

function wcexd_products_download() {

	if(isset($_POST['wcexd-products-hidden']) && wp_verify_nonce( $_POST['wcexd-products-nonce'], 'wcexd-products-submit' )) {

		/*Inizio documento csv*/
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=wcexd-products-list.csv');
		header("Content-Transfer-Encoding: binary");

		/*Leggo il dato inserito dall'utente*/
		$use_suppliers = isset($_POST['wcexd-use-suppliers']) ? $_POST['wcexd-use-suppliers'] : 0;
		$exclude_danea_vars = isset($_POST['wcexd-exclude-danea-vars']) ? $_POST['wcexd-exclude-danea-vars'] : 0;
		$wcexd_products_tax_name = isset($_POST['wcexd-products-tax-name']) ? $_POST['wcexd-products-tax-name'] : 0;

		/*Salvo il dato nel database*/
		update_option('wcexd-use-suppliers', $use_suppliers);
		update_option('wcexd-exclude-danea-vars', $exclude_danea_vars); 
		update_option('wcexd-products-tax-name', $wcexd_products_tax_name);

		/*Pesi e misure*/
		$size_type = get_option('wcexd-size-type');
		if(isset($_POST['wcexd-size-type'])) {
			$size_type = $_POST['wcexd-size-type'];
			update_option('wcexd-size-type', $size_type);
		}

		$weight_type = get_option('wcexd-weight-type');
		if(isset($_POST['wcexd-weight-type'])) {
			$weight_type = $_POST['wcexd-weight-type'];
			update_option('wcexd-weight-type', $weight_type);
		}

		$args = array('post_type' => array('product', 'product_variation'), 'post_status'=>'publish', 'posts_per_page' => -1);

		$products = new WP_Query($args);
		if($products->have_posts()) :

			$fp = fopen('php://output', 'w');
			
			$list = array('Cod.', 'Descrizione',	'Tipologia', 'Categoria', 'Sottocategoria', 'Cod. Udm',	 
					'Cod. Iva', WCtoDanea::get_prices_col_name(1),	WCtoDanea::get_prices_col_name(2), WCtoDanea::get_prices_col_name(3), 'Formula listino 1',	
					'Formula listino 2',	'Formula listino 3',	'Note', 'Cod. a barre',	'Internet',	
					'Produttore',	'Descriz. web (Sorgente HTML)',	'E-commerce',	'Extra 1',	'Extra 2',	
					'Extra 3',	'Extra 4',	'Cod. fornitore',	'Fornitore',	'Cod. prod. forn.', 'Prezzo forn.', 
					'Note fornitura', 'Ord. a multipli di', 'Gg. ordine', 'Scorta min.', 'Ubicazione', 'Tot. q.tà caricata', 
					'Tot. q.tà scaricata', 'Q.tà giacenza', 'Q.tà impegnata', 'Q.tà disponibile', 'Q.tà in arrivo', 'Vendita media mensile	', 
					'Stima data fine magazz.', 'Stima data prossimo ordine', 'Data primo carico', 'Data ultimo carico', 'Data ultimo scarico	', 
					'Costo medio d\'acq.',	 'Ultimo costo d\'acq.',	'Prezzo medio vend.',	'Stato magazzino', 'Udm Dim.', 'Dim. netta X', 'Dim. netta Y', 'Dim. netta Z',
					'Volume netto', 'Dim. imballo X', 'Dim. imballo Y', 'Dim. imballo Z', 'Volume imballo', 'Udm peso', 'Peso netto', 'Peso lordo', 'Immagine'	);
					
			fputcsv($fp, $list);
			
			  while($products->have_posts()) : $products->the_post();
			  
				/*Richiamo il singolo "document"*/
				$product = wc_get_product( get_the_ID() );
				
				/*Se richiesto, escludo le variabili taglie/ colori generate da danea*/
				if($exclude_danea_vars) {
					if(strpos($product->get_slug(), 'danea') === 0) {
						continue;
					}
				}

				/*Escludo le variazioni di un prodotto non pubblicato*/
				if(get_post_status($product->get_parent_id()) != 'publish') {
					continue;
				}

				/*Se presente lo sku, ha la precedenza*/
				if(get_post_meta(get_the_ID(), '_sku', true)) {
					$product_id = get_post_meta(get_the_ID(), '_sku', true);			
				} else {
					$product_id = get_the_ID();
				}

				/*Recupero la categoria del prodotto*/
				if($product->get_parent_id()) {
					$product_category = WCtoDanea::get_product_category_name($product->get_parent_id());
				} else {
					$product_category = WCtoDanea::get_product_category_name($product->get_id());
				}
				
				/*Controllo la presenza di sensei*/
				$id_fornitore = null;
				if(isset($_POST['sensei']) && ( WCtoDanea::get_sensei_author($product->get_id()) != null) ) {
					$id_fornitore = WCtoDanea::get_sensei_author($product->get_id());
					//Salvo il dato nel database
					update_option( 'wcexd-sensei-option', 1 ); 
				} elseif($use_suppliers) {
					$id_fornitore = get_post_field('post_author', get_the_ID());; 
					update_option( 'wcexd-sensei-option', 0 );
				}

				/*Ottengo il nome del fornitore (post author)*/
				$denominazione = null;
				if($id_fornitore) {
					$supplier_name = get_user_meta( $id_fornitore, 'billing_first_name', true ) . ' ' . get_user_meta( $id_fornitore, 'billing_last_name', true );
					//Se presente il nome dell'azienda, modifico la denominazione per Danea
					if(get_user_meta($id_fornitore, 'billing_company', true)) {
						$denominazione = (get_user_meta($id_fornitore, 'billing_company', true));
					} else {
						$denominazione = $supplier_name;
					}					
				}
				
				/*Scorporo iva*/
				if(get_option('woocommerce_prices_include_tax') == 'yes') {
					$get_price = $product->get_price();
				} else {
					$get_price = $product->get_price()/ ( 1 + ( WCtoDanea::get_tax_rate($product->get_id())/ 100 ) );
				}
				
				/*Articolo con gestione magazzino o meno*/
				$manage_stock = get_post_meta(get_the_ID(), '_manage_stock', true);
				if($manage_stock == 'yes') {
					$product_type = (get_post_meta(get_the_ID(), 'wcifd-danea-size-color', true)) ? 'Art. con magazzino (taglie/colori)' : 'Art. con magazzino';
				} else {
					$product_type = 'Articolo';
				}

				/*Trasformo il formato del prezzo*/
				$price = round($get_price, 2);
				$prezzo = str_replace('.', ',', $price);


				/*Pesi e misure*/
				$weight_unit = get_option('woocommerce_weight_unit');
				$weight_type = get_option('wcexd-weight-type');
				$weight = get_post_meta(get_the_ID(), '_weight', true);
				$gross_weight = null;
				$net_weight = null;

				if($weight_type == 'net-weight') {
					$net_weight = $weight;
				} else {
					$gross_weight = $weight;
				}

				$size_unit = get_option('woocommerce_dimension_unit');
				$size_type = get_option('wcexd-size-type');
				$width = get_post_meta(get_the_ID(), '_width', true);
				$height = get_post_meta(get_the_ID(), '_height', true);
				$length = get_post_meta(get_the_ID(), '_length', true);

				$net_width = null;
				$net_height = null;
				$net_length = null;

				$gross_width = null;
				$gross_height = null;
				$gross_length = null;

				if($size_type == 'net-size') {
					$net_width = $width;
					$net_height = $height;
					$net_length = $length;					
				} else {
					$gross_width = $width;
					$gross_height = $height;
					$gross_length = $length;					
				}
				
				$tax_rate = $wcexd_products_tax_name == 1 ? WCtoDanea::get_tax_rate($product->get_id(), 'name') : WCtoDanea::get_tax_rate($product->get_id());
				
				$data = array($product_id, $product->get_title(), $product_type, $product_category['cat'],$product_category['sub'],'', $tax_rate, 
				$prezzo, '','','','','', WCtoDanea::get_product_notes(),'','', '',$product->get_description(),'','','','','', $id_fornitore, $denominazione,'','','','','','','','','', 
				$product->get_stock_quantity(),'','','','','','','','','','','','','',$size_unit,$net_width,$net_height,$net_length,'',$gross_width,$gross_height,$gross_length,'',$weight_unit,$net_weight,$gross_weight,'');	
				fputcsv($fp, $data);

			  endwhile;

			fclose($fp);
		endif;

		exit;
	}
}