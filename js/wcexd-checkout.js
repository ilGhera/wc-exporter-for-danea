/**
 * Gestisce la visualizzazione dei campi fiscali in base al tipo di fattura selezionato
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/js
 * @version 1.1.9.1
 */
jQuery(document).ready(function($){

	var company_req;
	var invoice_type    = $('#billing_wcexd_invoice_type');
	var company         = $('#billing_company_field');
	var company_opt     = $('label span.optional', company);
	var cf 		        = $('#billing_wcexd_cf_field');
	var p_iva 		    = $('#billing_wcexd_piva_field');
	var pec             = $('#billing_wcexd_pec_field');
	var receiver_code   = $('#billing_wcexd_pa_code_field');
	var billing_country = $('select#billing_country');
	var cf_abbr         = $('label abbr.required', cf);

	/**
	 * Mostra solo i campi fiscali necessari
	 */
	var check_invoice_type = function() {

		jQuery(function($){

			/*Mostro il codice fiscale*/
			if( ! cf.hasClass('wcexd-hidden-field') ) {
				
				cf.show();
			}
		
			if($(invoice_type).val() === 'private-invoice') {
				
				company.show();
				company_req.hide();
				company_opt.show();

				p_iva.hide();

				if( ! pec.hasClass('wcexd-hidden-field') ) {
					pec.show();
					receiver_code.show();					
				}
			
			} else if($(invoice_type).val() === 'private') {
				
				company.hide();
				p_iva.hide();
				pec.hide();
				receiver_code.hide();
				
				console.log('mand: ' + options.cf_mandatory);

				if ( 0 == options.cf_mandatory ) {

					/*Nascondi asterisco required*/
					cf_abbr.hide();

				}

			} else {
				
				p_iva.show();

				company.show();
				company_opt.hide();

				if( null == company_req ) {

					$('label', company).append('<abbr class="required">*</abbr>');
					company_req = $('label .required', company);

				} else {

					company_req.show();

				}

				if( ! pec.hasClass('wcexd-hidden-field') ) {
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

			var is_italy = 'IT' === $(billing_country).val() ? true : false;

			/*Campi fattura elettronica*/
			if( 1 == options.only_italy && ! is_italy ) {

				pec.addClass('wcexd-hidden-field').hide();          
				receiver_code.addClass('wcexd-hidden-field').hide();		

			} else {

				pec.removeClass('wcexd-hidden-field');          
				receiver_code.removeClass('wcexd-hidden-field');		

			}

			/*Codice fiscale*/
			if( 1 == options.cf_only_italy && ! is_italy ) {

				cf.addClass('wcexd-hidden-field').hide();		

			} else {

				cf.removeClass('wcexd-hidden-field');

				if( ! is_italy ) {

					cf_abbr.hide();
	
				} else {
	
					/*Mostra asterisco required se in Italia*/
					cf_abbr.show();
	
				}
				
			}

		})

	}
	check_country_for_fields();
	check_invoice_type();


	/*Cambiamento paese*/
	// if( options.only_italy || options.cf_only_italy ) {

		$(billing_country).on('change', function(){
			
			check_country_for_fields();
			check_invoice_type();

		})

	// }


	/*Cambiamento tipo di documento*/
	$(invoice_type).on('change', function(){

		check_invoice_type();

	})

})