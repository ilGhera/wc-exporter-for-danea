<?php
/**
 * Users download
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 *
 * @since 1.0.1
 */
class WCEXD_Suppliers_Download {
    
    /**
     * The file
     */
    public $fp;

    /**
     * The plugin functions
     *
     * @var object
     */
    public $functions;


    /**
     * The admin settings for the users download
     */
    public $users_role;

    /**
     * The constructor
     *
     * @return void
     */
    public function __construct() {

        /* Actions */
        add_action( 'admin_init', array( $this, 'init' ) );

        $this->functions = new WCEFD_Functions();

    }


    /**
     * Get data sent by the admin
     *
     * @return void
     */
    public function init() {

        if( isset( $_POST['wcexd-users'] ) && wp_verify_nonce( $_POST['wcexd-suppliers-nonce'], 'wcexd-suppliers-submit' ) ) {

            /* Admin option selected */
            $this->users_role = sanitize_text_field( wp_unslash( $_POST['wcexd-users'] ) );

            /* Save data */
            update_option( 'wcexd-users-role', $this->users_role ); 

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
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=wcexd-suppliers-list.csv');
		header("Content-Transfer-Encoding: binary");

		$this->fp = fopen('php://output', 'w');
		
            $list = array(
                'Cod.',
                'Denominazione',
                'Indirizzo',
                'Cap',
                'Città',
                'Prov.',
                'Regione',
                'Nazione',
                'Referente',
                'Tel.',
                'Cell',
                'Fax',
                'e-mail',
                'Pec',
                'Codice fiscale',
                'Partita Iva',
                'Sconti',
                'Listino',
                'Fido',
                'Pagamento',
                'Banca',
                'Ns Banca',
                'Data Mandato SDD',
                'Emissione SDD',
                'Rit. acconto?',
                'Doc via e-mail?',
                'Fatt. con Iva',
                'Conto reg.',
                'Resp. trasporto',
                'Porto',
                'Avviso nuovi doc.',
                'Note doc.',
                'Home page',
                'Login web',
                'Extra 1',
                'Extra 2',
                'Extra 3',
                'Extra 4',
                'Extra 5',
                'Extra 6',
                'Note',
            );
                    
            fputcsv($this->fp, $list);

            $this->get_users();

        fclose($this->fp);

        exit;

    }


    /**
     * The users loop
     *
     * @return void
     */
    public function get_users() {
	  
        $args = array(
            'role' => $this->users_role
        );

		$suppliers = get_users($args);

		foreach($suppliers as $supplier) {

            $this->prepare_single_user_data( $supplier );

		}

    }


    /**
     * The single user data
     *
     * @param object $user the WP user.
     *
     * @return void
     */
    public function prepare_single_user_data( $supplier ) {

        $supplier_name = get_user_meta( $supplier->ID, 'billing_first_name', true ) . ' ' . get_user_meta( $supplier->ID, 'billing_last_name', true );

        //Se presente il nome dell'azienda, modifico la denominazione per Danea
        if(get_user_meta($supplier->ID, 'billing_company', true)) {

            $denominazione = (get_user_meta($supplier->ID, 'billing_company', true));

        } elseif($supplier_name != ' ') {

            $denominazione = $supplier_name;

        } else {

            $denominazione = $supplier->display_name;

        }

		/*Recupero i nomi dei campi C.Fiscale e P.IVA*/
		$get_cf_name = $this->functions->get_italian_tax_fields_names('cf_name');
		$get_pi_name = $this->functions->get_italian_tax_fields_names('pi_name');
	
        /*SE ATTIVO UNO DEI PLUGIN, RECUPRO CF E P.IVA DEL SINGOLO UTENTE*/
        $cf_value = ($get_cf_name) ? get_user_meta($supplier->ID, $get_cf_name, true) : '';
        $pi_value = ($get_pi_name) ? get_user_meta($supplier->ID, $get_pi_name, true) : '';


        $data = array(
            $supplier->ID,
            $denominazione,
            get_user_meta($supplier->ID, 'billing_address_1', true),
            get_user_meta($supplier->ID, 'billing_postcode', true),
            get_user_meta($supplier->ID, 'billing_city', true),
            get_user_meta($supplier->ID, 'billing_state', true),
            '',
            get_user_meta($supplier->ID, 'billing_country', true),
            $supplier_name,
            '',
            get_user_meta($supplier->ID, 'billing_phone', true),
            '',
            $supplier->user_email,
            '',
            $cf_value,
            $pi_value,
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
        );

        fputcsv($this->fp, $data);

    }

}
new WCEXD_Suppliers_Download();

