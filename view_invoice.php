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


// Include WB config.php file and admin class
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



// Look for language file
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Show invoice, delivery note or reminder
if (isset($_POST['view'])) {
	$view = $_POST['view'];
}
else {
	$view = 'invoice';
}

// Make difference between invoice, delivery note and reminder
$display_reminder      = "none";
$display_delivery_note = "none";
$display_invoice       = "none";
if ($view == 'reminder') {
	$title            = $MOD_BAKERY['TXT_REMINDER'];
	$display_reminder = '';
}
elseif ($view == 'delivery_note') {
	$title                 = $MOD_BAKERY['TXT_DELIVERY_NOTE'];
	$display_delivery_note = '';
}
else {
	$title = $MOD_BAKERY['TXT_INVOICE'];
	$display_invoice = '';
}

// Header
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $MOD_BAKERY['TXT_PRINT'].' '.$title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php if (defined('DEFAULT_CHARSET')) { echo DEFAULT_CHARSET; } else { echo 'utf-8'; }?>" />
<link href="<?php echo WB_URL; ?>/modules/bakery/backend.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

// Get invoice template from db payment methods table
$query_payment_methods = $database->query("SELECT value_4 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = 'invoice'");
if ($query_payment_methods->numRows() > 0) {
	$payment_methods = $query_payment_methods->fetchRow();
}
$invoice_template = stripslashes($payment_methods['value_4']);

// Get invoice data string from db customer table
$query_customer = $database->query("SELECT invoice_id, invoice FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");
if ($query_customer->numRows() > 0) {
	$customer = $query_customer->fetchRow();

	if ($customer['invoice'] != '') {
		// Convert string to array
		$invoice = stripslashes($customer['invoice']);
		$invoice_array = explode("&&&&&", $invoice);
		// Chop off phone number and email from customer address
		$invoice_address = explode("<br /><br />", $invoice_array[4]);
		$invoice_cust_address = explode("<br /><br />", $invoice_array[5]);
		// If given get customer tax no
		$cust_tax_no = !empty($invoice_array[15]) ? $invoice_array[15] : ' &#8212; ';
		// Change frontend classes (eg. mod_bakery_anything_f) to backend classes (eg. mod_bakery_anything_b)
		$invoice_array[8] = str_replace("_f'", "_b'", $invoice_array[8]);
		// Chop off the time part
		// $invoice_array[9] = strstr($invoice_array[9], ', ', true);
		// Current date
		$today = @date(DEFAULT_DATE_FORMAT);
		// Invoice id
		$invoice_id = $customer['invoice_id'];

		// Replace invoice placeholders by values
		$vars = array('[WB_URL]', '[ORDER_ID]', '[INVOICE_ID]', '[SHOP_NAME]', '[BANK_ACCOUNT]', '[CUSTOMER_NAME]', '[ADDRESS]', '[CUST_ADDRESS]', '[SHIPPING_ADDRESS]', '[CUST_EMAIL]', '[ITEM_LIST]', '[ORDER_DATE]', '[CURRENT_DATE]', '[TITLE]', '[DISPLAY_INVOICE]', '[DISPLAY_DELIVERY_NOTE]', '[DISPLAY_REMINDER]', '[CUST_TAX_NO]');
		$values = array(WB_URL, $invoice_array[0], $invoice_id, $invoice_array[1], nl2br($invoice_array[2]), $invoice_array[3], $invoice_address[0], $invoice_cust_address[0], $invoice_array[6], $invoice_array[7], $invoice_array[8], $invoice_array[9], $today, $title, $display_invoice, $display_delivery_note, $display_reminder, $cust_tax_no);
		$invoice = str_replace($vars, $values, $invoice_template);
		
		// Wrap invoice in a <div> and add an anchor
		$invoice = "<div id='invoice'>\n".$invoice."\n</div>\n<a name='bottom'></a>";
		// View invoice
		echo $invoice;
	}
	else {
		echo '<p class="mod_bakery_error_b">'.$TEXT['NONE_FOUND'].'!</p>';
	}
}

// Buttons and select document type for printing
?>
<br />
<div id="button">
<form name="doc_type" action="<?php echo WB_URL; ?>/modules/bakery/view_invoice.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_id=<?php echo $order_id; ?>#bottom" method="post" style="margin: 0;">
  <table width="98%" align="center" cellpadding="0" cellspacing="0" class="mod_bakery_submit_row_b">
      <tr valign="top">
        <td height="30" align="left" style="padding-left: 12px;"><input type="button" value="<?php echo $MOD_BAKERY['TXT_PRINT']; ?>" onclick="javascript: window.print();" style="width: 220px; margin-top: 5px;" />
          <select name="view" onchange="javascript:document.doc_type.submit();" style="width: 150px; margin: 5px 0 0 22px;">
            <option value="invoice"<?php echo $view == 'invoice' ? ' selected="selected"' : ''; ?>><?php echo $MOD_BAKERY['TXT_INVOICE']; ?></option>
            <option value="delivery_note"<?php echo $view == 'delivery_note' ? ' selected="selected"' : ''; ?>><?php echo $MOD_BAKERY['TXT_DELIVERY_NOTE']; ?></option>
            <option value="reminder"<?php echo $view == 'reminder' ? ' selected="selected"' : ''; ?>><?php echo $MOD_BAKERY['TXT_REMINDER']; ?></option>
          </select>
        </td>
        <td height="30" align="right" style="padding-right: 12px;"><input type="button" value="<?php echo $TEXT['CLOSE']; ?>" onclick="javascript: window.close();" style="width: 120px; margin-top: 5px;" />
        </td>
      </tr>
    </table>
</form>
</div>
</body>
