<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2017, Christoph Marti

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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly");
}

// Include WB functions
require_once(WB_PATH.'/framework/functions.php');

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');

// Delete item access file, images and thumbs associated with the section
$query_items = $database->query("SELECT item_id, link FROM ".TABLE_PREFIX."mod_bakery_items WHERE section_id = '$section_id'");
if ($query_items->numRows() > 0) {
	while ($item = $query_items->fetchRow()) {
		// Delete item access file
		if (is_writable(WB_PATH.PAGES_DIRECTORY.$item['link'].PAGE_EXTENSION)) { unlink(WB_PATH.PAGES_DIRECTORY.$item['link'].PAGE_EXTENSION); }
		// Delete any images if they exists
		$image = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/images/item'.$item['item_id'];
		$thumb = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.$item['item_id'];
		if (is_dir($image)) { rm_full_dir($image); }
		if (is_dir($thumb)) { rm_full_dir($thumb); }
		// Delete item attributes in db
		$database->query("DELETE FROM ".TABLE_PREFIX."mod_bakery_item_attributes WHERE item_id = '{$item['item_id']}'");
	}
}

// Delete items and page settings in db
$database->query("DELETE FROM ".TABLE_PREFIX."mod_bakery_items WHERE section_id = '$section_id'");
$database->query("DELETE FROM ".TABLE_PREFIX."mod_bakery_page_settings WHERE section_id = '$section_id'");
