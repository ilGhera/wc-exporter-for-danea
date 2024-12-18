<?php
/**
 * WCEXD Functions Class
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 *
 * @since 1.6.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCEXD_Functions class
 *
 * @since 1.6.3
 */
class WCEXD_Functions {

	/**
	 * Get the product VAT value
	 *
	 * @param  object $product the WC product ID.
	 * @param  string $type    the field to return (name or value).
	 *
	 * @return mixed
	 */
	public function get_tax_rate( $product, $type = null ) {

		$output = 'FC';

		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {

			$output        = 0;
			$results       = array();
			$tax_status    = $product->get_tax_status();
			$base_location = isset( wc_get_base_location()['country'] ) ? wc_get_base_location()['country'] : 'IT';

			/* Get parent data in case of variation */
			$parent_id         = $product->get_parent_id();
			$parent_product    = wc_get_product( $parent_id );
			$parent_tax_status = $parent_id ? $parent_product->get_tax_status() : null;

			if ( 'taxable' === $tax_status || ( null === $tax_status && 'taxable' === $parent_tax_status ) ) {

				/* Null with standard class (22%) */
				$tax_class = $tax_status ? $product->get_tax_class() : $parent_product->get_tax_class();

				if ( 'parent' === $tax_class && 'taxable' === $parent_tax_status ) {

					$tax_class = $parent_product->get_tax_class();

				}

				$rates = WC_Tax::get_rates_for_tax_class( $tax_class );

				if ( is_array( $rates ) ) {

					foreach ( $rates as $rate ) {

						if ( $rate->tax_rate_country === $base_location ) {

							/* The specific tax rate for the country */
							$output = 'name' === $type ? $rate->tax_rate_name : intval( $rate->tax_rate );

							continue;

						} elseif ( ! $rate->tax_rate_country ) {

							/* A generic tax rate where the country is not specified */
							$results[] = 'name' === $type ? $rate->tax_rate_name : intval( $rate->tax_rate );

						}
					}
				}

				if ( ! $output && isset( $results[0] ) ) {

					$output = $results[0];

				}
			}
		}

		return $output;

	}


	/**
	 * Move the single taxonomy term based od its parent ID
	 *
	 * @param  object $a the first taxonomy term.
	 * @param  object $b the second taxonomy term.
	 *
	 * @return mixed
	 */
	public function sort_sub_categories( $a, $b ) {

		if ( isset( $a->parent ) && isset( $b->parent ) ) {

			if ( $a->parent === $b->parent ) {

				return 0;

			}

			return ( $a->parent > $b->parent ) ? +1 : -1;

		}

	}


	/**
	 * The sub-categories string used for the products download
	 *
	 * @param  array $child the taxonomy terms.
	 *
	 * @return string la lista formattata per Danea Easyfatt
	 */
	public function prepare_sub_categories( $child ) {

		$list = array();

		if ( ! empty( $child ) ) {

			usort( $child, array( $this, 'sort_sub_categories' ) );

			foreach ( $child as $cat ) {

				$list[] = $cat->slug;

			}

			$child_string = implode( ' >> ', $list );

			return $child_string;

		}

	}


	/**
	 * Get the product category name
	 *
	 * @param object $product      the WC product.
	 * @param bool   $is_variation variatio object with true.
	 *
	 * @return string
	 */
	public function get_product_category_name( $product, $is_variation = false ) {

		$parent  = null;
		$child   = array();
		$cat_ids = $product->get_category_ids();

		if ( $is_variation ) {

			$parent_p = wc_get_product( $product->get_parent_id() );
			$cat_ids  = $parent_p->get_category_ids();

		}

		if ( $cat_ids ) {

			foreach ( $cat_ids as $cat_id ) {

				$cat = get_term_by( 'id', $cat_id, 'product_cat' );

				if ( 0 !== $cat->parent ) {

					$child[] = $cat;

					$get_parent = get_term_by( 'id', $cat->parent, 'product_cat' );
					$parent     = 0 === $get_parent->parent ? $get_parent->slug : $parent;

				} else {

					$parent = null === $parent ? $cat->slug : $parent;

				}
			}

			if ( $child ) {

				$child_string = self::prepare_sub_categories( $child );

				$cat_name = array(
					'cat' => $parent,
					'sub' => $child_string,
				);

			} else {

				$cat_name = array(
					'cat' => $parent,
					'sub' => '',
				);

			}
		} else {

			$cat_name = null;

		}

		return $cat_name;

	}


	/**
	 * Get the author of the Sensei course linked to the product
	 *
	 * @param  int $product_id the WC product ID.
	 *
	 * @return int the author ID.
	 */
	public function get_sensei_author( $product_id ) {

		global $wpdb;

		$courses = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT post_id
			FROM $wpdb->postmeta
			WHERE
			meta_key = '_course_woocommerce_product'
			AND meta_value = %d 
            ",
				$product_id
			)
		);

		if ( is_array( $courses ) && isset( $courses[0] ) ) {

			$course_id = get_object_vars( $courses[0] );
			$author    = get_post_field( 'post_author', $course_id['post_id'] );

			return $author;

		}

	}


	/**
	 * Fiscal fields names based on the specific plugins in use.
	 *
	 * @param  string $field the field to search.
	 *
	 * @return string the meta_key name to use to get the data from the database
	 */
	public function get_italian_tax_fields_names( $field ) {

		$cf_name      = null;
		$pi_name      = null;
		$pec_name     = null;
		$pa_code_name = null;

		/* Fields generated by the plugin */
		if ( get_option( 'wcexd_company_invoice' ) || get_option( 'wcexd_private_invoice' ) ) {

			$cf_name      = 'billing_wcexd_cf';
			$pi_name      = 'billing_wcexd_piva';
			$pec_name     = 'billing_wcexd_pec';
			$pa_code_name = 'billing_wcexd_pa_code';

		} else {

			/* Plugin supported */
			if ( class_exists( 'WC_BrazilianCheckoutFields' ) ) {

				/* WooCommerce Aggiungere CF e P.IVA */
				$cf_name = 'billing_cpf';
				$pi_name = 'billing_cnpj';

			} elseif ( class_exists( 'WooCommerce_Piva_Cf_Invoice_Ita' ) || class_exists( 'WC_Piva_Cf_Invoice_Ita' ) ) {

				/* WooCommerce P.IVA e Codice Fiscale per Italia */
				$cf_name      = 'billing_cf';
				$pi_name      = 'billing_piva';
				$pec_name     = 'billing_pec';
				$pa_code_name = 'billing_pa_code';

			} elseif ( function_exists( 'ywccp_init' ) ) {

				/* YITH WooCommerce Checkout Manager */
				$cf_name = 'billing_Codice_Fiscale';
				$pi_name = 'billing_Partita_IVA';

			} elseif ( function_exists( 'woocf_on_checkout' ) ) {

				/* WOO Codice Fiscale */
				$cf_name = 'billing_CF';
				$pi_name = 'billing_iva';

			} elseif ( class_exists( 'WooCommerce_Italian_add_on_plus' ) ) {

				/* WooCommerce Italian Add-on Plus */
				$cf_name      = 'billing_cf';
				$pi_name      = 'billing_cf'; // temp.
				$pec_name     = 'billing_PEC';
				$pa_code_name = 'billing_PEC';

			}
		}

		switch ( $field ) {
			case 'cf_name':
				return $cf_name;
			case 'pi_name':
				return $pi_name;
			case 'pec_name':
				return $pec_name;
			case 'pa_code_name':
				return $pa_code_name;
		}
	}


	/**
	 * Get the name of the price list cols base on the VAT
	 *
	 * @param  int $n the price list number.
	 *
	 * @return string
	 */
	public function get_prices_col_name( $n ) {

		$include_tax = get_option( 'woocommerce_prices_include_tax' );

		if ( 'yes' === $include_tax ) {

			return 'Listino ' . $n . ' (ivato)';

		} else {

			return 'Listino ' . $n;

		}

	}


	/**
	 * Get the attributes of the single product variation
	 *
	 * @param object $product      the WC product.
	 * @param bool   $is_variation variatio object with true.
	 *
	 * @return string
	 */
	public function get_product_notes( $product, $is_variation = false ) {

		if ( $is_variation ) {

			$parent = wc_get_product( $product->get_parent_id() );
			$output = array(
				'parent_id'  => $parent->get_id(),
				'parent_sku' => $parent->get_sku(),
			);

			if ( $product->get_attributes() ) {

				$output['var_attributes'] = $product->get_attributes();

				return json_encode( $output );

			}
		} else {

			$output = array();

			if ( $product->get_children() ) {

				$output['product_type'] = 'variable';

				if ( is_array( $product->get_attributes() ) ) {

					$attributes = array();

					foreach ( $product->get_attributes() as $key => $attr ) {

						if ( $attr->get_id() ) {

							$attributes[ $key ] = array_map( 'trim', explode( ', ', $product->get_attribute( $key ) ) );

						} else {

							$attributes[ $key ] = array_map( 'trim', explode( '|', $product->get_attribute( $key ) ) );

						}
					}

					$output['attributes'] = $attributes;

				}

				return json_encode( $output );

			}
		}
	}


	/**
	 * Get the Danea price list to be used as base price for the specific user level.
	 *
	 * @param string $billing_email the customer email.
	 *
	 * @return string
	 */
	public function get_the_price_list( $billing_email ) {

		/* Il listino di default per il prezzo base */
		$regular_price_list = get_option( 'wcifd-regular-price-list' );

		/* WooCommerce Role Based Price */
		$wc_rbp = function_exists( 'get_wc_rbp' ) ? get_wc_rbp() : null;

		/* Il plugin RBP non è installato */
		if ( ! $wc_rbp ) {

			$output = $regular_price_list;

		} elseif ( is_array( $wc_rbp ) ) {

			/* Recupero l'utente se registrato */
			$user = get_user_by( 'email', $billing_email );

			if ( is_object( $user ) && ! is_wp_error( $user ) ) {

				/* Ruolo utente */
				$role = isset( $user->roles[0] ) ? $user->roles[0] : null;

			} else {

				/* Ruolo utente */
				$role = 'logedout';

			}

			if ( $role ) {

				if ( array_key_exists( $role, $wc_rbp ) ) {

					/* Il listino di default previsto per questo livello utente */
					$result = isset( $wc_rbp[ $role ]['regular_price'] ) ? $wc_rbp[ $role ]['regular_price'] : $regular_price_list;
					$output = $result ? $result : $regular_price_list;

				} else {

					$output = $regular_price_list;
				}
			}

			return ( sprintf( 'Listino %s', intval( $output ) ) );

		}

	}

}

new WCEXD_Functions( true );

