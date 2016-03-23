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

// Make use of the skinable backend themes of WB > 2.7
// Check if THEME_URL is supported otherwise use ADMIN_URL
if (!defined('THEME_URL')) {
	define('THEME_URL', ADMIN_URL);
}

//Look for language File
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');
// Include WB functions file
require_once(WB_PATH.'/framework/functions.php');

// Include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
if (file_exists(WB_PATH.'/framework/module.functions.php') && file_exists(WB_PATH.'/modules/edit_module_files.php')) {
	include_once(WB_PATH.'/framework/module.functions.php');
}

// Delete empty Database records
$database->query("DELETE FROM ".TABLE_PREFIX."mod_bakery_items WHERE page_id = '$page_id' and section_id = '$section_id' and title=''");

// Get shop name
$query_general_settings = $database->query("SELECT shop_name, display_settings FROM ".TABLE_PREFIX."mod_bakery_general_settings");
if ($query_general_settings->numRows() > 0) {
	$fetch_general_settings = $query_general_settings->fetchRow();
	$shop_name = stripslashes($fetch_general_settings['shop_name']);
	$display_settings = "inline";
	if ($fetch_general_settings['display_settings'] == "1") {
		$display_settings = "none";
		if ($_SESSION['USER_ID'] == 1) {
			$display_settings = "inline";
		}
	}
} 
?>



<div id="mod_bakery_modify_b">

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr height="25">
		<td><input type="button" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_options.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" value="<?php echo $MOD_BAKERY['TXT_ITEM_OPTIONS']; ?>" style="width: 200px; " /></td>
		<td><input type="button" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_orders.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" value="<?php echo $MOD_BAKERY['TXT_ORDER_ADMIN']; ?>" style="width: 200px; " /></td>
		<td><input type="button" value="<?php echo $MOD_BAKERY['TXT_GENERAL_SETTINGS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_general_settings.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; display: <?php echo $display_settings; ?>;" /></td>
		<td><input type="button" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_payment_methods.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>';" value="<?php echo $MOD_BAKERY['TXT_PAYMENT_METHODS']; ?>" style="float:right;width: 200px; display: <?php echo $display_settings; ?>;" /></td>
	</tr>
	<tr>
		<td><input type="button" value="[+] <?php echo $MOD_BAKERY['TXT_ADD_ITEM']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/add_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; " /></td>
		<td><input type="button" value="<?php echo $MOD_BAKERY['TXT_STOCK_ADMIN']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; " /></td>
		<td><input type="button" value="<?php echo $MOD_BAKERY['TXT_PAGE_SETTINGS']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_page_settings.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 200px; display: <?php echo $display_settings; ?>;" /></td>
		<td><?php
			if (function_exists('edit_module_css')) {
				if ($display_settings == "inline") {
					edit_module_css('bakery');
				}
			} else {
				echo "<input type='button' name='edit_module_file' class='mod_bakery_edit_css' value='{$TEXT['CAP_EDIT_CSS']}' onclick=\"javascript: alert('To take advantage of this feature please upgrade to WB 2.7 or higher.')\" />";
			}
			?>
		</td>
	</tr>
</table>



<br />
<h2><?php echo $TEXT['MODIFY'].' / '.$TEXT['DELETE'].' '.$MOD_BAKERY['TXT_ITEM']; ?></h2>
<?php

// Loop through existing items
$query_items = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_bakery_items` WHERE section_id = '$section_id' ORDER BY position ASC");

if ($query_items->numRows() > 0) {
	$num_items = $query_items->numRows();
	?>
	<table cellpadding="2" cellspacing="0" border="0" width="100%" class="mod_bakery_dragndrop_b">
	<caption><?php echo get_menu_title($page_id); ?></caption>
		<thead>
		  <tr height="30" class="grouptr">
				<td></td>
				<td style="text-align: right;"><span title="<?php echo $MOD_BAKERY['TXT_ITEM']; ?> ID">ID</span></td>
				<td></td>
				<td><?php echo $TEXT['NAME']; ?></td>				
				<td><?php echo $TEXT['ACTIVE']; ?></td>
				<td><?php echo $TEXT['DELETE']; ?>?</td>
				<td></td>
				<td></td>
				<td><span id="dragBakeryResult"></span></td>
		  </tr>
		</thead>
		<tbody id="dragBakeryTable">
		<?php 


		// LOOP ITEMS
		while ($post = $query_items->fetchRow()):

			// Prepare thumb path and url
			$thumb_path = WB_PATH.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.$post['item_id'].'/';
			$thumb_url  = WB_URL.MEDIA_DIRECTORY.'/'.$img_dir.'/thumbs/item'.$post['item_id'].'/';

			// Get main thumb (image with position == 1)
			$main_image = FALSE;
			$main_image = $database->get_one("SELECT `filename` FROM ".TABLE_PREFIX."mod_bakery_images WHERE `item_id` = '{$post['item_id']}' AND `active` = '1' ORDER BY `position` ASC LIMIT 1");

			// Check if png image has a jpg thumb (version < 1.7.6 used jpg thumbs only)
			$main_thumb = $main_image;
			if (!file_exists($thumb_path.$main_thumb)) {
				$main_thumb = str_replace('.png', '.jpg', $main_thumb);
			}
			$main_thumb_url = $thumb_url.$main_thumb;

		?>
		<tr id="row_<?php echo $post['item_id']; ?>" class="irow">
			<td class="dragdrop_bakery"></td>
			<td class="item_id"><?php echo $post['item_id']; ?></td>

			<td style="width: 5%; padding-left: 5px;">
			<div class="mod_bakery_thumbnail_b">
                <?php if ($main_image):
                // Check if main image is set and display it
                ?>				
					<a href="<?php echo $main_thumb_url; ?>" target="_blank">
						<img src="<?php echo $main_thumb_url; ?>" alt="<?php echo $MOD_BAKERY['TXT_IMAGE'].' '.$post['main_image']; ?>" height="48" border="0" />
					</a>				           
                <?php else: 
                // else show the "noimage" icon --> 
                ?>                   
                   <img src="<?php echo WB_URL; ?>/modules/bakery/images/nopic.png" alt="<?php echo $TEXT['NONE_FOUND']; ?>" title="<?php echo $TEXT['NONE_FOUND']; ?>" height="48" width="48" border="0" />
                <?php endif; ?>
                </div>     
			</td>
			
			<td style="width: 60%">
				<a href="<?php echo WB_URL; ?>/modules/bakery/modify_item.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $post['item_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
					<strong><?php echo stripslashes($post['title']); ?></strong>
				</a>
			</td>
			
			<td style="width: 10%">
				<?php echo (($post['active'] == 1) ? '<b style="color:#2C8F43;">'.$TEXT['YES'] : '<b style="color:#DF291B;">'.$TEXT['NO'].'</b>'); ?>
			</td>
			
			<td style="width: 10%" align="center">
				<a href="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/bakery/delete_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $post['item_id']; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/delete_16.png" border="0" alt="[x]" />
				</a>
			</td>
			
			<td style="width: 18px" class="move_up">
			<?php if ($post['position'] != 1) { ?>
				<a href="<?php echo WB_URL; ?>/modules/bakery/move_up.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $post['item_id']; ?>" title="<?php echo $TEXT['MOVE_UP']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/up_16.png" border="0" alt="/\" />
				</a>
			<?php } ?>
			</td>
			
			<td style="width: 118px" class="move_down">
			<?php if ($post['position'] != $num_items) { ?>
				<a href="<?php echo WB_URL; ?>/modules/bakery/move_down.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $post['item_id']; ?>" title="<?php echo $TEXT['MOVE_DOWN']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/down_16.png" border="0" alt="\/" />
				</a>
			<?php } ?>
			</td>
			<td class="dragdrop_bakery"></td>
		</tr>
		<?php
		
		
	endwhile; //LOOP
	?>
	</tbody>
		<tfoot>
			<tr>
				<td></td>	
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
	<?php
} else {
	echo $TEXT['NONE_FOUND'];
}
?>
</div> <!-- enddiv #mod_bakery_modify_b -->