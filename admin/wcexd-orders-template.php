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

    <h3 class="wcexd"><?php _e( 'Export your WooCommerce orders list.', 'wc-exporter-for-danea' ); ?></h3>

    <p>
        <?php _e( 'The import of orders in Danea is done by using a XML file.', 'wc-exporter-for-danea' ); ?>
        <ul class="wcexd">
            <li>'<?php _e( 'Copy the full URL of your feed with your updated WooCommerce orders list', 'wc-exporter-for-danea' ); ?></li>
            <li>'<?php _e( 'In Danea, choose "Scarica ordini" from the menu "Strumenti/ E-commerce"', 'wc-exporter-for-danea' ); ?></li>
            <li>'<?php _e( 'In the next window, paste the URL of your WooCommerce orders list in "Impostazioni/ indirizzo..."', 'wc-exporter-for-danea' ); ?></li>
        <ul>
        <p><?php _e( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ); ?></p>
        <a href="https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm" target="_blank">https://help.danea.it/easyfatt/index.htm#t=Ricezione_ordini_di_acquisto.htm</a>
    </p>

    <?php
    /* Check the admin settings */
    $orders_statuses = get_option( 'wcexd-orders-statuses' ) ? get_option( 'wcexd-orders-statuses' ) : array( 'any' );

    if ( isset( $_POST['wcexd-orders-sent'] ) ) {

        $orders_statuses = isset( $_POST['wcexd-orders-statuses'] ) ? map_deep( $_POST['wcexd-orders-statuses'], 'sanitize_text_field' ) : array( 'any' );

        update_option( 'wcexd-orders-statuses', $orders_statuses );

    }

    $wcexd_orders_tax_name = get_option( 'wcexd-orders-tax-name' );

    if ( isset( $_POST['wcexd-orders-sent'] ) ) {

        $wcexd_orders_tax_name = isset( $_POST['wcexd-orders-tax-name'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-orders-tax-name'] ) ) : 0;
        update_option( 'wcexd-orders-tax-name', $wcexd_orders_tax_name );

    }

    $wcexd_currency_exchange = get_option( 'wcexd-currency-exchange' );

    if ( isset( $_POST['wcexd-orders-sent'] ) ) {

        $wcexd_currency_exchange = isset( $_POST['wcexd-currency-exchange'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-currency-exchange'] ) ) : 0;
        update_option( 'wcexd-currency-exchange', $wcexd_currency_exchange );

    }
    ?>
    
    <form name="wcexd-orders" id="wcexd-orders" class="wcexd-form" method="post" action="">
        <table class="form-table">
            <?php
            $premium_key = strtolower( get_option( 'wcexd-premium-key' ) );
            $url_code    = get_option( 'wcexd-url-code' );

            if ( ! $url_code ) {

                $url_code = wcexd_rand_md5( 6 );
                add_option( 'wcexd-url-code', $url_code );

            }

            $receive_orders_url = __( 'Please insert your <strong>Premium Key</strong>', 'wc-exporter-for-danea' );

            if ( $premium_key ) {

                $receive_orders_url = sprintf( '%1$s/%2$s%3$s', home_url(), $premium_key, $url_code );

            }
            ?>
            <tr>
                <th scope="row"><?php echo __( 'Orders status', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <select form="wcexd-orders" class="wcexd" name="wcexd-orders-statuses[]" multiple data-placeholder="<?php echo __( 'All orders', 'wc-exporter-for-danea' ); ?>">
                        <?php
                        $statuses = wc_get_order_statuses();

                        foreach ( $statuses as $key => $value ) {

                            echo '<option name="' . $key . '" value="' . $key . '"';
                            echo ( in_array( $key, $orders_statuses ) ) ? ' selected="selected">' : '>';
                            echo __( $value, 'wc-exporter-for-danea' ) . '</option>';

                        }
                        ?>
                    </select>
                    <p class="description"><?php echo __( 'Select the order\'s status that you want to import in Danea', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __( 'Tax name', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <label for="wcexd-orders-tax-name">
                        <input type="checkbox" name="wcexd-orders-tax-name" value="1"<?php echo 1 === intval( $wcexd_orders_tax_name ) ? ' checked="checked"' : ''; ?>>
                        <?php echo __( 'Export the tax name instead of the rate', 'wc-exporter-for-danea' ); ?>
                    </label>
                    <p class="description"><?php echo __( 'Recommended option if the tax rates were imported from Danea Easyfatt previously.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __( 'Currency Exchange', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <label for="wcexd-currency-exchange">
                        <input type="checkbox" name="wcexd-currency-exchange" value="1"<?php echo 1 === $wcexd_currency_exchange ? ' checked="checked"' : ''; ?>>
                        <?php echo __( 'Export orders in euros', 'wc-exporter-for-danea' ); ?>
                    </label>
                    <p class="description"><?php echo __( 'Export orders received in dollars into euros using the most recent exchange rate from the Bank of Italy.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php echo __( 'Feed URL', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <div class="wcexd-copy-url"><span<?php echo( ! $premium_key ? ' class="wcexd-red"' : null ); ?>><?php echo $receive_orders_url; ?></span></div>
                    <p class="description"><?php echo __( 'Add this URL to the <b>Settings</b> tab of the function <b>Download orders</b> (Ctrl+O) in Danea.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
        </table>                      
        <p class="submit">
            <input type="hidden" name="wcexd-orders-sent" value="1">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save options', 'wc-exporter-for-danea' ); ?>" />
        </p>	    
    </form>
</div>
