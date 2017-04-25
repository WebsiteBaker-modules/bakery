<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2017, Christoph Marti

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


// PAYMENT METHOD ADVANCE PAYMENT
// ******************************

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'Abholung im Ladengesch&auml;ft';

// USED BY FILE bakery/payment_methods/bopis/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Abholung im Ladengesch&auml;ft';
$MOD_BAKERY[$payment_method]['TXT_PICKUP'] = 'Holen Sie Ihre Bestellung bei uns im Ladengesch&auml;ft ab.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Bezahlen Sie Ihre Bestellung im Ladengesch&auml;ft.';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Sie erhalten von uns eine E-Mail mit der Auftragsbest&auml;tigung.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Bitte holen Sie Ihre Bestellung bei uns im Ladengesch&auml;ft ab und bezahlen Sie diese bar direkt im Laden.';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Bestätigung für Ihre [SHOP_NAME] Bestellung';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Guten Tag [CUSTOMER_NAME]

Herzlichen Dank für Ihren Einkauf bei [SHOP_NAME].
Sie haben die unten stehenden Artikel aus unserem Sortiment bestellt:
[ITEM_LIST]

Bitte holen Sie Ihre Bestellung bei uns im Ladengeschäft "[SHOP_NAME]" ab und bezahlen Sie diese bar direkt im Laden.


Wir danken für das uns entgegengebrachte Vertrauen.

Mit freundlichen Grüssen
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Neue [SHOP_NAME] Bestellung';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Hallo [SHOP_NAME] Admin

NEUE BESTELLUNG BEI [SHOP_NAME]:
	Bestellnummer: [ORDER_ID]
	Zahlungsart: Bei Abholung im Ladengeschäft

Lieferadresse:
[ADDRESS]

Folgende Artikel wurden bestellt:
[ITEM_LIST]


Kundenbemerkung:
[CUST_MSG]


Mit freundlichen Grüssen
[SHOP_NAME]


';



// If iso-8859-1 is set as WB default charset convert some utf-8 strings to iso-8859-1
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'iso-8859-1') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
