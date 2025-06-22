<?php
/**
 * Products download
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 *
 * @since 1.6.5
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCEXD_Products_Download class
 *
 * @since 1.6.5
 */
class WCEXD_Products_Download {

	/**
	 * The file
	 *
	 * @var object
	 */
	public $fp;

	/**
	 * The plugin functions
	 *
	 * @var object
	 */
	public $functions;

	/**
	 * Use suppliers
	 *
	 * @var bool
	 */
	public $use_suppliers;

	/**
	 * Export type
	 *
	 * @var string
	 */
	public $export_type;

	/**
	 * Export Danea products variations
	 *
	 * @var bool
	 */
	public $danea_vars;

	/**
	 * Use the tax name
	 *
	 * @var bool
	 */
	public $wcexd_products_tax_name;

	/**
	 * Net or gross
	 *
	 * @var string
	 */
	public $size_type;

	/**
	 * Net or gross
	 *
	 * @var string
	 */
	public $weight_type;

	/**
	 * Use Sensei
	 *
	 * @var bool
	 */
	public $use_sensei;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		/* Actions */
		add_action( 'admin_init', array( $this, 'init' ) );

		$this->functions = new WCEXD_Functions();

	}

	/**
	 * Get data sent by the admin
	 *
	 * @return void
	 */
	public function init() {

		if ( isset( $_POST['wcexd-products-nonce'], $_POST['wcexd-products-hidden'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wcexd-products-nonce'] ) ), 'wcexd-products-submit' ) ) {

			/* Admin options selected */
			$this->use_suppliers           = isset( $_POST['wcexd-use-suppliers'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-use-suppliers'] ) ) : 0;
			$this->wcexd_products_tax_name = isset( $_POST['wcexd-products-tax-name'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-products-tax-name'] ) ) : 0;
			$this->export_type             = isset( $_POST['wcexd-export-type'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-export-type'] ) ) : 0;
			$this->size_type               = isset( $_POST['wcexd-size-type'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-size-type'] ) ) : 0;
			$this->weight_type             = isset( $_POST['wcexd-weight-type'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-weight-type'] ) ) : 0;
			$this->use_sensei              = isset( $_POST['wcexd-sensei'] ) ? sanitize_text_field( wp_unslash( $_POST['wcexd-sensei'] ) ) : 0;

			/* Export Danea vars */
			$this->danea_vars = 'variations' === $this->export_type ? true : false;

			/* Save data */
			update_option( 'wcexd-use-suppliers', $this->use_suppliers );
			update_option( 'wcexd-products-tax-name', $this->wcexd_products_tax_name );
			update_option( 'wcexd-export-type', $this->export_type );
			update_option( 'wcexd-size-type', $this->size_type );
			update_option( 'wcexd-weight-type', $this->weight_type );
			update_option( 'wcexd-sensei-option', $this->use_sensei );

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

		/* Define the file name */
		$file_name = $this->danea_vars ? 'wcexd-variations-list.csv' : 'wcexd-products-list.csv';

		/* Start CSV */
		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: private', false );
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $file_name );
		header( 'Content-Transfer-Encoding: binary' );

		$this->fp = fopen( 'php://output', 'w' );

			$products_list = array(
				'Cod.',
				'Descrizione',
				'Tipologia',
				'Categoria',
				'Sottocategoria',
				'Cod. Udm',
				'Cod. Iva',
				$this->functions->get_prices_col_name( 1 ),
				$this->functions->get_prices_col_name( 2 ),
				$this->functions->get_prices_col_name( 3 ),
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

			$variations_list = array(
				'Cod.',
				'Descrizione',
				'Colore',
				'Taglia',
				'Cod. a barre',
				'Q.tà giacenza',
			);

			/* Define the list to use */
			$list = $this->danea_vars ? $variations_list : $products_list;

			fputcsv( $this->fp, $list );

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
			'limit'  => -1,
		);

		/* Define the products type to get */
		$args['type'] = $this->danea_vars ? array( 'variable' ) : apply_filters( 'wcexd_export_products_types', array( 'simple', 'variable' ) );

		$products = wc_get_products( $args );

		if ( is_array( $products ) ) {

			foreach ( $products as $product ) {

				/* Don't export parent products withs Danea variations */
				if ( ! $this->danea_vars ) {

					$this->prepare_single_product_data( $product );
				}

				/* Don't export variations if not required */
				if ( 'products' !== $this->export_type ) {

					$variations = $product->get_children();

					if ( $variations ) {

						foreach ( $variations as $var_id ) {

							$variation = wc_get_product( $var_id );

							if ( $this->danea_vars ) {

								$this->prepare_single_variation_data( $variation );

							} else {

								$this->prepare_single_product_data( $variation, true );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Check if the product/variation is a Danea size/color var
	 * Both with parent and child product
	 *
	 * @param object $product the WC product/variation.
	 *
	 * @return bool
	 */
	public function is_danea_variation( $product ) {

		if ( $product->get_attribute( 'color' ) || $product->get_attribute( 'size' ) ) {

			return true;
		}
	}

	/**
	 * The single Danea variation data
	 *
	 * @param object $variation the WC product variation.
	 *
	 * @return void
	 */
	public function prepare_single_variation_data( $variation ) {

		/* Do not export other variations */
		if ( ! $this->is_danea_variation( $variation ) ) {

			return;
		}

		/* The product parent data */
		$parent_data   = $variation->get_parent_id() ? $variation->get_parent_data() : array();
		$parent_status = isset( $parent_data['status'] ) ? $parent_data['status'] : null;
		$parent_code   = isset( $parent_data['sku'] ) && $parent_data['sku'] ? $parent_data['sku'] : $variation->get_parent_id();

		/* Do not export variations of products not published */
		if ( 'publish' !== $parent_status ) {
			return;
		}

		$details = ' | ' . implode( ' - ', array_map( 'ucfirst', $variation->get_attributes() ) );

		/* The product code to use in Danea */
		$variation_code = $variation->get_sku() ? $variation->get_sku() : $variation->get_id();

		/* Generate the variation sku if necessary */
		if ( is_int( $variation_code ) ) {

			/* Translators: 1 The product ID. 2 The size attr. 3 The color attr. */
			$variation_code = sprintf( '%1d/%2$s/%3$s', $variation_code, $variation->get_attribute( 'size' ), $variation->get_attribute( 'color' ) );
		}

		$data = array(
			$parent_code,
			/* Translators: 1. The product title 2. The variable attributes */
			sprintf( '%1$s%2$s', $variation->get_title(), $details ),
			$variation->get_attribute( 'color' ),
			$variation->get_attribute( 'size' ),
			$variation_code,
			$variation->get_stock_quantity(),
		);

		fputcsv( $this->fp, $data );
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

		/* Do not import variations of products not published */
		if ( $is_variation && 'publish' !== get_post_status( $product->get_parent_id() ) ) {

			return;
		}

		/* The product code to use in Danea */
		$product_code = $product->get_sku() ? $product->get_sku() : $product->get_id();

		/* Get the product category */
		$product_category     = $this->functions->get_product_category_name( $product, $is_variation );
		$product_category_cat = isset( $product_category['cat'] ) ? $product_category['cat'] : null;
		$product_category_sub = isset( $product_category['sub'] ) ? $product_category['sub'] : null;

		$tax_rate = 1 === intval( $this->wcexd_products_tax_name ) ? $this->functions->get_tax_rate( $product, 'name' ) : $this->functions->get_tax_rate( $product );

		$details = null;

		/* Variation details */
		if ( $is_variation ) {

			$details = ' | ' . implode( ' - ', array_map( 'ucfirst', $product->get_attributes() ) );
		}

		/* Code in Notes */
		$notes = 'all' === $this->export_type ? $this->functions->get_product_notes( $product, $is_variation ) : null;

		$data = array(
			$product_code,
			/* Translators: 1. The product title 2. The variable attributes */
			sprintf( '%1$s%2$s', $product->get_title(), $details ),
			$this->get_the_product_type( $product ),
			$product_category_cat,
			$product_category_sub,
			'',
			$tax_rate,
			$this->get_product_price( $product ),
			$this->get_product_price( $product, 'sale' ),
			'',
			'',
			'',
			'',
			$notes,
			'',
			'',
			'',
			$product->get_description(),
			'',
			'',
			'',
			'',
			'',
			$this->get_supplier_info( $product ),
			$this->get_supplier_info( $product, 'name' ),
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
			get_option( 'woocommerce_dimension_unit' ),
			$this->get_the_product_measures( $product, 'net_width' ),
			$this->get_the_product_measures( $product, 'net_height' ),
			$this->get_the_product_measures( $product, 'net_length' ),
			'',
			$this->get_the_product_measures( $product, 'gross_width' ),
			$this->get_the_product_measures( $product, 'gross_height' ),
			$this->get_the_product_measures( $product, 'gross_length' ),
			'',
			get_option( 'woocommerce_weight_unit' ),
			$this->get_the_product_measures( $product, 'net_weight' ),
			$this->get_the_product_measures( $product, 'gross_weight' ),
			'',
		);

		fputcsv( $this->fp, $data );
	}

	/**
	 * Define the product type to use in Danea Easyfatt
	 *
	 * @param $object $product the WC product.
	 *
	 * @return string
	 */
	public function get_the_product_type( $product ) {

		/* Manage stock */
		if ( $product->get_manage_stock() ) {

			/* Previously imported from Danea Easyfatt */
			$product_type = $product->get_meta( 'wcifd-danea-size-color', true ) ? 'Art. con magazzino (taglie/colori)' : 'Art. con magazzino';

			/* Variations with size and/or color */
			$product_type = $this->is_danea_variation( $product ) ? 'Art. con magazzino (taglie/colori)' : $product_type;

		} else {

			$product_type = 'Articolo';
		}

		return $product_type;
	}

	/**
	 * Get the regular and sale product price
	 *
	 * @param $object $product the WC product.
	 * @param string $data    the data to return.
	 *
	 * @return string
	 */
	public function get_product_price( $product, $data = 'regular' ) {

		$regular_price     = null;
		$sale_price        = null;
		$get_regular_price = $product->get_regular_price();
		$get_sale_price    = $product->get_sale_price();

		/* Set the price format */
		$regular_price = $get_regular_price ? round( $get_regular_price, 2 ) : $get_regular_price;
		$regular_price = str_replace( '.', ',', $regular_price );

		if ( $get_sale_price ) {

			$sale_price = round( $get_sale_price, 2 );
			$sale_price = str_replace( '.', ',', $sale_price );
		}

		return 'regular' === $data ? $regular_price : $sale_price;
	}

	/**
	 * Get the product supplier ID and name
	 *
	 * @param object $product the WC product.
	 * @param string $data    the data to return.
	 *
	 * @return mixed
	 */
	public function get_supplier_info( $product, $data = 'id' ) {

		$id_supplier = null;

		/* Check if Sensei is active */
		$course_author_id = $this->functions->get_sensei_author( $product->get_id() );

		if ( $this->use_sensei && $course_author_id ) {

			$id_supplier = $course_author_id;

		} elseif ( $this->use_suppliers ) {

			$id_supplier = get_post_field( 'post_author', $product->get_id() );
		}

		/* Supplier name (post author) */
		$supplier_name = null;

		if ( $id_supplier ) {

			$supplier_name = sprintf( '%1$s %2$s', get_user_meta( $id_supplier, 'billing_first_name', true ), get_user_meta( $id_supplier, 'billing_last_name', true ) );
			$company_name  = get_user_meta( $id_supplier, 'billing_company', true );

			/* Use the company name if exists */
			$supplier_name = $company_name ? $company_name : $supplier_name;
		}

		return 'id' === $data ? $id_supplier : $supplier_name;
	}

	/**
	 * Get the product width, length and wight
	 *
	 * @param object $product the WC product.
	 * @param tring  $data    the data to return.
	 *
	 * @return float
	 */
	public function get_the_product_measures( $product, $data ) {

		$output = array();

		/* Weight */
		$this->weight_type      = get_option( 'wcexd-weight-type' );
		$weight                 = $product->get_weight();
		$output['gross_weight'] = null;
		$output['net_weight']   = null;

		if ( 'net-weight' === $this->weight_type ) {

			$output['net_weight'] = number_format( floatval( $weight ), 2, ',', '' );

		} else {

			$output['gross_weight'] = number_format( floatval( $weight ), 2, ',', '' );
		}

		/* Measures */
		$this->size_type        = get_option( 'wcexd-size-type' );
		$width                  = number_format( floatval( $product->get_width() ), 2, ',', '' );
		$height                 = number_format( floatval( $product->get_height() ), 2, ',', '' );
		$length                 = number_format( floatval( $product->get_length() ), 2, ',', '' );
		$output['net_width']    = null;
		$output['net_height']   = null;
		$output['net_length']   = null;
		$output['gross_width']  = null;
		$output['gross_height'] = null;
		$output['gross_length'] = null;

		if ( 'net-size' === $this->size_type ) {

			$output['net_width']  = $width;
			$output['net_height'] = $height;
			$output['net_length'] = $length;

		} else {

			$output['gross_width']  = $width;
			$output['gross_height'] = $height;
			$output['gross_length'] = $length;
		}

		return isset( $output[ $data ] ) ? $output[ $data ] : null;
	}
}

new WCEXD_Products_Download();

