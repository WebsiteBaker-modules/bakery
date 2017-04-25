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


// Remove any tags and add slashes
$update_payment_method = $admin->add_slashes(strip_tags($_POST['update_payment_method']));
$modify_payment_method = $admin->add_slashes(strip_tags($_POST['modify_payment_method']));
$reload = $_POST['reload'] == 'true' ? true : false;


// Update payment methods 'active'
foreach ($_POST['all_payment_methods'] as $pm_id) {
	if (is_numeric($pm_id)) {
		$active = isset($_POST['payment_methods'][$pm_id]) ? 1 : 0;
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_payment_methods SET active = '$active' WHERE pm_id = '$pm_id'");
	}
}


// Write fields into db
foreach ($_POST['update'] as $field => $value) {
	$field = $admin->add_slashes(strip_tags($field));
	$value = ($update_payment_method == "invoice" && $field == "value_4") ? $admin->add_slashes($value) : $admin->add_slashes(strip_tags($value));
	$updates[] = "$field = '$value'";
}
$update_string = implode($updates,", ");

// Update payment methods
$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_payment_methods SET $update_string WHERE directory = '$update_payment_method'");

// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	// If a payment method has been selected go back to the payment method page
	if ($reload) {
		$admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/bakery/modify_payment_methods.php?page_id='.$page_id.'&section_id='.$section_id.'&payment_method='.$modify_payment_method);
	} else {
		$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
	}
}

// Print admin footer
$admin->print_footer();
