<?php
/**
 * Admin options page and functions
 *
 * @author ilGhera
 *
 * @package wc-exporter-for-danea-premium/admin
 * @since 1.5.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCEXD_Admin class
 */
class WCEXD_Admin {

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'in_plugin_update_message-wc-exporter-for-danea-premium/wc-exporter-for-danea.php', array( $this, 'check_update_message' ), 10, 2 );

	}


	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-exporter-for-danea' === $screen->id ) {

			/*css*/
			wp_enqueue_style( 'wcexd-style', WCEXD_URI . 'css/wc-exporter-for-danea.css', array(), WCEXD_VERSION );
			wp_enqueue_style( 'tzcheckbox-style', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.css', array(), WCEXD_VERSION );
			wp_enqueue_style( 'chosen-style', WCEXD_URI . '/vendor/harvesthq/chosen/chosen.min.css', array(), WCEXD_VERSION );

			/*js*/
			wp_enqueue_script( 'wcexd-admin', WCEXD_URI . 'js/wcexd-admin.js', array( 'jquery' ), WCEXD_VERSION, false );
			wp_enqueue_script( 'tzcheckbox', WCEXD_URI . 'js/tzCheckbox/jquery.tzCheckbox/jquery.tzCheckbox.js', array( 'jquery' ), WCEXD_VERSION, false );
			wp_enqueue_script( 'tzcheckbox-script', WCEXD_URI . 'js/tzCheckbox/js/script.js', array( 'jquery' ), WCEXD_VERSION, false );
			wp_enqueue_script( 'chosen', WCEXD_URI . '/vendor/harvesthq/chosen/chosen.jquery.min.js', array(), WCEXD_VERSION, false );

		}
	}


	/**
	 * Add the submenu
	 *
	 * @return void
	 */
	public function add_menu() {

		add_submenu_page( 'woocommerce', 'WED Options', 'WC Exporter for Danea', 'manage_woocommerce', 'wc-exporter-for-danea', array( $this, 'setup_options_page' ) );

	}


	/**
	 * The plugin options page
	 *
	 * @return void
	 */
	public function setup_options_page() {

		/* Check current user permissions */
		if ( ! current_user_can( 'manage_woocommerce' ) ) {

			wp_die( esc_html__( 'Ops, you have not permissions to do that!', 'wc-exporter-for-danea' ) );

		}

		/* Start page template */
		echo '<div class="wrap">';

			echo '<div class="wrap-left">';

				/* Check if WooCommerce is active */
		if ( ! class_exists( 'WooCommerce' ) ) {

			echo '<div id="message" class="error">';
				echo '<p>';
					echo '<strong>' . esc_html__( 'WARNING! It seems like WooCommerce is not installed.', 'wc-exporter-for-danea' ) . '</strong>';
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
				echo '<iframe width="300" height="800" scrolling="no" src="http://www.ilghera.com/images/wed-premium-iframe.html"></iframe>';
			echo '</div>'; // wrap-right.
			echo '<div class="clear"></div>';

		echo '</div>'; // wrap.

	}


	/**
	 * The tab menu
	 *
	 * @return void
	 */
	public function tab_menu() {

		echo '<div id="wcexd-general">';

			/* Header */
			echo '<h1 class="wcexd main">' . esc_html__( 'Woocommmerce Exporter per Danea', 'wc-exporter-for-danea' ) . '</h1>';

            /* The premium key form */
            $this->premium_key_form();

		echo '</div>';

		echo '<h2 id="wcexd-admin-menu" class="nav-tab-wrapper woo-nav-tab-wrapper">';
			echo '<a href="#" data-link="wcexd-impostazioni" class="nav-tab nav-tab-active" onclick="return false;">' . esc_html__( 'Settings', 'wc-exporter-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcexd-fornitori" class="nav-tab" onclick="return false;">' . esc_html__( 'Suppliers', 'wc-exporter-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcexd-prodotti" class="nav-tab" onclick="return false;">' . esc_html__( 'Products', 'wc-exporter-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcexd-clienti" class="nav-tab" onclick="return false;">' . esc_html__( 'Customers', 'wc-exporter-for-danea' ) . '</a>';
			echo '<a href="#" data-link="wcexd-ordini" class="nav-tab" onclick="return false;">' . esc_html__( 'Orders', 'wc-exporter-for-danea' ) . '</a>';
		echo '</h2>';
	}


    /**
     * The premium key form
     *
     * @return void
     */
    public function premium_key_form() {

        /*Plugin premium key*/
        $key = sanitize_text_field( get_option( 'wcexd-premium-key' ) );

		if ( isset( $_POST['wcexd-premium-key'], $_POST['wcexd-premium-key-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcexd-premium-key-nonce'] ) ), 'wcexd-premium-key' ) ) {
			$key = sanitize_text_field( wp_unslash( $_POST['wcexd-premium-key'] ) );
			update_option( 'wcexd-premium-key', $key );
		}

        echo '<form id="wcexd-options" method="post" action="">';

            echo '<label>' . esc_html__( 'Premium Key', 'wc-exporter-for-danea' ) . '</label>';
            echo '<input type="text" class="regular-text code" name="wcexd-premium-key" id="wcexd-premium-key" placeholder="' . esc_html__( 'Add your Premium Key', 'wc-exporter-for-danea' ) . '" value="' . esc_attr( $key ) . '" />';
            echo '<p class="description">' . esc_html__( 'Add here the Premium Key that you received by email, you will keep your copy of <strong>WooCommerce Exporter for Danea - Premium</strong> updated.', 'wc-exporter-for-danea' ) . '</p>';
            echo '<input type="hidden" name="done" value="1" />';

            wp_nonce_field( 'wcexd-premium-key', 'wcexd-premium-key-nonce' );

            echo '<input type="submit" class="button button-primary" value="' . esc_html__( 'Save', 'wc-exporter-for-danea' ) . '" />';
        echo '</form>';

    }


	/**
	 * Message to the admin in case of update not downlodable for bad or missed premium key
	 *
	 * @param  array $plugin_data the plugin data.
	 * @param  array $response    the response data.
	 *
	 * @return void
	 */
	public function check_update_message( $plugin_data, $response ) {

		$message = null;
		$key     = get_option( 'wcexd-premium-key' );

		if ( ! $key ) {

			$message = 'A <b>Premium Key</b> is required for keeping this plugin up to date. Please, add yours in the <a href="' . admin_url() . 'admin.php/?page=wc-exporter-for-danea">options page</a> or click <a href="https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/" target="_blank">here</a> for prices and details.';

		} else {

			$decoded_key = explode( '|', base64_decode( $key ) );
			$bought_date = date( 'd-m-Y', strtotime( $decoded_key[1] ) );
			$limit       = strtotime( $bought_date . ' + 365 day' );
			$now         = strtotime( 'today' );

			if ( $limit < $now ) {

				$message = 'It seems like your <strong>Premium Key</strong> is expired. Please, click <a href="https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/" target="_blank">here</a> for prices and details.';

			} elseif ( ! in_array( intval( $decoded_key[2] ), array( 140, 1582 ), true ) ) {

				$message = 'It seems like your <strong>Premium Key</strong> is not valid. Please, click <a href="https://www.ilghera.com/product/woocommerce-exporter-for-danea-premium/" target="_blank">here</a> for prices and details.';

			}
		}

		echo ( $message ) ? '<br><span class="wcexd-alert">' . wp_kses_post( $message ) . '</span>' : '';

	}

}

new WCEXD_Admin();

