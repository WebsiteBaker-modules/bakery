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

// Include WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Include WB template parser
require_once(WB_PATH.'/include/phplib/template.inc');

// Create template object for screen output
$tpl_so = new Template(WB_PATH.'/modules/bakery/templates/summary');
// Define how to deal with unknown {PLACEHOLDERS} (remove:=default, keep, comment)
$tpl_so->set_unknowns('keep');
// Define debug mode (0:=disabled (default), 1:=variable assignments, 2:=calls to get variable, 4:=debug internals)
$tpl_so->debug = 0;

// Create template object for invoice print
$tpl_ip = new Template(WB_PATH.'/modules/bakery/templates/invoice');
// Define how to deal with unknown {PLACEHOLDERS} (remove:=default, keep, comment)
$tpl_ip->set_unknowns('keep');
// Define debug mode (0:=disabled (default), 1:=variable assignments, 2:=calls to get variable, 4:=debug internals)
$tpl_ip->debug = 0;

// Get order id
$order_id = $_SESSION['bakery']['order_id'];



// CUSTOMERS MESSAGE
// *****************

// Get customers message
$cust_msg = !empty($_POST['cust_msg']) ? strip_tags($_POST['cust_msg']) : '';
// Save customers message in session var
$_SESSION['bakery']['cust_msg'] = $cust_msg;



// PAYMENT METHOD
// **************

// Put payment method selected by user into session var
if (!empty($_POST['payment_method'])) {
	$payment_method_arr = array_keys($_POST['payment_method']);
	$payment_method_arr = array_intersect($payment_method_arr, $setting_payment_methods);
	$_SESSION['bakery']['payment_method'] = $payment_method_arr[0];
}
$payment_method = $_SESSION['bakery']['payment_method'];



// CHECK IF CUSTOMER HAS AGREED TO TERMS AND CONDITIONS
// ****************************************************

if (!isset($_POST['agree']) || $_POST['agree'] != 'yes') {
	// Error message if customer has not agreed to terms and conditions, then show it again
	
	$tpl_so->set_file('agree_tac', 'err_agree_tac.htm');
	$tpl_so->set_var(array(
		'ERR_AGREE'				=>	$MOD_BAKERY['ERR_AGREE'],
		'SETTING_CONTINUE_URL'	=>	$setting_continue_url,
		'SETTING_TAC_URL'		=>	$setting_tac_url,
		'TXT_AGREE'				=>	$MOD_BAKERY['TXT_AGREE'],
		'SETTING_SHOP_NAME'		=>	$setting_shop_name,
		'TXT_SUBMIT_ORDER'		=>	$MOD_BAKERY['TXT_SUBMIT_ORDER']
	));
	$tpl_so->pparse('output', 'agree_tac');
	return;
}



// EMPTY CART
// **********

// If cart is empty, show an error message and a "continue shopping" button
$sql_result1 = $database->query("SELECT * FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id'");
$n_row = $sql_result1->numRows();
if ($n_row < 1) {
	// Show empty cart error message using template file
	$tpl_so->set_file('empty_cart', 'err_empty_cart.htm');
	$tpl_so->set_var(array(
		'ERR_CART_EMPTY'			=>	$MOD_BAKERY['ERR_CART_EMPTY'],
		'TXT_CONTINUE_SHOPPING'		=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING']
	));
	$tpl_so->pparse('output', 'empty_cart');
	return;
}



// GET ITEM DETAILS FROM DATABASE
// ******************************

// Get item id, attributes, sku, quantity, price and tax_rate from db order table
$i = 1;
while ($row1 = $sql_result1->fetchRow()) {
	foreach ($row1 as $field => $value) {
		if ($field != 'order_id') {
			$items[$i][$field] = $value;
			// Get item name and shipping from db items table
			if ($field == 'item_id') {
				$sql_result2 = $database->query("SELECT title, shipping, description FROM " .TABLE_PREFIX."mod_bakery_items WHERE item_id = '$row1[item_id]'");
				$row2 = $sql_result2->fetchRow();	
				$items[$i]['name'] = $row2[0];
				$items[$i]['shipping'] = $row2[1];
				$items[$i]['description'] = $row2[2];
			} 
		}
	}
	// Default if item has no attributes
	$items[$i]['html_show_attribute'] = '';
	$items[$i]['email_show_attribute'] = '';
	$items[$i]['attribute_price'] = 0;
	// Initialize vars
	$items[$i]['show_attribute'] = '';
	$attribute['operator'] = '';
	// Get item attribute ids
	if ($items[$i]['attributes'] != "none") {
		$attribute_ids = explode(",", $items[$i]['attributes']);
		foreach ($attribute_ids as $attribute_id) {
			// Get option name and attribute name, price, operator (=/+/-)
			$query_attributes = $database->query("SELECT o.option_name, a.attribute_name, ia.price, ia.operator FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_attributes a ON o.option_id = a.option_id INNER JOIN ".TABLE_PREFIX."mod_bakery_item_attributes ia ON a.attribute_id = ia.attribute_id WHERE ia.item_id = {$items[$i]['item_id']} AND ia.attribute_id = $attribute_id");
			$attribute = $query_attributes->fetchRow();
			// Calculate the item attribute prices sum depending on the operator
			if ($attribute['operator'] == "+") {
				$items[$i]['attribute_price'] = $items[$i]['attribute_price'] + $attribute['price'];
			} elseif ($attribute['operator'] == "-") {
				$items[$i]['attribute_price'] = $items[$i]['attribute_price'] - $attribute['price'];
			// If operator is '=' then override the item price by the attribute price
			} elseif ($attribute['operator'] == "=") {
				$items[$i]['price'] = $attribute['price'];
			}
			// Prepare option and attributes for display in cart table
			$items[$i]['show_attribute'] .= ", ".$attribute['option_name'].":&nbsp;".$attribute['attribute_name'];
		}
		// Now calculate item price including all attribute prices
		$items[$i]['price'] = $items[$i]['price'] + $items[$i]['attribute_price'];
		// Never undercut zero
		$items[$i]['price'] = $items[$i]['price'] < 0 ? 0 : $items[$i]['price'];
		// Make string with all item attributes 
		// HTML version: remove leading comma and space
		$items[$i]['html_show_attribute'] = substr($items[$i]['show_attribute'], 2);
		// Email version: add \n\t to html version and replace &nbsp; by space
		$items[$i]['email_show_attribute'] = "\n\t".str_replace("&nbsp;", " ", $items[$i]['html_show_attribute']);
	}
	// Increment counter
	$i++;
}



// MAKE CUSTOMER AND SHIPPING ADDRESS FOR DIFFERENT SYSTEMS
// ********************************************************

// Get charset
if (defined('DEFAULT_CHARSET')) { $charset = DEFAULT_CHARSET; } else { $charset = 'utf-8'; }

// Include country file depending on the language
if (LANGUAGE_LOADED) {
    if (file_exists(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php');
    }
}
else {
	require_once(WB_PATH.'/modules/bakery/languages/countries/EN.php');
}

// Set default state for countries without a state file
$MOD_BAKERY['TXT_STATE_CODE'][1] = '';
$MOD_BAKERY['TXT_STATE_NAME'][1] = '';
// Include state file depending on the shop country
if (file_exists(WB_PATH.'/modules/bakery/languages/states/'.$setting_shop_country.'.php')) {
	require_once(WB_PATH.'/modules/bakery/languages/states/'.$setting_shop_country.'.php');
}


// GET CUSTOMER DATA

// Arrays for all forms and fields
$forms   = array('cust', 'ship');
$fields  = array('company', 'first_name', 'last_name', 'tax_no', 'street', 'city', 'state', 'country', 'zip', 'email', 'phone');
// Get customer data from the session var
foreach ($forms as $form) {
	foreach ($fields as $field) {
		$field_var = $form.'_'.$field;
		if (isset($_SESSION['bakery'][$form][$field])) {
			$$field_var = $_SESSION['bakery'][$form][$field];
		} else {
			$$field_var = '';
		}
	}
}


// CUSTOMER ADDRESS

// Convert country code to country name
$country_key = array_keys($MOD_BAKERY['TXT_COUNTRY_CODE'], $cust_country);
$cust_country_name = $MOD_BAKERY['TXT_COUNTRY_NAME'][$country_key[0]];
// Convert country to uppercase
if (function_exists('mb_strtoupper')) {
	$cust_country_name = mb_strtoupper(entities_to_umlauts($cust_country_name, $charset), $charset);
}

// Retain state code for sales tax calculation
$cust_state_code = $cust_state;
// Convert state code to state name
if ($state_key = array_keys($MOD_BAKERY['TXT_STATE_CODE'], $cust_state)) {
	$cust_state = $MOD_BAKERY['TXT_STATE_NAME'][$state_key[0]];
	$cust_state = entities_to_umlauts($cust_state, $charset);
}

// Join customer first and last name
$cust_name = $cust_first_name." ".$cust_last_name;

// Prepare field customer company
if ($setting_company_field != "show" OR $cust_company == '') {
	$email_cust_company = '';
	$cust_company       = '';
}
else {
	$email_cust_company  = $cust_company."\n\t";
	$cust_company        = $cust_company.'<br />';
}

// Show address with state field
if ($setting_state_field == "show") {
	if ($setting_zip_location == "end") {
		// Show zip at the end of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_city, $cust_state $cust_zip<br />$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_city.", ".$cust_state." ".$cust_zip."\n\t".$cust_country_name."\n\n\t".$cust_phone."\n";
	}
	else {
		// Show zip inside of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_country-$cust_zip $cust_city<br />$cust_state<br />$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_country."-".$cust_zip." ".$cust_city."\n\t".$cust_state."\n\t".$cust_country_name."\n\n\t".$cust_phone."\n";
	}
}
// Show address w/o state field	
else {
	if ($setting_zip_location == "end") {
		// Show zip at the end of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_city<br />$cust_country-$cust_zip<br />$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_city."\n\t".$cust_country."-".$cust_zip."\n\t".$cust_country_name."\n\n\t".$cust_phone."\n";
	}
	else {	
		// Show zip inside of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_country-$cust_zip $cust_city<br />$cust_country_name<br /><br />$cust_phone<br />$cust_email";	
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_country."-".$cust_zip." ".$cust_city."\n\t".$cust_country_name."\n\n\t".$cust_phone."\n";
	}
}

// Make var that contains either customer address or - if existing - shipping address
$email_address = $email_cust_address;
$address       = $cust_address;



// SHIPPING ADDRESS

if ($setting_shipping_form == "always" || $_SESSION['bakery']['ship_data']) {

	// Convert country code to country name
	$country_key = array_keys($MOD_BAKERY['TXT_COUNTRY_CODE'], $ship_country);
	$ship_country_name = $MOD_BAKERY['TXT_COUNTRY_NAME'][$country_key[0]];
	// Convert country to uppercase
	if (function_exists('mb_strtoupper')) {
		$ship_country_name = mb_strtoupper(entities_to_umlauts($ship_country_name, $charset), $charset);
	}

	// Convert state code to state name
	if ($state_key  = array_keys($MOD_BAKERY['TXT_STATE_CODE'], $ship_state)) {
		$ship_state = $MOD_BAKERY['TXT_STATE_NAME'][$state_key[0]];
		$ship_state = entities_to_umlauts($ship_state, $charset);
	}

	// Join customer first and last name
	$ship_name = $ship_first_name." ".$ship_last_name;

	// Prepare field shipping company
	if ($setting_company_field != "show" OR $ship_company == '') {
		$email_ship_company = '';
		$ship_company       = '';
	}
	else {
		$email_ship_company  = $ship_company."\n\t";
		$ship_company        = $ship_company.'<br />';
	}

	// Show address with state field
	if ($setting_state_field == "show") {
		if ($setting_zip_location == "end") {
			// Show zip at the end of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_city, $ship_state $ship_zip<br />$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_city.", ".$ship_state." ".$ship_zip."\n";
		}
		else {
			// Show zip inside of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_country-$ship_zip $ship_city<br />$ship_state<br />$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_country."-".$ship_zip." ".$ship_city."\n\t".$ship_state."\n\t".$ship_country_name."\n";		
		}
	}
	// Show address w/o state field	
	else {
		if ($setting_zip_location == "end") {
			// Show zip at the end of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_city<br />$ship_country-$ship_zip<br />$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_city."\n\t".$ship_country."-".$ship_zip."\n\t".$ship_country_name."\n";
		}
		else {	
			// Show zip inside of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_country-$ship_zip $ship_city<br />$ship_country_name";	
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_country."-".$ship_zip." ".$ship_city."\n\t".$ship_country_name."\n";		
		}
	}
	// Make var that contains either customer address or - if existing - the shipping address
	$email_address = $email_ship_address;
	$address       = $ship_address;
}
// No shipping address
else {
	$ship_address                   = '';
	$MOD_BAKERY['TXT_SHIP_ADDRESS'] = '';
	$email_ship_address             = "\t".$TEXT['NONE'];
}



// DISPLAY CUSTOMER TAX NUMBER
// ***************************

$display_tax_no = '';
if ($cust_tax_no == '') {
	$display_tax_no = 'none';
}



// CALCULATE ITEMS SALES TAX
// *************************

// Initialize vars
$sales_tax        = 0;
$f_tax_rate_array = array();

// Calculate items sales tax
for ($i = 1; $i <= sizeof($items); $i++) {
	if ($setting_tax_included == 'included') {
		// Calculate tax amount for prices including tax (brutto)
		$sales_tax = $sales_tax + $items[$i]['price'] * $items[$i]['quantity'] * $items[$i]['tax_rate'] / (100 + $items[$i]['tax_rate']);
	}
	else {
		// Calculate tax amount for prices excluding tax (netto)
		$sales_tax = $sales_tax + $items[$i]['price'] * $items[$i]['quantity'] / 100 * $items[$i]['tax_rate'];
	}
	// Get tax rate(s) for display
	$f_tax_rate_array[] = number_format($items[$i]['tax_rate'], 1);
}



// SHOW TITLE AND ADDRESS
// **********************

// Assign page filename for tracking with Google Analytics _trackPageview() function
global $ga_page;
$ga_page = '/view_summary.php';

// Show summary title and address using template file
$tpl_so->set_file('summary_address', 'address.htm');
$tpl_so->set_var(array(
	'TXT_ORDER_SUMMARY'		=>	$MOD_BAKERY['TXT_ORDER_SUMMARY'],
	'TXT_ORDER_ID'			=>	$MOD_BAKERY['TXT_ORDER_ID'],
	'ORDER_ID'				=>	$order_id,
	'WB_URL'				=>	WB_URL,
	'SETTING_CONTINUE_URL'	=>	$setting_continue_url,
	'TXT_ADDRESS'			=>	$MOD_BAKERY['TXT_ADDRESS'],
	'CUST_ADDRESS'			=>	$cust_address,
	'DISPLAY_TAX_NO'		=>	$display_tax_no,
	'TXT_CUST_TAX_NO'		=>	$MOD_BAKERY['TXT_CUST_TAX_NO'],
	'CUST_TAX_NO'			=>	$cust_tax_no,
	'TXT_SHIP_ADDRESS'		=>	$MOD_BAKERY['TXT_SHIP_ADDRESS'],
	'SHIP_ADDRESS'			=>	$ship_address,
	'TXT_MODIFY_ADDRESS'	=>	$MOD_BAKERY['TXT_MODIFY_ADDRESS']
));
$tpl_so->pparse('output', 'summary_address');



// SHOW PAYMENT METHOD
// *******************

// Look for payment method language file
if (LANGUAGE_LOADED) {
    include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
        include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
    }
}

// Show payment methode selected by customer
$tpl_so->set_file('payment_method', 'pay_method.htm');
$tpl_so->set_var(array(
	'TXT_SELECTED_PAY_METHOD'	=>	$MOD_BAKERY['TXT_SELECTED_PAY_METHOD'],
	'PAY_METHOD'				=>	$MOD_BAKERY[$payment_method]['TXT_TITLE'],
	'TXT_MODIFY_PAY_METHODS'	=>	$MOD_BAKERY['TXT_MODIFY_PAY_METHODS']
));
$tpl_so->pparse('output', 'payment_method');



// SHOW SUMMARY TABLE
// ******************

// Determine shipping per item sum of all items specified
for ($i = 1; $i <= sizeof($items); $i++) {
	$shipping_array[] = $items[$i]['shipping'];
}
$shipping_sum = array_sum($shipping_array);
// Set shipping to 0 if customer buys online and picks it up in store (bopis)
if ($payment_method == 'bopis') {
	$shipping_sum = 0;
}
// Check if we have to display a tax rate column in the invoice
$num_of_tax_rates = count(array_unique($f_tax_rate_array));


// Prepare table settings depending on different conditions:
// Shipping per item and tax rates
if ($shipping_sum > 0 && $num_of_tax_rates > 1) {
	$display_shipping = '';
	$display_tax_rate = '';
	$colspan_summary_l = 6;
	$colspan_summary_m = 5;
	$colspan_summary_s = 4;
	$colspan_invoice_l = 7;
	$colspan_invoice_m = 6;
	$colspan_invoice_s = 5;
}
// Shipping per item but no tax rates
elseif ($shipping_sum > 0 && $num_of_tax_rates <= 1) {
	$display_shipping = '';
	$display_tax_rate = "none";
	$colspan_summary_l = 6;
	$colspan_summary_m = 5;
	$colspan_summary_s = 4;
	$colspan_invoice_l = 6;
	$colspan_invoice_m = 5;
	$colspan_invoice_s = 4;
}
// No shipping per item but tax rates
elseif ($shipping_sum <= 0 && $num_of_tax_rates > 1) {
	$display_shipping = "none";
	$display_tax_rate = '';
	$colspan_summary_l = 5;
	$colspan_summary_m = 4;
	$colspan_summary_s = 3;
	$colspan_invoice_l = 6;
	$colspan_invoice_m = 5;
	$colspan_invoice_s = 4;
}
// No shipping per item and no tax rates
else {
	$display_shipping = "none";
	$display_tax_rate = "none";
	$colspan_summary_l = 5;
	$colspan_summary_m = 4;
	$colspan_summary_s = 3;
	$colspan_invoice_l = 5;
	$colspan_invoice_m = 4;
	$colspan_invoice_s = 3;
}

// Make summary table header for screen using template file
$tpl_so->set_file('summary_table_header', 'table_header.htm');
$tpl_so->set_var(array(
	'TXT_SKU'				=>	$MOD_BAKERY['TXT_SKU'],
	'TXT_NAME'				=>	$MOD_BAKERY['TXT_NAME'],
	'TXT_QUANTITY'			=>	$MOD_BAKERY['TXT_QUANTITY'],
	'TXT_PRICE'				=>	$MOD_BAKERY['TXT_PRICE'],
	'SETTING_SHOP_CURRENCY'	=>	$setting_shop_currency,
	'DISPLAY_SHIPPING'		=>	$display_shipping,
	'TXT_SHIPPING'			=>	$MOD_BAKERY['TXT_SHIPPING'],
	'TXT_SUM'				=>	$MOD_BAKERY['TXT_SUM'],
	'COLSPAN_L'				=>	$colspan_summary_l
));
$tpl_so->pparse('output', 'summary_table_header');

// Make invoice table header for invoice print using template file
$tpl_ip->set_file('invoice_table_header', 'table_header.htm');
$tpl_ip->set_var(array(
	'TXT_SKU'				=>	$MOD_BAKERY['TXT_SKU'],
	'TXT_NAME'				=>	$MOD_BAKERY['TXT_NAME'],
	'TXT_QUANTITY'			=>	$MOD_BAKERY['TXT_QUANTITY'],
	'TXT_PRICE'				=>	$MOD_BAKERY['TXT_PRICE'],
	'SETTING_SHOP_CURRENCY'	=>	$setting_shop_currency,
	'DISPLAY_SHIPPING'		=>	$display_shipping,
	'TXT_SHIPPING'			=>	$MOD_BAKERY['TXT_SHIPPING'],
	'DISPLAY_TAX_RATE'		=>	$display_tax_rate,
	'TXT_TAX'				=>	$MOD_BAKERY['TXT_TAX'],
	'TXT_SUM'				=>	$MOD_BAKERY['TXT_SUM'],
	'COLSPAN_L'				=>	$colspan_invoice_l
));
$tpl_ip->parse('invoice_print', 'invoice_table_header', true);


// Initialize vars
$count_items            = 0;
$order_subtotal         = 0;
$item_shipping_subtotal = 0;
$order_total            = 0;
$email_item_list        = '';


// LOOP THROUGH ITEMS
for ($i = 1; $i <= sizeof($items); $i++) {

	// Calculate order subtotal and shipping per item subtotal (w/o tax and general shipping)
	if ($shipping_sum > 0) {
		$f_price    = number_format($items[$i]['price'], 2, $setting_dec_point, $setting_thousands_sep);
		$f_shipping = number_format($items[$i]['shipping'], 2, $setting_dec_point, $setting_thousands_sep);
  		// See http://www.bakery-shop.ch/#shipping_total
		// $item_total = $items[$i]['quantity'] * ($items[$i]['price'] + $items[$i]['shipping']);
		$item_total             = $items[$i]['quantity'] * $items[$i]['price'];
		$item_shipping          = $items[$i]['quantity'] * $items[$i]['shipping'];
		$f_total                = number_format($item_total, 2, $setting_dec_point, $setting_thousands_sep);
		$order_subtotal         = $order_subtotal + $item_total;
		$item_shipping_subtotal = $item_shipping_subtotal + $item_shipping;
		$f_order_subtotal       = number_format($order_subtotal, 2, $setting_dec_point, $setting_thousands_sep);
	}
	// Calculate order subtotal without shipping per item (w/o tax and general shipping)
	else {
		$f_price          = number_format($items[$i]['price'], 2, $setting_dec_point, $setting_thousands_sep);
		$f_shipping       = 0;
		$item_total       = $items[$i]['quantity'] * $items[$i]['price'];
		$f_total          = number_format($item_total, 2, $setting_dec_point, $setting_thousands_sep);
		$order_subtotal   = $order_subtotal + $item_total;
		$f_order_subtotal = number_format($order_subtotal, 2, $setting_dec_point, $setting_thousands_sep);
	}

	// Count number of items for shipping per item
	$count_items += $items[$i]['quantity'];

	// Make table of ordered items for screen output using template file
	$tpl_so->set_file('summary_table_body', 'table_body.htm');
	$tpl_so->set_var(array(
		'SKU'				=>	$items[$i]['sku'],
		'NAME'				=>	$items[$i]['name'],
		'ATTRIBUTE'			=>	$items[$i]['html_show_attribute'],
		'DESCRIPTION'		=>	$items[$i]['description'],
		'QUANTITY'			=>	$items[$i]['quantity'],
		'PRICE'				=>	$f_price,
		'DISPLAY_SHIPPING'	=>	$display_shipping,
		'SHIPPING'			=>	$f_shipping,
		'TOTAL'				=>	$f_total
	));
	$tpl_so->parse('screen_output', 'summary_table_body', true);

	// Make table of ordered items for invoice print using template file
	$tpl_ip->set_file('invoice_table_body', 'table_body.htm');
	$tpl_ip->set_var(array(
		'SKU'				=>	$items[$i]['sku'],
		'NAME'				=>	$items[$i]['name'],
		'ATTRIBUTE'			=>	$items[$i]['html_show_attribute'],
		'DESCRIPTION'		=>	$items[$i]['description'],
		'QUANTITY'			=>	$items[$i]['quantity'],
		'PRICE'				=>	$f_price,
		'DISPLAY_SHIPPING'	=>	$display_shipping,
		'SHIPPING'			=>	$f_shipping,
		'DISPLAY_TAX_RATE'	=>	$display_tax_rate,
		'ITEM_TAX_RATE'		=>	number_format($items[$i]['tax_rate'], 1),
		'TOTAL'				=>	$f_total
	));
	$tpl_ip->parse('invoice_print', 'invoice_table_body', true);

	// Make list of ordered items for email with shipping per item
	// Do not change text indent since it fits to the email
	if ($shipping_sum > 0) {
		$email_item_list .= "
	{$MOD_BAKERY['TXT_SKU']}: {$items[$i]['sku']}
	{$MOD_BAKERY['TXT_NAME']}: {$items[$i]['name']}{$items[$i]['email_show_attribute']}
	{$MOD_BAKERY['TXT_QUANTITY']}: {$items[$i]['quantity']}
	{$MOD_BAKERY['TXT_PRICE']}: $setting_shop_currency $f_price
	{$MOD_BAKERY['TXT_SHIPPING']}: $setting_shop_currency $f_shipping
	{$MOD_BAKERY['TXT_SUM']}: $setting_shop_currency $f_total\n";
	}
	// Make list of ordered items for email without shipping per item
	// Do not change text indent since it fits to the email
	else {
		$email_item_list .= "
	{$MOD_BAKERY['TXT_SKU']}: {$items[$i]['sku']}
	{$MOD_BAKERY['TXT_NAME']}: {$items[$i]['name']}{$items[$i]['email_show_attribute']}
	{$MOD_BAKERY['TXT_QUANTITY']}: {$items[$i]['quantity']}
	{$MOD_BAKERY['TXT_PRICE']}: $setting_shop_currency $f_price
	{$MOD_BAKERY['TXT_SUM']}: $setting_shop_currency $f_total\n";
	}
}



// CALCULATE SHIPPING	
// ******************

// Select the shipping cost-effective country
$effective_country = $_SESSION['bakery']['ship_data'] ? $ship_country : $cust_country;

// Select shipping rate
if ($effective_country == $setting_shop_country) {
   $setting_shipping_rate = $setting_shipping_domestic;
}
elseif (in_array($effective_country, $setting_zone_countries)) {
	$setting_shipping_rate = $setting_shipping_zone;
}
else {
	$setting_shipping_rate = $setting_shipping_abroad;
}

// Calculate shipping
if ($setting_shipping_method == "highest") {
	// Determine highest shipping per item of all items specified
	$highest_shipping = $shipping_array;
	rsort($highest_shipping, SORT_NUMERIC);
	$shipping = $highest_shipping[0];
} else {
	// Determine shipping and add shipping per item subtotal
	if ($setting_shipping_method == "flat") {
		$shipping = $setting_shipping_rate;
	}
	elseif ($setting_shipping_method == "items") {
		$shipping = $setting_shipping_rate * $count_items;
	}
	elseif ($setting_shipping_method == "positions") {
		$shipping = $setting_shipping_rate * sizeof($items);
	}
	elseif ($setting_shipping_method == "percentage") {
		$shipping = $order_subtotal / 100 * $setting_shipping_rate;
	}
	else {
		$shipping = 0;
	}
	$shipping = $shipping + $item_shipping_subtotal;  // See http://www.bakery-shop.ch/#shipping_total
}

// Text normal shipping
$txt_shipping_cost = $MOD_BAKERY['TXT_SHIPPING_COST'];
$css_class_cart_shipping_f = "mod_bakery_cart_shipping_f";
$css_class_invoice_shipping_b = "mod_bakery_invoice_shipping_b";

// Free shipping for larger amounts
if ($order_subtotal >= $setting_free_shipping) {
	$shipping = 0;
	// Text free shipping
	$txt_shipping_cost = $MOD_BAKERY['TXT_FREE_SHIPPING'];
	$css_class_cart_shipping_f = "mod_bakery_cart_free_shipping_f";
	$css_class_invoice_shipping_b = "mod_bakery_invoice_free_shipping_b";
} else {
	// If no free shipping and shipping tax rate is not 0, add shipping tax rate to the tax rates array
	if ($setting_tax_rate_shipping != 0) {
		$f_tax_rate_array[] = number_format($setting_tax_rate_shipping, 1);	
	}
}

// Set shipping to 0 if customer buys online and picks it up in store (bopis)
if ($payment_method == 'bopis') {
	$shipping = 0;
}

// Format shipping for display
$f_shipping = number_format($shipping, 2, $setting_dec_point, $setting_thousands_sep);

// Inform customers about free shipping limit using template file
if ($setting_free_shipping_msg == "show" && $setting_free_shipping > 0 && $order_subtotal < $setting_free_shipping) {
	$tpl_so->set_file('summary_free_shipping', 'free_shipping.htm');
	$tpl_so->set_var(array(
		'TXT_FREE_SHIPPING'			=>	$MOD_BAKERY['TXT_FREE_SHIPPING'],
		'TXT_OVER'					=>	$MOD_BAKERY['TXT_OVER'],
		'SETTING_SHOP_CURRENCY'		=>	$setting_shop_currency,
		'SETTING_FREE_SHIPPING'		=>	$setting_free_shipping
	));
	$tpl_so->pparse('output', 'summary_free_shipping');
}



// CALCULATE SHIPPING SALES TAX
// ****************************

// Calculate tax amount for shipping including tax (brutto)
if ($setting_tax_included == 'included') {
	$sales_tax_shipping = $shipping * $setting_tax_rate_shipping / (100 + $setting_tax_rate_shipping);
}
else {
	// Calculate tax amount for shipping excluding tax (netto)
	$sales_tax_shipping = $shipping / 100 * $setting_tax_rate_shipping;
}



// CHECK IF WE HAVE TO PAY SALES TAX
// *********************************

// Initialize var
$pay_sales_tax = false;

// Sales tax depending on country
if ($setting_tax_by == 'country') {

	// Special handling for EU tax zone according to EU law
	// http://www.websitebaker2.org/forum/index.php/topic,21690.msg145849.html
	if (strpos($setting_tax_group, $setting_shop_country) !== false &&
		strpos($setting_tax_group, $cust_country) !== false) {
		if ($cust_tax_no == '') {
			$pay_sales_tax = true;
		}
	}

	// All other countries
	else {
		if ($cust_country == $setting_shop_country) {
			$pay_sales_tax = true;
		}
	}
}

// Sales tax depending on state
elseif ($setting_tax_by == 'state') {
	if ($cust_state_code == $setting_shop_state) {
		$pay_sales_tax = true;
	}
}



// CALCULATE TOTAL	
// ***************

// Convert tax rate array into string for displaying
$f_tax_rate = '0%';
if (count($f_tax_rate_array) > 0) {
	$f_tax_rate_array = array_unique($f_tax_rate_array);
	$f_tax_rate = implode(' / ', $f_tax_rate_array).'%';
}

// Total item and shipping sales tax
$sales_tax = $sales_tax + $sales_tax_shipping;


// Calculate total dependig on settings and sales tax
// Setting no sales tax
if ($setting_tax_by == 'none') {
	$order_total = $order_subtotal + $shipping;
}
// Prices sales tax included
elseif ($setting_tax_included == 'included') {
	if ($pay_sales_tax) {
		$order_total = $order_subtotal + $shipping;
	} else {
		$sales_tax   = $sales_tax * -1;
		$order_total = $order_subtotal + $sales_tax + $shipping;
		$MOD_BAKERY['TXT_INCL'] = '- ';
	}
}
// Prices sales tax not included
else {
	if ($pay_sales_tax) {
		$order_total = $order_subtotal + $sales_tax + $shipping;
	} else {
		$order_total = $order_subtotal + $shipping;
		$f_tax_rate  = '0%';
		$sales_tax   = 0;
	}
	$MOD_BAKERY['TXT_INCL'] = '';
}

// Format output for display
$f_sales_tax   = number_format($sales_tax,   2, $setting_dec_point, $setting_thousands_sep);
$f_order_total = number_format($order_total, 2, $setting_dec_point, $setting_thousands_sep);
// Put rounded order total into the session var for later use with payment gateways
$_SESSION['bakery']['order_total'] = round($order_total, 2);

// Write shipping fee and sales tax into db
$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET shipping_fee = '$f_shipping', sales_tax = '$f_sales_tax' WHERE order_id='{$_SESSION['bakery']['order_id']}'");



// IF NEEDED ADD HIDDEN PAYMENT GATEWAY POST DATA
// **********************************************

// Include info file
if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/info.php')) {
    include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/info.php');
}

// Get checkout url for either onsite payment method or payment gateway 
$checkout_url = isset($payment_gateway_url) ? $payment_gateway_url : $setting_continue_url;

// Get hidden gateway data
$pay_gateway_data = '';
if (is_file(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/post_data.php')) {
	include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/post_data.php');
}



// VIEW TABLE OF SALES TAX, SHIPPING, ORDER TOTAL AND DISPLAY BUTTONS
// ******************************************************************

// Depending on general settings show/hide sales taxes	
$display_tax = $setting_tax_by == 'none' ? 'none' : '';

// Make table of sales tax, shipping, order total and buttons for screen output using template file
$tpl_so->set_file('summary_table_footer', 'table_footer.htm');
$tpl_so->set_var(array(
	'COLSPAN_L'					=>	$colspan_summary_l,
	'COLSPAN_M'					=>	$colspan_summary_m,
	'COLSPAN_S'					=>	$colspan_summary_s,
	'TXT_SUBTOTAL'				=>	$MOD_BAKERY['TXT_SUBTOTAL'],
	'SETTING_SHOP_CURRENCY'		=>	$setting_shop_currency,
	'ORDER_SUBTOTAL'			=>	$f_order_subtotal,
	'CSS_CLASS_CART_SHIPPING'	=>	$css_class_cart_shipping_f,
	'TXT_SHIPPING_COST'			=>	$txt_shipping_cost,
	'SHIPPING'					=>	$f_shipping,
	'DISPLAY_TAX'				=>	$display_tax,
	'TXT_INCL'					=>	$MOD_BAKERY['TXT_INCL'],
	'TAX_RATE'					=>	$f_tax_rate,
	'TXT_TAX'					=>	$MOD_BAKERY['TXT_TAX'],
	'SALES_TAX'					=>	$f_sales_tax,
	'TXT_TOTAL'					=>	$MOD_BAKERY['TXT_TOTAL'],
	'ORDER_TOTAL'				=>	$f_order_total,
	'SETTING_CONTINUE_URL'		=>	$setting_continue_url,
	'TXT_CONTINUE_SHOPPING'		=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING'],
	'TXT_CANCEL_ORDER'			=>	$MOD_BAKERY['TXT_CANCEL_ORDER'],
	'TXT_JS_CONFIRM'			=>	$MOD_BAKERY['TXT_JS_CONFIRM'],	
	'CHECKOUT_URL'				=>	$checkout_url,
	'PAY_GATEWAY_DATA'			=>	$pay_gateway_data,
	'TXT_SUBMIT_ORDER'			=>	$MOD_BAKERY['TXT_SUBMIT_ORDER'],
	'TXT_BUY'					=>	$MOD_BAKERY['TXT_BUY'].' '.$setting_shop_currency.' '.$f_order_total
));
$tpl_so->pparse('screen_output', 'summary_table_footer', true);



// Make table of sales tax, shipping and order total for invoice print using template file
$tpl_ip->set_file('invoice_table_footer', 'table_footer.htm');
$tpl_ip->set_var(array(
	'COLSPAN_L'					=>	$colspan_invoice_l,
	'COLSPAN_M'					=>	$colspan_invoice_m,
	'COLSPAN_S'					=>	$colspan_invoice_s,
	'TXT_SUBTOTAL'				=>	$MOD_BAKERY['TXT_SUBTOTAL'],
	'SETTING_SHOP_CURRENCY'		=>	$setting_shop_currency,
	'ORDER_SUBTOTAL'			=>	$f_order_subtotal,
	'CSS_CLASS_INVOICE_SHIPPING'=>	$css_class_invoice_shipping_b,
	'TXT_SHIPPING_COST'			=>	$txt_shipping_cost,
	'SHIPPING'					=>	$f_shipping,
	'DISPLAY_TAX'				=>	$display_tax,
	'TXT_INCL'					=>	$MOD_BAKERY['TXT_INCL'],
	'TAX_RATE'					=>	$f_tax_rate,
	'TXT_TAX'					=>	$MOD_BAKERY['TXT_TAX'],
	'SALES_TAX'					=>	$f_sales_tax,
	'TXT_TOTAL'					=>	$MOD_BAKERY['TXT_TOTAL'],
	'ORDER_TOTAL'				=>	$f_order_total
));
$tpl_ip->parse('invoice_print', 'invoice_table_footer', true);

// Save invoice print in a var to store it in the db later on
$invoice_item_list = $tpl_ip->get('invoice_print');


	// Make list of sales tax, shipping and order total for the email
	// Do not change text indent since it fits to the email
	$email_setting_tax_by = $setting_tax_by == "none" ? '' : "\n\t{$MOD_BAKERY['TXT_INCL']} $f_tax_rate {$MOD_BAKERY['TXT_TAX']}: $setting_shop_currency $f_sales_tax";
	$email_item_list .= "
	-------------------------------------
	{$MOD_BAKERY['TXT_SUBTOTAL']}: $setting_shop_currency $f_order_subtotal
	$txt_shipping_cost: $setting_shop_currency $f_shipping $email_setting_tax_by
	-------------------------------------
	-------------------------------------
	{$MOD_BAKERY['TXT_TOTAL']}: $setting_shop_currency $f_order_total
	-------------------------------------";



// PREPARE ORDER DATA FOR PAYMENT GATEWAYS, EMAIL AND INVOICE
// **********************************************************

// Get order date from db for invoice and make readable form
$query_customer = $database->query("SELECT order_date FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '{$_SESSION['bakery']['order_id']}'");
if ($query_customer->numRows() > 0) {
	$customer   = $query_customer->fetchRow();
	$order_date = gmdate(DEFAULT_DATE_FORMAT.', '.DEFAULT_TIME_FORMAT, $customer['order_date']+TIMEZONE);
}

// Get bank account from db for invoice
$query_invoice = $database->query("SELECT value_1 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'invoice'");
if ($query_invoice->numRows() > 0) {
	$invoice      = $query_invoice->fetchRow();
	$bank_account = stripslashes($invoice['value_1']);
}

// Make string of invoice data and email data to store in db
$invoice_array = array($order_id, $setting_shop_name, $bank_account, $cust_name, $address, $cust_address, $ship_address, $cust_email, $invoice_item_list, $order_date, $setting_shop_email, $email_address, $email_cust_address, $email_ship_address, $email_item_list, $cust_tax_no, $cust_msg);
$invoice_str   = addslashes(implode("&&&&&", $invoice_array));

// Write invoice data and email data string into db
$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET invoice = '$invoice_str' WHERE order_id = '{$_SESSION['bakery']['order_id']}'");




// Code below is deprecated and stoped droplets working (only used for WB < 2.8.1)
if (version_compare(WB_VERSION, '2.8.1') < 0) {
	
	// AVOID OUTPUT FILTER
	// Obtain the settings of the output filter module
	if (file_exists(WB_PATH.'/modules/output_filter/filter-routines.php')) {
		include_once(WB_PATH.'/modules/output_filter/filter-routines.php');
		if (function_exists('getOutputFilterSettings')) {
			$filter_settings = getOutputFilterSettings();
		} else {
			$filter_settings = get_output_filter_settings();
		}
	} else {
		// No output filter used, define default settings
		$filter_settings['email_filter'] = 0;
	}
	
	// NOTE:
	// With ob_end_flush() the output filter will be disabled for Bakery the summary page
	// If you are using e.g. ob_start in the index.php of your template it is possible that you will indicate problems
	if ($filter_settings['email_filter'] && !($filter_settings['at_replacement']=='@' && $filter_settings['dot_replacement']=='.')) { 
		ob_end_flush();
	}
}
