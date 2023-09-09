<?php
/**
 * Pagina opzioni/ strumenti
 *
 * @author ilGhera
 *
 * @package wc-exporter-for-danea/includes
 * @since 1.5.1
 */

/**
 * Registrzione foglio di stile
 */
function wcexd_register_style() {
	wp_enqueue_style( 'wcexd-style', WCEXD_URI . 'css/wc-exporter-for-danea.css' );
}
add_action( 'admin_init', 'wcexd_register_style' );


/**
 * Voce di menu
 */
function wcexd_add_menu() {

	$wcexd_page = add_submenu_page( 'woocommerce', 'WED Options', 'WC Exporter for Danea', 'manage_woocommerce', 'wc-exporter-for-danea', 'wcexd_options' );

	return $wcexd_page;
}
add_action( 'admin_menu', 'wcexd_add_menu' );


/**
 * txCheckBox
 */
function wcexd_add_scripts() {
	$screen = get_current_screen();
	if ( $screen->id === 'woocommerce_page_wc-exporter-for-danea' ) {
		
		/*css*/
		wp_enqueue_style( 'tzcheckbox-style', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css' );
		wp_enqueue_style( 'chosen-style', WCEXD_URI . '/vendor/harvesthq/chosen/chosen.min.css' );

		/*js*/
		wp_enqueue_script( 'wcexd-admin', WCEXD_URI . 'js/wcexd-admin.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'tzcheckbox', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array( 'jquery' ) );
		wp_enqueue_script( 'tzcheckbox-script', WCEXD_URI . 'js/tzCheckbox/js/script.js', array( 'jquery' ) );
		wp_enqueue_script( 'chosen', WCEXD_URI . '/vendor/harvesthq/chosen/chosen.jquery.min.js' );

	}
}
add_action( 'admin_enqueue_scripts', 'wcexd_add_scripts' );


/**
 * Go premium button
 */
function wcexd_go_premium() {

	$title = __( 'This is a premium functionality, click here for more information', 'wcexd' );
	$output = '<span class="wcexd label label-warning premium">';
		$output .= '<a href="https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium" target="_blank" title="' . esc_attr( $title ) . '">Premium</a>';
	$output .= '</span>';

	$allowed = array(
		'span' => array(
			'class' => [],
		),
		'a'    => array(
			'target' => [],
			'title'  => [],
			'href'   => [],
		),
	);

	echo wp_kses( $output, $allowed );

}


/**
 * Pagina opzioni
 */
function wcexd_options() {

	/*Controllo se l'utente ha i diritti d'accessso necessari*/
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( __( 'Ops, you have not permissions to do that!', 'wc-exporter-for-danea' ) );
	}

	/*Inizio template di pagina*/
	echo '<div class="wrap">';
	echo '<div class="wrap-left">';

	/*Controllo se woocommerce e' installato*/
	if ( ! class_exists( 'WooCommerce' ) ) { ?>
		<div id="message" class="error">
			<p>
				<strong><?php echo __( 'WARNING! It seems like WooCommerce is not installed.', 'wc-exporter-for-danea' ); ?></strong>
			</p>
		</div>
		<?php
		exit;
	}
	?>

	<div id="wcexd-generale">
		<?php
		/*Header*/
		echo '<h1 class="wcexd main">' . __( 'Woocommmerce Exporter per Danea', 'wc-exporter-for-danea' ) . '</h1>';
		?>
	</div>

	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	<h2 id="wcexd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="#" data-link="wcexd-impostazioni" class="nav-tab nav-tab-active" onclick="return false;"><?php echo __( 'Settings', 'wc-exporter-for-danea' ); ?></a>
		<a href="#" data-link="wcexd-fornitori" class="nav-tab" onclick="return false;"><?php echo __( 'Suppliers', 'wc-exporter-for-danea' ); ?></a>
		<a href="#" data-link="wcexd-prodotti" class="nav-tab" onclick="return false;"><?php echo __( 'Products', 'wc-exporter-for-danea' ); ?></a>
		<a href="#" data-link="wcexd-clienti" class="nav-tab premium" onclick="return false;"><?php echo __( 'Customers', 'wc-exporter-for-danea' ); ?></a>    
		<a href="#" data-link="wcexd-ordini" class="nav-tab premium" onclick="return false;"><?php echo __( 'Orders', 'wc-exporter-for-danea' ); ?></a>                                        
	</h2>

	<!-- IMPOSTAZIONI -->
    <?php include( WCEXD_INCLUDES . 'wc-checkout-fields/templates/wcexd-checkout-template.php' ); ?>

	<!-- ESPORTAZIONE ELENCO FORNITORI (WORDPRESS USERS) WOOCOMMERCE -->     
	  
	<div id="wcexd-fornitori" class="wcexd-admin">
		<?php
		/*Dichiarazione variabili*/
		$opt_users_role = 'wcexd-users-role';
		$users_field_role = 'wcexd-users-role';

		/*Leggo il dato se già esistente nel database*/
		$users_val = get_option( $opt_users_role );

		echo '<h3 class="wcexd">' . __( 'WooCommerce suppliers export', 'wc-exporter-for-danea' ) . '</h3>';
		echo '<p>' . __( 'The import of suppliers in Danea is done by using an Excel/ OpenIffice file.', 'wc-exporter-for-danea' );
		echo '<ul class="wcexd"><li>' . __( 'Choose the WordPress user role that identifies your suppliers.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'Download your suppliers list.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'Open and save the file with one of the above programs.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'In Danea, go to "Fornitori/ Utilità", choose "Importa con Excel/OpenOffice/LibreOffice" and use the file just created.', 'wc-exporter-for-danea' ) . '</li></ul></p>';
		echo '<p>' . __( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ) . '</p>';
		echo '<a href="https://www.danea.it/software/easyfatt/supporto/import-altri-software/" target="_blank">https://www.danea.it/software/easyfatt/supporto/import-altri-software/</a></p>';
		global $wp_roles;
		$roles = $wp_roles->get_names();
		?>
		  
		<!--Form Fornitori-->
		<form name="wcexd-suppliers-submit" id="wcexd-suppliers-submit" class="wcexd-form"  method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row"><?php echo __( 'User role', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<select class="wcexd wcexd-users" name="wcexd-users" form="wcexd-suppliers-submit">
							<?php
							foreach ( $roles as $key => $value ) {
								echo '<option value="' . $key . '"' . ( $key === $users_val ? ' selected="selected"' : '' ) . '> ' . __( $value, 'woocommerce' ) . '</option>';
							}
							?>
						</select>
						<p class="description"><?php echo __( 'Select the user level of your suppliers.', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
			</table>

			<?php wp_nonce_field( 'wcexd-suppliers-submit', 'wcexd-suppliers-nonce' ); ?>
			<p class="submit">
				<input type="submit" name="download_csv" class="button-primary" value="<?php _e( 'Download suppliers list (.CSV)', 'wc-exporter-for-danea' ); ?>" />
			</p>
		</form>
	</div>

	
	<!-- ESPORTAZIONE ELENCO PRODOTTI WOOCOMMERCE -->
   
	<div id="wcexd-prodotti" class="wcexd-admin">
		<?php
		echo '<h3 class="wcexd">' . __( 'WooCommerce products export', 'wc-exporter-for-danea' ) . '</h3>';
		echo '<p>' . __( 'The import of products in Danea is done by using an Excel/ OpenIffice file.', 'wc-exporter-for-danea' );
		echo '<ul class="wcexd"><li>' . __( 'Download your WooCommerce products list.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'Open and save the file with one of the above programs.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'In Danea, go to "Prodotti/ Utilità", choose "Importa con Excel/OpenOffice/LibreOffice" and use the file just created.', 'wc-exporter-for-danea' ) . '</li></ul></p>';
		echo '<p>' . __( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ) . '</p>';
		echo '<a href="https://www.danea.it/software/easyfatt/supporto/import-altri-software/" target="_blank">https://www.danea.it/software/easyfatt/supporto/import-altri-software/</a></p>';

		$size_type = get_option( 'wcexd-size-type' );
		$weight_type = get_option( 'wcexd-weight-type' );
		$wcexd_products_tax_name = get_option( 'wcexd_products_tax_name' );
		?>

		<form name="wcexd-products-submit" id="wcexd-products-submit" class="wcexd-form"  method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Suppliers', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<fieldset>
							<label for="wcexd-use-suppliers">
								<input type="checkbox" class="wcexd-use-suppliers" name="wcexd-use-suppliers" value="1" 
								<?php
								if ( get_option( 'wcexd-use-suppliers' ) == 1 ) {
									echo 'checked="checked"'; }
								?>
								>
								<?php echo __( 'Use the product author as supplier', 'wc-exporter-for-danea' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __( 'Tax name', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<label for="wcexd-products-tax-name">
							<input type="checkbox" name="wcexd-products-tax-name" value="1"<?php echo $wcexd_products_tax_name == 1 ? ' checked="checked"' : ''; ?>>
							<?php echo __( 'Export the tax name instead of the rate', 'wc-exporter-for-danea' ); ?>
						</label>
						<p class="description"><?php echo __( 'Recommended option if the tax rates were imported from Danea Easyfatt previously.', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e( 'Danea sizes and color', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<fieldset>
							<label for="wcexd-exclude-danea-vars">
								<input type="checkbox" class="wcexd-exclude-danea-vars" name="wcexd-exclude-danea-vars" value="1" 
								<?php
								if ( get_option( 'wcexd-exclude-danea-vars' ) == 1 ) {
									echo 'checked="checked"'; }
								?>
								>
								<?php echo __( 'Exclude sizes and colors', 'wc-exporter-for-danea' ); ?>
							</label>
						</fieldset>
						<p class="description"><?php echo __( 'The Danea sizes and colors variations, transferred previously in WooCommerce, cannot be imported with a file. Do you want to exclude them?', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
				<?php if ( class_exists( 'WooThemes_Sensei' ) ) { ?>
					<tr>
						<th scope="row"><?php _e( 'Sensei', 'wc-exporter-for-danea' ); ?></th>
						<td>
							<fieldset>
								<label for="sensei">
									<input type="checkbox" name="sensei" value="1" 
									<?php
									if ( get_option( 'wcexd-sensei-option' ) == 1 ) {
										echo 'checked="checked"'; }
									?>
									/>
									<?php echo __( 'If you\'re using Woothemes Sensei, you may want to link every WooCommerce product with the Teacher of the course associate.', 'wc-exporter-for-danea' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
				<?php } ?>

				<tr>
					<th scope="row"><?php echo __( 'Products measures', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<select name="wcexd-size-type" class="wcexd">
							<option value="gross-size"<?php echo( $size_type == 'gross-size' ) ? ' selected="selected"' : ''; ?>><?php echo __( 'Gross measures', 'wc-exporter-for-danea' ); ?></option>
							<option value="net-size"<?php echo( $size_type == 'net-size' ) ? ' selected="selected"' : ''; ?>><?php echo __( 'Net measures', 'wc-exporter-for-danea' ); ?></option>
						</select>
						<p class="description"><?php echo __( 'Choose if the exported values will be used in Danea as gross or net measures.', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
				<tr>
				<tr>
					<th scope="row"><?php echo __( 'Product weight', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<select name="wcexd-weight-type" class="wcexd">
							<option value="gross-weight"<?php echo( $weight_type == 'gross-weight' ) ? 'selected="selected"' : ''; ?>><?php echo __( 'Gross weight', 'wc-exporter-for-danea' ); ?></option>
							<option value="net-weight"<?php echo( $weight_type == 'net-weight' ) ? 'selected="selected"' : ''; ?>><?php echo __( 'Net weight', 'wc-exporter-for-danea' ); ?></option>
						</select>
						<p class="description"><?php echo __( 'Choose if the value exported will be used in Danea as gross or net weight.', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="hidden" name="wcexd-products-hidden" value="1" />
				<?php wp_nonce_field( 'wcexd-products-submit', 'wcexd-products-nonce' ); ?>
				<input type="submit" name="download_csv" class="button-primary" value="<?php _e( 'Download products list (.CSV)', 'wc-exporter-for-danea' ); ?>" />
			</p>
		</form>    
	</div>

	<!-- ESPORTAZIONE ELENCO CLIENTI (WORDPRESS USERS) WOOCOMMERCE -->     
	<div id="wcexd-clienti" class="wcexd-admin">
		<?php
		echo '<h3 class="wcexd">' . __( 'Export your WooCommerce customers list.', 'wc-exporter-for-danea' ) . '</h3>';
		echo '<p>' . __( 'The import of clients in Danea is done by using an Excel/ OpenIffice file.', 'wc-exporter-for-danea' );
		echo '<ul class="wcexd"><li>' . __( 'Choose the WordPress user role that identifies your customers.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'Download your WooCommerce customers list.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'Open and save the file with one of the above programs.', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'In Danea, go to "Clienti/ Utilità", choose "Importa con Excel/OpenOffice/LibreOffice" and use the file just created.', 'wc-exporter-for-danea' ) . '</li></ul></p>';
		echo '<p>' . __( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ) . '</p>';
		echo '<a href="https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm" target="_blank">https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm</a></p>';
		?>
				
		<!--Form Clienti-->
		<form name="wcexd-clients-submit" id="wcexd-clients-submit" class="wcexd-form"  method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'User role', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<select class="wcexd wcexd-clients" name="wcexd-clients" disabled="disabled" form="wcexd-clients-submit">
							<option value="customer" selected="selected"><?php echo __('Customer', 'woocommerce'); ?></option>';	
						</select>
						<p class="description"><?php echo __( 'Select the user level of your clients.', 'wc-exporter-for-danea' ); ?></p>

                        <?php wcexd_go_premium(); ?>
					</td>
				</tr>
			</table>
			<?php wp_nonce_field( 'wcexd-clients-submit', 'wcexd-clients-nonce' ); ?>
			<p class="submit">
				<input type="submit" name="download_csv" class="button-primary" disabled="disabled" value="<?php _e( 'Download customers list (.CSV)', 'wc-exporter-for-danea' ); ?>" />
			</p>
		</form>
	</div>
 
	<!-- ESPORTAZIONE ELENCO ORDINI WOOCOMMERCE -->
	<div id="wcexd-ordini" class="wcexd-admin">
		<?php
		/*Header form ordini*/
		echo '<h3 class="wcexd">' . __( 'Export your WooCommerce orders list.', 'wc-exporter-for-danea' ) . '</h3>';
		echo '<p>' . __( 'The import of orders in Danea is done by using a XML file.', 'wc-exporter-for-danea' );
		echo '<ul class="wcexd">';
		echo '<li>' . __( 'Copy the full URL of your feed with your updated WooCommerce orders list', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'In Danea, choose "Scarica ordini" from the menu "Strumenti/ E-commerce"', 'wc-exporter-for-danea' ) . '</li>';
		echo '<li>' . __( 'In the next window, paste the URL of your WooCommerce orders list in "Impostazioni/ indirizzo..."', 'wc-exporter-for-danea' ) . '</li><ul>';
		echo '<p>' . __( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ) . '</p>';
		echo '<a href="https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm" target="_blank">https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm</a></p>';
        ?>

		<form name="wcexd-orders" id="wcexd-orders" class="wcexd-form" method="post" action="">
			<table class="form-table">
				<?php $receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wc-exporter-for-danea' ); ?>
				<tr>
					<th scope="row"><?php echo __( 'Orders status', 'wc-exporter-for-danea' ); ?></th>
					<td>
				    	<select form="wcexd-orders" class="wcexd" name="wcexd-orders-status" disabled="disabled">
				    		<option name="all" value=""><?php echo __('All', 'wc-exporter-for-danea'); ?></option>
						</select>
						<p class="description"><?php echo __( 'Select the order\'s status that you want to import in Danea', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __( 'Tax name', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<label for="wcexd-orders-tax-name">
							<input type="checkbox" name="wcexd-orders-tax-name" value="1" disabled="disabled">
							<?php echo __( 'Export the tax name instead of the rate', 'wc-exporter-for-danea' ); ?>
						</label>
						<p class="description"><?php echo __( 'Recommended option if the tax rates were imported from Danea Easyfatt previously.', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __( 'Currency Exchange', 'wc-exporter-for-danea' ); ?></th>
					<td>
						<label for="wcexd-currency-exchange">
							<input type="checkbox" name="wcexd-currency-exchange" value="1" disabled="disabled">
							<?php echo __( 'Export orders in euros', 'wc-exporter-for-danea' ); ?>
						</label>
						<p class="description"><?php echo __( 'Export orders received in dollars into euros using the most recent exchange rate from the Bank of Italy.', 'wc-exporter-for-danea' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo __( 'Feed URL', 'wc-exporter-for-danea' ); ?></th>
					<td>
				        <div class="wcexd-copy-url"><span class="wcexd-red"><?php echo $receive_orders_url; ?></span></div>
						<p class="description"><?php echo __( 'Add this URL to the <b>Settings</b> tab of the function <b>Download orders</b> (Ctrl+O) in Danea.', 'wc-exporter-for-danea' ); ?></p>
                        <?php wcexd_go_premium(); ?>
					</td>
				</tr>
			</table>                      
			<p class="submit">
				<input type="hidden" name="wcexd-orders-sent" value="1">
				<input type="submit" name="Submit" class="button-primary" disabled="disabled" value="<?php esc_attr_e( 'Save options', 'wc-exporter-for-danea' ); ?>" />
			</p>	    
		</form>
	</div>

    </div><!--WRAP-LEFT-->
	
	<div class="wrap-right">
		<iframe width="300" height="1200" scrolling="no" src="http://www.ilghera.com/images/wed-iframe.html"></iframe>
	</div>
	<div class="clear"></div>
    
	</div><!--WRAP-->
	<?php
}

