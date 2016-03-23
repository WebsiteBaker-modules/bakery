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


// PayPal IPN (Instant Payment Notification)
// *****************************************

// Sample code provided by PayPal as a starting point
// https://github.com/paypal/ipn-code-samples



// DEBUG
// Log requests into 'ipn.log' in the same directory
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation)
// Set this to 'false' once you go live or do not require logging
$active   = true;        // IPN on = true, IPN off = false
$debug    = false;       // Enable debug mode
$sandbox  = false;       // Use paypal sandbox
$delay    = false;       // Delay IPN respond to push up PDT
$log_file = './ipn.log'; // Make sure the loge file is writable


// Deactivate IPN
if (!$active) {
	exit();
}
// Delay IPN
if ($delay) {
	sleep(15);
}


// Include WB config.php file and WB admin class
require('../../../../config.php');
require_once(WB_PATH.'/framework/class.admin.php');

// Get setting of magic quotes
$magic_quotes_on = false;
if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1) {
	$magic_quotes_on = true;
}



// Read POST data
// Reading posted data directly from $_POST causes serialization issues with array data in POST
// Reading raw POST data from input stream instead.
$raw_post_data  = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost         = array();
foreach ($raw_post_array as $keyval) {
	$keyval = explode ('=', $keyval);
	if (count($keyval) == 2)
		$myPost[$keyval[0]] = urldecode($keyval[1]);
}

// Read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($myPost as $key => $value) {
	if ($magic_quotes_on) {
		$value = urlencode(stripslashes($value));
	} else {
		$value = urlencode($value);
	}
	$req .= "&$key=$value";
}

// Post IPN data back to PayPal to validate the IPN data is genuine
// Without this step anyone can fake IPN data
if ($sandbox) {
	$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
	$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
}

// cURL
$ch = curl_init($paypal_url);
if ($ch == false) {
	if ($debug) {	
		error_log(date('[Y-m-d H:i e] ').'Can not initialize cURL session.'.PHP_EOL, 3, $log_file);
	}
}
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
if ($debug) {
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
}
// CONFIG: Optional proxy configuration
//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
// Set TCP timeout to 30 seconds
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// CONFIG: Please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below. Ensure the file is readable by the webserver.
// This is mandatory for some environments.
//$cert = __DIR__ . "./cacert.pem";
//curl_setopt($ch, CURLOPT_CAINFO, $cert);
$res = curl_exec($ch);

// cURL error
if (curl_errno($ch) != 0) {
	if ($debug) {	
		error_log(date('[Y-m-d H:i e] ').'Can not connect to PayPal to validate IPN message: '.curl_error($ch).PHP_EOL, 3, $log_file);
	}
	curl_close($ch);
	exit();
}
else {
	// Log the entire HTTP response if debug is switched on
	if ($debug) {
		error_log(date('[Y-m-d H:i e] ').'HTTP request of validation request: '.curl_getinfo($ch, CURLINFO_HEADER_OUT)." for IPN payload: $req".PHP_EOL, 3, $log_file);
		error_log(date('[Y-m-d H:i e] ').'HTTP response of validation request: '.$res.PHP_EOL, 3, $log_file);
	}
	curl_close($ch);
}

// Inspect IPN validation result and act accordingly
// Split response headers and payload, a better way for strcmp
$tokens = explode("\r\n\r\n", trim($res));
$res    = trim(end($tokens));



// VERIFIED
if (strcmp($res, 'VERIFIED') == 0) {

	// POST vars
	$payment_currency = $_POST['mc_currency'];
	$payer_email      = $_POST['payer_email'];
	$txn_id           = $_POST['txn_id'];
	$receiver_email   = $_POST['receiver_email'];
	$order_id         = $_POST['invoice'];
	$payment_status   = $_POST['payment_status'];
	$payment_amount   = $_POST['mc_gross'];

	// Get shop currency from db
	$shop_currency = $database->get_one("SELECT `shop_currency` FROM ".TABLE_PREFIX."mod_bakery_general_settings");
	// Get transaction id from db
	$transaction_id = $database->get_one("SELECT `transaction_id` FROM ".TABLE_PREFIX."mod_bakery_customer WHERE `order_id` = '$order_id'");
	// Get receiver’s email from db 
	$paypal_email = $database->get_one("SELECT `value_1` FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE `directory` = 'paypal'");

	// Check some transaction details to validate transaction 
	$error = false;

	// Confirm that the payment status is completed
	if ($payment_status != 'Completed') {
		$error = true;
		if ($debug) {	
			error_log(date('[Y-m-d H:i e] ').'ERROR: The payment status returned by PayPal is "'.$payment_status.'". The payment status should be "Completed"'.PHP_EOL, 3, $log_file);
		}
	}
	// Check if the payment_currency is correct
	if ($payment_currency != $shop_currency) {
		$error = true;
		if ($debug) {	
			error_log(date('[Y-m-d H:i e] ').'ERROR: The payment currency did not match.'.PHP_EOL, 3, $log_file);
		}
	}
	// Check if the transaction id is correct
	if ($transaction_id != $txn_id && $transaction_id != 'none') {
		$error = true;
		if ($debug) {	
			error_log(date('[Y-m-d H:i e] ').'ERROR: The transaction id did not match.'.PHP_EOL, 3, $log_file);
		}
	}
	// Validate if the receiver’s email address is registered to Bakery
	if (strtolower($receiver_email) != strtolower($paypal_email)) {
		$error = true;
		if ($debug) {	
			error_log(date('[Y-m-d H:i e] ').'ERROR: The receiver\'s PayPal email address is not registered to Bakery.'.PHP_EOL, 3, $log_file);
		}
	}


	// If no errors occured set payment status to successfull
	if ($error != true) {
		if ($debug) {	
			error_log(date('[Y-m-d H:i e] ').'VERIFIED IPN: The transaction has been completed successfully. '.$req.PHP_EOL, 3, $log_file);
		}
		// Set payment status success and update db
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_id = '$txn_id', transaction_status = 'paid' WHERE order_id = '$order_id' AND transaction_id = 'none' AND transaction_status IN ('none','pending')");
		$payment_method  = 'paypal';
		$payment_status  = 'success';
		$no_confirmation = true;
		include '../../view_confirmation.php';
	}

	// ERROR
	else {					
		if ($debug) {	
			error_log(date('[Y-m-d H:i e] ').'IPN ERROR: The transaction has not been completed yet. Please see the transaction-specific errors in the log file above.'.PHP_EOL, 3, $log_file);
		}

		// Set payment status pending and update db
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_id = '$txn_id', transaction_status = 'pending' WHERE order_id = '$order_id' AND transaction_id = 'none' AND transaction_status = 'none'");
		$payment_method  = 'paypal';
		$payment_status  = 'pending';
		$no_confirmation = true;
		include '../../view_confirmation.php';
	}
}

// INVALID
elseif (strcmp($res, 'INVALID') == 0) {
	if ($debug) {	
		error_log(date('[Y-m-d H:i e] ').'INVALID IPN: The transaction is invalid and has not been completed. '.$req.PHP_EOL, 3, $log_file);
	}
}
?>