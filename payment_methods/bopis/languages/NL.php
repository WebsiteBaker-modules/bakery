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


// PAYMENT METHOD ADVANCE PAYMENT
// ******************************

// SETTINGS - USED BY BACKEND


// USED BY FILE bakery/payment_methods/bopis/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Afhalen in de winkel';
$MOD_BAKERY[$payment_method]['TXT_PICKUP'] = 'Afhalen uw bestelling in onze winkel.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Contant betalen in de winkel';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'U ontvangt een bevestigings email.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Afhalen uw bestelling in onze winkel op contant betalen.';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Bevestiging voor uw [SHOP_NAME] order';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Geachte [CUSTOMER_NAME]

Bedankt voor uw bestelling bij [SHOP_NAME].
Hieronder vind u een overzicht van de door u bestelde produkten:
[ITEM_LIST]

Afhalen uw bestelling in onze winkel "[SHOP_NAME]" op contant betalen.


Bedankt voor het in ons gestelde vertrouwen.

Met vriendelijke groeten,
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Nieuwe bestelling bij [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Geachte [SHOP_NAME] Administrator

NIEUWE BESTELLING BIJ [SHOP_NAME]:
	Bestelling #: [ORDER_ID]
	Betaal methode: Afhalen in de winkel

Aflever adres:
[ADDRESS]

Factuur adres:
[CUST_ADDRESS]

Bestellijst: 
[ITEM_LIST]


Klant opmerking:
[CUST_MSG]


Met vriendelijke groet,
[SHOP_NAME]


';



// If iso-8859-1 is set as WB default charset convert some utf-8 strings to iso-8859-1
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'iso-8859-1') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
