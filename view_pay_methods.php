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

// Include WB template parser and create template object
require_once(WB_PATH.'/include/phplib/template.inc');
$tpl = new Template(WB_PATH.'/modules/bakery/templates/pay_methods');
// Define how to deal with unknown {PLACEHOLDERS} (remove:=default, keep, comment)
$tpl->set_unknowns('remove');
// Define debug mode (0:=disabled (default), 1:=variable assignments, 2:=calls to get variable, 4:=debug internals)
$tpl->debug = 0;

// Include WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Assign page filename for tracking with Google Analytics _trackPageview() function
global $ga_page;
$ga_page = '/view_pay_method.php';



// TITLE, CUSTOMERS MESSAGE AND TERMS & CONDITIONS
// ***********************************************

// Customers message
$display_cust_msg = $setting_cust_msg == 'show' ? 'table-row' : 'none';
$cust_msg         = '';
if (!empty($_SESSION['bakery']['cust_msg'])) {
	$cust_msg = htmlspecialchars($_SESSION['bakery']['cust_msg'], ENT_QUOTES);
}



// If tac url is set customers have to accept the terms & conditions
if (!empty($setting_tac_url)) {
	$tac_input_type = 'checkbox';
	$tac_link       = "<a href='$setting_tac_url' target='_blank'>{$MOD_BAKERY['TXT_AGREE']} $setting_shop_name</a>";
	$js_check_tac   = "return checkTaC('{$MOD_BAKERY['TXT_JS_AGREE']}')";
} else {
	$tac_input_type = 'hidden';
	$tac_link       = '';
	$js_check_tac   = '';
}

// Show title, customers message and terms & conditions using template file
$tpl->set_file('pay_methods_title', 'title.htm');
$tpl->set_var(array(
	'WB_URL'					=>	WB_URL,
	'TXT_TAC_AND_PAY_METHOD'	=>	$MOD_BAKERY['TXT_TAC_AND_PAY_METHOD'],
	'TXT_JS_AGREE'				=>	$MOD_BAKERY['TXT_JS_AGREE'],
	'SETTING_CONTINUE_URL'		=>	$setting_continue_url,
	'TAC_INPUT_TYPE'			=>	$tac_input_type,
	'TAC_LINK'					=>	$tac_link,
	'TXT_PAY_METHOD'			=>	$MOD_BAKERY['TXT_SELECT_PAY_METHOD'],
	'DISPLAY_CUST_MSG'			=>	$display_cust_msg,
	'TXT_ENTER_CUST_MSG'		=>	$MOD_BAKERY['TXT_ENTER_CUST_MSG'],
	'CUST_MSG'					=>	$cust_msg
));
$tpl->pparse('output', 'pay_methods_title');




// DISPLAY LIST OF PAYMENT METHODS
// *******************************

// Only show payment method/payment gateway if we have to
if ($num_payment_methods > 0) {
	foreach ($setting_payment_methods as $payment_method) {
		if (is_file(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/gateway.php')) {
			include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/gateway.php');
		}
	}
} else {
	// Show payment methods error using template file
	$tpl->set_file('pay_methods_error', 'error.htm');
	$tpl->set_var(array(
		'ERR_NO_PAYMENT_METHOD'	=>	$MOD_BAKERY['ERR_NO_PAYMENT_METHOD']
	));
	$tpl->pparse('output', 'pay_methods_error');
}

// Show payment methods footer using template file
$tpl->set_file('pay_methods_footer', 'footer.htm');
$tpl->set_var(array(
	'ERR_NO_PAYMENT_METHOD'	=>	$MOD_BAKERY['ERR_NO_PAYMENT_METHOD']
));
$tpl->pparse('output', 'pay_methods_footer');
