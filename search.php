<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2015, Christoph Marti
  
  This code is based on wb_searchext_mod_bakery v2.2 by thorn.
  It is adopted to Bakery v0.9 by thorn (thanks to thorn!).
  For further information see:
  http://nettest.thekk.de/pages/testing/new-search-function.php
  
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


function bakery_search($func_vars) {
	extract($func_vars, EXTR_PREFIX_ALL, 'func');
	
	// How many lines of excerpt we want to have at most
	$max_excerpt_num = $func_default_max_excerpt;
	// Show thumbnails?
	$show_thumb   = true;
	// Show option-attributes?
	$show_options = true;
	$divider      = ".";
	$result       = false;

	$table_item     = TABLE_PREFIX."mod_bakery_items";
	$table_images   = TABLE_PREFIX."mod_bakery_images";
	$table_item_att = TABLE_PREFIX."mod_bakery_item_attributes";
	$table_att      = TABLE_PREFIX."mod_bakery_attributes";

	// Fetch all active bakery-items in this section
	// Do not care if the shop is offline
	$query = $func_database->query("
		SELECT `item_id`, `title`, `sku`, `definable_field_0`, `definable_field_1`, `definable_field_2`, `link`, `description`, `full_desc`, `modified_when`, `modified_by`
		FROM `$table_item`
		WHERE `section_id`='$func_section_id' AND `active` = '1'
		ORDER BY `title` ASC
	");

	// Now call print_excerpt() for every single item
	if ($query->numRows() > 0) {
		while ($res = $query->fetchRow()) {

			// $res['link'] contains PAGES_DIRECTORY/bakery/... (e.g. "/pages/bakery/...")
			// Remove the leading PAGES_DIRECTORY
			$page_link = preg_replace('/^\\'.PAGES_DIRECTORY.'/', '', $res['link'], 1);

			// Thumbnail
			$pic_link = '';
			if ($show_thumb) {
				$query_thumb = $func_database->query("
					SELECT `filename`
					FROM `$table_images`
					WHERE `item_id` = '".$res['item_id']."' AND `active` = '1'
					ORDER BY position ASC
					LIMIT 1
				");
				if ($query_thumb->numRows() > 0) {
					$thumb     = $query_thumb->fetchRow();
					$thumb_dir = '/bakery/thumbs/item'.$res['item_id'].'/';
					if (is_file(WB_PATH.MEDIA_DIRECTORY.$thumb_dir.$thumb['filename'])) {
						$pic_link = $thumb_dir.$thumb['filename'];
					}
				}
			}

			// Option attributes
			$options = '.';
			if ($show_options) {
				$query_att = $func_database->query("
					SELECT `attribute_name`
					FROM `$table_item_att` INNER JOIN `$table_att` USING(`attribute_id`)
					WHERE `item_id` = '{$res['item_id']}'
					ORDER BY `$table_att`.`option_id` ASC
				");

				if ($query_att->numRows() > 0) {
					while ($res_att = $query_att->fetchRow()) {
						$options .= $res_att['attribute_name'].'.';
					}
				}
			}

			$mod_vars = array(
				'page_link' => $page_link,
				'page_link_target' => "#wb_section_$func_section_id",
				
				// "item-title" as link, and "description" as description
				'page_title' => $res['title'],
				'page_description' => $res['description'],
				// or page_title as link, and  "item-title" as description
			//	'page_title' => $func_page_title,
			//	'page_description' => $res['title'],
				
				'page_modified_when' => $res['modified_when'],
				'page_modified_by' => $res['modified_by'],
				'text' => $res['title'].$divider.$res['description'].$divider.$res['full_desc'].$divider.$res['definable_field_0'].$divider.$res['definable_field_1'].$divider.$res['definable_field_2'].$divider.$options.$divider.$res['sku'].$divider,
				'max_excerpt_num' => $max_excerpt_num,
				'pic_link' => $pic_link
			);
			if (print_excerpt2($mod_vars, $func_vars)) {
				$result = true;
			}
		}
	}
	return $result;
}
