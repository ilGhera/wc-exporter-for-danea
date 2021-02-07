/**
 * Admin JS
 *
 * @author ilGhera
 * @package wc-exporter-for-danea-premium/js
 * @since 1.2.0
 */

var wcexdAdminController = function() {

	var self = this;

	self.onLoad = function() {

		self.tabs_navigation();
		self.chosen();

	}


	/**
	 * Menu tabs navigation
	 */
	self.tabs_navigation = function() {
		
		jQuery(document).ready(function ($) {
			
			var $contents = $('.wcexd-admin')
			var url = window.location.href.split("#")[0];
			var hash = window.location.href.split("#")[1];

			if(hash) {
		        $contents.hide();		    
			    $('#' + hash).fadeIn(200);		
		        $('h2#wcexd-admin-menu a.nav-tab-active').removeClass("nav-tab-active");
		        $('h2#wcexd-admin-menu a').each(function(){
		        	if($(this).data('link') == hash) {
		        		$(this).addClass('nav-tab-active');
		        	}
		        })
		        
		        $('html, body').animate({
		        	scrollTop: 0
		        }, 'slow');
			}

			$("h2#wcexd-admin-menu a").click(function () {
		        var $this = $(this);
		        
		        $contents.hide();
		        $("#" + $this.data("link")).fadeIn(200);
		        $('h2#wcexd-admin-menu a.nav-tab-active').removeClass("nav-tab-active");
		        $this.addClass('nav-tab-active');

		        window.location = url + '#' + $this.data('link');

		        $('html, body').scrollTop(0);

		    })
	        	
		})
	}


	/**
	 * Fires Chosen
	 * @param  {bool} destroy method distroy
	 */
	self.chosen = function(destroy = false) {

		jQuery(function($){

			$('select.wcexd').chosen({
		
				disable_search_threshold: 10,
				width: '200px'
			
			});

		})

	}
}
	
/**
 * Class starter with onLoad method
 */
jQuery(document).ready(function($) {
	
	var Controller = new wcexdAdminController;
	Controller.onLoad();

});
