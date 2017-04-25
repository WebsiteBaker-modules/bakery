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


// Include WB config.php file and WB admin class
require('../../../../config.php');
require_once(WB_PATH.'/framework/class.admin.php');

// Get the payment method settings from db
$query_payment_methods = $database->query("SELECT value_1, value_2, value_4 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'payment-network'");
if ($query_payment_methods->numRows() > 0) {
	$payment_methods = $query_payment_methods->fetchRow();
	$user_id         = stripslashes($payment_methods['value_1']);  // payment-network user id
	$project_id      = stripslashes($payment_methods['value_2']);  // payment-network project id
	$notification_pw = stripslashes($payment_methods['value_4']);  // payment-network notification password
}

// Check if payment is completed
if (isset($_POST)) {
	if ($_POST['user_id'] == $user_id && $_POST['project_id'] == $project_id) {

		// Make array with post data sent by payment gateway and the project password
		$post_data = array( 
			'transaction'               => $_POST['transaction'],
			'user_id'                   => $_POST['user_id'],
			'project_id'                => $_POST['project_id'],
			'sender_holder'             => $_POST['sender_holder'],
			'sender_account_number'     => $_POST['sender_account_number'],
			'sender_bank_code'          => $_POST['sender_bank_code'],
			'sender_bank_name'          => $_POST['sender_bank_name'],
			'sender_bank_bic'           => $_POST['sender_bank_bic'],
			'sender_iban'               => $_POST['sender_iban'],
			'sender_country_id'         => $_POST['sender_country_id'],
			'recipient_holder'          => $_POST['recipient_holder'],
			'recipient_account_number'  => $_POST['recipient_account_number'],
			'recipient_bank_code'       => $_POST['recipient_bank_code'],
			'recipient_bank_name'       => $_POST['recipient_bank_name'],
			'recipient_bank_bic'        => $_POST['recipient_bank_bic'],
			'recipient_iban'            => $_POST['recipient_iban'],
			'recipient_country_id'      => $_POST['recipient_country_id'],
			'international_transaction' => $_POST['international_transaction'],
			'amount'                    => $_POST['amount'],
			'currency_id'               => $_POST['currency_id'],
			'reason_1'                  => $_POST['reason_1'],
			'reason_2'                  => $_POST['reason_2'],
			'security_criteria'         => $_POST['security_criteria'],
			'user_variable_0'           => $_POST['user_variable_0'],
			'user_variable_1'           => $_POST['user_variable_1'],
			'user_variable_2'           => $_POST['user_variable_2'],
			'user_variable_3'           => $_POST['user_variable_3'],
			'user_variable_4'           => $_POST['user_variable_4'],
			'user_variable_5'           => $_POST['user_variable_5'],
			'created'                   => $_POST['created'],
			'notification_password'     => $notification_pw
		); 
	
		// Generate security hash 
		$hash_string = implode('|', $post_data); 
		$hash = sha1($hash_string); 
	
		// Compare hashes
		if ($_POST['hash'] == $hash) {
			// If hashes did match set status paid and write transaction into db
			$transaction_id = $_POST['transaction'];
			$order_id       = $_POST['user_variable_0'];
			if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'paid', transaction_id = '$transaction_id' WHERE order_id = '$order_id'")) {
				// Store this data in the payment-network account
				echo "Transaction proceeded successfully\n";
				echo "Transaction ID: ".$_POST['transaction']."\n";
				echo "Order ID: ".$order_id."\n";
				echo "Created: ".$_POST['created']."\n";
				echo "Amount: ".$_POST['currency_id'].' '.$_POST['amount']."\n";
				echo "Security Criteria: ".$_POST['security_criteria']."\n";
			} else {
				echo $database->get_error()."\n";
			}
		} else {
			echo "ERROR: Hash did not match \n";
		}
	} else {
		echo "ERROR: Wrong user id or project id \n";
	}
} else {
	echo "ERROR: No POST vars received \n";
}
