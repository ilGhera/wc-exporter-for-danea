<?php
/**
 * Modifica la pagina di checkout con i campi relativi alla fatturazione
 * @author ilGhera
 * @package wc-exporter-for-danea/includes
 * @version 1.1.6
 */
class wcexd_checkout_fields {

	public $custom_fields;

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'add_checkout_script' ) );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'set_custom_fields' ) );
		add_action( 'woocommerce_before_order_notes', array( $this, 'display_fields' ) );
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
		$this->only_italy    = get_option('wcexd_only_italy');

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
            	'only_italy' => $this->only_italy,
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
	 * @param object $fields
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
				if ( ( $sum === 1 && ! isset( $select['private']['active'] ) || $sum > 1 ) ) {

					$fields['billing']['billing_wcexd_cf']['required'] = true;					
				
				} elseif ( $sum === 1 && isset( $select['private']['active'] ) ) {
					if ( get_option( 'wcexd_cf_mandatory' ) ) {

						$fields['billing']['billing_wcexd_cf']['required'] = true;					
					
					}
				}
			}

			/*Rendo obbligatorio cf e p. iva quando richiesto*/
			if ( isset( $_POST['billing_wcexd_invoice_type'] ) ) {

				if ( $_POST['billing_wcexd_invoice_type'] === 'private-invoice' ) {

					$fields['billing']['billing_wcexd_piva']['required'] = false;

				} elseif ( $_POST['billing_wcexd_invoice_type'] === 'private' ) {

					$fields['billing']['billing_wcexd_piva']['required'] = false;

					if ( ! get_option( 'wcexd_cf_mandatory' ) ) {

						$fields['billing']['billing_wcexd_cf']['required'] = false;

					}
				}
			}

			if ( ! isset( $this->custom_fields['billing_wcexd_pec'] ) && isset( $this->custom_fields['billing_wcexd_pa_code'] ) ) {
				$fields['billing']['billing_wcexd_pa_code']['required'] = true;
			} elseif ( isset( $this->custom_fields['billing_wcexd_pec'] ) && ! isset( $this->custom_fields['billing_wcexd_pa_code'] ) ) {
				$fields['billing']['billing_wcexd_pec']['required'] = true;
			}
		}

		return $fields;

	}


	/**
	 * Verifica la correttezza del dato fiscale inserito
	 * @param  string $valore P.IVA o Codice Fiscale
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
		if ( isset( $_POST['billing_wcexd_invoice_type'] ) && $_POST['billing_wcexd_invoice_type'] !== 'private' ) {
			
			if ( isset( $this->custom_fields['billing_wcexd_pec'] ) && isset( $this->custom_fields['billing_wcexd_pa_code'] ) ) {
				
				$pec = isset( $_POST['billing_wcexd_pec'] ) ? sanitize_text_field( $_POST['billing_wcexd_pec'] ) : '';
				$pa_code = isset( $_POST['billing_wcexd_pa_code'] ) ? sanitize_text_field( $_POST['billing_wcexd_pa_code'] ) : '';

				$country = $_POST['billing_country'];

				if ( ! $this->only_italy || ( $this->only_italy && 'IT' == $country ) ) {
					
					if ( ! $pec && ! $pa_code ) {
						wc_add_notice( __( 'Il campo <strong>PEC</strong> o il campo <strong>Codice destinatario</strong> devono essere compilati.', 'wcexd' ), 'error' );
					}

				}

			}

		}

		/*Controllo campi fiscali*/
		if ( get_option( 'wcexd_fields_check' ) ) {

			/*Codice fiscale*/
			if ( isset( $_POST['billing_wcexd_cf'] ) && $_POST['billing_wcexd_cf'] !== '' && $this->fiscal_field_checker( $_POST['billing_wcexd_cf'] ) === false ) {
				wc_add_notice( 'ATTENZIONE! Sembra che il <strong>Codice Fiscale</strong> inserito non sia corretto.', 'error' );
			}

			/*Partita IVA*/
			if ( isset( $_POST['billing_wcexd_invoice_type'] ) && $_POST['billing_wcexd_invoice_type'] === 'company-invoice' ) {
				if ( isset( $_POST['billing_wcexd_piva'] ) && $_POST['billing_wcexd_piva'] !== '' && $this->fiscal_field_checker( $_POST['billing_wcexd_piva'] ) === false ) {
					wc_add_notice( 'ATTENZIONE! Sembra che la <strong>Partita IVA</strong> inserito non sia corretta.', 'error' );
				}
			}
		}
	}


	/**
	 * Inserisci i campi personalizzati nella pagina di checkout
	 * @param object $checkout
	 */
	public function display_fields( $checkout ) {

		if ( $this->custom_fields ) {
			foreach ( $this->custom_fields as $key => $value ) {
				if ( isset( $checkout->checkout_fields[ $key ] ) ) {
					// woocommerce_form_field( $key, array('label' => $value), $checkout->get_value( $key ) );
				}
			}
		}
	}


	/**
	 * Salva i campi personalizzati
	 * @param  object $order l'ordine in questione
	 * @param  array $data  i dati dell'ordine
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
	 * @param  int $order_id   l'id dell'ordine
	 */
	public function display_custom_data( $order_id ) {

		if ( $this->custom_fields ) {

			$order = wc_get_order( $order_id );

			echo '<h2>' . __( 'Dati di fatturazione', 'wcexd' ) . '</h2>';

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
	 * @param  object $order l'ordine
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
	 * @param  array $fields        i campi da restituire
	 * @param  bool  $sent_to_admin da includere anche nella mail per l'admin, default true
	 * @param  object $order        l'woocommerce_admin_order_data_after_shipping_address
	 * @return array                Per motivi di formattazione, restituisco un array vuoto e stampo direttamente i campi con css in linea
	 */
	public function display_custom_data_in_email( $order, $sent_to_admin, $plain_text, $email ) {

		if ( $this->custom_fields ) {

			echo '<h2>' . __( 'Dati fatturazione', 'wcexd' ) . '</h2>';
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
	 * @param  object $user user WP
	 */
	public function extra_user_profile_fields( $user ) {

		if ( $this->custom_fields ) {

			echo '<h3>' . __( 'Dati di fatturazione', 'wcexd' ) . '</h3>';

		    echo '<table class="form-table">';

		    	foreach ($this->custom_fields as $key => $value) {
		
				    echo '<tr>';
				        echo '<th><label for="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</label></th>';
				        echo '<td>';
							echo '<input type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( get_the_author_meta( $key, $user->ID ) ) . '" class="regular-text" />';
				            // echo '<span class="description">' . _e("Please enter your address.") . '</span>';
				        echo '</td>';
				    echo '</tr>';
		
		    	}

		    echo '</table>';
		}

	}

	/**
	 * Consente di editare i campi fiscali del plugin nella pagina profilo dell'utente
	 * @param  int $user_id l'id dell'utente WP
	 */
	public function save_extra_user_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) ) { 
	        
	        return false; 
	    
	    } else {

	    	if ( $this->custom_fields ) {
	    		
	    		foreach ($this->custom_fields as $key => $value) {

				    update_user_meta( $user_id, $key, $_POST[ $key ] );
	    			
	    		}

	    	}

	    }

	}

}
new wcexd_checkout_fields();
