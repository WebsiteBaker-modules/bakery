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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');

// Look for language file
if (LANGUAGE_LOADED) {
    include(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        include(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Clean post array
$_POST = array_map('strip_tags', $_POST);

// Check for blank fields
foreach ($_POST as $field => $value) {
	// Except of these fields...
	if ($field != 'cust_tax_no' && (strpos($field, 'company') === false)) {
		if ($value == '') {
			$blanks[] = $field;
		}
	}
}


// If blank fields show error message
if (isset($blanks)) {
	$form_error = $MOD_BAKERY['ERR_FIELD_BLANK'];
	$error_bg   = $blanks;
	extract($_POST);
	include('view_form.php');
	return;
}

// If email fields do not match show error message
if ($_POST['cust_email'] !== $_POST['cust_confirm_email']) {
	$error_bg[] = 'cust_email';
	$error_bg[] = 'cust_confirm_email';
	$errors[]   = $MOD_BAKERY['ERR_EMAILS_NOT_MATCHED'];
}


// Add a charset besides of latin to the address form regexp
// Makes use of unicode scripts (see http://www.regular-expressions.info/unicode.html#script)
$us = '';
if (!empty($MOD_BAKERY['ADD_CHARSET'])) {
	switch (strtolower($MOD_BAKERY['ADD_CHARSET'])) {
		case 'cyrillic':
			$us = '\p{Cyrillic}';
			break;
		case 'greek':
			$us = '\p{Greek}';
			break;
		case 'hebrew':
			$us = '\p{Hebrew}';
			break;
		case 'arabic':
			$us = '\p{Arabic}';
			break;
	}
}

// Check the textfields
foreach ($_POST as $field => $value) {
	if ($field != 'pay_methods') {
		$field  = strip_tags($field);
		$value  = strip_tags($value);

		if (strpos($field, 'company') !== false) {
			if (!preg_match('#^[\p{Latin}'.$us.'0-9+&\s\-]{0,50}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_NAME'];
			}
		}

		if (strpos($field, 'first_name') !== false) {
			if (!preg_match('#^[\p{Latin}'.$us.'.\s\-]{1,50}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_NAME'];
			}
		}

		if (strpos($field, 'last_name') !== false) {
			if (!preg_match('#^[\p{Latin}'.$us.'\s\'\-]{1,50}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_NAME'];
			}
		}

		if (strpos($field, 'cust_tax_no') !== false &&
			strpos($setting_tax_group, $setting_shop_country) !== false) {
			include('check_vat.php');
			$value = trim($value);
			if (!check_vat($value, $setting_tax_group)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_CUST_TAX_NO'];
			}
		}

		if (strpos($field, 'street') !== false) {
			if (!preg_match('#^[\p{Latin}'.$us.'0-9.,\s\-]{1,50}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_STREET'];
			}
		}

		if (strpos($field, 'city') !== false) {
			if (!preg_match('#[\p{Latin}'.$us.'.\s\-]{1,50}#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_CITY'];
			}
		}

		if (strpos($field, 'state') !== false) {
			if (!preg_match('#^[\p{Latin}'.$us.'0-9.\s\-]{1,50}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_STATE'];
			}
		}

		if (strpos($field, 'country') !== false) {
			if (!preg_match('#^[A-Z]{2}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_COUNTRY'];
			}
		}

		if (strpos($field, 'email') !== false) {
			if (!preg_match('#^.+@.+\..+$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_EMAIL'];
			}
		}

		if (strpos($field, 'zip') !== false) {
			if (!preg_match('#^[A-Za-z0-9\s\-]{4,10}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_ZIP'];
			}
		}

		if (strpos($field, 'phone') !== false) {
			if (!preg_match('#^[0-9)(xX+./\s\-]{7,20}$#u', $value)) {
				$error_bg[] = $field;
				$errors[]   = htmlspecialchars($value, ENT_QUOTES).' '.$MOD_BAKERY['ERR_INVAL_PHONE'];
			}
		}

		$$field = strip_tags(trim($value));
	}
}

// If any errors occured show address form again
if (@is_array($errors)) {  
	$form_error = '';
	foreach ($errors as $value) {
		$form_error .= $value.'<br />';
	}
	$form_error .= '<br />'.$MOD_BAKERY['ERR_INVAL_TRY_AGAIN'];
	include('view_form.php');
	return;
}


// Make arrays for all forms and fields
$forms  = array('cust', 'ship');
$fields = array('company', 'first_name', 'last_name', 'tax_no', 'street', 'city', 'state', 'country', 'zip', 'email', 'confirm_email', 'phone');

// Loop through post vars and import them into session var and the current symbol table
foreach ($forms as $form) {
	foreach ($fields as $field) {
		$field_var = $form.'_'.$field;
		if (!isset($_SESSION['bakery'][$form][$field])) $_SESSION['bakery'][$form][$field] = '';
		if (isset($_POST[$field_var])) $_SESSION['bakery'][$form][$field] = strip_tags($_POST[$field_var]);
	}
}

// If all fields correct, write them into db
foreach ($_POST as $field => $value) {
	if ($field != 'save_form' && $field != 'cust_confirm_email') {
		$field = $admin->add_slashes(strip_tags($field));
		$value = $admin->add_slashes(strip_tags($value));
		$updates[] = "$field = '$value'";
	}
}

// Make update string
if (isset($_SESSION['USER_ID'])) {
	$update_string = "user_id = '{$_SESSION['USER_ID']}',".implode($updates,",");
} else {
	$update_string = implode($updates,",");
}

// Update db
$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET $update_string WHERE order_id = '{$_SESSION['bakery']['order_id']}'");

// If form data is ok, show payment methods
include('view_pay_methods.php');
return;
