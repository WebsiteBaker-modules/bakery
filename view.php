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

// Look for language file
if (LANGUAGE_LOADED) {
    include(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        include(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Get user's username, display name, email, and id - needed for insertion into item info
$users = array();
$query_users = $database->query("SELECT user_id,username,display_name,email FROM ".TABLE_PREFIX."users");
if ($query_users->numRows() > 0) {
	while ($user = $query_users->fetchRow()) {
		// Insert user info into users array
		$user_id = $user['user_id'];
		$users[$user_id]['username']     = $user['username'];
		$users[$user_id]['display_name'] = $user['display_name'];
		$users[$user_id]['email']        = $user['email'];
	}
}

// Update the section id of the last visited Bakery section for use with MiniCart
$_SESSION['bakery']['last_section_id'] = $section_id;

// Get general settings
$query_general_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_general_settings");
if ($query_general_settings->numRows() > 0) {
	$fetch_general_settings = $query_general_settings->fetchRow();
	
	$setting_shop_name     = stripslashes($fetch_general_settings['shop_name']);
	$setting_shop_email    = stripslashes($fetch_general_settings['shop_email']);
	$setting_tac_url       = stripslashes($fetch_general_settings['tac_url']);
	$setting_shop_country  = stripslashes($fetch_general_settings['shop_country']);
	$setting_shop_state    = stripslashes($fetch_general_settings['shop_state']);
	$setting_shipping_form = stripslashes($fetch_general_settings['shipping_form']);
	$setting_company_field = stripslashes($fetch_general_settings['company_field']);
	$setting_state_field   = stripslashes($fetch_general_settings['state_field']);
	$setting_tax_no_field  = stripslashes($fetch_general_settings['tax_no_field']);
	$setting_tax_group     = stripslashes($fetch_general_settings['tax_group']);
	$setting_zip_location  = stripslashes($fetch_general_settings['zip_location']);
	$setting_cust_msg      = stripslashes($fetch_general_settings['cust_msg']);
	$setting_skip_cart     = stripslashes($fetch_general_settings['skip_cart']);
	$setting_use_captcha   = stripslashes($fetch_general_settings['use_captcha']);
	
	$setting_definable_field_0   = stripslashes($fetch_general_settings['definable_field_0']);
	$setting_definable_field_1   = stripslashes($fetch_general_settings['definable_field_1']);
	$setting_definable_field_2   = stripslashes($fetch_general_settings['definable_field_2']);
	$setting_stock_mode          = stripslashes($fetch_general_settings['stock_mode']);
	$setting_stock_limit         = stripslashes($fetch_general_settings['stock_limit']);
	$setting_out_of_stock_orders = stripslashes($fetch_general_settings['out_of_stock_orders']);

	$setting_shop_currency = stripslashes($fetch_general_settings['shop_currency']);
	$setting_dec_point     = stripslashes($fetch_general_settings['dec_point']);
	$setting_thousands_sep = stripslashes($fetch_general_settings['thousands_sep']);
	$setting_tax_by        = stripslashes($fetch_general_settings['tax_by']);
	$setting_tax_rate      = stripslashes($fetch_general_settings['tax_rate']);
	$setting_tax_rate1     = stripslashes($fetch_general_settings['tax_rate1']);
	$setting_tax_rate2     = stripslashes($fetch_general_settings['tax_rate2']);
	$setting_tax_included  = stripslashes($fetch_general_settings['tax_included']);
	
	$setting_tax_rate_shipping = stripslashes($fetch_general_settings['tax_rate_shipping']);
	$setting_free_shipping     = stripslashes($fetch_general_settings['free_shipping']);
	$setting_free_shipping_msg = stripslashes($fetch_general_settings['free_shipping_msg']);
	$setting_shipping_method   = stripslashes($fetch_general_settings['shipping_method']);
	$setting_shipping_domestic = stripslashes($fetch_general_settings['shipping_domestic']);
	$setting_shipping_abroad   = stripslashes($fetch_general_settings['shipping_abroad']);
	$setting_shipping_zone     = stripslashes($fetch_general_settings['shipping_zone']);
	$setting_zone_countries    = explode(",", stripslashes($fetch_general_settings['zone_countries']));  // make array
	$setting_shipping_d_a      = $setting_shipping_domestic."/".$setting_shipping_abroad;
}

// Get payment method settings
$query_payment_methods = $database->query("SELECT directory FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE active = '1'");
if ($query_payment_methods->numRows() > 0) {
	while ($fetch_payment_methods = $query_payment_methods->fetchRow()) {
		$setting_payment_methods[] = stripslashes($fetch_payment_methods['directory']);
	}
} else {
	$setting_payment_methods = array();
}
$num_payment_methods = count($setting_payment_methods);

// Get page settings
$query_page_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_page_settings WHERE section_id = '$section_id'");
if ($query_page_settings->numRows() > 0) {
	$fetch_page_settings    = $query_page_settings->fetchRow();

	$setting_page_offline   = stripslashes($fetch_page_settings['page_offline']);
	$setting_offline_text   = stripslashes($fetch_page_settings['offline_text']);
	$setting_header         = stripslashes($fetch_page_settings['header']);
	$setting_item_loop      = stripslashes($fetch_page_settings['item_loop']);
	$setting_footer         = stripslashes($fetch_page_settings['footer']);
	$setting_item_header    = stripslashes($fetch_page_settings['item_header']);
	$setting_item_footer    = stripslashes($fetch_page_settings['item_footer']);
	$setting_items_per_page = $fetch_page_settings['items_per_page'];
	$setting_num_cols       = $fetch_page_settings['num_cols'];
	$setting_resize         = stripslashes($fetch_page_settings['resize']);
	$setting_lightbox2      = stripslashes($fetch_page_settings['lightbox2']);
} else {
	$setting_page_offline   = 'no';
	$setting_offline_text   = '';
	$setting_header         = '';
	$setting_item_loop      = '';
	$setting_footer         = '';
	$setting_item_header    = '';
	$setting_item_footer    = '';
	$setting_items_per_page = 1;
	$setting_num_cols       = 3;
	$setting_resize         = 100;
	$setting_lightbox2      = 'detail';
}

// Get continue url
$query_continue_url = $database->query("SELECT p.link FROM ".TABLE_PREFIX."pages p INNER JOIN ".TABLE_PREFIX."mod_bakery_page_settings ps ON p.page_id = ps.page_id WHERE p.page_id = ps.continue_url AND ps.section_id = '$section_id'");
if ($query_continue_url->numRows() > 0) {
	$fetch_continue_url = $query_continue_url->fetchRow();
	$setting_continue_url = WB_URL.PAGES_DIRECTORY.stripslashes($fetch_continue_url['link']).PAGE_EXTENSION;
}

// Add a wrapper for Bakery to help with layout
echo "\n<div id='mod_bakery_wrapper_f'>\n";
$end_of_wrapper = "\n</div> <!-- End of bakery wrapper -->\n";








// ***************************************************************************************** //
// Check if we should show the SHOPPING CART, PROCESS ORDER, the MAIN PAGE or an ITEM itself //
// ***************************************************************************************** //


// GENERATE ORDER ID FOR NEW ORDERS
// ********************************

// MSIE image buttons only submit the click coordinates like 'anything_x' and 'anything_y'
// Convert POST name 'anything_x' to 'anything'
if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
	foreach ($_POST as $key => $value) {
		$count = 0;
		$ie_post_key = str_replace('_x', '', $key, $count);
		if ($count > 0) {
			$_POST[$ie_post_key] = 1;
		}
	}
}

// Check submitted POST/GET vars
if (isset($_REQUEST['view_cart'])  && ($_REQUEST['view_cart']   != '') || // normally POST, GET for MiniCart
   isset($_POST['add_to_cart'])    && ($_POST['add_to_cart']    != '') ||
   isset($_POST['update_cart'])    && ($_POST['update_cart']    != '') ||
   isset($_POST['submit_order'])   && ($_POST['submit_order']   != '') ||
   isset($_POST['hide_ship_form']) && ($_POST['hide_ship_form'] != '') ||
   isset($_POST['add_ship_form'])  && ($_POST['add_ship_form']  != '')) {

	// Check order id
	if (!isset($_SESSION['bakery']['order_id']) || ($_SESSION['bakery']['order_id'] == '')) {
		$mktime = @mktime();
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_customer (order_date) VALUES ('$mktime')");
		$order_id = $database->get_one("SELECT LAST_INSERT_ID()");
		$_SESSION['bakery']['order_id'] = $order_id;
		
		// Delete db records of not submitted orders older than 1 hour
		$outdate = $mktime - (60 * 60 * 1);
		$query_outdated_orders = $database->query("SELECT order_id FROM " .TABLE_PREFIX."mod_bakery_customer WHERE order_date < $outdate AND submitted = 'no'");
		if ($query_outdated_orders->numRows() > 0) {
			while ($outdated_orders = $query_outdated_orders->fetchRow()) {
				$outdated_order_id = stripslashes($outdated_orders['order_id']);

				// First put not sold items back to stock...
				$query_order = $database->query("SELECT item_id, quantity FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$outdated_order_id'");
				if ($query_order->numRows() > 0) {
					while ($order = $query_order->fetchRow()) {
						$item_id = stripslashes($order['item_id']);
						$quantity = stripslashes($order['quantity']);
						// Query item stock
						$query_items = $database->query("SELECT stock FROM " .TABLE_PREFIX."mod_bakery_items WHERE item_id = '$item_id'");
						$item = $query_items->fetchRow();
						$stock = stripslashes($item['stock']);
						// Only use stock admin if stock is not blank
						if (is_numeric($stock) && $stock != '') {
							// Update stock to required quantity
							$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock + '$quantity' WHERE item_id = '$item_id'");
						}
					}
				}
				
				// ...then delete not submitted orders
				$database->query("DELETE FROM " .TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$outdated_order_id' AND submitted = 'no'");
				$database->query("DELETE FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$outdated_order_id'");
			}
		}			
	}
	$order_id = $_SESSION['bakery']['order_id'];



	//  SHOPPING CART FUNCTIONS
	//  ***********************


	// PUT ITEM INTO THE CART
	if (isset($_POST['add_to_cart']) && ($_POST['add_to_cart'] != '')) {
		
		// Get item ID and quantity ( -> $value)
		$sql_result1 = $database->query("SELECT * FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id'");
		
		foreach ($_POST as $field => $value) {
			// Error message if quantity < 1
			if (substr($field,0,4) == "item" && $value < 1) {
				$cart_error[] = $MOD_BAKERY['ERR_QUANTITY_ZERO'];
			}
			if (substr($field,0,4) == "item" && $value > 0) {
				// Get item_id
				$item_id = substr($field,4,strlen($field)-4);
				// Get item attributes and make comma separated string
				if (isset($_POST['attribute'][0])) {
					$attributes = implode(",", $_POST['attribute']);
				} else {
				// If no attribute is given set it to "none"
					$attributes = "none";
				}

				//  Error message if item is in cart already
				while ($row1 = $sql_result1->fetchRow()) {
					if ($row1['item_id'] == $item_id && $row1['attributes'] == $attributes) {
						$cart_error[] = $MOD_BAKERY['ERR_ITEM_EXISTS'];
						include('view_cart.php');
						echo $end_of_wrapper;  // End of bakery wrapper
						return;
					}
				}
				
				// Get item price, sku, stock and tax_rate
				$sql_result2 = $database->query("SELECT title, price, sku, stock, tax_rate FROM " .TABLE_PREFIX."mod_bakery_items WHERE item_id = '$item_id'");
				$row2     = $sql_result2->fetchRow();
				$row2     = array_map('stripslashes', $row2);
				$title    = $row2['title'];
				$sku      = $row2['sku'];
				$price    = $row2['price'];
				$tax_rate = $row2['tax_rate'];
				$stock    = $row2['stock'];
				$quantity = $value;

				// Only use stock admin if stock is not blank
				if (is_numeric($stock) && $stock != '') {
					// If item is short of stock show error message
					if ($setting_out_of_stock_orders) {

						// Case: Allow out of stock orders
						if ($stock < $value) {
							$cart_error[] = "{$MOD_BAKERY['TXT_SHORT_OF_STOCK_SUBSEQUENT_DELIVERY']}!<br /><b>$stock</b> {$MOD_BAKERY['TXT_ITEMS']} <b>$title</b> {$MOD_BAKERY['TXT_AVAILABLE_QUANTITY']}.";
						}
						// Update stock
						$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock - '$value' WHERE item_id = '$item_id'");

					} else {
						// Case: No out of stock orders
						// If item is short of stock...
						if ($stock <= $value) {
							// ...set quantity to remaining stock
							$quantity = $stock;
							// Show error message
							if ($stock < $value) {
								$cart_error[] = "<b>$quantity</b> {$MOD_BAKERY['TXT_ITEMS']} <b>$title</b> {$MOD_BAKERY['TXT_AVAILABLE_QUANTITY']}.<br />{$MOD_BAKERY['TXT_SHORT_OF_STOCK_QUANTITY_CAPPED']}!";
							}
						}
						// Update stock
						$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock - '$quantity' WHERE item_id = '$item_id'");
					}
				}
				// Insert ordered item data into db
				if ($quantity > 0) {
					$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_order (order_id, item_id, attributes, sku, quantity, price, tax_rate) VALUES ('$order_id', '$item_id', '$attributes', '$sku', '$quantity', '$price', '$tax_rate')");
				}
			}
		}

		// If required skip cart
		if ($setting_skip_cart == "yes") {
			include('view_overview.php');
			echo $end_of_wrapper;  // End of bakery wrapper
			unset($_SESSION['bakery']['minicart']);
			return;
		} else {
			// Show cart
			include('view_cart.php');
			echo $end_of_wrapper;  // End of bakery wrapper
			return;
		}
	}


	// UPDATE CART
	elseif (isset($_POST['update_cart']) && ($_POST['update_cart'] != '')) {
	
		// Update quantities in db
		foreach ($_POST['quantity'] as $item_id => $attributes) {
			foreach ($_POST['quantity'][$item_id] as $attributes => $quantity) {
				$item_id = strip_tags($item_id);
				$attributes = strip_tags($attributes);
				$quantity = abs(strip_tags($quantity));

				// Query item stock
				$query_items = $database->query("SELECT title, stock FROM " .TABLE_PREFIX."mod_bakery_items WHERE item_id = '$item_id'");
				$item = $query_items->fetchRow();
				$title = stripslashes($item['title']);
				$stock = stripslashes($item['stock']);

				// Only use stock admin if stock is not blank
				if (is_numeric($stock) && $stock != '') {
					// If item is short of stock show error message
					if ($setting_out_of_stock_orders) {

						// Case: Allow out of stock orders
						// Query current item quantity 
						$query_order = $database->query("SELECT quantity FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id' AND item_id = '$item_id' AND attributes = '$attributes'");
						$order = $query_order->fetchRow();
						$quantity_current = stripslashes($order['quantity']);
						// Calculate difference
						$quantity_diff = $quantity - $quantity_current;
						// If items are short of stock show error message
						if ($stock < $quantity_diff) {
							$cart_error[0] = "{$MOD_BAKERY['TXT_SHORT_OF_STOCK_SUBSEQUENT_DELIVERY']}!";
							$available_quantity = $stock + $quantity_current;
							$cart_error[] = "<b>$available_quantity</b> {$MOD_BAKERY['TXT_ITEMS']} <b>$title</b> {$MOD_BAKERY['TXT_AVAILABLE_QUANTITY']}.";
						}						
						// Update stock to required quantity
						$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock - '$quantity_diff' WHERE item_id = '$item_id'");

					} else {
						// Case: No out of stock orders
						// Query current item quantity 
						$query_order = $database->query("SELECT quantity FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id' AND item_id = '$item_id' AND attributes = '$attributes'");
						$order = $query_order->fetchRow();
						$quantity_current = stripslashes($order['quantity']);
						// Calculate difference
						$quantity_diff = $quantity - $quantity_current;
						// If item is short of stock...
						if ($stock <= $quantity_diff) {
							// Set quantity to sum of remaining stock and current number of items in cart
							$quantity = $stock + $quantity_current;
							// Update stock abd deactivate item
							$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = '0' WHERE item_id = '$item_id'");
							// Compose error message
							if ($stock < $quantity_diff) {
								$cart_error[] = "<b>$quantity</b> {$MOD_BAKERY['TXT_ITEMS']} <b>$title</b> {$MOD_BAKERY['TXT_AVAILABLE_QUANTITY']}.";
							}
						}
						// Stock is large enough
						else {
							// Update stock to required quantity and make sure item is activated
							$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock - '$quantity_diff' WHERE item_id = '$item_id'");
						}
					}
				}
				// Update item order quantity
				$database->query("UPDATE " .TABLE_PREFIX."mod_bakery_order SET quantity = '$quantity' WHERE order_id = '$order_id' AND item_id = '$item_id' AND attributes = '$attributes'");
			}
		}

		// Add opening paragraph to the error message
		if (isset($cart_error) && !$setting_out_of_stock_orders) {
			$cart_error[] = "{$MOD_BAKERY['TXT_SHORT_OF_STOCK_QUANTITY_CAPPED']}!";
		}

		// Delete ordered items with quantity 0
		$database->query("DELETE FROM " .TABLE_PREFIX."mod_bakery_order WHERE quantity = '0' AND order_id = '$order_id'");
		
		// Enable success message to show in view_cart.php
		$cart_success = true;

		// Show cart
		include('view_cart.php');
		echo $end_of_wrapper;  // End of bakery wrapper
		return;
	}


	// SHOW ADDRESS FORM(S)
	elseif (!empty($_POST['submit_order']) || !empty($_POST['add_ship_form']) || !empty($_POST['hide_ship_form'])) {
		include('view_form.php');
		echo $end_of_wrapper;  // End of bakery wrapper
		return;
	}


	// SHOW CART ONLY
	else {
		include('view_cart.php');
		echo $end_of_wrapper;  // End of bakery wrapper
		return;
	}
}



//  SHOW ADDRESS FORM FOR MODIFYING
//  *******************************

elseif (isset($_POST['modify_address']) && isset($_SESSION['bakery']['order_id']) && ($_SESSION['bakery']['order_id'] != '')) {
	include('view_form.php');
	echo $end_of_wrapper;  // End of bakery wrapper
	return;
}



//  CHECK ADDRESS FORM, SAVE CUSTOMER DATA, THEN SHOW PAYMENT METHODS
//  *****************************************************************

elseif (isset($_POST['save_form']) && isset($_SESSION['bakery']['order_id']) && ($_SESSION['bakery']['order_id'] != '')) {
	include('save_form.php');
	echo $end_of_wrapper;  // End of bakery wrapper
	return;
}

	
	
//  SHOW PAYMENT METHODS
//  ********************

elseif (isset($_POST['pay_methods']) && isset($_SESSION['bakery']['order_id']) && ($_SESSION['bakery']['order_id'] != '')) {
	include('view_pay_methods.php');
	echo $end_of_wrapper;  // End of bakery wrapper
	return;
}



//  CANCEL ORDER
//  ************

elseif (isset($_POST['cancel_order']) && isset($_SESSION['bakery']['order_id']) && ($_SESSION['bakery']['order_id'] != '')) {

	// Get order id
	$order_id = $_SESSION['bakery']['order_id'];
	// First put not sold items back to stock...
	$query_order = $database->query("SELECT item_id, quantity FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id'");
	if ($query_order->numRows() > 0) {
		while ($order = $query_order->fetchRow()) {
			$item_id = stripslashes($order['item_id']);
			$quantity = stripslashes($order['quantity']);
			// Query item stock
			$query_items = $database->query("SELECT stock FROM " .TABLE_PREFIX."mod_bakery_items WHERE item_id = '$item_id'");
			$item = $query_items->fetchRow();
			$stock = stripslashes($item['stock']);
			// Only use stock admin if stock is not blank
			if (is_numeric($stock) && $stock != '') {
				// Update stock to required quantity
				$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock + '$quantity' WHERE item_id = '$item_id'");
			}
		}
	}

	// Delete the db records not needed any more
	$database->query("DELETE FROM " .TABLE_PREFIX."mod_bakery_customer WHERE order_id='$order_id' AND submitted='no'");
	$database->query("DELETE FROM " .TABLE_PREFIX."mod_bakery_order WHERE order_id='$order_id'");

	// Clean up the session array
	unset($_SESSION['bakery']);

	// Order canceled by user => show message
	echo "<div class='mod_bakery_success_f' style='margin-top: 50px'><p>{$MOD_BAKERY['TXT_CANCEL']}</p>";
	echo "<p>{$MOD_BAKERY['TXT_DELETED']}</p>";
	echo "<p style='font-weight: bold'>{$MOD_BAKERY['TXT_THANK_U_VISIT']}</p></div>";
	echo $end_of_wrapper;  // End of bakery wrapper 
	return;
}



// VIEW SUMMARY PAGE
// *****************

elseif (isset($_POST['summary']) && isset($_SESSION['bakery']['order_id']) && ($_SESSION['bakery']['order_id'] != '')) {
	// View payment methods
	include('view_summary.php');
	echo $end_of_wrapper;  // End of bakery wrapper
	return;
}



// SUBMIT FINAL ORDER
// ******************

elseif (isset($_POST['checkout']) && isset($_SESSION['bakery']['order_id']) && ($_SESSION['bakery']['order_id'] != '')) {
	$payment_method = $_SESSION['bakery']['payment_method'];
	if (is_file(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/check_payment.php')) {
		include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/check_payment.php');
		echo $end_of_wrapper;  // End of bakery wrapper
	}
}



// CHECK PAYMENT STATUS AND VIEW CONFIRMATION
// ******************************************

elseif (isset($_SESSION['bakery']['order_id']) && !empty($_SESSION['bakery']['order_id']) &&
      ((isset($_REQUEST['payment_method']) && in_array($_REQUEST['payment_method'], $setting_payment_methods)) ||
      (isset($_REQUEST['pm']) && in_array($_REQUEST['pm'], $setting_payment_methods)))) {

	// Get the payment method
	if (isset($_REQUEST['payment_method'])) {
		$payment_method = $_REQUEST['payment_method'];
	} elseif (isset($_REQUEST['pm'])) {
		$payment_method = $_REQUEST['pm'];
	} else {
		$payment_method = '';
	}

	// Check the status of the payment (error/canceled/success/pending)
	if ($payment_method == $_SESSION['bakery']['payment_method']) {
		require(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/check_payment.php');
	} else {
		$payment_status = 'error';
	}

	// Display error/canceled message OR in case of success/pending display confirmation and send emails
	require(WB_PATH.'/modules/bakery/view_confirmation.php');

	// Assign page filename for tracking with Google Analytics _trackPageview() function
	global $ga_page;
	$ga_page = "/$payment_method/$payment_status.php";
		
	echo $end_of_wrapper;  // End of bakery wrapper
	return;
}



// SET PAGE OFFLINE FOR MAINTENANCE
// ********************************

elseif ($setting_page_offline == 'yes') {
	echo '<div class="mod_bakery_error_f" style="margin-top: 50px"><p>'.$setting_offline_text.'</p></div>';
	echo $end_of_wrapper;  // End of bakery wrapper
	return;
}



// SHOW OVERVIEW PAGE
// ******************

elseif (!defined('ITEM_ID') OR !is_numeric(ITEM_ID)) {
	include('view_overview.php');
	echo $end_of_wrapper;  // End of bakery wrapper
}



// SHOW ITEM DETAIL PAGE
// *********************

elseif (defined('ITEM_ID') AND is_numeric(ITEM_ID)) {
	include('view_item.php');
	echo $end_of_wrapper;  // End of bakery wrapper
}