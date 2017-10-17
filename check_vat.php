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



// Check EU vat numbers
function check_vat($vat, $tax_group) {

	// No check if soap extension is not laoded
	if (!extension_loaded('soap')) {
        return true;
	}

	// No check if vat number string has been left empty
    if (empty($vat)) {
        return true;
	}

	// Clean vat number string
	$invalid_chars = array(chr(0), chr(9), chr(10), chr(11), chr(13), chr(173));
	$vat           = str_replace($invalid_chars, '', $vat);

	// Split country code and vat number
    $country_code = strtoupper(substr($vat, 0, 2));
    $vat_no       = substr($vat, 2);
    
    // Country code must make part of the EU tax zone
    if (strpos($tax_group, $country_code) === false) {
    	return false;
    }
    
    // Number part can not be empty
    if (empty($vat_no)) {
    	return false;
    }

	// Check vat using SOAP
	$result   = null;
	$wsdl_url = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';
	try {  
	    $soap   = @new SoapClient($wsdl_url, array('exceptions' => 1));
	    $result = $soap->checkVat(array('countryCode' => $country_code, 'vatNumber' => $vat_no));
	} catch(SoapFault $E) {  
	    echo '<div class="mod_bakery_error_f"><p>'.$E->faultstring.'</p></div>'; 
	}
    if (isset($result->valid) && !$result->valid) {
        return false;
	}
	return true;
}
