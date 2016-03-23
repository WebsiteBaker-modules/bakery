<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2016, Christoph Marti

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



//  MODIFY THE LINES BELOW TO FIT THE PAYMENT GATEWAY REQUIREMENTS
// ***************************************************************

// Use payment gateway sandbox url for testing
$testing = false;

// URLs of the payment gateway provider
$payment_gateway_url = "https://www.paypal.com/cgi-bin/webscr";
$sandbox_url         = "https://www.sandbox.paypal.com/cgi-bin/webscr";
$security_info_url   = "https://www.paypal.com/[SETTING_SHOP_COUNTRY]/cgi-bin/webscr?cmd=_security-center-outside";


// Set URL for testing or productiv site
$payment_gateway_url = $testing ? $sandbox_url : $payment_gateway_url;



// DEFINE THE PAYMENT METHOD SETTINGS
// **********************************

/*
  Define the payment method settings that the shop admin will have to set in the Bakery backend.
  Make sure that every var set below has its counterpart in the payment method language files:
  eg. $field_1 = 'email';
      $MOD_BAKERY[$payment_method]['TXT_EMAIL'] = 'E-Mail';
  =>  'email' will be converted to uppercase 'TXT_EMAIL'
*/  
$field_1 = 'email';
$field_2 = 'page';
$field_3 = 'auth_token';
$field_4 = '';
$field_5 = '';
$field_6 = '';



// PAYMENT METHOD INFO
// *******************

$payment_method_name    = 'PayPal';
$payment_method_version = '0.7';
$payment_method_author  = 'Christoph Marti';
$requires_bakery_module = '1.7';
