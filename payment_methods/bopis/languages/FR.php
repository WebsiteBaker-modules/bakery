<?php

/*
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
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
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'L&apos;enl&eacute;vement d&eacute;part boutique';
$MOD_BAKERY[$payment_method]['TXT_PICKUP'] = 'Veuillez venir chercher votre commande &agrave; la boutique.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Payer comptant &agrave; la boutique';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Vous allez recevoir un email de confirmation de votre commande.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Veuillez venir chercher votre commande &agrave; la boutique en payant comptant.';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Confirmation pour votre commande sur [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Cher [CUSTOMER_NAME]

Merci beaucoup pour votre commande sur [SHOP_NAME].
Veuillez trouver ci-dessous les informations concernant les article command&eacute;s:
[ITEM_LIST]

Veuillez venir chercher votre commande à la boutique "[SHOP_NAME]" en payant comptant.


Nous vous remercions d&apos;avoir fait vos achats sur notre site.

[SHOP_NAME] vous remercie pour votre commande.

';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Nouvelle commande sur [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Cher administrateur de [SHOP_NAME] 

NOUVELLE COMMANDE SUR [SHOP_NAME]:
	Commande #: [ORDER_ID]
	M&eacute;thode de paiement: L&apos;enl&eacute;vement d&eacute;part boutique

Adresse de Livraison:
[ADDRESS]

Adresse de Facturation:
[CUST_ADDRESS]

Liste des articles command&eacute;s:
[ITEM_LIST]


Note du client:
[CUST_MSG]


Meilleures consid&eacute;rations,
[SHOP_NAME]


';



// If iso-8859-1 is set as WB default charset convert some utf-8 strings to iso-8859-1
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'iso-8859-1') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
