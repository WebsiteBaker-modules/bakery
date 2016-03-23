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
$update_when_modified = true; // Tells script to update when this page was last updated
require(WB_PATH.'/modules/admin.php');

// This code removes any php tags and adds slashes
$friendly = array('&lt;', '&gt;', '?php');
$raw = array('<', '>', '');

$page_offline = isset($_POST['page_offline']) ? "yes" : "no";
$offline_text = $admin->add_slashes(strip_tags($_POST['offline_text']));
$continue_url = $admin->add_slashes(strip_tags($_POST['continue_url']));
$header = $admin->add_slashes(str_replace($friendly, $raw, $_POST['header']));
$item_loop = $admin->add_slashes(str_replace($friendly, $raw, $_POST['item_loop']));
$footer = $admin->add_slashes(str_replace($friendly, $raw, $_POST['footer']));
$item_header = $admin->add_slashes(str_replace($friendly, $raw, $_POST['item_header']));
$item_footer = $admin->add_slashes(str_replace($friendly, $raw, $_POST['item_footer']));
$items_per_page = $_POST['items_per_page'];
$num_cols = $_POST['num_cols'];
if (extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) {
	$resize = $_POST['resize'];
} else {
	$resize = '';
}
if (isset($_POST['lb2_overview']) && isset($_POST['lb2_detail'])) {
	$lightbox2 = "all";
} elseif (isset($_POST['lb2_overview'])) {
	$lightbox2 = "overview";
} elseif (isset($_POST['lb2_detail'])) {
	$lightbox2 = "detail";
} else {
	$lightbox2 = '';
}

// Update settings without the "continue shopping url" of specified section ids
if ($_POST['modify'] == "multiple") {
	$where_clause = '';
	foreach ($_POST['modify_sections'] as $section_id) {
		if (!is_numeric($section_id)) {
			continue;
		}
		$where_clause .= "section_id = '$section_id' OR ";
	}
	$where_clause = rtrim($where_clause, ' OR ');

	$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_page_settings SET page_offline = '$page_offline', offline_text = '$offline_text', header = '$header', item_loop = '$item_loop', footer = '$footer', item_header = '$item_header', item_footer = '$item_footer', items_per_page = '$items_per_page', num_cols = '$num_cols', resize = '$resize', lightbox2 = '$lightbox2' WHERE $where_clause");
}

// Update settings without the "continue shopping url" of all section ids 
elseif ($_POST['modify'] == "all") {
$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_page_settings SET page_offline = '$page_offline', offline_text = '$offline_text', header = '$header', item_loop = '$item_loop', footer = '$footer', item_header = '$item_header', item_footer = '$item_footer', items_per_page = '$items_per_page', num_cols = '$num_cols', resize = '$resize', lightbox2 = '$lightbox2'");
}

// Update settings of current section id only
elseif ($_POST['modify'] == "current") {
$database->query("UPDATE ".TABLE_PREFIX."mod_bakery_page_settings SET page_offline = '$page_offline', offline_text = '$offline_text', continue_url = '$continue_url', header = '$header', item_loop = '$item_loop', footer = '$footer', item_header = '$item_header', item_footer = '$item_footer', items_per_page = '$items_per_page', num_cols = '$num_cols', resize = '$resize', lightbox2 = '$lightbox2' WHERE section_id = '$section_id'");
}

// Check if there is a db error, otherwise say successful
if ($database->is_error()) {
	$admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
	$admin->print_success($TEXT['SUCCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

// Print admin footer
$admin->print_footer();
