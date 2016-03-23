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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');

// Get module pages directory from general setting table
$query_general_settings = $database->query("SELECT pages_directory FROM ".TABLE_PREFIX."mod_bakery_general_settings");
$general_settings       = $query_general_settings->fetchRow();
$module_pages_directory = '/'.$general_settings['pages_directory'];

// Delete
$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE name = 'module' AND value = 'bakery'");
$database->query("DELETE FROM ".TABLE_PREFIX."search WHERE extra = 'bakery'");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_items");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_images");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_options");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_attributes");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_item_attributes");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_customer");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_order");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_general_settings");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_page_settings");
$database->query("DROP TABLE ".TABLE_PREFIX."mod_bakery_payment_methods");

// Include WB functions file
require_once(WB_PATH.'/framework/functions.php');

$directory = WB_PATH.PAGES_DIRECTORY.$module_pages_directory;
if (is_dir($directory)) {
	rm_full_dir($directory);
}

$directory = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir;
if (is_dir($directory)) {
	rm_full_dir($directory);
}
