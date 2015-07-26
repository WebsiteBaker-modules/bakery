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


// Check vars
$get_transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : '';
$get_status = isset($_GET['status']) ? $_GET['status'] : '';

// Get transaction id and status from db
$query_customers = $database->query("SELECT transaction_id, transaction_status FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '{$_SESSION['bakery']['order_id']}'");
if ($query_customers->numRows() > 0) {
	$customer = $query_customers->fetchRow();
	$db_transaction_id = stripslashes($customer['transaction_id']);
	$transaction_status = stripslashes($customer['transaction_status']);
} else {
	return;
}

// Check if the payment has been canceled by user
if ($get_status == 'canceled') {
	$payment_status = "canceled";	
	return;
}

// Check if the payment has been completed successfull
elseif ($get_transaction_id == $db_transaction_id && $transaction_status == 'paid') {
	$payment_status = "success";	
	return;
}

// Check if there has been an error during payment processing
// If payment not canceled nor successfull there must be an error
else {
	$payment_status = "error";
	return;
}
