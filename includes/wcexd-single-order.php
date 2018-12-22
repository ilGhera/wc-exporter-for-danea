<?php 
/**
 * Singolo ordine
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/includes
 * @version 1.1.0
 */


/*Formattazione data ordine*/
$originalDate = $order->post_date;
$newDate = date("Y-m-d", strtotime($originalDate));
$customer_name =  WCtoDanea::order_details($order->ID, '_billing_first_name') . ' ' . WCtoDanea::order_details($order->ID, '_billing_last_name'); 
$shipping_name =  WCtoDanea::order_details($order->ID, '_shipping_first_name') . ' ' . WCtoDanea::order_details($order->ID, '_shipping_last_name'); 

/*Recupero i nomi dei campi fiscali italiani*/
$cf_name      = '_' . WCtoDanea::get_italian_tax_fields_names('cf_name');
$pi_name      = '_' . WCtoDanea::get_italian_tax_fields_names('pi_name');
$pec_name     = '_' . WCtoDanea::get_italian_tax_fields_names('pec_name');
$pa_code_name = '_' . WCtoDanea::get_italian_tax_fields_names('pa_code_name');

/*Definisco il destinatario per la fattura elettronica*/
$e_invoice_receiver = WCtoDanea::order_details($order->ID, $pa_code_name) ? WCtoDanea::order_details($order->ID, $pa_code_name) : WCtoDanea::order_details($order->ID, $pec_name);
?>

<Document>
  <DocumentType>C</DocumentType>
  <CustomerWebLogin><?php echo (WCtoDanea::order_details($order->ID, '_customer_user') == 0) ? '' : WCtoDanea::order_details($order->ID, '_customer_user'); ?></CustomerWebLogin>
  <CustomerName><?php echo (WCtoDanea::order_details($order->ID, '_billing_company')) ? WCtoDanea::order_details($order->ID, '_billing_company') : $customer_name; ?></CustomerName>
  <CustomerAddress><?php echo WCtoDanea::order_details($order->ID, '_billing_address_1'); ?></CustomerAddress>
  <CustomerPostcode><?php echo WCtoDanea::order_details($order->ID, '_billing_postcode'); ?></CustomerPostcode>
  <CustomerCity><?php echo WCtoDanea::order_details($order->ID, '_billing_city'); ?></CustomerCity>
  <CustomerProvince><?php echo WCtoDanea::order_details($order->ID, '_billing_state'); ?></CustomerProvince>
  <CustomerCountry><?php echo WCtoDanea::order_details($order->ID, '_shipping_country'); ?></CustomerCountry>
  <CustomerVatCode><?php echo WCtoDanea::order_details($order->ID, $pi_name); ?></CustomerVatCode>
  <CustomerFiscalCode><?php echo WCtoDanea::order_details($order->ID, $cf_name); ?></CustomerFiscalCode>
  <CustomerEInvoiceDestCode><?php echo $e_invoice_receiver; ?></CustomerEInvoiceDestCode>
  <CustomerTel><?php echo WCtoDanea::order_details($order->ID, '_billing_phone'); ?></CustomerTel>
  <CustomerCellPhone></CustomerCellPhone>
  <CustomerEmail><?php echo WCtoDanea::order_details($order->ID, '_billing_email'); ?></CustomerEmail>
  <DeliveryName><?php echo (WCtoDanea::order_details($order->ID, '_shipping_company')) ? WCtoDanea::order_details($order->ID, '_shipping_company') : $shipping_name; ?></DeliveryName>
  <DeliveryAddress><?php echo WCtoDanea::order_details($order->ID, '_shipping_address_1'); ?></DeliveryAddress>
  <DeliveryPostcode><?php echo WCtoDanea::order_details($order->ID, '_shipping_postcode'); ?></DeliveryPostcode>
  <DeliveryCity><?php echo WCtoDanea::order_details($order->ID, '_shipping_city'); ?></DeliveryCity>
  <DeliveryProvince><?php echo WCtoDanea::order_details($order->ID, '_shipping_state'); ?></DeliveryProvince>
  <DeliveryCountry><?php echo WCtoDanea::order_details($order->ID, '_shipping_country'); ?></DeliveryCountry>
  <DeliveryTel></DeliveryTel>
  <DeliveryCellPhone></DeliveryCellPhone>
  <Date><?php echo $newDate; ?></Date>
  <Number><?php echo $order->order_id; ?></Number>
  <Total><?php echo WCtoDanea::order_details($order->ID, '_order_total'); ?></Total>
  <CostDescription><?php echo WCtoDanea::get_shipping_method_name($order->ID); ?></CostDescription>
<?php 
$cost_vat_code = null;
if(WCtoDanea::order_details($order->ID, '_order_shipping_tax') != 0) {
  $cost_vat_code = number_format(WCtoDanea::order_details($order->ID, '_order_shipping_tax') * 100 / WCtoDanea::order_details($order->ID, '_order_shipping'));
}
?>
  <CostVatCode><?php echo $cost_vat_code ? $cost_vat_code : 'FC'; ?></CostVatCode>
  <CostAmount><?php echo round(WCtoDanea::order_details($order->ID, '_order_shipping'), 2); ?></CostAmount>
  <PricesIncludeVat>false</PricesIncludeVat>
  <PaymentName><?php echo WCtoDanea::order_details($order->ID, '_payment_method_title'); ?></PaymentName>
  <InternalComment><?php echo $order->post_excerpt; ?></InternalComment>
  <CustomField2></CustomField2>
  <SalesAgent></SalesAgent>
  <Rows>
  <?php
  $items = WCtoDanea::get_order_items($order->order_id);
  foreach($items as $item) {
  $get_product_id = WCtoDanea::item_info($item['order_item_id'], '_product_id');
  $variation_id = wc_get_order_item_meta($item['order_item_id'], '_variation_id');

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
  $is_bundle = WCtoDanea::item_info($item['order_item_id'], '_bundled_items');  


  /*Verifico se il prodotto appartiene a un bundle*/
  $is_bundled = WCtoDanea::item_info($item['order_item_id'], '_bundled_by');


  /*Recupero i dettagli del singolo item*/
  $item_get_subtotal = WCtoDanea::item_info($item['order_item_id'], '_line_subtotal');
  $item_get_total = WCtoDanea::item_info($item['order_item_id'], '_line_total');
  $item_get_tax = WCtoDanea::item_info($item['order_item_id'], '_line_tax');
  $item_discount = wc_get_order_item_meta($item['order_item_id'], '_wcexd_item_discount');
  $tax_rate = get_option('wcexd-orders-tax-name') == 1 ? WCtoDanea::get_tax_rate($item_id, 'name') : WCtoDanea::get_tax_rate($item_id);
  $item_price = number_format($item_get_subtotal / WCtoDanea::item_info($item['order_item_id'], '_qty'), 2);
    

  /*Definisco prezzo e sconto*/
  $discount = null;
  if($item_discount && !$is_bundle) {
    $item_price = number_format( (($item_get_subtotal * 100) / (100 - $item_discount)) / WCtoDanea::item_info($item['order_item_id'], '_qty'), 2);
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
  if($item_get_subtotal != $item_get_total) {
    $cart_discount = number_format( (($item_get_subtotal - $item_get_total) / $item_get_subtotal * 100), 2);
    $discount = ($item_discount) ? $item_discount . '+' . $cart_discount . '%' : $cart_discount . '%';
  } ?>
    <Row>
      <Code><?php echo $product_id; ?></Code>
      <Description><?php echo esc_html($item['order_item_name']); ?></Description>
<?php echo $size; ?>
<?php echo $color; ?>
      <Qty><?php echo WCtoDanea::item_info($item['order_item_id'], '_qty'); ?></Qty>
      <Um>pz</Um>
      <Price><?php echo $item_price; ?></Price>
      <VatCode><?php echo $tax_rate; ?></VatCode>
      <Discounts><?php echo $discount; ?></Discounts>
    </Row>
<?php } ?>
  </Rows>
  <Payment>
    <Advance>false</Advance>
    <Date></Date>
    <Amount><?php echo WCtoDanea::order_details($order->ID, '_order_total'); ?></Amount>
    <Paid><?php echo(get_post_status($order->ID) == 'wc-completed') ? 'true' : 'false'; ?></Paid>
  </Payment>        
</Document>