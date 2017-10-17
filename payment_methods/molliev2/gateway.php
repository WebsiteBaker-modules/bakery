<?php
/**
 *
 * @category        Bakery Payment_method
 * @package         Mollie (NL) API version
 * @author          Dev4me - Ruud Eisinga
 * @link			http://www.dev4me.nl/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.3 and higher
 * @version         1.1
 * @lastmodified    July 12, 2016
 *
 */

// Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}
include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/info.php');
include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/EN.php');
if (file_exists(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php')) {
	include(WB_PATH.'/modules/bakery/payment_methods/'.$payment_method.'/languages/'.LANGUAGE.'.php');
}

// Check if there has been a payment method error
$pm_error_msg = '';
$pay_error = isset($_GET['pay_error']) ? $_GET['pay_error'] : 0;
switch ($pay_error) {
	case 1:
		$pm_error_msg = "<tr>\n<td colspan='2'><div class='mod_bakery_error_f' style='margin-bottom: 10px'><p>{$MOD_BAKERY[$payment_method]['ERROR_CREATING_PM']}</p></div>\n</td>\n</tr>";
		break;
	
	case 2:
		$pm_error_msg = "<tr>\n<td colspan='2'><div class='mod_bakery_error_f' style='margin-bottom: 10px'><p>{$MOD_BAKERY[$payment_method]['ERROR_NO_BANK_SELECTED']}</p></div>\n</td>\n</tr>";
		break;
}

//if(!isset($_SESSION['methods'])) { // Fetch current payment options
	$partner_id = $database->get_one("SELECT value_1 FROM ".TABLE_PREFIX."mod_bakery_payment_methods WHERE directory = '$payment_method'");
	require_once dirname(__FILE__) . "/Mollie/API/Autoloader.php";
	$mollie = new Mollie_API_Client;
	$mollie->setApiKey($partner_id);
	$methods = $mollie->methods->all();
	$pm = '';
	foreach ($methods as $method) $pm .= '<img style="margin:10px 15px;" title="'.htmlspecialchars($method->description).'" src="' . htmlspecialchars($method->image->normal) . '">';
	$_SESSION['methods'] = $pm;
//}
?>
<tr>
	<td colspan="2">
		<h3 class="mod_bakery_pay_h_f"><?php echo $MOD_BAKERY[$payment_method]['TXT_TITLE']; ?></h3>
	</td>
</tr>
<?php echo $pm_error_msg; ?>
<tr>
	<td colspan="2" class="mod_bakery_pay_td_f"><?php echo $MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_1']; ?> <br /></td>
</tr>
<tr>
	<td colspan="2">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr align="left" valign="top">
				<td width="45%" class="mod_bakery_pay_td_f">
					<b>1</b>.<br />
					<?php echo $MOD_BAKERY[$payment_method]['TXT_PAY_ONLINE_2']; ?><br />
					<br />
					<b>2</b>.<br />
					<?php echo $MOD_BAKERY[$payment_method]['TXT_SECURE']; ?><br/>
				</td>
				<td width="45%" class="mod_bakery_pay_td_f">
					<b>3</b>.<br />
					<?php echo $MOD_BAKERY[$payment_method]['TXT_CONFIRMATION_NOTICE']; ?><br />
					<br />
					<b>4</b>.<br />
					<?php echo $MOD_BAKERY[$payment_method]['TXT_SHIPMENT']; ?>
				</td>
				<td>
					<img align="right" src="<?php echo WB_URL ?>/modules/bakery/payment_methods/molliev2/logo_mollie.png" alt="Mollie" />
				<td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" style="text-align: center; padding:20px;">
		<?php echo $_SESSION['methods']; ?>
	</td>
</tr>
<tr>
	<td colspan="2" class="mod_bakery_pay_submit_f">
		<input type="submit" name="payment_method[<?php echo $payment_method ?>]" class="mod_bakery_bt_pay_advance_f" value="<?php echo $MOD_BAKERY[$payment_method]['TXT_PAY']; ?>" onclick="javascript: return checkTaC()" />
	</td>
</tr>
<tr>
	<td colspan="2">
		<hr class="mod_bakery_hr_f" />
	</td>
</tr>
