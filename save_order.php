<?php

/*
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (C) 2007 - 2017, Christoph Marti

  LICENSE TERMS:
  Please read the software license agreement included in this package
  carefully before using the software. By installing and using the software,
  your are agreeing to be bound by the terms of the software license.
  If you do not agree to the terms of the license, do not use the software.
  Using any part of the software indicates that you accept these terms.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/


require('../../config.php');

// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// Include the WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Validate the order_id
$order_id = NULL;
if (is_numeric($_POST['order_id'])) {
	$order_id = $_POST['order_id'];
}




// INITIALIZE VARS
// ***************

$vars = array('cust_company', 'cust_first_name', 'cust_last_name', 'cust_tax_no', 'cust_street', 'cust_city', 'cust_state', 'cust_country', 'cust_zip', 'cust_email', 'cust_phone', 'ship_company', 'ship_first_name', 'ship_last_name', 'ship_street', 'ship_city', 'ship_state', 'ship_country', 'ship_zip', 'shop_name', 'bank_account', 'cust_name', 'address', 'cust_address', 'ship_address', 'cust_email', 'item_list', 'order_date', 'shop_email', 'email_address', 'email_cust_address', 'email_ship_address', 'email_item_list', 'cust_tax_no', 'cust_msg');
foreach ($vars as $var) {
	$$var = '';
}



// GET CURRENT INVOICE DATA FROM DATABASE
// **************************************

// Get invoice data string from db customer table
$invoice = $database->get_one("SELECT invoice FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");

// Convert invoice data string to an array
if (!empty($invoice)) {
	$invoice       = stripslashes($invoice);
	$invoice_array = explode('&&&&&', $invoice);

	// Vars
	$shop_name          = $invoice_array[1];
	$bank_account       = $invoice_array[2];
	$cust_name          = $invoice_array[3];
	$address            = $invoice_array[4];
	$cust_address       = $invoice_array[5];
	$ship_address       = $invoice_array[6];
	$cust_email         = $invoice_array[7];
	$item_list          = $invoice_array[8];
	$order_date         = $invoice_array[9];
	$shop_email         = $invoice_array[10];
	$email_address      = $invoice_array[11];
	$email_cust_address = $invoice_array[12];
	$email_ship_address = $invoice_array[13];
	$email_item_list    = $invoice_array[14];
	$cust_tax_no        = $invoice_array[15];
	$cust_msg           = $invoice_array[16];
}



// GET THE POST DATA
// *****************

$update_string = '';
$new_values    = array();
$updates       = array();
foreach ($_POST as $field => $value) {
	$skip_fields = array('page_id', 'section_id', 'order_id', 'save_and_return', 'save');
	if (!in_array($field, $skip_fields)) {
		$field = strip_tags($field);
		$value = strip_tags($value);
		// Convert applicable fields into vars
		$$field = $value;
		// Prepare update string
		$value     = $admin->add_slashes($value);
		$updates[] = "$field = '$value'";
	}
}
$update_string = implode($updates, ', ');



// CUSTOMER ADDRESS
// ****************

// Get general settings
$query_general_settings = $database->query("SELECT shop_country, state_field, zip_location, hide_country FROM ".TABLE_PREFIX."mod_bakery_general_settings");
if ($query_general_settings->numRows() > 0) {
	$general_settings = $query_general_settings->fetchRow();
	$setting_shop_country = stripslashes($general_settings['shop_country']);
	$setting_state_field  = stripslashes($general_settings['state_field']);
	$setting_zip_location = stripslashes($general_settings['zip_location']);
	$setting_hide_country = stripslashes($general_settings['hide_country']);
}

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

// Convert country code to country name
$country_key       = array_keys($MOD_BAKERY['TXT_COUNTRY_CODE'], $cust_country);
$cust_country_name = $MOD_BAKERY['TXT_COUNTRY_NAME'][$country_key[0]];
// Convert country to uppercase
if (function_exists('mb_strtoupper')) {
	$cust_country_name = mb_strtoupper(entities_to_umlauts($cust_country_name, $charset), $charset);
}

// Convert state code to state name
if ($state_key = array_keys($MOD_BAKERY['TXT_STATE_CODE'], $cust_state)) {
	$cust_state = $MOD_BAKERY['TXT_STATE_NAME'][$state_key[0]];
	$cust_state = entities_to_umlauts($cust_state, $charset);
}

// Join customer first and last name
$cust_name = $cust_first_name.' '.$cust_last_name;

// Prepare field customer company
if (empty($cust_company)) {
	$email_cust_company = '';
	$cust_company       = '';
} else {
	$email_cust_company = $cust_company."\n\t";
	$cust_company       = $cust_company.'<br />';
}

// Prepare field customer country
if ($setting_hide_country == 'hide' && $setting_shop_country == $cust_country) {
	$email_cust_country_name = '';
	$cust_country_name       = '';
}
else {
	$email_cust_country_name = "\n\t".$cust_country_name;
	$cust_country_name       = '<br />'.$cust_country_name;
}

// Show address with state field
if (!empty($cust_state)) {
	if ($setting_zip_location == 'end') {
		// Show zip at the end of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_city, $cust_state $cust_zip$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_city.", ".$cust_state.' '.$cust_zip.$cust_country_name."\n\n\t".$cust_phone."\n";	
	}
	else {
		// Show zip inside of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_zip $cust_city<br />$cust_state$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_zip.' '.$cust_city."\n\t".$cust_state.$cust_country_name."\n\n\t".$cust_phone."\n";
	}
}
// Show address w/o state field	
else {
	if ($setting_zip_location == 'end') {
		// Show zip at the end of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_city<br />$cust_country-$cust_zip$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_city."\n\t".$cust_country."-".$cust_zip.$cust_country_name."\n\n\t".$cust_phone."\n";
	}
	else {	
		// Show zip inside of address
		$cust_address = $cust_company."$cust_name<br />$cust_street<br />$cust_zip $cust_city$cust_country_name<br /><br />$cust_phone<br />$cust_email";
		$email_cust_address = "\t".$email_cust_company.$cust_name."\n\t".$cust_street."\n\t".$cust_zip.' '.$cust_city.$cust_country_name."\n\n\t".$cust_phone."\n";
	}
}

// Make var that contains either customer address or - if existing - invoice address
$address        = $cust_address;
$email_address = $email_cust_address;




// SHIPPING ADDRESS
// ****************

// Initialize var
$ship_address       = '';
$email_ship_address = '';

// Check if the shipping address has been provided
if (!empty($ship_first_name) && !empty($ship_last_name) && !empty($ship_street) && !empty($ship_zip) && !empty($ship_city) && !empty($ship_country)) {

	// Convert country code to country name
	$country_key       = array_keys($MOD_BAKERY['TXT_COUNTRY_CODE'], $ship_country);
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
	$ship_name = $ship_first_name.' '.$ship_last_name;

	// Prepare field shipping company
	if (empty($ship_company)) {
		$email_ship_company = '';
		$ship_company       = '';
	} else {
		$email_ship_company = $ship_company."\n\t";
		$ship_company       = $ship_company.'<br />';
	}

	// Prepare field shipping country
	if ($setting_hide_country == 'hide' && $setting_shop_country == $ship_country) {
		$email_ship_country_name = '';
		$ship_country_name       = '';
	}
	else {
		$email_ship_country_name = "\n\t".$ship_country_name;
		$ship_country_name       = '<br />'.$ship_country_name;
	}

	// Show address with state field
	if (!empty($ship_state)) {
		if ($setting_zip_location == 'end') {
			// Show zip at the end of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_city, $ship_state $ship_zip$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_city.", ".$ship_state.' '.$ship_zip."\n";
		}
		else {
			// Show zip inside of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_country-$ship_zip $ship_city<br />$ship_state$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_country."-".$ship_zip.' '.$ship_city."\n\t".$ship_state.$ship_country_name."\n";		
		}
	}
	// Show address w/o state field	
	else {
		if ($setting_zip_location == 'end') {
			// Show zip at the end of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_city<br />$ship_country-$ship_zip$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_city."\n\t".$ship_country."-".$ship_zip.$ship_country_name."\n";
		}
		else {	
			// Show zip inside of address
			$ship_address = $ship_company."$ship_name<br />$ship_street<br />$ship_country-$ship_zip $ship_city$ship_country_name";
			$email_ship_address = "\t".$email_ship_company.$ship_name."\n\t".$ship_street."\n\t".$ship_country."-".$ship_zip.' '.$ship_city.$ship_country_name."\n";		
		}
	}

	// Make var that contains either customer address or - if existing - the invoice address
	$address       = $ship_address;
	$email_address = $email_ship_address;
}



// UPDATE DATABASE
// ***************

// Make string of invoice data to store in db
$invoice_array = array($order_id, $shop_name, $bank_account, $cust_name, $address, $cust_address, $ship_address, $cust_email, $item_list, $order_date, $shop_email, $email_address, $email_cust_address, $email_ship_address, $email_item_list, $cust_tax_no, $cust_msg);
$invoice_str   = addslashes(implode('&&&&&', $invoice_array));

// Add invoice data string to the update string
$update_string .= ",invoice = '$invoice_str'";

// Update db
$database->query("UPDATE ".TABLE_PREFIX ."mod_bakery_customer SET $update_string WHERE order_id = '$order_id'");

// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/bakery/modify_orders.php?page_id='.$page_id);
}
else {
	// Different targets depending on the save action
	if (!empty($_POST['save_and_return'])) {
		$return_url = WB_URL.'/modules/bakery/modify_order.php?page_id='.$page_id.'&section_id='.$section_id.'&order_id='.$order_id;
	}
	else {
		$return_url = WB_URL.'/modules/bakery/modify_orders.php?page_id='.$page_id;
	}
	// Print success message and return
	$admin->print_success($TEXT['SUCCESS'], $return_url);
}

// Print admin footer
$admin->print_footer();

?>