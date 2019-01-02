=== Woocommerce Exporter for Danea ===
Contributors: ghera74
Tags: Fattura elettronica, Woocommerce, Danea Easyfatt, ecommerce, exporter, csv, shop, orders, products, gestionale
Version: 1.1.2
Requires at least: 4.0
Tested up to: 5.0
Stable tag: 1.0.0


Export suppliers, products, customers and orders from your Woocommerce store to Danea.

== Description ==

Se hai realizzato il tuo negozio online con Woocommerce ed utilizzi Danea come gestionale, Woocommerce Exporter per Danea è quello che ti serve!
Nella versione Free, permette di esportare un elenco di utenti Wordpress come fornitori, e l'elenco dei tuoi prodotti.
Nella versione Premium, potrai esportare anche clienti e ordini.
Ecco il dettaglio dei contenuti che è possibile esportare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei prodotti Woocommerce (CSV).
* Premium - L'elenco dei clienti Woocommerce (CSV).
* Premium - L'elenco degli ordini Woocommerce, attraverso un feed (xml) che potrà essere usato in ogni momento in Danea per scaricare gli ordini effettuati sul tuo sito.


**LE NOVITÀ DI QUESTA VERSIONE**

**Pronto per l'Obbligo di  Fatturazione Elettronica 2019**

* Possibilità di attivare i campi Codice Fiscale e P.IVA
* Controllo validità campi Codice Fiscale e P.IVA
* Possibilità di attivare i campi PEC e Codice Ricevente, necessari per la fattura elettronica

Fatturazione elettronica con Danea Easyfatt

https://youtu.be/tLWc_1i7778


**ENGLISH**

If you've built your online store with Woocommerce and you're using Danea as management software, you definitely need Woocommerce Exporter for Danea!
With this Free version you can export the suppliers and the products from your store.
With Premium version, you'll be able also to export clients and orders.


**NEW ON THIS VERSION**

* Fiscal code and P.IVA fields are now available width a dedicated option
* Fiscal code and P.IVA fields validation tool.
* PEC and Receiver code fields are now available, ready for the Italian eletronic invoice


== Installation ==
From your WordPress dashboard
<ul>
<li>Visit 'Plugins > Add New'</li>
<li>Search for 'Woocommerce Exporter for Danea' and download it.</li>
<li>Activate Woocommerce Exporter for Danea from your Plugins page.</li>
<li>Once Activated, go to Woocommerce/ Woocommerce Exporter for Danea.</li>
</ul>

From WordPress.org
<ul>
<li>Download Woocommerce Exporter for Danea</li>
<li>Upload the 'woocommerce-exporter-for-danea' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)</li>
<li>Activate Woocommerce Exporter for Danea from your Plugins page.</li>
<li>Once Activated, go to Woocommerce/ Woocommerce Exporter for Danea.</li>
</ul>

== Screenshots ==
1. Activate custom checkout fields
2. Choose the user role and download your updated suppliers list
3. Download your updated products list
4. Export orders to Danea Easyfatt (Premium)


== Changelog ==

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