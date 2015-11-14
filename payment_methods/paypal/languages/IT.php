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


// PAYMENT METHOD PAYPAL
// *********************

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'PayPal';
$MOD_BAKERY[$payment_method]['TXT_EMAIL'] = 'Email PayPal';
$MOD_BAKERY[$payment_method]['TXT_PAGE'] = 'Pagina PayPal';
$MOD_BAKERY[$payment_method]['TXT_AUTH_TOKEN'] = 'PDT Identity Token';

$MOD_BAKERY[$payment_method]['TXT_NOTICE'] = '
<b>Website Payment Preferences</b><br />
Log in to your PayPal account: Go to &quot;My Account&quot; &gt; &quot;Profile&quot; &gt; &quot;My selling tools&quot; &gt; &quot;Website preferences&quot;.<br />

<b>Auto Return:</b> Click the &quot;Auto Return&quot; radio button <i>On</i>.<br />
<b>Return URL:</b> Enter the url as shown below in the field &quot;Return URL&quot;:<input type="text" value="' . WB_URL . '" readonly="true" onclick="this.select();" style="width: 98%;" /><br /><br />

<b>Payment Data Transfer:</b> Click the &quot;Payment Data Transfer (PDT)&quot; radio button <i>On</i> and then click <i>Save</i>.<br />
Your Identity Token is shown below the PDT On/Off radio buttons. Copy&amp;paste your Identity Token to the textfield right above this yellow box.<br /><br />

<b>Instant Payment Notification Preferences</b><br />
Go to &quot;My Account&quot; &gt; &quot;Profile&quot; &gt; &quot;My selling tools&quot; &gt; &quot;Instant payment notifications&quot;.<br />
Click the &quot;Choose IPN Settings&quot; button and you will be taken to the configuration page.<br />
Copy&amp;paste the full url as shown below to the field &quot;Notification URL&quot;:<input type="text" value="' . WB_URL . '/modules/bakery/payment_methods/paypal/ipn.php" readonly="true" onclick="this.select();" style="width: 98%;" />
Click the &quot;Receive IPN messages (Enabled)&quot; radio button and save your changes.<br />';

// USED BY FILE bakery/payment_methods/paypal/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'PayPal (Carta di Credito)';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Paga online con PayPal usando la tua carta di credito: facile, sicuro, gratis...';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'Paga il tuo ordine online usando la tua carta di credito o pagamento PayPal.';
$MOD_BAKERY[$payment_method]['TXT_SECURITY'] = 'Maggiori informazioni sull\'acquisto sicuro su PayPal Security Center';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'La transazione &egrave; gestita dai server sicuri PayPal.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'Dopo il completamento della transazione, ti invieremo via email la conferma dell\'ordine e la ricevuta d\'acquisto.';
$MOD_BAKERY[$payment_method]['TXT_WEBSITE'] = 'Sito Web PayPal';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Pagher&ograve; con PayPal';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'Ora verrai redirezionato su un server sicuro PayPal per effettuare il pagamento.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Vai a PayPal ora';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Totale incl. tasse e spedizione';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Grazie per il tuo pagamento online! La transazione &egrave; stata completata.<br />Ti abbiamo inviato una email con la ricevuta d\'acquisto PayPal e la conferma dell\'ordine.';
$MOD_BAKERY[$payment_method]['TXT_PENDING'] = 'Grazie per il tuo pagamento online! La transazione sar&agrave; processata a breve.<br />Ti invieremo una email con la ricevuta d\'acquisto PayPal e la conferma dell\'ordine.';
$MOD_BAKERY[$payment_method]['TXT_TRANSACTION_STATUS'] = "ATTENZIONE:\n\tLo stato della transazione &egrave; \"IN SOSPESO\".\n\tPer visualizzare tutti i dettagli della transazione, ti preghiamo di eseguire il login sul tuo account PayPal.";
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Ti spediremo l\'ordine il pi&ugrave; presto possibile.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'Si &egrave; verificato un problema. La transazione non &egrave; stata completata.<br />Ti preghiamo di contattare il gestore del sito web.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'Hai cancellato il tuo pagamento PayPal.<br />Vuoi continuare l\'acquisto?';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Conferma del tuo ordine su [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Gentile [CUSTOMER_NAME]

Grazie per aver acquistato su [SHOP_NAME].
Qui sotto trover&agrave; il riepilogo del suo ordine:
[ITEM_LIST]

Le spediremo l\'ordine all\'indirizzo qui sotto:

[ADDRESS]


La ringraziamo per la fiducia.

Cordiali Saluti,
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Nuovo ordine su [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Gentile [SHOP_NAME] Amministratore

NUOVO ORDINE SU [SHOP_NAME]:
	Ordine #: [ORDER_ID]
	Metodo di pagamento: PayPal
[TRANSACTION_STATUS]

Indirizzo di Spedizione:
[ADDRESS]

Indirizzo di Fatturazione:
[CUST_ADDRESS]

Prodotti Ordinati: 
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
