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


// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');




// SHOW ITEM DETAIL PAGE
// *********************

// Load jQuery if not loaded yet
?>
<script type="text/javascript">window.jQuery || document.write('<script src="<?php echo WB_URL; ?>/modules/bakery/jquery/jquery-1.7.2.min.js"><\/script>')</script>
<?php

// If requested include lightbox2 (css is appended to the frontend.css stylesheet)
if ($setting_lightbox2 == 'detail' || $setting_lightbox2 == 'all') {
	?>
	<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/bakery/lightbox2/js/lightbox.js"></script>
	<script type="text/javascript">
	//  Lightbox2 options
	lightbox.option({
		'albumLabel': '<?php echo $MOD_BAKERY['TXT_IMAGE']; ?> %1 <?php echo $TEXT['OF']; ?> %2'
	})
	</script>
	<?php
}

// Calculate price change depending on selected item option using js and jquery
?>
<script type="text/javascript" src="<?php echo WB_URL; ?>/modules/bakery/jquery/calc_price.js"></script>
<script type="text/javascript">
$(document).ready(function() {

	// Get the price container (must be adapted if html template has been modified)
	container     = $('.mod_bakery_item_price_f').parent().next();
	
	// General settings
	currency      = '<?php echo $setting_shop_currency; ?>';
	decimal_sep   = '<?php echo $setting_dec_point; ?>';
	thousands_sep = "<?php echo $setting_thousands_sep; ?>";

	// Calculate price on document ready
	$('.mod_bakery_item_select_f :selected').calcPrice();

	// Calculate price on selcted item option
	$('.mod_bakery_item_select_f').change(function() {
		$('.mod_bakery_item_select_f :selected').calcPrice();
	});
});
</script>
<?php


// Get page and item info
$query_page = $database->query("SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '".PAGE_ID."'");
if ($query_page->numRows() > 0) {
	$page      = $query_page->fetchRow();
	$page_link = page_link($page['link']);
} else {
	exit('Page not found');
}

// Get total number of items
$query_total_num = $database->query("SELECT item_id FROM ".TABLE_PREFIX."mod_bakery_items WHERE section_id = '$section_id' AND active = '1' AND title != ''");
$total_num = $query_total_num->numRows();

// Get item info
$query_item = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_items WHERE item_id = '".ITEM_ID."' AND active = '1'");
if ($query_item->numRows() > 0) {
	$item     = $query_item->fetchRow();	
	$position = $item['position'];
	$title    = htmlspecialchars(stripslashes($item['title']));
	$price    = number_format(stripslashes($item['price']), 2, $setting_dec_point, $setting_thousands_sep);

	// Initialize vars
	$next_link     = '';
	$previous_link = '';

	// If number of items is limited on overview pages,
	// add saved position as a get parameter to the page link
	if ($setting_items_per_page > 0) {
		$p         = empty($_SESSION['bakery']['position']) ? 0 : $_SESSION['bakery']['position'];
		$page_link = page_link($page['link']).'?p='.$p;
	}

	// Create previous and next links
	$query_surrounding = $database->query("SELECT item_id FROM ".TABLE_PREFIX."mod_bakery_items WHERE position != '$position' AND section_id = '$section_id' AND active = '1' LIMIT 1");
	if ($query_surrounding->numRows() > 0) {
		// Get previous
		if ($position > 1) {
			$query_previous = $database->query("SELECT title, link FROM ".TABLE_PREFIX."mod_bakery_items WHERE position < '$position' AND section_id = '$section_id' AND active = '1' ORDER BY position DESC LIMIT 1");
			if ($query_previous->numRows() > 0) {
				$previous = $query_previous->fetchRow();
				// Truncate text and add horizontal ellipsis
				if (strlen($previous['title']) > $link_length) {
					$previous['title'] = substr($previous['title'], 0, $link_length).'…';
				}
				$previous_link = '<a href="'.WB_URL.PAGES_DIRECTORY.$previous['link'].PAGE_EXTENSION.'">&laquo; '.htmlspecialchars(stripslashes($previous['title'])).'</a>';
			}
		}
		// Get next
		$query_next = $database->query("SELECT title, link FROM ".TABLE_PREFIX."mod_bakery_items WHERE position > '$position' AND section_id = '$section_id' AND active = '1' ORDER BY position ASC LIMIT 1 ");
		if ($query_next->numRows() > 0) {
			$next = $query_next->fetchRow();
			// Truncate text and add horizontal ellipsis
			if (strlen($next['title']) > $link_length) {
				$next['title'] = substr($next['title'], 0, $link_length).'…';
			}
			$next_link = '<a href="'.WB_URL.PAGES_DIRECTORY.$next['link'].PAGE_EXTENSION.'">'.htmlspecialchars(stripslashes($next['title'])).' &raquo;</a>';
		}
	}

	$out_of = $position.' '.strtolower($TEXT['OUT_OF']).' '.$total_num;
	$of     = $position.' '.strtolower($TEXT['OF']).' '.$total_num;
	
	// User who last modified the item
	$uid = $item['modified_by'];
	
	// Workout date and time of last modified item
	$item_date = gmdate(DATE_FORMAT, $item['modified_when']+TIMEZONE);
	$item_time = gmdate(TIME_FORMAT, $item['modified_when']+TIMEZONE);



	// Item thumb(s) and image(s)

	// Initialize or reset thumb(s) and image(s) befor laoding next item
	$thumb_arr = array();
	$image_arr = array();
	$thumb     = '';
	$image     = '';

	// Prepare thumb and image directory pathes and urls
	$thumb_path = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.ITEM_ID.'/';
	$img_path   = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/images/item'.ITEM_ID.'/';
	$thumb_url  = WB_URL.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.ITEM_ID.'/';
	$img_url    = WB_URL.MEDIA_DIRECTORY.'/'.$img_dir.'/images/item'.ITEM_ID.'/';

	// Get image data from db
	$query_image = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_images WHERE item_id = '".ITEM_ID."' AND active = '1' ORDER BY position ASC");
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

			// Check if png image has a jpg thumb (version < 1.7.6 used jpg thumbs only)
			$thumb_file = $image_file;
			if (!file_exists($thumb_path.$thumb_file)) {
				$thumb_file = str_replace('.png', '.jpg', $thumb_file);
			}

			// Prepare div image wrapper for image caption
			$caption_prepend = empty($img_caption) ? '' : '<div class="mod_bakery_item_caption_f">';
			$caption_append  = empty($img_caption) ? '' : '<br />'.$img_caption.'</div>';

			// Add unique image id that corresponds to the item attribute
			$thumb_id = empty($item_attribute) ? '' : 'mod_bakery_thumb_attr'.$item_attribute.'_f';
			$img_id   = empty($item_attribute) ? '' : 'mod_bakery_img_attr'.$item_attribute.'_f';

			// Make array of all item thumbs and images
			if (file_exists($thumb_path.$thumb_file) && file_exists($img_path.$image_file)) {
				// If needed add lightbox2 link to the thumb/image...
				if ($setting_lightbox2 == 'detail' || $setting_lightbox2 == 'all') {
					$prepend = '<a href="'.$img_url.$image_file.'" rel="lightbox[image_'.ITEM_ID.']" title="'.$img_title.'"><img src="';
					$thumb_append = '" alt="'.$img_alt.'" title="'.$img_title.'" id="'.$thumb_id.'" class="mod_bakery_item_thumb_f" /></a>';
					$img_append = '" alt="'.$img_alt.'" title="'.$img_title.'" id="'.$img_id.'" class="mod_bakery_item_img_f" /></a>';
				// ...else add thumb/image only
				} else {
					$prepend = '<img src="';
					$thumb_append = '" alt="'.$img_alt.'" title="'.$img_title.'" id="'.$thumb_id.'" class="mod_bakery_item_thumb_f" />';
					$img_append = '" alt="'.$img_alt.'" title="'.$img_title.'" id="'.$img_id.'" class="mod_bakery_item_img_f" />';
				}
				// Make array
				$thumb_arr[] = $prepend.$thumb_url.$thumb_file.$thumb_append;
				$image_arr[] = $caption_prepend.$prepend.$img_url.$image_file.$img_append.$caption_append;
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
	$open_tr       = '';
	$open_td       = '';
	$close_td      = "\n";
	$select_end    = '<br />'."\n";
	// Wrap select in a table row
	if ($use_table) {
		$open_tr    = '<tr>'."\n";
		$open_td    = '<td valign="top">'."\n";
		$close_td   = "\n".'</td>'."\n";
		$select_end = '</td>'."\n".'</tr>';
	}

	// Get number of item options and loop for each of them
	$query_num_options = $database->query("SELECT DISTINCT o.option_name, ia.option_id FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_item_attributes ia ON o.option_id = ia.option_id WHERE ia.item_id = ".ITEM_ID);			
	if ($query_num_options->numRows() > 0) {
		while ($num_options = $query_num_options->fetchRow()) {
			$option_name = stripslashes($num_options['option_name']);
			$option_id   = stripslashes($num_options['option_id']);

			// Get item attributes
			$query_attributes = $database->query("SELECT o.option_name, a.attribute_name, ia.attribute_id, ia.price, ia.operator FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_attributes a ON o.option_id = a.option_id INNER JOIN ".TABLE_PREFIX."mod_bakery_item_attributes ia ON a.attribute_id = ia.attribute_id WHERE item_id = ".ITEM_ID." AND ia.option_id = '$option_id' ORDER BY o.option_name, LENGTH(a.attribute_name), a.attribute_name ASC");
			if ($query_attributes->numRows() > 0) {
				$option_select .= $open_tr.$open_td.'<span class="mod_bakery_item_option_f">'.$option_name.': </span>'.$close_td.$open_td.'<select name="attribute[]" class="mod_bakery_item_select_f">'."\n"; 
				while ($attributes = $query_attributes->fetchRow()) {
					$attributes = array_map('stripslashes', $attributes);
					// Make attribute select
					$attributes['operator'] = $attributes['operator'] == '=' ? '' : $attributes['operator'];
					$ia_price = ', '.$setting_shop_currency.' '.$attributes['operator'].$attributes['price'];
					$ia_price = $attributes['price'] == 0 ? '' : $ia_price;
					$option_select .= '<option value="'.$attributes['attribute_id'].'">'.$attributes['attribute_name'].$ia_price.'</option>'."\n";
				}
				$option_select .= '</select>'."\n".$select_end;
				$option         = $option_select;
			}
		}
	}

	// Check if we should show number of items, stock image or "in stock" message or nothing at all
	$item_stock = stripslashes($item['stock']);
	// Only show if item stock is not blank
	if ($item_stock == '' && $setting_stock_mode != 'none') {
		$stock = $MOD_BAKERY['TXT_N/A'];
	} else {
		// Display number of items
		if ($setting_stock_mode == 'number') {
			if ($item_stock < 1) {
				$stock = 0;
			} else {
				$stock = $item_stock;
			}
		// Display stock image
		} elseif ($setting_stock_mode == 'img' && is_numeric($setting_stock_limit) && $setting_stock_limit != '') {
			if ($item_stock < 1) {
				$stock = '<img src="'.WB_URL.'/modules/bakery/images/out_of_stock.gif" alt="'.$MOD_BAKERY['TXT_OUT_OF_STOCK'].'" class="mod_bakery_item_stock_img_f" />';
			} elseif ($item_stock > $setting_stock_limit) {
				$stock = '<img src="'.WB_URL.'/modules/bakery/images/in_stock.gif" alt="'.$MOD_BAKERY['TXT_IN_STOCK'].'" class="mod_bakery_item_stock_img_f" />';
			} else {
				$stock = '<img src="'.WB_URL.'/modules/bakery/images/short_of_stock.gif" alt="'.$MOD_BAKERY['TXT_SHORT_OF_STOCK'].'" class="mod_bakery_item_stock_img_f" />';
		}
		// Display stock text message			
		} elseif ($setting_stock_mode == 'text' && is_numeric($setting_stock_limit) && $setting_stock_limit != '') {
			if ($item_stock < 1) {
				$stock = '<span class="mod_bakery_item_out_of_stock_f">'.$MOD_BAKERY['TXT_OUT_OF_STOCK'].'</span>';
			} elseif ($item_stock > $setting_stock_limit) {
				$stock = '<span class="mod_bakery_item_in_stock_f">'.$MOD_BAKERY['TXT_IN_STOCK'].'</span>';
			} else {
				$stock = '<span class="mod_bakery_item_short_of_stock_f">'.$MOD_BAKERY['TXT_SHORT_OF_STOCK'].'</span>';
			}
		// Display nothing
		} else {
			$stock = '';
		}
	}

	// Replace [wblinkPAGE_ID] generated by wysiwyg editor by real link
	$item['full_desc'] = stripslashes($item['full_desc']);
	$pattern = '/\[wblink(.+?)\]/s';
	preg_match_all($pattern,$item['full_desc'],$ids);
	foreach ($ids[1] as $page_id) {
		$pattern = '/\[wblink'.$page_id.'\]/s';
		// Get page link
		$query_pages = $database->query("SELECT link FROM ".TABLE_PREFIX."pages WHERE page_id = '$page_id' LIMIT 1");
		$page = $query_pages->fetchRow();
		$link = WB_URL.PAGES_DIRECTORY.$page['link'].PAGE_EXTENSION;
		$item['full_desc'] = preg_replace($pattern,$link,$item['full_desc']);
	}

	// Replace placeholders by values
	$vars = array('[ADD_TO_CART]', '[PAGE_TITLE]', '[THUMB]', '[THUMBS]', '[IMAGE]', '[IMAGES]', '[TITLE]', '[ITEM_ID]', '[SKU]', '[STOCK]', '[PRICE]', '[TAX_RATE]', '[SHIPPING]', '[FIELD_1]', '[FIELD_2]', '[FIELD_3]', '[OPTION]', '[DESCRIPTION]', '[FULL_DESC]', '[SHOP_URL]', '[SHIPPING_DOMESTIC]', '[SHIPPING_ABROAD]', '[SHIPPING_D_A]', '[CURRENCY]', '[BACK]', '[DATE]', '[TIME]', '[USER_ID]', '[USERNAME]', '[DISPLAY_NAME]', '[EMAIL]', '[PREVIOUS]', '[NEXT]', '[OUT_OF]', '[OF]', '[TEXT_OUT_OF]', '[TEXT_OF]', '[TXT_ITEM]', '[TXT_SKU]', '[TXT_STOCK]', '[TXT_PRICE]', '[TXT_TAX_RATE]', '[TXT_SHIPPING]', '[TXT_FIELD_1]', '[TXT_FIELD_2]', '[TXT_FIELD_3]', '[TXT_FULL_DESC]', '[TXT_SHIPPING_COST]', '[TXT_DOMESTIC]', '[TXT_ABROAD]', '[TXT_BACK]');
	if (isset($users[$uid]['username']) AND $users[$uid]['username'] != '') {
		$values = array($MOD_BAKERY['TXT_ADD_TO_CART'], PAGE_TITLE, $thumb, $thumbs, $image, $images, $title, ITEM_ID, stripslashes($item['sku']), $stock, $price, stripslashes($item['tax_rate']), stripslashes($item['shipping']), stripslashes($item['definable_field_0']), stripslashes($item['definable_field_1']), stripslashes($item['definable_field_2']), $option, stripslashes($item['description']), $item['full_desc'], $setting_continue_url, $setting_shipping_domestic, $setting_shipping_abroad, $setting_shipping_d_a, $setting_shop_currency, $page_link, $item_date, $item_time, $uid, $users[$uid]['username'], $users[$uid]['display_name'], $users[$uid]['email'], $previous_link, $next_link, $out_of, $of,  $TEXT['OUT_OF'], $TEXT['OF'], $MOD_BAKERY['TXT_ITEM'], $MOD_BAKERY['TXT_SKU'], $MOD_BAKERY['TXT_STOCK'], $MOD_BAKERY['TXT_PRICE'], $MOD_BAKERY['TXT_TAX_RATE'], $MOD_BAKERY['TXT_SHIPPING'], $setting_definable_field_0, $setting_definable_field_1, $setting_definable_field_2, $MOD_BAKERY['TXT_FULL_DESC'], $MOD_BAKERY['TXT_SHIPPING_COST'], $MOD_BAKERY['TXT_DOMESTIC'], $MOD_BAKERY['TXT_ABROAD'], $TEXT['BACK']);
	} else {
		$values = array($MOD_BAKERY['TXT_ADD_TO_CART'], PAGE_TITLE, $thumb, $thumbs, $image, $images, $title, ITEM_ID, stripslashes($item['sku']), $stock, $price, stripslashes($item['tax_rate']), stripslashes($item['shipping']), stripslashes($item['definable_field_0']), stripslashes($item['definable_field_1']), stripslashes($item['definable_field_2']), $option, stripslashes($item['description']), $item['full_desc'], $setting_continue_url, $setting_shipping_domestic, $setting_shipping_abroad, $setting_shipping_d_a, $setting_shop_currency, $page_link, $item_date, $item_time, '', '', '', '', $previous_link, $next_link, $out_of, $of, $TEXT['OUT_OF'], $TEXT['OF'], $MOD_BAKERY['TXT_ITEM'], $MOD_BAKERY['TXT_SKU'], $MOD_BAKERY['TXT_STOCK'], $MOD_BAKERY['TXT_PRICE'], $MOD_BAKERY['TXT_TAX_RATE'], $MOD_BAKERY['TXT_SHIPPING'], $setting_definable_field_0, $setting_definable_field_1, $setting_definable_field_2, $MOD_BAKERY['TXT_FULL_DESC'], $MOD_BAKERY['TXT_SHIPPING_COST'], $MOD_BAKERY['TXT_DOMESTIC'], $MOD_BAKERY['TXT_ABROAD'], $TEXT['BACK']);
	}

	// Print item header
	echo str_replace($vars, $values, $setting_item_header);

	// Print item footer
	echo str_replace($vars, $values, $setting_item_footer);

} else {
	echo $TEXT['NONE_FOUND'];
	return;
}
