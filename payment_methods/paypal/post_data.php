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
	$value_1 = stripslashes($payment_methods['value_1']); // email
	$value_2 = stripslashes($payment_methods['value_2']); // page style
	$value_3 = stripslashes($payment_methods['value_3']); // auth token
	$value_4 = stripslashes($payment_methods['value_4']);
	$value_5 = stripslashes($payment_methods['value_5']);
	$value_6 = stripslashes($payment_methods['value_6']);
}

// Charset
if (defined('DEFAULT_CHARSET')) { $charset = DEFAULT_CHARSET; } else { $charset = 'utf-8'; }

// For US use state code instead of state name
if ($cust_country == 'US') {
	$cust_state = $cust_state_code;
}

// Workarround to prevent WB email filter by using html entities
// Do not submit email addresses like eg. firstname(dot)lastname(at)example(dot)com since
// the payment gateway will not recognize it
$search_arr  = array('.', '@');
$replace_arr = array('&#46;', '&#64;');
$pg_business = str_ireplace($search_arr, $replace_arr, $value_1);
$pg_email    = str_ireplace($search_arr, $replace_arr, $cust_email);


// Pass entire payment to PayPal, regardless of individual items
$post_data = array(
	'cmd'           => '_ext-enter',
	'redirect_cmd'  => '_xclick',
	'business'      => $pg_business,
	'item_name'     => $MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'],
	'invoice'       => $order_id,
	'page_style'    => $value_2,
	'no_shipping'   => 1,
	'currency_code' => $setting_shop_currency,
	'amount'        => $_SESSION['bakery']['order_total'],
	'charset'       => $charset,
	'email'         => $pg_email,
	'first_name'    => $cust_first_name,
	'last_name'     => $cust_last_name,
	'address1'      => $cust_street,
	'city'          => $cust_city,
	'state'         => $cust_state,
	'country'       => $cust_country,
	'zip'           => $cust_zip,
	'night_phone_b' => $cust_phone,
	'return'        => $setting_continue_url.'?pm='.$payment_method,
	'notify_url'    => WB_URL . '/modules/bakery/payment_methods/' . $payment_method . '/ipn.php',
	'cancel_return' => $setting_continue_url.'?pm='.$payment_method.'&status=canceled'
);

/*
// Pass individual items to PayPal
$post_data = array(
	'cmd'           => '_cart',
	'upload'        => '1',
	'business'      => $pg_business,
	'invoice'       => $order_id,
	'page_style'    => $value_2,
	'no_shipping'   => 1,
	'currency_code' => $setting_shop_currency,
	'shipping'      => $shipping,
	'charset'       => $charset,
	'email'         => $pg_email,
	'first_name'    => $cust_first_name,
	'last_name'     => $cust_last_name,
	'address1'      => $cust_street,
	'city'          => $cust_city,
	'state'         => $cust_state,
	'country'       => $cust_country,
	'zip'           => $cust_zip,
	'night_phone_b' => $cust_phone,
	'return'        => $setting_continue_url.'?pm='.$payment_method,
	'notify_url'    => WB_URL . '/modules/bakery/payment_methods/' . $payment_method . '/ipn.php',
	'cancel_return' => $setting_continue_url.'?pm='.$payment_method.'&status=canceled'
);
$n = sizeof($items);
for ($i = 1; $i <= $n; $i++) {
	$attribut = $items[$i]['html_show_attribute'] != '' ? ' ('.$items[$i]['html_show_attribute'].')' : '';
	$post_data['item_name_'.$i] = $items[$i]['name'].$attribut;
	$post_data['amount_'.$i]    = $items[$i]['price'];
	$post_data['quantity_'.$i]  = $items[$i]['quantity'];
}
*/

if ($testing) {
	echo '<pre>';
	echo '<b>DEBUG:<br />Data posted to PayPal:</b><br />';
	print_r($post_data);
	echo '</pre>';
}

// Hidden gateway data
$pay_gateway_data = '';
foreach ($post_data as $name => $value) {
	$pay_gateway_data .= "\n\t\t\t<input type='hidden' name='$name' value='$value' />";
}
