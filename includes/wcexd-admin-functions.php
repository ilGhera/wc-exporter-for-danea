<?php
/*
WOOCOMMERCE EXPORTER FOR DANEA - PREMIUM | FUNZIONI DI AMMINISTRAZIONE
*/


add_action( 'admin_init', 'wcexd_register_style' );
add_action( 'admin_menu', 'wcexd_add_menu' );

add_action( 'admin_init', 'wcexd_register_js_menu' );
add_action( 'admin_menu', 'wcexd_js_menu' );


//CREONE Wcexd STYLE
function wcexd_register_style() {
	wp_register_style( 'wcexd-style', plugins_url('css/wc-exporter-for-danea.css', 'wc-exporter-for-danea-premium/css'));
}

function wcexd_add_style() {
	wp_enqueue_style( 'wcexd-style');
}


//RICHIAMO SCRIPT JS NECESSARIO ALLA NAVIGAZIONE DEL MENU
function wcexd_register_js_menu() {
	wp_register_script('wcexd-admin-nav', plugins_url('js/wcexd-admin-nav.js', 'wc-exporter-for-danea-premium/js'), array('jquery'), '1.0', true );
}

function wcexd_js_menu() {
	wp_enqueue_script('wcexd-admin-nav');
}


//VOCE DI MENU 
function wcexd_add_menu() {
	$wcexd_page = add_submenu_page( 'woocommerce','WED Options', 'WC Exporter for Danea', 'manage_woocommerce', 'wc-exporter-for-danea', 'wcexd_options');
	
	//Richiamo lo style per Wcexd
	add_action( 'admin_print_styles-' . $wcexd_page, 'wcexd_add_style' );
	//Richiamo lo script per Wcexd
	add_action( 'admin_print_scripts-' . $wcexd_page, 'wcexd_js_menu');
	
	return $wcexd_page;
}

//PAGINA OPZIONI
function wcexd_options() {
	
	//Controllo se l'utente ha i diritti d'accessso necessari
	if ( !current_user_can( 'manage_woocommerce' ) )  {
		wp_die( __( 'Sembra che tu non abbia i permessi sufficienti per visualizzare questa pagina.', 'wcexd' ) );
	}


	//INIZIO TEMPLATE DI PAGINA
	echo '<div class="wrap">'; 
	echo '<div class="wrap-left">';
	
	//CONTROLLO SE WOOCOMMERCE E' INSTALLATO
	if ( !class_exists( 'WooCommerce' ) ) { ?>
      <!--Messaggio per l'utente-->
      <div id="message" class="error"><p><strong>
		<?php echo __('ATTENZIONE! Sembra che Woocommerce non sia installato.', 'wcexd' ); ?>
      </strong></p></div>
	<?php exit; 
	} ?>	

	<div id="wcexd-generale">
	<?php
		//HEADER
		echo "<h1 class=\"wcexd main\">" . __( 'Woocommmerce Exporter per Danea', 'wcexd' ) . "<span style=\"font-size:60%;\"> 0.9.6</span></h1>";
		

		//PLUGIN PREMIUM KEY
		$key = sanitize_text_field(get_option('wcexd-premium-key'));
		if(isset($_POST['wcexd-premium-key'])) {
		$key = sanitize_text_field($_POST['wcexd-premium-key']);
		update_option('wcexd-premium-key', $key);
		}
		echo '<form id="wcexd-options" method="post" action="">';
		echo '<label>' . __('Premium Key', 'wcexd') . '</label>';
		echo '<input type="text" class="regular-text" name="wcexd-premium-key" id="wcexd-premium-key" placeholder="' . __('Add your Premium Key', 'wcexd' ) . '" value="' . $key . '" />';
		echo '<p class="description">' . __('Incolla qui la Premium Key che hai ricevuto via mail, potrai ricevere gli ultimi aggiornamenti di <strong>Woocommerce Exporter per Danea - Premium</strong>.', 'wcexd') . '</p>';
		echo '<input type="hidden" name="done" value="1" />';
		echo '<input type="submit" class="button button-primary" value="' . __('Salva ', 'wcexd') . '" />';
		echo '</form>';
	?>
	</div>

        
    <!--LIBRERIA JQUERY-->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    
	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	  <h2 id="wcexd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="#" data-link="wcexd-fornitori" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __('Fornitori', 'wcexd'); ?></a>
        <a href="#" data-link="wcexd-prodotti" class="nav-tab" onclick="return false;"><?php echo __('Prodotti', 'wcexd'); ?></a>
        <a href="#" data-link="wcexd-clienti" class="nav-tab" onclick="return false;"><?php echo __('Clienti', 'wcexd'); ?></a>    
        <a href="#" data-link="wcexd-ordini" class="nav-tab" onclick="return false;"><?php echo __('Ordini', 'wcexd'); ?></a>                                        
	  </h2>
      
      
 <!-- ESPORTAZIONE ELENCO FORNITORI (WORDPRESS USERS) WOOCOMMERCE -->     
      
      <div id="wcexd-fornitori" class="wcexd-admin" style="display: block;">

	<?php

	  //Dichiarazione variabili
	  $opt_users_role = 'wcexd-users-role';		
	  $users_field_role = 'wcexd-users-role';
	
	  //Leggo il dato se già esistente nel database
	  $users_val = get_option( $opt_users_role );
  
		echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco fornitori Woocommerce', 'wcexd' ) . "</h3>";
		echo "<p>" . __( 'L\'importazione dei fornitori in Danea avviene attraverso l\'utilizzo di un file Excel/ OpenOffice. ', 'wcexd' );
		echo "<ul class=\"wcexd\"><li>" . __('Scegli il ruolo utente Wordpress che identifica i tuoi fornitori', 'wcexd' ) . "</li>";
		echo "<li>" . __('Scarica l\'elenco aggiornato dei tuoi fornitori', 'wcexd' ) . "</li>";
		echo "<li>" . __('Apri e salva il file con uno dei programmi sopra indicati.', 'wcexd' ) . "</li>";
		echo "<li>" . __('In Danea, vai in "Fornitori/ Utilità", scegli "Importa con Excel/OpenOffice/LibreOffice" ed utilizza il file appena creato.', 'wcexd' ) . "</li></ul></p>";
		echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
		echo "<a href=\"http://www.danea.it/software/domustudio/help/index.htm#html/importare_anagrafiche_e_fornitori.htm\" target=\"_blank\">http://www.danea.it/software/domustudio/help/index.htm#html/importare_anagrafiche_e_fornitori.htm</a></p>";
    ?>
    
    <?php 
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
						if($users_val) {
						  echo '<option value=" ' .  $users_val . ' " selected="selected"> ' . $users_val . '</option>';	
						  foreach ($roles as $key => $value) {
						      if($key != $users_val) {
						        echo '<option value=" ' .  $key . ' "> ' . $key . '</option>';
						      }
						  }
						  
						} else {
							echo '<option value="Subscriber" selected="selected">Subscriber</option>';	
							foreach ($roles as $key => $value) {
							    if($key != 'Subscriber') {
							      echo '<option value=" ' .  $key . ' "> ' . $key . '</option>';
							    }
							}
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

	  //Dichiarazione variabili
	  $opt_clients_role = 'wcexd-clients-role';		
	  $clients_field_role = 'wcexd-clients-role';
	
	  //Leggo il dato se già esistente nel database
	  $clients_val = get_option( $opt_clients_role );
  
		echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco clienti Woocommerce', 'wcexd' ) . "</h3>";
		echo "<p>" . __( 'L\'importazione dei clienti in Danea avviene attraverso l\'utilizzo di un file Excel/ OpenOffice. ', 'wcexd' );
		echo "<ul class=\"wcexd\"><li>" . __('Scegli il ruolo utente Wordpress che identifica i tuoi clienti', 'wcexd' ) . "</li>";
		echo "<li>" . __('Scarica l\'elenco aggiornato dei tuoi clienti', 'wcexd' ) . "</li>";
		echo "<li>" . __('Apri e salva il file con uno dei programmi sopra indicati.', 'wcexd' ) . "</li>";
		echo "<li>" . __('In Danea, vai in "Clienti/ Utilità", scegli "Importa con Excel/OpenOffice/LibreOffice" ed utilizza il file appena creato.', 'wcexd' ) . "</li></ul></p>";
		echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
		echo "<a href=\"http://www.danea.it/software/easyfatt/help/index.htm#html/Microsoft_Excel.htm\" target=\"_blank\">http://www.danea.it/software/easyfatt/help/index.htm#html/Microsoft_Excel.htm</a></p>";
    ?>
    
    <?php 
     global $wp_roles;
     $roles = $wp_roles->get_names();      
	?>
                
    <!--Form Fornitori-->
    <form name="wcexd-clients-submit" id="wcexd-clients-submit" class="wcexd-form"  method="post" action="">
    	<table class="form-table">
    		<tr>
    			<th scope="row"><?php _e("Ruolo utente", 'wcexd' ); ?></th>
    			<td>
					<select class="wcexd-clients" name="wcexd-clients" form="wcexd-clients-submit">
						<?php
						if($clients_val) {
							echo '<option value=" ' .  $clients_val . ' " selected="selected"> ' . $clients_val . '</option>';	
							foreach($roles as $role) {
								if($role != $clients_val) {
									echo '<option value=" ' .  $role . ' "> ' . $role . '</option>';
								}
							}
						} else {
							echo '<option value="Customer" selected="selected">Customer</option>';	
							foreach($roles as $role) {
								if($role != 'Customer') {
									echo '<option value=" ' .  $role . ' "> ' . $role . '</option>';
								}
							}
						} 
						?>
					</select>
					<p class="description"><?php echo __('Seleziona il livello utente corrispondente ai tuoi clienti', 'wcexd'); ?></p>

    			</td>

    		</tr>
    	</table>
      <?php wp_nonce_field( 'wcexd-clients-submit', 'wcexd-clients-nonce'); ?>
      <p class="submit">
	      <input type="submit" name="download_csv" class="button-primary" value="<?php _e('Download elenco clienti (.csv)', 'wcexd' ) ; ?>" />
      </p>
    </form>
 
 </div>
 
 
 

 <!-- ESPORTAZIONE ELENCO ORDINI WOOCOMMERCE -->

  
    <div id="wcexd-ordini" class="wcexd-admin">

    <?php 
	  //Dichiarazione variabili
	  $opt_orders_name = 'wcexd-orders-name';	
	  $hidden_orders_name = 'wcexd_orders_hidden';	
	  $orders_field_name = 'wcexd-orders-name';
	
	  //Leggo il dato se già esistente nel database
	  $orders_val = get_option( $opt_orders_name );
  
		  
	  //Controllo se il nome del feed è già stato inserito
	  if( isset($_POST[ $hidden_orders_name ]) && $_POST[ $hidden_orders_name ] == 'Y' ) {
		  
		  //Leggo il dato inserito dall'utente
		  $orders_val = $_POST[ $opt_orders_name ];
  
		  //Salvo il dato nel database
		  update_option( $opt_orders_name, $orders_val ); 
		  
		  //Aggiorno i permalinks
		  flush_rewrite_rules(); 					
	?>
		  
	  <!--Messaggio di conferma per l'utente-->
	  <div class="updated"><p><strong><?php _e('Le informazioni sono state salvate.', 'wcexd' ); ?></strong></p></div>


	<?php }

      //Header form ordini
	  echo "<h3 class=\"wcexd\">" . __( 'Esportazione elenco ordini Woocommerce', 'wcexd' ) . "</h3>"; 
	  echo "<p>" . __( 'L\'importazione degli ordini in Danea avviene attraverso l\'utilizzo di un file xml. ', 'wcexd' );
	  echo "<ul class=\"wcexd\"><li>" . __('Scegli un nome per il tuo file xml.', 'wcexd' ) . "</li>";
	  echo "<li>" . __('Copia l\'indirizzo completo del tuo feed con l\'elenco ordini Woocommerce aggiornato.', 'wcexd' ) . "</li>";
	  echo "<li>" . __('In Danea, scegli "Scarica ordini" dal menù "Strumenti/ E-commerce"', 'wcexd' ) . "</li>";
	  echo "<li>" . __('Nella finestra seguente, incolla l\'indirizzo del tuo elenco ordini in in "Impostazioni/ Indirizzo..."', 'wcexd' ) . "</li><ul>";
	  echo "<p>" . __('Per maggiori informazioni, visita questa pagina:', 'wcexd' ) . "</p>";
	  echo "<a href=\"http://www.danea.it/software/easyfatt/help/index.htm#Ricezione_ordini_di_acquisto.htm\" target=\"_blank\">http://www.danea.it/software/easyfatt/help/index.htm#Ricezione_ordini_di_acquisto.htm</a></p>";
	?>
   
    <?php 
    //VERIFICO LE IMPOSTAZIONI DELL'UTENTE PER IL FEED ORDINI
    $orders_status = get_option('wcexd-orders-status');
    if(isset($_POST['wcexd-orders-status'])) {
    	$orders_status = $_POST['wcexd-orders-status'];
    	update_option('wcexd-orders-status', $orders_status);
    }

    ?>
    
    <form name="wcexd-orders" id="wcexd-orders" class="wcexd-form" method="post" action="">

        <input type="hidden" name="<?php echo $hidden_orders_name; ?>" value="Y">

        <table class="form-table">
        	<tr>
		    	<th scope="row"><?php echo __('Stato ordini', 'wcexd'); ?></th>
		    	<td>
			    	<select form="wcexd-orders" name="wcexd-orders-status">
			    		<option name="all" value=""<?php echo($orders_status == null) ? ' selected="selected"' : ''; ?>><?php echo __('Tutti', 'wcexd'); ?></option>
			    		<?php
			    		$statuses = wc_get_order_statuses();
			    		foreach ($statuses as $key => $value) {
				    		echo '<option name="' . $key . '" value="' . $key . '"';
				    		echo ($orders_status == $key) ? ' selected="selected">' : '>';
				    		echo __($value, 'wcexd') . '</option>';
			    		}
			    		?>
			    	</select>
			    	<p class="description"><?php echo __('Seleziona lo stato dell\'ordine che desideri importare in Danea', 'wcexd'); ?></p>
		    	</td>
		    </tr>
	    	<tr>
		    	<th scope="row"><?php echo __("Nome Feed", 'wcexd' ); ?></th>
		    	<td>
			        <input class="wcexd-input" type="text" name="<?php echo $orders_field_name; ?>" value="<?php echo $orders_val; ?>" size="20">
			        <p class="description"><?php echo __('Scegli un nome per il feed dei tuoi ordini.', 'wcexd'); ?></p>
		    	</td>
		    </tr>
		    <tr>
		    	<th scope="row"><?php echo __("Feed URL", 'wcexd' ); ?></th>
		        <td>
			        <div class="wcexd-copy-url"><?php echo($orders_val != null) ? '<span>' . home_url() . '/' .  $orders_val . '</span>' : __('Qui apparirà l\'url completo del tuo feed', 'wcexd'); ?></div>
			        <p class="description"><?php echo($orders_val != null) ? __('Utilizza questo url per l\'importazione degli ordini in Danea.', 'wcexd') : __('Potrai utilizzare questo url per l\'importazione degli ordini in Danea', 'wcexd'); ?></p>
		        </td>
		    </tr>
    	</table>                      
        
        <p class="submit">
	        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Salva impostazioni') ?>" />
        </p>
    
    </form>
   
    </div>

    </div><!--WRAP-LEFT-->
	
	<div class="wrap-right">
		<iframe width="300" height="800" scrolling="no" src="http://www.ilghera.com/images/wed-premium-iframe.html"></iframe>
	</div>
	<div class="clear"></div>
    
  </div>
    
    <?php
    
}