<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2016, Christoph Marti

  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/



// CONFIG: HTML email max width
$max_width = 600;



// Include WB admin wrapper script
require('../../config.php');
require(WB_PATH.'/modules/admin.php');

// Check if GET and SESSION vars are set
if (!isset($_GET['page_id']) OR !isset($_GET['section_id']) OR !isset($_GET['order_id']) OR !is_numeric($_GET['page_id']) OR !is_numeric($_GET['section_id']) OR !is_numeric($_GET['order_id']) OR !isset($_SESSION['USER_ID']) OR !isset($_SESSION['GROUP_ID'])) {
	die($MESSAGE['FRONTEND_SORRY_NO_VIEWING_PERMISSIONS']);
} else {
	$page_id    = $_GET['page_id'];
	$section_id = $_GET['section_id'];
	$order_id   = $_GET['order_id'];
}

// Check if user is authenticated to view this page
$admin = new admin('', '', false, false);
if ($admin->get_page_permission($page_id, $action='admin') === false) {
	// User not allowed to view this page
	die($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES']);
}

// Look for language file
if (LANGUAGE_LOADED) {
	require_once(WB_PATH.'/modules/bakery/languages/EN.php');
	if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
		require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
	}
}

// Set invoice
$display_reminder      = 'none';
$display_delivery_note = 'none';
$display_invoice       = '';
$title                 = $MOD_BAKERY['TXT_INVOICE'];
$charset               = defined('DEFAULT_CHARSET') ? DEFAULT_CHARSET : 'utf-8';


// Get invoice template from db payment methods table
$query_payment_methods = $database->query("SELECT value_4 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'invoice'");
if ($query_payment_methods->numRows() > 0) {
	$payment_methods = $query_payment_methods->fetchRow();
}
$invoice_template = stripslashes($payment_methods['value_4']);

// Get invoice data string from db customer table
$query_customer = $database->query("SELECT `invoice_id`, `sent_invoices`, `invoice`, `submitted`, `transaction_status` FROM `".TABLE_PREFIX."mod_bakery_customer` WHERE `order_id` = '$order_id'");
if ($query_customer->numRows() > 0) {
	$customer = $query_customer->fetchRow();

	if ($customer['invoice'] != '') {
		// Convert string to array
		$invoice       = stripslashes($customer['invoice']);
		$sent_invoices = stripslashes($customer['sent_invoices']);		
		$invoice_array = explode('&&&&&', $invoice);

		// The invoice data
		$order_id             = $invoice_array[0];
		$shop_name            = $invoice_array[1];
		$bank_account         = nl2br($invoice_array[2]);
		$cust_name            = $invoice_array[3];

		// Chop off phone number and email from address
		$invoice_address      = explode('<br /><br />', $invoice_array[4]);
		$invoice_address      = $invoice_address[0];
		$invoice_cust_address = explode('<br /><br />', $invoice_array[5]);
		$invoice_cust_address = $invoice_cust_address[0];

		$invoice_ship_address = $invoice_array[6];
		$cust_email           = $invoice_array[7];

		// Change frontend classes (eg. mod_bakery_anything_f) to backend classes (eg. mod_bakery_anything_b)
		$html_item_list       = str_replace("_f'", "_b'", $invoice_array[8]);

		$order_date           = $invoice_array[9];
		$shop_email           = $invoice_array[10];
		$address              = $invoice_array[11];
		$cust_address         = $invoice_array[12];
		$ship_address         = $invoice_array[13];
		$item_list            = $invoice_array[14];

		// If given get customer tax no
		$cust_tax_no = !empty($invoice_array[15]) ? $invoice_array[15] : ' &#8212; ';

		// Current date
		$current_date         = @date(DEFAULT_DATE_FORMAT);

		// Invoice id
		$invoice_id           = $customer['invoice_id'];
		$submitted_method     = $customer['submitted'];
		$transaction_status   = $customer['transaction_status'];

		// Get payment method name other than the internal identifier
		$payment_method = $submitted_method;
		// Look for payment method language file
		if (LANGUAGE_LOADED) {
		    include_once(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
		    if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
		        include_once(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
		    }
		}

		// Email subject
		$email_subject = $shop_name.' '.$MOD_BAKERY['TXT_INVOICE'];



		// Replace invoice placeholders by values
		$vars = array('[WB_URL]', '[ORDER_ID]', '[INVOICE_ID]', '[SHOP_NAME]', '[BANK_ACCOUNT]', '[CUSTOMER_NAME]', '[ADDRESS]', '[CUST_ADDRESS]', '[SHIPPING_ADDRESS]', '[CUST_EMAIL]', '[ITEM_LIST]', '[ORDER_DATE]', '[CURRENT_DATE]', '[TITLE]', '[DISPLAY_INVOICE]', '[DISPLAY_DELIVERY_NOTE]', '[DISPLAY_REMINDER]', '[CUST_TAX_NO]', '[PAYMENT_METHOD]');
		$values = array(WB_URL, $order_id, $invoice_id, $shop_name, $bank_account, $cust_name, $invoice_address, $invoice_cust_address, $invoice_ship_address, $cust_email, $html_item_list, $order_date, $current_date, $title, $display_invoice, $display_delivery_note, $display_reminder, $cust_tax_no, $MOD_BAKERY[$payment_method]['TXT_NAME']);
		$invoice = str_replace($vars, $values, $invoice_template);

		// Reset header image to a max width
		$pattern = '#<img src="(.*)" #Uis';
		if (preg_match($pattern, $invoice, $matches)) {
			$img_path = str_replace(WB_URL, WB_PATH, $matches[1]);
			list($width, $height, $type, $attr) = getimagesize($img_path);
			if (is_numeric($width) && $width > $max_width) {
				$height  = round($max_width * $height / $width);
				$width   = $max_width;
				$invoice = preg_replace('#width="(.*)"#Uis', 'width="'.$width.'"', $invoice, 1);
				$invoice = preg_replace('#height="(.*)"#Uis', 'height="'.$height.'"', $invoice, 1);
			}
		}

		// Generate the invoice html
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>'.$email_subject.'</title>
<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />
<link href="'.WB_URL.'/modules/bakery/backend.css" rel="stylesheet" type="text/css" />
</head>
<body>
';
		$html .= '<div id="invoice" style="width: '.$max_width.'px; margin: 0 auto;">'."\n".$invoice."\n</div>\n";
		$html .= '</body>'."\n";

		// Remove all "\r" in emails to avoid double line breaks
		$html = str_replace ("\r", '', $html);

		// Make email headers
		$headers  = 'MIME-Version: 1.0'."\n";
		$headers .= 'Content-type: text/html; charset="'.$charset.'"'."\n";
		$headers .= "Return-Path: $shop_email"."\n";
		$headers .= "Reply-To: $shop_email"."\n";
		$headers .= "From: $shop_name <$shop_email>";


		// Premailer => bring css inline
		// @link http://premailer.dialect.ca/api
		// @link https://gist.github.com/barock19/1591053
		require_once('library/premailer.php');
		$pre = Premailer::html($html);
		// $html  = $pre['html'];
		// $plain = $pre['plain'];


		// Send invoice email to customer
		if (mail($cust_email, $email_subject, $pre['html'], $headers)) {
			// On success increment email counter
			$database->query("UPDATE `".TABLE_PREFIX."mod_bakery_customer` SET `sent_invoices` = `sent_invoices` + '1' WHERE order_id = '$order_id'");
			// On success view confirmation
			$admin->print_success($MOD_BAKERY['TXT_INVOICE_HAS_BEEN_SENT_SUCCESSFULLY'], WB_URL.'/modules/bakery/modify_orders.php?page_id='.$page_id);
		} else {
			$admin->print_error($database->get_error(), WB_URL.'/modules/bakery/modify_orders.php?page_id='.$page_id);
		}
	}
}

// Print admin footer
$admin->print_footer();