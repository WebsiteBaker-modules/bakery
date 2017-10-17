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
$tpl = new Template(WB_PATH.'/modules/bakery/templates/confirmation');
// Define how to deal with unknown {PLACEHOLDERS} (remove:=default, keep, comment)
$tpl->set_unknowns('remove');
// Define debug mode (0:=disabled (default), 1:=variable assignments, 2:=calls to get variable, 4:=debug internals)
$tpl->debug = 0;


// Check if payment status and payment method is set
if (is_string($payment_status) && is_string($payment_method)) {

	// Look for payment method language file
	if (LANGUAGE_LOADED) {
		include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
		if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
			include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
		}
	}



	// ERROR
	// *****

	if ($payment_status == "error") {

		// Show error message using template file
		$tpl->set_file('error', 'error.htm');
		$tpl->set_var(array(
			'ERROR'						=>	$MOD_BAKERY[$payment_method]['ERROR'],
			'SETTING_CONTINUE_URL'		=>	$setting_continue_url,
			'TXT_CONTINUE_SHOPPING'		=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING'],
			'TXT_CANCEL_ORDER'			=>	$MOD_BAKERY['TXT_CANCEL_ORDER'],
			'TXT_JS_CONFIRM'			=>	$MOD_BAKERY['TXT_JS_CONFIRM']
		));
		$tpl->pparse('output', 'error');
		return;
	}



	// CANCELED
	// ********

	if ($payment_status == "canceled") {

		// Show message using template file
		$tpl->set_file('canceled', 'canceled.htm');
		$tpl->set_var(array(
			'CANCELED'					=>	$MOD_BAKERY[$payment_method]['CANCELED'],
			'SETTING_CONTINUE_URL'		=>	$setting_continue_url,
			'TXT_CONTINUE_SHOPPING'		=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING'],
			'TXT_CANCEL_ORDER'			=>	$MOD_BAKERY['TXT_CANCEL_ORDER'],
			'TXT_JS_CONFIRM'			=>	$MOD_BAKERY['TXT_JS_CONFIRM']
		));
		$tpl->pparse('output', 'canceled');
		return;
	}



	// SUCCESS OR PENDING
	// ******************

	if ($payment_status == "success" || $payment_status == "pending") {

		// Get the order id from the session var or,
		// in case this script has been called by a payment method directly (eg. paypal ipn),
		// use the one provided by the payment gateway
		$order_id = isset($order_id) && is_numeric($order_id) ? $order_id : $_SESSION['bakery']['order_id'];
		
		// Initialize var
		$email_sent = 2;

		// UPDATE DB

		// Check if we have to update db and send emails
		$query_customers = $database->query("SELECT submitted FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id' AND submitted = 'no' AND  status = 'none'");

		if ($query_customers->numRows() == 1) {

			// Reset email sent to 0
			$email_sent = 0;

			// Consecutive numbering of invoice numbers
			$new_invoice_id = $database->get_one("SELECT MAX(invoice_id) + 1 AS new_invoice_id FROM ".TABLE_PREFIX."mod_bakery_customer");
			// Update db
			$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET submitted = '$payment_method', status = 'ordered', invoice_id = '$new_invoice_id' WHERE order_id = '$order_id'");


			// SEND CONFIRMATION EMAILS

			// Get the email templates from the db
			$query_payment_methods = $database->query("SELECT cust_email_subject, cust_email_body, shop_email_subject, shop_email_body FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = '$payment_method'");
			if ($query_payment_methods->numRows() > 0) {
				$payment_methods    = $query_payment_methods->fetchRow();
				$cust_email_subject = stripslashes($payment_methods['cust_email_subject']);
				$cust_email_body    = stripslashes($payment_methods['cust_email_body']);
				$shop_email_subject = stripslashes($payment_methods['shop_email_subject']);
				$shop_email_body    = stripslashes($payment_methods['shop_email_body']);
			}
	
			// Get email data string from db customer table
			$query_customer = $database->query("SELECT invoice FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");
			if ($query_customer->numRows() > 0) {
				$customer = $query_customer->fetchRow();
				if (!empty($customer['invoice'])) {
					// Convert string to array
					$invoice = stripslashes($customer['invoice']);
					$invoice_array = explode('&&&&&', $invoice);
	
					// Email vars to replace placeholders in the email body
					$setting_shop_name = $invoice_array[1];
					$bank_account      = $invoice_array[2];
					$cust_name         = $invoice_array[3];
					$cust_email        = $invoice_array[7];
					$shop_email        = $invoice_array[10];
					$address           = $invoice_array[11];
					$cust_address      = $invoice_array[12];
					$ship_address      = $invoice_array[13];
					$item_list         = $invoice_array[14];
					$cust_tax_no       = $invoice_array[15];
					$cust_msg          = $invoice_array[16];
				}
			}
			
			// In case this script has been called by a payment method directly (eg. paypal ipn)
			// we have to add the shop email var
			$setting_shop_email = isset($setting_shop_email) ? $setting_shop_email : $shop_email;
	
			// Remove all "\r" in emails to avoid double line breaks
			$cust_email_subject = str_replace ("\r", '', $cust_email_subject);
			$cust_email_body    = str_replace ("\r", '', $cust_email_body);
			$shop_email_subject = str_replace ("\r", '', $shop_email_subject);
			$shop_email_body    = str_replace ("\r", '', $shop_email_body);
	
			// Make email headers
			if (defined('DEFAULT_CHARSET')) { $charset = DEFAULT_CHARSET; } else {  $charset = 'utf-8'; }
			$headers  = "MIME-Version: 1.0"."\n";
			$headers .= "Content-type: text/plain; charset=\"$charset\""."\n";
	
			$cust_email_headers  = $headers."Return-Path: $setting_shop_email"."\n";
			$cust_email_headers .= "Reply-To: $setting_shop_email"."\n";
			$cust_email_headers .= "From: $setting_shop_name <$setting_shop_email>";
	
			$shop_email_headers  = $headers."Return-Path: $setting_shop_email"."\n";
			$shop_email_headers .= "Reply-To: $cust_email"."\n";
			$shop_email_headers .= "From: $setting_shop_name <$setting_shop_email>";
	
			// Make transaction status notice
			$transaction_status_notice = '';
			if ($payment_status == 'pending' && isset($MOD_BAKERY[$payment_method]['TXT_TRANSACTION_STATUS'])) {
				$transaction_status_notice  = $MOD_BAKERY[$payment_method]['TXT_TRANSACTION_STATUS'];
			}

			// If customer has not sent a message then display 'none'
			$cust_msg = empty($cust_msg) ? "\t".$TEXT['NONE'] : $cust_msg;

			// Replace placeholders by values in the email body
			$vars = array('[ORDER_ID]', '[SHOP_NAME]', '[BANK_ACCOUNT]', '[TRANSACTION_STATUS]', '[CUSTOMER_NAME]', '[ADDRESS]', '[CUST_ADDRESS]', '[SHIPPING_ADDRESS]', '[CUST_EMAIL]', '[ITEM_LIST]', '[CUST_TAX_NO]', '[CUST_MSG]');
			$values = array($order_id, $setting_shop_name, $bank_account, $transaction_status_notice, $cust_name, $address, $cust_address, $ship_address, $cust_email, $item_list, $cust_tax_no, $cust_msg);
		
			$cust_email_subject = str_replace($vars, $values, $cust_email_subject);
			$shop_email_subject = str_replace($vars, $values, $shop_email_subject);
			$cust_email_body    = str_replace($vars, $values, $cust_email_body);
			$shop_email_body    = str_replace($vars, $values, $shop_email_body);
	
			// Send confirmation e-mail to customer and shop
			// Increment email counter
			if (mail($cust_email, $cust_email_subject, $cust_email_body, $cust_email_headers)) {
				$email_sent++;
			}
			if (mail($setting_shop_email, $shop_email_subject, $shop_email_body, $shop_email_headers)) {
				$email_sent++; 
			}
		}


		// WEBSITE CONFIRMATION

		// In case payment data has been transfered in the background (eg. paypal ipn)
		// there is no way to show a confirmation page to the customer
		if (!isset($no_confirmation)) {

			// Show confirmation using template file
			if ($payment_status == 'success') {
				$tpl->set_file('success', 'success.htm');
				$tpl->set_var(array(
					'TXT_SUCCESS'			=>	$MOD_BAKERY[$payment_method]['TXT_SUCCESS'],
					'TXT_SHIPMENT'			=>	$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'],
					'TXT_THANK_U_ORDER'		=>	$MOD_BAKERY['TXT_THANK_U_ORDER']
				));
				$tpl->pparse('output', 'success');
			}
			elseif ($payment_status == "pending") {
				$tpl->set_file('pending', 'pending.htm');
				$tpl->set_var(array(
					'TXT_PENDING'			=>	$MOD_BAKERY[$payment_method]['TXT_PENDING'],
					'TXT_SHIPMENT'			=>	$MOD_BAKERY[$payment_method]['TXT_SHIPMENT'],
					'TXT_THANK_U_ORDER'		=>	$MOD_BAKERY['TXT_THANK_U_ORDER']
				));
				$tpl->pparse('output', 'pending');
			}
	
			// If emails have not been sent show additional email error using template file	
			if ($email_sent < 2) {
				$shop_email_link = '<a href="mailto:' . $setting_shop_email . '">' . $setting_shop_email . '</a>';
				$tpl->set_file('email_error', 'email_error.htm');
				$tpl->set_var(array(
					'ERR_EMAIL_NOT_SENT'	=>	$MOD_BAKERY['ERR_EMAIL_NOT_SENT'] . ':<br />' . $shop_email_link
				));
				$tpl->pparse('output', 'email_error');
			}
		}

		// Clean up the session array
		if (isset($_SESSION['bakery'])) {
			unset($_SESSION['bakery']);
		}
		return;
	}
} else {
	echo '<b>ERROR: Payment status or payment method is not defined.</b>';
	return;
}
