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

// Look for country file
if (LANGUAGE_LOADED) {
	if (file_exists(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php')) {
		require_once(WB_PATH.'/modules/bakery/languages/countries/'.LANGUAGE.'.php');
	}
}
else {
	require_once(WB_PATH.'/modules/bakery/languages/countries/EN.php');
}

// Get content of general settings table
$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_bakery_general_settings WHERE shop_id = '0'");
$fetch_settings = $query_settings->fetchRow();

// Look for state file
$use_states = false;
if (file_exists(WB_PATH.'/modules/bakery/languages/states/'.stripslashes($fetch_settings['shop_country']).'.php')) {
	require_once(WB_PATH.'/modules/bakery/languages/states/'.stripslashes($fetch_settings['shop_country']).'.php');
	$use_states = true;
}
?>


<script language="javascript" type="text/javascript">
	function mod_bakery_country_reload_b() {
		document.getElementsByName("reload")[0].value = "true";
		document.modify.submit();
	}
	function mod_bakery_toggle_stock_mode_b() {
		if (document.getElementsByName("stock_mode")[0].value == "text" || document.getElementsByName("stock_mode")[0].value == "img") {
			document.getElementById('stock_limit').style.display = 'inline';
		} else {
			document.getElementById('stock_limit').style.display = 'none';
		}
	}
</script>

<form name="modify" action="<?php echo WB_URL; ?>/modules/bakery/save_general_settings.php" method="post" style="margin: 0;">
<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
<input type="hidden" name="reload" value="false" />

<table cellpadding="2" cellspacing="0" border="0" align="center" width="98%">
	<tr>
		<td colspan="5"><h2><?php echo $MOD_BAKERY['TXT_GENERAL_SETTINGS']; ?></h2></td>
	</tr>


<!-- Shop -->
	<tr valign="bottom">
		  <td width="30%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_SHOP']." ".$MOD_BAKERY['TXT_SETTINGS']; ?>:</strong></td>
		  <td height="32" colspan="4">&nbsp;</td>
    </tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHOP_NAME']; ?>:</td>
		<td colspan="4">
			<input type="text" name="shop_name" style="width: 98%" maxlength="100" value="<?php echo stripslashes($fetch_settings['shop_name']); ?>" /></td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHOP_EMAIL']; ?>:</td>
		<td colspan="4">
			<input type="text" name="shop_email" style="width: 98%" maxlength="50" value="<?php echo stripslashes($fetch_settings['shop_email']); ?>" /></td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHOP'].' '.$TEXT['PAGES_DIRECTORY']; ?>:</td>
		<td colspan="4">
			<input type="text" name="pages_directory" style="width: 98%" maxlength="20" value="<?php echo stripslashes($fetch_settings['pages_directory']); ?>" /></td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_TAC_URL']; ?>:</td>
		<td colspan="4">
			<input type="text" name="tac_url" style="width: 98%" maxlength="255" value="<?php echo stripslashes($fetch_settings['tac_url']); ?>" /></td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHOP_COUNTRY']; ?>:</td>
		<td colspan="4">
		<?php
			echo "<select name='shop_country' style='width: 98%' onchange='mod_bakery_country_reload_b()'>"; 
			for ($n = 1; $n <= count($MOD_BAKERY['TXT_COUNTRY_NAME']); $n++) {
				$country = $MOD_BAKERY['TXT_COUNTRY_NAME'][$n];
				$country_code = $MOD_BAKERY['TXT_COUNTRY_CODE'][$n];
				echo "<option value='$country_code'";
				if ($country_code == stripslashes($fetch_settings['shop_country'])) {
					echo ' selected="selected"';
				}
				echo ">$country</option>\n";
			}
		echo "</select>"; ?></td>
	</tr>
	<tr>
		<td width="30%" align="right"<?php if (!$use_states) { echo " class='mod_bakery_disabled_b'"; } ?>><?php echo $MOD_BAKERY['TXT_SHOP_STATE']; ?>:</td>
		<td colspan="4">
			<select name="shop_state"<?php if (!$use_states) { echo " disabled='disabled'"; } ?> style='width: 98%'>
			<?php
			if ($use_states) {
				for ($n = 1; $n <= count($MOD_BAKERY['TXT_STATE_NAME']); $n++) {
					$state = $MOD_BAKERY['TXT_STATE_NAME'][$n];
					$state_code = $MOD_BAKERY['TXT_STATE_CODE'][$n];
					echo "<option value='$state_code'";
					if ($state_code == stripslashes($fetch_settings['shop_state'])) {
						echo ' selected="selected"';
					}
					echo ">$state</option>\n";
				}
			} ?>
			</select>		</td>
	</tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_SHIP_ADDRESS']; ?>:</td>
	  <td colspan="4">
	    <select name="shipping_form" style="width: 98%">
	      <option value="none" <?php if ($fetch_settings['shipping_form'] == "none") { echo 'selected="selected"'; } ?> >
		  <?php echo $TEXT['NONE']; ?></option>
		  <option value="request" <?php if ($fetch_settings['shipping_form'] == "request") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_FORM_REQUEST']; ?></option>
		  <option value="hideable" <?php if ($fetch_settings['shipping_form'] == "hideable") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_FORM_HIDEABLE']; ?></option>
	      <option value="always" <?php if ($fetch_settings['shipping_form'] == "always") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_FORM_ALWAYS']; ?></option>
        </select></td>
    </tr>
	<tr>
	  <td align="right" valign="top"><?php echo $MOD_BAKERY['TXT_ADDRESS_FORM']; ?>:</td>
	  <td colspan="4">
		  <table width="98%" border="0" cellspacing="0" cellpadding="0">
		  	<tr>
		  		<td width="38%"><input type="checkbox" name="company_field" id="company_field" value="show" <?php if ($fetch_settings['company_field'] == 'show') { echo 'checked="checked"'; } ?> />
			  	<label for="company_field"><?php echo $MOD_BAKERY['TXT_SHOW_COMPANY_FIELD']; ?></label></td>
		  		<td><input type="checkbox" name="tax_no_field" id="tax_no_field" value="show" <?php if ($fetch_settings['tax_no_field'] == 'show') { echo 'checked="checked"'; } ?> onclick="javascript: sync_checkboxes(this);" />
		  		<label for="tax_no_field"><?php echo $MOD_BAKERY['TXT_SHOW_TAX_NO_FIELD']; ?></label></td>
		  	</tr>
		  	<tr>
		  		<td><input type="checkbox" name="state_field" id="state_field" value="show" <?php if ($fetch_settings['state_field'] == 'show') { echo 'checked="checked"'; } ?> />
		  		<label for="state_field"><?php echo $MOD_BAKERY['TXT_SHOW_STATE_FIELD']; ?></label></td>
		  		<td><input type="checkbox" name="zip_location" id="zip_location" value="end" <?php if ($fetch_settings['zip_location'] == 'end') { echo 'checked="checked"'; } ?> />
		  		<label for="zip_location"><?php echo $MOD_BAKERY['TXT_SHOW_ZIP_END_OF_ADDRESS']; ?></label></td>
		  	</tr>
		  </table>
	  </td>
    </tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_CUST_MSG']; ?>:</td>
	  <td colspan="4">
		<input type="checkbox" name="cust_msg" id="cust_msg" value="show" <?php if ($fetch_settings['cust_msg'] == 'show') { echo 'checked="checked"'; } ?> />
		<label for="cust_msg"><?php echo $MOD_BAKERY['TXT_SHOW_TEXTAREA']; ?></label>
    </tr>
	<tr>
	  <td align="right" valign="top"><?php echo $MOD_BAKERY['TXT_CART']; ?>:</td>
	  <td colspan="4">
	    <input type="checkbox" name="skip_cart" id="skip_cart" value="yes" <?php if ($fetch_settings['skip_cart'] == 'yes') { echo 'checked="checked"'; } ?> />
		<label for="skip_cart"><?php echo $MOD_BAKERY['TXT_SKIP_CART_AFTER_ADDING_ITEM']; ?></label><br /><span style="margin-left: 22px;">(<?php echo $MOD_BAKERY['TXT_MINICART_STRONGLY_RECOMMENDED']; ?>)</span></td>
    </tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_SETTINGS']; ?>:</td>
	  <td colspan="4">
	    <input type="checkbox" name="display_settings" id="display_settings" value="1" <?php if ($fetch_settings['display_settings'] == '1') { echo 'checked="checked"'; } ?> />
		<label for="display_settings"><?php echo $MOD_BAKERY['TXT_DISPLAY_SETTINGS_TO_ADMIN_ONLY']; ?></label></td>
    </tr>
<!--<tr>
		<td width="25%" align="right"><?php echo $MOD_BAKERY['TXT_USE_CAPTCHA']; ?>:</td>
		<td colspan="4">
		  <input type="checkbox" name="use_captcha" id="use_captcha" value="yes" <?php if ($fetch_settings['use_captcha'] == 'yes') { echo 'checked="checked"'; } ?> />
	</tr>-->
	
	
<!-- Items -->
	<tr valign="bottom">
		  <td width="30%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_ITEM']." ".$MOD_BAKERY['TXT_SETTINGS']; ?>:</strong></td>
		  <td height="32" colspan="4">&nbsp;</td>
    </tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_FREE_DEFINABLE_FIELD']; ?> 1:</td>
		<td colspan="4">
			<input type="text" name="definable_field_0" style="width: 98%" maxlength="50" value="<?php echo stripslashes($fetch_settings['definable_field_0']); ?>" />		</td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_FREE_DEFINABLE_FIELD']; ?> 2:</td>
		<td colspan="4">
			<input type="text" name="definable_field_1" style="width: 98%" maxlength="50" value="<?php echo stripslashes($fetch_settings['definable_field_1']); ?>" />		</td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_FREE_DEFINABLE_FIELD']; ?> 3:</td>
		<td colspan="4">
			<input type="text" name="definable_field_2" style="width: 98%" maxlength="50" value="<?php echo stripslashes($fetch_settings['definable_field_2']); ?>" />		</td>
	</tr>
	<tr>
		<td align="right" valign="top" style="padding-top: 5px;"><?php echo $MOD_BAKERY['TXT_STOCK']; ?>:</td>
		<td colspan="4">
	    <select name="stock_mode" style="width: 98%" onchange="mod_bakery_toggle_stock_mode_b()">
	      <option value="text" <?php if ($fetch_settings['stock_mode'] == "text") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_STOCK_MODE_TEXT']; ?></option>
	      <option value="img" <?php if ($fetch_settings['stock_mode'] == "img") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_STOCK_MODE_IMAGE']; ?></option>
	      <option value="number" <?php if ($fetch_settings['stock_mode'] == "number") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_STOCK_MODE_NUMBER']; ?></option>
		  <option value="none" <?php if ($fetch_settings['stock_mode'] == "none") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_STOCK_MODE_NONE']; ?></option>
        </select>
		<br />
		<span id="stock_limit" style="display: none;">&laquo;<?php echo ucfirst($MOD_BAKERY['TXT_SHORT_OF_STOCK']); ?>&raquo;: <input name="stock_limit" type="text" style="width: 25px; text-align: center;" value="<?php echo stripslashes($fetch_settings['stock_limit']); ?>" maxlength="3" /> &ndash; 1 <?php echo $MOD_BAKERY['TXT_ITEMS']; ?></span>		
		</td>
    </tr>
	<script language="javascript" type="text/javascript">
		if (document.getElementsByName("stock_mode")[0].value == "text" || document.getElementsByName("stock_mode")[0].value == "img") {
			document.getElementById('stock_limit').style.display = 'inline';
		}
	</script>
	<tr>
	  <td align="right">&nbsp;</td>
	  <td colspan="4">
	    <input type="checkbox" name="out_of_stock_orders" id="out_of_stock_orders" value="1" <?php if ($fetch_settings['out_of_stock_orders'] == '1') { echo 'checked="checked"'; } ?> />
		<label for="out_of_stock_orders"><?php echo $MOD_BAKERY['TXT_ALLOW_OUT_OF_STOCK_ORDERS']; ?></label></td>
    </tr>	

<!-- Payment -->
	<tr valign="bottom">
	  <td width="30%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_PAYMENT']." ".$MOD_BAKERY['TXT_SETTINGS']; ?>:</strong></td>
	  <td height="32" colspan="4">&nbsp;</td>
    </tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHOP_CURRENCY']; ?>:</td>
		<td colspan="4">
			<input type="text" name="shop_currency" style="width: 35px; text-align: center;" value="<?php echo stripslashes($fetch_settings['shop_currency']); ?>" maxlength="3" /> 
		  (USD, EUR, CHF, ... &nbsp;&nbsp;<a href="http://en.wikipedia.org/wiki/ISO_4217#Active_codes" target="_blank">&gt; ISO 4217</a>) </td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SEPARATOR_FOR']; ?>:</td>
		
		<td colspan="5"><?php echo $MOD_BAKERY['TXT_DECIMAL']; ?>: 
			<input name="dec_point" type="text" style="width: 10px; text-align: center;" value="<?php echo stripslashes($fetch_settings['dec_point']); ?>" maxlength="1" /> &nbsp;&nbsp;&nbsp;
			<?php echo $MOD_BAKERY['TXT_GROUP_OF_THOUSANDS']; ?>: 
			<input name="thousands_sep" type="text" style="width: 10px; text-align: center;" value="<?php echo stripslashes($fetch_settings['thousands_sep']); ?>" maxlength="1" />		</td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_TAX']; ?>:</td>
		<td colspan="4">
			<input type="radio" name="tax_by" id="tax_by_country" value="country"<?php if ($fetch_settings['tax_by'] == 'country') { echo ' checked="checked"'; } ?> />
			<label for="tax_by_country"><?php echo $MOD_BAKERY['TXT_SHOP_COUNTRY']; ?></label>&nbsp;&nbsp;&nbsp;
			<input type="radio" name="tax_by" id="tax_by_state" value="state"<?php if ($fetch_settings['tax_by'] == 'state') { echo ' checked="checked"'; } ?><?php if (!$use_states) { echo " disabled='disabled'"; } ?> />
			<label for="tax_by_state"<?php if (!$use_states) { echo " class='mod_bakery_disabled_b'"; } ?>><?php echo $MOD_BAKERY['TXT_SHOP_STATE']; ?></label>&nbsp;&nbsp;&nbsp;
			<input type="radio" name="tax_by" id="tax_by_none" value="none"<?php if ($fetch_settings['tax_by'] == 'none') { echo ' checked="checked"'; } ?> />
			<label for="tax_by_none"><?php echo $TEXT['NONE']; ?></label>		</td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_TAX_RATE']." ".$MOD_BAKERY['TXT_ITEM']; ?>:</td>
		<td width="13%">
			1.<input type="text" name="tax_rate" size="5" maxlength="5" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['tax_rate']); ?>" />%</td>
        <td width="13%">
			2.<input type="text" name="tax_rate1" size="5" maxlength="5" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['tax_rate1']); ?>" />%</td>
	    <td width="13%">
			3.<input type="text" name="tax_rate2" size="5" maxlength="5" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['tax_rate2']); ?>" />%</td>
	    <td><input type="checkbox" name="tax_included" id="tax_included" value="included" <?php if ($fetch_settings['tax_included'] == 'included') { echo 'checked="checked"'; } ?> />
			<label for="tax_included"><?php echo $MOD_BAKERY['TXT_TAX_INCLUDED']; ?></label>
		</td>
	</tr>

	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHOW_TAX_NO_FIELD']; ?>:</td>
		<td colspan="4">
	  	  <input type="checkbox" name="tax_no_field" id="tax_no_field" value="show" <?php if ($fetch_settings['tax_no_field'] == 'show') { echo 'checked="checked"'; } ?> onclick="javascript: sync_checkboxes(this);" />
		</td>
	</tr>

	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_TAX_GROUP']; ?>:</td>
		<td colspan="4">
	  	  <input type="text" name="tax_group" id="tax_group" value="<?php echo stripslashes($fetch_settings['tax_group']); ?>" style="width: 98%" />
	  	  <?php
			if (!extension_loaded('soap')) {
				echo '<span style="color: red;">To take advantage of the VAT-No check please make sure the <a href="http://php.net/manual/en/book.soap.php" target="_blank">soap extension</a> is loaded.</span>';
			}
	  	  ?>
		</td>
	</tr>

<!-- Shipping -->
	<tr valign="bottom">
	  <td width="30%" height="32" align="right"><strong><?php echo $MOD_BAKERY['TXT_SHIPPING']." ".$MOD_BAKERY['TXT_SETTINGS']; ?>:</strong></td>
	  <td height="32" colspan="4">&nbsp;</td>
    </tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_TAX_RATE']." ".$MOD_BAKERY['TXT_SHIPPING']; ?>:</td>
	  <td colspan="4">
		  <input type="text" name="tax_rate_shipping" size="5" maxlength="5" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['tax_rate_shipping']); ?>" />%</td>
	</tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_FREE_SHIPPING']." ".$MOD_BAKERY['TXT_OVER']; ?>:</td>
	  <td colspan="4">
	  	<input type="text" name="free_shipping" size="8" maxlength="8" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['free_shipping']); ?>" /><?php echo stripslashes($fetch_settings['shop_currency']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
		<input type="checkbox" name="free_shipping_msg" id="free_shipping_msg" value="show" <?php if ($fetch_settings['free_shipping_msg'] == 'show') { echo 'checked="checked"'; } ?> />
		<label for="free_shipping_msg"><?php echo $MOD_BAKERY['TXT_SHOW_FREE_SHIPPING_MSG']; ?></label></td>
    </tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_SHIPPING_BASED_ON']; ?>:</td>
	  <td colspan="4">
	    <select name="shipping_method">
	      <option value="flat" <?php if ($fetch_settings['shipping_method'] == "flat") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_METHOD_FLAT']; ?></option>
	      <option value="items" <?php if ($fetch_settings['shipping_method'] == "items") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_METHOD_ITEMS']; ?></option>
	      <option value="positions" <?php if ($fetch_settings['shipping_method'] == "positions") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_METHOD_POSITIONS']; ?></option>
	      <option value="percentage" <?php if ($fetch_settings['shipping_method'] == "percentage") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_METHOD_PERCENTAGE']; ?></option>
	      <option value="highest" <?php if ($fetch_settings['shipping_method'] == "highest") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_METHOD_HIGHEST']; ?></option>
		  <option value="none" <?php if ($fetch_settings['shipping_method'] == "none") { echo 'selected="selected"'; } ?> >
		  <?php echo $MOD_BAKERY['TXT_SHIPPING_METHOD_NONE']; ?></option>
        </select>	  </td>
    </tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHIPPING']." ".$MOD_BAKERY['TXT_DOMESTIC']; ?>:</td>
		<td width="13%">
			<input type="text" name="shipping_domestic" size="6" maxlength="7" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['shipping_domestic']); ?>" /><?php if ($fetch_settings['shipping_method'] != "percentage") { echo stripslashes($fetch_settings['shop_currency']); } else { echo "%"; } ?></td>
	    <td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="30%" align="right"><?php echo $MOD_BAKERY['TXT_SHIPPING']." ".$MOD_BAKERY['TXT_ABROAD']; ?>:</td>
		<td>
			<input type="text" name="shipping_abroad" size="6" maxlength="7" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['shipping_abroad']); ?>" /><?php if ($fetch_settings['shipping_method'] != "percentage") { echo stripslashes($fetch_settings['shop_currency']); } else { echo "%"; } ?></td>
		<td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	  <td align="right"><?php echo $MOD_BAKERY['TXT_SHIPPING']; ?>:</td>
	  <td><input type="text" name="shipping_zone" size="6" maxlength="7" style="text-align: right;" value="<?php echo stripslashes($fetch_settings['shipping_zone']); ?>" /><?php if ($fetch_settings['shipping_method'] != "percentage") { echo stripslashes($fetch_settings['shop_currency']); } else { echo "%"; } ?></td>
	  <td colspan="3">... <?php echo $MOD_BAKERY['TXT_ZONE_COUNTRIES']; ?>:</td>
    </tr>
	<tr>
		<td width="30%" align="right">&nbsp;</td>
		<td>&nbsp;</td>
        <td colspan="3">
		<?php
			$zone_countries = explode(",", stripslashes($fetch_settings['zone_countries']));
			echo "<select name='zone_countries[]' size='3' multiple='multiple'>"; 
			for ($n = 1; $n <= count($MOD_BAKERY['TXT_COUNTRY_NAME']); $n++) {
				$country = $MOD_BAKERY['TXT_COUNTRY_NAME'][$n];
				$country_code = $MOD_BAKERY['TXT_COUNTRY_CODE'][$n];
				if ($country_code != stripslashes($fetch_settings['shop_country'])) {
					echo "<option value='$country_code'";
					if (in_array($country_code, $zone_countries)) {
						echo ' selected="selected"';
					}
				}
				echo ">$country</option>\n";
			}
		echo "</select>"; ?></td>
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
