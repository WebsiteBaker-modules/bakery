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

// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}
include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/info.php');
// Look for payment method language file
include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
	include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
}
// Get the payment method settings from db
$partner_id 	= $database->get_one("SELECT value_1 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = '$payment_method'");
$amount 		= $_SESSION['bakery']['order_total'];  // Amount 
$description 	= $MOD_BAKERY[$payment_method]['TXT_AGGREGATED_ITEMS'];
$report_url 	= WB_URL.'/modules/bakery/payment_methods/'.$payment_method.'/report.php';
$return_url 	= $setting_continue_url.'?pm='.$payment_method;

// Put the payment data into the session var for later use with redirect.php
$_SESSION['bakery'][$payment_method]['partner_id'] 	= $partner_id;
$_SESSION['bakery'][$payment_method]['amount'] 		= $amount;
$_SESSION['bakery'][$payment_method]['description'] = $description;
$_SESSION['bakery'][$payment_method]['return_url'] 	= $return_url;
$_SESSION['bakery'][$payment_method]['report_url'] 	= $report_url;

