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


// PAYMENT METHOD INVOICE
// **********************

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_BANK_ACCOUNT'] = 'Compte Bancaire de la Boutique';
$MOD_BAKERY[$payment_method]['TXT_INVOICE_TEMPLATE'] = 'Mod&egrave;le de la Facture';
$MOD_BAKERY[$payment_method]['TXT_INVOICE_ALERT'] = '1. Alerte de Relance apr&eacute;s';
$MOD_BAKERY[$payment_method]['TXT_REMINDER_ALERT'] = '2. Alerte de Relance apr&eacute;s';

// USED BY FILE bakery/payment_methods/invoice/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Facture';
$MOD_BAKERY[$payment_method]['TXT_ACCOUNT'] = 'Veuillez effectuer votre r&egrave;glement sur notre compte conform&eacute;ment aux conditions en vigueur sur le site.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Montant &agrave; payer';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Vous allez recevoir un email de confirmation de votre commande.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Votre commande sera exp&eacute;di&eacute;e le plus rapidement possible.';

// INVOICE TEMPLATE
$MOD_BAKERY[$payment_method]['INVOICE_TEMPLATE'] = '<img src="[WB_URL]/modules/bakery/images/logo.gif" width="690" height="75" alt="[SHOP_NAME] Logo" class="mod_bakery_logo_b" />
<br />
<p class="mod_bakery_shop_address_b">[SHOP_NAME] | Soci&eacute;t&eacute; | No de Rue | Code Postal | PAYS</p>
<br /><br /><br />
<p class="mod_bakery_cust_address_b" style="display: [DISPLAY_INVOICE]">[CUST_ADDRESS]</p>
<p class="mod_bakery_cust_address_b" style="display: [DISPLAY_DELIVERY_NOTE]">[ADDRESS]</p>
<p class="mod_bakery_cust_address_b" style="display: [DISPLAY_REMINDER]">[CUST_ADDRESS]</p>
<br /><br /><br /><br /><br /><br />
<h2>[TITLE]</h2>
<table class="mod_bakery_invoice_no_b" cellspacing="0" cellpadding="0">
<tr>
<td align="right">Date:</td>
<td>[CURRENT_DATE]</td>
</tr>
<tr>
<td align="right">Facture n&deg:</td>
<td>[INVOICE_ID]</td>
</tr>
<tr>
<td align="right">Commande:</td>
<td>[ORDER_ID] | [ORDER_DATE]</td>
</tr>
<tr>
<td align="right">Votre ID TVA:</td>
<td>[CUST_TAX_NO]</td>
</tr>
</table>
<br />
[ITEM_LIST]
<br /><br /><br />

<div style="display: [DISPLAY_INVOICE]">
<p class="mod_bakery_thank_you_b">Nous vous remercions d&apos;avoir fait vos achats sur [SHOP_NAME].</p>
<p class="mod_bakery_pay_invoice_b">Veuillez envoyer votre r&egrave;glement dans les 30 jours sur le compte suivant:</p>
<p class="mod_bakery_bank_account_b">[BANK_ACCOUNT]</p>
</div>

<div style="display: [DISPLAY_DELIVERY_NOTE]">
<p class="mod_bakery_thank_you_b">Nous vous remercions d&apos;avoir fait vos achats sur [SHOP_NAME].</p>
</div>

<div style="display: [DISPLAY_REMINDER]">
<p class="mod_bakery_pay_invoice_b">Veuillez ne pas tenir compte de cette lettre si vous avez d&eacute;j&agrave; effectu&eacute; votre paiement. Dans le cas contraire nous vous prions d&apos;envoyer votre r&egrave;glement dans les 10 jours sur le compte suivant:</p>
<p class="mod_bakery_bank_account_b">[BANK_ACCOUNT]</p>
</div>


<br /><br />';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Confirmation for your order at [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Cher [CUSTOMER_NAME]

Merci beaucoup pour votre commande sur [SHOP_NAME].
Veuillez trouver ci-dessous les informations concernant les article command&eacute;s:
[ITEM_LIST]

Votre commande sera envoy&eacute;e &agrave; l&apos;adresse suivante:

[ADDRESS]

La facture est adress&eacute;e &agrave; l&apos;adresse suivante:

[CUST_ADDRESS]


Nous vous remercions d&apos;avoir fait vos achats sur notre site.

[SHOP_NAME] vous remercie pour votre commande.

';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Nouvelle commande sur [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Cher administrateur de [SHOP_NAME] 

NOUVELLE COMMANDE SUR [SHOP_NAME]:
		   Commande #: [ORDER_ID]
  M&eacute;thode de paiement: Paiement anticip&eacute;

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
