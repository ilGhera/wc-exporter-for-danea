<?php
/** Products template
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/admin
 *
 * @since 1.6.0
 */

?>

<div id="wcexd-prodotti" class="wcexd-admin">

    <h3 class="wcexd"><?php _e( 'WooCommerce products export', 'wc-exporter-for-danea' ); ?></h3>
    <p>
        <?php _e( 'The import of products in Danea is done by using an Excel/ OpenIffice file.', 'wc-exporter-for-danea' ); ?>
        <ul class="wcexd">
            <li><?php _e( 'Download your WooCommerce products list.', 'wc-exporter-for-danea' ); ?></li>
            <li><?php _e( 'Open and save the file with one of the above programs.', 'wc-exporter-for-danea' ); ?></li>
            <li><?php _e( 'In Danea, go to "Prodotti/ Utilità", choose "Importa con Excel/OpenOffice/LibreOffice" and use the file just created.', 'wc-exporter-for-danea' ); ?></li>
        </ul>
    </p>
    <p><?php _e( 'Need more information? Please, visit this page:', 'wc-exporter-for-danea' ); ?></p>
    <a href="https://www.danea.it/software/easyfatt/supporto/import-altri-software/" target="_blank">https://www.danea.it/software/easyfatt/supporto/import-altri-software/</a></p>

    <?php
    $size_type               = get_option( 'wcexd-size-type' );
    $weight_type             = get_option( 'wcexd-weight-type' );
    $wcexd_products_tax_name = get_option( 'wcexd-products-tax-name' );
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
                            if ( 1 === intval( get_option( 'wcexd-use-suppliers' ) ) ) {
                                echo 'checked="checked"'; }
                            ?>
                            >
                            <?php _e( 'Use the product author as supplier', 'wc-exporter-for-danea' ); ?>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Tax name', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <label for="wcexd-products-tax-name">
                        <input type="checkbox" name="wcexd-products-tax-name" value="1"<?php echo 1 === intval( $wcexd_products_tax_name ) ? ' checked="checked"' : ''; ?>>
                        <?php _e( 'Export the tax name instead of the rate', 'wc-exporter-for-danea' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Recommended option if the tax rates were imported from Danea Easyfatt previously.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e( 'Danea sizes and color', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <fieldset>
                        <label for="wcexd-exclude-danea-vars">
                            <input type="checkbox" class="wcexd-exclude-danea-vars" name="wcexd-exclude-danea-vars" value="1" 
                            <?php
                            if ( 1 === intval( get_option( 'wcexd-exclude-danea-vars' ) ) ) {
                                echo 'checked="checked"'; }
                            ?>
                            >
                            <?php _e( 'Exclude sizes and colors', 'wc-exporter-for-danea' ); ?>
                        </label>
                    </fieldset>
                    <p class="description"><?php _e( 'The Danea sizes and colors variations, transferred previously in WooCommerce, cannot be imported with a file. Do you want to exclude them?', 'wc-exporter-for-danea' ); ?></p>
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
                                if ( 1 === intval( get_option( 'wcexd-sensei-option' ) ) ) {
                                    echo 'checked="checked"'; }
                                ?>
                                />
                                <?php _e( 'If you\'re using Woothemes Sensei, you may want to link every WooCommerce product with the Teacher of the course associate.', 'wc-exporter-for-danea' ); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            <?php } ?>

            <tr>
                <th scope="row"><?php _e( 'Products measures', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <select name="wcexd-size-type" class="wcexd">
                        <option value="gross-size"<?php echo( 'gross-size' === $size_type ) ? ' selected="selected"' : ''; ?>><?php _e( 'Gross measures', 'wc-exporter-for-danea' ); ?></option>
                        <option value="net-size"<?php echo( 'net-size' === $size_type ) ? ' selected="selected"' : ''; ?>><?php _e( 'Net measures', 'wc-exporter-for-danea' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Choose if the exported values will be used in Danea as gross or net measures.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
            <tr>
            <tr>
                <th scope="row"><?php _e( 'Product weight', 'wc-exporter-for-danea' ); ?></th>
                <td>
                    <select name="wcexd-weight-type" class="wcexd">
                        <option value="gross-weight"<?php echo( 'gross-weight' === $weight_type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Gross weight', 'wc-exporter-for-danea' ); ?></option>
                        <option value="net-weight"<?php echo( 'net-weight' === $weight_type ) ? 'selected="selected"' : ''; ?>><?php _e( 'Net weight', 'wc-exporter-for-danea' ); ?></option>
                    </select>
                    <p class="description"><?php _e( 'Choose if the value exported will be used in Danea as gross or net weight.', 'wc-exporter-for-danea' ); ?></p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'wcexd-products-submit', 'wcexd-products-nonce' ); ?>

        <p class="submit">
            <input type="hidden" name="wcexd-products-hidden" value="1" />
            <input type="submit" name="download_csv" class="button-primary" value="<?php _e( 'Download products list (.CSV)', 'wc-exporter-for-danea' ); ?>" />
        </p>
    </form>    
</div>
