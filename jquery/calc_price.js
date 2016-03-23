
/*
  Module developed for the Open Source Content Management System WebsiteBaker (http://websitebaker.org)
  Copyright (C) 2007 - 2016, Christoph Marti

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


// *********************************************************************
// jQuery plug-in
// Calculate price change depending on option selection
// *********************************************************************

(function($) {
	$.fn.calcPrice = function() {

		// Get basic price and keep it stored in global var
		if (typeof basic_price === 'undefined') {
			basic_price = container.text().replace(/\D/g, '');
			basic_price = parseFloat(basic_price / 100);
		}
		var price = basic_price;

		// Loop through selected options
		$.each(this, function(key, value) {
			var sel_option = $(value);
			// Split the selected option into attribute / currency / sign / price
			arr = sel_option.text().match(/(.*)([A-Z]{3}) ([+,-]?)(.*)/);

			if (arr) {
				var sign      = arr[3];
				var opt_price = parseFloat(arr[4]);
				// Calculate new price
				if (sign == '+') {
				    price = price + opt_price;
				} else if (sign == '-') {
				    price = price - opt_price;
				} else {
					price = opt_price;
				}
			}
		});

		// Money number format
		// Adapted from: http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript
		var n = price,
		c = 2,
		d = decimal_sep,
		t = thousands_sep,
		sign = (n < 0) ? '-' : '',
		i = parseInt(n = Math.abs(n).toFixed(c)) + '',
		j = ((j = i.length) > 3) ? j % 3 : 0;

		new_price = sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : ''); 

		// Refresh item price on page
		container.html(currency + ' ' + new_price);

	};
})(jQuery);

