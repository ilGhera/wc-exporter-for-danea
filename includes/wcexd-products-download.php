<?php
/**
 * Products download
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 *
 * @since 1.4.8
 */
class WCEXD_Products_Download {

    /**
     * The file
     */
    public $fp;


    /**
     * The admin settings for the products download
     */
    public $use_suppliers;
    public $exclude_danea_vars;
    public $wcexd_products_tax_name;
    public $size_type;
    public $weight_type;


    /**
     * The constructor
     *
     * @return void
     */
    public function __construct() {

        add_action( 'admin_init', array( $this, 'init_action' ) );

    }


    /**
     * Get data sent by the admin
     *
     * @return void
     */
    public function init_action() {

        if ( isset( $_POST['wcexd-products-hidden'] ) && wp_verify_nonce( $_POST['wcexd-products-nonce'], 'wcexd-products-submit' ) ) {

            /* Admin options selected */
            $this->use_suppliers  = isset( $_POST['wcexd-use-suppliers'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-use-suppliers'] ) ) : 0;
            $this->exclude_danea_vars      = isset( $_POST['wcexd-exclude-danea-vars'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-exclude-danea-vars'] ) ) : 0;
            $this->wcexd_products_tax_name = isset( $_POST['wcexd-products-tax-name'] ) ?  sanitize_text_field( wp_unslash( $_POST['wcexd-products-tax-name'] ) ) : 0;
            $this->size_type               = isset( $_POST['wcexd-size-type'] ) ?  sanitize_text_field( wp_unslash( $_POST['wcexd-size-type'] ) ) : 0;
            $this->weight_type             = isset( $_POST['wcexd-weight-type'] ) ?  sanitize_text_field( wp_unslash( $_POST['wcexd-weight-type'] ) ) : 0;

            /* Save data */
            update_option( 'wcexd-use-suppliers', $this->use_suppliers );
            update_option( 'wcexd-exclude-danea-vars', $this->exclude_danea_vars );
            update_option( 'wcexd-products-tax-name', $this->wcexd_products_tax_name );
            update_option( 'wcexd-size-type', $this->size_type );
            update_option( 'wcexd-weight-type', $this->weight_type );

            /* Create file */
            $this->create_file();

        }

    }


    /**
     * Create CSV file
     *
     * @return void
     */
    public function create_file() {

		/* Start CSV */
		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: private', false );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=wcexd-products-list.csv' );
		header( 'Content-Transfer-Encoding: binary' );

        $this->fp = fopen( 'php://output', 'w' );

            $list = array(
                'Cod.',
                'Descrizione',
                'Tipologia',
                'Categoria',
                'Sottocategoria',
                'Cod. Udm',
                'Cod. Iva',
                WCtoDanea::get_prices_col_name( 1 ),
                WCtoDanea::get_prices_col_name( 2 ),
                WCtoDanea::get_prices_col_name( 3 ),
                'Formula listino 1',
                'Formula listino 2',
                'Formula listino 3',
                'Note',
                'Cod. a barre',
                'Internet',
                'Produttore',
                'Descriz. web (Sorgente HTML)',
                'E-commerce',
                'Extra 1',
                'Extra 2',
                'Extra 3',
                'Extra 4',
                'Cod. fornitore',
                'Fornitore',
                'Cod. prod. forn.',
                'Prezzo forn.',
                'Note fornitura',
                'Ord. a multipli di',
                'Gg. ordine',
                'Scorta min.',
                'Ubicazione',
                'Tot. q.tà caricata',
                'Tot. q.tà scaricata',
                'Q.tà giacenza',
                'Q.tà impegnata',
                'Q.tà disponibile',
                'Q.tà in arrivo',
                'Vendita media mensile	',
                'Stima data fine magazz.',
                'Stima data prossimo ordine',
                'Data primo carico',
                'Data ultimo carico',
                'Data ultimo scarico	',
                'Costo medio d\'acq.',
                'Ultimo costo d\'acq.',
                'Prezzo medio vend.',
                'Stato magazzino',
                'Udm Dim.',
                'Dim. netta X',
                'Dim. netta Y',
                'Dim. netta Z',
                'Volume netto',
                'Dim. imballo X',
                'Dim. imballo Y',
                'Dim. imballo Z',
                'Volume imballo',
                'Udm Peso',
                'Peso netto',
                'Peso lordo',
                'Immagine',
            );

            fputcsv( $this->fp, $list);

            $this->get_products();

        fclose( $this->fp );

		exit;

    }


    /**
     * The products loop
     *
     * @return void
     */
    public function get_products() {

        $args = array(
            'status' => 'publish',
            'type'   => array( 'simple', 'variable' ),
            'limit'  => -1,
        );

        $products   = wc_get_products( $args );
        /* error_log( 'PRODUCTS: ' . print_r( $products, true ) ); */

        if ( is_array( $products ) ) {

            foreach ( $products as $product ) {

                $this->prepare_single_product_data( $product );

                $variations = $product->get_children();

                if ( $variations ) {

                    foreach ( $variations as $var_id ) {

                        $variation = wc_get_product( $var_id );

                        $this->prepare_single_product_data( $variation, true );
                        /* error_log( 'VAR: ' . print_r( $variation, true ) ); */

                    }

                }


            }

        }

    }


    /**
     * The single product data
     *
     * @param object $product      the WC product.
     * @param bool   $is_variation variatio object with true.
     *
     * @return void
     */
    public function prepare_single_product_data( $product, $is_variation = false ) {

        /* error_log( 'PRODUCT ' . $product->get_id() . ': ' . $product->get_name() ); */
        /* error_log( 'VARIATIONS: ' . print_r( $variations, true ) ); */

        /* Do not import variations of products not published */
        if ( $is_variation && 'publish' !== get_post_status( $product->get_parent_id() ) ) {

            return;

        }

        /* Exclude Danea variations if set by the admin */
        if ( $is_variation && $this->exclude_danea_vars ) {

            if ( 0 === strpos( $product->get_slug(), 'danea' ) ) {

                return;

            }
        }

        /* The product code to use in Danea */
        $product_code = $product->get_sku() ? $product->get_sku() : $product->get_id();

        /* Get the product category */
        $product_category     = WCtoDanea::get_product_category_name( $product->get_id() );
        $product_category_cat = isset( $product_category['cat'] ) ? $product_category['cat'] : null;
        $product_category_sub = isset( $product_category['sub'] ) ? $product_category['sub'] : null;

        /* Check if Sensei is active */
        $id_supplier = null;

        if ( isset( $_POST['sensei'] ) && ( ! WCtoDanea::get_sensei_author( $product->get_id() ) ) ) {

            $id_supplier = WCtoDanea::get_sensei_author( $product->get_id() );

            /* Save to the database */
            update_option( 'wcexd-sensei-option', 1 );

        } elseif ( $this->use_suppliers ) {

            $id_supplier = get_post_field( 'post_author', $product->get_id() );

            /* Save to the database */
            update_option( 'wcexd-sensei-option', 0 );

        }

        /* Supplier name (post author) */
        $supplier_name = null;

        if ( $id_supplier ) {

            $supplier_name = sprintf( '%1$s %2$s', get_user_meta( $id_supplier, 'billing_first_name', true ), get_user_meta( $id_supplier, 'billing_last_name', true ) );
            $company_name  = get_user_meta( $id_supplier, 'billing_company', true );

            /* Use the company name if exists */
            $supplier_name = $company_name ? $company_name : $supplier_name;

        }

        $regular_price     = null;
        $sale_price        = null;
        $get_regular_price = $product->get_regular_price();
        $get_sale_price    = $product->get_sale_price();

        /* Manage stock */
        if ( $product->get_manage_stock() ) {

            $product_type = $product->get_meta( 'wcifd-danea-size-color', true ) ? 'Art. con magazzino (taglie/colori)' : 'Art. con magazzino';

        } else {

            $product_type = 'Articolo';

        }

        /* Set the price format */
        $regular_price = $get_regular_price ? round( $get_regular_price, 2 ) : $get_regular_price;
        $regular_price = str_replace( '.', ',', $regular_price );

        if ( $get_sale_price ) {

            $sale_price = round( $get_sale_price, 2 );
            $sale_price = str_replace( '.', ',', $sale_price );

        }


        /* Weight and measures */
        $weight_unit       = get_option( 'woocommerce_weight_unit' );
        $this->weight_type = get_option( 'wcexd-weight-type' );
        $weight            = $product->get_weight();
        $gross_weight      = null;
        $net_weight        = null;

        if ( 'net-weight' === $this->weight_type ) {

            $net_weight = number_format( floatval( $weight ), 2, ',', '' );

        } else {

            $gross_weight = number_format( floatval( $weight ), 2, ',', '' );

        }

        $size_unit       = get_option( 'woocommerce_dimension_unit' );
        $this->size_type = get_option( 'wcexd-size-type' );
        $width           = number_format( floatval( $product->get_width() ), 2, ',', '' ); 
        $height          = number_format( floatval( $product->get_height() ), 2, ',', '' );
        $length          = number_format( floatval( $product->get_length() ), 2, ',', '' );
        $net_width       = null;
        $net_height      = null;
        $net_length      = null;
        $gross_width     = null;
        $gross_height    = null;
        $gross_length    = null;

        if ( 'net-size' === $this->size_type ) {

            $net_width  = $width;
            $net_height = $height;
            $net_length = $length;

        } else {

            $gross_width  = $width;
            $gross_height = $height;
            $gross_length = $length;

        }

        $tax_rate = 1 === intval( $this->wcexd_products_tax_name ) ? WCtoDanea::get_tax_rate( $product->get_id(), 'name' ) : WCtoDanea::get_tax_rate( $product->get_id() );
        
        $details = null;
        if ( $is_variation ) {

            $variation = new WC_Product_Variation( get_the_ID() );
            $details   = ' | ' . implode( ' - ', array_map( 'ucfirst', $variation->get_variation_attributes() ) );

        }

        $data = array(
            $product_code,
            $product->get_title() . $details,
            $product_type,
            $product_category_cat,
            $product_category_sub,
            '',
            $tax_rate,
            $regular_price,
            $sale_price,
            '',
            '',
            '',
            '',
            WCtoDanea::get_product_notes(),
            '',
            '',
            '',
            $product->get_description(),
            '',
            '',
            '',
            '',
            '',
            $id_supplier,
            $supplier_name,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',

            $product->get_stock_quantity(),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $size_unit,
            $net_width,
            $net_height,
            $net_length,
            '',
            $gross_width,
            $gross_height,
            $gross_length,
            '',
            $weight_unit,
            $net_weight,
            $gross_weight,
            '',
        );

        fputcsv( $this->fp, $data );

    }

}
new WCEXD_Products_Download();


