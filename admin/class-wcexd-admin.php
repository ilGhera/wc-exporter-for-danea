<?php
/**
 * Pagina opzioni/ strumenti
 *
 * @author ilGhera
 *
 * @package wc-exporter-for-danea-premium/includes
 * @since 1.5.1
 */

class WCEXD_Admin {

    public function __construct() {

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'add_menu' ) );

    }


    /**
     * Enqueue scripts and styles 
     *
     * @return void
     */
    public function enqueue_scripts() {

        $screen = get_current_screen();

        if ( $screen->id === 'woocommerce_page_wc-exporter-for-danea' ) {
            
            /*css*/
            wp_enqueue_style( 'wcexd-style', WCEXD_URI . 'css/wc-exporter-for-danea.css' );
            wp_enqueue_style( 'tzcheckbox-style', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css' );
            wp_enqueue_style( 'chosen-style', WCEXD_URI . '/vendor/harvesthq/chosen/chosen.min.css' );

            /*js*/
            wp_enqueue_script( 'wcexd-admin', WCEXD_URI . 'js/wcexd-admin.js', array( 'jquery' ), '1.0' );
            wp_enqueue_script( 'tzcheckbox', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array( 'jquery' ) );
            wp_enqueue_script( 'tzcheckbox-script', WCEXD_URI . 'js/tzCheckbox/js/script.js', array( 'jquery' ) );
            wp_enqueue_script( 'chosen', WCEXD_URI . '/vendor/harvesthq/chosen/chosen.jquery.min.js' );

        }
    }


    /**
     * Add the submenu
     *
     * @return string
     */
    public function add_menu() {

        add_submenu_page( 'woocommerce', 'WED Options', 'WC Exporter for Danea', 'manage_woocommerce', 'wc-exporter-for-danea', array( $this, 'setup_options_page' ) );

    }


    /**
     * Go premium button
     *
     * @return void
     */
    public static function go_premium() {

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
     * The plugin options page 
     *
     * @return void
     */
    public function setup_options_page() {

        /* Check current user permissions */
        if ( ! current_user_can( 'manage_woocommerce' ) ) {

            wp_die( __( 'Ops, you have not permissions to do that!', 'wc-exporter-for-danea' ) );

        }

        /* Start page template */
        echo '<div class="wrap">';

            echo '<div class="wrap-left">';

                /* Check if WooCommerce is active */
                if ( ! class_exists( 'WooCommerce' ) ) {

                    echo '<div id="message" class="error">';
                        echo '<p>';
                            echo '<strong>' . __( 'WARNING! It seems like WooCommerce is not installed.', 'wc-exporter-for-danea' ) . '</strong>';
                        echo '</p>';
                    echo '</div>';

                    exit;

                }

                $this->tab_menu();

                include WCEXD_INCLUDES . 'wc-checkout-fields/templates/wcexd-checkout-template.php';
                include WCEXD_ADMIN . 'wcexd-suppliers-template.php';
                include WCEXD_ADMIN . 'wcexd-products-template.php';
                include WCEXD_ADMIN . 'wcexd-clients-template.php';
                include WCEXD_ADMIN . 'wcexd-orders-template.php';

            echo '</div>'; // wrap-left.

            echo '<div class="wrap-right">';
                echo '<iframe width="300" height="1200" scrolling="no" src="http://www.ilghera.com/images/wed-iframe.html"></iframe>';
            echo '</div>'; // wrap-right.
            echo '<div class="clear"></div>';

        echo '</div>'; // wrap.;

    }


    /**
     * The tab menu 
     *
     * @return void
     */
    public function tab_menu() {

        echo '<div id="wcexd-general">';

            /*Header*/
            echo '<h1 class="wcexd main">' . __( 'Woocommmerce Exporter per Danea', 'wc-exporter-for-danea' ) . '</h1>';

        echo '</div>';

        /* echo '<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>'; */
        echo '<h2 id="wcexd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">';
            echo '<a href="#" data-link="wcexd-impostazioni" class="nav-tab nav-tab-active" onclick="return false;">' . __( 'Settings', 'wc-exporter-for-danea' ) . '</a>';
            echo '<a href="#" data-link="wcexd-fornitori" class="nav-tab" onclick="return false;">' . __( 'Suppliers', 'wc-exporter-for-danea' ) . '</a>';
            echo '<a href="#" data-link="wcexd-prodotti" class="nav-tab" onclick="return false;">' . __( 'Products', 'wc-exporter-for-danea' ) . '</a>';
            echo '<a href="#" data-link="wcexd-clienti" class="nav-tab" onclick="return false;">' . __( 'Customers', 'wc-exporter-for-danea' ) . '</a>';
            echo '<a href="#" data-link="wcexd-ordini" class="nav-tab" onclick="return false;">'. __( 'Orders', 'wc-exporter-for-danea' ) . '</a>';
        echo '</h2>';
    }

}

new WCEXD_Admin();

