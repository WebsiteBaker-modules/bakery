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


// Use PayPal PDT (Payment Data Transfer)

// Sample code provided by PayPal as a starting point
// https://github.com/paypal/pdt-code-samples


// DEBUG
$testing = false;  // Use testing mode for detailed success / error messages
$sandbox = false;  // Use paypal sandbox



// Initialize or set vars
$errors   = array();
$keyarray = array();

// Check GET vars
$tx_token   = isset($_GET['tx'])     ? strip_tags($_GET['tx'])     : '';
$get_status = isset($_GET['status']) ? strip_tags($_GET['status']) : '';

// Check if the payment has been canceled by user
if ($get_status == 'canceled') {
	$payment_status = 'canceled';	
	return;
}

// Get SESSION vars
$order_id    = $_SESSION['bakery']['order_id'];
$order_total = $_SESSION['bakery']['order_total'];

// Get PayPal email (business var) and authentication token from db
$query_payment_methods = $database->query("SELECT value_1, value_3 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'paypal'");
if ($query_payment_methods->numRows() > 0) {
	$payment_methods  = $query_payment_methods->fetchRow();
	$setting_business = stripslashes($payment_methods['value_1']);
	$auth_token       = stripslashes($payment_methods['value_3']);
}

// Get transaction id from db
$transaction_id = $database->get_one("SELECT transaction_id FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");


// Read the post from PayPal system and add 'cmd' var
$req  = 'cmd=_notify-synch';
$req .= "&tx=$tx_token&at=$auth_token";

// Set host
$pdt_url = $sandbox ? 'www.sandbox.paypal.com' : 'www.paypal.com';

// cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://$pdt_url/cgi-bin/webscr");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);

// Set cacert.pem verisign certificate path in curl using 'CURLOPT_CAINFO' field here,
// if your server does not bundled with default verisign certificates.
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: $pdt_url"));

$res = curl_exec($ch);


// Process validation from PayPal
if (!$res) {
	// HTTP error
	if ($testing) {
		echo '<h2>PayPal Payment Data Transfer (PDT)</h2>';
		echo '<p>The PayPal payment with order id <b>' . $order_id . '</b> and transaction id <b>' . $tx_token . '</b>';
		echo ' could not be verified by Bakery.</p>';
		echo '<p><b>cURL-ERROR: Unable to connect to PayPal PDT server</b> ('.curl_errno($ch).': '.curl_error($ch).').</p>';
		echo '<p>To see all the transaction details, please log in to your PayPal account.</p>';
	}

	$payment_status = "error";

	// Close cURL connection and return
	curl_close($ch);
	return;
}

else {
	// Close cURL connection
	curl_close($ch);

	// Prepare testing message
	$testing_msg      = '<h2>PayPal Payment Data Transfer (PDT)</h2>';
	// Payment already completed successfully by IPN
	if ($transaction_id != 'none') {
		$testing_msg .= 'The PayPal payment has already been completed successfully by IPN.<br />';
		$testing_msg .= 'Transaction has already been saved in data base.<br /><br />';
	}
	$testing_msg   .= 'The PayPal payment with order id <b>'.$order_id.'</b> ';
	// Testing message transaction details
	$testing_msg_2  = 'To see all the transaction details, please log in to your PayPal account.<br /><br />';
	$testing_msg_2 .= 'Find further information on this transaction below:<br />';
	$testing_msg_2 .= nl2br(urldecode($res)) . '<br />';

	// Parse the data
	$lines = explode("\n", $res);

	// SUCCESS
	if (strcmp($lines[0], 'SUCCESS') == 0) {
		for ($i = 1; $i < count($lines) - 1; $i++) {
			list($key, $val) = explode('=', $lines[$i]);
			$keyarray[urldecode($key)] = urldecode($val);
		}

		// Confirm that the payment status is Completed
		if ($keyarray['payment_status'] != 'Completed') {
			$errors[] = 'The payment status returned by PayPal is "' . $keyarray['payment_status'] . '".';
			$errors[] = 'The payment status should be "Completed".';
		}
		// Check if the transaction id is correct
		if ($transaction_id != $keyarray['txn_id'] && $transaction_id != 'none') {
			$errors[] = 'The transaction id did not match.';
		}
		// Validate if the receiver’s email address is registered to Bakery
		if ($keyarray['business'] != $setting_business) {
			$errors[] = 'The receiver’s PayPal email address (business var) is not registered to Bakery.';
		}
		// Check if the order id is correct
		if ($keyarray['invoice'] != $order_id) {
			$errors[] = 'The order id did not match.';
		}
		// Check if the payment amount is correct
		if ($keyarray['mc_gross'] != $order_total) {
			$errors[] = 'The payment amount did not match.';
		}

		// If no errors occured set payment status to successfull
		if (count($errors) == 0) {
			if ($testing) {
				$testing_msg .= 'has been completed successfully.<br />';
				$testing_msg .= $testing_msg_2;
				echo $testing_msg;
			}

			// Set payment status success and update db
			$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'paid', transaction_id = '$tx_token' WHERE order_id = '$order_id' AND transaction_id = 'none' AND transaction_status IN ('none','pending')");
			$payment_status = "success";
			return;
		}

		// ERROR
		else {
			if ($testing) {
				$testing_msg .= 'has not been completed.<br /><br />';
				$testing_msg .= '<b>Please see the list below for transaction-specific details:</b><br />';
				foreach ($errors as $value) {
					$testing_msg .= ' - ' . $value . '<br />';
				}
				$testing_msg .= '<br /><br />';
				$testing_msg .= $testing_msg_2;
				echo $testing_msg;
			}

			// Set payment status pending and update db
			$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'pending', transaction_id = '$tx_token' WHERE order_id = '$order_id' AND transaction_id = 'none' AND transaction_status = 'none'");
			$payment_status = "pending";
			return;
		}
	}

	// FAIL
	elseif (strcmp($lines[0], "FAIL") == 0) {
		if ($testing) {
			$testing_msg .= 'is invalid and has not been completed.<br />';
			$testing_msg .= $testing_msg_2;
			echo $testing_msg;
		}

		// Set payment status pending and update db
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'pending' WHERE order_id = '$order_id' AND transaction_id = 'none' AND transaction_status = 'none'");
		$payment_status = "pending";
		return;
	}
}
