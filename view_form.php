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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Include WB template parser and create template object
require_once(WB_PATH.'/include/phplib/template.inc');
$tpl = new Template(WB_PATH.'/modules/bakery/templates/form');
// Define how to deal with unknown {PLACEHOLDERS} (remove:=default, keep, comment)
$tpl->set_unknowns('remove');
// Define debug mode (0:=disabled (default), 1:=variable assignments, 2:=calls to get variable, 4:=debug internals)
$tpl->debug = 0;

// Include country file depending on the language
if (LANGUAGE_LOADED) {
	if (file_exists(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php')) {
		require_once(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php');
	}
}
else {
	require_once(WB_PATH.'/modules/bakery/languages/countries/EN.php');
}

// Include state file depending on the shop country
$select_shop_country = '';
$use_states = false;
if (file_exists(WB_PATH.'/modules/bakery/languages/states/'.$setting_shop_country.'.php')) {
	require_once(WB_PATH.'/modules/bakery/languages/states/'.$setting_shop_country.'.php');
	$select_shop_country = $setting_shop_country;
	$use_states = true;
}



// GET CUSTOMER DATA TO PREPOPULATE THE TEXT FIELDS
// ************************************************

// Arrays for all forms and fields
$forms  = array('cust', 'ship');
$fields = array('company', 'first_name', 'last_name', 'tax_no', 'street', 'city', 'state', 'country', 'zip', 'email', 'confirm_email', 'phone');

// Get customer data and use session var to store it
foreach ($forms as $form) {
	foreach ($fields as $field) {
		$field_var = $form.'_'.$field;
		// Set session var with customer data if not set yet
		if (!isset($_SESSION['bakery'][$form][$field])) $_SESSION['bakery'][$form][$field] = '';
		if (!empty($_POST[$field_var])) $_SESSION['bakery'][$form][$field] = strip_tags($_POST[$field_var]);
		// Make vars like $cust_company, $cust_first_name,... and $ship_company, $ship_first_name,...
		$$field_var = $_SESSION['bakery'][$form][$field];
	}
}


// For logged in user try to get customer data of a previous order from the db...
if (isset($_SESSION['USER_ID']) && $cust_first_name == '' && $cust_last_name == '' && $cust_street == '' && $cust_city == '' && $cust_state == '' && $cust_zip == '' && $cust_email == '' && $cust_phone == '') {
	$sql_result = $database->query("SELECT cust_company, cust_first_name, cust_last_name, cust_tax_no, cust_street, cust_city, cust_state, cust_country, cust_zip, cust_email, cust_phone FROM ".TABLE_PREFIX."mod_bakery_customer WHERE user_id = '{$_SESSION['USER_ID']}' ORDER BY order_id DESC LIMIT 1");
	$n = $sql_result->numRows();
	if ($n > 0) {
		$row = $sql_result->fetchRow();
		extract($row);
		$cust_confirm_email = $cust_email;
	}
}

// ...and same for the shipping data
if (isset($_SESSION['USER_ID']) &&  $ship_first_name == '' && $ship_last_name == '' && $ship_street == '' && $ship_city == '' && $ship_state == '' && $ship_zip == '') {
	$sql_result = $database->query("SELECT ship_company, ship_first_name, ship_last_name, ship_street, ship_city, ship_state, ship_country, ship_zip FROM ".TABLE_PREFIX."mod_bakery_customer WHERE user_id = '{$_SESSION['USER_ID']}' ORDER BY order_id DESC LIMIT 1");
	$n = $sql_result->numRows();
	if ($n > 0) {
		$row = $sql_result->fetchRow();
		extract($row);
	}
}


// If no country has been selected, preselect the shop country
if (!isset($cust_country) || $cust_country == '') {
	$cust_country = $setting_shop_country;
}
if ((!isset($ship_country) || $ship_country == '') && $setting_shipping_form != 'none') {
	$ship_country = $setting_shop_country;
}
// If no state is selected, preselect the shop state
if (!isset($cust_state) || $cust_state == '') {
	$cust_state = $setting_shop_state;
}
if ((!isset($ship_state) || $ship_state == '') && $setting_shipping_form != 'none') {
	$ship_state = $setting_shop_state;
}



// SHOW TITLE AND MESSAGES IF ANY
// ******************************

// Assign page filename for tracking with Google Analytics _trackPageview() function
global $ga_page;
$ga_page = '/view_form.php';

// Show form title using template file
$tpl->set_file('form_title', 'title.htm');
$tpl->set_var(array(
	'WB_URL'					=>	WB_URL,
	'TXT_SUBMIT_ORDER'			=>	$MOD_BAKERY['TXT_SUBMIT_ORDER'],
	'TXT_ADDRESS'				=>	$MOD_BAKERY['TXT_ADDRESS'],
	'TXT_FILL_IN_ADDRESS'		=>	$MOD_BAKERY['TXT_FILL_IN_ADDRESS'],
	'SETTING_CONTINUE_URL'		=>	$setting_continue_url
));
$tpl->pparse('output', 'form_title');

// Show form error messages using template file
if (isset($form_error)) {
	$tpl->set_file('form_error', 'error.htm');
	$tpl->set_var(array(
		'FORM_ERROR'			=>	$form_error
	));
	$tpl->pparse('output', 'form_error');
}



// SET FILE AND BLOCKS FOR FORM TEMPLATE
// *************************************

$tpl->set_file('form', 'form.htm');

$tpl->set_block('form', 'main_block', 'main');

$tpl->set_block('main_block', 'cust_country_block', 'cust_country');
$tpl->set_block('main_block', 'cust_state_block', 'cust_state');
$tpl->set_block('main_block', 'cust_textfields_block', 'cust_textfields');
$tpl->set_block('main_block', 'cust_button_block', 'cust_button');
$tpl->set_block('main_block', 'cust_buttons_block', 'cust_buttons');

$tpl->set_block('main_block', 'ship_title_block', 'ship_title');
$tpl->set_block('main_block', 'ship_country_block', 'ship_country');
$tpl->set_block('main_block', 'ship_state_block', 'ship_state');
$tpl->set_block('main_block', 'ship_textfields_block', 'ship_textfields');
$tpl->set_block('main_block', 'ship_button_block', 'ship_button');
$tpl->set_block('main_block', 'ship_buttons_block', 'ship_buttons');



// CUSTOMER ADDRESS FORM ONLY
// **************************

// Concatenate tax no and optional
$MOD_BAKERY['TXT_CUST_TAX_NO'] = $MOD_BAKERY['TXT_CUST_TAX_NO'] . ' (' . $MOD_BAKERY['TXT_OPTIONAL'] . ')';

// Make array for the customer address form
if ($setting_zip_location == 'end') {
	// Show zip at the end of address
	$cust_info = array('cust_email' => $MOD_BAKERY['TXT_CUST_EMAIL'], 'cust_confirm_email' => $MOD_BAKERY['TXT_CUST_CONFIRM_EMAIL'], 'cust_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'cust_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'cust_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'cust_tax_no' => $MOD_BAKERY['TXT_CUST_TAX_NO'], 'cust_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'cust_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'cust_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'cust_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'cust_country' => $MOD_BAKERY['TXT_CUST_COUNTRY'], 'cust_phone' => $MOD_BAKERY['TXT_CUST_PHONE']);
	$length = array('cust_email' => '50', 'cust_confirm_email' => '50', 'cust_company' => '50', 'cust_first_name' => '50', 'cust_last_name' => '50', 'cust_tax_no' => '20', 'cust_street' => '50', 'cust_zip' => '10', 'cust_city' => '50', 'cust_state' => '50', 'cust_phone' => '20');
} else {
	// Show zip inside of address
	$cust_info = array('cust_email' => $MOD_BAKERY['TXT_CUST_EMAIL'], 'cust_confirm_email' => $MOD_BAKERY['TXT_CUST_CONFIRM_EMAIL'], 'cust_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'cust_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'cust_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'cust_tax_no' => $MOD_BAKERY['TXT_CUST_TAX_NO'], 'cust_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'cust_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'cust_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'cust_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'cust_country' => $MOD_BAKERY['TXT_CUST_COUNTRY'], 'cust_phone' => $MOD_BAKERY['TXT_CUST_PHONE']);
	$length = array('cust_email' => '50', 'cust_confirm_email' => '50', 'cust_company' => '50', 'cust_first_name' => '50', 'cust_last_name' => '50', 'cust_tax_no' => '20', 'cust_street' => '50', 'cust_zip' => '10', 'cust_city' => '50', 'cust_state' => '50', 'cust_phone' => '20');
}

// Delete field customer company if not needed
if ($setting_company_field != 'show') {
	unset($cust_info['cust_company']);
	unset($length['cust_company']);
}

// Delete field customer state if not needed
if ($setting_state_field != 'show') {
	unset($cust_info['cust_state']);
	unset($length['cust_state']);
}

// Delete field customer tax number if not needed
if ($setting_tax_no_field != 'show') {
	unset($cust_info['cust_tax_no']);
	unset($length['cust_tax_no']);
}

// Initialize vars
$country_options = '';
$state_options   = '';

// Loop through all fields and generate the form
foreach ($cust_info as $field => $value) {

	// Generate country dropdown menu...
	if ($field == "cust_country") {
		// Prepare cust country options
		for ($n = 1; $n <= count($MOD_BAKERY['TXT_COUNTRY_NAME']); $n++) {
			$country = $MOD_BAKERY['TXT_COUNTRY_NAME'][$n];
			$country_code = $MOD_BAKERY['TXT_COUNTRY_CODE'][$n];
			$selected_country = ($country_code == @$_POST['country'] || $country_code == @$cust_country) ? ' selected="selected"' : '';
			$country_options .= "\n\t\t\t<option value='$country_code'$selected_country>$country</option>";
		}
		// Show cust country block using template file
		$tpl->set_var(array(
			'TXT_CUST_COUNTRY'		=>	$MOD_BAKERY['TXT_CUST_COUNTRY'],
			'SELECT_SHOP_COUNTRY'	=>	$select_shop_country,
			'COUNTRY_OPTIONS'		=>	$country_options
		));
		$tpl->parse('form', 'cust_country_block', true);
	}

	else {
		// Generate state dropdown menu...
		if ($use_states && $field == 'cust_state') {
			// Prepare cust state options
			for ($n = 1; $n <= count($MOD_BAKERY['TXT_STATE_NAME']); $n++) {
				$state          = $MOD_BAKERY['TXT_STATE_NAME'][$n];
				$state_code     = $MOD_BAKERY['TXT_STATE_CODE'][$n];
				$selected_state = ($state_code == @$_POST['cust_state'] || $state_code == @$cust_state) ? ' selected="selected"' : '';
				$state_options .= "\n\t\t\t<option value='$state_code'$selected_state>$state</option>";
			}
			// Show cust state options block using template file
			$tpl->set_var(array(
				'TXT_CUST_STATE'	=>	$MOD_BAKERY['TXT_CUST_STATE'],
				'STATE_OPTIONS'		=>	$state_options
			));
			$tpl->parse('form', 'cust_state_block', true);
		}

		// Generate all other fields
		// Add css class (red background) if the textfield is blank or incorrect
		$css_error_class = isset($error_bg) && in_array($field, $error_bg) ? 'mod_bakery_errorbg_f ' : '';
		// Show cust textfields block using template file
		$tpl->set_var(array(
			'TR_ID'				=>	$field."_text",
			'LABEL'				=>	$value,
			'CSS_ERROR_CLASS'	=>	$css_error_class,
			'NAME'				=>	$field,
			'VALUE'				=>	htmlspecialchars(@$$field, ENT_QUOTES),
			'MAXLENGTH'			=>	$length[$field]
		));
		$tpl->parse('form', 'cust_textfields_block', true);
	}
}



// CHECK IF WE HAVE TO SHOW THE SHIP FORM
// **************************************

// Initialize session var ship_form depending on general settings
if (!isset($_SESSION['bakery']['ship_form'])) {
	$_SESSION['bakery']['ship_form']     = null;
	if ($setting_shipping_form == 'request') {
		$_SESSION['bakery']['ship_form'] = false;
	} elseif ($setting_shipping_form == 'hideable') {
		$_SESSION['bakery']['ship_form'] = true;
	}
}

// Toogle session var depending on address form buttons "add" or "hide ship form"
if (!empty($_POST['add_ship_form'])) {
	$_SESSION['bakery']['ship_form'] = true;
} elseif (!empty($_POST['hide_ship_form'])) {
	$_SESSION['bakery']['ship_form'] = false;
}

// Check if we have to show ship form
$show_ship_form = false;
if ($setting_shipping_form != 'none') {
	if ($setting_shipping_form == 'request' && $_SESSION['bakery']['ship_form']) {
		$show_ship_form = true;
	}
	if ($setting_shipping_form == 'hideable' && $_SESSION['bakery']['ship_form']) {
		$show_ship_form = true;
	}
	if ($setting_shipping_form == 'always') {
		$show_ship_form = true;
	}
}

// Show the submit button (without shipping address form)...
if ($setting_shipping_form == 'none') {
	$tpl->set_var(array(
		'TXT_SELECT_PAYMENT_METHOD'	=>	$MOD_BAKERY['TXT_SELECT_PAYMENT_METHOD']
	));
	$tpl->parse('form', 'cust_button_block', true);
}
// ...or show a button to add the shipping address form and the submit button (without shipping address form)
elseif (($setting_shipping_form == 'request' || $setting_shipping_form == 'hideable') && !$_SESSION['bakery']['ship_form']) {
	$tpl->set_var(array(
		'TXT_ADD_SHIP_FORM'         =>	$MOD_BAKERY['TXT_ADD_SHIP_FORM'],
		'TXT_SELECT_PAYMENT_METHOD'	=>	$MOD_BAKERY['TXT_SELECT_PAYMENT_METHOD']
	));
	$tpl->parse('form', 'cust_buttons_block', true);
}



// ADD SHIPPING ADDRESS FORM IF REQUIRED
// *************************************
	
if ($show_ship_form) {

	$_SESSION['bakery']['ship_data'] = true;

	// Make array for the shipping address form
	if ($setting_zip_location == 'end') {
		// Show zip at the end of address
		$ship_info = array('ship_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'ship_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'ship_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'ship_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'ship_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'ship_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'ship_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'ship_country' => $MOD_BAKERY['TXT_CUST_COUNTRY']);
		$length = array('ship_company' => '50', 'ship_first_name' => '50', 'ship_last_name' => '50', 'ship_street' => '50', 'ship_zip' => '10', 'ship_city' => '50', 'ship_state' => '50');
	} else {
		// Show zip inside of address
		$ship_info = array('ship_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'ship_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'ship_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'ship_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'ship_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'ship_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'ship_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'ship_country' => $MOD_BAKERY['TXT_CUST_COUNTRY']);
		$length = array('ship_company' => '50', 'ship_first_name' => '50', 'ship_last_name' => '50', 'ship_street' => '50', 'ship_zip' => '10', 'ship_city' => '50', 'ship_state' => '50');
	}

	// Delete field shipping company if not needed
	if ($setting_company_field != "show") {
		unset($ship_info['ship_company']);
		unset($length['ship_company']);
	}
	
	// Delete field shipping state if not needed
	if ($setting_state_field != "show") {
		unset($ship_info['ship_state']);
		unset($length['ship_state']);
	}

	// Show ship form title using template file
	$tpl->set_var(array(
		'TXT_FILL_IN_SHIP_ADDRESS'	=>	$MOD_BAKERY['TXT_FILL_IN_SHIP_ADDRESS']
	));
	$tpl->parse('form', 'ship_title_block', true);

	
	// Initialize vars
	$country_options = '';
	$state_options   = '';
	
	// Loop through all fields and generate the shipping address form
	foreach ($ship_info as $field => $value) {
	
		// Generate country dropdown menu...
		if ($field == 'ship_country') {
			// Prepare ship country options
			for ($n = 1; $n <= count($MOD_BAKERY['TXT_COUNTRY_NAME']); $n++) {
				$country = $MOD_BAKERY['TXT_COUNTRY_NAME'][$n];
				$country_code = $MOD_BAKERY['TXT_COUNTRY_CODE'][$n];
				$selected_country = ($country_code == @$_POST['country'] || $country_code == @$ship_country) ? ' selected="selected"' : '';
				$country_options .= "\n\t\t\t<option value='$country_code'$selected_country>$country</option>";
			}
			// Show ship country block using template file
			$tpl->set_var(array(
				'TXT_CUST_COUNTRY'		=>	$MOD_BAKERY['TXT_CUST_COUNTRY'],
				'SELECT_SHOP_COUNTRY'	=>	$select_shop_country,
				'COUNTRY_OPTIONS'		=>	$country_options
			));
			$tpl->parse('form', 'ship_country_block', true);
		}
		
		else {
			// Generate state dropdown menu...
			if ($use_states && $field == 'ship_state') {
				// Prepare cust state options
				for ($n = 1; $n <= count($MOD_BAKERY['TXT_STATE_NAME']); $n++) {
					$state = $MOD_BAKERY['TXT_STATE_NAME'][$n];
					$state_code = $MOD_BAKERY['TXT_STATE_CODE'][$n];
					$selected_state = ($state_code == @$_POST['cust_state'] || $state_code == @$ship_state) ? ' selected="selected"' : '';
					$state_options .= "\n\t\t\t<option value='$state_code'$selected_state>$state</option>";
				}
				// Show cust state options block using template file
				$tpl->set_var(array(
					'TXT_CUST_STATE'	=>	$MOD_BAKERY['TXT_CUST_STATE'],
					'STATE_OPTIONS'		=>	$state_options
				));
				$tpl->parse('form', 'ship_state_block', true);
			}
	
	
			// Generate all other fields
			// Add css class (red background) if the textfield is blank or incorrect
			$css_error_class = isset($error_bg) && in_array($field, $error_bg) ? 'mod_bakery_errorbg_f ' : '';
			// Show ship textfields block using template file
			$tpl->set_var(array(
				'TR_ID'				=>	$field."_text",
				'LABEL'				=>	$value,
				'CSS_ERROR_CLASS'	=>	$css_error_class,
				'NAME'				=>	$field,
				'VALUE'				=>	htmlspecialchars(@$$field, ENT_QUOTES),
				'MAXLENGTH'			=>	$length[$field]
			));
			$tpl->parse('form', 'ship_textfields_block', true);
		}
	}

	// Show the submit button and a button to hide the shipping address form at the bottom of the form...
	if ($setting_shipping_form == "request" || $setting_shipping_form == "hideable") {
		$tpl->set_var(array(
			'TXT_HIDE_SHIP_FORM'        =>	$MOD_BAKERY['TXT_HIDE_SHIP_FORM'],
			'TXT_SELECT_PAYMENT_METHOD' =>	$MOD_BAKERY['TXT_SELECT_PAYMENT_METHOD']
		));
		$tpl->parse('form', 'ship_buttons_block', true);
	}
	// ...or show the submit button
	elseif ($setting_shipping_form == 'always') {
		$tpl->set_var(array(
			'TXT_SELECT_PAYMENT_METHOD'	=>	$MOD_BAKERY['TXT_SELECT_PAYMENT_METHOD']
		));
		$tpl->parse('form', 'ship_button_block', true);
	}
}

// Delete ship data if ship form has not been completed
else {
	unset($_SESSION['bakery']['ship']);
	$_SESSION['bakery']['ship_data'] = false;
}


// PARSE FORM TEMPLATE
// *******************
	
$tpl->parse('form', 'main_block', true);
$tpl->pparse('output', 'form');

// Initialize js to toggle customer/shipping state text field/select list
if ($_SESSION['bakery']['ship_form']) {
	echo "<script type='text/javascript'>
		<!--
		mod_bakery_toggle_state_f('$select_shop_country', 'cust', 0);
		mod_bakery_toggle_state_f('$select_shop_country', 'ship', 0);
		-->
	</script>
	";
} else {
	echo "<script type='text/javascript'>
		<!--
		mod_bakery_toggle_state_f('$select_shop_country', 'cust', 0);
		-->
	</script>
	";
}

// Code below is deprecated and stoped droplets working (only used for WB < 2.8.1)
if (version_compare(WB_VERSION, '2.8.1') < 0) {
	
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
	// With ob_end_flush() the output filter will be disabled for Bakery address form page
	// If you are using e.g. ob_start in the index.php of your template it is possible that you will indicate problems
	if ($filter_settings['email_filter'] && !($filter_settings['at_replacement']=='@' && $filter_settings['dot_replacement']=='.')) { 
		ob_end_flush();
	}
}
