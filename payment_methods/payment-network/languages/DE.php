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


// PAYMENT METHOD PAYMENT-NETWORK
// ******************************

// Get the current url scheme
$url = parse_url(WB_URL);

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'SOFORT &Uuml;berweisung';
$MOD_BAKERY[$payment_method]['TXT_USER_ID'] = 'Kundennummer';
$MOD_BAKERY[$payment_method]['TXT_PROJECT_ID'] = 'Projektnummer';
$MOD_BAKERY[$payment_method]['TXT_PROJECT_PW'] = 'Projekt Passwort';
$MOD_BAKERY[$payment_method]['TXT_NOTIFICATION_PW'] = 'Benachrichtigungspasswort';
$MOD_BAKERY[$payment_method]['TXT_NOTICE'] = "
<b>SOFORT &Uuml;berweisung erweiterte Einstellungen</b><br />
Loggen Sie sich in Ihr <a href='https://www.sofortueberweisung.de/payment/users/login' target='_blank'>SOFORT &Uuml;berweisung</a> Konto ein: Gehen Sie zu &quot;Meine Projekte&quot; &gt; &quot;Projekt ausw&auml;hlen&quot; &gt; &quot;Erweiterte Einstellungen&quot;:<br /><br />

<b>Shopsystem-Schnittstelle:</b> Aktivieren Sie &quot;Automatische Weiterleitung&quot; und geben Sie unter &quot;Erfolgslink&quot; folgende vollst&auml;ndige URL ein:<input type='text' value='".$url['scheme']."://-USER_VARIABLE_1-?pm=payment-network&transaction_id=-TRANSACTION-' readonly='true' onclick='this.select();' style='width: 98%;' />

Geben Sie unter &quot;Abbruch-Link&quot; die folgende vollst&auml;ndige URL ein:<input type='text' value='".$url['scheme']."://-USER_VARIABLE_1-?pm=payment-network&amp;status=canceled' readonly='true' onclick='this.select();' style='width: 98%;' /><br /><br />

<b>Nicht änderbare Eingabeparameter:</b> Aktivieren Sie &quot;Betrag&quot; und &quot;Verwendungszweck&quot;.<br /><br />

<b>Benachrichtigungen:</b> Erstellen Sie eine E-Mail Benachrichtigung <u>und</u> eine HTTP Benachrichtigung mit der <i>POST</i>-Methode an die folgende vollst&auml;ndige URL:<input type='text' value='".WB_URL."/modules/bakery/payment_methods/payment-network/report.php' readonly='true' onclick='this.select();' style='width: 98%;' /><br /><br />

<b>Passw&ouml;rter und Hash-Algorithmus:</b> Legen Sie ein Projekt-Passwort und ein Benachrichtigungspasswort fest <u>und</u> aktivieren Sie die Input-Pr&uuml;fung mit dem Hash-Algorithmus <i>SHA1</i>.";

// USED BY FILE bakery/payment_methods/payment-network/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'SOFORT &Uuml;berweisung';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Mit SOFORT &Uuml;berweisung k&ouml;nnen Sie bequem, einfach und sicher ohne Registrierung mit Ihrem Online-Banking Konto bezahlen.';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'Bezahlen Sie Ihre Bestellung online &uuml;ber Ihr Online-Banking Konto. Sie ben&ouml;tigen lediglich Bankkontonummer, Bankleitzahl, PIN und TAN.';
$MOD_BAKERY[$payment_method]['TXT_SECURITY'] = 'Mehr Informationen zur Zahlungssicherheit finden Sie auf der';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'Die Zahlungsabwicklung l&auml;uft &uuml;ber einen sicheren Server von SOFORT &Uuml;berweisung.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'Nach Ihrer Transaktion erhalten Sie per E-Mail unsere Auftragsbest&auml;tigung.';
$MOD_BAKERY[$payment_method]['TXT_WEBSITE'] = 'SOFORT &Uuml;berweisung Website';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'Ich bezahle per SOFORT &Uuml;berweisung';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'Zur Zahlungsabwicklung werden Sie zu einen sicheren Server von SOFORT &Uuml;berweisung weitergeleitet.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Jetzt zu SOFORT &Uuml;berweisung wechseln';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Summe inkl Mwst + Versand';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Besten Danke f&uuml;r Ihre online Zahlung. Ihre Transaktion bei SOFORT &Uuml;berweisung wurde abgeschlossen.<br />Unsere Auftragsbest&auml;tigung wurde Ihnen per E-Mail zugesandt.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'Die gew&uuml;nschten Artikel senden wir Ihnen unverz&uuml;glich zu.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'Es ist ein Problem aufgetreten. Ihre Transaktion konnte nicht abgeschlossen werden.<br />Bitte wenden Sie sich an den Shop-Betreiber.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'Sie haben Ihre Zahlung bei SOFORT &Uuml;berweisung abgebrochen.<br />M&ouml;chten Sie Ihren Einkauf trotzdem fortsetzen?';

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
	Zahlungsart: SOFORT Überweisung

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
