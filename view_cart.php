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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');

// Include WB template parser and create template object
require_once(WB_PATH.'/include/phplib/template.inc');
$tpl = new Template(WB_PATH.'/modules/bakery/templates/cart');
// Define how to deal with unknown {PLACEHOLDERS} (remove:=default, keep, comment)
$tpl->set_unknowns('keep');
// Define debug mode (0:=disabled (default), 1:=variable assignments, 2:=calls to get variable, 4:=debug internals)
$tpl->debug = 0;



// EMPTY CART
// **********

// If cart is empty, show an error message and a "continue shopping" button
$sql_result1 = $database->query("SELECT * FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id'");
$n_row = $sql_result1->numRows();
if ($n_row < 1) {
	// Show empty cart error message using template file
	$tpl->set_file('empty_cart', 'empty.htm');
	$tpl->set_var(array(
		'ERR_CART_EMPTY'			=>	$MOD_BAKERY['ERR_CART_EMPTY'],
		'TXT_CONTINUE_SHOPPING'		=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING']
	));
	$tpl->pparse('output', 'empty_cart');
	return;
}



// GET ITEM DETAILS FROM DATABASE
// ******************************

// Get order id, item id, attributes, sku, quantity, price and tax_rate from db table order
$i = 1;
while ($row1 = $sql_result1->fetchRow()) {
	foreach ($row1 as $field => $value) {
		if ($field != "order_id") {
			$items[$i][$field] = $value;
			// Get item name, shipping. link and main image from db items table
			if ($field == "item_id") {
				$sql_result2 = $database->query("SELECT title, shipping, link FROM ".TABLE_PREFIX."mod_bakery_items WHERE item_id = '".$row1['item_id']."'");
				$row2 = $sql_result2->fetchRow();	
				$items[$i]['name']     = $row2[0];
				$items[$i]['shipping'] = $row2[1];
				$items[$i]['link']     = WB_URL.PAGES_DIRECTORY.$row2[2].PAGE_EXTENSION;

				// Item thumbnail
				// Default if no thumb exists
				$items[$i]['thumb_url']    = WB_URL.'/modules/bakery/images/transparent.gif';
				$items[$i]['thumb_width']  = $cart_thumb_max_size;
				$items[$i]['thumb_height'] = $cart_thumb_max_size;
				// Get main thumb (image with position == 1)
				$main_thumb = '';
				$main_thumb = $database->get_one("SELECT `filename` FROM ".TABLE_PREFIX."mod_bakery_images WHERE `item_id` = '{$row1['item_id']}' AND `active` = '1' ORDER BY `position` ASC LIMIT 1");
				$main_thumb = str_replace(".png", ".jpg", $main_thumb);
				// Item thumb if exists
				$thumb_dir               = '/bakery/thumbs/item'.$row1['item_id'].'/';
				$items[$i]['thumb_path'] = WB_PATH.MEDIA_DIRECTORY.$thumb_dir.$main_thumb;
				if (is_file($items[$i]['thumb_path'])) {
					// Thumb URL
					$items[$i]['thumb_url'] = WB_URL.MEDIA_DIRECTORY.$thumb_dir.$main_thumb;
					// Get thumb image size
					$size = getimagesize($items[$i]['thumb_path']);
					if ($size[0] > 1 && $size[1] > 1) {
						if ($size[0] > $size[1]) {
							$items[$i]['thumb_height'] = round($cart_thumb_max_size * $size[1] / $size[0]);
						}
						elseif ($size[0] < $size[1]) {
							$items[$i]['thumb_width']  = round($cart_thumb_max_size * $size[0] / $size[1]);
						}
					}
				}
			} 
		}
	}

	// Default if item has no attributes
	$items[$i]['show_attribute'] = '';
	$items[$i]['attribute_price'] = 0;
	// Get item attribute ids
	if ($items[$i]['attributes'] != "none") {
		$attribute_ids = explode(",", $items[$i]['attributes']);
		foreach ($attribute_ids as $attribute_id) {
			// Get option name and attribute name, price, operator (+/-/=)
			$query_attributes = $database->query("SELECT o.option_name, a.attribute_name, ia.price, ia.operator FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_attributes a ON o.option_id = a.option_id INNER JOIN ".TABLE_PREFIX."mod_bakery_item_attributes ia ON a.attribute_id = ia.attribute_id WHERE ia.item_id = {$items[$i]['item_id']} AND ia.attribute_id = $attribute_id");
			$attribute = $query_attributes->fetchRow();
			// Calculate the item attribute prices sum depending on the operator
			if ($attribute['operator'] == "+") {
				$items[$i]['attribute_price'] = $items[$i]['attribute_price'] + $attribute['price'];
			} elseif ($attribute['operator'] == "-") {
				$items[$i]['attribute_price'] = $items[$i]['attribute_price'] - $attribute['price'];
			// If operator is '=' then override the item price by the attribute price
			} elseif ($attribute['operator'] == "=") {
				$items[$i]['price'] = $attribute['price'];
			}
			// Prepare option and attributes for display in cart table
			$items[$i]['show_attribute'] .= ", ".$attribute['option_name'].":&nbsp;".$attribute['attribute_name'];
		}
		// Now calculate item price including all attribute prices
		$items[$i]['price'] = $items[$i]['price'] + $items[$i]['attribute_price'];
		// Never undercut zero
		$items[$i]['price'] = $items[$i]['price'] < 0 ? 0 : $items[$i]['price'];
		// Remove leading comma and space
		$items[$i]['show_attribute'] = substr($items[$i]['show_attribute'], 2);
	}
	// Increment counter
	$i++;
}



// SHOW TITLE AND MESSAGES IF ANY
// ******************************

// Assign page filename for tracking with Google Analytics _trackPageview() function
global $ga_page;
$ga_page = '/view_cart.php';

// Show cart title using template file
$tpl->set_file('cart_title', 'title.htm');
$tpl->set_var(array(
	'TXT_CART'			=>	$MOD_BAKERY['TXT_CART']
));
$tpl->pparse('output', 'cart_title');

// If enabled show cart success message using template file
if (isset($cart_success)) {
	$tpl->set_file('cart_success', 'success.htm');
	$tpl->set_var(array(
		'TXT_UPDATE_CART_SUCCESS'		=>	$MOD_BAKERY['TXT_UPDATE_CART_SUCCESS']
	));
	$tpl->pparse('output', 'cart_success');
}

// Compose the cart error messages
if (isset($cart_error) && is_array($cart_error)) {
	$message = '';
	foreach ($cart_error as $value) {
		$message .= "<p>".$value."</p>";
	}
	// Show cart error messages using template file
	$tpl->set_file('cart_error', 'error.htm');
	$tpl->set_var(array(
		'MESSAGE'					=>	$message,
		'TXT_CONTINUE_SHOPPING'		=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING']
	));
	$tpl->pparse('output', 'cart_error');
}



// SHOW CART TABLE
// ***************

// Determine shipping sum of all items specified
for ($i = 1; $i <= sizeof($items); $i++) {
	$shipping_sum[] = $items[$i]['shipping'];
}
$shipping_sum = array_sum($shipping_sum);

// With shipping per item 
if ($shipping_sum > 0) {
	$display_shipping = '';
	$colspan_l = 7;
	$colspan_m = 6;
}
// No shipping per item
else {
	$display_shipping = "none";
	$colspan_l = 6;
	$colspan_m = 5;
}

// Show cart table header using template file
$tpl->set_file('cart_table_header', 'table_header.htm');
$tpl->set_var(array(
	'TXT_ORDER_ID'			=>	$MOD_BAKERY['TXT_ORDER_ID'],
	'ORDER_ID'				=>	$order_id,
	'SETTING_CONTINUE_URL'	=>	$setting_continue_url,
	'TXT_SKU'				=>	$MOD_BAKERY['TXT_SKU'],
	'TXT_NAME'				=>	$MOD_BAKERY['TXT_NAME'],
	'TXT_QUANTITY'			=>	$MOD_BAKERY['TXT_QUANTITY'],
	'TXT_PRICE'				=>	$MOD_BAKERY['TXT_PRICE'],
	'SETTING_SHOP_CURRENCY'	=>	$setting_shop_currency,
	'DISPLAY_SHIPPING'		=>	$display_shipping,
	'TXT_SHIPPING'			=>	$MOD_BAKERY['TXT_SHIPPING'],
	'TXT_SUM'				=>	$MOD_BAKERY['TXT_SUM'],
	'COLSPAN_L'				=>	$colspan_l
));
$tpl->pparse('output', 'cart_table_header');


// Loop through items
$order_total = 0;
for ($i = 1; $i <= sizeof($items); $i++) {

	// Calculate order total with shipping per item
	if ($shipping_sum > 0) {
		$f_price       = number_format($items[$i]['price'], 2, $setting_dec_point, $setting_thousands_sep);
		$f_shipping    = number_format($items[$i]['shipping'], 2, $setting_dec_point, $setting_thousands_sep);
		// See http://www.bakery-shop.ch/#shipping_total
		// $total         = $items[$i]['quantity'] * ($items[$i]['price'] + $items[$i]['shipping']);
		$total         = $items[$i]['quantity'] * $items[$i]['price'];
		$f_total       = number_format($total, 2, $setting_dec_point, $setting_thousands_sep);
		$order_total   = $order_total + $total;
		$f_order_total = number_format($order_total, 2, $setting_dec_point, $setting_thousands_sep);
	}
	// Calculate order total without shipping per item
	else {
		$f_price       = number_format($items[$i]['price'], 2, $setting_dec_point, $setting_thousands_sep);
		$f_shipping    = 0;
		$total         = $items[$i]['quantity'] * $items[$i]['price'];
		$f_total       = number_format($total, 2, $setting_dec_point, $setting_thousands_sep);
		$order_total   = $order_total + $total;
		$f_order_total = number_format($order_total, 2, $setting_dec_point, $setting_thousands_sep);
	}

	// Show cart table body using template file 
	$tpl->set_file('cart_table_body', 'table_body.htm');
	$tpl->set_var(array(
		'THUMB_URL'			=>	$items[$i]['thumb_url'],
		'THUMB_WIDTH'		=>	$items[$i]['thumb_width'],
		'THUMB_HEIGHT'		=>	$items[$i]['thumb_height'],
		'LINK'				=>	$items[$i]['link'],
		'SKU'				=>	$items[$i]['sku'],
		'NAME'				=>	$items[$i]['name'],
		'ATTRIBUTE'			=>	$items[$i]['show_attribute'],
		'ITEM_ID'			=>	$items[$i]['item_id'],
		'ATTRIBUTES'		=>	$items[$i]['attributes'],
		'QUANTITY'			=>	$items[$i]['quantity'],
		'WB_URL'			=>	WB_URL,
		'TEXT_DELETE'		=>	$TEXT['DELETE'],
		'PRICE'				=>	$f_price,
		'DISPLAY_SHIPPING'	=>	$display_shipping,
		'SHIPPING'			=>	$f_shipping,
		'TOTAL'				=>	$f_total
	));
	$tpl->pparse('output', 'cart_table_body');
}


// Show order total and buttons using template file
$tpl->set_file('cart_table_footer', 'table_footer.htm');
$tpl->set_var(array(
	'COLSPAN_L'				=>	$colspan_l,
	'COLSPAN_M'				=>	$colspan_m,
	'TXT_SUM'				=>	$MOD_BAKERY['TXT_SUM'],
	'SETTING_SHOP_CURRENCY'	=>	$setting_shop_currency,
	'ORDER_TOTAL'			=>	$f_order_total,
	'TXT_CONTINUE_SHOPPING'	=>	$MOD_BAKERY['TXT_CONTINUE_SHOPPING'],
	'TXT_UPDATE_CART'		=>	$MOD_BAKERY['TXT_UPDATE_CART'],
	'TXT_SUBMIT_ORDER'		=>	$MOD_BAKERY['TXT_SUBMIT_ORDER'],
	'ORDER_ID'		    	=>	$order_id
));

$tpl->pparse('output', 'cart_table_footer');
