<?php
/**
 * Modifica la pagina di checkout con i campi relativi alla fatturazione
 *
 * @author ilGhera
 * @package wc-exporter-for-danea/includes
 * @since 1.2.1
 */
class WCEXD_Checkout_Fields {

	public $custom_fields;

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'add_checkout_script' ) );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'set_custom_fields' ) );
		add_action( 'woocommerce_checkout_create_order', array( $this, 'save_fields' ), 10, 2 );
		add_action( 'woocommerce_thankyou', array( $this, 'display_custom_data' ) );
		add_action( 'woocommerce_view_order', array( $this, 'display_custom_data' ) );
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_custom_data_in_admin' ) );
		add_filter( 'woocommerce_email_customer_details', array( $this, 'display_custom_data_in_email' ), 10, 4 );
		add_action( 'woocommerce_checkout_process', array( $this, 'checkout_fields_check' ) );
		add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );

		$this->custom_fields = $this->get_active_custom_fields();
		$this->cf_mandatory  = get_option( 'wcexd_cf_mandatory' );
		$this->only_italy    = get_option( 'wcexd_only_italy' );
		$this->cf_only_italy = get_option( 'wcexd_cf_only_italy' );

	}

	/**
	 * Caricamento script
	 */
	public function add_checkout_script() {

		wp_enqueue_script( 'wcexd-checkout-script', WCEXD_URI . 'js/wcexd-checkout.js' );
		wp_localize_script(
			'wcexd-checkout-script',
			'options',
			array(
				'cf_mandatory'  => $this->cf_mandatory,
				'only_italy'    => $this->only_italy,
				'cf_only_italy' => $this->cf_only_italy,
			)
		);

	}


	/**
	 * Campi da aggiungere alla pagina di checkout in base alle opzioni scelte dall'utente
	 */
	public function get_active_custom_fields() {

		$output = array();

		$custom_fields = array(
			'billing_wcexd_piva'    => __( 'Partita IVA', 'wcexd' ),
			'billing_wcexd_cf'      => __( 'Codice fiscale', 'wcexd' ),
			'billing_wcexd_pec'     => __( 'PEC', 'wcexd' ),
			'billing_wcexd_pa_code' => __( 'Codice destinatario', 'wcexd' ),
		);

		foreach ( $custom_fields as $key => $value ) {
			if ( get_option( $key . '_active' ) === '1' ) {
				$output[ $key ] = $value;
			}
		}

		return $output;
	}


	/**
	 * Aggiunge i field customizzati all'elenco fields di WC
	 *
	 * @param object $fields i campi del modulo di checkout già presenti.
	 */
	public function set_custom_fields( $fields ) {

		$select = array(
			'private' => array(
				'active' => get_option( 'wcexd_private' ),
				'field'  => array( 'private' => __( 'Privato (Ricevuta)', 'wcexd' ) ),
			),
			'private_invoice' => array(
				'active' => get_option( 'wcexd_private_invoice' ),
				'field' => array( 'private-invoice' => __( 'Privato (Fattura)', 'wcexd' ) ),
			),
			'company_invoice' => array(
				'active' => get_option( 'wcexd_company_invoice' ),
				'field' => array( 'company-invoice' => __( 'Azienda (Fattura)', 'wcexd' ) ),
			),
		);

		/*La somma dei tipi di documenti abilitati dall'admin*/
		$sum = ( $select['private']['active'] + $select['private_invoice']['active'] + $select['company_invoice']['active'] );

		if ( $sum > 1 ) {
			$fields['billing']['billing_wcexd_invoice_type'] = array(
				'type'    => 'select',
				'options' => array(),
				'label'   => __( 'Documento fiscale', 'wcexd' ),
				'required'    => true,
				'class'   => array(
					'field-name form-row-wide',
				),
			);

			/*Controllo posizione campo*/
			if ( get_option( 'wcexd_document_type' ) ) {

				$fields['billing']['billing_wcexd_invoice_type']['priority'] = 1;

			}

			foreach ( $select as $key => $value ) {
				if ( '1' === $value['active'] ) {
					$label = key( $value['field'] );
					$fields['billing']['billing_wcexd_invoice_type']['options'][ $label ] = $value['field'][ $label ];
				}
			}
		}

		if ( ! empty( $this->custom_fields ) ) {
			foreach ( $this->custom_fields as $key => $value ) {
				$fields['billing'][ $key ] = array(
					'type' => 'text',
					'label' => $value,
					'class' => array(
						'field-name form-row-wide',
					),
				);
			}

			if ( isset( $this->custom_fields['billing_wcexd_piva'] ) ) {
				$fields['billing']['billing_wcexd_piva']['required'] = true;
			}

			/*Obbligatorietà cf al caricamento di pagina*/
			if ( isset( $this->custom_fields['billing_wcexd_cf'] ) ) {
				if ( ( 1 === $sum && ! isset( $select['private']['active'] ) || $sum > 1 ) ) {

					$fields['billing']['billing_wcexd_cf']['required'] = true;

				} elseif ( 1 === $sum && isset( $select['private']['active'] ) ) {
					if ( $this->cf_mandatory ) {

						$fields['billing']['billing_wcexd_cf']['required'] = true;

					}
				}
			}

			/*Rendo obbligatorio cf e p. iva ed azienda solo quando richiesto*/
			if ( isset( $_POST['billing_wcexd_invoice_type'] ) ) {

				if ( 'private-invoice' === $_POST['billing_wcexd_invoice_type'] ) {

					$fields['billing']['billing_wcexd_piva']['required'] = false;

				} elseif ( 'private' === $_POST['billing_wcexd_invoice_type'] ) {

					$fields['billing']['billing_wcexd_piva']['required'] = false;

					if ( ! $this->cf_mandatory ) {

						$fields['billing']['billing_wcexd_cf']['required'] = false;

					}
				} else {

					$fields['billing']['billing_company']['required'] = true;

				}
			}


			/*Codice fiscale non obbligatorio fuori dall'Italia*/
			if ( isset( $_POST['billing_country'] ) && 'IT' !== $_POST['billing_country'] ) {

				$fields['billing']['billing_wcexd_cf']['required'] = false;

			}

			/*Rendo obbligatorio cf e p. iva ed azienda solo quando richiesto*/
			if ( isset( $_POST['billing_wcexd_invoice_type'] ) ) {

				if ( 'private' !== $_POST['billing_wcexd_invoice_type'] ) {

					if ( ! isset( $this->custom_fields['billing_wcexd_pec'] ) && isset( $this->custom_fields['billing_wcexd_pa_code'] ) ) {

						$fields['billing']['billing_wcexd_pa_code']['required'] = true;

					} elseif ( isset( $this->custom_fields['billing_wcexd_pec'] ) && ! isset( $this->custom_fields['billing_wcexd_pa_code'] ) ) {

						$fields['billing']['billing_wcexd_pec']['required'] = true;

					}
				}
			}
		}

		return $fields;

	}


	/**
	 * Verifica la correttezza del dato fiscale inserito
	 *
	 * @param  string $valore P.IVA o Codice Fiscale.
	 * @return bool
	 */
	public function fiscal_field_checker( $valore ) {
		$expression = '^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$';
		if ( is_numeric( $valore ) ) {
			$expression = '^[0-9]{11}$';
		}
		if ( preg_match( '/' . $expression . '/', $valore ) ) {
			return true;
		}
		return false;
	}


	/**
	 * Verifica i campi di checkout al click per la creazione dell'ordine
	 */
	public function checkout_fields_check() {

		/*PEC e Codice destinatario*/
		if ( isset( $_POST['billing_wcexd_invoice_type'] ) && 'private' !== $_POST['billing_wcexd_invoice_type'] && isset( $_POST['billing_country'] ) ) {

			if ( isset( $this->custom_fields['billing_wcexd_pec'] ) && isset( $this->custom_fields['billing_wcexd_pa_code'] ) ) {

				$pec = isset( $_POST['billing_wcexd_pec'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_wcexd_pec'] ) ) : '';
				$pa_code = isset( $_POST['billing_wcexd_pa_code'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_wcexd_pa_code'] ) ) : '';

				$country = sanitize_text_field( wp_unslash( $_POST['billing_country'] ) );

				if ( ! $this->only_italy || ( $this->only_italy && 'IT' === $country ) ) {

					if ( ! $pec && ! $pa_code ) {
						// wc_add_notice( __( 'Il campo <strong>PEC</strong> o il campo <strong>Codice destinatario</strong> devono essere compilati.', 'wcexd' ), 'error' );
						
						$code = 'IT' === $country ? '0000000' : 'xxxxxxx';

						wc_add_notice( sprintf( __( 'Non hai inserito <strong>PEC</strong> o <strong>Codice destinatario</strong>, puoi utilizzare il Codice destinatario generico <strong><u>%s</u></strong>.', 'wcexd' ), $code ), 'error' );
					}

				}

			}

		}

		/*Controllo campi fiscali*/
		if ( get_option( 'wcexd_fields_check' ) ) {

			/*Codice fiscale*/
			if ( isset( $_POST['billing_wcexd_cf'] ) && '' !== $_POST['billing_wcexd_cf'] && false === $this->fiscal_field_checker( $_POST['billing_wcexd_cf'] ) ) {

				wc_add_notice( 'ATTENZIONE! Sembra che il <strong>Codice Fiscale</strong> inserito non sia corretto.', 'error' );

			}

			/*Partita IVA*/
			if ( isset( $_POST['billing_wcexd_invoice_type'] ) && 'company-invoice' === $_POST['billing_wcexd_invoice_type'] ) {
				if ( isset( $_POST['billing_wcexd_piva'] ) && '' !== $_POST['billing_wcexd_piva'] && false === $this->fiscal_field_checker( $_POST['billing_wcexd_piva'] ) ) {

					wc_add_notice( 'ATTENZIONE! Sembra che la <strong>Partita IVA</strong> inserito non sia corretta.', 'error' );

				}
			}
		}
	}


	/**
	 * Salva i campi personalizzati
	 *
	 * @param  object $order l'ordine in questione.
	 * @param  array  $data  i dati dell'ordine.
	 */
	public function save_fields( $order, $data ) {

		if ( $this->custom_fields ) {
			foreach ( $this->custom_fields as $key => $value ) {
				if ( isset( $data[ $key ] ) ) {
					$order->update_meta_data( '_' . $key, sanitize_text_field( $data[ $key ] ) );
				}
			}
		}

	}


	/**
	 * Visualizza le informazioni personalizzate nella pagina di checkout e in front-end, nell'area profilo dell'utente.
	 *
	 * @param  int $order_id   l'id dell'ordine.
	 */
	public function display_custom_data( $order_id ) {

		if ( $this->custom_fields ) {

			$order = wc_get_order( $order_id );

			echo '<h2>' . esc_html__( 'Dati di fatturazione', 'wcexd' ) . '</h2>';

			echo '<table class="shop_table shop_table_responsive">';
				echo '<tbody>';
			if ( $this->custom_fields ) {
				foreach ( $this->custom_fields as $key => $value ) {
					if ( $order->get_meta( '_' . $key ) ) {
						echo '<tr>';
							echo '<th width="40%">' . esc_html( $value ) . ':</th>';
							echo '<td>' . esc_html( $order->get_meta( '_' . $key ) ) . '</td>';
						echo '</tr>';
					}
				}
			}
				echo '</tbody>';
			echo '</table>';

		}
	}


	/**
	 * Visualizza le informazioni personalizzate nel back-end dell'ordine
	 *
	 * @param  object $order l'ordine.
	 */
	public function display_custom_data_in_admin( $order ) {

		if ( $this->custom_fields ) {

			foreach ( $this->custom_fields as $key => $value ) {
				if ( $order->get_meta( '_' . $key ) ) {
					echo '<p><strong>' . esc_html( $value ) . ': </strong><br>' . esc_html( $order->get_meta( '_' . $key ) ) . '</p>';
				}
			}

		}
	}


	/**
	 * Mostra le informaizoni personalizzate nella email di conferma ordine
	 *
	 * @param object $order l'ordine WC.
	 */
	public function display_custom_data_in_email( $order ) {

		if ( $this->custom_fields ) {

			echo '<h2>' . esc_html__( 'Dati fatturazione', 'wcexd' ) . '</h2>';
			foreach ( $this->custom_fields as $key => $value ) {
				if ( $order->get_meta( '_' . $key ) ) {
					echo '<p style="margin: 0 0 8px;">' . esc_html( $value ) . ': <span style="font-weight: normal;">' . esc_html( $order->get_meta( '_' . $key ) ) . '</span></p>';
				}
			}
			echo '<div style="display: block; padding-bottom: 25px;"></div>';
		}

	}


	/**
	 * Aggiunge i campi fiscali del plugin alla pagina profilo dell'utente
	 *
	 * @param  object $user user WP.
	 */
	public function extra_user_profile_fields( $user ) {

		if ( $this->custom_fields ) {

			echo '<h3>' . esc_html__( 'Dati di fatturazione', 'wcexd' ) . '</h3>';

			echo '<table class="form-table">';

				foreach ( $this->custom_fields as $key => $value ) {

					echo '<tr>';
						echo '<th><label for="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</label></th>';
						echo '<td>';
							echo '<input type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( get_the_author_meta( $key, $user->ID ) ) . '" class="regular-text" />';
						echo '</td>';
					echo '</tr>';

				}

			echo '</table>';
		}

	}

	/**
	 * Consente di editare i campi fiscali del plugin nella pagina profilo dell'utente
	 *
	 * @param  int $user_id l'id dell'utente WP.
	 */
	public function save_extra_user_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {

			return false;

		} else {

			if ( $this->custom_fields ) {

				foreach ( $this->custom_fields as $key => $value ) {

					$new_value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';

					if ( $new_value ) {

						update_user_meta( $user_id, $key, $new_value );

					}

				}

			}

		}

	}

}
new WCEXD_Checkout_Fields();
