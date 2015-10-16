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

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');


// SHOW OVERVIEW PAGE
// ******************

// If requested include lightbox2 (css is appended to the frontend.css stylesheet)
if ($setting_lightbox2 == "overview" || $setting_lightbox2 == "all") {
	// Load jQuery if not loaded yet
	?>
	<script type="text/javascript">window.jQuery || document.write('<script src="<?php echo WB_URL; ?>/modules/bakery/jquery/jquery-1.7.2.min.js"><\/script>')</script>
	<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/bakery/lightbox2/js/lightbox.js"></script>
	<script type="text/javascript">
	//  Lightbox2 options
	$(function () {
	    var lightbox, options;
	    options = new LightboxOptions;

	    options.fileLoadingImage = '<?php echo WB_URL; ?>/modules/bakery/lightbox2/images/loading.gif';
	    options.fileCloseImage   = '<?php echo WB_URL; ?>/modules/bakery/lightbox2/images/close.png';
	    options.labelImage       = '<?php echo $MOD_BAKERY['TXT_IMAGE']; ?>';
	    options.labelOf          = '<?php echo $TEXT['OF']; ?>';

	    return lightbox          = new Lightbox(options);
	});
	</script>
	<?php
}


// Check if there is a start point defined for pagination
$position = isset($_GET['p']) && is_numeric($_GET['p']) && $_GET['p'] >= 0 ? $_GET['p'] : 0;
$_SESSION['bakery']['position'] = $position;

// Get total number of items
$query_total_num = $database->query("SELECT item_id FROM ".TABLE_PREFIX."mod_bakery_items WHERE section_id = '$section_id' AND active = '1' AND title != ''");
$total_num = $query_total_num->numRows();

// Work-out if we need to add limit code to sql
if ($setting_items_per_page != 0) {
	$limit_sql = " LIMIT $position, $setting_items_per_page";
} else {
	$limit_sql = '';
}

// Query items (for this page)
$query_items = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_items WHERE section_id = '$section_id' AND active = '1' AND title != '' ORDER BY position ASC".$limit_sql);
$num_items = $query_items->numRows();

// Create previous and next links for pagination
if ($setting_items_per_page > 0) {

	// Previous links
	if ($position > 0) {
		$pl_prepend         = '<a href="?p='.($position - $setting_items_per_page).'">&laquo; ';
		$pl_append          = '</a>';
		$previous_link      = $pl_prepend.$TEXT['PREVIOUS'].$pl_append;
		$previous_page_link = $pl_prepend.$TEXT['PREVIOUS_PAGE'].$pl_append;
	} else {
		$previous_link      = '';
		$previous_page_link = '';
	}

	// Next links
	if ($position + $setting_items_per_page >= $total_num) {
		$next_link      = '';
		$next_page_link = '';
	} else {
		$nl_prepend     = '<a href="?p='.($position + $setting_items_per_page).'"> ';
		$nl_append      = ' &raquo;</a>';
		$next_link      = $nl_prepend.$TEXT['NEXT'].$nl_append;
		$next_page_link = $nl_prepend.$TEXT['NEXT_PAGE'].$nl_append;
	}

	// Item position out of total items
	if ($position + $setting_items_per_page > $total_num) {
		$num_of = $position + $num_items;
	} else {
		$num_of = $position + $setting_items_per_page;
	}

	$item_num = $position + 1 == $num_of ? ($position + 1) : ($position + 1).'-'.$num_of;

	$out_of                      = $item_num.' '.strtolower($TEXT['OUT_OF']).' '.$total_num;
	$of                          = $item_num.' '.strtolower($TEXT['OF']).' '.$total_num;
	$display_previous_next_links = '';

// No pagination
} else {
	$display_previous_next_links = 'none';
}



// Print header
if ($display_previous_next_links == 'none') {
	echo  str_replace(array('[PAGE_TITLE]','[SHOP_URL]','[VIEW_CART]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,$setting_continue_url, $MOD_BAKERY['TXT_VIEW_CART'],'','','','','','', $display_previous_next_links, $MOD_BAKERY['TXT_ITEM']), $setting_header);
} else {
	echo str_replace(array('[PAGE_TITLE]','[SHOP_URL]','[VIEW_CART]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,$setting_continue_url, $MOD_BAKERY['TXT_VIEW_CART'], $next_page_link, $next_link, $previous_page_link, $previous_link, $out_of, $of, $display_previous_next_links, $MOD_BAKERY['TXT_ITEM']), $setting_header);
}


// Loop through and show items
if ($num_items > 0) {
	$counter = 0;
	while ($item = $query_items->fetchRow()) {
		$item_id   = stripslashes($item['item_id']);
		$title     = htmlspecialchars(stripslashes($item['title']));
		$price     = number_format(stripslashes($item['price']), 2, $setting_dec_point, $setting_thousands_sep);
		$uid       = $item['modified_by']; // User who last modified the item
		// Workout date and time of last modified item
		$item_date = gmdate(DATE_FORMAT, $item['modified_when']+TIMEZONE);
		$item_time = gmdate(TIME_FORMAT, $item['modified_when']+TIMEZONE);
		// Work-out the item link
		$item_link = WB_URL.PAGES_DIRECTORY.$item['link'].PAGE_EXTENSION;


		// Item thumb(s) and image(s)

		// Initialize or reset thumb(s) and image(s) befor laoding next item
		$thumb_arr = array();
		$image_arr = array();
		$thumb     = '';
		$image     = '';

		// Get image data from db
		$query_image = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_images WHERE `item_id` = '$item_id' AND `active` = '1' ORDER BY position ASC");
		if ($query_image->numRows() > 0) {
			while ($image = $query_image->fetchRow()) {
				$image          = array_map('stripslashes', $image);
				$image          = array_map('htmlspecialchars', $image);
				$img_id         = $image['img_id'];
				$item_attribute = $image['item_attribute_id'];
				$image_file     = $image['filename'];
				$img_alt        = $image['alt'];
				$img_title      = $image['title'];
				$img_caption    = $image['caption'];

				// Thumbs use .jpg extension only
				$thumb_file = str_replace(".png", ".jpg", $image_file);

				// Prepare thumb and image directory pathes and urls
				$thumb_dir = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.$item_id.'/';
				$img_dir   = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/images/item'.$item_id.'/';
				$thumb_url = WB_URL.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.$item_id.'/';
				$img_url   = WB_URL.MEDIA_DIRECTORY.'/'.$img_dir.'/images/item'.$item_id.'/';

				// Make array of all item thumbs and images
				if (file_exists($thumb_dir.$thumb_file) && file_exists($img_dir.$image_file)) {
					// If needed add lightbox2 link to the thumb/image...
					if ($setting_lightbox2 == "overview" || $setting_lightbox2 == "all") {
						$thumb_prepend = "<a href='".$img_url.$image_file."' rel='lightbox[image_".$item_id."]' title='".$img_title."'><img src='";
						$img_prepend   = "<a href='".$img_url.$image_file."' rel='lightbox[image_".$item_id."]' title='".$img_title."'><img src='";
						$thumb_append  = "' alt='".$img_alt."' title='".$img_title."' class='mod_bakery_main_thumb_f' /></a>";
						$img_append    = "' alt='".$img_alt."' title='".$img_title."' class='mod_bakery_main_img_f' /></a>";
					// ...else add thumb/image only
					} else {
						$thumb_prepend = "<a href='".$item_link."'><img src='";
						$img_prepend   = "<img src='";
						$thumb_append  = "' alt='".$img_alt."' title='".$img_title."' class='mod_bakery_main_thumb_f' /></a>";
						$img_append    = "' alt='".$img_alt."' title='".$img_title."' class='mod_bakery_main_img_f' />";
					}
					// Make array
					$thumb_arr[] = $thumb_prepend.$thumb_url.$thumb_file.$thumb_append;
					$image_arr[] = $img_prepend.$img_url.$image_file.$img_append;
				}
			}
		}
		// Main thumb/image (image position 1)
		$thumb = empty($thumb_arr[0]) ? '' : $thumb_arr[0];
		$image = empty($image_arr[0]) ? '' : $image_arr[0];
		unset($thumb_arr[0]);
		unset($image_arr[0]);

		// Make strings for use in the item templates
		$thumbs = implode("\n", $thumb_arr);
		$images = implode("\n", $image_arr);



		// Show item options and attributes if we have to
		
		// Initialize vars
		$option        = '';
		$option_select = '';
		
		// Get number of item options and loop through them
		$query_num_options = $database->query("SELECT DISTINCT o.option_name, ia.option_id FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_item_attributes ia ON o.option_id = ia.option_id WHERE ia.item_id = $item_id");			
		if ($query_num_options->numRows() > 0) {
			while ($num_options = $query_num_options->fetchRow()) {
				$option_name = stripslashes($num_options['option_name']);
				$option_id   = stripslashes($num_options['option_id']);

				// Get item attributes
				$query_attributes = $database->query("SELECT o.option_name, a.attribute_name, ia.attribute_id, ia.price, ia.operator FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_attributes a ON o.option_id = a.option_id INNER JOIN ".TABLE_PREFIX."mod_bakery_item_attributes ia ON a.attribute_id = ia.attribute_id WHERE item_id = $item_id AND ia.option_id = '$option_id' ORDER BY o.option_name, LENGTH(a.attribute_name), a.attribute_name ASC");
				if ($query_attributes->numRows() > 0) {
					$option_select .= $option_name.": <select name='attribute[]' class='mod_bakery_main_select_f'>"; 
					while ($attributes = $query_attributes->fetchRow()) {
						$attributes = array_map('stripslashes', $attributes);
						// Make attribute select
						$attributes['operator'] = $attributes['operator'] == "=" ? '' : $attributes['operator'];
						$ia_price = ", ".$setting_shop_currency." ".$attributes['operator'].$attributes['price'];
						$ia_price = $attributes['price'] == 0 ? '' : $ia_price;
						$option_select .= "<option value='{$attributes['attribute_id']}'>{$attributes['attribute_name']}$ia_price</option>\n";
					}
					$option_select .= "</select><br />";
					$option         = $option_select;
				}
			}
		}

		// Check if we should show number of items, stock image or "in stock" message or nothing at all
		$item_stock = stripslashes($item['stock']);
		// Only show if item stock is not blank
		if ($item_stock == '' && $setting_stock_mode != "none") {
			$stock = $MOD_BAKERY['TXT_N/A'];
		} else {
			// Display number of items
			if ($setting_stock_mode == "number") {
				if ($item_stock < 1) {
					$stock = 0;
				} else {
					$stock = $item_stock;
				}
			// Display stock image
			} elseif ($setting_stock_mode == "img" && is_numeric($setting_stock_limit) && $setting_stock_limit != '') {
				if ($item_stock < 1) {
					$stock = "<img src='".WB_URL."/modules/bakery/images/out_of_stock.gif' alt='".$MOD_BAKERY['TXT_OUT_OF_STOCK']."' class='mod_bakery_main_stock_img_f' />";
				} elseif ($item_stock > $setting_stock_limit) {
					$stock = "<img src='".WB_URL."/modules/bakery/images/in_stock.gif' alt='".$MOD_BAKERY['TXT_IN_STOCK']."' class='mod_bakery_main_stock_img_f' />";
				} else {
					$stock = "<img src='".WB_URL."/modules/bakery/images/short_of_stock.gif' alt='".$MOD_BAKERY['TXT_SHORT_OF_STOCK']."' class='mod_bakery_main_stock_img_f' />";
			}
			// Display stock text message			
			} elseif ($setting_stock_mode == "text" && is_numeric($setting_stock_limit) && $setting_stock_limit != '') {
				if ($item_stock < 1) {
					$stock = "<span class='mod_bakery_main_out_of_stock_f'>".$MOD_BAKERY['TXT_OUT_OF_STOCK']."</span>";
				} elseif ($item_stock > $setting_stock_limit) {
					$stock = "<span class='mod_bakery_main_in_stock_f'>".$MOD_BAKERY['TXT_IN_STOCK']."</span>";
				} else {
					$stock = "<span class='mod_bakery_main_short_of_stock_f'>".$MOD_BAKERY['TXT_SHORT_OF_STOCK']."</span>";
				}
			} else {
				$stock = '';
			}
		}

		// Replace placeholders by values
		$vars = array('[ADD_TO_CART]', '[PAGE_TITLE]', '[THUMB]', '[THUMBS]', '[IMAGE]', '[IMAGES]', '[TITLE]', '[ITEM_ID]', '[SKU]', '[STOCK]', '[PRICE]', '[TAX_RATE]', '[SHIPPING]', '[FIELD_1]', '[FIELD_2]', '[FIELD_3]', '[OPTION]', '[DESCRIPTION]', '[FULL_DESC]', '[SHOP_URL]', '[SHIPPING_DOMESTIC]', '[SHIPPING_ABROAD]', '[SHIPPING_D_A]', '[CURRENCY]', '[LINK]', '[DATE]', '[TIME]', '[USER_ID]', '[USERNAME]', '[DISPLAY_NAME]', '[EMAIL]', '[TEXT_READ_MORE]', '[TXT_ITEM]', '[TXT_PRICE]', '[TXT_TAX_RATE]', '[TXT_STOCK]', '[TXT_FIELD_1]', '[TXT_FIELD_2]', '[TXT_FIELD_3]');
		if (isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
			$values = array($MOD_BAKERY['TXT_ADD_TO_CART'], PAGE_TITLE, $thumb, $thumbs, $image, $images, $title, $item_id, stripslashes($item['sku']), $stock, $price, stripslashes($item['tax_rate']), stripslashes($item['shipping']), stripslashes($item['definable_field_0']), stripslashes($item['definable_field_1']), stripslashes($item['definable_field_2']), $option, stripslashes($item['description']), stripslashes($item['full_desc']), $setting_continue_url, $setting_shipping_domestic, $setting_shipping_abroad, $setting_shipping_d_a, $setting_shop_currency, $item_link, $item_date, $item_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email'], $TEXT['READ_MORE'], $MOD_BAKERY['TXT_ITEM'], $MOD_BAKERY['TXT_PRICE'], $MOD_BAKERY['TXT_TAX_RATE'], $MOD_BAKERY['TXT_STOCK'], $setting_definable_field_0, $setting_definable_field_1, $setting_definable_field_2);
		} else {
			$values = array($MOD_BAKERY['TXT_ADD_TO_CART'], PAGE_TITLE, $thumb, $thumbs, $image, $images, $title, $item_id, stripslashes($item['sku']), $stock, $price, stripslashes($item['tax_rate']), stripslashes($item['shipping']), stripslashes($item['definable_field_0']), stripslashes($item['definable_field_1']), stripslashes($item['definable_field_2']), $option, stripslashes($item['description']), stripslashes($item['full_desc']), $setting_continue_url, $setting_shipping_domestic, $setting_shipping_abroad, $setting_shipping_d_a, $setting_shop_currency, $item_link, $item_date, $item_time, '', '', '', '', $TEXT['READ_MORE'], $MOD_BAKERY['TXT_ITEM'], $MOD_BAKERY['TXT_PRICE'], $MOD_BAKERY['TXT_TAX_RATE'], $MOD_BAKERY['TXT_STOCK'], $setting_definable_field_0, $setting_definable_field_1, $setting_definable_field_2);
		}
		echo str_replace($vars, $values, $setting_item_loop);
		// Increment counter
		$counter = $counter + 1;
		// Check if we should end this row
		if ($counter % $setting_num_cols == 0 && $counter != $num_items) {
			echo "</tr><tr>\n";
		}
	}

	// Add cells to complete an open row at the end of the table
	if ($counter > $setting_num_cols) {
		while ($counter % $setting_num_cols != 0) {
			echo "<td class='mod_bakery_main_td_f'>&nbsp;</td>\n";
			$counter++;
		}
	}
}


// Print footer
if ($display_previous_next_links == 'none') {
	echo  str_replace(array('[PAGE_TITLE]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,'','','','','','', $display_previous_next_links, $MOD_BAKERY['TXT_ITEM']), $setting_footer);
} else {
	echo str_replace(array('[PAGE_TITLE]','[NEXT_PAGE_LINK]','[NEXT_LINK]','[PREVIOUS_PAGE_LINK]','[PREVIOUS_LINK]','[OUT_OF]','[OF]','[DISPLAY_PREVIOUS_NEXT_LINKS]','[TXT_ITEM]'), array(PAGE_TITLE,$next_page_link, $next_link, $previous_page_link, $previous_link, $out_of, $of, $display_previous_next_links, $MOD_BAKERY['TXT_ITEM']), $setting_footer);
}
