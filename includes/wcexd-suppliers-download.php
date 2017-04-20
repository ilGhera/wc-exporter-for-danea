<?php
/*
WOOCOMMERCE EXPORTER FOR DANEA - PREMIUM | TEMPLATE CSV FORNITORI
*/


add_action('admin_init', 'wcexd_suppliers_download');

function wcexd_suppliers_download() {

	if($_POST['wcexd-users'] && wp_verify_nonce( $_POST['wcexd-suppliers-nonce'], 'wcexd-suppliers-submit' )) {
	
		//INIZIO DOCUMENTO CSV
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=wcexd-suppliers-list.csv');
		header("Content-Transfer-Encoding: binary");


		//Leggo il dato inserito dall'utente
		$users_val = $_POST['wcexd-users'];

		//Salvo il dato nel database
		update_option( 'wcexd-users-role', $users_val ); 

		//Recupero i nomi dei campi C.Fiscale e P.IVA
		$get_cf_name = WCtoDanea::get_italian_tax_fields_names('cf_name');
		$get_pi_name = WCtoDanea::get_italian_tax_fields_names('pi_name');
		  
		$args = array('role' => $users_val );

		$suppliers = get_users($args);
				
		$fp = fopen('php://output', 'w');
		
		$list = array('Cod.', 'Denominazione',	'Indirizzo', 'Cap', 'Città', 'Prov.', 'Regione', 'Nazione', 'Referente',	'Tel.', 'Cell', 'Fax',	 
						'e-mail',	'Pec',	'Codice fiscale', 'Partita Iva',	'Sconti', 'Listino', 'Fido',	'Pagamento', 'Banca',	'Ns Banca', 'Data Mandato SDD', 
						'Emissione SDD', 'Rit. acconto?', 'Doc via e-mail?', 'Fatt. con Iva', 'Conto reg.', 'Resp. trasporto', 'Porto', 'Avviso nuovi doc.', 
						'Note doc.', 'Home page', 'Login web', 'Extra 1', 'Extra 2', 'Extra 3', 'Extra 4', 'Extra 5', 'Extra 6', 'Note'	);
				
		fputcsv($fp, $list);

		foreach($suppliers as $supplier) {
			
			$supplier_name = get_user_meta( $supplier->ID, 'billing_first_name', true ) . ' ' . get_user_meta( $supplier->ID, 'billing_last_name', true );
			//Se presente il nome dell'azienda, modifico la denominazione per Danea
			if(get_user_meta($supplier->ID, 'billing_company', true)) {
				$denominazione = (get_user_meta($supplier->ID, 'billing_company', true));
			} elseif($supplier_name != ' ') {
				$denominazione = $supplier_name;
			} else {
				$denominazione = $supplier->display_name;
			}

			//SE ATTIVO UNO DEI PLUGIN, RECUPRO CF E P.IVA DEL SINGOLO UTENTE
  			$cf_value = ($get_cf_name) ? get_user_meta($supplier->ID, $get_cf_name, true) : '';
			$pi_value = ($get_pi_name) ? get_user_meta($supplier->ID, $get_pi_name, true) : '';


			$data = array($supplier->ID, $denominazione, get_user_meta($supplier->ID, 'billing_address_1', true), get_user_meta($supplier->ID, 'billing_postcode', true),get_user_meta($supplier->ID, 'billing_city', true), get_user_meta($supplier->ID, 'billing_state', true), 
			'', get_user_meta($supplier->ID, 'billing_country', true), $supplier_name,'', get_user_meta($supplier->ID, 'billing_phone', true),'', $supplier->user_email,'', $cf_value, $pi_value,'','','','','','','','','','','','','','','','','','','','','','','','','');

			fputcsv($fp, $data);
		}

		fclose($fp);

		//FINE DOCUMENTO CSV

		exit;

	}

}