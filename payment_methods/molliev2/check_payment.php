<?php
/**
 *
 * @category        Bakery Payment_method
 * @package         Mollie (NL) API version
 * @author          Dev4me - Ruud Eisinga
 * @link			http://www.dev4me.nl/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.3 and higher
 * @version         1.0
 * @lastmodified    June 19, 2015
 *
 */


// Get transaction id and status from db
$query_customers = $database->query("SELECT transaction_id, transaction_status FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '{$_SESSION['bakery']['order_id']}'");
if ($query_customers->numRows() > 0) {
	$customer = $query_customers->fetchRow();
	$transaction_id = stripslashes($customer['transaction_id']);
	$transaction_status = stripslashes($customer['transaction_status']);
} else {
	return;
}

if ($transaction_id && $transaction_status != 'paid') {
	$partner_id = $_SESSION['bakery'][$payment_method]['partner_id'];
	require_once dirname(__FILE__).'/info.php';
	require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";
	$mollie = new Mollie_API_Client;
	$mollie->setApiKey($partner_id);
	$payment  = $mollie->payments->get($transaction_id);
	$order_id = $payment->metadata->order_id;
	debug_write($order_id, $payment);
	if ($payment->isPaid() == true) {
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'paid', status = 'ordered', submitted = 'molliev2' WHERE transaction_id = '$transaction_id'");
		$payment_status = "success";	
		return;
	} elseif ($payment->isOpen() == true) {
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'pending', status = 'busy', submitted = 'molliev2' WHERE transaction_id = '$transaction_id'");
		$payment_status = "pending";	
		return;
	}
}

debug_write($_SESSION['bakery']['order_id'], "Status: ".$transaction_status );

// Check if the payment has been canceled by user
if ($transaction_status != 'paid') {
	$payment_status = "canceled";	
	return;
}

// Check if the payment has been completed successfull
elseif ($transaction_status == 'paid') {
	$payment_status = "success";	
	$email_sent = true;
	return;
}

// Check if there has been an error during payment processing
else {
	$payment_status = "error";
	return;
}



function debug_write ($order_id, $status) {
	require dirname(__FILE__).'/info.php';
	if(!$mollie_debug) return;
	$order_id = intval($order_id);
	$database = dirname(__FILE__) . "/orders/return-{$order_id}.txt";
	$status = print_r($status,true);
	file_put_contents($database, $status);
}
?>