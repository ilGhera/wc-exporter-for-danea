<?php
/*
WOOCOMMERCE EXPORTER FOR DANEA - PREMIUM | TEMPLATE CSV CLIENTI
*/


add_action('admin_init', 'wcexd_clients_download');

function wcexd_clients_download() {

	if($_POST['wcexd-clients'] && wp_verify_nonce( $_POST['wcexd-clients-nonce'], 'wcexd-clients-submit' )) {

		//INIZIO DOCUMENTO CSV
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=wcexd-clients-list.csv');
		header("Content-Transfer-Encoding: binary");


		//Leggo il dato inserito dall'utente
		$clients_val = $_POST['wcexd-clients'];

		//Salvo il dato nel database
		update_option( 'wcexd-clients-role', $clients_val ); 

		//Recupero i nomi dei campi C.Fiscale e P.IVA
		$get_cf_name = WCtoDanea::get_italian_tax_fields_names('cf_name');
		$get_pi_name = WCtoDanea::get_italian_tax_fields_names('pi_name');
		  
		$args = array('role' => $clients_val );

		$clients = get_users($args);

			$fp = fopen('php://output', 'w');
			
			$list = array('Cod.', 'Denominazione',	'Indirizzo', 'Cap', 'Città', 'Prov.', 'Regione', 'Nazione', 'Referente',	'Tel.', 'Cell', 'Fax',	 
							'e-mail',	'Pec',	'Codice fiscale', 'Partita Iva',	'Sconti', 'Listino', 'Fido',	'Pagamento', 'Banca',	'Ns Banca', 'Data Mandato SDD', 
							'Emissione SDD', 'Rit. acconto?', 'Doc via e-mail?', 'Fatt. con Iva', 'Conto reg.', 'Resp. trasporto', 'Porto', 'Avviso nuovi doc.', 
							'Note doc.', 'Home page', 'Login web', 'Extra 1', 'Extra 2', 'Extra 3', 'Extra 4', 'Extra 5', 'Extra 6', 'Note'	);
					
			fputcsv($fp, $list);
			
			foreach($clients as $client) {	

				$client_name = get_user_meta( $client->ID, 'billing_first_name', true ) . ' ' . get_user_meta( $client->ID, 'billing_last_name', true );

				//Se presente il nome dell'azienda, modifico la denominazione per Danea
				if(get_user_meta($client->ID, 'billing_company', true)) {
					$denominazione = (get_user_meta($client->ID, 'billing_company', true));
				} elseif($client_name != ' ') {
					$denominazione = $client_name;
				} else {
					$denominazione = $client->display_name;
				}

				//SE ATTIVO UNO DEI PLUGIN, RECUPRO CF E P.IVA DEL SINGOLO UTENTE
	  			$cf_value = ($get_cf_name) ? get_user_meta($client->ID, $get_cf_name, true) : '';
				$pi_value = ($get_pi_name) ? get_user_meta($client->ID, $get_pi_name, true) : '';


				$data = array($client->ID, $denominazione, get_user_meta( $client->ID, 'billing_address_1', true ), get_user_meta( $client->ID, 'billing_postcode', true ), get_user_meta( $client->ID, 'billing_city', true ), 
				get_user_meta( $client->ID, 'billing_state', true ),'', get_user_meta( $client->ID, 'billing_country', true ), $customer_name, get_user_meta( $client->ID, 'billing_phone', true ),get_user_meta( $client->ID, 'billing_cellphone', true ), '', $client->user_email,'', 
				$cf_value, $pi_value,'','','','','','','','','','','','','','','','','','','','','','','','','');

				fputcsv($fp, $data);
			}

			fclose($fp);//get_user_meta( $client->ID, 'billing_cnpj', true )

		//FINE DOCUMENTO CSV

		exit;

	}

}