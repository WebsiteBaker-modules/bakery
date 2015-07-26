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


require('../../config.php');

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Make use of the skinable backend themes of WB > 2.7
// Check if THEME_URL is supported otherwise use ADMIN_URL
if (!defined('THEME_URL')) {
	define('THEME_URL', ADMIN_URL);
}

// Look for language file
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Get cat (category) and make query clause
if (isset($_GET['cat'])) {
	$category = is_numeric($_GET['cat'])  ? $_GET['cat']  : '';
} elseif (isset($_POST['cat'])) {
	$category = is_numeric($_POST['cat']) ? $_POST['cat'] : '';
} else {
	$category = '';
}
$clause = $category != '' ? " AND section_id = $category" : '';

// Bakery page list
$query_pages = "SELECT p.page_id, p.page_title, p.visibility, p.admin_groups, p.admin_users, p.viewing_groups, p.viewing_users, s.section_id FROM ".TABLE_PREFIX."pages p INNER JOIN ".TABLE_PREFIX."sections s ON p.page_id = s.page_id WHERE s.module = 'bakery' AND p.visibility != 'deleted' ORDER BY p.level, p.position ASC";
$get_pages = $database->query($query_pages);


// Generate sections select
if ($get_pages->numRows() > 0) {
	$sections_select = '';
	while ($page = $get_pages->fetchRow()) {
		$page = array_map('stripslashes', $page);
		// Only display if visible
		if ($admin->page_is_visible($page) == false)
			continue;
		// Get user perms
		$admin_groups = explode(',', str_replace('_', '', $page['admin_groups']));
		$admin_users = explode(',', str_replace('_', '', $page['admin_users']));
		// Check user perms
		$in_group = FALSE;
		foreach ($admin->get_groups_id() as $cur_gid) {
			if (in_array($cur_gid, $admin_groups)) {
				$in_group = TRUE;
			}
		}
		if (($in_group) OR is_numeric(array_search($admin->get_user_id(), $admin_users))) {
			$can_modify = true;
		} else {
			$can_modify = false;
		}
		// Options
		$sections_select .= "<option value='{$page['section_id']}'";
		if ($category == $page['section_id']) {
			$sections_select .= ' selected="selected"';
		}
		$sections_select .= $can_modify == false ? " disabled='disabled' style='color: #aaa;'" : '';
		$sections_select .= ">{$page['page_title']}</option>\n";
	}
}

// Title and section select   ?>
<form name="category" action="<?php echo WB_URL; ?>/modules/bakery/stock.php" method="post" style="margin: 0;">
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<h2 style="display: inline;"><?php echo $MOD_BAKERY['TXT_STOCK_ADMIN']; ?></h2>
<select name="cat" onchange="javascript: document.category.submit();" style="width: 360px; margin: 0 0 15px 100px;">
<option value=""><?php echo $MOD_BAKERY['TXT_ALL']. ' ' . $MOD_BAKERY['TXT_ITEMS']; ?></option>
<?php echo $sections_select; ?>
</select>
</form>

<?php
// Order items table by ... and toggle asc/desc for db query
$order = "ASC";
$text_order = $MOD_BAKERY['TXT_ORDER_DESC'];
if (isset($_GET['order'])) {
	$order = $_GET['order'] == "ASC" ? "DESC" : "ASC";
	$text_order = $_GET['order'] == "ASC" ? $MOD_BAKERY['TXT_ORDER_ASC'] : $MOD_BAKERY['TXT_ORDER_DESC'];
}
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : "item_id";
// Sort ORDER BY stock as integer (not as string)
$order_by = $order_by == 'stock' ? 'CAST(' . $order_by . ' AS UNSIGNED INTEGER)' : $order_by;

// Query items table
$query_items = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_items WHERE title != ''$clause ORDER BY $order_by $order");
if ($query_items->numRows() > 0) {

	// Items table header   ?>
	<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_stock.php" method="post" style="margin: 0;">
	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
	<input type="hidden" name="cat" value="<?php echo $category; ?>" />
	<table cellpadding="4" cellspacing="0" border="0" width="98%" align="center">
	<tr height="30" valign="bottom" class="mod_bakery_submit_row_b">
      <th align="left"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=sku&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $MOD_BAKERY['TXT_SKU']; ?></a></th>
      <th align="center"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=modified_when&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $TEXT['DATE']; ?></a></th>
      <th align="left"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=title&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $MOD_BAKERY['TXT_NAME']; ?></a></th>
      <th align="left"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=price&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $MOD_BAKERY['TXT_PRICE']; ?></a></th>
      <th align="left"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=shipping&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $MOD_BAKERY['TXT_SHIPPING']; ?></a></th>
      <th align="center"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=stock&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $MOD_BAKERY['TXT_IN_STOCK']; ?></a></th>
      <th align="left"><a href="<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;order_by=active&amp;order=<?php echo $order; ?>&amp;cat=<?php echo $category; ?>" title="<?php echo $text_order; ?>"><?php echo $TEXT['ACTIVE']; ?></a></th>
      <th colspan="2" align="left"><?php echo $TEXT['ACTIONS']; ?></th>
	</tr>
	<?php
	
	// List items table
	$row = 'a';
	while ($item = $query_items->fetchRow()) {
		$item = array_map('stripslashes', $item);
		?>
		<tr class="row_<?php echo $row; ?>" height="20">
			<td width="7%" align="right" nowrap="nowrap"><?php echo $item['sku']; ?></td>
			<td width="115" align="center"><?php echo gmdate(DEFAULT_DATE_FORMAT, $item['modified_when']+TIMEZONE); ?></td>
			<td align="left"><a href="<?php echo WB_URL; ?>/modules/bakery/modify_item.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $item['item_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>"><?php echo $item['title']; ?></a></td>
			<td width="30" align="right"><?php echo $item['price']; ?></td>
			<td width="30" align="right"><?php echo $item['shipping']; ?></td>
			<td width="70" align="center"><input type="text" name="stock[<?php echo $item['item_id']; ?>]" value="<?php echo $item['stock']; ?>" style="width: 50px; text-align: right;" /></td>
			<td width="20" align="center"><input type="checkbox" name="active[<?php echo $item['item_id']; ?>]" value="1"<?php if ($item['active'] == '1') {echo ' checked="checked"';} ?> /></td>
			<td width="20" align="center">
				<a href="<?php echo WB_URL; ?>/modules/bakery/modify_item.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;item_id=<?php echo $item['item_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/modify_16.png" border="0" alt="<?php echo $TEXT['MODIFY']; ?>" />
				</a>
			</td>
			<td width="20" align="right">
				<a href="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/bakery/delete_item.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&item_id=<?php echo $item['item_id']; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/delete_16.png" border="0" alt="<?php echo $TEXT['DELETE']; ?>" />
				</a>
			</td>
		</tr>
		<?php
		// Alternate row color
		if ($row == 'a') {
			$row = 'b';
		} else {
			$row = 'a';
		}
	}
	?>
	</table>
	<?php
} else {
	echo $TEXT['NONE_FOUND']."<br /><br />";
}

// Show buttons
?>
<table width="98%" align="center" cellpadding="0" cellspacing="0" class="mod_bakery_submit_row_b">
	<tr valign="top">
	  <td height="30" align="left"  style="padding-left: 12px;">
	  <input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin-top: 5px;" /></td>
	  <td height="30" align="center">
	  <input type="button" value="<?php echo $TEXT['RELOAD']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/stock.php?page_id=<?php echo $page_id; ?>&cat=<?php echo $category; ?>';" style="width: 100px; margin-top: 5px;" /></td>
	  <td height="30" align="right"  style="padding-right: 12px;">
	  <input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" /></td>
	</tr>
</table>
</form>
<?php

// Print admin footer
$admin->print_footer();
