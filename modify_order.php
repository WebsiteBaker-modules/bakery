<?php

/*
  Module developed for the Open Source Content Management System Website Baker (http://websitebaker.org)
  Copyright (C) 2007 - 2017, Christoph Marti

  LICENSE TERMS:
  Please read the software license agreement included in this package
  carefully before using the software. By installing and using the software,
  your are agreeing to be bound by the terms of the software license.
  If you do not agree to the terms of the license, do not use the software.
  Using any part of the software indicates that you accept these terms.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/


require('../../config.php');

// Validate the GET values
if (!isset($_GET['page_id']) OR !isset($_GET['section_id']) OR !isset($_GET['order_id']) OR !is_numeric($_GET['page_id']) OR !is_numeric($_GET['section_id']) OR !is_numeric($_GET['order_id'])) {
	header("Location: ".ADMIN_URL."/pages/index.php");
} else {
	$page_id    = $_GET['page_id'];
	$section_id = $_GET['section_id'];
	$order_id   = $_GET['order_id'];
}

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

// Look for language file
if (LANGUAGE_LOADED) {
    require_once(WB_PATH.'/modules/bakery/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php')) {
        require_once(WB_PATH.'/modules/bakery/languages/'.LANGUAGE.'.php');
    }
}

// Look for country file
if (LANGUAGE_LOADED) {
	if (file_exists(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php')) {
		require_once(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php');
	}
}
else {
	require_once(WB_PATH.'/modules/bakery/languages/countries/EN.php');
}

// Get some general settings
$query_general_settings = $database->query("SELECT shop_country, state_field, zip_location FROM ".TABLE_PREFIX."mod_bakery_general_settings");
if ($query_general_settings->numRows() > 0) {
	$fetch_general_settings = $query_general_settings->fetchRow();
	$setting_shop_country   = stripslashes($fetch_general_settings['shop_country']);
	$setting_state_field    = stripslashes($fetch_general_settings['state_field']);
	$setting_zip_location   = stripslashes($fetch_general_settings['zip_location']);
}

// Get customer data from DB customer table
$query_customer = $database->query("SELECT order_date, cust_company, cust_first_name, cust_last_name, cust_tax_no, cust_street, cust_city, cust_state, cust_country, cust_zip, cust_email, cust_phone, ship_company, ship_first_name, ship_last_name, ship_street, ship_city, ship_state, ship_country, ship_zip, invoice_id FROM ".TABLE_PREFIX."mod_bakery_customer WHERE order_id = '$order_id'");

if ($query_customer->numRows() > 0) {
	$customer = $query_customer->fetchRow();
	$customer = array_map('stripslashes', $customer);

	// Import variables from the returned array into the current symbol table
	$customer = array_map('htmlspecialchars', $customer);
	extract($customer);

	// Make human readable form of the order date
	$order_date = gmdate(DEFAULT_DATE_FORMAT.', '.DEFAULT_TIME_FORMAT, $order_date + TIMEZONE);
?>



<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_order.php" method="post" style="margin: 0;">

<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />

<table cellpadding="2" cellspacing="0" border="0" align="center" width="98%">
	<tr>
		<td colspan="5"><strong><?php echo $MOD_BAKERY['TXT_EDIT_ORDER'].' '.$TEXT['OF'].' '.$cust_first_name.' '.$cust_last_name; ?></strong></td>
	</tr>


<!-- Registration -->
	<tr valign="bottom">
		<td width="25%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_ORDER']; ?>:</strong></td>
		<td height="32" colspan="4">&nbsp;</td>
    </tr>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_ORDER_ID']; ?>:</td>
		<td colspan="4">
			<?php echo $order_id; ?>
		</td>
	</tr>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_INVOICE_ID']; ?>:</td>
		<td colspan="4">
			<?php echo $invoice_id; ?>
		</td>
	</tr>
	<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_ORDER_DATE']; ?>:</td>
		<td colspan="4">
			<?php echo $order_date; ?>
		</td>
	</tr>


<!-- Customer address form -->
	<tr valign="bottom">
		<td width="25%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_ADDRESS']; ?>:</strong></td>
		<td height="32" colspan="4">&nbsp;</td>
    </tr>
	<?php
	// Make array for the customer address form
	if ($setting_zip_location == 'end') {
		// Show zip at the end of address
		$cust_info = array('cust_email' => $MOD_BAKERY['TXT_CUST_EMAIL'], 'cust_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'cust_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'cust_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'cust_tax_no' => $MOD_BAKERY['TXT_CUST_TAX_NO'], 'cust_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'cust_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'cust_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'cust_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'cust_country' => $MOD_BAKERY['TXT_CUST_COUNTRY'], 'cust_phone' => $MOD_BAKERY['TXT_CUST_PHONE']);
		$length = array('cust_email' => '50', 'cust_confirm_email' => '50', 'cust_company' => '50', 'cust_first_name' => '50', 'cust_last_name' => '50', 'cust_tax_no' => '20', 'cust_street' => '50', 'cust_zip' => '10', 'cust_city' => '50', 'cust_state' => '50', 'cust_phone' => '20');
	} else {
		// Show zip inside of address
		$cust_info = array('cust_email' => $MOD_BAKERY['TXT_CUST_EMAIL'], 'cust_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'cust_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'cust_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'cust_tax_no' => $MOD_BAKERY['TXT_CUST_TAX_NO'], 'cust_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'cust_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'cust_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'cust_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'cust_country' => $MOD_BAKERY['TXT_CUST_COUNTRY'], 'cust_phone' => $MOD_BAKERY['TXT_CUST_PHONE']);
		$length = array('cust_email' => '50', 'cust_confirm_email' => '50', 'cust_company' => '50', 'cust_first_name' => '50', 'cust_last_name' => '50', 'cust_tax_no' => '20', 'cust_street' => '50', 'cust_zip' => '10', 'cust_city' => '50', 'cust_state' => '50', 'cust_phone' => '20');
	}


	// Make the form
	foreach ($cust_info as $field => $value) {

		// The customer state
		if ($field == 'cust_state') {

			// Hide state field
			if ($setting_state_field == 'hide') {
				continue;
			}

			// Include state file depending on selected customer country
			if (file_exists(WB_PATH.'/modules/bakery/languages/states/'.$cust_country.'.php')) {
				require_once(WB_PATH.'/modules/bakery/languages/states/'.$cust_country.'.php');

				// State dropdown menu
				echo '<tr><td width="25%" align="right">'.$MOD_BAKERY['TXT_CUST_STATE'].':</td><td colspan="4"><select name="cust_state" style="width: 98%">';
				echo '<option value="">'.$TEXT['PLEASE_SELECT'].'&hellip;</option>';
				for ($n = 1; $n <= count($MOD_BAKERY['TXT_STATE_NAME']); $n++) {
					$state      = $MOD_BAKERY['TXT_STATE_NAME'][$n];
					$state_code = $MOD_BAKERY['TXT_STATE_CODE'][$n];
					echo '<option value="'.$state_code.'"';
					if ($state_code == $cust_state) {
						echo ' selected="selected"';
					}
					echo '>'.$state.'</option>'."\n";
				}
				echo '</select></td></tr>'."\n";
				unset($MOD_BAKERY['TXT_STATE_NAME'], $MOD_BAKERY['TXT_STATE_CODE'][$n]);
			}
		}

		// The customer country
		elseif ($field == 'cust_country') {
			echo '<tr><td width="25%" align="right">'.$MOD_BAKERY['TXT_CUST_COUNTRY'].':</td><td colspan="4"><select name="cust_country" style="width: 98%">';
			for ($n = 1; $n <= count($MOD_BAKERY['TXT_COUNTRY_NAME']); $n++) {
				$country      = $MOD_BAKERY['TXT_COUNTRY_NAME'][$n];
				$country_code = $MOD_BAKERY['TXT_COUNTRY_CODE'][$n];
				echo '<option value="'.$country_code.'"';
				if ($country_code == $cust_country) {
					echo ' selected="selected"';
				}
				echo '>'.$country.'</option>'."\n";
			}
		echo '</select></td></tr>'."\n";
		}

		// And the others customer textfields
		else {
			echo '<tr><td width="25%" align="right">'.$value.':</td>
			<td colspan="4"><input type="text" style="width: 98%" name="'.$field.'" value="'.htmlspecialchars(@$$field, ENT_QUOTES).'" maxlength="'.$length[$field].'" /></td></tr>'."\n";
		}
	}
?>


<!-- Shipping address form -->
	<tr valign="bottom">
		<td width="25%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_SHIP_ADDRESS']; ?>:</strong></td>
		 <td height="32" colspan="4">&nbsp;</td>
    </tr>
	<?php
	// Make array for the shipping address form
	if ($setting_zip_location == 'end') {
		// Show zip at the end of address
		$ship_info = array('ship_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'ship_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'ship_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'ship_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'ship_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'ship_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'ship_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'ship_country' => $MOD_BAKERY['TXT_CUST_COUNTRY']);
		$length = array('ship_company' => '50', 'ship_first_name' => '50', 'ship_last_name' => '50', 'ship_street' => '50', 'ship_zip' => '10', 'ship_city' => '50', 'ship_state' => '50');
	} else {
		// Show zip inside of address
		$ship_info = array('ship_company' => $MOD_BAKERY['TXT_CUST_COMPANY'], 'ship_first_name' => $MOD_BAKERY['TXT_CUST_FIRST_NAME'], 'ship_last_name' => $MOD_BAKERY['TXT_CUST_LAST_NAME'], 'ship_street' => $MOD_BAKERY['TXT_CUST_ADDRESS'], 'ship_zip' => $MOD_BAKERY['TXT_CUST_ZIP'], 'ship_city' => $MOD_BAKERY['TXT_CUST_CITY'], 'ship_state' => $MOD_BAKERY['TXT_CUST_STATE'], 'ship_country' => $MOD_BAKERY['TXT_CUST_COUNTRY']);
		$length = array('ship_company' => '50', 'ship_first_name' => '50', 'ship_last_name' => '50', 'ship_street' => '50', 'ship_zip' => '10', 'ship_city' => '50', 'ship_state' => '50');
	}


	// Make the shipping address form
	foreach ($ship_info as $field => $value) {

		// The shipping state
		if ($field == 'ship_state') {

			// Hide state field
			if ($setting_state_field == 'hide') {
				continue;
			}

			// Include state file depending on selected shipping country
			if (file_exists(WB_PATH.'/modules/bakery/languages/states/'.$ship_country.'.php')) {
				require_once(WB_PATH.'/modules/bakery/languages/states/'.$ship_country.'.php');

				// State dropdown menu
				echo '<tr><td width="25%" align="right">'.$MOD_BAKERY['TXT_CUST_STATE'].':</td><td colspan="4"><select name="ship_state" style="width: 98%">';
				echo '<option value="">'.$TEXT['PLEASE_SELECT'].'&hellip;</option>';
				for ($n = 1; $n <= count($MOD_BAKERY['TXT_STATE_NAME']); $n++) {
					$state      = $MOD_BAKERY['TXT_STATE_NAME'][$n];
					$state_code = $MOD_BAKERY['TXT_STATE_CODE'][$n];
					echo '<option value="'.$state_code.'"';
					if ($state_code == $ship_state) {
						echo ' selected="selected"';
					}
					echo '>'.$state.'</option>'."\n";
				}
				echo '</select></td></tr>'."\n";
				unset($MOD_BAKERY['TXT_STATE_NAME'], $MOD_BAKERY['TXT_STATE_CODE'][$n]);
			}
		}

		// The shipping country
		elseif ($field == 'ship_country') {
			echo '<tr><td width="25%" align="right">'.$MOD_BAKERY['TXT_CUST_COUNTRY'].':</td><td colspan="4"><select name="ship_country" style="width: 98%">';
			echo '<option value="">'.$TEXT['PLEASE_SELECT'].'&hellip;</option>';
			for ($n = 1; $n <= count($MOD_BAKERY['TXT_COUNTRY_NAME']); $n++) {
				$country      = $MOD_BAKERY['TXT_COUNTRY_NAME'][$n];
				$country_code = $MOD_BAKERY['TXT_COUNTRY_CODE'][$n];
				echo '<option value="'.$country_code.'"';
				if ($country_code == $ship_country) {
					echo ' selected="selected"';
				}
				echo '>'.$country.'</option>'."\n";
			}
		echo '</select>';
		}

		// And the others shipping textfields
		else {
			echo '<tr><td width="25%" align="right">'.$value.':</td>
			<td colspan="4"><input type="text" style="width: 98%" name="'.$field.'" value="'.htmlspecialchars(@$$field, ENT_QUOTES).'" maxlength="'.$length[$field].'" /></td></tr>'."\n";
		}
	}

?>


<!-- Buttons -->
	<tr valign="top" class="mod_bakery_submit_row_b">
		<td height="40" colspan="5">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="left" style="padding-left: 12px;">
						<input name="save_and_return" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 160px; margin-top: 10px;">
						<input name="save" type="submit" value="<?php echo $TEXT['SAVE'].' &amp; '.$TEXT['BACK']; ?>" style="width: 240px; margin-left: 20px;">
					</td>
					<td align="right" style="padding-right: 12px;">
						<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php echo WB_URL; ?>/modules/bakery/modify_orders.php?page_id=<?php echo $page_id; ?>';" style="width: 160px; margin-top: 10px;">
					</td>
				</tr>
			</table>
		</td>
	</tr>

</table>
</form>

<?php
}
else {
	echo '<p class="mod_bakery_error_b">'.$TEXT['NONE_FOUND'].'!</p>';
}

// Print admin footer
$admin->print_footer();

?>
