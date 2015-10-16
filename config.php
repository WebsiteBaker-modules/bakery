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


//Prevent this file from being accessed directly
if (defined('WB_PATH') == false) {
	exit("Cannot access this file directly"); 
}




// **********************************************
// SET DEFAULT VALUES OF SOME ADDITIONAL SETTINGS
// **********************************************


// FORMS (FRONTEND)
// ****************

// List of special chars that are accepted by the address form fields (added to the regexp)
$add_chars = 'ÁÀÂÃÄÅáàâãäåÆæÇçČčÐðÉÈÊËéèêëÍÌÎÏíìîïÑñÓÒÔÕÖØóòôõöøŒœÞþÚÙÛÜúùûüŠšßÝŸýÿŽž°';



// CART (FRONTEND)
// ****************

// Default cart thumb max. size (px)
$cart_thumb_max_size = 40;



// TEMPLATES (FRONTEND)
// ********************

// For item detail templates built with a table wrap selects in a table row
// Affects item options selects => [OPTION] placeholder
$use_table = TRUE;

// On item detail pages chop long pagination links and add … (horizontal ellipsis)
// Number of allowed chars before chopping link text
$link_length = 24;




// IMAGES AND THUMBNAILS (BACKEND)
// *******************************

// Name of the media subfolder that contains the Bakery images and thumbs
// No more than a proper directory name - no leading nor trailing slash
$img_dir = 'bakery'; 

// Selectable thumbnail default sizes (modify page settings)
$default_thumb_sizes['40']  = '40x40px';
$default_thumb_sizes['50']  = '50x50px';
$default_thumb_sizes['60']  = '60x60px';
$default_thumb_sizes['75']  = '75x75px';
$default_thumb_sizes['100'] = '100x100px';
$default_thumb_sizes['125'] = '125x125px';
$default_thumb_sizes['150'] = '150x150px';

// Accepted max lenght of image filenames (modify item)
$filename_max_length = 40;

// For item images set image resize default values (modify item)
$fetch_item['imgresize'] = '';  // yes = selected by default
$fetch_item['quality']   = 75;
$fetch_item['maxwidth']  = 400;
$fetch_item['maxheight'] = 300;






