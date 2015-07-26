<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2015, Christoph Marti

  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
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


if ($_POST['attribute_name'] != '') {
	// Insert new attribute name into db
	if ($_POST['attribute_id'] == '') {
		$option_id = $admin->add_slashes(strip_tags($_POST['option_id']));
		$attribute_name = $admin->add_slashes(strip_tags($_POST['attribute_name']));
		$database->query("INSERT INTO ".TABLE_PREFIX."mod_bakery_attributes (option_id, attribute_name) VALUES ('$option_id', '$attribute_name')");
	}
	// Update attribute name
	else {
		$option_id = $admin->add_slashes(strip_tags($_POST['option_id']));
		$attribute_name = $admin->add_slashes(strip_tags($_POST['attribute_name']));
		$attribute_id = $_POST['attribute_id'];
		$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_attributes SET option_id = '$option_id', attribute_name = '$attribute_name' WHERE attribute_id = '$attribute_id'");
	}
} else {
	$admin->print_error($MESSAGE['MEDIA']['BLANK_NAME'], WB_URL.'/modules/bakery/modify_options.php?page_id='.$page_id);
}


// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), WB_URL.'/modules/bakery/modify_options.php?page_id='.$page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/bakery/modify_options.php?page_id='.$page_id);
}


// Print admin footer
$admin->print_footer();
