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



// ITEM OPTIONS
// ************

echo "<h2>1. {$MOD_BAKERY['TXT_ITEM_OPTIONS']}&nbsp;&nbsp;&nbsp;(<span style='text-transform: lowercase;'>{$TEXT['ADD']}/{$TEXT['MODIFY']}/{$TEXT['DELETE']}</span>)</h2>";

// Query options table
$query_options = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_options ORDER BY option_name ASC");

// Initialize vars
$option_id = '';
$option_name = '';
$option_select = array();

// Form and table header
?>
<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_option.php" method="post" style="margin: 0;">
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<table cellpadding="2" cellspacing="0" border="0" width="98%" align="center">
	<tr height="30" valign="bottom" class="mod_bakery_submit_row_b">
      <th align="left"><span style="margin-left: 5px;"><?php echo $MOD_BAKERY['TXT_OPTION_NAME']."</span> (".$MOD_BAKERY['TXT_EG_OPTION_NAME']; ?>)</th>
      <th colspan="2" align="center"><?php echo $TEXT['ACTIONS']; ?></th>
      <th align="left">&nbsp;</th>
	</tr>

<?php
if ($query_options->numRows() > 0) {
	// List option table
	$row = 'a';
	while ($option = $query_options->fetchRow()) {
		$option = array_map('stripslashes', $option);
		// Make array for the select in the attributes section 
		$option_select = $option_select + array($option['option_id'] => $option['option_name']);
		// Prepare the option which is required to be modified for the form
		if (isset($_GET['option_id']) && $option['option_id'] == $_GET['option_id']) {
			$option_id = $option['option_id'];
			$option_name = $option['option_name'];
			continue;
		}
		
		// List the options
		?>
		<tr class="row_<?php echo $row; ?>" height="20">
			<td width="300" align="left"><span style="margin-left: 5px;"><?php echo $option['option_name']; ?></span></td>
			<td align="center" width="22">
				<a href="<?php echo WB_URL; ?>/modules/bakery/modify_options.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;option_id=<?php echo $option['option_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/modify_16.png" alt="<?php echo $TEXT['MODIFY'].' '.$MOD_BAKERY['TXT_OPTION_NAME']; ?>" border="0" />
				</a>
			</td>
			<td width="22">
				<a href="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/bakery/delete_option.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&option_id=<?php echo $option['option_id']; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/delete_16.png" border="0" alt="<?php echo $TEXT['DELETE'].' '.$MOD_BAKERY['TXT_OPTION_NAME']; ?>" />
				</a>
			</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		// Alternate row color
		if ($row == 'a') {
			$row = 'b';
		} else {
			$row = 'a';
		}
	}
} else {
	echo "<tr height='30'><td colspan='4'>\n";
	echo "<span style='color: red; padding-left: 50px;'>".$TEXT['NONE_FOUND']."</span>";
	echo "</td></tr>";
}

// Show the option modification form
?>
	<tr height="48" class="mod_bakery_submit_row_b">
		<td width="300" align="left"><input type="hidden" name="option_id" value="<?php echo $option_id; ?>" />
		<input type="text" name="option_name" value="<?php echo $option_name; ?>" style="width: 90%; margin-left: 5px;" />
		</td>
		<td colspan="2"><input type="submit" name="submit" value="<?php echo $TEXT['ADD']; ?>" />
		</td>
		<td align="right"  style="padding-right: 12px;"><input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px;" />
		</td>
	</tr>
	</table>
	</form>
<br /><br /><br />
<?php



// ITEM ATTRIBUTES
// ***************

echo "<h2>2. {$MOD_BAKERY['TXT_OPTION_ATTRIBUTES']}&nbsp;&nbsp;&nbsp;(<span style='text-transform: lowercase;'>{$TEXT['ADD']}/{$TEXT['MODIFY']}/{$TEXT['DELETE']}</span>)</h2>";


// Query options and attributes table
$query_attributes = $database->query("SELECT o.option_name, o.option_id, a.attribute_name, a.attribute_id FROM ".TABLE_PREFIX."mod_bakery_options o INNER JOIN ".TABLE_PREFIX."mod_bakery_attributes a ON o.option_id = a.option_id ORDER BY o.option_name, LENGTH(a.attribute_name), a.attribute_name ASC");

// Initialize vars
$attribute_id = '';
$attribute_name = '';

// Form and table header
?>
<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_attribute.php" method="post" style="margin: 0;">
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<table cellpadding="2" cellspacing="0" border="0" width="98%" align="center">
	<tr height="30" valign="bottom" class="mod_bakery_submit_row_b">
      <th align="left"><span style="margin-left: 5px;"><?php echo $MOD_BAKERY['TXT_OPTION_NAME']."</span> (".$MOD_BAKERY['TXT_EG_OPTION_NAME']; ?>)</th>
      <th align="left"><span style="margin-left: 12px;"><?php echo $MOD_BAKERY['TXT_OPTION_ATTRIBUTES']." (".$MOD_BAKERY['TXT_EG_OPTION_ATTRIBUTE']; ?>)</span></th>
      <th colspan="2" align="center"><?php echo $TEXT['ACTIONS']; ?></th>
      <th align="left">&nbsp;</th>
	</tr>

<?php
if ($query_attributes->numRows() > 0) {
	// List attributes table
	$row = 'a';
	while ($attribute = $query_attributes->fetchRow()) {
		$attribute = array_map('stripslashes', $attribute);
		// Prepare the attribute which is required to be modified for the form
		if (isset($_GET['attribute_id']) && $attribute['attribute_id'] == $_GET['attribute_id']) {
			$attribute_id = $attribute['attribute_id'];
			$attribute_name = $attribute['attribute_name'];
			continue;
		}
		
		// List the attributes
		?>
		<tr class="row_<?php echo $row; ?>" height="20">
			<td width="200" align="left"><span style="margin-left: 5px;"><?php echo $attribute['option_name']; ?></span></td>
			<td align="left"><span style="margin-left: 12px;"><?php echo $attribute['attribute_name']; ?></span></td>
			<td align="center" width="22">
				<a href="<?php echo WB_URL; ?>/modules/bakery/modify_options.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;option_id=<?php echo $attribute['option_id']; ?>&amp;attribute_id=<?php echo $attribute['attribute_id']; ?>" title="<?php echo $TEXT['MODIFY']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/modify_16.png" alt="<?php echo $TEXT['MODIFY'].' '.$MOD_BAKERY['TXT_OPTION_NAME']; ?>" border="0" />
				</a>
			</td>
			<td align="left" width="22">
				<a href="javascript: confirm_link('<?php echo $TEXT['ARE_YOU_SURE']; ?>', '<?php echo WB_URL; ?>/modules/bakery/delete_attribute.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&attribute_id=<?php echo $attribute['attribute_id']; ?>');" title="<?php echo $TEXT['DELETE']; ?>">
					<img src="<?php echo THEME_URL; ?>/images/delete_16.png" border="0" alt="<?php echo $TEXT['DELETE'].' '.$MOD_BAKERY['TXT_OPTION_NAME']; ?>" />	
				</a>
			</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		// Alternate row color
		if ($row == 'a') {
			$row = 'b';
		} else {
			$row = 'a';
		}
	}
} else {
	echo "<tr height='30'><td colspan='5'>\n";
	echo "<span style='color: red; padding-left: 50px;'>".$TEXT['NONE_FOUND']."</span>";
	echo "</td></tr>";
}

	// Show the attribute modification form
	?>
	<tr height="48" class="mod_bakery_submit_row_b">
		<td><?php
		// Generate option select
	echo "<select name='option_id' style='width: 200px'>";
	foreach ($option_select as $option_id => $option_name) {
		echo "<option value='$option_id'";
		echo isset($_GET['option_id']) && $_GET['option_id'] == $option_id ? ' selected="selected"' : '';
		echo ">$option_name</option>\n";
	}
	echo "</select>";
	?>
	</td>
		<td align="left"><input type="hidden" name="attribute_id" value="<?php echo $attribute_id; ?>" />
		<input type="text" name="attribute_name" value="<?php echo $attribute_name; ?>" style="width: 90%; margin-left: 5px;" />
		</td>
		<td colspan="2"><input type="submit" name="submit" value="<?php echo $TEXT['ADD']; ?>" />
		</td>
		<td width="100px" align="right"><input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px;" />
		</td>
	</tr>
	</table>
	</form>


<?php 

// Print admin footer
$admin->print_footer();
