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


//Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Database
global $database;


// Setup styles to help id errors
echo'
<style type="text/css">
.good {
	color: green;
}
.bad {
	color: red;
}
.ok {
	color: blue;
}
.warn {
	color: yellow;
}
</style>
';




// ****************************************
// BAKERY UPGRADE STARTING FROM VERSION 0.7 
// ****************************************

// Get new modul version from modul info file
$info_file = WB_PATH.'/modules/bakery/info.php';
if (file_exists($info_file)) {
	include($info_file);
}
$new_module_version = $module_version;

// Get old modul version from db
$sql            = "SELECT `version` FROM `".TABLE_PREFIX."addons` WHERE `directory` = 'bakery'";
$module_version = $database->get_one($sql);


// Version to be installed is the same or older than currently installed version
if ($module_version >= $new_module_version) {
	echo '<span class="bad">';
	$admin->print_error($MESSAGE['GENERIC_ALREADY_INSTALLED']);
	echo '</span><br />';
	return;
}




// UPGRADE TO VERSION 0.7 
// **********************

if ($module_version < 0.7) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.7:</h3>';

	// Get ITEMS table to see what needs to be created
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	$items = $itemstable->fetchRow();
	
	
	// Adding new fields to the existing ITEMS table
	echo'<b>Adding new fields to the items table</b><br />';
	
	if (!array_key_exists('option_attributes', $items)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `option_attributes` TEXT NOT NULL AFTER `shipping`")) {
				echo '<span class="good">Database field option_attributes added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field option_attributes exists, update not needed</span><br />'; }
	
	
	if (!array_key_exists('option_name', $items)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `option_name` VARCHAR(255) NOT NULL DEFAULT '' AFTER `shipping`")) {
				echo '<span class="good">Database field option_name added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field option_name exists, update not needed</span><br />'; }
		
	
	
	
	// Get CUSTOMER table to see what needs to be created
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$num_customers = $customertable->numRows();
	if ($num_customers == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_customer (order_id) VALUES ('0')");
	}
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$customer = $customertable->fetchRow();
	
	
	// Modifying fields or adding new fields to the existing CUSTOMER table
	echo'<b>Modifying fields and adding new fields to the customer table</b><br />';
	
	if (array_key_exists('cust_name', $customer)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` CHANGE `cust_name` `cust_first_name` VARCHAR(50)")) {
				echo '<span class="good">Changed database field cust_name to cust_first_name successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field cust_last_name exists, update not needed</span><br />'; }
	
	
	if (!array_key_exists('cust_last_name', $customer)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `cust_last_name` VARCHAR(50) AFTER `cust_first_name`")) {
				echo '<span class="good">Database field cust_last_name added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field cust_last_name exists, update not needed</span><br />'; }
	
	
	
	
	// Get ORDER table to see what needs to be created
	$ordertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_order`");
	$num_orders = $ordertable->numRows();
	if ($num_orders == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_order (order_id) VALUES ('0')");
	}
	$ordertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_order`");
	$order = $ordertable->fetchRow();
	
	
	// Adding new field to the existing ORDER table
	echo'<b>Adding new field to the order table</b><br />';
	
	if (!array_key_exists('attribute', $order)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_order` ADD `attribute` VARCHAR(50) NOT NULL AFTER `item_id`")) {
				echo '<span class="good">Database field attribute added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field attribute exists, update not needed</span><br />'; }
	
	
	
	
	// Get SETTINGS table to see what needs to be created
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		// Adding new fields to the existing SETTINGS table
		echo'<b>Adding new fields to the settings table</b><br />';
		
		if (!array_key_exists('offline_text', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` ADD `offline_text` TINYTEXT NOT NULL AFTER `page_id`")) {
					echo '<span class="good">Database field offline_text added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field offline_text exists, update not needed</span><br />'; }
		
		
		if (!array_key_exists('page_offline', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` ADD `page_offline` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `page_id`")) {
					echo '<span class="good">Database field page_offline added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field page_offline exists, update not needed</span><br />'; }
				
		
		if (array_key_exists('shop_url', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` CHANGE `shop_url` `proceed_url` VARCHAR(255) NOT NULL")) {
					echo '<span class="good">Changed database field shop_url to proceed_url successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field proceed_url exists, update not needed</span><br />'; }
				
		
		if (array_key_exists('paypal_return', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` DROP `paypal_return`")) {
					echo '<span class="good">Database field paypal_return deleted successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field paypal_return does not exist, update not needed</span><br />'; }
		
		
		if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` CHANGE `shipping_method` `shipping_method` VARCHAR(20) NOT NULL")) {
			echo '<span class="good">Database field shipping_method changed successfully</span><br />';
		} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		
		
		if (!array_key_exists('free_shipping_msg', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` ADD `free_shipping_msg` ENUM('show','hide') NOT NULL DEFAULT 'hide' AFTER `shipping_method`")) {
					echo '<span class="good">Database field free_shipping_msg added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field free_shipping_msg exists, update not needed</span><br />'; }
		
		
		if (!array_key_exists('free_shipping', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_settings` ADD `free_shipping` DECIMAL(6,2) NOT NULL AFTER `shipping_method`")) {
					echo '<span class="good">Database field free_shipping added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field free_shipping exists, update not needed</span><br />'; }
		
	} else {
		echo '<span class="warn">Database settings table does not exist, update not needed</span><br />';
		}
	
	echo"<br />";
	
	
	
	
	// Separat settings table into a general settings table and page settings table
	
	// Add new general settings table to the database
	echo'<b>Adding new general settings table to the database</b><br />';
	
	// Create new GENERAL SETTINGS table
	$mod_bakery = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bakery_general_settings` ( '
			. '`shop_id` INT NOT NULL DEFAULT \'0\','
			. '`shop_name` VARCHAR(100) NOT NULL ,'
			. '`use_captcha` ENUM(\'yes\',\'no\') NOT NULL ,'
			. '`tac_url` VARCHAR(255) NOT NULL ,'
			. '`shop_email` VARCHAR(50) NOT NULL ,'
			. '`shop_country` VARCHAR(2) NOT NULL ,'
			. '`shop_currency` VARCHAR(3) NOT NULL ,'
			. '`bank_account` TEXT NOT NULL ,'
			. '`paypal_email` VARCHAR(50) NOT NULL ,'
			. '`paypal_page` VARCHAR(255) NOT NULL ,'
			. '`payment_method` VARCHAR(20) NOT NULL ,'
			. '`tax_rate` DECIMAL(5,3) NOT NULL ,'
			. '`tax_included` ENUM(\'included\',\'excluded\') NOT NULL ,'
			. '`shipping_domestic` DECIMAL(6,2) NOT NULL ,'
			. '`shipping_abroad` DECIMAL(6,2) NOT NULL ,'
			. '`shipping_method` VARCHAR(20) NOT NULL ,'
			. '`free_shipping` DECIMAL(6,2) NOT NULL ,'
			. '`free_shipping_msg` ENUM(\'show\',\'hide\') NOT NULL ,'
			. '`email_subject_advance` TEXT NOT NULL ,'
			. '`email_pay_advance` TEXT NOT NULL ,'
			. '`email_subject_paypal` TEXT NOT NULL ,'
			. '`email_paypal` TEXT NOT NULL ,'
			. 'PRIMARY KEY (shop_id)'
			. ' )';
	if ($database->query($mod_bakery)) {
		echo '<span class="good">Created new general_settings table successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
	
	// Get "old" settings to insert them into the new general_settings table
	if ($settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_settings` ORDER BY section_id DESC LIMIT 1")) {
		$settings = $settingstable->fetchRow();
		if ($settings['section_id'] == '') {
			echo '<span class="warn">No old settings in database to insert into general_settings table</span><br />';
		}
		else {
		echo '<span class="good">Got old settings from database section_id='.$settings['section_id'].' successfully</span><br />';
		}
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
	
	// Set default general_settings 
	$shop_id = 0;
	$shop_name = $settings['shop_name'];
	$use_captcha = $settings['use_captcha'];
	$tac_url = $settings['tac_url'];
	$shop_email = $settings['shop_email'];
	$shop_country = $settings['shop_country'];
	$shop_currency = $settings['shop_currency'];
	$bank_account = $settings['bank_account'];
	$paypal_email = $settings['paypal_email'];
	$paypal_page = $settings['paypal_page'];
	$payment_method = "all";
	$tax_rate = $settings['tax_rate'];
	$tax_included = $settings['tax_included'];
	$shipping_domestic = $settings['shipping_domestic'];
	$shipping_abroad = $settings['shipping_abroad'];
	if ($settings['shipping_method'] == '') {$shipping_method = "flat"; } else {$shipping_method = $settings['shipping_method']; }
	if ($settings['free_shipping'] == '') {$settings['free_shipping'] = 0; } else {$free_shipping = $settings['free_shipping']; }
	if ($settings['free_shipping_msg'] == '') {$settings['free_shipping_msg'] = "all"; } else {$free_shipping_msg = $settings['free_shipping_msg']; }
	$email_subject_advance = $settings['email_subject_advance'];
	$email_pay_advance = $settings['email_pay_advance'];
	$email_subject_paypal = $settings['email_subject_paypal'];
	$email_paypal = $settings['email_paypal'];
	
	// Insert values into general_settings table 
	if ($database->query("INSERT INTO ".TABLE_PREFIX."mod_bakery_general_settings (shop_id, shop_name, use_captcha, tac_url, shop_email, shop_country, shop_currency, bank_account, paypal_email, paypal_page, payment_method, tax_rate, tax_included, shipping_domestic, shipping_abroad, shipping_method, free_shipping, free_shipping_msg, email_subject_advance, email_pay_advance, email_subject_paypal, email_paypal)
	VALUES ('$shop_id', '$shop_name', '$use_captcha', '$tac_url', '$shop_email', '$shop_country', '$shop_currency', '$bank_account', '$paypal_email', '$paypal_page', '$payment_method', '$tax_rate', '$tax_included', '$shipping_domestic', '$shipping_abroad', '$shipping_method', '$free_shipping', '$free_shipping_msg', '$email_subject_advance', '$email_pay_advance', '$email_subject_paypal', '$email_paypal')")) {
		echo '<span class="good">Added default settings into general_settings table successfully</span><br />';
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
	
	
	
	
	// Insert default settings
	
	// Set default page_settings 
	if ($settings['page_offline'] == '') { $page_offline = "no"; } else {$page_offline = $settings['page_offline']; }
	if ($settings['offline_text'] == '') {
		if (LANGUAGE_LOADED) {
			include(WB_PATH.'/modules/bakery/languages/EN.php');
			if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
				include(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
			}
		}
		$offline_text  = $MOD_BAKERY['ERR_OFFLINE_TEXT'];
	}
	else {
		$offline_text = $settings['offline_text'];
	}
	
	// Adding default settings to the new fields
	$query_dates = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_settings WHERE section_id != 0 and page_id != 0");
	while ($result = $query_dates->fetchRow()) {
	
		echo '<br /><b>Adding default settings to database for bakery section_id='.$result['section_id'].'</b><br />';
		$section_id = $result['section_id'];
	
		if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_settings` SET `page_offline` = '$page_offline' WHERE `section_id` = $section_id")) {
			echo '<span class="good">Database data page_offline added successfully</span><br />';
		}
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	
			
		if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_settings` SET `offline_text` = '$offline_text' WHERE `section_id` = $section_id")) {
			echo '<span class="good">Database data offline_text added successfully</span><br />';
		}
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
	
	echo"<br />";
	
	
	
	
	// Rename settings table to page_settings table
	echo'<b>Renaming settings table to page_settings table</b><br />';
	
	if ($database->query("RENAME TABLE `".TABLE_PREFIX."mod_bakery_settings` TO `".TABLE_PREFIX."mod_bakery_page_settings`")) {
		echo '<span class="good">Renamed settings table to page_settings table successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
	
	// Delete all fields which have been moved to general_settings table
	if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_page_settings` DROP `shop_name`, DROP `tac_url`, DROP `use_captcha`, DROP `shop_email`, DROP `shop_country`, DROP `shop_currency`, DROP `bank_account`, DROP `paypal_email`, DROP `paypal_page`, DROP `tax_rate`, DROP `tax_included`, DROP `shipping_domestic`, DROP `shipping_abroad`, DROP `shipping_method`, DROP `free_shipping`, DROP `free_shipping_msg`, DROP `email_subject_advance`, DROP `email_pay_advance`, DROP `email_subject_paypal`, DROP `email_paypal`")) {
		echo '<span class="good">Deleted all fields of page_settings (which have been created newly in the general_settings table) successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
}




// UPGRADE TO VERSION 0.8 
// **********************

if ($module_version < 0.8) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.8:</h3>';

	// Get ITEMS table to see what needs to be created
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	$items = $itemstable->fetchRow();
	
	
	// Adding new fields to the existing ITEMS table
	echo'<b>Adding new field to the items table</b><br />';
	
	if (!array_key_exists('tax_rate', $items)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `tax_rate` DECIMAL(5,3) NOT NULL AFTER `shipping`")) {
				echo '<span class="good">Database field tax_rate added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field tax_rate exists, update not needed</span><br />'; }		
	
	
	
	// Get CUSTOMER table to see what needs to be created
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$num_customers = $customertable->numRows();
	if ($num_customers == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_customer (order_id) VALUES ('0')");
	}
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$customer = $customertable->fetchRow();
	
	
	// Modifying fields or adding new fields to the existing CUSTOMER table
	echo'<b>Adding new fields to the customer table</b><br />';
	
	if (!array_key_exists('user_id', $customer)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `user_id` INT(6) NOT NULL AFTER `submitted`")) {
				echo '<span class="good">Database field user_id added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field user_id exists, update not needed</span><br />'; }


	if (!array_key_exists('cust_state', $customer)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `cust_state` VARCHAR(50) NOT NULL AFTER `cust_city`")) {
				echo '<span class="good">Database field cust_state added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field cust_state exists, update not needed</span><br />'; }
	
	
	
	// Get ORDER table to see what needs to be created
	$ordertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_order`");
	$num_orders = $ordertable->numRows();
	if ($num_orders == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_order (order_id) VALUES ('0')");
	}
	$ordertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_order`");
	$order = $ordertable->fetchRow();
	
	
	// Adding new field to the existing ORDER table
	echo'<b>Adding new field to the order table</b><br />';
	
	if (!array_key_exists('tax_rate', $order)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_order` ADD `tax_rate` DECIMAL(5,3) NOT NULL AFTER `price`")) {
				echo '<span class="good">Database field attribute added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field tax_rate exists, update not needed</span><br />'; }
	
	
	
	
	// Get GENERAL SETTINGS table to see what needs to be created
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	$settings = $settingstable->fetchRow();

	// Adding new fields to the existing SETTINGS table
	echo'<b>Adding new fields to the general_settings table</b><br />';
	
	if (!array_key_exists('zip_location', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `zip_location` ENUM('inside','end') NOT NULL DEFAULT 'inside' AFTER `shop_country`")) {
				echo '<span class="good">Database field zip_location added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field zip_location exists, update not needed</span><br />'; }
	
	
	if (!array_key_exists('state_field', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `state_field` ENUM('show','hide') NOT NULL DEFAULT 'hide' AFTER `shop_country`")) {
				echo '<span class="good">Database field state_field added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field state_field exists, update not needed</span><br />'; }
	
	
	if (!array_key_exists('tax_rate2', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `tax_rate2` DECIMAL(5,3) NOT NULL AFTER `tax_rate`")) {
				echo '<span class="good">Database field tax_rate2 added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field tax_rate2 exists, update not needed</span><br />'; }


	if (!array_key_exists('tax_rate1', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `tax_rate1` DECIMAL(5,3) NOT NULL AFTER `tax_rate`")) {
				echo '<span class="good">Database field tax_rate1 added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field tax_rate1 exists, update not needed</span><br />'; }


	if (!array_key_exists('zone_countries', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `zone_countries` TEXT NOT NULL AFTER `shipping_abroad`")) {
				echo '<span class="good">Database field zone_countries added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field zone_countries exists, update not needed</span><br />'; }


	if (!array_key_exists('shipping_zone', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `shipping_zone` DECIMAL(6,2) NOT NULL AFTER `shipping_abroad`")) {
				echo '<span class="good">Database field shipping_zone added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field shipping_zone exists, update not needed</span><br />'; }

	echo"<br />";
	
	
	// Insert default settings into items table

	// Get "old" settings
	if ($settingstable = $database->query("SELECT tax_rate FROM `".TABLE_PREFIX."mod_bakery_general_settings`")) {
		$settings = $settingstable->fetchRow();
		if ($settings['tax_rate'] == '') {
			echo '<span class="warn">No old tax_rate setting in database to insert into items table</span><br />';
		}
		else {
			echo '<span class="good">Got old tax_rate setting (<b>'.$settings['tax_rate'].'%</b>) from database to insert into items table</span><br />';
		}
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}

	// Insert values into general_settings table 
	if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_items` SET `tax_rate` = '$tax_rate'")) {
		echo '<span class="good">Added default tax_rate setting into items table successfully</span><br />';
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
}

	

	
// UPGRADE TO VERSION 0.8.1 
// ************************

if ($module_version < 0.81) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.8.1:</h3>';
	
	// Get SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_page_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (array_key_exists('proceed_url', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_page_settings` CHANGE `proceed_url` `continue_url` VARCHAR(255) NOT NULL")) {
					echo '<span class="good">Changed database field proceed_url to continue_url successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field continue_url exists, update not needed</span><br />'; }
		
	}
}

	

	
// UPGRADE TO VERSION 0.8.3
// ************************

if ($module_version < 0.83) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.8.3:</h3>';
	
	// Get SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('tax_rate_shipping', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `tax_rate_shipping` DECIMAL(5,3) NOT NULL AFTER `tax_included`")) {
					echo '<span class="good">Database field tax_rate_shipping added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field tax_rate_shipping exists, update not needed</span><br />'; }
		
	}
}

	

	
// UPGRADE TO VERSION 0.9
// ************************

if ($module_version < 0.9) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.9:</h3>';



	// Add new options table to the database
	echo'<b>Adding new options table to the database</b><br />';
	
	// Create new OPTIONS table
	$mod_bakery = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bakery_options` ( '
			. ' `option_id` INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY, '
			. ' `option_name` VARCHAR(50) NOT NULL'
			. ' )';
	if ($database->query($mod_bakery)) {
		echo '<span class="good">Created new options table successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}


	// Add new attributes table to the database
	echo'<b>Adding new attributes table to the database</b><br />';
	
	// Create new ATTRIBUTES table
	$mod_bakery = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bakery_attributes` ( '
			. ' `attribute_id` INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY, '
			. ' `option_id` INT(6) NOT NULL, '
			. ' `attribute_name` VARCHAR(50) NOT NULL'
			. ' )';
	if ($database->query($mod_bakery)) {
		echo '<span class="good">Created new attributes table successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}


	// Add new items attributes table to the database
	echo'<b>Adding new item attributes table to the database</b><br />';
	
	// Create new ITEMS ATTRIBUTES table
	$mod_bakery = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bakery_item_attributes` ( '
			. ' `assign_id` INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY, '
			. ' `item_id` INT(6) NOT NULL, '
			. ' `option_id` INT(6) NOT NULL, '
			. ' `attribute_id` INT(6) NOT NULL, '
			. ' `price` DECIMAL(9,2) NOT NULL, '
			. ' `operator` VARCHAR(1) NOT NULL'
			. ' )';
	if ($database->query($mod_bakery)) {
		echo '<span class="good">Created new item attributes table successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}
	
	

	// There has to be at least one row in the ITEMS table - if not, insert blank row
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	$num_items = $itemstable->numRows();
	if ($num_items == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_items (item_id) VALUES ('0')");
	}

	// Get ITEMS table to see what needs to be added
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	if ($item = $itemstable->fetchRow()) {

		if (!array_key_exists('stock', $item)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `stock` VARCHAR(20) NOT NULL DEFAULT '' AFTER `sku`")) {
					echo '<span class="good">Database field stock added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field stock exists, update not needed</span><br />'; }
	}


	// There has to be at least one row in the CUSTOMER table - if not, insert blank row
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$num_customers = $customertable->numRows();
	if ($num_customers == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_customer (order_id) VALUES ('0')");
	}

	// Get CUSTOMER table to see what needs to be changed
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	if ($customer = $customertable->fetchRow()) {

		if (array_key_exists('submitted', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` CHANGE `submitted` `submitted` VARCHAR(3) NOT NULL")) {
					echo '<span class="good">Changed database field submitted to type VARCHAR(3) successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		}

		if (!array_key_exists('status', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `status` VARCHAR(20) NOT NULL DEFAULT 'none' AFTER `submitted`")) {
					echo '<span class="good">Database field status added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field status exists, update not needed</span><br />'; }
		
		if (!array_key_exists('invoice', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `invoice` TEXT NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field invoice added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field invoice exists, update not needed</span><br />'; }

		if (!array_key_exists('ship_zip', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_zip` VARCHAR(10) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_zip added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_zip exists, update not needed</span><br />'; }
		
		if (!array_key_exists('ship_country', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_country` VARCHAR(2) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_country added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_country exists, update not needed</span><br />'; }
		
		if (!array_key_exists('ship_state', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_state` VARCHAR(50) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_state added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_state exists, update not needed</span><br />'; }
	
		if (!array_key_exists('ship_city', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_city` VARCHAR(50) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_city added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_city exists, update not needed</span><br />'; }
	
		if (!array_key_exists('ship_street', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_street` VARCHAR(50) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_street added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_street exists, update not needed</span><br />'; }
	
		if (!array_key_exists('ship_last_name', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_last_name` VARCHAR(50) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_last_name added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_last_name exists, update not needed</span><br />'; }
	
		if (!array_key_exists('ship_first_name', $customer)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_first_name` VARCHAR(50) NOT NULL AFTER `cust_phone`")) {
					echo '<span class="good">Database field ship_first_name added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_first_name exists, update not needed</span><br />'; }
				
	}


	// There has to be at least one row in the ORDER table - if not, insert blank row
	$ordertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_order`");
	$num_order = $ordertable->numRows();
	if ($num_order == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_order (order_id) VALUES ('0')");
	}

	// Get ORDER table to see what needs to be added
	$ordertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_order`");
	if ($order = $ordertable->fetchRow()) {

		if (array_key_exists('attribute', $order)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_order` CHANGE `attribute` `attributes` VARCHAR(50) NOT NULL")) {
				echo '<span class="good">Changed database field attribute to attribute<b>s</b> successfully</span><br />';
			}
			else {
				echo '<span class="bad">'.$database->get_error().'</span><br />';
			}

			if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_order` SET `attributes` = ''")) {
				echo '<span class="good">Deleted outdated order attributes successfully</span><br />';
			}
			else {
				echo '<span class="bad">'.$database->get_error().'</span><br />';
			}
		}
	}


	// Get GENERAL SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('shipping_form', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `shipping_form` VARCHAR(10) NOT NULL AFTER `shop_country`")) {
					echo '<span class="good">Database field shipping_form added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field shipping_form exists, update not needed</span><br />'; }
		
		if (!array_key_exists('invoice', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `invoice` TEXT NOT NULL AFTER `email_paypal`")) {
					echo '<span class="good">Database field invoice added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field invoice exists, update not needed</span><br />'; }

		if (!array_key_exists('email_invoice', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `email_invoice` TEXT NOT NULL AFTER `email_paypal`")) {
					echo '<span class="good">Database field email_invoice added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field email_invoice exists, update not needed</span><br />'; }

		if (!array_key_exists('email_subject_invoice', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `email_subject_invoice` TEXT NOT NULL AFTER `email_paypal`")) {
					echo '<span class="good">Database field email_subject_invoice added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field email_subject_invoice exists, update not needed</span><br />'; }		

	}


	// Insert default settings into general settings table
	if (LANGUAGE_LOADED) {
		include(WB_PATH.'/modules/bakery/languages/EN.php');
    	include(WB_PATH.'/modules/bakery/payment_methods/invoice/languages/EN.php');
		if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
			include(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
		}
		$payment_method = "invoice";
		if (file_exists(WB_PATH.'/modules/bakery/payment_methods/invoice/languages/'.LANGUAGE.'.php')) {
			include(WB_PATH.'/modules/bakery/payment_methods/invoice/languages/'.LANGUAGE.'.php');
		}
	}

	$email_subject_invoice = $MOD_BAKERY[$payment_method]['EMAIL_SUBJECT_CUSTOMER'];
	$email_invoice = $MOD_BAKERY[$payment_method]['EMAIL_BODY_CUSTOMER'];
	$invoice = $admin->add_slashes($MOD_BAKERY[$payment_method]['INVOICE_TEMPLATE']);

	if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_general_settings` SET `email_subject_invoice` = '$email_subject_invoice', `email_invoice` = '$email_invoice', `invoice` = '$invoice'")) {
		echo '<span class="good">Added default general settings successfully</span><br />';
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}


	// General upgrade note
	echo '
<div style="margin: 15px 0; padding: 10px 10px 10px 60px; text-align: left; color: red; border: solid 1px red; background-color: #FFDCD9; background-image: url('.WB_URL.'/modules/bakery/images/information.gif); background-position: 15px 15px; background-repeat: no-repeat;">
	<p style="font-weight: bold;">IMPORTANT UPGRADE NOTE UPGRADING TO v0.9</p>
	<ul style="padding-left: 20px;">
		<li style="list-style: square;"><b>Stylesheet</b>: If you keep your current Bakery stylesheets, make sure you are changing the class names from mod<strong>e</strong>_ to mod_ (mod without &quot;<strong>e</strong>&quot;, eg. mod_bakery_anything_f).</li><br />
		<li style="list-style: square;"><b>Item options</b>: Due to a new system handling the item options you lost all your item options. Use your database backup to restore the options and attributes to their former condition.</li>
	</ul>
</div>
';
}




// UPGRADE TO VERSION 0.9.6
// ************************

if ($module_version < 0.96) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.9.6:</h3>';
	
	// Get SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('shop_state', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `shop_state` VARCHAR(2) NOT NULL AFTER `shop_country`")) {
					echo '<span class="good">Database field shop_state added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field shop_state exists, update not needed</span><br />'; }
		
		if (!array_key_exists('tax_by', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `tax_by` VARCHAR(10) NOT NULL AFTER `tax_included`")) {
					echo '<span class="good">Database field tax_by added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field tax_by exists, update not needed</span><br />'; }
	
		// Insert default settings into general settings table
		if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_general_settings` SET `tax_by` = 'country'")) {
			echo '<span class="good">Added default general settings successfully</span><br />';
		}
		else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
		}
	}
}

	

	
// UPGRADE TO VERSION 0.9.7
// ************************

if ($module_version < 0.97) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 0.9.7:</h3>';
	
	// Get SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		echo "<b>Modifying table general_settings:</b><br />";
		
		if (!array_key_exists('definable_field_0', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `definable_field_0` VARCHAR(50) NOT NULL AFTER `use_captcha`")) {
					echo '<span class="good">Database field definable_field_0 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field definable_field_0 exists, update not needed</span><br />'; }

		if (!array_key_exists('definable_field_1', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `definable_field_1` VARCHAR(50) NOT NULL AFTER `definable_field_0`")) {
					echo '<span class="good">Database field definable_field_1 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field definable_field_1 exists, update not needed</span><br />'; }
		
		if (!array_key_exists('definable_field_2', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `definable_field_2` VARCHAR(50) NOT NULL AFTER `definable_field_1`")) {
					echo '<span class="good">Database field definable_field_2 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field definable_field_2 exists, update not needed</span><br />'; }
		
		if (!array_key_exists('stock_mode', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `stock_mode` VARCHAR(10) NOT NULL AFTER `definable_field_2`")) {
					echo '<span class="good">Database field stock_mode added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field stock_mode exists, update not needed</span><br />'; }
		
		if (!array_key_exists('stock_limit', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `stock_limit` INT(3) NOT NULL AFTER `stock_mode`")) {
					echo '<span class="good">Database field stock_limit added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field stock_limit exists, update not needed</span><br />'; }
	}
	
	// Change continue_url to a link not containing domain name nor page directory
	echo "<br /><b>Modifying table page_settings:</b><br />";
	$settingstable = $database->query("SELECT section_id, continue_url FROM `".TABLE_PREFIX."mod_bakery_page_settings`");
	while ($settings = $settingstable->fetchRow()) {
		$section_id = $settings['section_id'];
		$continue_url = str_replace(array(WB_URL.PAGES_DIRECTORY, PAGE_EXTENSION), array('', ''), $settings['continue_url']);
		if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_page_settings SET continue_url = '$continue_url' WHERE section_id = '$section_id'")) {
			echo '<span class="good">Changed continue_url of section_id='.$section_id.' successfully to a link not containing domain name nor page directory</span><br />';
		} else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
			}
	}
	
	// Get ITEMS table to see what needs to be added
	echo "<br /><b>Modifying table items:</b><br />";
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	if ($item = $itemstable->fetchRow()) {

		if (!array_key_exists('definable_field_0', $item)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `definable_field_0` VARCHAR(150) NOT NULL AFTER `tax_rate`")) {
					echo '<span class="good">Database field definable_field_0 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field definable_field_0 exists, update not needed</span><br />'; }

		if (!array_key_exists('definable_field_1', $item)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `definable_field_1` VARCHAR(150) NOT NULL AFTER `definable_field_0`")) {
					echo '<span class="good">Database field definable_field_1 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field definable_field_1 exists, update not needed</span><br />'; }
		
		if (!array_key_exists('definable_field_2', $item)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `definable_field_2` VARCHAR(150) NOT NULL AFTER `definable_field_1`")) {
					echo '<span class="good">Database field definable_field_2 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field definable_field_2 exists, update not needed</span><br />'; }
		
	}
	
	// Change item link to a link not containing domain name nor page directory
	while ($item = $itemstable->fetchRow()) {
		$item_id = $item['item_id'];
		$link = str_replace(PAGES_DIRECTORY, '', $item['link']);
		if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_items SET link = '$link' WHERE item_id = '$item_id'")) {
			echo '<span class="good">Changed item link of item_id='.$item_id.' successfully to a link not containing domain name nor page directory</span><br />';
		} else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
			}
	}
	
}	




// UPGRADE TO VERSION 1.1
// **********************

if ($module_version < 1.1) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.1:</h3>';


	// Add new fields to the general settings table
	echo'<b>Adding new fields to the general_settings table</b><br />';
	
	// Get SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {

		if (!array_key_exists('display_settings', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `display_settings` ENUM('1','0') NOT NULL DEFAULT '1' AFTER `zip_location`")) {
					echo '<span class="good">Database field display_settings added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field display_settings exists, update not needed</span><br />'; }


		if (!array_key_exists('out_of_stock_orders', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `out_of_stock_orders` ENUM('1','0') NOT NULL DEFAULT '0' AFTER `stock_limit`")) {
					echo '<span class="good">Database field out_of_stock_orders added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field out_of_stock_orders exists, update not needed</span><br />'; }


		if (!array_key_exists('thousands_sep', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `thousands_sep` VARCHAR(1) NOT NULL DEFAULT '\'' AFTER `shop_currency`")) {
					echo '<span class="good">Database field thousands_sep added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field thousands_sep exists, update not needed</span><br />'; }


		if (!array_key_exists('dec_point', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `dec_point` VARCHAR(1) NOT NULL DEFAULT '.' AFTER `shop_currency`")) {
					echo '<span class="good">Database field dec_point added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field dec_point exists, update not needed</span><br />'; }


		if (!array_key_exists('skip_checkout', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `skip_checkout` ENUM('1','0') NOT NULL DEFAULT '0' AFTER `tax_included`")) {
					echo '<span class="good">Database field skip_checkout added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field skip_checkout exists, update not needed</span><br />'; }
	}


	// Add new payment methods table to the database
	echo'<br /><b>Adding new payment methods table to the database</b><br />';
	
	// Create new PAYMENT METHODS table
	$mod_bakery = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bakery_payment_methods` ( '
			. '`pm_id` INT(11) NOT NULL AUTO_INCREMENT ,'
			. '`active` INT(1) NOT NULL ,'
			. '`directory` VARCHAR(50) NOT NULL ,'
			. '`name` VARCHAR(50) NOT NULL ,'
			. '`version` VARCHAR(6) NOT NULL ,'
			. '`author` VARCHAR(50) NOT NULL ,'
			. '`requires` VARCHAR(6) NOT NULL ,'
			. '`field_1` VARCHAR(150) NOT NULL ,'
			. '`value_1` TEXT NOT NULL ,'
			. '`field_2` VARCHAR(150) NOT NULL ,'
			. '`value_2` TEXT NOT NULL ,'
			. '`field_3` VARCHAR(150) NOT NULL ,'
			. '`value_3` TEXT NOT NULL ,'
			. '`field_4` VARCHAR(150) NOT NULL ,'
			. '`value_4` TEXT NOT NULL ,'
			. '`field_5` VARCHAR(150) NOT NULL ,'
			. '`value_5` TEXT NOT NULL ,'
			. '`field_6` VARCHAR(150) NOT NULL ,'
			. '`value_6` TEXT NOT NULL ,'
			. '`cust_email_subject` TEXT NOT NULL ,'
			. '`cust_email_body` TEXT NOT NULL ,'
			. '`shop_email_subject` TEXT NOT NULL ,'
			. '`shop_email_body` TEXT NOT NULL ,'
			. 'PRIMARY KEY (`pm_id`)'
			. ' )';
	if ($database->query($mod_bakery)) {
		echo '<span class="good">Created new payment_methods table successfully</span><br />';
	}
	else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
	}


	// Add all avaiable payment_methods to the db
	echo'<br /><b>Adding all avaiable payment_methods to the database</b><br />';
	
	if (!file_exists(WB_PATH.'/modules/bakery/payment_methods/load.php')) {
		echo '<span class="bad">File load.php is missing. Cannot create new database table payment_methods nor move the payment method settings. Please add your payment settings manually!</span><br />';
	} else {
		// Include payment methods loading file
		include(WB_PATH.'/modules/bakery/payment_methods/load.php');

		// Get "old" general settings
		if ($settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`")) {
			$settings = $settingstable->fetchRow();
			extract($settings);
			$moved_successfully = true;

			// Loop through the payment methods and overwrite default values that have been inserted by load.php
			foreach ($load_payment_methods as $payment_method) {
				switch ($payment_method) {
					case 'advance':
						if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_payment_methods SET cust_email_subject = '$email_subject_advance', cust_email_body = '$email_pay_advance' WHERE `directory` = 'advance'")) {
							echo '<span class="good">Moved advance payment method settings to the payment_methods table successfully</span><br />';
						} else {
							echo '<span class="bad">'.$database->get_error().'</span><br />';
							$moved_successfully = false;
						}
					break;
					case 'invoice':
						if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_payment_methods SET value_1 = '$bank_account', value_4 = '$invoice', cust_email_subject = '$email_subject_invoice', cust_email_body = '$email_invoice' WHERE `directory` = 'invoice'")) {
							echo '<span class="good">Moved invoice payment method settings to the payment_methods table successfully</span><br />';
						} else {
							echo '<span class="bad">'.$database->get_error().'</span><br />';
							$moved_successfully = false;
						}
					break;
					case 'paypal':
					if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_payment_methods SET value_1 = '$paypal_email', value_2 = '$paypal_page', cust_email_subject = '$email_subject_paypal', cust_email_body = '$email_paypal' WHERE `directory` = 'paypal'")) {
						echo '<span class="good">Moved PayPal payment method settings to the payment_methods table successfully</span><br />';
					} else {
						echo '<span class="bad">'.$database->get_error().'</span><br />';
						$moved_successfully = false;
					}
					break;
				}
			}
		}
	}	


	// Delete all fields which have been moved to the payment_methods table
	echo'<br /><b>Deleting all fields which have been moved to the payment methods table</b><br />';
	
	if ($moved_successfully) {
		if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` DROP `payment_method`, DROP `bank_account`, DROP `paypal_email`, DROP `paypal_page`, DROP `email_subject_advance`, DROP `email_pay_advance`, DROP `email_subject_paypal`, DROP `email_paypal`, DROP `email_subject_invoice`, DROP `email_invoice`, DROP `invoice`")) {
			echo '<span class="good">Deleted all fields of general_settings (that have been moved to the payment_methods table) successfully</span><br />';
		}
		else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
		}
	} else {
		echo '<span class="bad">Did not drop general_settings fields since they have not been moved to the payment_methods table</span><br />';
	}


	// Modifying fields or adding new fields to the existing CUSTOMER table
	echo'<b>Modifying fields and adding new fields to the customer table</b><br />';

	// Get CUSTOMER table to see what needs to be created
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$num_customers = $customertable->numRows();
	if ($num_customers == 0) {
		$database->query("INSERT INTO " .TABLE_PREFIX."mod_bakery_customer (order_id) VALUES ('0')");
	}
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	$customer = $customertable->fetchRow();

	if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` CHANGE `submitted` `submitted` VARCHAR(20) NOT NULL DEFAULT 'no'")) {
		echo '<span class="good">Changed database field submitted to type VARCHAR(20) successfully</span><br />';
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}

	if (!array_key_exists('transaction_status', $customer)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `transaction_status` VARCHAR(10) NOT NULL DEFAULT 'none' AFTER `submitted`")) {
				echo '<span class="good">Database field transaction_status added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field transaction_status exists, update not needed</span><br />'; }

	if (!array_key_exists('transaction_id', $customer)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `transaction_id` VARCHAR(50) NOT NULL DEFAULT 'none' AFTER `submitted`")) {
				echo '<span class="good">Database field transaction_id added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field transaction_id exists, update not needed</span><br />'; }


	// Change payment method abbreviations
	echo'<br /><b>Changing all payment method abbreviations to full length words</b><br />';
	
	// Change payment method abbreviations to full length words
	$payment_methods = array("adv"=>"advance", "inv"=>"invoice", "pp"=>"paypal");
	foreach ($payment_methods as $abbr => $payment_method) {
		if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_customer` SET `submitted` = '$payment_method' WHERE `submitted` = '$abbr'")) {
			echo '<span class="good">Changed payment method abbreviation <b>'.$abbr.'</b> to full length word <b>'.$payment_method.'</b> successfully</span><br />';
		} else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
		}
	}
}




// UPGRADE TO VERSION 1.3
// **********************

if ($module_version < 1.3) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.3:</h3>';

	// Get ITEMS table to see what needs to be created
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	$items = $itemstable->fetchRow();
	
		if (!array_key_exists('main_image', $items)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` CHANGE `extension` `main_image` VARCHAR(50) NOT NULL")) {
					echo '<span class="good">Changed database field extension to main_image successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field main_image exists, update not needed</span><br />'; }
	

	// Get SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_page_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('lightbox2', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_page_settings` ADD `lightbox2` VARCHAR(10) NOT NULL DEFAULT 'detail' AFTER `resize`")) {
					echo '<span class="good">Database field lightbox2 added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field lightbox2 exists, update not needed</span><br />'; }

		if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_page_settings` SET `resize` = '100' WHERE resize = 0")) {
			echo '<span class="good">Changed thumbnail resize to 100x100px successfully</span><br />';
		} else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
		}
	}


	// General upgrade note
	echo '
<div style="margin: 15px 0; padding: 10px 10px 10px 60px; text-align: left; color: red; border: solid 1px red; background-color: #FFDCD9; background-image: url('.WB_URL.'/modules/bakery/images/information.gif); background-position: 15px 15px; background-repeat: no-repeat;">
	<p style="font-weight: bold;">IMPORTANT UPGRADE NOTE UPGRADING TO v1.3</p>
	<ul style="padding-left: 20px;">
		<li style="list-style: square;"><b>Item images</b>: Due to a new way how Bakery handles and stores images you will have to reupload <b>ALL</b> item images using the Bakery backend. Use your backup of the <code>/media/bakery</code> directory. Use speaking image file names since they are used for the image <code>&lt;alt&gt;<code> and </code>&lt;title&gt;</code> tag and shown as the Lightbox2 caption.</li><br />
		<li style="list-style: square;"><b>Item templates</b>: Use the vars [THUMB], [THUMBS], [IMAGE] and [IMAGES] to display images. Depending on your page settings the images will be linked automatically to the detail page or overlay on the current page using Lightbox2. So there is no more need to link the image in your template.</li>
	</ul>
</div>
';
}




// UPGRADE TO VERSION 1.4.0
// ************************

if ($module_version < 1.40) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.4.0:</h3>';

	// Change PAYMENT METHODS table
	if ($database->query("UPDATE `".TABLE_PREFIX."mod_bakery_payment_methods` SET `value_4` = `value_2`, `field_4` = `field_2`, `value_2` = '', `field_2` = '' WHERE `directory` = 'invoice' AND `version` = '0.1' LIMIT 1")) {
		echo '<span class="good">Changed database table payment methods successfully</span><br />';
	}
	else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}


	// Get GENERAL SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('skip_cart', $settings)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `skip_cart` ENUM('yes',  'no') NOT NULL DEFAULT 'no' AFTER `zip_location`")) {
					echo '<span class="good">Database field skip_cart added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field skip_cart exists, update not needed</span><br />'; }
	}


	// Convert continue_url from string link to numeric page_id
	if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_page_settings ps SET ps.continue_url = (SELECT p.page_id FROM ".TABLE_PREFIX."pages p WHERE ps.continue_url = p.link) WHERE LEFT(`continue_url`, 1) = '/'")) {
		echo '<span class="good">Converted continue_url from string link to numeric page_id successfully</span><br />';
	} else {
		echo '<span class="bad">'.$database->get_error().'</span><br />';
	}


	// Replace all submit button names "cart" by new ones "view_cart" or "add_to_cart" at all page templates
	$display_warning = false;
	$replace_counter = 0;
	$query_page_settings = $database->query("SELECT section_id, header, item_loop, footer, item_header, item_footer FROM ".TABLE_PREFIX."mod_bakery_page_settings");
	if ($query_page_settings->numRows() > 0) {
		while ($page_settings = $query_page_settings->fetchRow()) {
			$page_settings = array_map('stripslashes', $page_settings);
			foreach ($page_settings as $template_name => $template_html) {
				if ($template_name != 'section_id' && !is_numeric($template_name)) {
					if ($template_name == 'header' || $template_name == 'footer') {
						$template_html = str_replace('name="cart"', 'name="view_cart"', $template_html);
						$updates[] = "$template_name = '$template_html'";
					} else {
						$template_html = str_replace('name="cart"', 'name="add_to_cart"', $template_html);
						$updates[] = "$template_name = '$template_html'";
					}
				}
			}
			$update_string = implode($updates,",");
			if ($database->query("UPDATE ".TABLE_PREFIX."mod_bakery_page_settings SET $update_string WHERE section_id = '{$page_settings['section_id']}'")) {
				if ($replace_counter == 0) {
					echo "<span class='good'>Replaced all submit buttons named &quot;cart&quot; by &quot;view_cart&quot; or &quot;add_to_cart&quot;&hellip;</span><br />";
				}
				echo "<span class='good'> &ndash; in all page templates with section id {$page_settings['section_id']} successfully</span><br />";
				$display_warning = true;
				$replace_counter++;
			} else {
				echo '<span class="bad">'.$database->get_error().'</span><br />';
			}
		}
		// Upgrade note
		if ($display_warning) {
			echo '
<div style="margin: 15px 0; padding: 10px 10px 10px 60px; text-align: left; color: red; border: solid 1px red; background-color: #FFDCD9; background-image: url('.WB_URL.'/modules/bakery/images/information.gif); background-position: 15px 25px; background-repeat: no-repeat;">
	<p style="font-weight: bold;">IMPORTANT UPGRADE NOTES UPGRADING TO v1.4.0</p>
	<p style="font-weight: bold;">Due to the new delivery notes printing feature you have to modify your invoice template slightly.</p>
	<ol>
	  <li style="list-style: decimal;">Go to &quot;Payment Methods&quot; &gt; select &quot;Invoice&quot; &gt; &quot;Invoice Template&quot;.</li>
	  <li style="list-style: decimal;">Replace the placeholder [INVOICE_OR_DUNNING] by [TITLE].</li>
	  <li style="list-style: decimal;">Replace some lines of html code at the bottom of the invoice template by the appropriate ones. See example code at the <a href="http://www.bakery-shop.ch/#upgrade_note_140" target="_blank">Bakery website</a>.
	  <br />Use the help page at &quot;Payment Methods&quot; &gt; select &quot;Invoice&quot; &gt; &quot;Invoice Template&quot; &gt; &quot;Help&quot; to get information on the new placeholders [DISPLAY_INVOICE], [DISPLAY_DELIVERY_NOTE] and [DISPLAY_REMINDER].</li>
	</ol>
	<p style="font-weight: bold;">If your shop frontend is not working as expected please check all of your page templates manually.</p>
	<ol>
		<li style="list-style: decimal;">Select a Bakery page and go to &quot;Page Settings&quot; &gt; &quot;Layout Settings&quot;.</li>
		<li style="list-style: decimal;">Make sure all submit buttons formerly named <code>name=&quot;cart&quot;</code> have been replaced correctly by the upgrade script:</li>
		<ul style="padding-left: 20px;">
			<li style="list-style: square;">Set <code>name=&quot;view_cart&quot;</code> for submit buttons that jump to the cart view.</li>
			<li style="list-style: square;">Set <code>name=&quot;add_to_cart&quot;</code> for submit buttons that add items to the cart.</li>
		</ul>
		<li style="list-style: decimal;">Repeat for all other Bakery pages.</li>
	</ol>
</div>
';
		}
	}
}




// UPGRADE TO VERSION 1.5.1
// ************************

if ($module_version < 1.51) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.5.1:</h3>';

	// Get GENERAL SETTINGS table to see what needs to be created
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	$settings = $settingstable->fetchRow();

	// Adding new fields to the existing SETTINGS table
	echo'<b>Adding new field to the general_settings table</b><br />';
	
	if (!array_key_exists('pages_directory', $settings)){
			if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `pages_directory` VARCHAR(20) NOT NULL DEFAULT 'bakery' AFTER `shop_email`")) {
				echo '<span class="good">Database field pages_directory added successfully</span><br />';
			} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
	} else { echo '<span class="ok">Database field pages_directory exists, update not needed</span><br />'; }

}




// UPGRADE TO VERSION 1.5.5
// ************************

if ($module_version < 1.55) {
	echo '
<div style="margin: 15px 0; padding: 10px 10px 10px 60px; text-align: left; color: red; border: solid 1px red; background-color: #FFDCD9; background-image: url('.WB_URL.'/modules/bakery/images/information.gif); background-position: 15px 25px; background-repeat: no-repeat;">
	<p style="font-weight: bold;">IMPORTANT UPGRADE NOTES UPGRADING TO BAKERY v1.5.5</p>
	<p style="font-weight: bold;">This upgrade note only concerns users of the DIRECTebanking.com / sofort&uuml;berweisung.de payment method.</p>
	<p style="padding: 5px; border: 1px solid red;">As a new  payment method security feature <strong>DIRECTebanking.com</strong> / <strong>sofort&uuml;berweisung.de</strong> now supports a notification password. This password is used to verify the HTTP response notifications. All users  of this payment method  have to add a notification password otherwise the payment method will not work properly any more. </p>
	<p>In order to set the notification password</span> follow the steps below:</p>
	<ol>
	  <li style="list-style: decimal;"><a href="https://www.sofortueberweisung.de/payment/users/login" target="_blank">Log in</a> to your DIRECTebanking.com / sofort&uuml;berweisung.de account.</li>
	  <li style="list-style: decimal;">Go to &quot;My projects&quot; &gt; select a project &gt; &quot;Extended settings&quot; &gt; &quot;Passwords and hash algorithm&quot;</li>
	  <li style="list-style: decimal;">Set the notification password. Please note: As soon as the password is set, it can not be unset anymore.</li>
	  <li>Copy or write down the generated notification password for later use.</li>
	  <br />
	  <li style="list-style: decimal;">Log in to the Bakery backend.</li>
      <li>Add the DIRECTebanking.com / sofort&uuml;berweisung.de  notification password at &quot;Payment Methods&quot; &gt; select &quot;DIRECTebanking.com / sofort&uuml;berweisung.de&quot; &gt; &quot;DIRECTebanking.com / sofort&uuml;berweisung.de Settings&quot;.</li>
	</ol>
</div>
';
}




// UPGRADE TO VERSION 1.6
// **********************

if ($module_version < 1.60) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.6.0:</h3>';

	// Get COSTUMER table to see what needs to be changed
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	if ($costumer = $customertable->fetchRow()) {
	
		if (!array_key_exists('cust_tax_no', $costumer)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `cust_tax_no` VARCHAR(11) NOT NULL AFTER `cust_last_name`;")) {
					echo '<span class="good">Database field cust_tax_no added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field cust_tax_no exists, update not needed</span><br />'; }
	}


	// Get GENERAL SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('tax_no_field', $settings)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `tax_no_field` ENUM('show', 'hide') NOT NULL AFTER `state_field`")) {
					echo '<span class="good">Database field tax_no_field added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field tax_no_field exists, update not needed</span><br />'; }

		// Include default EU tax zone file
		include(WB_PATH.'/modules/bakery/eu_tax_zone.php');

		if (!array_key_exists('tax_group', $settings)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `tax_group` VARCHAR(255) NOT NULL DEFAULT '$tax_group' AFTER `tax_no_field`")) {
					echo '<span class="good">Database field tax_group added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field tax_group exists, update not needed</span><br />'; }
	}
}




// UPGRADE TO VERSION 1.7
// **********************

if ($module_version < 1.70) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.7.0:</h3>';

	// Get COSTUMER table to see what needs to be changed
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	if ($costumer = $customertable->fetchRow()) {
	
		if (!array_key_exists('cust_company', $costumer)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `cust_company` VARCHAR(50) NOT NULL AFTER `user_id`;")) {
					echo '<span class="good">Database field cust_company added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field cust_company exists, update not needed</span><br />'; }
	
		if (!array_key_exists('ship_company', $costumer)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `ship_company` VARCHAR(50) NOT NULL AFTER `cust_phone`;")) {
					echo '<span class="good">Database field ship_company added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field ship_company exists, update not needed</span><br />'; }
	
		if (!array_key_exists('invoice_id', $costumer)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `invoice_id` INT(6) NOT NULL AFTER `ship_zip`;")) {
					echo '<span class="good">Database field invoice_id added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field invoice_id exists, update not needed</span><br />'; }
	}


	// Get GENERAL SETTINGS table to see what needs to be changed
	$settingstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_general_settings`");
	if ($settings = $settingstable->fetchRow()) {
	
		if (!array_key_exists('company_field', $settings)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `company_field` ENUM('show','hide') NOT NULL AFTER `shipping_form`")) {
					echo '<span class="good">Database field company_field added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field company_field exists, update not needed</span><br />'; }

		if (!array_key_exists('cust_msg', $settings)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` ADD `cust_msg` ENUM('show','hide') NOT NULL AFTER `zip_location`")) {
					echo '<span class="good">Database field cust_msg added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field cust_msg exists, update not needed</span><br />'; }
	
		if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` CHANGE `shop_state` `shop_state` VARCHAR(5) NOT NULL")) {
			echo '<span class="good">Changed database field shop_state to type VARCHAR(5) successfully</span><br />';
		} else {
			echo '<span class="bad">'.$database->get_error().'</span><br />';
		}

		if (array_key_exists('skip_checkout', $settings)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_general_settings` DROP `skip_checkout`")) {
					echo '<span class="good">Database field skip_checkout deleted successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field skip_checkout does not exist, update not needed</span><br />'; }

	}


	// Get ITEMS table to see what needs to be changed
	$itemstable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items`");
	if ($items = $itemstable->fetchRow()) {

		if (!array_key_exists('created_when', $items)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `created_when` INT NOT NULL DEFAULT '0' AFTER `modified_by`")) {
					echo '<span class="good">Database field created_when added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field created_when exists, update not needed</span><br />'; }

		if (!array_key_exists('created_by', $items)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` ADD `created_by` INT NOT NULL DEFAULT '0' AFTER `created_when`")) {
					echo '<span class="good">Database field created_by added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field created_by exists, update not needed</span><br />'; }
	
		if (array_key_exists('main_image', $items)){
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_items` DROP `main_image`")) {
					echo '<span class="good">Database field main_image deleted successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field main_image does not exist, update not needed</span><br />'; }

	}
}


// Add new images table to the database
echo'<b>Adding new images table to the database</b><br />';

// Create new GENERAL SETTINGS table
$mod_bakery = 'CREATE TABLE `'.TABLE_PREFIX.'mod_bakery_images` ( '
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
			. ' )';
if ($database->query($mod_bakery)) {
	echo '<span class="good">Created new images table successfully</span><br />';
}
else {
	echo '<span class="bad">'.$database->get_error().'</span><br />';
}


	echo '
<div style="margin: 15px 0; padding: 10px 10px 10px 60px; text-align: left; color: red; border: solid 1px red; background-color: #FFDCD9; background-image: url('.WB_URL.'/modules/bakery/images/information.gif); background-position: 15px 25px; background-repeat: no-repeat;">
	<p style="font-weight: bold;">IMPORTANT UPGRADE NOTES UPGRADING TO BAKERY v1.7.0</p>
	<p style="padding: 5px; border: 1px solid red;">This version features a big improvement in handling item images. It is now possible to reorder item images, add a title, alt attribute and even a image caption. The image on the top position will be used as main image.</p>
	<p>In order to set the image settings you have to <b>open each item</b> in the Bakery backend:</p>
	<ol>
	  <li style="list-style: decimal;">The database will then be synced automatically with the item images currently saved in the <code>/media/bakery/</code> directory.</li>
	  <li style="list-style: decimal;">Please enter your image data.</li>
	  <li style="list-style: decimal;">If you set a title and no alt attribute, the title will be copied and used for the alt attribute as well.</li>
	  <li style="list-style: decimal;">The alt attribute is mandatory.</li>
	  <li style="list-style: decimal;">The image at the top position (position 1) will be used as main image.</li>
	  <li style="list-style: decimal;">If you enter a image caption, the image will be wrapped in a &lt;div&gt; container and the image will be followed after a &lt;br&gt; by your caption.</li>
	  <li style="list-style: decimal;">The item attribut generates a unique id like <code>mod_bakery_img_attrXX_f</code> where the <code>XX</code> stands for the image attribute id. This can be used for any JavaScript actions depending on the selected option attribute. Eg. change main image depending on selected item option.</li>
	  <li style="list-style: decimal;">Not used images can be deactivated or deleted in the backend.</li>
	</ol> 
</div>
';




// UPGRADE TO VERSION 1.7.2
// ************************

if ($module_version < 1.72) {

	// Titel: Upgrading to
	echo'<h3>Upgrading to version 1.7.2:</h3>';

	// Get COSTUMER table to see what needs to be changed
	$customertable = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_customer`");
	if ($costumer = $customertable->fetchRow()) {
	
		if (!array_key_exists('sent_invoices', $costumer)) {
				if ($database->query("ALTER TABLE `".TABLE_PREFIX."mod_bakery_customer` ADD `sent_invoices` INT(1) NOT NULL DEFAULT '0' AFTER `invoice_id`;")) {
					echo '<span class="good">Database field sent_invoices added successfully</span><br />';
				} else { echo '<span class="bad">'.$database->get_error().'</span><br />'; }
		} else { echo '<span class="ok">Database field sent_invoices exists, update not needed</span><br />'; }
	}
}








// STOP FOR DEBUGGING - DISPLAY ERROR LOG
// **************************************
?>
<br /><br />
<div style="padding: 15px 10px; text-align: center; color: blue; border: solid 1px blue; background-color: #DCEAFE;">
	<p style="font-weight:bold;">Please check the upgrade log carefully. Save a copy for later use. Then click&hellip;</p>
	<form action="">
		<input type="button" value="OK" onclick="location.href='index.php'" style="width: 30%;">
	</form>
</div>
<?php

// Print admin footer
$admin->print_footer();
?>

<script language="javascript" type="text/javascript">
	stop();
</script>
