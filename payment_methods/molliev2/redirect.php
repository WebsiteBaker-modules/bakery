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
require_once(WB_PATH.'/framework/class.admin.php');
require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";
$payment_method = "molliev2";

if (isset($_SESSION['bakery'][$payment_method]['amount'])) {
	$partner_id = $_SESSION['bakery'][$payment_method]['partner_id'];
	$amount = $_SESSION['bakery'][$payment_method]['amount'];
	$description = $_SESSION['bakery'][$payment_method]['description'];
	$return_url = $_SESSION['bakery'][$payment_method]['return_url'];
	$report_url = $_SESSION['bakery'][$payment_method]['report_url'];
	$order_id = $_SESSION['bakery']['order_id'];
	
	try {
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($partner_id);
	
		$payment = $mollie->payments->create(array(
			"amount"       => $amount,
			"description"  => $description,
			"webhookUrl"   => $report_url,
			"redirectUrl"  => $return_url,
			"metadata"     => array(
				"order_id" => $order_id,
			),
		));
		// Update transaction_id in customer table 
		$transaction_id = $payment->id;
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET transaction_id = '$transaction_id' WHERE order_id = '$order_id'");
		header("Location: " . $payment->getPaymentUrl());
		exit();
	}
	catch (Mollie_API_Exception $e) 	{
		header('location: '.$_POST['setting_continue_url'].'?pay_error=1');
		exit();
	}
} 