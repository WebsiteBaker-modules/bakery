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

// Include WB config.php file, WB admin class and mollie class
require('../../../../config.php');
require_once WB_PATH.'/framework/class.admin.php';
require_once dirname(__FILE__).'/info.php';
require_once dirname(__FILE__).'/Mollie/API/Autoloader.php';

// Get the payment method settings from db
$partner_id = $database->get_one("SELECT value_1 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'molliev2'");

// Check if payment is completed
if (isset($_POST['id'])) {
	try {
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($partner_id);

		$payment  = $mollie->payments->get($_REQUEST["id"]);
		$order_id = $payment->metadata->order_id;
		$method = $payment->method;
		$transaction_id = $payment->id;
		debug_write($order_id, $payment);
		
		if ($payment->isPaid() == true) {
			$new_invoice_id = $database->get_one("SELECT MAX(`invoice_id`) + 1 AS new_invoice_id FROM ".TABLE_PREFIX."mod_bakery_customer");
			$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'paid', status = 'ordered', submitted = 'molliev2', invoice_id = '$new_invoice_id' WHERE transaction_id = '$transaction_id'");
			sendMail($transaction_id);
		} elseif ($payment->isOpen() == true) {
			$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_status = 'pending', status = 'busy', submitted = 'molliev2' WHERE transaction_id = '$transaction_id'");
		}
	}
	catch (Mollie_API_Exception $e)
	{
		echo "API call failed: " . htmlspecialchars($e->getMessage());
	}
}


function sendMail($transaction_id) {
	global $database;
	
	$query_payment_methods = $database->query("SELECT cust_email_subject, cust_email_body, shop_email_subject, shop_email_body FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'molliev2'");
	if ($query_payment_methods->numRows() > 0) {
		$payment_methods    = $query_payment_methods->fetchRow();
		$cust_email_subject = stripslashes($payment_methods['cust_email_subject']);
		$cust_email_body    = stripslashes($payment_methods['cust_email_body']);
		$shop_email_subject = stripslashes($payment_methods['shop_email_subject']);
		$shop_email_body    = stripslashes($payment_methods['shop_email_body']);
	}
		
	// Get email data string from db customer table
	$query_customer = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_customer WHERE transaction_id = '$transaction_id'");
	if ($query_customer->numRows() > 0) {
		$customer = $query_customer->fetchRow();
		if (!empty($customer['invoice'])) {
			// Convert string to array
			$invoice = stripslashes($customer['invoice']);
			$invoice_array = explode('&&&&&', $invoice);
			
			// Email vars to replace placeholders in the email body
			$order_id 		   = $customer['order_id'];
			$order_date 	   = $customer['order_date'];
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
			$cust_msg 	       = $invoice_array[16];
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
	$charset = 'utf-8';
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
	
	$n_order_id = substr("000000".$order_id,-6);
	$cust_msg = empty($cust_msg) ? "\t".$TEXT['NONE'] : $cust_msg;

	// Replace placeholders by values in the email body 
	$vars = array('[ORDER_ID]', '[SHOP_NAME]', '[BANK_ACCOUNT]', '[TRANSACTION_STATUS]', '[CUSTOMER_NAME]', '[ADDRESS]', '[CUST_ADDRESS]', '[SHIPPING_ADDRESS]', '[CUST_EMAIL]', '[ITEM_LIST]', '[CUST_TAX_NO]', '[CUST_MSG]');
	$values = array($n_order_id, $setting_shop_name, $bank_account, $transaction_status_notice, $cust_name, $address, $cust_address, $ship_address, $cust_email, $item_list, $cust_tax_no, $cust_msg);
		
	$cust_email_subject = str_replace($vars, $values, $cust_email_subject);
	$shop_email_subject = str_replace($vars, $values, $shop_email_subject);
	$cust_email_body    = str_replace($vars, $values, $cust_email_body);
	$shop_email_body    = str_replace($vars, $values, $shop_email_body);
		
	// Send confirmation e-mail to customer and shop
	if (mail($cust_email, $cust_email_subject, $cust_email_body, $cust_email_headers)) {
		$email_sent = true;
	}
	if (mail($setting_shop_email, $shop_email_subject, $shop_email_body, $shop_email_headers)) {
		$email_sent = true;
	}
}

function debug_write ($order_id, $status) {
	global $mollie_debug;
	if(!$mollie_debug) return;
	$order_id = intval($order_id);
	$database = dirname(__FILE__) . "/orders/order-{$order_id}.txt";
	$status = print_r($status,true);
	file_put_contents($database, $status);
}

?>