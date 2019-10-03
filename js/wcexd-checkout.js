/**
 * Gestisce la visualizzazione dei campi fiscali in base al tipo di fattura selezionato
 * @author ilGhera
 * @package wc-exporter-for-danea/js
 * @version 1.1.6.1
 */
jQuery(document).ready(function($){

	var invoice_type  = $('#billing_wcexd_invoice_type');
	var cf 		      = $('#billing_wcexd_cf_field');
	var p_iva 		  = $('#billing_wcexd_piva_field');
	var pec           = $('#billing_wcexd_pec_field');
	var receiver_code = $('#billing_wcexd_pa_code_field');
	var billing_country = $('select#billing_country');

	/**
	 * Mostra solo i campi fiscali necessari
	 */
	var check_invoice_type = function() {

		jQuery(function($){

			cf.show();
		
			if($(invoice_type).val() === 'private-invoice') {
				
				p_iva.hide();

				if( ! pec.hasClass('wxexd-hidden-field') ) {
					pec.show();
					receiver_code.show();					
				}
			
			} else if($(invoice_type).val() === 'private') {
				
				p_iva.hide();
				pec.hide();
				receiver_code.hide();

			} else {
				
				p_iva.show();

				if( ! pec.hasClass('wxexd-hidden-field') ) {
					pec.show();
					receiver_code.show();
				}
			
			}

		})
	}


	/**
	 * Visualizza i campi fiscali solo se il paese selezionato è l'Italia
	 */
	var check_country_for_fields = function() {

		jQuery(function($){

			if( options.only_italy && 'IT' !== $(billing_country).val() ) {

				pec.addClass('wxexd-hidden-field').hide();          
				receiver_code.addClass('wxexd-hidden-field').hide();		

			} else {

				pec.removeClass('wxexd-hidden-field');          
				receiver_code.removeClass('wxexd-hidden-field');		

			}

		})

	}
	check_country_for_fields();
	check_invoice_type();


	/*Cambiamento paese*/
	if( options.only_italy ) {

		$(billing_country).on('change', function(){
			
			check_country_for_fields();
			check_invoice_type();

		})

	}


	/*Cambiamento tipo di documento*/
	$(invoice_type).on('change', function(){

		check_invoice_type();

	})

})