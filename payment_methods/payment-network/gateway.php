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

// Include info file
include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/info.php');

// Look for payment method language file
if (LANGUAGE_LOADED) {
    include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
    if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
        include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
    }
}

// Payment network logo
$logo              = LANGUAGE == 'DE' ? 'logo_de' : 'logo';
// Payment network security info link
$security_info_url = isset($security_info[LANGUAGE]) ? $security_info[LANGUAGE] : $security_info['EN'];
?>


	<tr>
	  <td colspan="2"><h3 class="mod_bakery_pay_h_f"><?PHP echo $MOD_BAKERY[$payment_method]['TXT_TITLE']; ?> <img src="<?php echo WB_URL ?>/modules/bakery/payment_methods/<?php echo $payment_method ?>/<?php echo $logo ?>.png" alt="Logo <?php echo $payment_method_name ?>" width="112" height="30" /></h3></td>
	</tr>
	<tr>
	  <td colspan="2" class="mod_bakery_pay_td_f"><?PHP echo $MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1']; ?><br />
		<?PHP echo $MOD_BAKERY[$payment_method]['TXT_SECURITY']; ?><a href="<?php echo $security_info_url ?>" target="_blank"> &raquo; <?PHP echo $MOD_BAKERY[$payment_method]['TXT_WEBSITE']; ?></a>.</td>
	</tr>
	<tr>
	  <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr align="left" valign="top">
			<td width="50%" class="mod_bakery_pay_td_f"><b>1</b>.<br />
			  <?PHP echo $MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2']; ?><br />
			  <br />
			  <b>2</b>.<br />
			  <?PHP echo $MOD_BAKERY[$payment_method]['TXT_SECURE']; ?></td>
			<td width="50%" class="mod_bakery_pay_td_f"><b>3</b>.<br />
			  <?PHP echo $MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE']; ?><br />
			  <br />
			  <b>4</b>.<br />
			  <?PHP echo $MOD_BAKERY[$payment_method]['TXT_SHIPMENT']; ?></td>
		  </tr>
		</table></td>
	</tr>
	<tr>
	  <td colspan="2" class="mod_bakery_pay_submit_f">
		<input type="submit" name="payment_method[<?php echo $payment_method ?>]" class="mod_bakery_bt_pay_<?php echo $payment_method ?>_f" value="<?php echo $MOD_BAKERY[$payment_method]['TXT_PAY']; ?>" onclick="javascript: return checkTaC()" />
	  </td>
	</tr>
	<tr>
	  <td colspan="2"><hr class="mod_bakery_hr_f" /></td>
	</tr>
