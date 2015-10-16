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

$mollie_debug = false;  // true dumps debug data in subdir /orders/

$field_1 = 'partner_id';
$field_2 = '';
$field_3 = '';
$field_4 = '';
$field_5 = '';
$field_6 = '';

// Payment method info
$payment_gateway_url = WB_URL."/modules/bakery/payment_methods/molliev2/redirect.php";
$payment_method_name = 'Mollie (API version)';
$payment_method_version = '1.0';
$payment_method_author = 'Dev4me - Ruud';
$requires_bakery_module = '1.7';

 
?>