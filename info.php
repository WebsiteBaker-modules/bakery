<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2015, Christoph Marti

  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/


/*
 -----------------------------------------------------------------------------------------
  Bakery module for WebsiteBaker v2.7 (http://www.websitebaker.org)
  The Bakery module provides the facility to add online shopping pages to WebsiteBaker
 -----------------------------------------------------------------------------------------
 
	DEVELOPMENT HISTORY:

   v1.7.2  (Christoph Marti; 05/04/2015)
	 + [languages/states/GB.php] Updated GB countries list
	 + Bugfix: Fixed some PHP include pathes using full path: WB_PATH.'/modules/bakery/'
	 + [save_item.php] Bugfix: If png image file is not resized do not change extension to .jpg
	 + [save_item.php] Bugfix: When incrementing image position and item had no image yet,
	   db function returned NULL instead of 0
	 + [save_item.php] Bugfix: Under certain circumstances false images have been deleted (reported by Boudi)
	   http://forum.websitebaker.org/index.php/topic,28198.msg196712.html#msg196712
	 + Added a button to the order page that makes is possible to send the customer invoice as an html email
	   Makes use of the Premailer api to bring css inline (http://premailer.dialect.ca/api)
	   (thanks to marmot, jacobi22 and dbs)

   v1.7.1  (Christoph Marti; 11/27/2014)
	 + [view.php] Bugfix: Added closing </div> <!-- End of bakery wrapper --> to the success page
	   (reported by bolengo)
	 + [payment_methods/paypal/post_data.php] Bugfix: Round amount sent to PayPal to 2 digits after the decimal point
	   (reported by tschiemer)
	 + [payment_methods/payment-network/post_data.php] Bugfix: Removed group of thousands separator in amount
	 + [install.php] Bugfix: Changed default value of field free_shipping in the db table mod_bakery_general_settings  
	   from 9999999 to 99999.99 (reported by daydreamer, thanks to jacobi22 and marmot)
	   http://www.websitebaker.org/forum/index.php/topic,26484.msg182112.html#msg182112
	 + Fixed some php mysql functions since they are deprecated as of PHP 5.5.0:
	    - mysql_insert_id() replaced by $database->get_one("SELECT LAST_INSERT_ID()")
	    - mysql_error() replaced by $database->get_error()
	 + Bugfix: Fixed a bug that stopped saving new images in the database when clicking "Save & Back"
	   (reported by jacobi22 and instantflorian)
	   http://www.websitebaker.org/forum/index.php/topic,26497.msg192755.html#msg192755
	 + [payment_methods/paypal/ipn.php] Updated PayPal IPN using cURL library to comply with TLS protocol
	   since PayPal discontinued support for SSL 3.0 support on december 3, 2014 (reported by N1kko)
	   This is due to the POODLE SSL 3.0 vulnerability 
	   http://www.websitebaker.org/forum/index.php/topic,27799.msg193340.html#msg193340

   v1.7.0  (Christoph Marti; 08/24/2013)
	 + [upgrade.php] Bugfix: Added database fields created_when and created_by to the mod_bakery_items table
	   (reported by instantflorian)
	 + [payment_methods/paypal/post_data.php] Bugfix: Changed order total posted to PayPal
	   from type string to int to stop PayPal moaning about number format (reported by instantflorian)
	 + [config.php] Added thumbnail default sizes (modify page settings)
	 + [save_item.php] If image alt is left blank, Bakery uses (if provided) 1. image title or 2. image caption
	 + [languages/CS.php] Added Czech language file (thanks to dana)
	 + [payment_methods/paypal/ipn.php] Updated PayPal IPN to comply with HTTP 1.1 protocol since
	   PayPal discontinued support for HTTP 1.0 starting february 1, 2013 / october 7, 2013 (reported by paulchen)

   v1.6.9  (Christoph Marti; 04/13/2013)
	 + Added code to not let prices in the cart undercut zero (reported by jacobi22)
	 + [save_item.php] Fixed changing item attributes (reported by jacobi22)
	 + [modify_item.php] Fixed move image up arrow-link
	 + Changed sorting of item attributes to natsort using mysql order clause
	   ORDER BY LENGTH(a.attribute_name), a.attribute_name
	 + [search.php] Made free definable fields searchable

   v1.6.8  (Christoph Marti; 03/27/2013)
	 + Added config.php file for some additional settings
	 + [modify_item.php] Limited lenght of item image filenames (can be set in config.php)
	 + [modify_item.php] Too long item image filenames will be wrapped in order to fit the table cell
	 + [view_item.php] For item detail templates built with a table wrap selects in a table row
	   Affects item options selects => [OPTION] placeholder

   v1.6.7  (Christoph Marti; 03/24/2013)
	 + On item detail pages added code to truncate long pagination links and add … (horizontal ellipsis)
	 + [calc_price.js] Fixed jquery function in case the option price is neither '-' nor '+' but '='
	 + [save_item.php] Bugfix: When duplicating an item the link to the origin has been saved in the db

   v1.6.6  (Christoph Marti; 03/17/2013)
	 + [view_cart.php] Bugfix: Fixed display of main image in cart (reported by jacobi22)
	 + Replaced ampersands by its entities &amp; in some non-js url query strings (reported by jacobi22)
	 + [modify_item.php] Added note that the image at the top position is used as main image (reported by jacobi22)
	 + On item detail pages added code to truncate long pagination texts and add a horizontal ellipsis
	 + [calc_price.js] Fixed jquery function in case the option price is neither '-' nor '+' but '='

   v1.6.5  (Christoph Marti; 03/13/2013)
	 + Improved item image ordering and added image data as title, alt, caption and item attribute assignment

   v1.6.4  (Christoph Marti; 01/21/2013)
	 + [languages/states/GB.php] Added state file for United Kingdom (thanks to kbob)
	 + Bugfix: Changed db field shop_state to VARCHAR(5) to fit longer state codes (reported by kbob)
	 + [languages/EN.php] Amended the english language file (thanks to kbob)
	 + [languages/EN-GB.php] Added British english language file (thanks to kbob)
	   Must be invoked manually by changing the filename EN-GB.php to EN.php
	 + Wiped out some depreciated code snippets (reported by stefek)
	 + Updated lightbox2 js to version 2.51 (reported by zirzy)
	   http://www.websitebaker2.org/forum/index.php/topic,21500.msg144767.html
	   Used modified version by kpull1 that allows to set options outside of the scriptfile
	 + Added jquery plug-in to calculate price change depending on selected option (thanks to neuling and marmot)
	   Restrictions: Only works on item detail pages
	   http://www.websitebaker2.org/forum/index.php/topic,24695.msg168375.html
	 + Changed PayPal PDT from php fsockopen() to curl lib
	 + Bugfix: Fixed pagination on overview page
	 + Altered back button on detail pages so now it jumps back to the overview page where you left
	 + On item detail pages altered previous and next linktexts to show according item titles
	 + Deleted last closing php tag on all php files
	 + [save_item.php] Changed sequence of item data saving:
	   1. Save it in database, 2. generate wb access file, 3. save images / thumbnails in media directory

   v1.6.3  (Christoph Marti; 11/18/2012)
	 + [check_vat.php] Added code to check if soap extension is loaded (reported by jacobi22)
	 + [check_vat.php] Added exception handler to the soap request to prevent fatal errors on simple network issues
	 + [view_item.php] Bugfix: Initialized 2 vars to prevent a notice about undefined variables (reported by jacobi22)
	 + [save_form.php] Moved variable $add_chars from language files to save_form.php (reported by jacobi22)
	 + [save_form.php] Added special chars to the address form regexp (reported by jacobi22)
	   http://www.websitebaker2.org/forum/index.php/topic,23005.msg169605.html

   v1.6.2  (Christoph Marti; 11/13/2012)
	 + Completely reworked Bakery checkout process
	   (new sequence: 1. address form -> 2. payment method -> 3. summary -> click to buy)
	 + Added new payment method "cash on delivery"
	 + Removed payment method "mollie"
	   Please use third party iDEAL gateway provided by ideal-checkout.nl
	 + Adapted all included payment methods to the new Bakery checkout
	 + Removed feature to skip checkout if only 1 payment method is selected
	 + [images/checkout_steps/] Changed checkout images from .gif to .png
	 + Added textarea for customers message on payment method page 
	 + Added consecutive numbering of invoices (order id ≠ invoice id)
	 + Updated and improved the Italian localisation (thanks to Kwb)
	 + [languages/states/AU.php] Added state file for Australia (thanks to Darren Brack)

   v1.6.1  (Christoph Marti; 10/26/2012)
	 + [templates/cart/table_header.htm] Bugfix: Fixed <hr> inside of <p> tag (thanks to gearup)
	 + [templates/summary/table_header.htm] Bugfix: Fixed <hr> inside of <p> tag (thanks to gearup)
	 + [add.php] Bugfix: <form> is not allowed in <table> elements (thanks to gearup)
	 + [view_form.php] Bugfix: Corrected wrong spelling MAXLENGHT to MAXLENGTH (thanks to gearup)
	 + [templates/form/form.htm] Bugfix: Corrected wrong spelling MAXLENGHT to MAXLENGTH (thanks to gearup)
	 + [payment_methods/paypal/ipn.php] Improved email string comparison of receivers email address validation
	   (thanks to instantflorian and marmot) http://www.websitebaker2.org/forum/index.php?topic=24445
	 + Added support for company name in the customer and shipping address forms
	 
   v1.6.0  (Christoph Marti; 06/17/2012)
	 + Resaved all bakery files using charset utf-8
	 + Replaced function utf8_encode() by utf8_decode() in all language files
	 + [view.php] Bugfix: Added code to prevent entering negative item quantities when updating the cart
	   (reported by ronald32)
	 + Added support for EU tax zone (based on patch by syncgw, thanks to syncgw)
	   http://www.websitebaker2.org/forum/index.php/topic,21690.msg145849.html
	 + Added placeholder [CUST_TAX_NO] to emails and the invoice
	 + Added support for German "Button-Gesetz" (based on patch by instantflorian, thanks to instantflorian)
	   http://www.websitebaker2.org/forum/index.php/topic,23974.msg163127.html
	 + [languages/states/FR.php] Added state file for Italy (thanks to Kurry)
	 + [mini_cart.php] Changed MiniCart displaying the note regarding sales tax depending on general settings
	   (reported by instantflorian)
	 + [view_form.php] Bugfix: Inactivated deprecated code that stoped droplets working on the address form page
	 + [view_pay.php] Bugfix: Inactivated deprecated code that stoped droplets working on the address form page
	 + Added items drag&drop (based on patch by stefek and CrnoGorak, thanks to stefek and CrnoGorak)
	   http://www.websitebaker2.org/forum/index.php/topic,20267.0.html
	 + [upgrade.php] Added support for $database = WbDatabase::getInstance() started at WB Rev. 1682
	   by keeping downward compatibility
	 
   v1.5.9  (Christoph Marti; 12/11/2011)
	 + [view_form.php, view_pay.php] Bugfix: Added support for WB function getOutputFilterSettings()
	   introduced in WB 2.8.2 SP2. It replaced the former function get_output_filter_settings()
	 + Bugfix: Replaced all ereg(), eregi() and ereg_replace() functions by strpos(), preg_match()
	   or preg_replace() as of PHP 5.3.0 the regex extension is deprecated in favor of the PCRE extension

   v1.5.8  (Christoph Marti; 10/24/2011)
	 + [view.php] Bugfix: Fixed state code validation to accept alphanumeric characters
	   (reported to elarifr)
	 + [languages/FR.php] Bugfix: Rectified translation of $MOD_BAKERY['TXT_SHOW_STATE_FIELD']
	   (thanks to elarifr)
	 + [view_cart.php] Bugfix: Use function getimagesize() with image path since
	   url does not work properly if php ini option allow_url_fopen is set to 0 (thanks to chio)

   v1.5.7  (Christoph Marti; 03/27/2011)
	 + [languages/countries/EN.php, languages/countries/DE.php] Bugfix: Rectified country code of China
	   (reported to syncgw)
	 + [modify_orders.php] Minor modification to support the payment status 'busy' (requested by Ruud)
	 + [payment_methods/load.php] Added possibility to add install.php/upgrade.php files to a payment method
	   (requested by Ruud)
	 + [view_summary.php] Bugfix: Rectified the term for free shipping on summary page and invoice
	 + [view_item.php] Bugfix: Fixed pagination on item detail pages in case of inactive item(s)
	   (reported by Donald169)
	 + [view_item.php] Bugfix: Replaced hardcoded page extension '.php' by wb constant PAGE_EXTENSION

   v1.5.6  (Christoph Marti; 06/07/2010)
	 + [view.php] Bugfix: Added code to exclude the cust_confirm_email field in the mysql update string
	   (thanks to syncgw)

   v1.5.5  (Christoph Marti; 06/01/2010)
	 + [save_general_settings.php] Clean out protocol names, if added to the shop name
	   in the Bakery backend, to prevent problems with the php mail() function
	 + [view.php, help.php, mini_cart.php] Replaced nested mysql selects by a mysql join statements since
	   the nested select failed on some mysql server versions
	 + [uninstall.php] Bugfix: Replaced hard coded Bakery pages directory by the one
	   saved in the general settings
	 + [search.php] Bugfix: If no main image is defined there will no longer be a thumbnail displayed
	   in search results
	 + Added FR language files: module, country list, state list, payment methods advance and invoice
	   (thanks to quinto)
	 + Improved the 'payment-network' payment method (version v0.2) 
	   + Added support for notification password used by http-response notification
	     (requested by Payment Network AG)
	 + Added email confirmation field to the address form (thanks to copta)
	 + Fixed some small issues (thanks to Ruud)
	   + [view_overview.php] Rectified unclosed <a> tag and incorrect thumb html string
	   + [view_summary.php] Set default state for countries without a state file

   v1.5.4  (Christoph Marti; 04/08/2010)
	 + [view.php] In case 'No out of stock orders' is set as general setting
	   and a customer puts a sold out item into the cart it will no longer be inserted into db
	 + [save_item.php] Bugfix: Added missing curly brace (reported by MCoffman and frododendron)

   v1.5.3  (Christoph Marti; 04/07/2010)
	 + [view.php] Added support for MSIE image buttons (that just submit the click coordinates) by
	   converting POST names from 'anything_x' to 'anything'
	 + [modify_item.php] Bugfix: The move/duplicate item dropdown menu occasionally displayed
	   improper item page therefore items were moved to other pages
	 + [save_item.php] After duplicating an item Bakery now jumps to the duplicated item
	 + [stock.php, save_stock.php] After saving changes to the stock admin page Bakery now retaines
	   the selected category (=section)
	 + [languages/states/CA.php] Added state file for Canada (thanks to mjm4842)
	 + [view.php] Bugfix: Removed some doubled <p> tags from error messages
	 + Bugfix 'paypal' payment method (version v0.4)
	   + Clarified error message in case Bakery fails to connect to the PayPal server (reported by ms)
	   + Made some other small fixes and improvements to the payment method code

   v1.5.2  (Christoph Marti; 03/19/2010)
	 + [save_item.php] Added code to replace triple page spacer by one page spacer in item urls
	 + Bugfix 'paypal' payment method (version v0.3)
	   + Rectified PayPal return url provided by the Bakery backend to stop PayPal moaning about 
	     improperly formatted return url (reported by westjoneff)
	 + [delete.php] Bugfix: When a page or section gets deleted, the associated item attributes in the db
	   will be deleted as well
	 + [save_item.php] Bugfix: When saving the item options sometimes they were duplicated accidentally
	   (reported by instantflorian)

   v1.5.1  (Christoph Marti; 03/13/2010)
	 + Added a loader icon to the direct checkout which is displayed before redirecting to
	   the payment gateway (currently only PayPal and Payment Network)
	 + Bugfix: Changed some not closed <img> tags to xhtml standards-compliant <img />
	 + [templates/cart/table_body.php] Bugfix: Fixed an empty img height="" attribute that made IE display
	   the cart thumb as a line. Img width and height is now calculated by Bakery
	   (reported by vm12, thanks to Thomas Zobrist)
	 + [view_confirmation.php] Bugfix: Improper use of single quotes which leads to variable name
	   displayed instead of email address (thanks to proglot)
	 + [stock.php] Improved the item overview by adding category (section) filter to the stock admin
	 + Improved the item image upload (thanks to stefek)
	 + Upload image can now be selected as main image before uploading
	 + Fixed number format of prices at overview and item detail pages (reported by instantflorian)
	 + Linked cart item thumbs to item detail pages (suggested by t0nno)
	 + [view_summary.php] Bugfix: Added round() to fix an issue with incorrectly formatted order total
	   amounts submitted to payment gateways (PayPal reported by itsnick, Mollie reported by ruud)
	 + [view_item.php] Bugfix: Fixed the previous/next item navigation to jump inactive items
	   (reported by instantflorian)
	 + Bugfix: Fixed the item attribute price calculation if an attribute is set to '='
	   (reported by instantflorian)
	 + [modify_item.php] Added submit buttons which save and return to the same page
	   (suggested by instantflorian)
	 + Added SEO feature that admits to rename the Bakery pages directory in the general settings
											  
   v1.5.0  (Christoph Marti; 01/12/2010)
	 + [modify_item.php] Changed the order of the option price operators from '=/+/-' to '+/-/='
	   to prevent misentry by using the default value '=' with an option price left blank or equal 0
	 + Prettyfied code by applying some code standards
	 + [search.php] Bugfix: Display of thumbnail in search results (invented in v1.3
	   by multiple item images)
	 + Added thumb display to the cart
	 + Bugfix: Rectified the use of <label> tags with the for attribute (thanks to klik)
	 + Added placeholders [TXT_TAX_RATE] and [TAX_RATE] (suggested by ebussinetz)
	 + Bugfix: Fixed double % for sales tax rate in emails
	 + [languages/countries/NL.php] Bugfix: Replaced special chars like ë, ö, ï, é and ä by html entities
	 + [stock.php] Bugfix: Rectified sorting of stock column (reported by mr.winkle)
	 + [stock.php] Bugfix: Missing submit button when 'shipping address' setting => 'always'
	   (reported by discofred)
	 + [view_summary.php] Bugfix: Removed item options leading coma in emails
	 + [view_summary.php] Bugfix: Rectified number format of item total and order subtotal on
	   summary page and in emails
	 + [view_oveview.php] [view_item.php] Bugfix: If stock level was zero, it displayed "n/a"
	   (reported by weiry)
	 + [add.php] Bugfix: Changed default setting of the continue_url from $link to $page_id
	   (invented in v1.4.0)
	 + Major improvement to the 'paypal' payment method (version v0.2)
	     + Added PayPal PDT (Payment Data Transfer) to receive payment notifications from PayPal
	     + Added PayPal IPN (Instant Payment Notification) to receive payment notifications from
	       PayPal in the background. Therefore it is independent of the customer's action and
	       reduces the chance for breakage
	 + Email data is now saved in the db instead of the session var in case
	   the email sending is triggered in the background (eg. by PayPal IPN)
	 + Bugfix: Replaced <?php echo WB_URL; ?>/admin by <?php echo ADMIN_URL; ?> (thanks to MurgtalNet)
	 + [view_summary.php] Bugfix (invented in v1.4.0): The $count_items var is not calculated
	   (thanks to Mase)
	 + Improved invoice template to use shipping address - if provided by customer - for delivery notes
	 + Added feature to update page settings of specified Bakery pages at once

   v1.4.1  (Christoph Marti; 10/23/2009)
	 + [precheck.php] Bugfix: Removed 'display_errors' => 1 since it is not required

   v1.4.0  (Christoph Marti; 10/20/2009)
	 + Bugfix: Added index file to a subdirectory
	 + Bugfix: Added index files to the bakery image directories and subdirectories (thanks to FrankH)
	 + [view.php] Bugfix: Rectified previous and next links on the item detail page (thanks to FrankH)
	 + Added support for WB 2.8 THEME_URL
	 + Added WB 2.8 precheck.php file
	 + [view_invoice.php] Added delivery notes printing feature
	 + [view_invoice.php] Added placeholders [DISPLAY_INVOICE], [DISPLAY_DELIVERY_NOTE] and [DISPLAY_REMINDER]
	 + Changed string "dunning letter" to "reminder"
	 + Modified payment method 'invoice' (version v0.2)
	   Added payment deadline feature to alert the shop admin if an invoice has not been payed in time
	 + Added placeholder [WB_URL] to the invoice template
	 + [view.php] Splitted view.php into view.php, view_overview.php and view_item.php
	 + Added feature to skip cart view after adding an item to the cart
	 + Outsourced html code to template files in order to ease design modification and
	   future upgrades (suggested by stefek)
	 + Added display of multiple tax rates in invoice item table
	 + [view_summary.php] Bugfix: 0% tax rates are no longer displayed on the summary page
	 + Improved opening of invoice window to full screen height and bottom view
	 + Improved handling of continue_url by using the page_id instead of a link
	   to avoid problems after changing page names
	 + Improved items overview page by adding cells to complete an open row at the end of the table
	   (thanks to hangja)
	 + Simplified Bakery shop navigation by giving unique names to all submit buttons
	 + Simplified calling the cart by approving the GET parameter view_cart[=yes]
	 + [view_item.php] [view_overview.php] Bugfix: If item stock has been left blank Bakery now displays "n/a"
	 + [upgrade.php] Bugfix: Fixed some undefined index notices in the upgrade script

   v1.3.6  (Christoph Marti; 07/13/2009)
	 + [payment_methods/payment-network/processor.php] Interchanged payment reference reason_1 and reason_2
	   (requested by Payment Network AG)
	 + Bugfix: Rectified payment-network return urls (success and canceled)

   v1.3.5  (Christoph Marti; 07/08/2009)
	 + [mini_cart.php] Updated mini cart to support the latest Bakery version (reported by stefek)
	   Bugfix: Added support for the vars $dec_point and $thousands_sep
	   Bugfix: Initialize var and set default value if item has no attributes
	 + Added placeholder [PAGE_TITLE] for use in the main page header and footer
	 + Changed name "Payment Network" to "sofortÃ¼berweisung.de/DIRECTebanking.com"
	   (requested by Payment Network AG)
	 + Made "Agree to terms and conditions" optional (requested by michi84)

   v1.3.4  (Christoph Marti; 06/29/2009)
	 + [modify_page_settings.php] Bugfix: Changed short form <? of php's open tag to long form <?php
	   (thanks to klok_pm)
	 + [return.php] Deleted file /payment_methods/mollie/return.php - a coding relic with no use
	 + [view_cart.php] Bugfix: Item delete button now works properly also with items which have multiple attributes 

   v1.3.3  (Christoph Marti; 06/19/2009)
	 + [upgrade.php] Bugfix: When upgrading the db field lightbox2 is now added correctly
	   (reported by snark)

   v1.3.2  (Christoph Marti; 06/16/2009)
	 + [view.php], [save_items.php] The image file name is used for the image <alt> and <title> tag
	   and is shown as the Lightbox2 caption (suggested by snark)
	 + [save_items.php] Bugfix: Includ missing language file

   v1.3.1  (Christoph Marti; 06/15/2009)
	 + [save_items.php] Bugfix: Added missing code to create the /media/bakery/ directory
	   (reported by Boudi)

   v1.3  (Christoph Marti; 06/14/2009)
	 + New feature to add multiple item images
	 + New feature using Lightbox2 to show the item images
	 + [modify_page_settings.php] Changed and added new presettings of the thumbnail resize 

   v1.2  (Christoph Marti; 05/30/2009)
	 + [processor.php] Bugfix: Turned include() path from relativ to absolute in order to prevent some php installation
	   from including the wrong info.php file (reported by erpe)

   v1.1.1  (Christoph Marti; 05/29/2009)
	 + [/payment_methods/advance/processor.php] [/payment_methods/invoice/processor.php] Bugfix: Added missing var
	   that stopped checkout procedure of advance and invoice payment if direct checkout has been activated
	   (reported by iradj)

   v1.1  (Christoph Marti; 05/25/2009)
	 + Changed handling of payment methods to make it easier adding new payment methods/payment gateways
	 + Added 2 new payment gateways: mollie (iDEAL) and payment-network (sofortueberweisung.de)
	 + Added option to skip the checkout page
	 + Added feature to set the separator for the decimal and group of thousands
	 + Separated email to customer and email to shop 
	 + [save_item.php] Bugfix: When modifying the item options they were duplicated accidentally
	   (reported by chio)
	 + Added feature to set the item price equal to the item attribute price by using the equals sign
	 + Added option to hide general settings, page settings, payment method settings
	   and css settings from shop merchant (all others than id 1)
	 + Added option to allow to order out of stock items or in stock items only
	 + Added placeholders [SHIPPING_ABROAD] and [SHIPPING_DOMESTIC] for use in the main page and
	   item html templates
	 + Bugfix: Replaced hardcoded page extension '.php' by wb constant PAGE_EXTENSION (reported by chio)
	 + Added support for Google Analytics to track visitors progress through a funnel (thanks to thorn and stefek)
	 + Added item delete button to the Bakery cart (requires javascript)
	 + Deleted hardcoded image size of "step 1-2-3" images to ease use of customized images
	 + [modify_item.php] Now uses htmlspecialchars() to output item date
	   to prevent problems with quotes (reported by instantflorian)
	 + [modify_orders.php] Changed the sequence of the table sort so that latest orders are on top
	 + [view.php] Bugfix: Does not add TIMEZONE to the order timestamp any more
	 + [view.php] Removed code that added css and js to the html body if register_frontend_modfiles() did not exist

   v1.0  (Christoph Marti; 01/19/2009)
	 + [view_summary.php] Bugfix: Refixed fix of v0.9.9 shipping rates for domestic, abroad and
	   a group of specified countries (thanks to thorn and doc)
	 + [view_summary.php] Bugfix: Set tax rate display on summary page to 0% if no sales tax is charged
	 + [view_summary.php] Bugfix: If sales tax setting is set to 'none', sales tax will not be displayed on the summary page

   v0.9.9  (Christoph Marti; 01/13/2009)
	 + [view_summary.php] Bugfix: Shipping rates for domestic, abroad and a group of specified countries
	   now work as expected (reported by erpe)

   v0.9.8  (Christoph Marti; 01/07/2009)
	 + [view_summary.php] Bugfix: If no sales tax has been charged on an order, a variable was not defined
	   (thanks to erpe)
	 + Added feature to display item stock to customers as gif images (thanks to funes for icons)
	 + [EN.php] [NL.php] Added missing text var $MOD_BAKERY['TXT_SHIPPING_METHOD_HIGHEST']
	 + [mini_cart.php] Bugfix: Fixed the continue_url of MiniCart

   v0.9.7  (Christoph Marti; 01/03/2009)
	 ! Bakery 0.9.7 requires WebsiteBaker version 2.7 or later
	 + Bugfix: Convert html entities to umlauts in the email text when using a state file
	 + Added feature to display item stock to customers as number or as info text
	 + Changed handling of items that are out of stock
	   Customers are still able to order but are warned that they will get a subsequent delivery
	 + Added feature to select the highest item shipping rate that makes part of an order as total shipping charges
	 + Added feature to add up to 3 free definable fields to the items
	 + Bugfix: Improved 2 js functions
	 + Improved the URL handling by only saving page links to the database
	   (without either domain names or page directories)
	 + Added feature to show/hide shipping address
	 + Rewritten code to handle and save customer data temporarily in the session var
	 + Improved Bakery session handling joining all Bakery session vars in the $_SESSION['bakery'] subarray
	 + [add.php] Bugfix: Removed alt attribute in the <a> tag of the thumbs (thanks to heinerle)
	 + [view.php] Bugfix: Added alt attribute to the thumbs <img> tag (thanks to heinerle)
	 + [view.php] Bugfix: Omit a new table row <tr></tr> when an item table is completed (thanks to heinerle)

   v0.9.6  (Christoph Marti; 10/02/2008)
	 ! Bakery 0.9.6 requires WebsiteBaker version 2.7 or later
	 + [view.php] Bugfix: Using a Bakery section with a snippet like anyitems on the same page
	   caused a notice due to already defined constants THUMB_PREPEND, THUMB_APPEND and NEW_ROW
	 + [modify_general_settings.php] Commented captcha setting for it is not used at the time being
	 + [modify.php] Added a header and a border to the Bakery section in the backend
	 + [delete.php] Bugfix: The item access file was not deleted when deleting a section
	 + [view_summary.php] Bugfix: If a customer provided a shipping address, it was not cost-effective
	   (reported by MUC)
	 + [view.php] Added a div wrapper id='mod_bakery_wrapper_f' for all Bakery content to ease layout
	   (thanks to cthamer) 
	 + [modify_general_settings.php] Added shop state to the general settings
	 + Added state files for US, DE, AT and CH
	   Path to the file: bakery/languages/states/XX.php where XX must correspond to the country code
	 + Added feature to charge sales tax depending on the state but on the country (eg. USA)
	   (thanks to cthamer)
	 + [view_pay.php] Made the link to the PayPal security center to fit the shop country
	   (reported by Ogierini)
	 + [view_pay.php] Disabled output filter for the Bakery checkout page (hidden fields) using
	   ob_end_flush()
	   NOTE: If you are using e.g. ob_start in the index.php of your template it is possible
	   that you will indicate problems

   v0.9.5  (Christoph Marti; 09/10/2008)
	 + If utf-8 is set as WB default charset, some iso-8859-1 encoded localisation strings
	   will be automatically converted to utf-8
	 + The invoice customer address does not include the phone number nor the email address any more
	 + [view_form.php] Disabled output filter for the Bakery address form using ob_end_flush()
	   NOTE: If you are using e.g. ob_start in the index.php of your template it is possible
	   that you will indicate problems
	 + [view.php] and [mini_cart.php] Bugfix: To include the language file replaced
	   function require_once() by include()
	   Prevents FATAL ERROR when a Bakery snippet function is called (thanks to heinerle)
	 + Added new Bakery default logo to the invoice view (thanks to stefek)
	 + [add.php] Bugfix: Var $link was not defined when a Bakery section was added to an existing page
	 + [view.php] Replace [wblinkPAGE_ID] generated by wysiwyg editor by real link
	 + Added image resizer for the item detail page
	 + Added feature to move or copy an item with its thumb and image to another section/page
	 + [delete_item.php] Bugfix: Did not delete item attributes in the db when deleting an item
	 + Modified continue_url text field in the page settings to a drop down menu to prevent misentry
	 + Bakery now makes use of the WB page spacer to seperate the item page name and the item id

   v0.9.4  (Christoph Marti; 07/12/2008)
	 + Added Egypt to the country list
	 + [view_summary.php] Fixed a bug invented in v0.9.2 that kept the db from inserting the invoice data
	 + [save_general_settings.php] Fixed a bug invented in v0.9.2 that stripped tags
	                               from the invoice template when inserting it in the db
	 + [view.php] Fixed a bug that did not put cart items back to stock when a customer had canceled final order
	 + [view.php] Fixed a bug that in certain cases miscalculated the stock on hand
	   when a customer had canceled final order

   v0.9.3  (Christoph Marti; 07/07/2008)
	 + Fixed a bug invented in v0.9.2 that precluded from inserting or updating item data in the db

   v0.9.2  (Christoph Marti; 06/28/2008)
	 + Replaced function addslashes() by WB function $admin->add_slashes()
	 + Added code to prevent from security vulnerability
	 + [save_item.php] Fixed a bug that did not delete the thumb when modifying an item by selecting delete image
	 + Fixed a bug that lost all previously entered item data after it had been submitted incompletely

   v0.9.1  (Christoph Marti; 05/31/2008)
	 + [view.php] Fixed a bug that used wrong order_id after deleting db records of not submitted orders

   v0.9  (Christoph Marti; 05/25/2008)
	 + Added improved module search (requires WB 2.7 or higher) (thanks to thorn)
	 + Added stock administration
	 + Changed the way Bakery handles the item options and attributes
	 + Added MiniCart to display the number of items and the total amount of the cart on any page or anywhere on a site
	   Include the file any_cart.php to the wb main template or in a code section (thanks to Bennie Wijs)
	 + [view.php] Separated the CSS class mod_bakery_main_thumbnail_f
	              into mod_bakery_main_thumbnail_f and mod_bakery_item_img_f
	 + Added an index.php file to all Bakery subdirectories to redirect the user to the WB main page
	   when trying to access the module directory directly
	 + Fixed a bug that did not clean up the session array properly
	   after the order has been canceled or payment has been completed (reported by ebussinetz)
	 + Added option to edit the module Bakery frontend and backend CSS (requires WB 2.7 or higher)
	   (thanks to doc)
	 + Added option to use an additional shipping address (reported by cookmaster)
	 + Added order administration and invoice printing feature to the Bakery backend (thanks to potsdamer)
	 + If no country is selected in the address form, Bakery preselects by default the shop country
	 + Changed the CSS class names from mode_ to mod_ (mod without "e": eg. mod_bakery_anything_f)

   v0.8.5  (Christoph Marti; 02/20/2008)
	 + [view_summary.php] Added a list of keys to the variables used in the Bakery html and email templates
	 + Added possibility to use placeholder [TXT_ITEM] in the page header and page footer templates
	 + Added wysiwyg editor to the Bakery item full description

   v0.8.4  (Christoph Marti; 02/17/2008)
	 + Added NL language and country files: NL.php, countries/NL.php, countries/NL-utf8.php
	   (thanks to RuudE)
	 + Set the Reply-To: of the email sent to the shop to the customer email address,
	   making it easy to contact customers later on
	 + [view_summary.php] Added possibility to use [CUST_EMAIL] in the email templates
	 + [view.php] Added missing alt attribute to the item <img> tag

   v0.8.3  (Christoph Marti; 02/02/2008)
	 + Modified the item templates to use more text vars instead of hard coded text making them completely multilingual
	   New placeholders: [TXT_ITEM], [TXT_SKU], [TXT_PRICE], [TXT_SHIPPING], [TXT_FULL_DESC],
	   [TXT_SHIPPING_COST], [TXT_DOMESTIC], [TXT_ABROAD], [TXT_BACK]
	 + Added possibility to select one predefined tax rate for shipping
	 + [view_summary.php] Added customer information "Free Shipping" in the summary table

   v0.8.2  (Christoph Marti; 01/31/2008)
	 + [view_summary.php] Corrected tax calculation for prices including tax (brutto prices)
	 + [view_summary.php] Added possibility to use [ORDER_ID] in the email templates

   v0.8.1  (Christoph Marti; 01/19/2008)
	 + [view_summary.php] Changed the way of displaying the order summary:
	   - Subtotal: Just calculates the total cost of items without any shipping per item
	   - Shipping: Calculates the total shipping costs including shipping per item
	 + [EN.php, DE.php] Added a missing language var $MOD_BAKERY['ERR_INVAL_STATE']
	 + [EN.php] Made some corrections to the English language
	 + Replaced the error, success and information icons by new ones
	 + Fixed a bug that caused a warning when the user did select no zone country in the general settings
	 + Changed the concept of "Proceed Shopping URL" to "Continue Shopping URL"   

   v0.8  (Christoph Marti; 01/03/2008)
	 + [view.php] Order date is now saved as time stamp
	 + [view.php] Deletes db records of not submitted orders older than 60 days automatically
	 + [view.php] After payment has been completed or order has been canceled,
	   Bakery will not kill the session and logged in users will not be kicked out any longer
	 + [delete.php]: When a page or section gets deleted, the associated images and thumbs
	   in the media directory will be deleted as well
	 + Improved the code to eliminate all php notice errors
	 + Added more CSS classes to the html and frontend.css file to give more control over the shop design
	 + [view.php]: Fixed a bug in the main page (overview) caused by a not reseted var in the items loop
	   It made for items which had no option showing up the item option of the preceding item
	 + [modify_item.php] Changed the input (name="option_attributes") from maxlength="150" to maxlength="600"
	 + Option to set up to 3 different tax rates in the general settings
	 + In the "Add/Modify Product" you can associate a predefined tax rate to a single item
	 + If a tax rate in the general settings is changed, all the associated item tax rates will be changed too
	 + [frontend.css] Changed .mod_bakery_table_currency to .mod_bakery_cart_currency_f
	 + The zip code can now be display either in front of the city or at the end of the address
	 + The address form now has an optional state field
	 + If a customer is logged in, Bakery tries to retrieve his address of a previous order
	   from the db to prepopulat the address fields
	 + Added option to set a third shipping rate according to specified countries

   v0.7.1  (Christoph Marti; 12/09/2007)
	 + [view.php]: Added a cancel order button to the message that is displayed when a user has canceled PayPal payment
	 + [install.php]: Deleted sql query that made the bakery settings table searchable
	 + [view_cart.php]: Added some missing closing tags like </p> and </td> to the html
	 + [view_summary.php]: Added some missing closing tags like </p> and </td> to the html

   v0.7  (Christoph Marti; 11/28/2007)
	 + Settings are now separated in general settings and page settings
	   General settings are shop settings which apply to all Bakery pages and can not differ from page to page
	   Replaced file modify_settings.php by modify_general_settings.php and modify_page_settings.php
	 + Settings are now separated in a general settings table and page settings table
	   Replaced file save_settings.php by save_general_settings.php and save_page_settings.php
	 + Payment method (pay in advance / paypal) can now be selected
	 + Email to shop is not sent as bcc: any more but as a second email
	 + Changed the default value for item quantity to 1
	 + [uninstall.php]: Now deletes the WB_PATH.MEDIA_DIRECTORY./bakery directory when Bakery gets uninstalled

   v0.6.1  (Christoph Marti; 11/16/2007)
	 + [upgrade.php]: Added an upgrade script
	 + [uninstall.php]: Fixed a bug that caused a fatal error during uninstallation
	                    when trying to delete non existing /pages/bakery directory 

   v0.6  (Christoph Marti; 11/14/2007)
	 + Added the possibility of an item option
	 + Modified/added shipping based on:
	   - a flat amount
	   - number of items
	   - number of positions
	   - percentage of subtotal
	   - no shipping
	 + Added a free shipping option
	 + Added option to inform customers about free shipping if their total does not reach the given limit
	 + Shipping is calculated on subtotal + shipping
	 + Added currency code in cart and summary pages
	 + Separated first name/last name in the customer address form
	 + Added option to switch shop pages online/offline for maintenance
	 + Added a way to extend the range of accepted chars by the customer address form
	 + Added icons to the error/success/information messages
	 + Changed the concept of the country files -> the country file now is included automatically depending on the language
	 + Changed the concept of "Shop URL" to "Proceed Shopping URL"
	 + Removed the setting of "PayPal Return URL" -> Bakery submits it automatically to PayPal
	 + Added 3 ways of updating the Bakery settings:
	   - Update settings of current shop page only
	   - Update settings except of the "Proceed Shopping URL" of all shop pages
	   - Update all settings of all shop pages
	 + [uninstall.php]: Now deletes the WB_PATH.PAGES_DIRECTORY./bakery directory when Bakery gets uninstalled

   v0.5  (Christoph Marti; 09/28/2007)
	 + [install.php]: Fixed a bug in the SQL syntax which avoided adding
	                  the table mod_bakery_items to the data base (invented v0.4)

   v0.4  (Christoph Marti; 09/25/2007)
	 + [includes/countries.php]: Added missing array number [1] on first country line
	 + [view.php]: Fixed invoice e-mail (pay in advance) that was not sent
	 + [view.php]: Fixed a bug in the SQL syntax that avoided adding items without sku# to the shopping cart
	 + [install.php]: Edited some data base settings from VARCHAR to DECIMAL(9,2) and others

   v0.3  (Christoph Marti; 09/21/2007)
	 + [add.php]: Added missing code to the variable $footer to make the previous/next links working
	 + [view.php]: Changed the regExp of the zip code to fit with all alphanumeric characters

   v0.2  (Christoph Marti; 09/17/2007)
	 + [view_pay.php]: Replaced a hard coded shop url by the variable

   v0.1  (Christoph Marti; 09/01/2007)
	 + Initial release
	 + The shopping cart of Bakery is based on Online-Shop by Janet Valade.
	 + The catalog of Bakery is based on Go-Cart by Hudge which itself is based on gallery by Ryan Djurovich.
	 
 -----------------------------------------------------------------------------------------
*/


$module_directory   = 'bakery';
$module_name        = 'Bakery';
$module_function    = 'page';
$module_version     = '1.72';
$module_platform    = '2.7';
$module_author      = 'Christoph Marti';
$module_license     = 'GNU General Public License';
$module_description = 'Bakery is a WebsiteBaker shop module with catalog, cart, stock administration, order administration and invoice/delivery note/reminder printing feature. Payment in advance, invoice, cash on delivery and/or different payment gateways. Further information can be found on the <a href="http://www.bakery-shop.ch" target="_blank">Bakery Website</a>.';

