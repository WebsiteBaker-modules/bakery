<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2017, Christoph Marti

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
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// Loop through the orders... 
foreach ($_POST['status'] as $order_id => $status) {
	$status = $admin->add_slashes(strip_tags($status));
	// ...and update status
	$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_customer SET status = '$status' WHERE order_id = '$order_id'");

	// If order was canceled put the ordered items back to the stock
	if ($status == 'canceled') {

		// Get the ordered items and their corresponding quantity
		$query_ordered_items = $database->query("SELECT item_id, quantity FROM ".TABLE_PREFIX."mod_bakery_order WHERE order_id = '$order_id'");
		while ($ordered_items = $query_ordered_items->fetchRow()) {
			$item_id  = $ordered_items['item_id'];
			$quantity = $ordered_items['quantity'];

			// Update item quantity
			$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET stock = stock + '$quantity' WHERE item_id = '$item_id'");
		}
	}
}

// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/bakery/modify_orders.php?page_id='.$page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/bakery/modify_orders.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
