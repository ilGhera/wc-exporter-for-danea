=== Woocommerce Exporter for Danea - Premium ===
Contributors: ghera74
Tags: Woocommerce, Danea, Easyfatt, ecommerce, exporter, csv, shop, orders, products
Version: 0.9.6
Requires at least: 4.0
Tested up to: 4.7.4
Stable tag: 0.9.6



Export suppliers, products, customers and orders from your Woocommerce store to Danea Easyfatt. 

----

Esporta fornitori, prodotti, clienti e ordini, dal tuo store Woocommerce a Danea Easyfatt.


== Description ==
If you've built your online store with Woocommerce and you're using Danea Easyfatt as management software, you definitely need Woocommerce Exporter for Danea - Premium!
You'll be able to export suppliers, products, clients and orders.

----

Se hai realizzato il tuo negozio online con Woocommerce ed utilizzi Danea Easyfatt come gestionale, Woocommerce Exporter per Danea è lo strumento che ti serve perché le due piattaforme siano in grado di comunicare.
WED ti permette di esportare:

* L'elenco dei fornitori, sotto forma di utenti Wordpress a cui si è assegnato un ruolo specifico (CSV).
* L'elenco dei prodotti Woocommerce (CSV).
* L'elenco dei clienti Woocommerce (CSV).
* L'elenco degli ordini Woocommerce, attraverso un feed (xml) che potrà essere usato in ogni momento in Danea per scaricare gli ordini effettuati sul tuo sito.

== Installation ==

Upload the ‘woocommerce-exporter-for-danea-premium’ directory to your ‘/wp-content/plugins/’ directory, using your favorite method (ftp, sftp, scp, etc…)
Activate Woocommerce Exporter for Danea – Premium from your Plugins page.
Once Activated, go to Woocommerce/ Woocommerce Exporter for Danea.

----

Per installare Woocommerce Exporter for Danea, dalla Bacheca del tuo sito Wordpress vai alla voce Plugin/ Aggiungi nuovo.
Clicca sul pulsante "Carica plugin" e seleziona la cartella compressa appena scaricata.
Completato il processo di installazione, troverai nel menù Woocommerce la pagina opzioni con tutte le informazioni necessarie all'utilizzo di WED.


== Screenshots ==
1. Choose the user role and download your updated suppliers list
2. Download your updated products list
3. Choose the user role and download your updated clients list
4. Choose a name for your orsers feed, copy and paste the full url in Danea.



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
* Enhancement: If present, the SKU will be used as product id, instead of the Wordpress post id.
* Enhancement: Now you can choose if export orders, completed orders or both.
* Enhancement: Fiscal code and P.IVA fields are now recognized by checking the specific plugin installed.
* Bug Fix: The db query didn't work in case the tables prefix was different than wp_
* Bug Fix: The customer web login was always 0 if the user was not registered. This were causing a continuing overwrite.
		

= 0.9.0 =
Release Date: 31 May, 2016

* First release
