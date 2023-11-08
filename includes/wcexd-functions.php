<?php
/**
 * WCEXD Functions Class 
 *
 * @author ilGhera
 *
 * @package wc-exporter-for-danea-premium/includes
 * @since 1.5.1
 */

defined( 'ABSPATH' ) || exit;

class WCtoDanea {

	/**
	 * Recupero il valore dell'iva
	 *
	 * @param  int    $product_id l'id del prodotto.
	 * @param  string $type       il tipo di dato da restituire, nome o aliquota.
     *
	 * @return mixed
	 */
	public static function get_tax_rate( $product_id, $type = '' ) {

		$output = 'FC';

		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {

			$output     = 0;
            $product    = wc_get_product( $product_id );
			$tax_status = $product->get_tax_status();

			/*In caso di variazione recupero dati del prodotto padre*/
			$parent_id         = wp_get_post_parent_id( $product_id );
            $parent_product    = wc_get_product( $parent_id );
			$parent_tax_status = $parent_id ? $parent_product->get_tax_status() : '';

			if ( 'taxable' === $tax_status || ( '' == $tax_status && 'taxable' === $parent_tax_status ) ) {

				/*Valore nullo con iva al 22, controllo necessario in caso di varizione di prodotto*/
				$tax_class = $tax_status ? $product->get_tax_class() : $parent_product->get_tax_class();

				if ( 'parent' === $tax_class && 'taxable' === $parent_tax_status ) {

					$tax_class = $parent_product->get_tax_class();

				}

				global $wpdb;
				$query = "SELECT tax_rate, tax_rate_name FROM " . $wpdb->prefix . "woocommerce_tax_rates WHERE tax_rate_class = '" . $tax_class . "'";

				$results = $wpdb->get_results( $query, ARRAY_A );

				if ( $results ) {
					$output = 'name' === $type ? $results[0]['tax_rate_name'] : intval( $results[0]['tax_rate'] );
				}
			}
		}
		
        /* error_log( 'OUTPUT: ' . print_r( $output, true ) ); */
		return $output;

	}


	/**
	 * Get the order tax items
	 *
	 * @param object $order the WC order.		
     * @param bool $shipping get just tax classes used for shipping.
     *
	 * @return array
	 */
	public static function get_order_tax_items( $order, $shipping = false ) {

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
     * @param object $order the WC order
     *
     * @return array 
     */
    public static function get_shipping_tax_rate( $order ) {

		$output = 'FC';

		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {
			
			$use_label = get_option('wcexd-orders-tax-name');
			$tax_items = self::get_order_tax_items( $order, true );

			foreach( $tax_items as $rate_id => $tax ){

                if ( $use_label ) {

                    $output   = $tax_items[ $rate_id ]['label'];

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
     *
	 * @return string
	 */
	public static function get_item_tax_rate( $order, $item ) {

		$output = 'FC';

		if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {
			
			$use_label = get_option('wcexd-orders-tax-name');
			$tax_items = self::get_order_tax_items( $order );
			$taxes     = $item->get_taxes();

			foreach( $taxes['subtotal'] as $rate_id => $tax ){

                if ( $use_label ) {

                    $output   = $tax_items[ $rate_id ]['label'];

                } else {

                    $output = $tax_items[ $rate_id ]['percent'];

                }

			}

		}

		return $output;
	}


	/**
	 * Recupero il nome del metodo di spedizione
	 *
	 * @param  int $order_id l'id dell'ordine.
     *
	 * @return string
	 */
	public static function get_shipping_method_name( $order_id ) {

		global $wpdb;
        $order = wc_get_order( $order_id );

		$query = "SELECT * FROM " . $wpdb->prefix . "woocommerce_order_items WHERE order_id = $order_id AND order_item_type = 'shipping'";

		$items = $wpdb->get_results( $query, ARRAY_A );

		$output = $items ? $items[0]['order_item_name'] : null;
        /* error_log( 'SHIPPING METHOD NAME 1: ' . $output ); */

        $order->get_shipping_to_display( $order->get_id() );
        /* error_log( 'SHIPPING METHOD NAME 1: ' . $output ); */

        /* Spese aggiuntive */
        $fees  = $order->get_fees();
        
        if ( is_array( $fees ) ) {

            foreach ( $fees as $fee ) {

                $output .= ' + ' . $fee->get_name();

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
	public static function sort_sub_categories( $a, $b ) {

		if ( isset( $a->parent ) && isset( $b->parent ) ) {

			if ( $a->parent == $b->parent ) {

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
	public static function prepare_sub_categories( $child ) {

		$list = array();

		if ( ! empty( $child ) ) {

			usort( $child, array( 'WCtoDanea', 'sort_sub_categories' ) );

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
	public static function get_product_category_name( $product, $is_variation = false ) {

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

				if ( 0 != $cat->parent ) {

					$child[] = $cat;

					$get_parent = get_term_by( 'id', $cat->parent, 'product_cat' );
					$parent     = 0 === $get_parent->parent ? $get_parent->slug : $parent;

				} else {

					$parent = null === $parent ? $cat->slug : $parent;

				}

			}

			if ( $child ) {

				$child_string = self::prepare_sub_categories( $child ); // temp.

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
	public static function get_sensei_author( $product_id ) {

		global $wpdb;

        $query = $wpdb->prepare(
            "
			SELECT post_id
			FROM %s 
			WHERE
			meta_key = '_course_woocommerce_product'
			AND meta_value = %d 
            ",
            $wpdb->postmeta,
            $product_id
        );

		$courses = $wpdb->get_results( $query );

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
	public static function get_italian_tax_fields_names( $field ) {

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

			/* WooCommerce Aggiungere CF e P.IVA */
			if ( class_exists( 'WC_BrazilianCheckoutFields' ) ) {
				$cf_name = 'billing_cpf';
				$pi_name = 'billing_cnpj';
			}

			/* WooCommerce P.IVA e Codice Fiscale per Italia */
			elseif ( class_exists( 'WooCommerce_Piva_Cf_Invoice_Ita' ) || class_exists( 'WC_Piva_Cf_Invoice_Ita' ) ) {
				$cf_name      = 'billing_cf';
				$pi_name      = 'billing_piva';
				$pec_name     = 'billing_pec';
				$pa_code_name = 'billing_pa_code';
			}

			/* YITH WooCommerce Checkout Manager */
			elseif ( function_exists( 'ywccp_init' ) ) {
				$cf_name = 'billing_Codice_Fiscale';
				$pi_name = 'billing_Partita_IVA';
			}

			/* WOO Codice Fiscale */
			elseif ( function_exists( 'woocf_on_checkout' ) ) {
				$cf_name = 'billing_CF';
				$pi_name = 'billing_iva';
			}

			/* WooCommerce Italian Add-on Plus */
			elseif ( class_exists( 'WooCommerce_Italian_add_on_plus' ) ) {
				$cf_name      = 'billing_cf';
				$pi_name      = 'billing_cf'; // temp.
				$pec_name     = 'billing_PEC';
				$pa_code_name = 'billing_PEC';

			}

		}

		switch ( $field ) {
			case 'cf_name':
				return $cf_name;
				break;
			case 'pi_name':
				return $pi_name;
				break;
			case 'pec_name':
				return $pec_name;
				break;
			case 'pa_code_name':
				return $pa_code_name;
				break;
		}
	}


	/**
     * Get the name of the price list cols base on the VAT
	 *
	 * @param  int $n the price list number.
     *
	 * @return string
	 */
	public static function get_prices_col_name( $n ) {

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
	public static function get_product_notes( $product, $is_variation = false ) {

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

}


/**
 * Hide item discount
 *
 * @param array $array the hidden order item metas.
 * @return arra the array updated
 */
function wcexd_hide_item_discount( $array ) {

	$array[] = '_wcexd_item_discount';

	return $array;

}
add_filter( 'woocommerce_hidden_order_itemmeta', 'wcexd_hide_item_discount' );


/**
 * Salva nel db la percentuale di sconto per singolo item dell'ordine
 *
 * @param  int $order_id l'id dell'ordine.
 */
function wcifd_add_item_details( $order_id ) {

	$order     = new WC_Order( $order_id );
    $user_data = get_userdata( $order->get_user_id() ); 
    $user_role = isset( $user_data->roles[0] ) ? $user_data->roles[0] : null;

	foreach ( $order->get_items() as $key => $item ) {

        $regular_price = null;
        $price         = null;
        
		if ( 'line_item' === $item['type'] ) {

			if ( 0 != $item['variation_id'] ) {

                /*WooCommerce Role Based Price*/
                $wc_rbp = get_post_meta( $item['variation_id'], '_role_based_price', true );

                if ( $wc_rbp && isset( $wc_rbp[ $user_role ] ) ) {

                    $regular_price = isset( $wc_rbp[ $user_role ]['regular_price'] ) ? $wc_rbp[ $user_role ]['regular_price'] : null; 
                    $price         = isset( $wc_rbp[ $user_role ]['selling_price'] ) ? $wc_rbp[ $user_role ]['selling_price'] : null;

                } else {

                    $regular_price = $regular_price ? $regular_price : get_post_meta( $item['variation_id'], '_regular_price', true );
                    $price         = $price ? $price : get_post_meta( $item['variation_id'], '_price', true );
                    
                }

			} else {

                /*WooCommerce Role Based Price*/
                $wc_rbp = get_post_meta( $item['product_id'], '_role_based_price', true );

                if ( $wc_rbp && isset( $wc_rbp[ $user_role ] ) ) {

                    $regular_price = isset( $wc_rbp[ $user_role ]['regular_price'] ) ? $wc_rbp[ $user_role ]['regular_price'] : null; 
                    $price         = isset( $wc_rbp[ $user_role ]['selling_price'] ) ? $wc_rbp[ $user_role ]['selling_price'] : null;

                } else {

                    $regular_price = $regular_price ? $regular_price : get_post_meta( $item['product_id'], '_regular_price', true );
                    $price         = $price ? $price : get_post_meta( $item['product_id'], '_price', true );

                }
			}

			if ( $price && 0 < $regular_price ) {

				$math = $price * 100 / $regular_price;
				$discount = number_format( ( 100 - $math ), 2, '.', '' );

				wc_add_order_item_meta( $key, '_wcexd_item_discount', $discount );

			}
		}
	}
}
add_action( 'woocommerce_thankyou', 'wcifd_add_item_details', 10, 1 );


/**
 * Random string
 *
 * @param  int $length la lunghezza della stringa richiesta.
 * @return string
 */
function wcexd_rand_md5( $length ) {

	$max = ceil( $length / 32 );
	$random = '';

	for ( $i = 0; $i < $max; $i ++ ) {

		$random .= md5( microtime( true ) . mt_rand( 10000, 90000 ) );

	}

	return substr( $random, 0, $length );

}


/**
 * Modifico il nome del link di verifica aggiornamento
 */
function wcexd_check_update() {

	return __( 'Check for updates', 'wc-exporter-for-danea' );

}
add_filter( 'puc_manual_check_link-wc-exporter-for-danea-premium', 'wcexd_check_update' );


/**
 * Modifico il messaggio del risultato aggiornamento
 *
 * @param  string $message il messagio per l'admin.
 * @param  string $status  presenza o meno di un aggiornamento disponibile, errore.
 * @return string          il messaggio restituito
 */
function wcexd_update_message( $message = '', $status = '' ) {

	if ( 'no_update' === $status ) {

		$message = __( ' <strong>WooCommerce Exporter for Danea - Premium</strong> is up to date.', 'wc-exporter-for-danea' );

	} else if ( 'update_available' === $status ) {

		$message = __( 'A new version of <strong>WooCommerce Exporter for Danea - Premium</strong> is available.', 'wc-exporter-for-danea' );

	} else {

		$message = __( 'An error occurred, please try again later.', 'wc-exporter-for-danea' );

	}

	return $message;

}
add_filter( 'puc_manual_check_message-wc-exporter-for-danea-premium', 'wcexd_update_message', 10, 2 );


/**
 * Listino Danea per prezzo base livello utente
 *
 * @param string $billing_email the customer email.
 *
 * @return string
 */
function get_the_price_list( $billing_email ) {

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

