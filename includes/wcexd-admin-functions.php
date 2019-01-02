<?php
/**
 * Pagina opzioni/ strumenti
 * @author ilGhera
<<<<<<< HEAD
 * @package wc-exporter-for-danea/includes
 * @version 1.1.2
=======
 * @package wc-exporter-for-danea-premium/includes
 * @version 1.1.3
>>>>>>> master
 */

/**
 * Registrzione foglio di stile
 */
function wcexd_register_style() {
	wp_enqueue_style( 'wcexd-style', WCEXD_URI . 'css/wc-exporter-for-danea.css');
}
add_action( 'admin_init', 'wcexd_register_style' );


/**
 * Registrzione script necessario al menu di navigazione
 */
function wcexd_register_js_menu() {
	wp_register_script('wcexd-admin-nav', WCEXD_URI . 'js/wcexd-admin-nav.js', array('jquery'), '1.0', true );
}
add_action( 'admin_init', 'wcexd_register_js_menu' );


/**
 * Richiamo script necessario al menu di navigazione
 */
function wcexd_js_menu() {
	wp_enqueue_script('wcexd-admin-nav');
}
add_action( 'admin_menu', 'wcexd_js_menu' );


/**
 * Voce di menu
 */
function wcexd_add_menu() {
	$wcexd_page = add_submenu_page( 'woocommerce','WED Options', 'WC Exporter for Danea', 'manage_woocommerce', 'wc-exporter-for-danea', 'wcexd_options');
	
	add_action( 'admin_print_scripts-' . $wcexd_page, 'wcexd_js_menu');
	
	return $wcexd_page;
}
add_action( 'admin_menu', 'wcexd_add_menu' );


/**
 * txCheckBox
 */
function wcexd_add_scripts() {
	$screen = get_current_screen();
	if($screen->id === 'woocommerce_page_wc-exporter-for-danea') {
		wp_enqueue_style('tzcheckbox-style', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css');
		wp_enqueue_script('tzcheckbox', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array('jquery'));
		wp_enqueue_script('tzcheckbox-script', WCEXD_URI . 'js/tzCheckbox/js/script.js', array('jquery'));
	}
}
add_action('admin_enqueue_scripts', 'wcexd_add_scripts');


/**
 * Pagina opzioni
 */
function wcexd_options() {
	
	/*Controllo se l'utente ha i diritti d'accessso necessari*/
	if ( !current_user_can( 'manage_woocommerce' ) )  {
		wp_die( __( 'Sembra che tu non abbia i permessi sufficienti per visualizzare questa pagina.', 'wcexd' ) );
	}


	/*Inizio template di pagina*/
	echo '<div class="wrap">'; 
	echo '<div class="wrap-left">';
	
	/*Controllo se woocommerce e' installato*/
	if ( !class_exists( 'WooCommerce' ) ) { ?>
		<div id="message" class="error">
			<p>
				<strong><?php echo __('ATTENZIONE! Sembra che Woocommerce non sia installato.', 'wcexd' ); ?></strong>
			</p>
		</div>
		<?php 
	exit; 
	} 
	?>	

	<div id="wcexd-generale">
		<?php
		/*Header*/
		echo "<h1 class=\"wcexd main\">" . __( 'Woocommmerce Exporter per Danea', 'wcexd' ) . "</h1>";
		?>
	</div>

	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	<h2 id="wcexd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="#" data-link="wcexd-impostazioni" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __('Impostazioni', 'wcexd'); ?></a>
		<a href="#" data-link="wcexd-fornitori" class="nav-tab" onclick="return false;"><?php echo __('Fornitori', 'wcexd'); ?></a>
		<a href="#" data-link="wcexd-prodotti" class="nav-tab" onclick="return false;"><?php echo __('Prodotti', 'wcexd'); ?></a>
		<a href="#" data-link="wcexd-clienti" class="nav-tab premium" onclick="return false;"><?php echo __('Clienti', 'wcexd'); ?></a>    
		<a href="#" data-link="wcexd-ordini" class="nav-tab premium" onclick="return false;"><?php echo __('Ordini', 'wcexd'); ?></a>                                        
	</h2>


	<!-- IMPOSTAZIONI -->
 	<?php
    $wcexd_company_invoice = get_option('wcexd_company_invoice');
    if(isset($_POST['wcexd-options-sent'])) {
    	$wcexd_company_invoice = isset($_POST['wcexd_company_invoice']) ? $_POST['wcexd_company_invoice'] : 0;
    	update_option('wcexd_company_invoice', $wcexd_company_invoice);
    	update_option('billing_wcexd_piva_active', $wcexd_company_invoice);
    }

    $wcexd_private_invoice = get_option('wcexd_private_invoice');
    if(isset($_POST['wcexd-options-sent'])) {
    	$wcexd_private_invoice = isset($_POST['wcexd_private_invoice']) ? $_POST['wcexd_private_invoice'] : 0;
    	update_option('wcexd_private_invoice', $wcexd_private_invoice);
    }

    /*Aggiorno cf nel db in base alle opzioni precedenti*/
    if(isset($_POST['wcexd-options-sent'])) {
    	if($wcexd_company_invoice === 0 && $wcexd_private_invoice === 0) {
	    	update_option('billing_wcexd_cf_active', 0);    		
    	} else {
	    	update_option('billing_wcexd_cf_active', 1);    		    		
    	}
	}

    $wcexd_fields_check = get_option('wcexd_fields_check');
    if(isset($_POST['wcexd-options-sent'])) {
    	$wcexd_fields_check = isset($_POST['wcexd_fields_check']) ? $_POST['wcexd_fields_check'] : 0;
    	update_option('wcexd_fields_check', $wcexd_fields_check);
    }

    $wcexd_pec_active = get_option('billing_wcexd_pec_active');
    if(isset($_POST['wcexd-options-sent'])) {
    	$wcexd_pec_active = isset($_POST['wcexd_pec_active']) ? $_POST['wcexd_pec_active'] : 0;
    	update_option('billing_wcexd_pec_active', $wcexd_pec_active);
    }

    $wcexd_pa_code_active = get_option('billing_wcexd_pa_code_active');
    if(isset($_POST['wcexd-options-sent'])) {
    	$wcexd_pa_code_active = isset($_POST['wcexd_pa_code_active']) ? $_POST['wcexd_pa_code_active'] : 0;
    	update_option('billing_wcexd_pa_code_active', $wcexd_pa_code_active);
    }
    ?>
  
    <div id="wcexd-impostazioni" class="wcexd-admin" style="display: block;">

		<h3 class="wcexd"><?php echo __( 'Pagina di checkout', 'wcexd' ); ?></h3>

		<!--Form Fornitori-->
	    <form name="wcexd-options-submit" id="wcexd-options-submit"  method="post" action="">
	    	<table class="form-table">
				<tr>
					<th scope="row"><?php echo __('Tipologie fattura', 'wcexd'); ?></th>
					<td>
						<p style="margin-bottom: 10px;">
							<label for="wcexd_company_invoice">
								<input type="checkbox" name="wcexd_company_invoice" value="1"<?php echo $wcexd_company_invoice == 1 ? ' checked="checked"' : ''; ?>>
								<?php echo __('Azienda', 'wcexd'); ?>							
							</label>							
						</p>
						<p>
							<label for="wcexd_private_invoice">
								<input type="checkbox" name="wcexd_private_invoice" value="1"<?php echo $wcexd_private_invoice == 1 ? ' checked="checked"' : ''; ?>>
								<?php echo __('Privato', 'wcexd'); ?>							
							</label>
						</p>
						<p class="description"><?php echo __('Attivando uno o più tipi di fattura, verranno visualizzati i campi P.IVA e Codice Fiscale quando necessari.', 'wcexd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Controllo campi', 'wcexd'); ?></th>
					<td>
						<label for="wcexd_fields_check">
							<input type="checkbox" name="wcexd_fields_check" value="1"<?php echo $wcexd_fields_check == 1 ? ' checked="checked"' : ''; ?>>
						</label>
						<p class="description"><?php echo __('Attiva il controllo dei Campi P.IVA e Codice Fiscale', 'wcexd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('PEC', 'wcexd'); ?></th>
					<td>
						<label for="wcexd_pec_active">
							<input type="checkbox" name="wcexd_pec_active" value="1"<?php echo $wcexd_pec_active == 1 ? ' checked="checked"' : ''; ?>>
						</label>
						<p class="description"><?php echo __('Attiva il campo PEC per la fatturazione elettronica', 'wcexd'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __('Codice ricevente', 'wcexd'); ?></th>
					<td>
						<label for="wcexd-pa-code">
							<input type="checkbox" name="wcexd_pa_code_active" value="1"<?php echo $wcexd_pa_code_active == 1 ? ' checked="checked"' : ''; ?>>
						</label>
						<p class="description"><?php echo __('Attiva il campo Codice ricevente per la fatturazione elettronica', 'wcexd'); ?></p>
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'wcexd-options-submit', 'wcexd-options-nonce'); ?>
			<p class="submit">
		        <input type="submit" name="wcexd-options-sent" class="button-primary" value="<?php esc_attr_e('Salva impostazioni', 'wcexd') ?>" />
			</p>
		</form>
    </div>

      
	<!-- ESPORTAZIONE ELENCO FORNITORI (WORDPRESS USERS) WOOCOMMERCE -->     
      
	<div id="wcexd-fornitori" class="wcexd-admin">
		<?php
		/*Dichiarazione variabili*/
		$opt_users_role = 'wcexd-users-role';		
		$users_field_role = 'wcexd-users-role';

		/*Leggo il dato se già esistente nel database*/
		$users_val = get_option( $opt_users_role );

		echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco fornitori Woocommerce', 'wcexd' ) . "</h3>";
		echo "<p>" . __( 'L\'importazione dei fornitori in Danea avviene attraverso l\'utilizzo di un file Excel/ OpenOffice. ', 'wcexd' );
		echo "<ul class=\"wcexd\"><li>" . __('Scegli il ruolo utente Wordpress che identifica i tuoi fornitori', 'wcexd' ) . "</li>";
		echo "<li>" . __('Scarica l\'elenco aggiornato dei tuoi fornitori', 'wcexd' ) . "</li>";
		echo "<li>" . __('Apri e salva il file con uno dei programmi sopra indicati.', 'wcexd' ) . "</li>";
		echo "<li>" . __('In Danea, vai in "Fornitori/ Utilità", scegli "Importa con Excel/OpenOffice/LibreOffice" ed utilizza il file appena creato.', 'wcexd' ) . "</li></ul></p>";
		echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
		echo "<a href=\"http://www.danea.it/software/domustudio/help/index.htm#html/importare_anagrafiche_e_fornitori.htm\" target=\"_blank\">http://www.danea.it/software/domustudio/help/index.htm#html/importare_anagrafiche_e_fornitori.htm</a></p>";
	    global $wp_roles;
	    $roles = $wp_roles->get_names();   
		?>
          
	    <!--Form Fornitori-->
	    <form name="wcexd-suppliers-submit" id="wcexd-suppliers-submit" class="wcexd-form"  method="post" action="">
	    	<table class="form-table">
	    		<tr>
	    			<th scope="row"><?php echo __("Ruolo utente", 'wcexd' ); ?></th>
	    			<td>
	    				<select class="wcexd-users" name="wcexd-users" form="wcexd-suppliers-submit">
							<?php
							foreach($roles as $key => $value) {
								echo '<option value="' .  $key . '"' . ($key === $users_val ? ' selected="selected"' : '') . '> ' . __($value, 'woocommerce') . '</option>';
							} 
							?>
						</select>
						<p class="description"><?php echo __('Seleziona il livello utente corrispondente ai tuoi fornitori.', 'wcexd'); ?></p>
	    			</td>
	    		</tr>
	    	</table>

			<?php wp_nonce_field( 'wcexd-suppliers-submit', 'wcexd-suppliers-nonce'); ?>
			<p class="submit">
				<input type="submit" name="download_csv" class="button-primary" value="<?php _e('Download elenco fornitori (.csv)', 'wcexd' ) ; ?>" />
			</p>
	    </form>
	</div>

    
	<!-- ESPORTAZIONE ELENCO PRODOTTI WOOCOMMERCE -->
   
    <div id="wcexd-prodotti" class="wcexd-admin">
		<?php   
		echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco prodotti Woocommerce', 'wcexd' ) . "</h3>";
		echo "<p>" . __( 'L\'importazione dei prodotti in Danea avviene attraverso l\'utilizzo di un file Excel/ OpenOffice. ', 'wcexd' );
		echo "<ul class=\"wcexd\"><li>" . __('Scarica l\'elenco aggiornato dei tuoi prodotti Woocommerce', 'wcexd' ) . "</li>";
		echo "<li>" . __('Apri e salva il file con uno dei programmi sopra indicati.', 'wcexd' ) . "</li>";
		echo "<li>" . __('In Danea, vai in "Prodotti/ Utilità", scegli "Importa con Excel/OpenOffice/LibreOffice" ed utilizza il file appena creato.', 'wcexd' ) . "</li></ul></p>";
		echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
		echo "<a href=\"http://www.danea.it/software/easyfatt/ecommerce/specifiche/ricezione_prodotti.asp\" target=\"_blank\">http://www.danea.it/software/easyfatt/ecommerce/specifiche/ricezione_prodotti.asp</a></p>";

		$size_type = get_option('wcexd-size-type');
		$weight_type = get_option('wcexd-weight-type');
		$wcexd_products_tax_name = get_option('wcexd_products_tax_name');
	    ?>

	    <form name="wcexd-products-submit" id="wcexd-products-submit" class="wcexd-form"  method="post" action="">
	    	<table class="form-table">
	    		<tr>
	    			<th scope="row"><?php _e("Fornitori", 'wcexd' ); ?></th>
	    			<td>
	    				<fieldset>
		    				<label for="wcexd-use-suppliers">
								<input type="checkbox" class="wcexd-use-suppliers" name="wcexd-use-suppliers" value="1" <?php if(get_option('wcexd-use-suppliers') == 1) { echo 'checked="checked"'; } ?>>
								<?php echo __('Utilizza l\'autore del prodotto come fornitore', 'wcexd'); ?>
		    				</label>
		    			</fieldset>
	    			</td>
	    		</tr>
				<tr>
					<th scope="row"><?php echo __('Nome imposta', 'wcexd'); ?></th>
					<td>
						<label for="wcexd-products-tax-name">
							<input type="checkbox" name="wcexd-products-tax-name" value="1"<?php echo $wcexd_products_tax_name == 1 ? ' checked="checked"' : ''; ?>>
							<?php echo __('Esporta il nome dell\'imposta e non l\'aliquota.', 'wcexd'); ?>
						</label>
						<p class="description"><?php echo __('Opzione consigliata se le aliquote sono state precedentemente importate da Danea Easyfatt.', 'wcexd'); ?></p>
					</td>
				</tr>
	    		<tr>
	    			<th scope="row"><?php _e("Danea taglie e colori", 'wcexd' ); ?></th>
	    			<td>
	    				<fieldset>
		    				<label for="wcexd-exclude-danea-vars">
								<input type="checkbox" class="wcexd-exclude-danea-vars" name="wcexd-exclude-danea-vars" value="1" <?php if(get_option('wcexd-exclude-danea-vars') == 1) { echo 'checked="checked"'; } ?>>
								<?php echo __('Escludi taglie e colori', 'wcexd'); ?>
		    				</label>
		    			</fieldset>
		    			<p class="description"><?php echo __('Le variazioni taglie/ colori create da Danea, trasferite a Woocommerce precedentemente, non possono essere importate attraverso un file. Escluderle dall\'esportazione?', 'wcexd'); ?></p>
	    			</td>
	    		</tr>
				<?php if ( class_exists( 'WooThemes_Sensei' ) ) { ?>
					<tr>
		    			<th scope="row"><?php _e("Sensei", 'wcexd' ); ?></th>
		    			<td>
		    				<fieldset>
			    				<label for="sensei">
									<input type="checkbox" name="sensei" value="1" <?php if(get_option('wcexd-sensei-option') == 1) { echo 'checked="checked"'; } ?>/>
									<?php echo __('Se utilizzi Woothemes Sensei, potresti voler abbinare ogni prodotto dello store all\'autore (Teacher) del corso ad esso associato, importandolo in Danea come fornitore.', 'wcexd' ); ?>
			    				</label>
			    			</fieldset>
		    			</td>
		    		</tr>
				<?php } ?>

				<tr>
					<th scope="row"><?php echo __('Misure prodotti', 'wcexd'); ?></th>
					<td>
						<select name="wcexd-size-type" class="wcexd">
							<option value="gross-size"<?php echo($size_type == 'gross-size') ? ' selected="selected"' : ''; ?>><?php echo __('Misure lorde', 'wcexd'); ?></option>
							<option value="net-size"<?php echo($size_type == 'net-size') ? ' selected="selected"' : ''; ?>><?php echo __('Misure nette', 'wcexd'); ?></option>
						</select>
						<p class="description"><?php echo __('Scegli se le misure esportate verranno usate in danea come lorde o nette.', 'wcexd'); ?></p>
					</td>
				</tr>
				<tr>
				<tr>
					<th scope="row"><?php echo __('Peso prodotti', 'wcexd'); ?></th>
					<td>
						<select name="wcexd-weight-type" class="wcexd">
							<option value="gross-weight"<?php echo($weight_type == 'gross-weight') ? 'selected="selected"' : ''; ?>><?php echo __('Peso lordo', 'wcexd'); ?></option>
							<option value="net-weight"<?php echo($weight_type == 'net-weight') ? 'selected="selected"' : ''; ?>><?php echo __('Peso netto', 'wcexd'); ?></option>
						</select>
						<p class="description"><?php echo __('Scegli se il peso esportato sarà usato in Danea come lordo o netto.', 'wcexd'); ?></p>
					</td>
				</tr>
	    	</table>

			<p class="submit">
				<input type="hidden" name="wcexd-products-hidden" value="1" />
				<?php wp_nonce_field( 'wcexd-products-submit', 'wcexd-products-nonce'); ?>
				<input type="submit" name="download_csv" class="button-primary" value="<?php _e('Download elenco prodotti (.csv)', 'wcexd' ) ; ?>" />
			</p>
	    </form>    
    </div>


	<!-- ESPORTAZIONE ELENCO CLIENTI (WORDPRESS USERS) WOOCOMMERCE -->     
      
    <div id="wcexd-clienti" class="wcexd-admin">
		<?php
		echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco clienti Woocommerce', 'wcexd' ) . "</h3>";
		echo "<p>" . __( 'L\'importazione dei clienti in Danea avviene attraverso l\'utilizzo di un file Excel/ OpenOffice. ', 'wcexd' );
		echo "<ul class=\"wcexd\"><li>" . __('Scegli il ruolo utente Wordpress che identifica i tuoi clienti', 'wcexd' ) . "</li>";
		echo "<li>" . __('Scarica l\'elenco aggiornato dei tuoi clienti', 'wcexd' ) . "</li>";
		echo "<li>" . __('Apri e salva il file con uno dei programmi sopra indicati.', 'wcexd' ) . "</li>";
		echo "<li>" . __('In Danea, vai in "Clienti/ Utilità", scegli "Importa con Excel/OpenOffice/LibreOffice" ed utilizza il file appena creato.', 'wcexd' ) . "</li></ul></p>";
		echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
		echo "<a href=\"http://www.danea.it/software/easyfatt/help/index.htm#html/Microsoft_Excel.htm\" target=\"_blank\">http://www.danea.it/software/easyfatt/help/index.htm#html/Microsoft_Excel.htm</a></p>";
		?>
                
	    <!--Form Clienti-->
	    <form name="wcexd-clients-submit" id="wcexd-clients-submit" class="wcexd-form"  method="post" action="">
	    	<table class="form-table">
	    		<tr>
	    			<th scope="row"><?php _e("Ruolo utente", 'wcexd' ); ?></th>
	    			<td>
						<select class="wcexd-clients" name="wcexd-clients" disabled="disabled" form="wcexd-clients-submit">
							<option value="customer" selected="selected"><?php echo __('Customer', 'woocommerce'); ?></option>';	
						</select>
						<p class="description"><?php echo __('Seleziona il livello utente corrispondente ai tuoi clienti', 'wcexd'); ?></p>
	    			</td>
	    		</tr>
	    	</table>
	        <?php wp_nonce_field( 'wcexd-clients-submit', 'wcexd-clients-nonce'); ?>
			<p class="submit">
				<input type="submit" name="download_csv" class="button-primary" disabled="disabled" value="<?php _e('Download elenco clienti (.csv)', 'wcexd' ) ; ?>" />
			</p>
		</form>
	</div>
 
 
	<!-- ESPORTAZIONE ELENCO ORDINI WOOCOMMERCE -->
 
    <div id="wcexd-ordini" class="wcexd-admin">
		<?php
		/*Header form ordini*/
		echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco ordini Woocommerce', 'wcexd' ) . "</h3>"; 
		echo "<p>" . __( 'L\'importazione degli ordini in Danea avviene attraverso l\'utilizzo di un file xml. ', 'wcexd' );
		echo "<ul class=\"wcexd\">";
		echo "<li>" . __('Copia l\'indirizzo completo del tuo feed con l\'elenco ordini Woocommerce aggiornato.', 'wcexd' ) . "</li>";
		echo "<li>" . __('In Danea, scegli "Scarica ordini" dal menù "Strumenti/ E-commerce"', 'wcexd' ) . "</li>";
		echo "<li>" . __('Nella finestra seguente, incolla l\'indirizzo del tuo elenco ordini in in "Impostazioni/ Indirizzo..."', 'wcexd' ) . "</li><ul>";
		echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
		echo "<a href=\"http://www.danea.it/software/easyfatt/help/index.htm#Ricezione_ordini_di_acquisto.htm\" target=\"_blank\">http://www.danea.it/software/easyfatt/help/index.htm#Ricezione_ordini_di_acquisto.htm</a></p>";
		?>	

	    <form name="wcexd-orders" id="wcexd-orders" class="wcexd-form" method="post" action="">
	        <table class="form-table">
	        	<?php $receive_orders_url = __('Please insert your <strong>Premium Key</strong>', 'wcexd'); ?>
	        	<tr>
			    	<th scope="row"><?php echo __('Stato ordini', 'wcexd'); ?></th>
			    	<td>
				    	<select form="wcexd-orders" name="wcexd-orders-status" disabled="disabled">
				    		<option name="all" value=""><?php echo __('Tutti', 'wcexd'); ?></option>
				    	</select>
				    	<p class="description"><?php echo __('Seleziona lo stato dell\'ordine che desideri importare in Danea', 'wcexd'); ?></p>
			    	</td>
			    </tr>
				<tr>
					<th scope="row"><?php echo __('Nome imposta', 'wcexd'); ?></th>
					<td>
						<label for="wcexd-orders-tax-name">
							<input type="checkbox" name="wcexd-orders-tax-name" value="1" disabled="disabled">
							<?php echo __('Esporta il nome dell\'imposta e non l\'aliquota.', 'wcexd'); ?>
						</label>
						<p class="description"><?php echo __('Opzione consigliata se le aliquote sono state precedentemente importate da Danea Easyfatt.', 'wcexd'); ?></p>
					</td>
				</tr>
			    <tr>
			    	<th scope="row"><?php echo __("Feed URL", 'wcexd' ); ?></th>
			        <td>
				        <div class="wcexd-copy-url"><span class="wcexd-red"><?php echo $receive_orders_url; ?></span></div>
				        <p class="description"><?php echo __('Aggiungi questo URL al tab <b>Impostazioni</b> della funzione <b>Scarica ordini</b> (Ctrl+O) di Danea.', 'wcexd'); ?></p>
			        </td>
			    </tr>
	    	</table>                      
	        <p class="submit">
	        	<input type="hidden" name="wcexd-orders-sent" value="1">
		        <input type="submit" name="Submit" class="button-primary" disabled="disabled" value="<?php esc_attr_e('Salva impostazioni', 'wcexd') ?>" />
	        </p>	    
	    </form>
    </div>

    </div><!--WRAP-LEFT-->
	
	<div class="wrap-right">
		<iframe width="300" height="900" scrolling="no" src="http://www.ilghera.com/images/wed-iframe.html"></iframe>
	</div>
	<div class="clear"></div>
    
	</div><!--WRAP-->
	<?php
}