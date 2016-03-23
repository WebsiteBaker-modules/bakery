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


require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
require_once(WB_PATH.'/framework/class.admin.php');

// Look for language file
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Get the payment method referer
$payment_method = isset($_GET['payment_method']) && is_string($_GET['payment_method']) ? $_GET['payment_method'] : '';

// Get general settings
$query_general_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_general_settings");
if ($query_general_settings->numRows() > 0) {
	$general_settings = $query_general_settings->fetchRow();
	$general_settings = array_map('stripslashes', $general_settings);
}

// Get page settings
$query_page_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_page_settings WHERE page_id = '$page_id'");
if ($query_page_settings->numRows() > 0) {
	$page_settings = $query_page_settings->fetchRow();
	$page_settings = array_map('stripslashes', $page_settings);
}

// Get continue url
$query_continue_url = $database->query("SELECT p.link FROM ".TABLE_PREFIX."pages p INNER JOIN ".TABLE_PREFIX."mod_bakery_page_settings ps ON p.page_id = ps.page_id WHERE p.page_id = ps.continue_url AND ps.section_id = '$section_id'");
if ($query_continue_url->numRows() > 0) {
	$fetch_continue_url = $query_continue_url->fetchRow();
	$continue_url = stripslashes($fetch_continue_url['link']);
}

// Get customer data
$query_customer = $database->query("SELECT invoice_id, invoice FROM ".TABLE_PREFIX."mod_bakery_customer WHERE submitted != 'no' ORDER BY order_id DESC LIMIT 1");
if ($query_customer->numRows() > 0) {
	$customer = $query_customer->fetchRow();
	if ($customer['invoice'] != '') {
		// Convert string to array
		$invoice       = stripslashes($customer['invoice']);
		$invoice_array = explode("&&&&&", $invoice);
		// Vars
		$order_id       = $invoice_array[0];
		#$shop_name     = $invoice_array[1];
		#$bank_account  = $invoice_array[2];
		$cust_name      = $invoice_array[3];
		$address        = $invoice_array[4];
		$cust_address   = $invoice_array[5];
		$ship_address   = $invoice_array[6];
		$cust_email     = $invoice_array[7];
		$html_item_list = $invoice_array[8];
		$order_date     = $invoice_array[9];
		$cust_tax_no    = $invoice_array[15];
		$cust_msg       = $invoice_array[16];

		// Invoice id
		$invoice_id     = $customer['invoice_id'];

		// For invoice template chop phone number and email from customer address
		$arr_invoice_tpl_address      = explode("<br /><br />", $invoice_array[4]);
		$invoice_tpl_address          = $arr_invoice_tpl_address[0];
		$arr_invoice_tpl_cust_address = explode("<br /><br />", $invoice_array[5]);
		$invoice_tpl_cust_address     = $arr_invoice_tpl_cust_address[0];
	}
}	

// Get items
$query_item = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_items ORDER BY item_id DESC LIMIT 1");
if ($query_item->numRows() > 0) {
	$item = $query_item->fetchRow();
	$item = array_map('stripslashes', $item);
}

// Get page info
$query_page = $database->query("SELECT * FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id'");
if ($query_page->numRows() > 0) {
	$page = $query_page->fetchRow();
}

// Check if we should show number of items or "in stock" message or nothing at all
// Display number of items
if ($general_settings['stock_mode'] == "number") {
	if ($item['stock'] < 1) {
		$stock = 0;
	} else {
		$stock = $item['stock'];
	}
// Display text message	
} elseif (is_numeric($general_settings['stock_limit']) && $general_settings['stock_limit'] != '') {
	if ($item['stock'] < 1) {
		$stock = $MOD_BAKERY['TXT_OUT_OF_STOCK'];
	} elseif ($item['stock'] > $general_settings['stock_limit']) {
		$stock = $MOD_BAKERY['TXT_IN_STOCK'];
	} else {
		$stock = $MOD_BAKERY['TXT_SHORT_OF_STOCK'];
	}
// Display nothing
} else {
	$stock = '';
}
?>



<h2>KEYS TO THE PLACEHOLDERS USED IN THE BAKERY TEMPLATES</h2>
<br />
<table  id="mod_bakery_placeholders_b" width="100%" cellpadding="5" cellspacing="0">
  <tr>
    <td><blockquote><strong>Template</strong></blockquote>      
      <ul>
        <li>PH = Page Header</li>
        <li>PPL = Page Product Loop</li>
        <li>PF = Page Footer</li>
        <li>IH = Item Header</li>
        <li>IF = Item Footer</li>
      </ul>
	</td>
    <td><blockquote><strong>Output Example Data</strong></blockquote>
	  <ul>
		<li><span class="mod_bakery_placeholders_localisation_b">Blue: Localisation (example language = <?php echo(defined('LANGUAGE') ? LANGUAGE : "EN"); ?>)</span></li>
		<li><span class="mod_bakery_placeholders_general_settings_b">Green: General settings</span></li>
		<li><span class="mod_bakery_placeholders_page_settings_b">Brown: Page settings (example page id = <?php echo $page_id; ?>)</span></li>
		<li><span class="mod_bakery_placeholders_customer_b">Red: Order and Customer (example order id = <?php echo (isset($order_id) ? $order_id : $TEXT['NONE_FOUND']); ?>)</span></li>
		<li><span class="mod_bakery_placeholders_items_b">Orange: Item (example item id = <?php echo (isset($item['item_id']) ? $item['item_id'] : $TEXT['NONE_FOUND']); ?>)</span></li>
		<li><span class="mod_bakery_placeholders_page_b">Pink: Page (example page id = <?php echo $page_id; ?>)</span></li>
      </ul>
	</td>
  </tr>
</table>
<br />
<table width="100%" cellpadding="5" cellspacing="0"  id="mod_bakery_placeholders_b">
  <tr>
    <td height="30" align="right"><input name="button" type="button" style="margin-right: 20px;" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_page_settings.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" value="&lt;&lt; <?php echo $MOD_BAKERY['TXT_PAGE_SETTINGS']; ?>" />
        <input name="button2" type="button" style="margin-right: 20px;" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_payment_methods.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" value="&lt;&lt; <?php echo $MOD_BAKERY['TXT_PAYMENT_METHODS']; ?>" />
	</td>
  </tr>
</table>
<br />
<table  id="mod_bakery_placeholders_b" width="100%" cellpadding="5" cellspacing="0">
  <tr class="mod_bakery_placeholders_header_b">
    <td colspan="8"><p><strong><a name="html"></a>Main Page and Item HTML Templates &nbsp;&nbsp;&nbsp;</strong>( &gt; Page Settings &gt; Layout Settings )</p></td>
  </tr>
  <tr>
    <th width="27%" align="left">Placeholder</th>
    <th width="3%">PH </th>
    <th width="4%">PPL</th>
    <th width="3%">PF</th>
    <th width="3%">IH</th>
    <th width="3%">IF</th>
    <th width="24%" align="left">Explanation</th>
    <th width="33%" align="left">Output Example Data</th>
  </tr>
  <tr valign="top">
    <td>[ADD_TO_CART]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Add to cart</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_ADD_TO_CART']; ?></td>
  </tr>
  <tr valign="top">
    <td>[BACK]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>URL to the current (overview) page</td>
    <td class="mod_bakery_placeholders_page_b"><?php echo (isset($page['link']) ? WB_URL."<wbr>".PAGES_DIRECTORY.$page['link'].PAGE_EXTENSION : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CURRENCY]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Currency (general setting)</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['shop_currency']) ? $general_settings['shop_currency'] : "USD"); ?></td>
  </tr>
  <tr valign="top">
    <td>[DATE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item modification date</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['modified_when']) ? gmdate(DATE_FORMAT, $item['modified_when']+TIMEZONE) : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[DESCRIPTION]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item description</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['description']) ? $item['description'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[DISPLAY_NAME]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>User display name </td>
    <td><?php echo (isset($_SESSION['DISPLAY_NAME']) ? $_SESSION['DISPLAY_NAME'] : "Administrator"); ?></td>
  </tr>
  <tr valign="top">
    <td>[DISPLAY_PREVIOUS_NEXT_LINKS]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>CSS display: </td>
    <td>none</td>
  </tr>
  <tr valign="top">
    <td>[EMAIL]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>User email</td>
    <td><?php echo (isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[FIELD_1]<br />[FIELD_2]<br />[FIELD_3]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Up to 3 free definable item fields which can be defined in the general settings.</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['definable_field_0']) ? $item['definable_field_0'] : ''); ?></td>
  </tr>
  <tr valign="top">
    <td>[FULL_DESC]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item full description</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['full_desc']) ? $item['full_desc'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[IMAGE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Main item image - only displayed if selected at section &quot;2. Product Images&quot; of the &quot;Add/modify item&quot; page</td>
    <td class="mod_bakery_placeholders_items_b">Depends on various settings: For an example please see the source code of your Bakery page! </td>
  </tr>
  <tr valign="top">
    <td>[IMAGES]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>All item images except for the item main image </td>
    <td class="mod_bakery_placeholders_items_b">Depends on various settings: For an example please see the source code of your Bakery page! </td>
  </tr>
  
  <tr valign="top">
    <td>[ITEM_ID]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item id</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['item_id']) ? $item['item_id'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[LINK]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Link to the item (detail)</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['link']) ? WB_URL.PAGES_DIRECTORY."<wbr>".$item['link'].PAGE_EXTENSION : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[NEXT]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Link to the next item</td>
    <td class="mod_bakery_placeholders_items_b"><a><?php echo $TEXT['NEXT']; ?> &gt;&gt;</a></td>
  </tr>
  <tr valign="top">
    <td>[NEXT_LINK]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Link to the next page</td>
    <td class="mod_bakery_placeholders_page_b"><a><?php echo $TEXT['NEXT']; ?> &gt;&gt;</a></td>
  </tr>
  <tr valign="top">
    <td>[NEXT_PAGE_LINK]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Link to the next page</td>
    <td class="mod_bakery_placeholders_page_b"><a><?php echo $TEXT['NEXT_PAGE']; ?> &gt;&gt;</a></td>
  </tr>
  <tr valign="top">
    <td>[OF]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>View number of items out of total number of items </td>
    <td class="mod_bakery_placeholders_general_settings_b">1-3 <?php echo $TEXT['OF']; ?> 10 </td>
  </tr>
  <tr valign="top">
    <td>[OPTION]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item option</td>
    <td class="mod_bakery_placeholders_items_b">Size:<br />
    &lt;select name='attribute1' class='mod_bakery_main_select_f'&gt;<br />&lt;option value='XL'&gt;XL&lt;/option&gt;<br />&lt;option value='X'&gt;X&lt;/option&gt;<br />&lt;option value='M'&gt;M&lt;/option&gt;<br />&lt;option value='S'&gt;S&lt;/option&gt;<br />&lt;option value='XS'&gt;XS&lt;/option&gt;<br />&lt;/select&gt;</td>
  </tr>
  <tr valign="top">
    <td>[OUT_OF]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>View item number out of total number of items</td>
    <td class="mod_bakery_placeholders_items_b">2 <?php echo $TEXT['OUT_OF']; ?> 10 </td>
  </tr>
  <tr valign="top">
    <td>[PAGE_TITLE]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Page title</td>
    <td class="mod_bakery_placeholders_page_b"><?php echo (isset($page['page_title']) ? $page['page_title'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[PREVIOUS]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Link to the previous item</td>
    <td class="mod_bakery_placeholders_items_b"><a>&lt;&lt; <?php echo $TEXT['PREVIOUS']; ?></a></td>
  </tr>
  <tr valign="top">
    <td>[PREVIOUS_LINK]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Link to the previous page</td>
    <td class="mod_bakery_placeholders_page_b"><a>&lt;&lt; <?php echo $TEXT['PREVIOUS']; ?></a></td>
  </tr>
  <tr valign="top">
    <td>[PREVIOUS_PAGE_LINK]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Link to the previous page</td>
    <td class="mod_bakery_placeholders_page_b"><a>&lt;&lt; <?php echo $TEXT['PREVIOUS_PAGE']; ?></a></td>
  </tr>
  <tr valign="top">
    <td>[PRICE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item Price</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['price']) ? $item['price'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHIPPING]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item Shipping</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['shipping']) ? $item['shipping'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHIPPING_DOMESTIC]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Shipping domestic</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['shipping_domestic']) ? $general_settings['shipping_domestic'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHIPPING_ABROAD]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Shipping abroad</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['shipping_abroad']) ? $general_settings['shipping_abroad'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHIPPING_D_A]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Shipping domestic/abroad</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['shipping_domestic']) && isset($general_settings['shipping_abroad']) ? $general_settings['shipping_domestic'].'/'.$general_settings['shipping_abroad'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHOP_URL]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Continue shopping URL (page setting) = URL to the current (overview) page</td>
    <td class="mod_bakery_placeholders_page_settings_b"><?php echo (isset($continue_url) ? WB_URL.PAGES_DIRECTORY."<wbr>".$continue_url.PAGE_EXTENSION : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SKU]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Stockkeeping unit, item no or product code</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['sku']) ? $item['sku'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[STOCK]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td> 
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Depending on the general setting &quot;Stock mode&quot; either the number of items in stock, an image or a localised short message like &quot;in stock&quot;, &quot;short of stock&quot; or &quot;out of stock&quot;.</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo $stock; ?></td>
  </tr>
  <tr valign="top">
    <td>[TAX_RATE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item Tax Rate</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['tax_rate']) ? $item['tax_rate'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[TEXT_OF]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>of</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $TEXT['OF']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TEXT_OUT_OF]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>out of</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $TEXT['OUT_OF']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TEXT_READ_MORE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Localisation of "<em>Read more</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $TEXT['READ_MORE']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_ABROAD]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Abroad</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_ABROAD']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_BACK]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Back</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $TEXT['BACK']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_DOMESTIC]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Domestic</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_DOMESTIC']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_FIELD_1]<br />[TXT_FIELD_2]<br />[TXT_FIELD_3]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Labels for up to 3 free definable item fields which can be defined in the general settings.</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['definable_field_0']) ? $general_settings['definable_field_0'] : ''); ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_FULL_DESC]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>Full description</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_FULL_DESC']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_ITEM]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">PF</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Item</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_ITEM']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_PRICE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of "<em>Price</em>"</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_PRICE']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_SHIPPING]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>Shipping</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_SHIPPING']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_SHIPPING_COST]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>Shipping cost </em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_SHIPPING_COST']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_SKU]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>SKU</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_SKU']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_STOCK]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>Stock</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_STOCK']; ?></td>
  </tr>
  <tr valign="top">
    <td>[TXT_TAX_RATE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Localisation of &quot;<em>Tax Rate</em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_TAX_RATE']; ?></td>
  </tr>
  <tr valign="top">
    <td>[THUMB]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Thumbnail of the main item image - only displayed if selected at section &quot;2. Product Images&quot; of the &quot;Add/modify item&quot; page</td>
    <td class="mod_bakery_placeholders_items_b">Depends on various settings: For an example please see the source code of your Bakery page!	</td>
  </tr>
  <tr valign="top">
    <td>[THUMBS]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>All thumbnails of the item images except for  the item main image </td>
    <td class="mod_bakery_placeholders_items_b">Depends on various settings: For an example please see the source code of your Bakery page! </td>
  </tr>
  <tr valign="top">
    <td>[TIME]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item modification time</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['modified_when']) ? gmdate(TIME_FORMAT, $item['modified_when']+TIMEZONE) : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[TITLE]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>Item title / Item name</td>
    <td class="mod_bakery_placeholders_items_b"><?php echo (isset($item['title']) ? $item['title'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[USERNAME]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>User name</td>
    <td><?php echo (isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : "admin"); ?></td>
  </tr>
  <tr valign="top">
    <td>[USER_ID]</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>PPL</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>IH</td>
    <td class="mod_bakery_placeholders_column_b">IF</td>
    <td>User id</td>
    <td><?php echo (isset($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : "1"); ?></td>
  </tr>
  <tr valign="top">
    <td>[VIEW_CART]</td>
    <td class="mod_bakery_placeholders_column_b">PH</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="mod_bakery_placeholders_column_b">&nbsp;</td>
    <td>Localisation of &quot;<em>View cart </em>&quot;</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_VIEW_CART']; ?></td>
  </tr>
  <tr valign="bottom">
    <td colspan="8" height="30" align="right">
	  <input type="button" value="&lt;&lt; <?php echo $MOD_BAKERY['TXT_PAGE_SETTINGS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_page_settings.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="margin-right: 20px;" /></td>
  </tr>
  <tr class="mod_bakery_placeholders_header_b">
    <td colspan="8"><p><strong><a name="email"></a>Email Templates</strong> &nbsp;&nbsp;&nbsp;( &gt; Payment Methods &gt; E-Mail Settings )</p></td>
  </tr>
  <tr>
    <th align="left">Placeholder</th>
    <th colspan="6" align="left">Explanation</th>
    <th align="left">Output Example Data</th>
  </tr>
  <tr valign="top">
    <td>[ADDRESS]</td>
    <td colspan="6"><strong>Either</strong> the value of <code>[CUST_ADDRESS]</code> <strong>or</strong> -  if provided by the customer - the value of <code>[SHIPPING_ADDRESS]</code></td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($address) ? $address : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[BANK_ACCOUNT]</td>
    <td colspan="6">Information about the shop bank account (general setting)</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['bank_account']) ? $general_settings['bank_account'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_ADDRESS]</td>
    <td colspan="6">Customer address<br />
      Contains the data of the first part of the Bakery address form: The customer postal address, phone number and email.<br />
      It is the main address and used for billing and - if no shipping address is provided by the customer - for shipping as well.<br />    </td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_address) ? $cust_address : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_EMAIL]</td>
    <td colspan="6">Customer email address</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_email) ? $cust_email : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_MSG]</td>
    <td colspan="6">Customers message: Notice written by the customer is sent to the shop admin by email</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_msg) ? $cust_msg : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_TAX_NO]</td>
    <td colspan="6">Customer VAT-No</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_tax_no) ? $cust_tax_no : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUSTOMER_NAME]</td>
    <td colspan="6">Customer first and last name</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_name) ? $cust_name : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[ITEM_LIST]</td>
    <td colspan="6">List of all ordered items</td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>[ORDER_ID]</td>
    <td colspan="6">Order id</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($order_id) ? $order_id : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHIPPING_ADDRESS]</td>
    <td colspan="6">Shipping address<br />
      If  the shipping address differs from the customer address and it is provided by the customer, it contains the data of the second part of the Bakery address form: The shipping postal address.<br />
      It is used for shipping only.<br />    </td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($ship_address) ? $ship_address : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHOP_NAME]</td>
    <td colspan="6">Shop name (general setting)</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['shop_name']) ? $general_settings['shop_name'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="bottom">
    <td colspan="8" height="30" align="right">
	  <input type="button" value="&lt;&lt; <?php echo $MOD_BAKERY['TXT_PAYMENT_METHODS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_payment_methods.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&payment_method=<?php echo $payment_method; ?>';" style="margin-right: 20px;" /></td>
  </tr>
  <tr class="mod_bakery_placeholders_header_b">
    <td colspan="8"><p><strong><a name="invoice"></a>Invoice Template</strong> &nbsp;&nbsp;&nbsp;( &gt; Payment Methods &gt; select Invoice &gt; Layout Settings )</p></td>
  </tr>
  <tr>
    <th align="left">Placeholder</th>
    <th colspan="6" align="left">Explanation</th>
    <th align="left">Output Example Data</th>
  </tr>
  <tr valign="top">
    <td>[ADDRESS]</td>
    <td colspan="6"><strong>Either</strong> the value of <code>[CUST_ADDRESS]</code> <strong>or</strong> -  if provided by the customer - the value of <code>[SHIPPING_ADDRESS]</code></td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($invoice_tpl_address) ? $invoice_tpl_address : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[BANK_ACCOUNT]</td>
    <td colspan="6">Information about the shop bank account (general setting)</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['bank_account']) ? $general_settings['bank_account'] : '&nbsp;'); ?></td>
  </tr>
   <tr valign="top">
    <td>[CURRENT_DATE]</td>
    <td colspan="6">Current date when you load the invoice from the server</td>
    <td><?php echo(@date(DEFAULT_DATE_FORMAT)); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_ADDRESS]</td>
    <td colspan="6">Customer address<br />
Contains the customer postal address of the first part of the  Bakery address form. It is the main address and used for billing and - if no shipping address is provided by the customer - for shipping as well.</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($invoice_tpl_cust_address) ? $invoice_tpl_cust_address : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_EMAIL]</td>
    <td colspan="6">Customer email address</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_email) ? $cust_email : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUST_TAX_NO]</td>
    <td colspan="6">Customer VAT-No</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_tax_no) ? $cust_tax_no : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[CUSTOMER_NAME]</td>
    <td colspan="6">Customer first and last name</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($cust_name) ? $cust_name : '&nbsp;'); ?></td>
  </tr>

  <tr valign="top">
    <td>[DISPLAY_DELIVERY_NOTE]</td>
    <td colspan="6">Displays the contained html text at delivery note pages only. Use like this:<br />
	<code>&lt;div style=&quot;display: [DISPLAY_DELIVERY_NOTE]&quot;&gt;Your specific delivery text.&lt;/div&gt;</code></td>
    <td>Delivery note: &quot;&quot; (empty)<br />      
    Invoice and reminder: &quot;none&quot;</td>
  </tr>

  <tr valign="top">
    <td>[DISPLAY_INVOICE]</td>
    <td colspan="6">Displays the contained html text at invoice  pages only. Use like this:<br />
      <code>&lt;div style=&quot;display: [DISPLAY_INVOICE]&quot;&gt;Your specific invoice text.&lt;/div&gt;</code></td>
    <td>Invoice: &quot;&quot; (empty)<br />
 Delivery note and reminder: &quot;none&quot;</td>
  </tr>
  
  <tr valign="top">
    <td>[DISPLAY_REMINDER]</td>
    <td colspan="6">Displays the contained html text at reminder  pages only. Use like this:<br />
	<code>&lt;div style=&quot;display: [DISPLAY_REMINDER]&quot;&gt;Your specific reminder text.&lt;/div&gt;</code></td>
    <td>Reminder: &quot;&quot; (empty)<br />
Invoice and delivery note: &quot;none&quot;</td>
  </tr>
  
  <tr valign="top">
    <td>[INVOICE_ID]</td>
    <td colspan="6">Consecutive numbering of invoices.<br />Please note: Invoice id usually is not equal order id.</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (!empty($invoice_id) ? $invoice_id : '&nbsp;'); ?></td>
  </tr>

  <tr valign="top">
    <td>[ITEM_LIST]</td>
    <td colspan="6">List of all ordered items</td>
    <td>For an example see the commented lines in the source code of this page!
	<span class="mod_bakery_placeholders_customer_b">
<!-- 

INVOICE TEMPLATE: EXAMPLE FOR [ITEM_LIST]
*****************************************
<?php echo (isset($html_item_list) ? $html_item_list : "No example available."); ?> 
-->
    </span></td>
  </tr>
  <tr valign="top">
    <td>[ORDER_DATE]</td>
    <td colspan="6">Date and time when customer made his order</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($order_date) ? $order_date : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[ORDER_ID]</td>
    <td colspan="6">Order id</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($order_id) ? $order_id : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHIPPING_ADDRESS]</td>
    <td colspan="6">Shipping address<br />
If  the shipping address differs from the customer address and it is provided by the customer, it contains the data of the second part of the Bakery address form: The shipping postal address.<br />
It is used for shipping only.</td>
    <td class="mod_bakery_placeholders_customer_b"><?php echo (isset($ship_address) ? $ship_address : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[SHOP_NAME]</td>
    <td colspan="6">Shop name (general setting)</td>
    <td class="mod_bakery_placeholders_general_settings_b"><?php echo (isset($general_settings['shop_name']) ? $general_settings['shop_name'] : '&nbsp;'); ?></td>
  </tr>
  <tr valign="top">
    <td>[TITLE]</td>
    <td colspan="6">Displays the localisation of &quot;<em>invoice</em>&quot;, &quot;<em>delivery note</em>&quot; or &quot;<em>reminder</em>&quot; depending on the admins selection</td>
    <td class="mod_bakery_placeholders_localisation_b"><?php echo $MOD_BAKERY['TXT_INVOICE']; ?><br /><?php echo $MOD_BAKERY['TXT_DELIVERY_NOTE']; ?><br /><?php echo $MOD_BAKERY['TXT_REMINDER']; ?></td>
  </tr>
  <tr valign="top">
    <td>[WB_URL]</td>
    <td colspan="6">WebsiteBaker URL (used for invoice logo)</td>
    <td><?php echo WB_URL; ?></td>
  </tr>
  <tr valign="bottom">
    <td colspan="8" height="30"><p><strong>PLEASE NOTE</strong>:<br />
    Don't forget to add your companys name and address to the invoice template and replace the Bakery logo by your shop logo.</p></td>
  </tr>
  <tr valign="bottom">
    <td colspan="8" height="30" align="right">
	  <input type="button" value="&lt;&lt; <?php echo $MOD_BAKERY['TXT_PAYMENT_METHODS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_payment_methods.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&payment_method=invoice';" style="margin-right: 20px;" /></td>
  </tr>
</table>


<?php

// Print admin footer
$admin->print_footer();
