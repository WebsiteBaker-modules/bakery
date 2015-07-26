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

// Look for language file
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Check installed payment methods and load new ones
require_once(WB_PATH.'/modules/bakery/payment_methods/load.php');

// Get selected payment method
$payment_method = isset($_GET['payment_method']) ? strip_tags($_GET['payment_method']) : 'advance';
?>


<script language="javascript" type="text/javascript">
	function mod_bakery_select_payment_method_b() {
		document.getElementsByName("reload")[0].value = "true";
		document.modify.submit();
	}
</script>

<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_payment_methods.php" method="post" style="margin: 0;">
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="update_payment_method" value="<?php echo $payment_method; ?>" />
<input type="hidden" name="reload" value="false" />

<table cellpadding="2" cellspacing="0" border="0" align="center" width="98%">
	<tr>
		<td colspan="5"><strong><?php echo $MOD_BAKERY['TXT_SELECT_PAYMENT_METHODS']; ?></strong></td>
	</tr>
	<tr valign="top">
	  <td align="right"><?php echo $MOD_BAKERY['TXT_PAYMENT_METHODS']; ?>:</td>
	  <td colspan="4">
		<?php



		// LOAD ALL PAYMENT METHODS/GATEWAYS, DISPLAY CHECKBOXES AND GENERATE DROP DOWN MENU
		$select_payment_method = '';
		
		// Get content of payment methods table
		$query_payment_methods = $database->query("SELECT pm_id, active, directory, name FROM ".TABLE_PREFIX."mod_bakery_payment_methods ORDER BY pm_id ASC");
		if ($query_payment_methods->numRows() > 0) {
			// Generate html table with checkboxes
			$i = 0;
			$num_col = 3;
			echo "<table cellpadding='0' cellspacing='0' border='0' align='left' width='90%'>\n";
			echo "\t\t\t<tr>\n";
			// Loop through payment methods
			while($fetch_payment_methods = $query_payment_methods->fetchRow()) {
				$fetch_payment_methods = array_map('stripslashes', $fetch_payment_methods);
				$pm_id = $fetch_payment_methods['pm_id'];
				$active = $fetch_payment_methods['active'];
				$directory = $fetch_payment_methods['directory'];
				$name = $fetch_payment_methods['name'];
				
				// If needed replace payment method names by localisations
				switch ($directory) {
					case 'cod': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_COD']; break;
					case 'advance': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_ADVANCE']; break;
					case 'invoice': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_INVOICE']; break;
					case 'payment-network': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_PAYMENT_NETWORK']; break;
				}

				// Generate checkboxes
				echo "\t\t\t\t<td><input type='checkbox' name='payment_methods[$pm_id]' id='payment_method_$pm_id' value='$directory'";
				if ($active) {
					echo ' checked="checked"';
				}
				echo "><label for='payment_method_$pm_id'>$name</label>\n";
				
				// Echo hidden fields for all payment methods
				echo "\t\t\t\t<input type='hidden' name='all_payment_methods[]' value='$pm_id' /></td>\n";
				
				// End of table row
				$i++;
				if (!($i % $num_col)) {
					echo "\t\t\t</tr><tr>\n";
				}
				
				// Generate select options for modifying payment methods
				$select_payment_method .= "<option value='$directory'";
				if ($payment_method == $directory) {
					$select_payment_method .= ' selected="selected"';
				}
				$select_payment_method .= ">$name</option>\n";
			}
			
			// Complete table with empty cells if needed
			while($i % $num_col) {
				echo "\t\t\t\t<td>&nbsp;</td>\n";
				$i++;
			}
		} ?>
			</tr>
		</table>
	  </td>
    </tr>
	<tr valign="bottom">
	  <td colspan="5" height="32"><strong><?php echo $TEXT['MODIFY'].' '.$MOD_BAKERY['TXT_PAYMENT_METHOD']; ?></strong></td>
    </tr>
	<tr>
	  <td width="30%" align="right"><?php echo $TEXT['PLEASE_SELECT']; ?>:</td>
	  <td colspan="4">
		<select name='modify_payment_method' style='width: 98%' onchange='mod_bakery_select_payment_method_b()'>
			<?php echo $select_payment_method; ?>
		</select></td>
	</tr>
	<?php



	// LOAD ALL PAYMENT METHODS/GATEWAYS, GENERATE DROP DOWN MENU AND TEXTAREAS FOR MODIFICATION
	
	// Get data of current payment method for modification
	$no_setting = true;
	$setting_table = '';
	$setting_info = '';
	$query_payment_methods = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = '$payment_method' LIMIT 1");
	if ($query_payment_methods->numRows() > 0) {
		$fetch_payment_methods = $query_payment_methods->fetchRow();
		$fetch_payment_methods = array_map('stripslashes', $fetch_payment_methods);
		$name = $fetch_payment_methods['name'];
		$cust_email_subject = $fetch_payment_methods['cust_email_subject'];
		$cust_email_body = $fetch_payment_methods['cust_email_body'];
		$shop_email_subject = $fetch_payment_methods['shop_email_subject'];
		$shop_email_body = $fetch_payment_methods['shop_email_body'];

		// If needed replace payment method names by localisations
		switch ($payment_method) {
			case 'cod': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_COD']; break;
			case 'advance': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_ADVANCE']; break;
			case 'invoice': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_INVOICE']; break;
			case 'payment-network': $name = $MOD_BAKERY['TXT_PAYMENT_METHOD_PAYMENT_NETWORK']; break;
		}

		// Look for payment method language file
		if (LANGUAGE_LOADED) {
			if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php')) {
				include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
			} else {
				echo "<p style='color: red;'><b>Failed to include language file...</b><br />The payment method &quot;$name&quot; is not avaiable. Make sure the requested payment method directory and associated files exist on your server.</p>";
			}
			if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
				include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
			}
		}

		// Generate textareas
		for ($i = 1; $i <= 6; $i++) {
			$field = $fetch_payment_methods['field_'.$i];
			$txt_index = "TXT_".strtoupper($field);
			$value = $fetch_payment_methods['value_'.$i];
			if ($field != '' && $field != 'invoice_template' && $field != 'invoice_alert' && $field != 'reminder_alert' ) {
				$no_setting = false;
				$setting_table .= "<tr>";
				$setting_table .= "<td width='30%' align='right' valign='top'>{$MOD_BAKERY[$payment_method][$txt_index]}:</td>";
				$setting_table .= "<td colspan='4'>";
				$setting_table .= "<textarea name='update[value_$i]' rows='3' style='width: 98%;'>$value</textarea></td>";
				$setting_table .= "</tr>";
			}

			// Special input fields for the invoice and reminder alert 
			elseif ($field == 'invoice_alert' || $field == 'reminder_alert') {
				$setting_table .= "<tr>
	  <td width='30%' align='right'>{$MOD_BAKERY[$payment_method][$txt_index]}:</td>
	  <td colspan='4'>
		<input type='text' maxlength='3' name='update[value_$i]' style='width: 30px; text-align: right;' value='$value' /> {$MOD_BAKERY['TXT_DAYS']}</td>
	</tr>";
			}

			// Special textarea for invoice template
			elseif ($field == 'invoice_template') {
				$setting_table .= "<tr valign='bottom'>
	  <td width='30%' height='32' align='right'><strong>{$MOD_BAKERY['TXT_LAYOUT']} {$MOD_BAKERY['TXT_SETTINGS']}:</strong></td>
	  <td height='32' colspan='4'><input type='button' value='{$MENU['HELP']}' onclick=\"javascript: window.location = '".WB_URL."/modules/bakery/help.php?page_id=$page_id&section_id=$section_id#invoice';\" style='width: 100px;' /></td>
	</tr>
	<tr>
	  <td width='30%' align='right' valign='top'>{$MOD_BAKERY[$payment_method]['TXT_INVOICE_TEMPLATE']}:</td>
	  <td colspan='4'>
		<textarea name='update[value_4]' style='width: 98%; height: 100px;'>$value</textarea></td>
	</tr>";
			}
		}
		
		// If no payment method setting has been set
		$setting_info = $no_setting ? $MOD_BAKERY['TXT_NO_PAYMENT_METHOD_SETTING'] : "&nbsp;";
	}

	// Show payment method header
	echo "<tr valign='bottom'>";
	echo "<td width='30%' height='32' align='right'><strong>$name {$MOD_BAKERY['TXT_SETTINGS']}:</strong></td>";
	echo "<td height='32' colspan='4'>$setting_info</td>";
	echo "</tr>";

	// Show payment method textareas
	echo $setting_table;
	
	// Show payment method notice if exists
	if (isset($MOD_BAKERY[$payment_method]['TXT_NOTICE']) && $MOD_BAKERY[$payment_method]['TXT_NOTICE'] != '') {
		echo "<tr valign='top'>";
		echo "<td width='30%' height='32' align='right'><strong>$name {$MOD_BAKERY['TXT_NOTICE']}:</strong></td>";
		echo "<td height='32' colspan='4'><p style='width: 97%; margin: 0; padding: 3px; border: solid 1px #FFD700; background-color: #FFFFDD;'>{$MOD_BAKERY[$payment_method]['TXT_NOTICE']}</p></td>";
		echo "</tr>";
	}
	
	// Emails to customer and shop ?>
	<tr valign="bottom">
	  <td width="30%" height="32" align="right"><strong><?php echo $name." ".$MOD_BAKERY['TXT_EMAIL']; ?>:</strong></td>
	  <td height="32" colspan="4"><input type="button" value="<?php echo $MENU['HELP']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/help.php?page_id=<?php echo $page_id; ?>&section_id=<?php echo $section_id; ?>&payment_method=<?php echo $payment_method; ?>#email';" style="width: 100px;" /></td>
    </tr>

	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_EMAIL_SUBJECT']." ".$MOD_BAKERY['TXT_CUSTOMER']; ?>:</td>
		<td colspan="4">
			<input type="text" name="update[cust_email_subject]" style="width: 98%" value="<?php echo $cust_email_subject; ?>" /></td>
	</tr>
	<tr>
		<td width="30%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_EMAIL_BODY']." ".$MOD_BAKERY['TXT_CUSTOMER']; ?>:</td>
		<td colspan="4">
			<textarea name="update[cust_email_body]" style="width: 98%; height: 80px;"><?php echo $cust_email_body; ?></textarea></td>
	</tr>

	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_EMAIL_SUBJECT']." ".$MOD_BAKERY['TXT_SHOP']; ?>:</td>
		<td colspan="4">
			<input type="text" name="update[shop_email_subject]" style="width: 98%" value="<?php echo $shop_email_subject; ?>" /></td>
	</tr>
	<tr>
		<td width="30%" align="right" valign="top"><?php echo $MOD_BAKERY['TXT_EMAIL_BODY']." ".$MOD_BAKERY['TXT_SHOP']; ?>:</td>
		<td colspan="4">
			<textarea name="update[shop_email_body]" style="width: 98%; height: 80px;"><?php echo $shop_email_body; ?></textarea></td>
	</tr>

</table>
<br />

<table width="98%" align="center" cellpadding="0" cellspacing="0" class="mod_bakery_submit_row_b">
	<tr valign="top">
	  <td height="30" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin-top: 5px;" /></td>
	  <td height="30" align="right">
	  <input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />&nbsp;&nbsp;&nbsp;</td>
	</tr>
</table>
</form>

<?php

// Print admin footer
$admin->print_footer();
