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
$MOD_BAKERY[$payment_method]['TXT_EMAIL'] = 'PayPal Email';
$MOD_BAKERY[$payment_method]['TXT_PAGE'] = 'PayPal Page';
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
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'PayPal (Credit card)';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Pay online with PayPal using your credit card: easy, safe, free...';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'Pay your order online using your credit card or PayPal payment.';
$MOD_BAKERY[$payment_method]['TXT_SECURITY'] = 'Learn more about buying safely on the PayPal Security Center page';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'The payment processing is handled by the secure PayPal server.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'After completion of the transaction, our order confirmation and a PayPal receipt for your purchase will be emailed to you.';
$MOD_BAKERY[$payment_method]['TXT_WEBSITE'] = 'PayPal Website';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'I will pay with PayPal';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'To handle the payment processing you will be redirected to a secure PayPal server.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Go to PayPal now';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Total incl. tax and shipping';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Thank you for your online payment! Your transaction has been completed.<br />Our order confirmation and a PayPal receipt for your purchase has been emailed to you.';
$MOD_BAKERY[$payment_method]['TXT_PENDING'] = 'Thank you for your online payment! Your transaction will be processed shortly.<br />Our order confirmation and a PayPal receipt for your purchase will be emailed to you.';
$MOD_BAKERY[$payment_method]['TXT_TRANSACTION_STATUS'] = "PLEASE NOTE:\n\tThe transaction status is \"PENDING\".\n\tTo see all the transaction details, please log in to your PayPal account.";
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'We will ship your order as soon as possible.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'A problem has occurred. The transaction has not been completed.<br />Please contact the shop admin.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'You have canceled your PayPal payment.<br />Do you like to continue shopping?';

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
	Payment method: PayPal
[TRANSACTION_STATUS]

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
