<?php
/** Suppliers template
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/admin
 *
 * @since 1.6.0
 */

?>

<div id="wcexd-fornitori" class="wcexd-admin">

    <?php $users_val = get_option( 'wcexd-suppliers-role' ); ?>

    <h3 class="wcexd"><?php _e( 'WooCommerce suppliers export', 'wc-exporter-for-danea' ); ?></h3>
    <p>
        <?php _e( 'The import of suppliers in Danea is done by using an Excel/ OpenIffice file.', 'wc-exporter-for-danea' ); ?>
        <ul class="wcexd">
            <li><?php _e( 'Choose the WordPress user role that identifies your suppliers.', 'wc-exporter-for-danea' ); ?></li>
            <li><?php _e( 'Download your suppliers list.', 'wc-exporter-for-danea' ); ?></li>
            <li><?php _e( 'Open and save the file with one of the above programs.', 'wc-exporter-for-danea' ); ?></li>
            <li><?php _e( 'In Danea, go to "Fornitori/ Utilità", choose "Importa con Excel/OpenOffice/LibreOffice" and use the file just created.', 'wc-exporter-for-danea' ); ?></li>
        </ul>
        <p><?php _e( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ); ?></p>
        <a href="https://www.danea.it/software/easyfatt/supporto/import-altri-software/" target="_blank">https://www.danea.it/software/easyfatt/supporto/import-altri-software/</a>
    </p>

    <?php
    global $wp_roles;
    $roles = $wp_roles->get_names();
    ?>
      
    <!--Form Fornitori-->
    <form name="wcexd-suppliers-submit" id="wcexd-suppliers-submit" class="wcexd-form"  method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e( 'User role', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <select class="wcexd wcexd-users" name="wcexd-users" form="wcexd-suppliers-submit">
                        <?php
                        foreach ( $roles as $key => $value ) {
                            echo '<option value="' . $key . '"' . ( $key === $users_val ? ' selected="selected"' : '' ) . '> ' . __( $value, 'woocommerce' ) . '</option>';
                        }
                        ?>
                    </select>
                    <p class="description"><?php _e( 'Select the user level of your suppliers.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'wcexd-suppliers-submit', 'wcexd-suppliers-nonce' ); ?>

        <p class="submit">
            <input type="submit" name="download_csv" class="button-primary" value="<?php _e( 'Download suppliers list (.CSV)', 'wc-exporter-for-danea' ); ?>" />
        </p>
    </form>
</div>
