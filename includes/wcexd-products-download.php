<?php
/*
WOOCOMMERCE EXPORTER FOR DANEA - PREMIUM | TEMPLATE CSV PRODOTTI
*/


add_action('admin_init', 'wcexd_products_download');

function wcexd_products_download() {

	if($_POST['wcexd-products-hidden'] && wp_verify_nonce( $_POST['wcexd-products-nonce'], 'wcexd-products-submit' )) {

		//INIZIO DOCUMENTO CSV
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=wcexd-products-list.csv');
		header("Content-Transfer-Encoding: binary");

		//Leggo il dato inserito dall'utente
		$use_suppliers = $_POST['wcexd-use-suppliers'];
		$exclude_danea_vars = $_POST['wcexd-exclude-danea-vars'];

		//Salvo il dato nel database
		update_option( 'wcexd-use-suppliers', $use_suppliers );
		update_option('wcexd-exclude-danea-vars', $exclude_danea_vars); 


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
					'Costo medio d\'acq.',	 'Ultimo costo d\'acq.',	'Prezzo medio vend.',	'Stato magazzino', 'Immagine'	);
					
			fputcsv($fp, $list);
			
			  while($products->have_posts()) : $products->the_post();
			  
				//RICHIAMO IL SINGOLO "DOCUMENT"
				$product = wc_get_product( get_the_ID() );

				
				//SE RICHIESTO, ESCLUDO LE VARIABILI TAGLIE/ COLORI GENERATE DA DANEA
				if($exclude_danea_vars) {
					if(strpos($product->slug, 'danea') === 0) {
						continue;
					}
				}

				//ESCLUDO LE VARIAZIONI DI UN PRODOTTO NON PUBBLICATO
				if(get_post_status($product->post->post_parent) != 'publish') {
					continue;
				}


				//SE PRESENTE LO SKU, HA LA PRECEDENZA
				if(get_post_meta(get_the_ID(), '_sku', true)) {
					$product_id = get_post_meta(get_the_ID(), '_sku', true);			
				} else {
					$product_id = get_the_ID();
				}

				//RECUPERO LA CATEGORIA DEL PRODOTTO
				if($product->post->post_parent) {
					$product_category = WCtoDanea::get_product_category_name($product->post->post_parent);
				} else {
					$product_category = WCtoDanea::get_product_category_name($product->id);
				}
				
				//CONTROLLO LA PRESENZA DI SENSEI
				if($_POST['sensei'] && ( WCtoDanea::get_sensei_author($product->id) != null) ) {
				  $id_fornitore = WCtoDanea::get_sensei_author($product->id);
				  //Salvo il dato nel database
				  update_option( 'wcexd-sensei-option', 1 ); 
				} elseif($use_suppliers) {
				  $id_fornitore = $product->post->post_author; 
				  update_option( 'wcexd-sensei-option', 0 );
				}

				//OTTENGO IL NOME DEL FORNITORE (POST AUTHOR)
				if($id_fornitore) {
					$supplier_name = get_user_meta( $id_fornitore, 'billing_first_name', true ) . ' ' . get_user_meta( $id_fornitore, 'billing_last_name', true );
					//Se presente il nome dell'azienda, modifico la denominazione per Danea
					if(get_user_meta($id_fornitore, 'billing_company', true)) {
						$denominazione = (get_user_meta($id_fornitore, 'billing_company', true));
					} else {
						$denominazione = $supplier_name;
					}					
				}
				
				//SCORPORO IVA
				if(get_option('woocommerce_prices_include_tax') == 'yes') {
					$get_price = $product->price;
				} else {
					$get_price = $product->price/ ( 1 + ( WCtoDanea::get_tax_rate()/ 100 ) );
				}
				
				//ARTICOLO CON GESTIONE MAGAZZINO O MENO
				$manage_stock = get_post_meta(get_the_ID(), '_manage_stock', true);
				if($manage_stock == 'yes') {
					$product_type = (get_post_meta(get_the_ID(), 'wcifd-danea-size-color', true)) ? 'Art. con magazzino (taglie/colori)' : 'Art. con magazzino';
				} else {
					$product_type = 'Articolo';
				}

				//TRASFORMO IL FORMATO DEL PREZZO
				$price = round($get_price, 2);
				$prezzo = str_replace('.', ',', $price);
				
				$data = array($product_id, $product->post->post_title, $product_type, $product_category['cat'],$product_category['sub'],'', WCtoDanea::get_tax_rate($product->id), 
				$prezzo, '','','','','', WCtoDanea::get_product_notes(),'','', '',$product->post->post_content,'','','','','', $id_fornitore, $denominazione,'','','','','','','','','', 
				$product->get_stock_quantity(),'','','','','','','','','','','','','','');	
				fputcsv($fp, $data);

			  endwhile;

			fclose($fp);
		endif;

		//FINE DOCUMENTO CSV

		exit;

	}

}