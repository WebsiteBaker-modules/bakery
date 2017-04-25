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


if (defined('WB_URL')) {
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_items`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_items` ( "
			. "`item_id` INT NOT NULL AUTO_INCREMENT ,"
			. "`section_id` INT NOT NULL DEFAULT '0' ,"
			. "`page_id` INT NOT NULL DEFAULT '0' ,"
			. "`group_id` INT NOT NULL DEFAULT '0' ,"
			. "`active` INT NOT NULL DEFAULT '0' ,"
			. "`position` INT NOT NULL DEFAULT '0' ,"
			. "`title` VARCHAR(255) NOT NULL DEFAULT '' ,"
			. "`sku` VARCHAR(20) NOT NULL DEFAULT '' ,"
			. "`stock` VARCHAR(20) NOT NULL DEFAULT '' ,"
			. "`price` DECIMAL(9,2) NOT NULL ,"
			. "`shipping` DECIMAL(9,2) NOT NULL ,"
			. "`tax_rate` DECIMAL(5,2) NOT NULL ,"
			. "`definable_field_0` VARCHAR(150) NOT NULL DEFAULT '' ,"
			. "`definable_field_1` VARCHAR(150) NOT NULL DEFAULT '' ,"
			. "`definable_field_2` VARCHAR(150) NOT NULL DEFAULT '' ,"
			. "`link` TEXT NOT NULL ,"
			. "`description` TEXT NOT NULL ,"
			. "`full_desc` TEXT NOT NULL ,"
			. "`modified_when` INT NOT NULL DEFAULT '0' ,"
			. "`modified_by` INT NOT NULL DEFAULT '0' ,"
			. "`created_when` int(11) NOT NULL DEFAULT '0' ,"
			. "`created_by` int(11) NOT NULL DEFAULT '0' ,"
			. "PRIMARY KEY (`item_id`)"
			. " )";
	$database->query($mod_bakery);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_images`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_images` ( "
			. "`img_id` int(11) NOT NULL AUTO_INCREMENT,"
			. "`item_id` int(11) NOT NULL DEFAULT '0',"
			. "`item_attribute_id` int(11) NOT NULL DEFAULT '0',"
			. "`filename` varchar(150) NOT NULL DEFAULT '',"
			. "`active` enum('1','0') NOT NULL DEFAULT '1',"
			. "`position` int(11) NOT NULL DEFAULT '0',"
			. "`alt` varchar(255) NOT NULL DEFAULT '',"
			. "`title` varchar(255) NOT NULL DEFAULT '',"
			. "`caption` text NOT NULL,"  
			. "PRIMARY KEY (`img_id`)"
			. " )";
	$database->query($mod_bakery);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_options`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_options` ( "
			. " `option_id` INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY ,"
			. " `option_name` VARCHAR(50) NOT NULL"
			. " )";
	$database->query($mod_bakery);
	
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_attributes`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_attributes` ( "
			. " `attribute_id` INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY ,"
			. " `option_id` INT(6) NOT NULL ,"
			. " `attribute_name` VARCHAR(50) NOT NULL "
			. " )";
	$database->query($mod_bakery);
	
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_item_attributes`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_item_attributes` ( "
			. " `assign_id` INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY ,"
			. " `item_id` INT(6) NOT NULL ,"
			. " `option_id` INT(6) NOT NULL ,"
			. " `attribute_id` INT(6) NOT NULL ,"
			. " `price` DECIMAL(9,2) NOT NULL ,"
			. " `operator` VARCHAR(1) NOT NULL "
			. " )";
	$database->query($mod_bakery);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_customer`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_customer` ( "
			. "`order_id` INT(6) NOT NULL AUTO_INCREMENT ,"
			. "`order_date` INT(11) NOT NULL ,"
			. "`shipping_fee` DECIMAL(9,2) NOT NULL ,"
			. "`sales_tax` DECIMAL(9,2) NOT NULL ,"
			. "`submitted` VARCHAR(20) NOT NULL DEFAULT 'no' ,"
			. "`transaction_id` VARCHAR(50) NOT NULL DEFAULT 'none' ,"
			. "`transaction_status` VARCHAR(10) NOT NULL DEFAULT 'none' ,"
			. "`status` VARCHAR(20) NOT NULL DEFAULT 'none' ,"
			. "`user_id` INT(6) NOT NULL ,"
			. "`cust_company` VARCHAR(50) NOT NULL ,"
			. "`cust_first_name` VARCHAR(50) NOT NULL ,"
			. "`cust_last_name` VARCHAR(50) NOT NULL ,"
			. "`cust_tax_no` VARCHAR(11) NOT NULL ,"
			. "`cust_street` VARCHAR(50) NOT NULL ,"
			. "`cust_city` VARCHAR(50) NOT NULL ,"
			. "`cust_state` VARCHAR(50) NOT NULL ,"
			. "`cust_country` VARCHAR(2) NOT NULL ,"
			. "`cust_zip` VARCHAR(10) NOT NULL ,"
			. "`cust_email` VARCHAR(50) NOT NULL ,"
			. "`cust_phone` VARCHAR(20) NOT NULL ,"
			. "`ship_company` VARCHAR(50) NOT NULL ,"
			. "`ship_first_name` VARCHAR(50) NOT NULL ,"
			. "`ship_last_name` VARCHAR(50) NOT NULL ,"
			. "`ship_street` VARCHAR(50) NOT NULL ,"
			. "`ship_city` VARCHAR(50) NOT NULL ,"
			. "`ship_state` VARCHAR(50) NOT NULL ,"
			. "`ship_country` VARCHAR(2) NOT NULL ,"
			. "`ship_zip` VARCHAR(10) NOT NULL ,"
			. "`invoice_id` INT(6) NOT NULL ,"
			. "`sent_invoices` INT(1) NOT NULL DEFAULT '0' ,"
			. "`invoice` TEXT NOT NULL ,"
			. "PRIMARY KEY (`order_id`)"
			. " )";
	$database->query($mod_bakery);
	
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_order`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_order` ( "
			. "`order_id` INT(6) NOT NULL AUTO_INCREMENT ,"
			. "`item_id` INT(5) NOT NULL ,"
			. "`attributes` VARCHAR(50) NOT NULL ,"
			. "`sku` VARCHAR(20) NOT NULL ,"
			. "`quantity` INT(7) NOT NULL ,"
			. "`price` DECIMAL(9,2) NOT NULL ,"
			. "`tax_rate` DECIMAL(5,2) NOT NULL ,"
			. "PRIMARY KEY (`order_id`, `item_id`, `attributes`)"
			. " )";
	$database->query($mod_bakery);
	
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_general_settings`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ( "
			. "`shop_id` INT NOT NULL DEFAULT '0' ,"
			. "`shop_name` VARCHAR(100) NOT NULL ,"
			. "`shop_email` VARCHAR(50) NOT NULL ,"
			. "`pages_directory` VARCHAR(20) NOT NULL DEFAULT 'bakery' ,"
			. "`tac_url` VARCHAR(255) NOT NULL ,"
			. "`shop_country` VARCHAR(2) NOT NULL DEFAULT 'CH' ,"
			. "`shop_state` VARCHAR(5) NOT NULL ,"
			. "`shipping_form` VARCHAR(10) NOT NULL DEFAULT 'none' ,"
			. "`company_field` ENUM('show','hide') NOT NULL ,"
			. "`state_field` ENUM('show','hide') NOT NULL ,"
			. "`tax_no_field` ENUM('show','hide') NOT NULL ,"
			. "`tax_group` VARCHAR(255) NOT NULL ,"
			. "`zip_location` ENUM('inside','end') NOT NULL ,"
			. "`no_revocation` VARCHAR(50) NOT NULL DEFAULT 'e-goods' ,"
			. "`hide_country` ENUM('show','hide') NOT NULL DEFAULT 'show' ,"
			. "`cust_msg` ENUM('show','hide') NOT NULL  ,"
			. "`skip_cart` ENUM('yes','no') NOT NULL DEFAULT 'no' ,"
			. "`display_settings` ENUM('1','0') NOT NULL DEFAULT '0' ,"
			. "`use_captcha` ENUM('yes','no') NOT NULL DEFAULT 'no' ,"
			. "`definable_field_0` VARCHAR(50) NOT NULL ,"
			. "`definable_field_1` VARCHAR(50) NOT NULL ,"
			. "`definable_field_2` VARCHAR(50) NOT NULL ,"
			. "`stock_mode` VARCHAR(10) NOT NULL DEFAULT 'none' ,"
			. "`stock_limit` INT(3) NOT NULL DEFAULT '10' ,"
			. "`out_of_stock_orders` ENUM('1','0') NOT NULL DEFAULT '0' ,"
			. "`shop_currency` VARCHAR(3) NOT NULL DEFAULT 'CHF' ,"
			. "`dec_point` VARCHAR(1) NOT NULL DEFAULT '.' ,"
			. "`thousands_sep` VARCHAR(1) NOT NULL DEFAULT '\'' ,"
			. "`tax_by` VARCHAR(10) NOT NULL DEFAULT 'country' ,"
			. "`tax_rate` DECIMAL(5,2) NOT NULL DEFAULT '0' ,"
			. "`tax_rate1` DECIMAL(5,2) NOT NULL DEFAULT '0' ,"
			. "`tax_rate2` DECIMAL(5,2) NOT NULL DEFAULT '0' ,"
			. "`tax_included` ENUM('included','excluded') NOT NULL DEFAULT 'included' ,"
			. "`tax_rate_shipping` DECIMAL(5,2) NOT NULL DEFAULT '0' ,"
			. "`free_shipping` DECIMAL(7,2) NOT NULL DEFAULT '99999.99' ,"
			. "`free_shipping_msg` ENUM('show','hide') NOT NULL DEFAULT 'hide' ,"
			. "`shipping_method` VARCHAR(20) NOT NULL DEFAULT 'flat' ,"
			. "`shipping_domestic` DECIMAL(6,2) NOT NULL DEFAULT '0' ,"
			. "`shipping_abroad` DECIMAL(6,2) NOT NULL DEFAULT '0' ,"
			. "`shipping_zone` DECIMAL(6,2) NOT NULL ,"
			. "`zone_countries` TEXT NOT NULL ,"
			. "PRIMARY KEY (`shop_id`)"
			. " )";
	$database->query($mod_bakery);
	
	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_page_settings`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_page_settings` ( "
			. "`section_id` INT NOT NULL DEFAULT '0' ,"
			. "`page_id` INT NOT NULL DEFAULT '0' ,"
			. "`page_offline` ENUM('yes','no') NOT NULL DEFAULT 'no' ,"
			. "`offline_text` TINYTEXT NOT NULL ,"
			. "`continue_url` INT(11) NOT NULL ,"
			. "`header` TEXT NOT NULL ,"
			. "`item_loop` TEXT NOT NULL ,"
			. "`footer` TEXT NOT NULL ,"
			. "`item_header` TEXT NOT NULL ,"
			. "`item_footer` TEXT NOT NULL ,"
			. "`items_per_page` INT NOT NULL DEFAULT '0' ,"
			. "`num_cols` INT NOT NULL DEFAULT '3' ,"
			. "`resize` INT NOT NULL DEFAULT '100' ,"
			. "`lightbox2` VARCHAR(10) NOT NULL DEFAULT 'detail' ,"
			. "PRIMARY KEY (`section_id`)"
			. " )";
	$database->query($mod_bakery);

	$database->query("DROP TABLE IF EXISTS `".TABLE_PREFIX."mod_bakery_payment_methods`");
	$mod_bakery = "CREATE TABLE `".TABLE_PREFIX."mod_bakery_payment_methods` ( "
			. "`pm_id` INT(11) NOT NULL AUTO_INCREMENT ,"
			. "`active` INT(1) NOT NULL ,"
			. "`directory` VARCHAR(50) NOT NULL ,"
			. "`name` VARCHAR(50) NOT NULL ,"
			. "`version` VARCHAR(6) NOT NULL ,"
			. "`author` VARCHAR(50) NOT NULL ,"
			. "`requires` VARCHAR(6) NOT NULL ,"
			. "`field_1` VARCHAR(150) NOT NULL ,"
			. "`value_1` TEXT NOT NULL ,"
			. "`field_2` VARCHAR(150) NOT NULL ,"
			. "`value_2` TEXT NOT NULL ,"
			. "`field_3` VARCHAR(150) NOT NULL ,"
			. "`value_3` TEXT NOT NULL ,"
			. "`field_4` VARCHAR(150) NOT NULL ,"
			. "`value_4` TEXT NOT NULL ,"
			. "`field_5` VARCHAR(150) NOT NULL ,"
			. "`value_5` TEXT NOT NULL ,"
			. "`field_6` VARCHAR(150) NOT NULL ,"
			. "`value_6` TEXT NOT NULL ,"
			. "`cust_email_subject` TEXT NOT NULL ,"
			. "`cust_email_body` TEXT NOT NULL ,"
			. "`shop_email_subject` TEXT NOT NULL ,"
			. "`shop_email_body` TEXT NOT NULL ,"
			. "PRIMARY KEY (`pm_id`)"
			. " )";
	$database->query($mod_bakery);



	// Set default values for general settings
	$shop_name  = str_replace('http://', '', WB_URL);
	$tac_url    = WB_URL.PAGES_DIRECTORY.'/';
	$shop_email = SERVER_EMAIL;
	// Include default EU tax zone file
	include(WB_PATH.'/modules/bakery/eu_tax_zone.php');
	// Insert default values into table general_settings 
	$database->query("INSERT INTO ".TABLE_PREFIX."mod_bakery_general_settings (shop_name, tac_url, shop_email, tax_group) VALUES ('$shop_name', '$tac_url', '$shop_email', '$tax_group')");



	// Insert info into the search table
	// Module query info
	$field_info = array();
	$field_info['page_id']       = 'page_id';
	$field_info['title']         = 'page_title';
	$field_info['link']          = 'link';
	$field_info['description']   = 'description';
	$field_info['modified_when'] = 'modified_when';
	$field_info['modified_by']   = 'modified_by';
	$field_info = serialize($field_info);
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('module', 'bakery', '$field_info')");
	// Query start
	$query_start_code = "SELECT [TP]pages.page_id, [TP]pages.page_title, [TP]pages.link, [TP]pages.description, [TP]pages.modified_when, [TP]pages.modified_by FROM [TP]mod_bakery_items, [TP]mod_bakery_page_settings, [TP]pages WHERE ";
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_start', '$query_start_code', 'bakery')");
	// Query body
	$query_body_code = "
	[TP]pages.page_id = [TP]mod_bakery_items.page_id AND [TP]mod_bakery_items.title LIKE \'%[STRING]%\'
	OR [TP]pages.page_id = [TP]mod_bakery_items.page_id AND [TP]mod_bakery_items.sku LIKE \'%[STRING]%\'
	OR [TP]pages.page_id = [TP]mod_bakery_items.page_id AND [TP]mod_bakery_items.price LIKE \'%[STRING]%\'
	OR [TP]pages.page_id = [TP]mod_bakery_items.page_id AND [TP]mod_bakery_items.description LIKE \'%[STRING]%\'
	OR [TP]pages.page_id = [TP]mod_bakery_items.page_id AND [TP]mod_bakery_items.full_desc LIKE \'%[STRING]%\'";
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_body', '$query_body_code', 'bakery')");
	// Query end
	$query_end_code = '';	
	$database->query("INSERT INTO ".TABLE_PREFIX."search (name,value,extra) VALUES ('query_end', '$query_end_code', 'bakery')");
	
	// Insert blank row (there needs to be at least one row for the search to work)
	$database->query("INSERT INTO ".TABLE_PREFIX."mod_bakery_items (section_id,page_id) VALUES ('0', '0')");
	$database->query("INSERT INTO ".TABLE_PREFIX."mod_bakery_page_settings (section_id,page_id) VALUES ('0', '0')");

}
