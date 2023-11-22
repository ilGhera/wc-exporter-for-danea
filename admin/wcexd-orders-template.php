<?php
/** Orders template
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/admin
 *
 * @since 1.6.0
 */

?>

<div id="wcexd-ordini" class="wcexd-admin">

	<h3 class="wcexd"><?php esc_html_e( 'Export your WooCommerce orders list.', 'wc-exporter-for-danea' ); ?></h3>

	<p>
		<?php esc_html_e( 'The import of orders in Danea is done by using a XML file.', 'wc-exporter-for-danea' ); ?>
		<ul class="wcexd">
			<li>'<?php esc_html_e( 'Copy the full URL of your feed with your updated WooCommerce orders list', 'wc-exporter-for-danea' ); ?></li>
			<li>'<?php esc_html_e( 'In Danea, choose "Scarica ordini" from the menu "Strumenti/ E-commerce"', 'wc-exporter-for-danea' ); ?></li>
			<li>'<?php esc_html_e( 'In the next window, paste the URL of your WooCommerce orders list in "Impostazioni/ indirizzo..."', 'wc-exporter-for-danea' ); ?></li>
		<ul>
		<p><?php esc_html_e( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ); ?></p>
		<a href="https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm" target="_blank">https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm</a>
	</p>

	<form name="wcexd-orders" id="wcexd-orders" class="wcexd-form" method="post" action="">
		<table class="form-table">
			<?php $receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wc-exporter-for-danea' ); ?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Orders status', 'wc-exporter-for-danea' ); ?></th>
				<td>
					<select form="wcexd-orders" class="wcexd" name="wcexd-orders-statuses[]" multiple data-placeholder="<?php esc_html_e( 'All orders', 'wc-exporter-for-danea' ); ?>">
						<?php
						$statuses = wc_get_order_statuses();

						foreach ( $statuses as $key => $value ) {

							echo '<option name="' . esc_attr( $key ) . '" value="' . esc_attr( $key ) . '"';
							esc_html_e( $value, 'wc-exporter-for-danea' ) . '</option>';

						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Select the order\'s status that you want to import in Danea', 'wc-exporter-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Tax name', 'wc-exporter-for-danea' ); ?></th>
				<td>
					<label for="wcexd-orders-tax-name">
						<input type="checkbox" name="wcexd-orders-tax-name" value="1" disabled="disabled">
						<?php esc_html_e( 'Export the tax name instead of the rate', 'wc-exporter-for-danea' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Recommended option if the tax rates were imported from Danea Easyfatt previously.', 'wc-exporter-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Currency Exchange', 'wc-exporter-for-danea' ); ?></th>
				<td>
					<label for="wcexd-currency-exchange">
						<input type="checkbox" name="wcexd-currency-exchange" value="1" disabled="disabled">
						<?php esc_html_e( 'Export orders in euros', 'wc-exporter-for-danea' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Export orders received in dollars into euros using the most recent exchange rate from the Bank of Italy.', 'wc-exporter-for-danea' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Feed URL', 'wc-exporter-for-danea' ); ?></th>
				<td>
					<div class="wcexd-copy-url"><span class="wcexd-red"><?php esc_url_e( $receive_orders_url ); ?></span></div>
					<p class="description"><?php esc_html_e( 'Add this URL to the <b>Settings</b> tab of the function <b>Download orders</b> (Ctrl+O) in Danea.', 'wc-exporter-for-danea' ); ?></p>
					<?php WCEXD_Admin::go_premium(); ?>
				</td>
			</tr>
		</table>                      
		<p class="submit">
			<input type="hidden" name="wcexd-orders-sent" value="1">
			<input type="submit" name="Submit" class="button-primary" disabled="disabled" value="<?php esc_attr_e( 'Save options', 'wc-exporter-for-danea' ); ?>" />
		</p>	    
	</form>
</div>
