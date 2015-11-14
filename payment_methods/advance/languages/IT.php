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
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'Pagamento Anticipato';

// USED BY FILE bakery/payment_methods/advance/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Pagamento Anticipato';
$MOD_BAKERY[$payment_method]['TXT_ACCOUNT'] = 'La preghiamo di effettuare il pagamento dovuto al nostro conto bancario in anticipo.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Pagher&ograve; in anticipo';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Le invieremo una email di conferma con le informazioni per il pagamento.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Appena riceveremo il suo pagamento, le spediremo l\'ordine.';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Conferma e fattura per il suo ordine su [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Gentile [CUSTOMER_NAME]

Grazie per aver acquistato su [SHOP_NAME].
Qui sotto trover&agrave; il riepilogo del suo ordine:
[ITEM_LIST]

La preghiamo di effettuare il pagamento dovuto al nostro conto bancario in anticipo
[BANK_ACCOUNT]

Non appena il pagamento risulter&agrave; effettuato le spediremo l\'ordine al pi&ugrave; presto all\'indirizzo qui sotto:

[ADDRESS]


La ringraziamo per la fiducia mostra.

Cordiali Saluti,
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Nuovo ordine su [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Gentile [SHOP_NAME] Amministratore

NUOVO ORDINE SU [SHOP_NAME]:
	Ordine #: [ORDER_ID]
	Metodo di pagamento: Pagamento Anticipato

Indirizzo di Spedizione:
[ADDRESS]

Indirizzo di Fatturazione:
[CUST_ADDRESS]

Prodotti ordinati: 
[ITEM_LIST]


Nota cliente:
[CUST_MSG]


Distinti Saluti,
[SHOP_NAME]


';



// If iso-8859-1 is set as WB default charset convert some utf-8 strings to iso-8859-1
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'iso-8859-1') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
