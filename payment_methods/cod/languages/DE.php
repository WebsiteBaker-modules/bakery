<?php

/*
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (C) 2016, Christoph Marti

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


// PAYMENT METHOD CASH ON DELIVERY PAYMENT
// ***************************************

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'Nachnahme';
$MOD_BAKERY[$payment_method]['TXT_CHARGES'] = 'Nachnahmegeb&uuml;hren<br />(ohne W&auml;hrungscode)';

// USED BY FILE bakery/payment_methods/cod/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Nachnahme';
$MOD_BAKERY[$payment_method]['TXT_PAY_CASH_ON_DELIVERY'] = 'Bezahlen Sie direkt bei Zustellung der Lieferung an den Mitarbeiter der Logistikfirma.';
$MOD_BAKERY[$payment_method]['TXT_ADDITIONAL_CHARGES_1'] = 'Bitte beachten Sie, dass zus&auml;tzliche <b>Nachnahmegeb&uuml;hren im Betrag von ';
$MOD_BAKERY[$payment_method]['TXT_ADDITIONAL_CHARGES_2'] = '</b> anfallen.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Ich bezahle per Nachnahme';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Sie erhalten von uns eine E-Mail mit der Auftragsbest&auml;tigung.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Die gew&uuml;nschten Artikel senden wir Ihnen unverz&uuml;glich zu.';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Bestätigung für Ihre [SHOP_NAME] Bestellung';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Guten Tag [CUSTOMER_NAME]

Herzlichen Dank für Ihren Einkauf bei [SHOP_NAME].
Sie haben die unten stehenden Artikel aus unserem Sortiment bestellt:
[ITEM_LIST]

Die gewünschten Artikel werden wir Ihnen unverzüglich an folgende Adresse senden:

[ADDRESS]

Bitte bezahlen Sie direkt bei Zustellung der Lieferung an den Mitarbeiter der Logistikfirma. Beachten Sie, dass zusätzliche Nachnahmegebühren anfallen können.


Wir danken für das uns entgegengebrachte Vertrauen.

Mit freundlichen Grüssen
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Neue [SHOP_NAME] Bestellung';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Hallo [SHOP_NAME] Admin

NEUE BESTELLUNG BEI [SHOP_NAME]:
	Bestellnummer: [ORDER_ID]
	Zahlungsart: Nachnahme

Lieferadresse:
[ADDRESS]

Folgende Artikel wurden bestellt: 
[ITEM_LIST]


Kundenbemerkung:
[CUST_MSG]


Mit freundlichen Grüssen
[SHOP_NAME]


';



// If utf-8 is set as default charset convert some iso-8859-1 strings to utf-8 
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'utf-8') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_encode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_encode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_encode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_encode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
