=== WooCommerce Exporter for Danea ===
Contributors: ghera74
Tags: Fattura elettronica, WooCommerce, Danea Easyfatt, ecommerce, exporter, csv, shop, orders, products, gestionale
Version: 1.4.2
Requires at least: 4.0
Tested up to: 5.9

Export suppliers, products, customers and orders from your Woocommerce store to Danea.

== Description ==

Se hai realizzato il tuo negozio online con WooCommerce ed utilizzi Danea come gestionale, WooCommerce Exporter per Danea è quello che ti serve!
Nella versione Free, permette di esportare un elenco di utenti Wordpress come fornitori, e l'elenco dei tuoi prodotti.
Nella versione Premium, potrai esportare anche clienti e ordini.

*Software certificato Danea Easyfatt*

Ecco il dettaglio dei contenuti che è possibile esportare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei prodotti Woocommerce (CSV).
* Premium - L'elenco dei clienti Woocommerce (CSV).
* Premium - L'elenco degli ordini Woocommerce, attraverso un feed (xml) che potrà essere usato in ogni momento in Danea per scaricare gli ordini effettuati sul tuo sito.


**FATTURAZIONE ELETTRONICA**

* (Premium) Possibilità di esportare ordini con stato differente
* Possibilità di disattivare il codice fiscale per gli acquisti esteri
* Nel caso in cui l'utente non inserisca PEC e Codice Destinatario, il sistema suggerisce ora il Codice destinatario generico 

Fatturazione elettronica con Danea Easyfatt

https://youtu.be/tLWc_1i7778


**ENGLISH**

If you've built your online store with Woocommerce and you're using Danea as management software, you definitely need Woocommerce Exporter for Danea!
With this Free version you can export the suppliers and the products from your store.
With Premium version, you'll be able also to export clients and orders.

*Danea Easyfatt certified*

**ELECTRONIC INVOICE**

* (Premium) Multiselect for exporting orders with different statuses
* Option to deactivate fiscal code outside Italy
* Generic recipient code suggested in case of empty fields


== Installation ==
From your WordPress dashboard

* Visit 'Plugins > Add New'
* Search for 'WooCommerce Exporter for Danea' and download it.
* Activate WooCommerce Exporter for Danea from your Plugins page.
* Once Activated, go to WooCommerce/ WooCommerce Exporter for Danea.


From WordPress.org

* Download WooCommerce Exporter for Danea
* Upload the 'woocommerce-exporter-for-danea' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
* Activate WooCommerce Exporter for Danea from your Plugins page.
* Once Activated, go to WooCommerce/ WooCommerce Exporter for Danea.


== Screenshots ==
1. Activate custom checkout fields
2. Choose the user role and download your updated suppliers list
3. Download your updated products list
4. Export orders to Danea Easyfatt (Premium)


== Changelog ==


= 1.4.2 =
Release Date: 7 April 2022 

    * Bug fix: Billing information title displayed even with no values in thank you page
    * Bug fix: Billing information title displayed even with no values in emails
    * Bug fix: (Premium) Tax Classes with value equal to zero always exported ad FC 
    * Bug fix: (Premium) Decimal numbers missed in order item discount


= 1.4.1 =
Release Date: 20 February 2022 

* Enhancement: New admin sidebar contents
* Enhancement: New go premium button in admin
* Enhancement: WordPress 5.9 support
* Enhancement: WooCommerce 6 support


= 1.4.0 =
Release Date: 7 October 2021 

* Enhancement: Danea Easyfatt certification
* Enhancement: (Premium) The DeliveryName field now includes the customer name in addition to the company name when present
* Enhancement: (Premium) Prices are now exported including VAT or not based on WooCommerce settings
* Bug fix: (Premium) Failure to export special chars in order products name


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
* Bug fix: Billing information not required by the selected document type


= 1.3.2 =
Release Date: 14 January 2021 

* Bug fix: Select2 presence check missed


= 1.3.1 =
Release Date: 8 January 2021 

* Enhancement: UK removed from EU countries


= 1.3.0 =
Release Date: 12 December, 2020

* Enhancement: VIES VAT number validation
* Enhancement: New option for mandatory fiscal code
* Enhancement: New option for mandatory VAT code only in European Union countries
* Bug fix: Company name field hidden with private invoice 


= 1.2.4 =
Release Date: 14 October, 2020

* Bug fix: Variable product custom attributes ingnored in products download


= 1.2.3 =
Release Date: 27 September, 2020

* Bug fix: WooCommerce tax settings ignored in products download
* Bug fix: Product sale price not exported 


= 1.2.2 =
Release Date: 08 September, 2020

* Enhancement: WooCommerce Italian Add-on Plus plugin in now supported
* Bug fix: Receiver code and PEC not set to mandatory when used individually


= 1.2.1 =
Release Date: 05 March, 2020

* Bug Fix: PEC and Receiver Code mandatory also for Private when not both activated


= 1.2.0 =
Release Date: 6 February, 2020

* Enhancement: (Premium) Multiselect for exporting orders with different statuses
* Enhancement: Option to deactivate fiscal code outside Italy
* Enhancement: Generic recipient code suggested in case of empty fields
* Enhancement: Code improvements
* Bug Fix: Billing company not mandatory with company invoice selected


= 1.1.6.1 =
Release Date: 03 October, 2019

* Bug Fix: Wrong checkout fields with Private receipt selected


= 1.1.6 =
Release Date: 01 October, 2019

* Enhancement: Possibility to put the invoice document type select field at the top of the form
* Enhancement: Plugin custom fields are now editable from the user profile page
* Enhancement: PEC and Receiving code can now be asked only to Italians customers
* Bug Fix: (Premium) Order full country name as required from Danea ver. 2019.45c
* Bug Fix: (Premium) "Electronic invoice" shown in Thank you page, even if no plugin checkout fields were used  
* Bug Fix: (Premium) PEC and Receiver code not exported with the customers data


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

* Enhancement: Improved suppliers download function 


= 1.1.1 =
Release Date: 27 December, 2018

* Enhancement: Plugin "WooCommerce P.IVA e Codice Fiscale per Italia support" updated


= 1.1.0 =
Release Date: 23 December, 2018

* Enhancement: Fiscal code and P.IVA fields are now available width a dedicated option
* Enhancement: Fiscal code and P.IVA fields validation tool.
* Enhancement: PEC and Receiver code fields are now available, ready for E-Invoice
* Enhancement: Better plugin settings navigation


= 1.0.0 =
Release Date: 18 January, 2018

* Enhancement: Export product weight and measures with gross or net values.
* Enhancement: Use the tax name instead of the value, useful if they were imported previously from Danea Easyfatt.
* Enhancement: (Premium) New orders feed url, created from the user's Premium Key and a random code
* Bug Fix: PHP Notices and deprecated functions.


= 0.9.5 =
Release Date: 11 April, 2017

* Bag Fix: Product parent categories not exported if not set.


= 0.9.4 =
Release Date: 09 April, 2017

* Enhancement: Added support to Wocommerce 3.0
* Enhancement: Now are exported also the subcategories.
* Enhancement: Shop manager can now handle the plugin options.
* Enhancement: Now Danea variations size and color are supported
* Enhancement: Exporting the products is possible to exclude the variations created by Danea (Size and Color).
* Enhancement: Better tabs navigation.
* Enhancement: Products variable attributes are transferred to Danea using the Notes field.
* Bag Fix: Product variations exported even if the parent is not published.
* Bag Fix: (Premium) Trashed orders in the order feed.
* Bag Fix: (Premium) Decimal numbers in CostVatCode.
* Bag Fix: (Premium) Product and cart discount wrong combination in orders feed.
* Bag Fix: (Premium) CostAmount and CostVatCode


= 0.9.3 =
Release Date: 15 March, 2017

* Bag Fix: Names of customers and suppliers absent in certain cases
* Bag Fix: Column label 'Pubblicaz. su web' changed in 'E-commerce'
* Bag Fix: Columns labels 'Listino 1', 'Listino 2', ... become 'Listino 1 (ivato)', ... with tax included.
* Bag Fix: The product type now change from 'Articolo' to 'Art. con magazzino' with manage stock.
* Bag Fix: Error importing products in Danea for unknown suppliers; now using the product's author as supplier is an option. 
* Bag Fix: (Premium) Problem with product price and discount.
* Bag Fix: (Premium) Added "PricesIncludeVat" to the orders feed to indicate to Danea that the prices are inclusive of tax.
* Bag Fix: (Premium) Paid set to true for order status wc-completed
* Bag Fix: (Premium) CostVatCode must be a percentage.


= 0.9.2 =
Release Date: 31 October, 2016

* Bug Fix: YITH WooCommerce Checkout Manager fields not recognised.


= 0.9.1 =
Release Date: 27 October, 2016
		
* Enhancement: If the Company field is presents, the name will be moved to referent.
* Enhancement: Added the Shipping address.
* Enhancement: Products export improved with all them variations.
* Enhancement: If present, the SKU will be used as product id, instead of the Wordpress post id.
* Enhancement: Fiscal code and P.IVA fields are now recognized by checking the specific plugin installed.
* Bug Fix: The db query didn't work in case the tables prefix was different than wp_

= 0.9.0 =
Release Date: 03 June, 2016

* First release

