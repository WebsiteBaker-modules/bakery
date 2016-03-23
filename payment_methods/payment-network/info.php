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

// Payment gateway testmode
// No sandbox available. Use the provided test account numbers. See for more information:
// Login > My projects > Select project > Base settings > Test the project > Available test accounts


// URLs of the payment gateway provider
$payment_gateway_url = "https://www.sofortueberweisung.de/payment/start";

// Currently supported website languages by payment-network.com
$security_info['DE'] = "https://www.payment-network.com/sue_de/kaeuferbereich/sicherheit";
$security_info['EN'] = "https://www.payment-network.com/deb_com_en/customerarea/security";
$security_info['FR'] = "https://www.payment-network.com/deb_com_fr/customerarea/security";
$security_info['NL'] = "https://www.payment-network.com/deb_com_nl/customerarea/security";
$security_info['IT'] = "https://www.payment-network.com/deb_com_it/clienti/sicurezza";
$security_info['ES'] = "https://www.payment-network.com/deb_com_es/para_compradores/seguridad";
$security_info['PL'] = "https://www.payment-network.com/deb_com_pl/dlakupujacego/bezpieczenstwo";



// DEFINE THE PAYMENT METHOD SETTINGS
// **********************************

/*
  Define the payment method settings that the shop admin will have to set in the Bakery backend.
  Make sure that every var set below has its counterpart in the payment method language files:
  eg. $field_1 = 'email';
      $MOD_BAKERY[$payment_method]['TXT_EMAIL'] = 'E-Mail';
  =>  'email' will be converted to uppercase 'TXT_EMAIL'
*/  
$field_1 = 'user_id';
$field_2 = 'project_id';
$field_3 = 'project_pw';
$field_4 = 'notification_pw';
$field_5 = '';
$field_6 = '';



// PAYMENT METHOD INFO
// *******************

$payment_method_name    = 'Payment Network';
$payment_method_version = '0.3';
$payment_method_author  = 'Christoph Marti';
$requires_bakery_module = '1.7';
