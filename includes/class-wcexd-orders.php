<?php
/**
 * Generate the order feed
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 *
 * @since 1.6.9
 */

defined( 'ABSPATH' ) || exit;

/**
 * WCEXD Orders
 *
 * @since 1.6.9
 */
class WCEXD_Orders {

	/**
	 * The plugin functions
	 *
	 * @var object
	 */
	public $functions;

	/**
	 * Prices including VAT or not
	 *
	 * @var bool
	 */
	public $tax_included;

	/**
	 * Numbering EC
	 *
	 * @var bool
	 */
	public $numbering;

	/**
	 * WCEXD Fee as item
	 *
	 * @var bool
	 */
	public $fee_as_order_item;

	/**
	 * The constructor
	 *
	 * @return void
	 */
	public function __construct() {

		/* Actions */
		add_action( 'init', array( $this, 'add_feed' ) );

		$this->functions         = new WCEXD_Functions( false );
		$this->tax_included      = 'yes' === get_option( 'woocommerce_prices_include_tax' ) ? true : false;
		$this->numbering         = get_option( 'wcexd-numbering' );
		$this->fee_as_order_item = get_option( 'wcexd-fee-as-item' );
	}

	/**
	 * Create the new feed.
	 *
	 * @return void
	 */
	public function add_feed() {

		$premium_key = strtolower( get_option( 'wcexd-premium-key' ) );
		$url_code    = strtolower( get_option( 'wcexd-url-code' ) );
		$feed_name   = $premium_key . $url_code;

		add_feed( $feed_name, array( $this, 'add_orders_feed' ) );

		/*Update permalinks*/
		global $wp_rewrite;

		$wp_rewrite->flush_rules();
	}

	/**
	 * The XML part about the customer
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 *
	 * @return void
	 */
	public function feed_customer_details( $writer, $order ) {

		$web_login     = 0 === $order->get_customer_id() ? '' : $order->get_customer_id();
		$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$customer_name = $order->get_billing_company() ? $order->get_billing_company() : $customer_name;

		/* Italian filscal fields names */
		$cf_name      = '_' . $this->functions->get_italian_tax_fields_names( 'cf_name' );
		$pi_name      = '_' . $this->functions->get_italian_tax_fields_names( 'pi_name' );
		$pec_name     = '_' . $this->functions->get_italian_tax_fields_names( 'pec_name' );
		$pa_code_name = '_' . $this->functions->get_italian_tax_fields_names( 'pa_code_name' );

		/* Italian filscal fields data */
		$cf      = $order->get_meta( $cf_name );
		$pi      = is_numeric( $order->get_meta( $pi_name ) ) ? $order->get_meta( $pi_name ) : null;
		$pec     = ! is_numeric( $order->get_meta( $pec_name ) ) ? $order->get_meta( $pec_name ) : null;
		$pa_code = is_numeric( $order->get_meta( $pa_code_name ) ) ? $order->get_meta( $pa_code_name ) : null;

		/* Use the receiver code if provided or the PEC address instead */
		$e_invoice_receiver = $order->get_meta( $pa_code_name ) ? $order->get_meta( $pa_code_name ) : $order->get_meta( $pec_name );

		$writer->writeElement( 'CustomerWebLogin', $web_login );
		$writer->writeElement( 'CustomerName', $customer_name );
		$writer->writeElement( 'CustomerAddress', $order->get_billing_address_1() );
		$writer->writeElement( 'CustomerPostcode', $order->get_billing_postcode() );
		$writer->writeElement( 'CustomerCity', $order->get_billing_city() );
		$writer->writeElement( 'CustomerProvince', $order->get_billing_state() );
		$writer->writeElement( 'CustomerCountry', $order->get_billing_country() );
		$writer->writeElement( 'CustomerVatCode', $pi );
		$writer->writeElement( 'CustomerFiscalCode', strtoupper( $cf ) );
		$writer->writeElement( 'CustomerEInvoiceDestCode', $e_invoice_receiver );
		$writer->writeElement( 'CustomerTel', $order->get_billing_phone() );
		$writer->writeElement( 'CustomerCellPhone', null );
		$writer->writeElement( 'CustomerEmail', $order->get_billing_email() );
	}

	/**
	 * The XML part about the delivery
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 *
	 * @return void
	 */
	public function feed_delivery_details( $writer, $order ) {

		$shipping_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
		$delivery_name = $order->get_shipping_company() ? $shipping_name . ' c/o ' . $order->get_shipping_company() : $shipping_name;
		$writer->writeElement( 'DeliveryName', $delivery_name );
		$writer->writeElement( 'DeliveryAddress', $order->get_shipping_address_1() );
		$writer->writeElement( 'DeliveryPostcode', $order->get_shipping_postcode() );
		$writer->writeElement( 'DeliveryCity', $order->get_shipping_city() );
		$writer->writeElement( 'DeliveryProvince', $order->get_shipping_state() );
		$writer->writeElement( 'DeliveryCountry', $order->get_shipping_country() );
		$writer->writeElement( 'DeliveryTel', null );
		$writer->writeElement( 'DeliveryCellPhone', null );
	}

	/**
	 * The cost amount of the order
	 *
	 * @param object $order  the WC order.
	 *
	 * @return float
	 */
	private function get_cost_amount( $order ) {

		/* Delivery cost */
		$cost_amount = round( $order->get_shipping_total(), 2 );

		if ( $this->tax_included ) {

			$cost_amount = round( $cost_amount + $order->get_shipping_tax(), 2 );
		}

		/* Fees */
		if ( ! $this->fee_as_order_item ) {

			$fees = $order->get_fees();

			if ( is_array( $fees ) ) {

				foreach ( $fees as $fee ) {

					$cost_amount += $fee->get_total() + $fee->get_total_tax();
				}
			}
		}

		return $cost_amount;
	}

	/**
	 * Add a single row for every fee
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 * @param object $fee the single fee.
	 *
	 * @return void
	 */
	private function feed_single_fee_as_row( $writer, $order, $fee ) {

		$vat_code  = $this->functions->get_item_tax_rate( $order, $fee, true );
		$fee_price = $this->tax_included ? $fee->get_total() + $fee->get_total_tax() : $fee->get_total();

		$writer->startElement( 'Row' );
		$writer->writeElement( 'Code', $fee->get_id() );
		$writer->writeElement( 'Description', $fee->get_name() );
		$writer->writeElement( 'Qty', 1 );
		$writer->writeElement( 'Price', $fee_price );
		$writer->writeElement( 'VatCode', $vat_code );
		$writer->endElement(); // Row.
	}

	/**
	 * The XML part about the generic info of the order
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 *
	 * @return void
	 */
	public function feed_order_details( $writer, $order ) {

		$original_date = $order->get_date_created();
		$new_date      = gmdate( 'Y-m-d', strtotime( $original_date ) );
		$exchange      = new WCEXD_Currency_Exchange( $order );

		$writer->writeElement( 'Date', $new_date );
		$writer->writeElement( 'Number', $order->get_id() );

		if ( $this->numbering ) {

			$writer->writeElement( 'Numbering', '-EC' );
		}

		$writer->writeElement( 'Total', $exchange->filter_price( $order->get_total() ) );
		$writer->writeElement( 'CostDescription', $this->functions->get_cost_description( $order ) ); // Temp.
		$writer->writeElement( 'CostVatCode', $this->functions->get_shipping_tax_rate( $order ) );
		$writer->writeElement( 'CostAmount', $exchange->filter_price( $this->get_cost_amount( $order ) ) );
		$writer->writeElement( 'PricesIncludeVat', $this->tax_included ? 'true' : 'false' );
		$writer->writeElement( 'PaymentName', $order->get_payment_method_title() );
		$writer->writeElement( 'InternalComment', htmlspecialchars( html_entity_decode( $order->get_customer_note() ) ) );
		$writer->writeElement( 'CustomField1', $exchange->get_the_usd_exchange_rate() );
		$writer->writeElement( 'PriceList', $this->functions->get_the_price_list( $order->get_billing_email() ) );
	}

	/**
	 * Get the discounts of the item
	 *
	 * @param object $item          the WC order item.
	 * @param bool   $regular_price return the item regular price with true.
	 *
	 * @return string
	 */
	private function get_item_discounts( $item, $regular_price = false ) {

		$output            = null;
		$cart_discount     = null;
		$product           = wc_get_product( $item->get_product_id() );
		$is_bundle         = $product && 'bundle' === $product->get_type() ? true : false;
		$item_get_subtotal = $item->get_subtotal();
		$item_get_total    = $item->get_total();
		$item_discount     = $item->get_meta( '_wcexd_item_discount', true ); // Temp.
		$item_discount     = number_format( floatval( $item_discount ), 2, '.', '' );
		$item_price        = $item->get_subtotal();
		$item_price        = $this->tax_included ? $item_price + $item->get_subtotal_tax() : $item_price;

		/* Item discount */
		if ( $item_discount && ! $is_bundle ) {

			$item_price = number_format( ( ( $item_price * 100 ) / ( 100 - $item_discount ) ), 2, '.', '' );

			/* Return the item regular price */
			if ( $regular_price ) {

				return $item_price;
			}

			/* Translators: the item discount */
			$output = sprintf( '%.2f%%', $item_discount );
		}

		/* Add the cart discount */
		if ( $item_get_subtotal && $item_get_subtotal !== $item_get_total ) {

			$cart_discount = number_format( ( ( $item_get_subtotal - $item_get_total ) / $item_get_subtotal * 100 ), 2, '.', '' );

			/* Translators: the item and the cart discount */
			$output = 0 < $item_discount ? sprintf( '%1$.2f+%2$.2f%%', $item_discount, $cart_discount ) : sprintf( '%.2f%%', $cart_discount );
		}

		return $output;
	}

	/**
	 * Get the item price before discount
	 *
	 * @param object $item the WC order item.
	 *
	 * @return float
	 */
	public function get_item_regular_price( $item ) {

		return $this->get_item_discounts( $item, true );
	}

	/**
	 * The XML part about the single item
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 * @param object $item   the WC order item.
	 *
	 * @return void
	 */
	public function feed_single_item_details( $writer, $order, $item ) {

		$code    = $item->get_product_id();
		$product = wc_get_product( $item->get_product_id() );
		$um      = 'pz';

		if ( is_object( $product ) ) {

			$code = $product->get_sku() ? $product->get_sku() : $code;
			$um   = $product->get_meta( '_wcifd-um' ) ? $product->get_meta( '_wcifd-um' ) : $um;
		}

		$variation_id = $item->get_variation_id();
		$hide_attr    = true;
		$vat_code     = $this->functions->get_item_tax_rate( $order, $item );
		$quantity     = $item->get_quantity();
		$price        = $this->get_item_regular_price( $item );
		$price        = $price / $quantity;
		$exchange     = new WCEXD_Currency_Exchange( $order );

		if ( $variation_id ) {

			$product_variation = new WC_Product_Variation( $variation_id );

			/* Get the attributes */
			$attr_size  = $product_variation->get_attribute( 'pa_size' );
			$size       = $attr_size ? $attr_size : '-';
			$attr_color = $product_variation->get_attribute( 'pa_color' );
			$color      = $attr_color ? $attr_color : '-';
			$hide_attr  = ! $attr_size && ! $attr_color ? true : false;

			if ( $hide_attr ) {

				/* Only size and color variations use parent sku */
				$code = $product_variation->get_sku() ? $product_variation->get_sku() : $variation_id;
			}
		}

		$writer->startElement( 'Row' );
		$writer->writeElement( 'Code', $code );
		$writer->writeElement( 'Description', wp_strip_all_tags( html_entity_decode( $item->get_name() ) ) );

		if ( ! $hide_attr ) {

			$writer->writeElement( 'Size', $size );
			$writer->writeElement( 'Color', $color );
		}

		$writer->writeElement( 'Qty', $quantity ); // Temp.
		$writer->writeElement( 'Um', $um );
		$writer->writeElement( 'Price', $exchange->filter_price( $price ) ); // Temp.
		$writer->writeElement( 'VatCode', $vat_code );
		$writer->writeElement( 'Discounts', $this->get_item_discounts( $item ) );
		$writer->endElement(); // Row.
	}

	/**
	 * The XML part about the order items
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 *
	 * @return void
	 */
	public function feed_items_details( $writer, $order ) {

		$writer->startElement( 'Rows' );

		foreach ( $order->get_items() as $item ) {

			/* Single item details */
			$this->feed_single_item_details( $writer, $order, $item );
		}

		/* Fees */
		if ( $this->fee_as_order_item ) {

			$fees = $order->get_fees();

			if ( is_array( $fees ) ) {

				foreach ( $fees as $fee ) {

					$this->feed_single_fee_as_row( $writer, $order, $fee );
				}
			}
		}

		$writer->endElement(); // Rows.
	}

	/**
	 * Get the orders
	 *
	 * @return array
	 */
	private static function get_orders() {

		$defaults = array_keys( wc_get_order_statuses() );

		/* The statuses if provided by the admin */
		$statuses = get_option( 'wcexd-orders-statuses' );

		if ( ! is_array( $statuses ) || in_array( 'any', $statuses, true ) ) {

			$statuses = $defaults;
		}

		$args = array(
			'limit'  => 150,
			'status' => $statuses,
		);

		if ( is_array( $statuses ) && ! empty( $statuses ) ) {

			$args['status'] = $statuses;
		}

		$orders = wc_get_orders( $args );

		if ( $orders ) {

			return $orders;
		}
	}

	/**
	 * The XML part about the order payment
	 *
	 * @param object $writer the xml writer.
	 * @param object $order  the WC order.
	 *
	 * @return void
	 */
	public function feed_payment_details( $writer, $order ) {

		$exchange = new WCEXD_Currency_Exchange( $order );
		$amount   = $exchange->filter_price( $order->get_total() );
		$paid     = 'completed' === $order->get_status() ? 'true' : 'false';

		$writer->startElement( 'Payment' );
			$writer->writeElement( 'Advance', 'false' );
			$writer->writeElement( 'Date', null );
			$writer->writeElement( 'Amount', $amount );
			$writer->writeElement( 'Paid', $paid );
		$writer->endElement(); // Payment.
	}

	/**
	 * The order XML feed.
	 *
	 * @return void
	 */
	public function add_orders_feed() {

		header( 'Content-Type: application/rss+xml; charset=UTF-8' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		$writer = new XMLWriter();

		$writer->openMemory();
		$writer->setIndent( 2 );
		$writer->setIndentString( '  ' );

		/* Start */
		$writer->startDocument( '1.0', 'UTF-8' );
		$writer->startElement( 'EasyfattDocuments' );
		$writer->writeAttribute( 'AppVersion', '2' );
		$writer->startElement( 'Documents' );

		/* Get orders */
		$orders = $this->get_orders();

		if ( is_array( $orders ) && ! empty( $orders ) ) {

			$include_tax = get_option( 'woocommerce_prices_include_tax' );

			foreach ( $orders as $order ) {

				if ( 'shop_order_refund' !== $order->get_type() && 'trash' !== $order->get_status() ) {

					$writer->startElement( 'Document' );
					$writer->writeElement( 'DocumentType', 'C' );

					/* Customer details */
					$this->feed_customer_details( $writer, $order );

					/* Delivery details */
					$this->feed_delivery_details( $writer, $order );

					/* Order details */
					$this->feed_order_details( $writer, $order );

					/* Items details */
					$this->feed_items_details( $writer, $order );

					/* Payment details */
					$this->feed_payment_details( $writer, $order );

					$writer->endElement(); // Document.
				}
			}
		}

		$writer->endElement(); // Documents.
		$writer->endElement(); // EasyFattDocuments.
		$writer->endDocument();

		echo $writer->outputMemory();
	}
}

new WCEXD_Orders();

