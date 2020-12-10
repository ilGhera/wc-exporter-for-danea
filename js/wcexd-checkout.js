/**
 * Gestisce la visualizzazione dei campi fiscali in base al tipo di fattura selezionato
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/js
 * @since 1.2.7
 */
jQuery(document).ready(function($){

	var company_req, cf_opt ;
	var invoice_type    = $('#billing_wcexd_invoice_type');
	var company         = $('#billing_company_field');
	var company_opt     = $('label span.optional', company);
	var cf 		        = $('#billing_wcexd_cf_field');
	var p_iva 		    = $('#billing_wcexd_piva_field');
	var pec             = $('#billing_wcexd_pec_field');
	var receiver_code   = $('#billing_wcexd_pa_code_field');
	var billing_country = $('#billing_country');
	var cf_abbr         = $('label abbr.required', cf);
	var optional        = $(company_opt).text();

	/**
	 * Modifica l'obbligatorietà dei campi PEC e Codice destinatario in base ai campi attivati dall'admin
	 *
	 * @return {void}
	 */
	var check_pec_code_mandatory = function() {

		jQuery(function($){

			if ( 1 > $(pec).length ) {

				$('.optional', receiver_code).hide(); 

				if ( ! $('label abbr', receiver_code).hasClass('required') ) {

					$('label', receiver_code).append('<abbr class="required">*</abbr>');
				}

			} else if ( 1 > $(receiver_code).length ) {

				$('.optional', pec).hide(); 

				if ( ! $('label abbr', pec).hasClass('required') ) {

					$('label', pec).append('<abbr class="required">*</abbr>');

				}
			
			}

		})

	}

	/**
	 * Mostra solo i campi fiscali necessari
	 */
	var check_invoice_type = function() {

		jQuery(function($){

			cf_opt = $('label span.optional', cf);

			/*Mostro il codice fiscale*/
			if( ! cf.hasClass('wcexd-hidden-field') ) {
				
				cf.show();
			}
		
			if($(invoice_type).val() === 'private-invoice') {
				
				if ( null != company_req ) {
					company_req.hide();
				}

				company.show();
				company_opt.show();

				p_iva.hide();

				if( ! pec.hasClass('wcexd-hidden-field') ) {
					pec.show();
					receiver_code.show();

					check_pec_code_mandatory();				
				}

				cf_abbr.show();
				cf_opt.hide();

			} else if($(invoice_type).val() === 'private') {
				
				company.hide();
				p_iva.hide();
				pec.hide();
				receiver_code.hide();
				
				if ( options.cf_mandatory != 1 && options.cf_mandatory != 3 ) {

					/*Nascondi asterisco required*/
					cf_abbr.hide();

					if ( $(cf_opt).length ) {

						cf_opt.show();

					} else {

						$('label', cf).append( '<span class="optional">' + optional + '</span>' );

					}

				} else {

					cf_abbr.show();
					cf_opt.hide();

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

					check_pec_code_mandatory();

				}

				if ( options.cf_mandatory != 2 && options.cf_mandatory != 3 ) {

					/*Nascondi asterisco required*/
					cf_abbr.hide();

					if ( $(cf_opt).length ) {

						cf_opt.show();

					} else {

						$('label', cf).append( '<span class="optional">' + optional + '</span>' );

					}

				} else {

					cf_abbr.show();
					cf_opt.hide();

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
	
	/* Select style */
	$(invoice_type).select2();
	$('#billing_wcexd_invoice_type_field .select2').css('width', '100%');

	check_country_for_fields();
	check_invoice_type();


	$(billing_country).on('change', function(){
		
		check_country_for_fields();
		check_invoice_type();

	})


	/*Cambiamento tipo di documento*/
	$(invoice_type).on('change', function(){

		check_invoice_type();

	})

})