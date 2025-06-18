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
     * WCEXD Fee as item
     *
     * @var bool
     */
    public $fee_as_order_item;

	/**
	 * The constructor
	 *
	 * @param bool $init hooks with true.
	 *
	 * @return void
	 */
	public function __construct( $init = false ) {

		if ( $init ) {

			/* Actions */
			add_action( 'woocommerce_thankyou', array( $this, 'add_item_details' ), 10, 1 );

			/* Filters */
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_item_discount' ) );
			add_filter( 'puc_manual_check_link-wc-exporter-for-danea-premium', array( $this, 'check_update' ) );
			add_filter( 'puc_manual_check_message-wc-exporter-for-danea-premium', array( $this, 'update_message' ), 10, 2 );

		}

        $this->fee_as_order_item = get_option( 'wcexd-fee-as-item' );

	}

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
	 * Get the order tax items
	 *
	 * @param object $order the WC order.
	 * @param bool   $shipping get just tax classes used for shipping.
	 *
	 * @return array
	 */
	public function get_order_tax_items( $order, $shipping = false ) {

		$output = array();

		foreach ( $order->get_items( 'tax' ) as $tax_item ) {

			if ( $shipping ) {

				if ( $tax_item->get_shipping_tax_total() ) {

					$output[ $tax_item->get_rate_id() ] = array(
						'label'   => $tax_item->get_label(),
						'percent' => $tax_item->get_rate_percent(),
					);

				}
			} else {

				$output[ $tax_item->get_rate_id() ] = array(
					'label'   => $tax_item->get_label(),
					'percent' => $tax_item->get_rate_percent(),
				);

			}
		}

		return $output;

	}

	/**
	 * Get the shipping tax rate
	 *
	 * @param object $order the WC order.
	 *
	 * @return array
	 */
	public function get_shipping_tax_rate( $order ) {

		$output = 'FC';

		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {

			$use_label = get_option( 'wcexd-orders-tax-name' );
			$tax_items = self::get_order_tax_items( $order, true );

			foreach ( $tax_items as $rate_id => $tax ) {

				if ( $use_label ) {

					$output = $tax_items[ $rate_id ]['label'];

				} else {

					$output = $tax_items[ $rate_id ]['percent'];

				}
			}
		}

		return $output;

	}

	/**
	 * Get item vat percentage or label
	 *
	 * @param  object $order the wc order.
	 * @param  object $item  the specific order item.
     * @param  bool   $is_fee true if the item is a fee.
	 *
	 * @return string
	 */
	public function get_item_tax_rate( $order, $item, $is_fee = false ) {

		$output = 'FC';

		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {

			$use_label = get_option( 'wcexd-orders-tax-name' );
			$tax_items = self::get_order_tax_items( $order );
			$taxes     = $item->get_taxes();
            $key       = $is_fee ? 'total' : 'subtotal';

			foreach ( $taxes[ $key ] as $rate_id => $tax ) {

				if ( $use_label ) {

					$output = $tax_items[ $rate_id ]['label'];

				} else {

					$output = $tax_items[ $rate_id ]['percent'];

				}
			}
		}

		return $output;
	}

	/**
	 * Get the shipping method name plus fees
	 *
	 * @param object $order the WC order.
	 *
	 * @return string
	 */
	public function get_cost_description( $order ) {

		$output = $order->get_shipping_method();

        if ( ! $this->fee_as_order_item ) {

            /* Fees */
            $fees = $order->get_fees();

            if ( is_array( $fees ) ) {

                foreach ( $fees as $fee ) {

                    $output .= ' + ' . $fee->get_name();

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
	 * Save the discount percentage for the single product of the order.
	 *
	 * @param  int $order_id the WC order ID.
	 *
	 * @return void
	 */
	public function add_item_details( $order_id ) {

		$order     = wc_get_order( $order_id );
		$user_data = get_userdata( $order->get_user_id() );
		$user_role = isset( $user_data->roles[0] ) ? $user_data->roles[0] : null;

		foreach ( $order->get_items() as $key => $item ) {

			$regular_price = null;
			$price         = null;

			if ( 'line_item' === $item['type'] ) {

				if ( $item->get_variation_id() ) {

					/*WooCommerce Role Based Price*/
					$wc_rbp = get_post_meta( $item->get_variation_id(), '_role_based_price', true );

					if ( $wc_rbp && isset( $wc_rbp[ $user_role ] ) ) {

						$regular_price = isset( $wc_rbp[ $user_role ]['regular_price'] ) ? $wc_rbp[ $user_role ]['regular_price'] : null;
						$price         = isset( $wc_rbp[ $user_role ]['selling_price'] ) ? $wc_rbp[ $user_role ]['selling_price'] : null;

					} else {

						$regular_price = $regular_price ? $regular_price : get_post_meta( $item['variation_id'], '_regular_price', true );
						$price         = $price ? $price : get_post_meta( $item['variation_id'], '_price', true );

					}
				} else {

					/*WooCommerce Role Based Price*/
					$wc_rbp = get_post_meta( $item->get_product_id(), '_role_based_price', true );

					if ( $wc_rbp && isset( $wc_rbp[ $user_role ] ) ) {

						$regular_price = isset( $wc_rbp[ $user_role ]['regular_price'] ) ? $wc_rbp[ $user_role ]['regular_price'] : null;
						$price         = isset( $wc_rbp[ $user_role ]['selling_price'] ) ? $wc_rbp[ $user_role ]['selling_price'] : null;

					} else {

						$regular_price = $regular_price ? $regular_price : get_post_meta( $item['product_id'], '_regular_price', true );
						$price         = $price ? $price : get_post_meta( $item['product_id'], '_price', true );

					}
				}

				if ( $price && 0 < $regular_price ) {

					$math     = $price * 100 / $regular_price;
					$discount = number_format( ( 100 - $math ), 2, '.', '' );

					wc_add_order_item_meta( $key, '_wcexd_item_discount', $discount );

				}
			}
		}
	}

	/**
	 * Hide item discount
	 *
	 * @param array $array the hidden order item metas.
	 *
	 * @return arra
	 */
	public function hide_item_discount( $array ) {

		$array[] = '_wcexd_item_discount';

		return $array;

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

	/**
	 * Random string
	 *
	 * @param  int $length the string length.
	 *
	 * @return string
	 */
	public static function rand_md5( $length ) {

		$max    = ceil( $length / 32 );
		$random = '';

		for ( $i = 0; $i < $max; $i ++ ) {

			$random .= md5( microtime( true ) . mt_rand( 10000, 90000 ) );

		}

		return substr( $random, 0, $length );

	}

	/**
	 * Custom text for the search update link
	 *
	 * @return string
	 */
	public function check_update() {

		return __( 'Check for updates', 'wc-exporter-for-danea' );

	}

	/**
	 * Custom messages for the update result
	 *
	 * @param  string $message the message for the admin.
	 * @param  string $status  update available or error.
	 *
	 * @return string
	 */
	public function update_message( $message = null, $status = null ) {

		if ( 'no_update' === $status ) {

			$message = __( ' <strong>WooCommerce Exporter for Danea - Premium</strong> is up to date.', 'wc-exporter-for-danea' );

		} elseif ( 'update_available' === $status ) {

			$message = __( 'A new version of <strong>WooCommerce Exporter for Danea - Premium</strong> is available.', 'wc-exporter-for-danea' );

		} else {

			$message = __( 'An error occurred, please try again later.', 'wc-exporter-for-danea' );

		}

		return $message;

	}

}

new WCEXD_Functions( true );

