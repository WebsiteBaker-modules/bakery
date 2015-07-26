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
$MOD_BAKERY[$payment_method]['TXT_EMAIL'] = 'PayPal E-Mail';
$MOD_BAKERY[$payment_method]['TXT_PAGE'] = 'Benutzerdefinierte Zahlungsseite';
$MOD_BAKERY[$payment_method]['TXT_AUTH_TOKEN'] = 'Identit&auml;tstoken';

$MOD_BAKERY[$payment_method]['TXT_NOTICE'] = '
<b>Website-Zahlungsoptionen</b><br />
Loggen Sie sich in Ihr PayPal Konto ein: Gehen Sie zu &quot;MeinKonto&quot; &gt; &quot;Mein Profil&quot; &gt; &quot;Verk&auml;ufer/H&auml;ndler&quot; &gt; &quot;Website-Einstellungen&quot;.<br />

<b>Automatische R&uuml;ckleitung:</b> Aktivieren Sie &quot;Automatische R&uuml;ckleitung&quot;.<br />
<b>R&uuml;ckleitungs-URL:</b> Geben Sie folgende URL als &quot;R&uuml;ckleitungs-URL&quot; an:<input type="text" value="' . WB_URL . '" readonly="true" onclick="this.select();" style="width: 98%;" /><br /><br />

<b>&Uuml;bertragung der Zahlungsdaten:</b> Aktivieren Sie &quot;&Uuml;bertragung der Zahlungsdaten&quot; und speichern Ihre Einstellung. Ihr Identit&auml;ts-Token wird unterhalb der &quot;&Uuml;bertragung der Zahlungsdaten&quot; Radio-Buttons angezeigt. Kopieren Sie Ihr Identit&auml;ts-Token ins Feld direkt oberhalb dieser gelben Box.<br /><br />

<b>Sofortige Zahlungsbest&auml;tigung (IPN)</b><br />
Gehen Sie zu &quot;MeinKonto&quot; &gt; &quot;Mein Profil&quot; &gt; &quot;Verk&auml;ufer/H&auml;ndler&quot; &gt; &quot;Benachrichtigungen über Sofortzahlungen&quot;.<br />
Durch Klicken auf &quot;Einstellungen für sofortige Zahlungsbestätigungen w&auml;hlen&quot; gelangen Sie auf die Konfigurationsseite.<br />
Kopieren Sie die unten stehende URL und f&uuml;gen Sie sie vollst&auml;ndig ins Feld &quot;Benachrichtigungs-URL&quot; auf der Konfigurationsseite ein:<input type="text" value="' . WB_URL . '/modules/bakery/payment_methods/paypal/ipn.php" readonly="true" onclick="this.select();" style="width: 98%;" />
Aktivieren Sie &quot;Sofortige Zahlungsbest&auml;tigungen erhalten (aktiviert)&quot; und speichern Ihre Einstellung.<br />';

// USED BY FILE bakery/payment_methods/paypal/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'PayPal (Kreditkarte)';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Bezahlen Sie online mit allen g&auml;ngigen Kreditkarten per PayPal: schnell, sicher, problemlos...';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'Bezahlen Sie Ihre Bestellung online mit allen g&auml;ngigen Kreditkarten per PayPal oder auch per PayPal-Zahlung.';
$MOD_BAKERY[$payment_method]['TXT_SECURITY'] = 'Mehr Informationen zur Zahlungssicherheit finden Sie auf der';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'Die Zahlungsabwicklung l&auml;uft &uuml;ber einen sicheren PayPal Server.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'Nach Ihrer Transaktion erhalten Sie per E-Mail unsere Auftragsbest&auml;tigung sowie eine Zahlungsbest&auml;tigung von PayPal.';
$MOD_BAKERY[$payment_method]['TXT_WEBSITE'] = 'PayPal Website';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Ich bezahle per PayPal';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'Zur Zahlungsabwicklung werden Sie zu einem sicheren PayPal Server weitergeleitet.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Jetzt zu PayPal wechseln';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Gesamtsumme inkl. Mwst und Versand';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Besten Danke f&uuml;r Ihre online Zahlung. Ihre Transaktion wurde abgeschlossen.<br />Unsere Auftragsbest&auml;tigung und eine Zahlungsbest&auml;tigung von PayPal wurden Ihnen per E-Mail zugesandt.';
$MOD_BAKERY[$payment_method]['TXT_PENDING'] = 'Besten Danke f&uuml;r Ihre online Zahlung. Ihre Transaktion wird in K&uuml;rze bearbeitet.<br />Unsere Auftragsbest&auml;tigung und eine Zahlungsbest&auml;tigung von PayPal wird Ihnen per E-Mail zugesandt.';
$MOD_BAKERY[$payment_method]['TXT_TRANSACTION_STATUS'] = "ACHTUNG:\n\tDie Transaktion ist noch \"OFFEN\".\n\tAlle Details zu dieser Zahlung finden Sie in Ihrer PayPal-Kontoübersicht.";
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Die gew&uuml;nschten Artikel senden wir Ihnen unverz&uuml;glich zu.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'Es ist ein Problem aufgetreten. Ihre Transaktion konnte nicht abgeschlossen werden.<br />Bitte wenden Sie sich an den Shop-Betreiber.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'Sie haben Ihre Zahlung bei PayPal abgebrochen.<br />M&ouml;chten Sie Ihren Einkauf trotzdem fortsetzen?';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Bestätigung für Ihre [SHOP_NAME] Bestellung';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Guten Tag [CUSTOMER_NAME]

Herzlichen Dank für Ihren Einkauf bei [SHOP_NAME].
Sie haben die unten stehenden Artikel aus unserem Sortiment bestellt:
[ITEM_LIST]

Die gewünschten Artikel werden wir Ihnen unverzüglich an folgende Adresse senden:

[ADDRESS]


Wir danken für das uns entgegengebrachte Vertrauen.

Mit freundlichen Grüssen
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'Neue [SHOP_NAME] Bestellung';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Hallo [SHOP_NAME] Admin

NEUE BESTELLUNG BEI [SHOP_NAME]:
	Bestellnummer: [ORDER_ID]
	Zahlungsart: PayPal
[TRANSACTION_STATUS]

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
