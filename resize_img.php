<?php

/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2015, Christoph Marti

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


// Resize PNG image
function resizePNG($source, $destination, $new_max_w, $new_max_h) {

	// Check if GD is installed
	if (extension_loaded('gd') AND function_exists('imagecreatefrompng')) {
		// First figure out the size of the image
		list($orig_w, $orig_h) = getimagesize($source);
		if ($orig_w > $new_max_w) {
			$new_w = $new_max_w;
			$new_h = intval($orig_h * ($new_w / $orig_w));
			if ($new_h > $new_max_h) {
				$new_h = $new_max_h;
				$new_w = intval($orig_w * ($new_h / $orig_h));
			}
		} else if ($orig_h > $new_max_h) {
			$new_h = $new_max_h;
			$new_w = intval($orig_w * ($new_h / $orig_h));
		} else {
			// Image to small to be downsized
			echo "<div align='center'><p style='color: red;'>Image to small to be downsized!</p></div>";
			return false;
		}
		// Now make the image
		$source = imagecreatefrompng($source);
		$dst_img = imagecreatetruecolor($new_w, $new_h);
		imagecopyresampled($dst_img, $source, 0,0,0,0, $new_w, $new_h, $orig_w, $orig_h);
		imagejpeg($dst_img, $destination);
		// Clear memory
		imagedestroy($dst_img);
		imagedestroy($source);
		// Return true
		return true;
	} else {
   	return false;
	}
}


// Resize JPEG image
function resizeJPEG($source, $new_max_w, $new_max_h, $quality = 75) {

	if ($img = imagecreatefromjpeg($source)) {
		$orig_w = imagesx($img);
		$orig_h = imagesy($img);
		$resize = FALSE;
		$handle;
		if ($orig_w > $new_max_w) {
			$new_w = $new_max_w;
			$new_h = intval($orig_h * ($new_w / $orig_w));
			if ($new_h > $new_max_h) {
				$new_h = $new_max_h;
				$new_w = intval($orig_w * ($new_h / $orig_h));
			}
			$resize = TRUE;
		} else if ($orig_h > $new_max_h) {
			$new_h = $new_max_h;
			$new_w = intval($orig_w * ($new_h / $orig_h));
			$resize = TRUE;
		} else {
			// Image cant be downsized
			echo "<div align='center'><p style='color: red;'>Image to small to be downsized!</p></div>";
			return false;
		}

		if ($resize) {
			// Resize using appropriate function
			if (function_exists('imagecopyresampled')) {
				$imageId = imagecreatetruecolor($new_w, $new_h);
				imagecopyresampled($imageId, $img, 0,0,0,0, $new_w, $new_h, $orig_w, $orig_h);
			} else {
				$imageId = imagecreate($new_w , $new_h);
				imagecopyresized($imageId, $img, 0,0,0,0, $new_w, $new_h, $orig_w, $orig_h);
			}
			$handle = $imageId;
			// Free original image
			imagedestroy($img);
		} else {
			$handle = $img;
		}
		imagejpeg($handle, $source, $quality);
		imagedestroy($handle);
	}
}
