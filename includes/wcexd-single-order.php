<?php 
/**
 * Singolo ordine
 * @author ilGhera
 *
 * @package wc-exporter-for-danea-premium/includes
 * @since 1.5.1
*/

$order    = wc_get_order( $order );
$exchange = new WCEXD_Currency_Exchange( $order );
/*Formattazione data ordine*/
$originalDate = $order->get_date_created();
$newDate = date("Y-m-d", strtotime($originalDate));
$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
$shipping_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
$delivery_name = $order->get_shipping_company() ? $shipping_name . ' c/o ' . $order->get_shipping_company() : $shipping_name;

/*Prezzi a carrello IVA inclusa o meno*/
$tax_included = 'yes' === get_option( 'woocommerce_prices_include_tax' ) ? true : false;

/*Spese di spedizione*/
$cost_amount = round($order->get_shipping_total(), 2);
if ( $tax_included ) {
    $cost_amount = round($cost_amount + $order->get_shipping_tax(), 2);
}

/* Spese aggiuntive */
$fees = $order->get_fees();
if ( is_array( $fees ) ) {
    foreach ( $fees as $fee ) {
        $cost_amount += $fee->get_total() + $fee->get_total_tax();
    }
}

/*Recupero i nomi dei campi fiscali italiani*/
$cf_name      = '_' . WCtoDanea::get_italian_tax_fields_names('cf_name');
$pi_name      = '_' . WCtoDanea::get_italian_tax_fields_names('pi_name');
$pec_name     = '_' . WCtoDanea::get_italian_tax_fields_names('pec_name');
$pa_code_name = '_' . WCtoDanea::get_italian_tax_fields_names('pa_code_name');

/* error_log( 'META DATA: ' . $order->get_meta( 'wc-codice-docente' ) ); */

/*Recupero i campi fiscali*/
$cf      = $order->get_meta( $cf_name );
$pi      = is_numeric( $order->get_meta( $pi_name ) ) ? $order->get_meta( $pi_name ) : null;
$pec     = ! is_numeric( $order->get_meta( $pec_name ) ) ? $order->get_meta( $pec_name ) : null;
$pa_code = is_numeric( $order->get_meta( $pa_code_name ) ) ? $order->get_meta( $pa_code_name ) : null;


/*Definisco il destinatario per la fattura elettronica*/
$e_invoice_receiver = $order->get_meta( $pa_code_name ) ? $order->get_meta( $pa_code_name ) : $order->get_meta( $pec_name );
?>
<Document>
  <DocumentType>C</DocumentType>
  <CustomerWebLogin><?php echo ( 0 === $order->get_customer_id() ) ? '' : $order->get_customer_id(); ?></CustomerWebLogin>
  <CustomerName><?php echo esc_html( $customer_name ); ?></CustomerName>
  <CustomerAddress><?php echo $order->get_billing_address_1(); ?></CustomerAddress>
  <CustomerPostcode><?php echo $order->get_billing_postcode(); ?></CustomerPostcode>
  <CustomerCity><?php echo $order->get_billing_city(); ?></CustomerCity>
  <CustomerProvince><?php echo $order->get_billing_state(); ?></CustomerProvince>
  <CustomerCountry><?php echo $order->get_billing_country(); ?></CustomerCountry>
  <CustomerVatCode><?php echo $pi; ?></CustomerVatCode>
  <CustomerFiscalCode><?php echo strtoupper($cf); ?></CustomerFiscalCode>
  <CustomerEInvoiceDestCode><?php echo $e_invoice_receiver; ?></CustomerEInvoiceDestCode>
  <CustomerTel><?php echo $order->get_billing_phone(); ?></CustomerTel>
  <CustomerCellPhone></CustomerCellPhone>
  <CustomerEmail><?php echo $order->get_billing_email(); ?></CustomerEmail>
  <DeliveryName><?php echo $delivery_name; ?></DeliveryName>
  <DeliveryAddress><?php echo $order->get_shipping_address_1(); ?></DeliveryAddress>
  <DeliveryPostcode><?php echo $order->get_shipping_postcode(); ?></DeliveryPostcode>
  <DeliveryCity><?php echo $order->get_shipping_city(); ?></DeliveryCity>
  <DeliveryProvince><?php echo $order->get_shipping_state(); ?></DeliveryProvince>
  <DeliveryCountry><?php echo $order->get_shipping_country(); ?></DeliveryCountry>
  <DeliveryTel></DeliveryTel>
  <DeliveryCellPhone></DeliveryCellPhone>
  <Date><?php echo $newDate; ?></Date>
  <Number><?php echo $order->get_id(); ?></Number>
  <Total><?php echo $exchange->filter_price( $order->get_total() ); ?></Total>
  <CostDescription><?php echo WCtoDanea::get_shipping_method_name($order->get_id()); ?></CostDescription>
  <CostVatCode><?php echo WCtoDanea::get_shipping_tax_rate($order); ?></CostVatCode>
  <CostAmount><?php echo $exchange->filter_price( $cost_amount ); ?></CostAmount>
  <PricesIncludeVat><?php echo $tax_included ? 'true' : 'false'; ?></PricesIncludeVat>
  <PaymentName><?php echo $order->get_payment_method_title(); ?></PaymentName>
  <InternalComment><?php echo htmlspecialchars( html_entity_decode( $order->get_customer_note() ) ); ?></InternalComment>
  <CustomField1><?php $exchange->the_usd_exchange_rate(); ?></CustomField1>
  <PriceList><?php echo get_the_price_list( $order->get_billing_email() ); ?></PriceList>
  <Rows>
  <?php
  $items = WCtoDanea::get_order_items($order->get_id());

  foreach($items as $item) {

  $get_product_id = WCtoDanea::item_info($item->get_id(), '_product_id');
  $variation_id   = wc_get_order_item_meta($item->get_id(), '_variation_id');

  $from_danea = false;
  if($variation_id) {
    $obj = get_post($variation_id);
    if(isset($obj->post_name) && strpos($obj->post_name, 'danea') === 0) {
      $from_danea = true;
    }
  }

  /*Definisco se il prodotto è una variazione o un prodotto padre, utile per le taglia/ colore di Danea*/
  if($variation_id && !$from_danea) {
    $item_id = $variation_id;
  } else {
    $item_id = $get_product_id; 
  }

  /*SKU o ID*/
  if(get_post_meta($item_id, '_sku', true)) {
    $product_id = get_post_meta($item_id, '_sku', true);
  } else {
    $product_id = $item_id;
  }

  /*Verifico se il prodotto è un bundle*/
  $is_bundle = WCtoDanea::item_info($item->get_id(), '_bundled_items');  

  /*Verifico se il prodotto appartiene a un bundle*/
  $is_bundled = WCtoDanea::item_info($item->get_id(), '_bundled_by');

  /*Recupero i dettagli del singolo item*/
  $item_get_subtotal = WCtoDanea::item_info($item->get_id(), '_line_subtotal');
  $item_get_total    = WCtoDanea::item_info($item->get_id(), '_line_total');
  $item_get_tax      = WCtoDanea::item_info($item->get_id(), '_line_tax');
  $item_discount     = wc_get_order_item_meta($item->get_id(), '_wcexd_item_discount');
  $tax_rate          = WCtoDanea::get_item_tax_rate($order, $item);
  $item_price        = $order->get_item_subtotal( $item, $tax_included, 2 ); 

  /*Definisco prezzo e sconto*/
  $discount = null;
  if($item_discount && !$is_bundle) {
    $item_price = number_format( ( ($item_price * 100) / (100 - $item_discount) ), 2, '.', '' );
    $discount = $item_discount . '%';
  }

  /*Taglie e colori*/
  $size  = null;
  $color = null;
  if($variation_id) {
    $product_variation = new WC_Product_Variation($variation_id);

    /*Restituisce l'etichetta*/
    $attr_size = $product_variation->get_attribute('pa_size');
    $attr_color = $product_variation->get_attribute('pa_color');

    $size = "      <Size>" . ($attr_size ? $attr_size : '-') . "</Size>\n";
    $color = "      <Color>" . ($attr_color ? $attr_color : '-') . "</Color>\n";
  }

  $cart_discount = false;
  if( $item_get_subtotal && $item_get_subtotal != $item_get_total) {
    $cart_discount = number_format( ( ($item_get_subtotal - $item_get_total) / $item_get_subtotal * 100), 2, '.', '' );
    $discount = ($item_discount) ? $item_discount . '+' . $cart_discount . '%' : $cart_discount . '%';
  } ?>
    <Row>
      <Code><?php echo $product_id; ?></Code>
      <Description><?php echo wp_kses_post( strip_tags( html_entity_decode( $item['name'] ) ) ); ?></Description>
<?php echo $size; ?>
<?php echo $color; ?>
      <Qty><?php echo WCtoDanea::item_info($item->get_id(), '_qty'); ?></Qty>
      <Um>pz</Um>
      <Price><?php echo $exchange->filter_price( $item_price ); ?></Price>
      <VatCode><?php echo $tax_rate; ?></VatCode>
      <Discounts><?php echo $discount; ?></Discounts>
    </Row>
<?php } ?>
  </Rows>
  <Payment>
    <Advance>false</Advance>
    <Date></Date>
    <Amount><?php echo $exchange->filter_price ( WCtoDanea::order_details($order->get_id(), '_order_total') ); ?></Amount>
    <Paid><?php echo(get_post_status($order->get_id()) == 'wc-completed') ? 'true' : 'false'; ?></Paid>
  </Payment>        
</Document>
