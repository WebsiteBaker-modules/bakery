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
// Get some default values
require_once(WB_PATH.'/modules/bakery/config.php');

// Look for language File
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Get header and footer
$query_page_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_page_settings WHERE section_id = '$section_id'");
$fetch_page_settings = $query_page_settings->fetchRow();

// Set raw html <'s and >'s to be replaced by friendly html code
$raw      = array('<', '>');
$friendly = array('&lt;', '&gt;');

// Get list of all module bakery pages and prepare <select>
$continue_url_select = '';
$cur_continue_url    = stripslashes($fetch_page_settings['continue_url']);

$query_pages = "SELECT p.page_id, p.link, p.visibility, p.admin_groups, p.admin_users, p.viewing_groups, p.viewing_users, s.section_id FROM ".TABLE_PREFIX."pages p INNER JOIN ".TABLE_PREFIX."sections s ON p.page_id = s.page_id WHERE s.module = 'bakery' AND p.visibility != 'deleted' ORDER BY p.level, p.position ASC";
$get_pages = $database->query($query_pages);

if ($get_pages->numRows() > 0) {
	// Generate sections select
	$continue_url_select .= "<select name='continue_url' style='width: 98%'>\n";
	while($page = $get_pages->fetchRow()) {
		$page = array_map('stripslashes', $page);
		// Only display if visible
		if ($admin->page_is_visible($page) == false)
			continue;
		// Get user perms
		$admin_groups = explode(',', str_replace('_', '', $page['admin_groups']));
		$admin_users = explode(',', str_replace('_', '', $page['admin_users']));
		// Check user perms
		$in_group = FALSE;
		foreach ($admin->get_groups_id() as $cur_gid){
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
		$continue_url         = WB_URL.PAGES_DIRECTORY.$page['link'].PAGE_EXTENSION;
		$continue_url_select .= '<option value="'.$page['page_id'].'"';
		$continue_url_select .= $cur_continue_url == $page['page_id'] ? ' selected="selected"' : '';
		$continue_url_select .= $can_modify == false ? " disabled='disabled' style='color: #aaa;'" : '';
		$continue_url_select .= '>'.$continue_url.'</option>'."\n";	
	}
	$continue_url_select .= "</select>";
}
?>



<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_page_settings.php" method="post" style="margin: 0;">

<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />

<table cellpadding="2" cellspacing="0" border="0" align="center" width="98%">
	<tr>
		<td colspan="3"><h2><?php echo $MOD_BAKERY['TXT_PAGE_SETTINGS']; ?></h2></td>
	</tr>


<!-- Shop -->
	<tr valign="bottom">
		  <td width="25%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_SHOP']." ".$MOD_BAKERY['TXT_SETTINGS']; ?>:</strong></td>
		  <td width="12" height="32" colspan="2">&nbsp;</td>
    </tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_PAGE_OFFLINE']; ?>:</td>
	  <td colspan="2"><input type="checkbox" name="page_offline" id="page_offline" value="yes" <?php if ($fetch_page_settings['page_offline'] == 'yes') { echo 'checked="checked"'; } ?> /></td>
    </tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_OFFLINE_TEXT']; ?>:</td>
	  <td colspan="2"><input type="text" name="offline_text" style="width: 98%" maxlength="255" value="<?php echo stripslashes($fetch_page_settings['offline_text']); ?>" /></td>
    </tr>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_CONTINUE_URL']; ?>:</td>
		<td colspan="2"><?php echo $continue_url_select; ?></td>
	</tr>


<!-- Layout -->
	<tr valign="bottom">
	  <td width="25%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_LAYOUT']." ".$MOD_BAKERY['TXT_SETTINGS']; ?>:</strong></td>
	  <td height="32" colspan="2"><input type="button" value="<?php echo $MENU['HELP']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/help.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>';" style="width: 100px;" /></td>
    </tr>
	<tr>
		<td width="25%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_OVERVIEW'].' ('.$TEXT['HEADER']; ?>):</td>
		<td colspan="2">
			<textarea name="header" style="width: 98%; height: 80px;"><?php echo stripslashes($fetch_page_settings['header']); ?></textarea></td>
	</tr>
	<tr>
		<td width="25%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_OVERVIEW'].' ('.$MOD_BAKERY['TXT_ITEM'].'-'.$TEXT['LOOP']; ?>):</td>
		<td colspan="2">
			<textarea name="item_loop" style="width: 98%; height: 80px;"><?php echo stripslashes($fetch_page_settings['item_loop']); ?></textarea></td>
	</tr>
	<tr>
		<td width="25%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_OVERVIEW'].' ('.$TEXT['FOOTER']; ?>):</td>
		<td colspan="2">
			<textarea name="footer" style="width: 98%; height: 80px;"><?php echo str_replace($raw, $friendly, stripslashes($fetch_page_settings['footer'])); ?></textarea>		</td>
	</tr>
	<tr>
		<td width="25%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_DETAIL'].' ('.$TEXT['HEADER']; ?>):</td>
		<td colspan="2">
			<textarea name="item_header" style="width: 98%; height: 80px;"><?php echo str_replace($raw, $friendly, stripslashes($fetch_page_settings['item_header'])); ?></textarea>		</td>
	</tr>
	<tr>
		<td width="25%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_DETAIL'].' ('.$TEXT['FOOTER']; ?>):</td>
		<td colspan="2">
			<textarea name="item_footer" style="width: 98%; height: 80px;"><?php echo str_replace($raw, $friendly, stripslashes($fetch_page_settings['item_footer'])); ?></textarea>		</td>
	</tr>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_ITEMS_PER_PAGE']; ?>:</td>
		<td colspan="2">
			<input type="text" name="items_per_page" style="width: 35px" value="<?php echo $fetch_page_settings['items_per_page']; ?>" /> 0 = <?php echo $TEXT['UNLIMITED']; ?>		</td>
	</tr>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_NUMBER_OF_COLUMNS']; ?>:</td>
		<td colspan="2">
			<select name="num_cols" style="width: 40px;">
				<?php
				for ($i = 1; $i <= 10; $i++) {
					if ($fetch_page_settings['num_cols'] == $i) { 
						$selected = ' selected';
					} else { 
						$selected = '';
					}
					echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
				}
				?>
			</select></td>
	</tr>
	<?php if (extension_loaded('gd') AND function_exists('imageCreateFromJpeg')) { /* Make's sure GD library is installed */ ?>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_THUMBNAIL']." ".$TEXT['SIZE']; ?>:</td>
		<td colspan="2">
			<select name="resize" style="width: 20%;">
				<?php
				foreach ($default_thumb_sizes AS $size => $size_name) {
					if ($fetch_page_settings['resize'] == $size) {
						$selected = ' selected';
					} else { 
						$selected = '';
					}
					echo '<option value="'.$size.'"'.$selected.'>'.$size_name.'</option>';
				}
				?>
			</select></td>
	</tr>
	<tr>
		<td width="25%" align="right">Lightbox2:</td>
		<td colspan="4">
		  <input type="checkbox" name="lb2_overview" id="lb2_overview" value="overview" <?php if ($fetch_page_settings['lightbox2'] == 'overview' || $fetch_page_settings['lightbox2'] == 'all') { echo 'checked="checked"'; } ?> />
		  <label for="lb2_overview"><?php echo $MOD_BAKERY['TXT_OVERVIEW']; ?></label> &nbsp;&nbsp;
		  <input type="checkbox" name="lb2_detail" id="lb2_detail" value="detail" <?php if ($fetch_page_settings['lightbox2'] == 'detail' || $fetch_page_settings['lightbox2'] == 'all') { echo 'checked="checked"'; } ?> />
		  <label for="lb2_detail"><?php echo $MOD_BAKERY['TXT_DETAIL']; ?></label>
		  </td>
	</tr>
	<?php } ?>
</table>

<?php
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
		if (isset($fetch_item['new_section_id']) && $fetch_item['new_section_id'] == $page['section_id']) {
			$sections_select .= ' selected="selected"';
		}
		elseif ($section_id == $page['section_id']) {
			$sections_select .= ' selected="selected"';
		}
		$sections_select .= $can_modify == false ? " disabled='disabled' style='color: #aaa;'" : '';
		$sections_select .= ">{$page['page_title']}</option>\n";
	}
}



// Save page settings   ?>
<table width="98%" align="center" cellpadding="0" cellspacing="4" class="mod_bakery_submit_row_b" style="padding: 10px;">
	<tr>
        <td><input type="radio" name="modify" id="modify_current" value="current" checked="checked" /></td>
        <td colspan="2"><label for="modify_current"><em><?php echo $MOD_BAKERY['TXT_MODIFY_THIS']; ?></em></label></td>
	</tr>
	<tr>
        <td><input type="radio" name="modify" id="modify_all" value="all" /></td>
        <td colspan="2"><label for="modify_all"><em><?php echo $MOD_BAKERY['TXT_MODIFY_ALL']; ?></em></label></td>
	</tr>
	<tr>
        <td><input type="radio" name="modify" id="modify_multiple" value="multiple" /></td>
        <td><label for="modify_multiple"><em><?php echo $MOD_BAKERY['TXT_MODIFY_MULTIPLE']; ?></em></label></td>
        <td rowspan="2">
		  <select name="modify_sections[]" multiple="multiple" style="width: 240px; margin: 0 5px 0 0;">
			<?php echo $sections_select; ?>
		  </select>
		</td>
	</tr>
	<tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
        <td colspan="2" height="30" align="left">
		  <input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin: 5px 0 0 15px;" />		</td>
        <td height="30" align="right">
		  <input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin: 5px 15px 0 0;" />
		</td>
	</tr>
</table>
</form>

<?php

// Print admin footer
$admin->print_footer();
