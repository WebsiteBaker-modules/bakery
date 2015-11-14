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


// PAYMENT METHOD PAYMENT-NETWORK
// ******************************

// Get the current url scheme
$url = parse_url(WB_URL);

// SETTINGS - USED BY BACKEND
$MOD_BAKERY[$payment_method]['TXT_NAME'] = 'SOFORT Banking';
$MOD_BAKERY[$payment_method]['TXT_USER_ID'] = 'Customer no';
$MOD_BAKERY[$payment_method]['TXT_PROJECT_ID'] = 'Project no';
$MOD_BAKERY[$payment_method]['TXT_PROJECT_PW'] = 'Project password';
$MOD_BAKERY[$payment_method]['TXT_NOTIFICATION_PW'] = 'Notification password';
$MOD_BAKERY[$payment_method]['TXT_NOTICE'] = "
<b>SOFORT Banking Extended settings</b><br />
Log in to your <a href='https://www.sofortueberweisung.de/payment/users/login' target='_blank'>SOFORT Banking</a> account: Go to &quot;My projects&quot; &gt; &quot;Select a project&quot; &gt; &quot;Extended settings&quot;<br /><br />

<b>Shop system interface:</b> Activate &quot;Automatic redirection&quot; and copy&amp;paste the full url as shown below to the field &quot;Success link&quot;:<input type='text' value='".$url['scheme']."://-USER_VARIABLE_1-?pm=payment-network&transaction_id=-TRANSACTION-' readonly='true' onclick='this.select();' style='width: 98%;' />

Copy&amp;paste the full url as shown below to the field &quot;Abort link&quot;:<input type='text' value='".$url['scheme']."://-USER_VARIABLE_1-?pm=payment-network&amp;status=canceled' readonly='true' onclick='this.select();' style='width: 98%;' /><br /><br />

<b>Non changeable parameters:</b> Activate &quot;Amount&quot; und &quot;Purpose&quot;.<br /><br />

<b>Notifications:</b> Add an email notification <u>and</u> a HTTP notification using the <i>POST</i>-methode and the full url as shown below:<input type='text' value='".WB_URL."/modules/bakery/payment_methods/payment-network/report.php' readonly='true' onclick='this.select();' style='width: 98%;' /><br /><br />

<b>Passwords and hash algorithm:</b> Create a project password and a notification password <u>and</u> activate the input check using the hash algorithm <i>SHA1</i>.";

// USED BY FILE bakery/payment_methods/payment-network/gateway.php
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'SOFORT Banking';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Pay online with SOFORT Banking using your ebanking account: easy, safe, free... No need to sign up or create a new account.';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'Pay your order online using your ebanking account. Just enter your bank account number, clearing number, PIN und TAN.';
$MOD_BAKERY[$payment_method]['TXT_SECURITY'] = 'Learn more about paying safely on the SOFORT Banking security page';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'The payment processing is handled by the secure SOFORT Banking server.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'After completion of the transaction an order confirmation will be emailed to you.';
$MOD_BAKERY[$payment_method]['TXT_WEBSITE'] = 'SOFORT Banking Website';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'I will pay with SOFORT Banking';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'To handle the payment processing you will be redirected to a secure SOFORT Banking server.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Go to SOFORT Banking now';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Total incl tax + shipping';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Thank you for your online payment with SOFORT Banking! Your transaction has been completed.<br />Our order confirmation has been emailed to you.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'We will ship your order as soon as possible.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'A problem has occurred. The transaction has not been completed.<br />Please contact the shop admin.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'You have canceled your SOFORT Banking payment.<br />Do you like to continue shopping?';

// EMAIL CUSTOMER
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = 'Confirmation for your order at [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = 'Dear [CUSTOMER_NAME]

Thank you for shopping at [SHOP_NAME].
Please find below the information about the products you have ordered:
[ITEM_LIST]

We will ship the order to the address below:

[ADDRESS]


Thank you for the confidence you have placed in us.

Kind regards,
[SHOP_NAME]


';

// EMAIL SHOP
$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = 'New order at [SHOP_NAME]';
$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = 'Dear [SHOP_NAME] Administrator

NEW ORDER AT [SHOP_NAME]:
	Order #: [ORDER_ID]
	Payment method: SOFORT Banking

Shipping address:
[ADDRESS]

Invoice address:
[CUST_ADDRESS]

List of ordered items: 
[ITEM_LIST]


Customers message:
[CUST_MSG]


Kind regards,
[SHOP_NAME]


';



// If iso-8859-1 is set as WB default charset convert some utf-8 strings to iso-8859-1
if (defined('DEFAULT_CHARSET') && DEFAULT_CHARSET == 'iso-8859-1') {
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER']);
	$MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_SHOP']);
	$MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP'] = utf8_decode($MOD_BAKERY[$payment_method]['EMAIL_BODY_SHOP']);
}
