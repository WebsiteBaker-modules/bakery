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
$MOD_BAKERY[$payment_method]['TXT_TITLE'] = 'Mollie online payments';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1'] = 'Our paymentprovider Mollie allows you to use several different payment methods.';
$MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2'] = 'You will enter the secure page of our payment provider.';
$MOD_BAKERY[$payment_method]['TXT_SECURE'] = 'Choose your payment method.';
$MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE'] = 'After succesfull payment you will receive an email with our order confirmation.';
$MOD_BAKERY[$payment_method]['TXT_PAY'] = 'I pay online using Mollie';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT'] = 'You are transfered to the secure website of Mollie.';
$MOD_BAKERY[$payment_method]['TXT_REDIRECT_NOW'] = 'Go to mollie';
$MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'] = 'Total amount incl. VAT and shipping';
$MOD_BAKERY[$payment_method]['ERROR_CREATING_PM'] = 'Error';

// USED BY FILE bakery/view_confirmation.php
$MOD_BAKERY[$payment_method]['TXT_SUCCESS'] = 'Thank you for your online payment! Your transaction has been completed.<br />Our order confirmation has been emailed to you.';
$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'] = 'We will ship your order as soon as possible.';
$MOD_BAKERY[$payment_method]['ERROR'] = 'A problem has occurred. The transaction has not been completed.<br />Please contact the shop admin.';
$MOD_BAKERY[$payment_method]['CANCELED'] = 'You have canceled your online payment.<br />Do you like to continue shopping?';

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
	Payment method: Mollie (iDEAL)

Shipping address:
[ADDRESS]

Invoice address:
[CUST_ADDRESS]

List of ordered items: 
[ITEM_LIST]


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

?>