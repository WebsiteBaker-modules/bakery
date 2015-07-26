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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Include info file
include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/info.php');

// Look for payment method language file
if (LANGUAGE_LOADED) {
    include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
        include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
    }
}

// Get the payment method settings from db
$query_payment_methods = $database->query("SELECT value_1, value_2, value_3, value_4, value_5, value_6 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = '$payment_method'");
if ($query_payment_methods->numRows() > 0) {
	$payment_methods = $query_payment_methods->fetchRow();
	// value_1 to value_6 correspond to the payment method settings field_1 to field_6 in the info.php file
	$value_1 = stripslashes($payment_methods['value_1']);  // user_id
	$value_2 = stripslashes($payment_methods['value_2']);  // project_id
	$value_3 = stripslashes($payment_methods['value_3']);  // project_pw
	$value_4 = stripslashes($payment_methods['value_4']);  // notification_pw (used by http-response notification)
	$value_5 = stripslashes($payment_methods['value_5']);
	$value_6 = stripslashes($payment_methods['value_6']);
}

// Get customer order date from db
$query_customer = $database->query("SELECT order_date FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");
if ($query_customer->numRows() > 0) {
	$fetch_customer = $query_customer->fetchRow();
}
$order_date = stripslashes($fetch_customer['order_date']);

// Make array with data sent to payment gateway
$post_data = array(
	'user_id'                 => $value_1,
	'project_id'              => $value_2,
	'sender_holder'           => '',
	'sender_account_number'   => '',
	'sender_bank_code'        => '',
	'sender_country_id'       => $cust_country,
	'amount'                  => $_SESSION['bakery']['order_total'],
	'currency_id'             => $setting_shop_currency,
	'reason_1'                => $MOD_BAKERY['TXT_ORDER']." ".$setting_shop_name,
	'reason_2'                => $MOD_BAKERY['TXT_ORDER_ID'].': '.$order_id,
	'user_variable_0'         => $order_id,
	'user_variable_1'         => str_ireplace(array('http://', 'https://'), '', $setting_continue_url), // url w/o scheme
	'user_variable_2'         => '',
	'user_variable_3'         => '',
	'user_variable_4'         => '',
	'user_variable_5'         => '',
	'project_password'        => $value_3
);

// Generate security hash 
$hash_string = implode('|', $post_data); 
$hash        = sha1($hash_string); 

// Hidden gateway data
$pay_gateway_data = '';
foreach ($post_data as $name => $value) {
	if (!empty($value) && $name != 'project_password') {
		$pay_gateway_data .= "\n\t\t\t<input type='hidden' name='$name' value='$value' />";
	}
}

$pay_gateway_data .= "\n\t\t\t".'<input type="hidden" name="hash" value="'.$hash.'" />';
$pay_gateway_data .= "\n\t\t\t".'<input type="hidden" name="interface_version" value="pn_wbb_v'.$payment_method_version.'" />';
