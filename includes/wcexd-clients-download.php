<?php
/**
 * Template csv clienti
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 * @since 1.1.2
 */


add_action('admin_init', 'wcexd_clients_download');

function wcexd_clients_download() {

	if(isset($_POST['wcexd-clients']) && wp_verify_nonce( $_POST['wcexd-clients-nonce'], 'wcexd-clients-submit' )) {

		/*Inizio documento csv*/
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=wcexd-clients-list.csv');
		header("Content-Transfer-Encoding: binary");


		/*Leggo il dato inserito dall'utente*/
		$clients_val = $_POST['wcexd-clients'];

		/*Salvo il dato nel database*/
		update_option( 'wcexd-clients-role', $clients_val ); 

		/*Recupero i nomi dei campi C.Fiscale e P.IVA*/
		$get_cf_name      = WCtoDanea::get_italian_tax_fields_names('cf_name');
		$get_pi_name      = WCtoDanea::get_italian_tax_fields_names('pi_name');
		$get_pec_name     = WCtoDanea::get_italian_tax_fields_names('pec_name');
		$get_pa_code_name = WCtoDanea::get_italian_tax_fields_names('pa_code_name');
		  
		$args = array('role' => $clients_val );

		$clients = get_users($args);

			$fp = fopen('php://output', 'w');
			
        $list = array(
            'Cod.',
            'Denominazione',
            'Indirizzo',
            'Cap',
            'Città',
            'Prov.',
            'Regione',
            'Nazione',
            'Referente',
            'Tel.',
            'Cell',
            'Fax',	 
            'e-mail',	
            'Pec',
            'Cod. destinatario Fatt. elettr.',
            'Codice fiscale',
            'Partita Iva',	
            'Sconti',
            'Listino',
            'Fido',	
            'Pagamento',
            'Banca',
            'Ns Banca',
            'Data Mandato SDD',
            'Emissione SDD',
            'Rit. acconto?',
            'Doc via e-mail?',
            'Fatt. con Iva',
            'Conto reg.',
            'Resp. trasporto',
            'Porto',
            'Avviso nuovi doc.',
            'Note doc.',
            'Home page',
            'Login web',
            'Extra 1',
            'Extra 2',
            'Extra 3',
            'Extra 4',
            'Extra 5',
            'Extra 6',
            'Note'
        );
					
			fputcsv($fp, $list);
			
			foreach($clients as $client) {	

				$client_name = get_user_meta( $client->ID, 'billing_first_name', true ) . ' ' . get_user_meta( $client->ID, 'billing_last_name', true );

				/*Se presente il nome dell'azienda, modifico la denominazione per Danea*/
				if(get_user_meta($client->ID, 'billing_company', true)) {
					$denominazione = (get_user_meta($client->ID, 'billing_company', true));
				} elseif($client_name != ' ') {
					$denominazione = $client_name;
				} else {
					$denominazione = $client->display_name;
				}

				/*RECUPRO CF, P.IVA E ALTRI DATI FISCALI DEL SINGOLO UTENTE*/
	  			$cf_value      = ($get_cf_name) ? get_user_meta($client->ID, $get_cf_name, true) : '';
				$pi_value      = ($get_pi_name) ? get_user_meta($client->ID, $get_pi_name, true) : '';
				$pec_value     = ($get_pec_name) ? get_user_meta($client->ID, $get_pec_name, true) : '';
				$pa_code_value = ($get_pa_code_name) ? get_user_meta($client->ID, $get_pa_code_name, true) : '';
				 


				$data = array($client->ID, $denominazione, get_user_meta( $client->ID, 'billing_address_1', true ), get_user_meta( $client->ID, 'billing_postcode', true ), get_user_meta( $client->ID, 'billing_city', true ), 
				get_user_meta( $client->ID, 'billing_state', true ),'', get_user_meta( $client->ID, 'billing_country', true ), $client_name, get_user_meta( $client->ID, 'billing_phone', true ),get_user_meta( $client->ID, 'billing_cellphone', true ), '', $client->user_email, $pec_value, $pa_code_value,
				$cf_value, $pi_value,'','','','','','','','','','','','','','','','','','','','','','','','','');

				fputcsv($fp, $data);
			}

			fclose($fp);

		exit;
	}
}
