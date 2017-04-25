<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2017, Christoph Marti

  LICENCE TERMS:
  This module is free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License  - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  DISCLAIMER:
  This module is distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/


// Generate a PNG thumbnail from an image
function make_thumb_png($source, $destination, $size) {

	// Check if GD is installed
	if (extension_loaded('gd') AND function_exists('imagecreatefrompng')) {
		// First figure out the size of the thumbnail
		list($original_x, $original_y) = getimagesize($source);
		if ($original_x > $original_y) {
			$thumb_w = $size;
			$thumb_h = $original_y * ($size / $original_x);
		}
		if ($original_x < $original_y) {
			$thumb_w = $original_x * ($size / $original_y);
			$thumb_h = $size;
		}
		if ($original_x == $original_y) {
			$thumb_w = $size;
			$thumb_h = $size;	
		}
		// Now make the thumbnail
		$source  = imagecreatefrompng($source);
		$dst_img = imagecreatetruecolor($thumb_w, $thumb_h);
		// Allow png transparency (full alpha channel information)
		imagealphablending($dst_img, false);
		imagesavealpha($dst_img, true);
		// Resizing
		imagecopyresampled($dst_img, $source, 0,0,0,0, $thumb_w, $thumb_h, $original_x, $original_y);
		imagepng($dst_img, $destination);
		// Clear memory
		imagedestroy($dst_img);
		imagedestroy($source);
		// Return true
		return true;
	} else {
		return false;
	}
}

?>