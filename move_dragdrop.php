<?php
/*
	Drag'N'Drop Position
*/

if (! isset($_POST['action']) || !isset($_POST['row']) ) { 
	header('Location: ../../index.php');	
}

else {
    
	require('../../config.php');

	// Check if user has permissions to access the Bakery module
	require_once('../../framework/class.admin.php');
	$admin = new admin('Modules', 'module_view', false, false);
	if (! ($admin->is_authenticated() && $admin->get_permission('bakery', 'module'))) 
		die(header('Location: ../../index.php'));
	
	// Sanitize variable
	$action = $admin->add_slashes($_POST['action']);
	// We just get the array here, and few lines below we sanitize it
	$row = $_POST['row'];	
	$sID = $database->get_one("SELECT `section_id` FROM `".TABLE_PREFIX."mod_bakery_items` WHERE `item_id` = ".intval($row[0]));
	
	
	/*
	Bakery isn't using ordering (ASC/DESC) so we comment this code

	$sorting = $database->get_one("SELECT `ordering` FROM `".TABLE_PREFIX."bakery_settings` WHERE `section_id` = ".$sID." ");
	if($sorting == 1) // DESC == new first
	{
		$row = array_reverse($row);
	}
	*/
	 
	// For security reasons (to prevent db hacks) this line verifies that
	// in the $action var there is no other text than "updatePosition"
	if ($action == "updatePosition") {
		$i = 1;
		foreach ($row as $recID) {
			// Sanitize array
			$recID = $admin->add_slashes($recID);
			$database->query("UPDATE `".TABLE_PREFIX."mod_bakery_items` SET `position` = ".$i." WHERE `item_id` = ".$recID." ");
			$i++;
		}
	
		// Include ordering class and reorder the entries
		require_once(WB_PATH.'/framework/class.order.php');
		$order = new order(TABLE_PREFIX.'mod_bakery_items', 'position', 'item_id', 'section_id');
		$order->clean($sID);

		// Now we can print the result in green field
		echo '<img src="'.WB_URL.'/modules/bakery/images/ajax-loader.gif" alt="" border="0" />';
	}
}
