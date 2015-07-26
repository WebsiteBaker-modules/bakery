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


// include WB config.php file and admin class
require('../../config.php');
require_once(WB_PATH.'/framework/class.admin.php');

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
	// User allowed to view this page
	die($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES']);
}



//Look for language File
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Header
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $MOD_BAKERY['TXT_ORDER']." ".$TEXT['VIEW_DETAILS']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php if (defined('DEFAULT_CHARSET')) { echo DEFAULT_CHARSET; } else { echo 'utf-8'; }?>" />
<link href="<?php echo WB_URL; ?>/modules/bakery/backend.css" rel="stylesheet" type="text/css" />
</head>

<?php
// Get invoice data string from db customer table
$query_customer = $database->query("SELECT invoice_id, invoice FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");
if ($query_customer->numRows() > 0) {
	$customer = $query_customer->fetchRow();
	if ($customer['invoice'] != '') {
		// Convert string to array
		$invoice = stripslashes($customer['invoice']);
		$invoice_array = explode("&&&&&", $invoice);
		// Vars
		$order_id         = $invoice_array[0];
		#$shop_name       = $invoice_array[1];
		#$bank_account    = $invoice_array[2];
		#$cust_name       = $invoice_array[3];
		#$address         = $invoice_array[4];
		$cust_address     = $invoice_array[5];
		$ship_address     = $invoice_array[6];
		#$cust_email      = $invoice_array[7];
		$html_item_list   = $invoice_array[8];
		$order_date       = $invoice_array[9];

		$invoice_id       = $customer['invoice_id'];

		// If given get customer tax no
		$cust_tax_no      = isset($invoice_array[15]) ? $invoice_array[15] : '';
		$display_tax_no   = $cust_tax_no == ''        ? 'none'             : 'table-row';

		// If given get customers message
		$cust_msg         = isset($invoice_array[16]) ? nl2br($invoice_array[16]) : '';
		$display_cust_msg = $cust_msg == ''           ? 'none'                    : 'table-row';

		// Change frontend classes (eg. mod_bakery_anything_f) to backend classes (eg. mod_bakery_anything_b)
		$html_item_list = str_replace("_f'", "_b'", $html_item_list);


// Show order
?>
<body>
<div id="order">
  <table width="540px" align="center" border="0" cellspacing="0" cellpadding="3">
	<tr>
	  <td colspan="6">
	    <span class="mod_bakery_order_b"><?php echo $MOD_BAKERY['TXT_ORDER_ID']."</span>: ".$order_id; ?><br />
	    <span class="mod_bakery_order_b"><?php echo $MOD_BAKERY['TXT_INVOICE_ID']."</span>: ".$invoice_id; ?><br />
		<span class="mod_bakery_order_b"><?php echo $MOD_BAKERY['TXT_ORDER_DATE']."</span>: ".$order_date; ?></td>
	</tr>
	<tr>
	  <td colspan="6">
		<table width="98%" border="0" cellspacing="0" cellpadding="6">
		  <tr>
			<td valign="top" width="10%"><span class="mod_bakery_address_b"><?php echo $MOD_BAKERY['TXT_ADDRESS']; ?></span></td> 
			<td valign="top" width="30%"><?php echo $cust_address; ?></td>
			<td width="4%">&nbsp;</td>
			<td valign="top" width="10%"><span class="mod_bakery_address_b"><?php echo $MOD_BAKERY['TXT_SHIP_ADDRESS']; ?></span></td>
			<td valign="top"><?php echo $ship_address; ?></td>
		  </tr>
		  <tr style="display: <?php echo $display_tax_no; ?>;">
		  	<td><span class="mod_bakery_tax_no_b"><?php echo $MOD_BAKERY['TXT_CUST_TAX_NO']; ?></span></td>
		  	<td colspan="4"><?php echo $cust_tax_no; ?></td>
		  </tr>
		</table>
	  </td>
	</tr>
    <tr>
	  <td colspan="6"><?php echo $html_item_list; ?></td>
	</tr>
	<tr style="display: <?php echo $display_cust_msg; ?>;">
	  <td colspan="6">
		<table width="98%" border="0" cellspacing="0" cellpadding="6">
		  <tr>
			<td valign="top" width="25%"><span class="mod_bakery_address_b"><?php echo $MOD_BAKERY['TXT_CUST_MSG']; ?></span></td> 
			<td valign="top"><?php echo $cust_msg; ?></td>
		  </tr>
		</table>
	  </td>
	</tr>
	<tr id="button" valign="top">
	  <td colspan="3" height="30" align="left" style="padding-left: 12px;">&nbsp;</td>
	  <td colspan="3" height="30" align="right" style="padding-right: 12px;">
	    <input type="button" value="<?php echo $TEXT['CLOSE']; ?>" onclick="javascript: window.close();" style="width: 120px; margin-top: 5px;" />
	  </td>
	</tr>
  </table>
</div>

	<?php
	}
	else {
	echo "<p class='mod_bakery_error_b'>".$TEXT['NONE_FOUND']."!</p>";
	echo "<p style='text-align: right;'><input type='button' value='{$TEXT['CLOSE']}' onclick='javascript: window.close();' style='width: 120px; margin-top: 5px;' /></p>";
	}
}
?>

</body>
