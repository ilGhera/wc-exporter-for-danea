<?php
/** Clients template
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/admin
 *
 * @since 1.6.0
 */

?>

<div id="wcexd-clienti" class="wcexd-admin">

	<h3 class="wcexd"><?php esc_html_e( 'Export your WooCommerce customers list.', 'wc-exporter-for-danea' ); ?></h3>
	<p>
		<?php esc_html_e( 'The import of clients in Danea is done by using an Excel/ OpenIffice file.', 'wc-exporter-for-danea' ); ?>
		<ul class="wcexd">
			<li><?php esc_html_e( 'Choose the WordPress user role that identifies your customers.', 'wc-exporter-for-danea' ); ?></li>
			<li><?php esc_html_e( 'Download your WooCommerce customers list.', 'wc-exporter-for-danea' ); ?></li>
			<li><?php esc_html_e( 'Open and save the file with one of the above programs.', 'wc-exporter-for-danea' ); ?></li>
			<li><?php esc_html_e( 'In Danea, go to "Clienti/ Utilità", choose "Importa con Excel/OpenOffice/LibreOffice" and use the file just created.', 'wc-exporter-for-danea' ); ?></li>
		</ul>
		<p><?php esc_html_e( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ); ?></p>
		<a href="https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm" target="_blank">https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm</a>
	</p>

	<?php
	global $wp_roles;
	$roles = $wp_roles->get_names();
	?>

	<!-- Clients form -->
	<form name="wcexd-clients-submit" id="wcexd-clients-submit" class="wcexd-form"  method="post" action="">
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'User role', 'wc-exporter-for-danea' ); ?></th>
				<td>
					<select class="wcexd wcexd-users" name="wcexd-users" form="wcexd-clients-submit">
						<?php
						foreach ( $roles as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '"> ' . esc_attr__( $value, 'woocommerce' ) . '</option>';
						}
						?>
					</select>
					<p class="description"><?php esc_html_e( 'Select the user level of your clients.', 'wc-exporter-for-danea' ); ?></p>
					<?php WCEXD_Admin::go_premium(); ?>
				</td>

			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="download_csv" class="button-primary" disabled="disabled" value="<?php esc_html_e( 'Download customers list (.CSV)', 'wc-exporter-for-danea' ); ?>" />
		</p>
	</form>

</div>
