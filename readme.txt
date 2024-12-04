=== WooCommerce Exporter for Danea - Premium ===
Contributors: ghera74
Tags: WooCommerce, Danea, Easyfatt, ecommerce, exporter, csv, shop, orders, products, fattura elettronica, gestionale
Version: 1.6.7
Requires at least: 4.0
Tested up to: 6.7


Export suppliers, products, customers and orders from your WooCommerce store to Danea Easyfatt. 

----

Esporta fornitori, prodotti, clienti e ordini, dal tuo store WooCommerce a Danea Easyfatt.


== Description ==
If you've built your online store with WooCommerce and you're using Danea Easyfatt as management software, you definitely need WooCommerce Exporter for Danea - Premium!
You'll be able to export suppliers, products, clients and orders.

*Danea Easyfatt certified*
----

Se hai realizzato il tuo negozio online con WooCommerce ed utilizzi Danea Easyfatt come gestionale, WooCommerce Exporter per Danea è lo strumento che ti serve perché le due piattaforme siano in grado di comunicare.
WC Exporter for Danea - Premium ti permette di esportare:

* L'elenco dei fornitori, sotto forma di utenti WordPress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei prodotti WooCommerce (CSV).
* L'elenco dei clienti WooCommerce (CSV).
* L'elenco degli ordini WooCommerce, attraverso un feed (XML) che potrà essere usato in ogni momento in Danea per scaricare gli ordini effettuati sul tuo sito.

*Software certificato Danea Easyfatt*

== Installation ==

Upload the ‘woocommerce-exporter-for-danea-premium’ directory to your ‘/wp-content/plugins/’ directory, using your favorite method (ftp, sftp, scp, etc…)
Activate WooCommerce Exporter for Danea – Premium from your Plugins page.
Once Activated, go to WooCommerce/ WooCommerce Exporter for Danea.

----

Per installare WooCommerce Exporter for Danea, dalla Bacheca del tuo sito WordPress vai alla voce Plugin/ Aggiungi nuovo.
Clicca sul pulsante "Carica plugin" e seleziona la cartella compressa appena scaricata.
Completato il processo di installazione, troverai nel menù WooCommerce la pagina opzioni con tutte le informazioni necessarie all'utilizzo di WC Exporter for Danea.


== Changelog ==

= 1.6.7 =
Release Date: 4 December 2024

    * Bug Fix: Mandatory fields missed
    * Bug Fix: Plugin text-domain loaded too early 


= 1.6.6 =
Release Date: 21 November 2024

    * Bug Fix: Special chars not decoded in orders items description 


= 1.6.5 =
Release Date: 4 April 2024

    * Enhancement: New filter hook for download products types 
    * Enhancement: WordPress 6.5 support 
    * Update: Plugin Update Checker


= 1.6.4 =
Release Date: 19 December 2023 

    * Bug Fix: Bad SKU in orders in case of variations previously imported in Danea as single products 
    * Bug Fix: Variation with only size or color not reconized exporting orders


= 1.6.3 =
Release Date: 15 December 2023 

    * Bug Fix: Incorrect country format when exporting users 
    * Bug Fix: VAT missing when exporting products if a reference country has not been set 


= 1.6.2 =
Release Date: 7 December 2023 

    * Bug Fix: Incorrect discount when exporting orders when using WC Role Based Price


= 1.6.1 =
Release Date: 5 December 2023 

    * Bug Fix: Missed items discounts 
    * Bug Fix: Fatal error exporting refunded orders

= 1.6.0 =
Release Date: 3 December 2023 

    * Enhancement: WooCommerce HPOS compatibility 
    * Enhancement: Export size/color variations 
    * Enhancement: WP Coding Standard 
    * Update: Plugin Update Checker
    * Update: Translations
    * Bug Fix: Product SKU not exported with variations


= 1.5.1 =
Release Date: 9 September 2023 

    * Enhancement: Ready to be traslated on WordPress.org
    * Update: Plugin Update Checker
    * Update: Translations
    * Bug Fix: HTML entities in title with products previously imported from Danea Easyfatt  


= 1.5.0 =
Release Date: 24 June 2023 

    * Enhancement: Export USD orders in EUR to Danea with a new exchange rate option
    * Bug Fix: Tax class applied to shipping costs even when not set in the options
    * Bug Fix: HTML tags in product name 


= 1.4.15 =
Release Date: 31 March 2023 

    * Enhancement: WordPress 6.2 support 
    * Bug Fix: JS error "Cannot read properties of undefined (reading 'toUpperCase')" 


= 1.4.14 =
Release Date: 22 March 2023 

    * Bug Fix: Wrong tax class applied to order items in case of zero tax rate 


= 1.4.13 =
Release Date: 13 March 2023 

    * Enhancement: Fiscal code in capital letters 
    * Enhancement: Default receiver code in capital letters 
    * Update: Plugin Update Checker
    * Bug Fix: Receiver code field not hidden with private invoices
    * Bug Fix: Missing translations 


= 1.4.12 =
Release Date: 15 February 2023 

    * Enhancement: New admin notice for license key
    * Update: Plugin Update Checker
    * Bug Fix: Nonce missed in premium key form 


= 1.4.11 =
Release Date: 9 January 2023 

    * Bug Fix: Error with special chars in order notes
    * Update: Plugin Update Checker


= 1.4.10 =
Release Date: 11 November 2022 

    * Bug Fix: Wrong tax rate applied in back-end orders


= 1.4.9 =
Release Date: 7 November 2022 

    * Enhancement: WordPress 6.1 support 
    * Update: Plugin Update Checker 


= 1.4.8 =
Release Date: 30 September 2022 

    * Enhancement: Display the attributes in the product's name of the variations exported


= 1.4.7 =
Release Date: 12 September 2022 

    * Enhancement: Export of the extra fees of the order 
    * Enhancement: Plugin Update Checker updated


= 1.4.6 =
Release Date: 7 April 2022 

    * Bug Fix: Double asterisk in Company checkout field when set as required in WooCommerce options


= 1.4.5 =
Release Date: 6 April 2022 

    * Bug fix: Tax Classes with value equal to zero always exported ad FC 
    * Bug fix: Decimal numbers missed in order item discount
    * Bug fix: Billing information title displayed even with no values in thank you page
    * Bug fix: Billing information title displayed even with no values in emails


= 1.4.4 =
Release Date: 7 February 2022 

    * Enhancement: Plugin Update Checker updated
    * Bug fix: Incorrect unit of weight in export products
    * Bug fix: Error exporting float values as weight or product dimensions


= 1.4.3 =
Release Date: 19 January 2022 

    * Bug fix: Error caused by the thousands separator in the product price


= 1.4.2 =
Release Date: 18 January 2022 

    * Enhancement: VAT code is now used even for the shipping cost
    * Bug fix: Wrong discounts with multi-item orders
    * Bug fix: Wrong shipping cost with tax included set


= 1.4.1 =
Release Date: 20 November 2021 

    * Bug fix: Wrong discount using the plugin WC Role Based Price


= 1.4.0 =
Release Date: 7 October 2021 

    * Enhancement: Danea Easyfatt certification
    * Enhancement: The DeliveryName field now includes the customer name in addition to the company name when present
    * Enhancement: Prices are now exported including VAT or not based on WooCommerce settings
    * Bug fix: Failure to export special chars in order products name


= 1.3.5 =
Release Date: 29 April 2021 

    * Enhancement: Default receiver code added automatically when necessary
    * Bug fix: Bad email address allowed in PEC field


= 1.3.4 =
Release Date: 16 February 2021 

    * Bug fix: Billing data entered not recognized 


= 1.3.3 =
Release Date: 7 February 2021 

    * Enhancement: Generic code improvements
    * Bug fix: VIES check not working in some cases with italian VAT number
    * Bug fix: Warning calculating doscount with free products
    * Bug fix: Billing information not required by the selected document type


= 1.3.2 =
Release Date: 14 January 2021 

    * Bug fix: Select2 presence check missed


= 1.3.1 =
Release Date: 8 January 2021 

    * Enhancement: UK removed from EU countries


= 1.3.0 =
Release Date: 11 December, 2020

    * Enhancement: VIES VAT number validation
    * Enhancement: New option for mandatory fiscal code
    * Enhancement: New option for mandatory VAT code only in European Union countries
    * Bug fix: Company name field hidden with private invoice 


= 1.2.9 =
Release Date: 14 October, 2020

    * Bug fix: Variable product custom attributes ingnored in products download


= 1.2.8 =
Release Date: 27 September, 2020

    * Bug fix: WooCommerce tax settings ignored in products download
    * Bug fix: Product sale price not exported 


= 1.2.7 =
Release Date: 08 September, 2020

    * Bug fix: Receiver code and PEC not set to mandatory when used individually


= 1.2.6 =
Release Date: 26 August, 2020

    * Enhancement: WooCommerce Italian Add-on Plus plugin in now supported
    * Bug fix: Incorrect Tax Rate assigned to order items with different Tax Zones set 


= 1.2.5 =
Release Date: 08 June, 2020

    * Bug fix: Incorrect ISO country code exporting orders


= 1.2.4 =
Release Date: 21 April, 2020

    * Bug Fix: PEC and Receiver Code not shown if Selling location(s) option in WC is set to a specific country 


= 1.2.3 =
Release Date: 05 March, 2020

    * Bug Fix: PEC and Receiver Code mandatory also for Private when not both activated


= 1.2.2 =
Release Date: 19 November, 2019

    * Bug Fix: "PHP Fatal error: Allowed memory size..." in some cases on exporting orders


= 1.2.1 =
Release Date: 15 November, 2019

    * Bug Fix: "Invalid argument supplied for foreach()... " with no orders status selected
    * Bug Fix: "PHP Fatal error:  Uncaught Error: Call to a member function get_title() on bool" getting the order item name


= 1.2.0 =
Release Date: 14 November, 2019

    * Enhancement: Multiselect for exporting orders with different statuses
    * Enhancement: Option to deactivate fiscal code outside Italy
    * Enhancement: Generic recipient code suggested in case of empty fields
    * Enhancement: Code improvements
    * Bug Fix: Billing company not mandatory with company invoice selected
    * Bug Fix: Sub-categories not fully exported in download products


= 1.1.9.1 =
Release Date: 03 October, 2019

    * Bug Fix: Wrong checkout fields with Private receipt selected


= 1.1.9 =
Release Date: 01 October, 2019

    * Enhancement: Possibility to put the invoice document type select field at the top of the form
    * Enhancement: Plugin custom fields are now editable from the user profile page
    * Enhancement: PEC and Receiving code can now be asked only to Italians customers
    * Bug Fix: Order full country name as required from Danea ver. 2019.45c
    * Bug Fix: "Electronic invoice" shown in Thank you page, even if no plugin checkout fields were used  
    * Bug Fix: PEC and Receiver code not exported with the customers data


= 1.1.8 =
Release Date: 14 February, 2019

    * Bug Fix: Typo in parent_id key in products download


= 1.1.7 =
Release Date: 12 February, 2019

    * Bug Fix: Fiscal code mandatory for private invoices even if not set in the plugin options


= 1.1.6 =
Release Date: 11 February, 2019

    * Bug Fix: Notice PHP on class_exists() function


= 1.1.5 =
Release Date: 07 January, 2019

    * Bug Fix: PEC or Receiver code required even for private customers 


= 1.1.4 =
Release Date: 04 January, 2019

    * Enhancement: Allow private purchases with no PEC or Receiver code required


= 1.1.3 =
Release Date: 02 January, 2019

    * Bug Fix: Wrong tax class exported for products variables 


= 1.1.2 =
Release Date: 29 December, 2018


    * Enhancement: Improved suppliers and clients download function
    * Bug Fix: Wrong variable used for "Referente" in clients download 


= 1.1.1 =
Release Date: 27 December, 2018

    * Enhancement: Plugin "WooCommerce P.IVA e Codice Fiscale per Italia support" updated


= 1.1.0 =
Release Date: 23 December, 2018

    * Enhancement: Fiscal code and P.IVA fields are now available width a dedicated option
    * Enhancement: Fiscal code and P.IVA fields validation tool.
    * Enhancement: PEC and Receiver code fields are now available, ready for E-Invoice
    * Enhancement: Better plugin settings navigation


= 1.0.1 =
Release Date: 09 April, 2018

    * Bug Fix: Special characters in product names stop the transfer of orders.  
    * Bug Fix: PHP Notices


= 1.0.0 =
Release Date: 15 January, 2018

    * Enhancement: Export product weight and measures with gross or net values.
    * Enhancement: New orders feed url, created from the user's Premium Key and a random code
    * Enhancement: Use the tax name instead of the value, useful if they were imported previously from Danea Easyfatt.
    * Bug Fix: PHP Notices and deprecated functions.


= 0.9.7.1 =
Release Date: 24 July, 2017

    * Bug Fix: Shipping cost must be passed to Danea as it is.


= 0.9.7 =
Release Date: 21 July, 2017

    * Bug Fix: Shipping cost amount with tax included.
    * Bug Fix: jQuery conflict for the plugin menu tabs.
    * Bug Fix: Order product name sanitized.
    * Bug Fix: Danea variation products (size & color) not reconized with just one attribute.


= 0.9.6 =
Release Date: 26 April, 2017

    * Bug Fix: Wrong discount for bundled items in orders feed.
    * Bug Fix: Custom order item meta visible in back-end order page.


= 0.9.5 =
Release Date: 11 April, 2017

    * Bug Fix: Product parent categories not exported if not set.


= 0.9.4 =
Release Date: 09 April, 2017

    * Enhancement: Added support to Wocommerce 3.0
    * Enhancement: Now are exported also the subcategories.
    * Enhancement: Products variable attributes are transferred to Danea using the Notes field.
    * Enhancement: Shop manager can now handle the plugin options.
    * Enhancement: Now Danea variations size and color are supported
    * Enhancement: Exporting the products is possible to exclude the variations created by Danea (Size and Color).
    * Enhancement: Better tabs navigation.
    * Bug Fix: Trashed orders in the order feed.
    * Bug Fix: Decimal numbers in CostVatCode.
    * Bug Fix: Product and cart discount wrong combination in orders feed.
    * Bug Fix: CostAmount and CostVatCode
    * Bug Fix: Product variations exported even if the parent is not published.


= 0.9.3 =
Release Date: 14 March, 2017

    * Bug Fix: Names of customers and suppliers absent in certain cases
    * Bug Fix: Column label 'Pubblicaz. su web' changed in 'E-commerce'
    * Bug Fix: Columns labels 'Listino 1', 'Listino 2', ... become 'Listino 1 (ivato)', ... with tax included.
    * Bug Fix: The product type now change from 'Articolo' to 'Art. con magazzino' with manage stock.
    * Bug Fix: Problem with product price and discount.
    * Bug Fix: Added "PricesIncludeVat" to the orders feed to indicate to Danea that the prices are inclusive of tax.
    * Bug Fix: Paid set to true for order status wc-completed
    * Bug Fix: Error importing products in Danea for unknown suppliers; now using the product's author as supplier is an option. 
    * Bug Fix: CostVatCode must be a percentage.


= 0.9.2 =
Release Date: 31 October, 2016

    * Bug Fix: YITH WooCommerce Checkout Manager fields not recognised.
    * Bug Fix: Orders items prices equal to zero when the sku is present.



= 0.9.1 =
Release Date: 27 October, 2016
		
    * Enhancement: If the Company field is presents, the name will be moved to referent.
    * Enhancement: Added the Shipping address.
    * Enhancement: Added the shipping costs.
    * Enhancement: Added the customer comments in every single order.
    * Enhancement: Products export improved with all them variations.
    * Enhancement: If present, the SKU will be used as product id, instead of the WordPress post id.
    * Enhancement: Now you can choose if export orders, completed orders or both.
    * Enhancement: Fiscal code and P.IVA fields are now recognized by checking the specific plugin installed.
    * Bug Fix: The db query didn't work in case the tables prefix was different than wp_
    * Bug Fix: The customer web login was always 0 if the user was not registered. This were causing a continuing overwrite.
		

= 0.9.0 =
Release Date: 31 May, 2016

    * First release

