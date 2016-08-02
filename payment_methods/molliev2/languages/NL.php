<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2012, Christoph Marti

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


// PAYMENT METHOD MOLLIE
// *********************

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_PARTNER_ID'] = 'Mollie API Key';
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'Mollie';

// USED BY FILE bakery/payment_methods/mollie/processor.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Mollie online betalingen';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Via onze betaalprovider Mollie kunt u op verschillende manieren online afrekenen.';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'U komt direct in de beveligde pagina van onze betaalprovider.';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'Kies de gewenste betaalmethode.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'Nadat de betaling is gedaan ontvangt u een email met onze orderbevestiging.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Ik betaal online via Mollie';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'Om de betaling uit te voeren wordt u nu doorgestuurd naar de beveiligde website van MOLLIE.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Ga nu naar mollie';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Totaal bedrag incl. BTW en verzendkosten';
$MOD_BAKERY[$payment_method]['ERROR_CREATING_PM'] = 'Er is en fout opgetreden. Uw betaling kon niet worden geinitialiseerd.';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Bedankt voor uw online betaling. Uw transactie is geaccepteerd.<br />Onze orderbevestiging zijn naar per email naar u verzonden.';
$MOD_BAKERY[$payment_method]['TXT_PENDING'] = 'Bedankt voor uw bestelling. Uw betaling is nog niet geaccepteerd.<br />Zodra deze alsnog binnenkomt zal een orderbevestiging worden verzonden.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Uw bestelling wordt zo spoedig mogelijk verzonden.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'Er is een probleem opgetreden. Uw betaling is niet uitgevoerd.<br />Neem contact op met de winkel beheerder.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'U heeft uw betaling afgebroken.<br />Wilt u verder gaan met winkelen?';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Bevestiging van uw [SHOP_NAME] bestelling';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Geachte [CUSTOMER_NAME]

Bedankt voor uw bestelling bij [SHOP_NAME].
Hieronder vind u een overzicht van de door u bestelde produkten:
[ITEM_LIST]

Wij zullen de goederen verzenden naar:

[ADDRESS]


Bedankt voor het in ons gestelde vertrouwen.

Met vriendelijke groeten,
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Nieuwe bestelling bij [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Geachte [SHOP_NAME] Administrator

NIEUWE BESTELLING BIJ [SHOP_NAME]:
	Bestelling #: [ORDER_ID]
	Betaal methode: Mollie (iDEAL)

Aflever adres:
[ADDRESS]

Factuur adres:
[CUST_ADDRESS]

Bestellijst: 
[ITEM_LIST]


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

?>