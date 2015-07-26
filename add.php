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


//Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}


// Look for language File
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}


// Set default values for page settings

// Shop
$page_offline = "no";
$offline_text = $MOD_BAKERY['ERR_OFFLINE_TEXT'];
$continue_url = $page_id;

// Layout
$header = $admin->add_slashes('<div class="mod_bakery_main_div_cart_bt_f">
<form action="[SHOP_URL]" method="post">
<input type="submit" name="view_cart" class="mod_bakery_bt_cart_f" value="[VIEW_CART]" />
</form>
</div>
<table cellpadding="5" cellspacing="0" border="0" width="98%">
<tr>
');
$item_loop = $admin->add_slashes('<td class="mod_bakery_main_td_f">
[THUMB]
<br />
<a href="[LINK]"><span class="mod_bakery_main_title_f">[TITLE]</span></a>
<br />
[DESCRIPTION]
<br />
[TXT_PRICE]: [CURRENCY] [PRICE]
<br />
[TXT_STOCK]: [STOCK]
<br />
<form action="[SHOP_URL]" method="post">
[OPTION]
<br />
<input type="text" name="item[ITEM_ID]" class="mod_bakery_main_input_f" value="1" size="2" />
<input type="submit" name="add_to_cart" class="mod_bakery_bt_add_f" value="[ADD_TO_CART]" />
</form>
</td>');
$footer = $admin->add_slashes('</tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" width="98%" style="display: [DISPLAY_PREVIOUS_NEXT_LINKS]">
<tr>
<td colspan="3" align="left"><hr /></td>
</tr>
<tr>
<td width="35%" align="left">[PREVIOUS_PAGE_LINK]</td>
<td width="30%" align="center">[TXT_ITEM] [OF] </td>
<td width="35%" align="right">[NEXT_PAGE_LINK]</td>
</tr>
</table>');
$item_header = $admin->add_slashes('<center>');
$item_footer = $admin->add_slashes('[IMAGE]
<form action="[SHOP_URL]" method="post">
<table border="0" cellspacing="0" cellpadding="5" class="mod_bakery_item_table_f">
<tr>
<td colspan="2" align="left" valign="top"><h2 class="mod_bakery_item_title_f">[TITLE]</h2></td>
</tr>
<tr>
<td align="left" valign="top"><span class="mod_bakery_item_sku_f">[TXT_SKU]:</span></td>
<td align="left" valign="top">[SKU]</td>
</tr>
<tr>
<td align="left" valign="top"><span class="mod_bakery_item_price_f">[TXT_PRICE]:</span></td>
<td align="left" valign="top">[CURRENCY] [PRICE]</td>
</tr>
<tr>
<td align="left" valign="top"><span class="mod_bakery_item_shipping_f">[TXT_SHIPPING]:</span></td>
<td align="left" valign="top">[CURRENCY] [SHIPPING] </td>
</tr>
<tr>
<td align="left" valign="top"><span class="mod_bakery_item_stock_f">[TXT_STOCK]:</span></td>
<td align="left" valign="top">[STOCK]</td>
</tr>
<tr>   	    
<td align="left" valign="top"><span class="mod_bakery_item_full_desc_f"><p>[TXT_FULL_DESC]:</p></span></td>
<td align="left" valign="top">[FULL_DESC]</td>
</tr>
<tr>   	    
<td align="left" valign="top"><span class="mod_bakery_shipping_cost_f">[TXT_SHIPPING_COST]:</span></td>
<td align="left" valign="top">
[TXT_DOMESTIC]: [CURRENCY] [SHIPPING_DOMESTIC]<br />
[TXT_ABROAD]: [CURRENCY] [SHIPPING_ABROAD]</td>
</tr>
[OPTION]
<tr>   	  
<td align="left" valign="top"> </td>
<td align="left" valign="top">
<input type="text" name="item[ITEM_ID]"  class="mod_bakery_item_input_f" value="1" size="2" />
<input type="submit" name="add_to_cart" class="mod_bakery_bt_add_f" value="[ADD_TO_CART]" />
</td>
</tr>
</table>
</form>
[PREVIOUS] | <a href="[BACK]">[TXT_BACK]</a> | [NEXT]
</center>
<br />');


// Insert default values into table page_settings 
$database->query("INSERT INTO ".TABLE_PREFIX."mod_bakery_page_settings (section_id, page_id, page_offline, offline_text, continue_url, header, item_loop, footer, item_header, item_footer)
VALUES ('$section_id', '$page_id', '$page_offline', '$offline_text', '$continue_url', '$header', '$item_loop', '$footer', '$item_header', '$item_footer')");
