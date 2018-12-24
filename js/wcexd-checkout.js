/**
 * Gestisce la visualizzazione dei campi fiscali in base al tipo di fattura selezionato
 * @author ilGhera
 * @package wc-exporter-for-danea/js
 * @version 1.1.0
 */
jQuery(document).ready(function($){

	var invoice_type = $('#billing_wcexd_invoice_type');
	var p_iva 		 = $('#billing_wcexd_piva_field');

	if($(invoice_type).val() === 'private') {
		p_iva.hide();
	}	

	$(invoice_type).on('change', function(){
		if($(this).val() === 'private') {
			p_iva.hide();
		} else {
			p_iva.show();
		}
	})
})