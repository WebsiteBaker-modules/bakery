/*
  Javascript routines for WebsiteBaker module Bakery
  Copyright (C) 2007 - 2015, Christoph Marti

  This Javascript routines are free software. You can redistribute it and/or modify it 
  under the terms of the GNU General Public License - version 2 or later, 
  as published by the Free Software Foundation: http://www.gnu.org/licenses/gpl.html.

  The Javascript routines are distributed in the hope that it will be useful, 
  but WITHOUT ANY WARRANTY; without even the implied warranty of 
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  GNU General Public License for more details.
*/



// **********************************************************************************
//   Function to change the checkbox status at the same time
//               for all checkboxes having the same name like the clicked one
// **********************************************************************************

function sync_checkboxes(clicked_checkbox) {

	// Get all checkboxes with the same name
	var checkboxes = document.getElementsByName(clicked_checkbox.name);
	// Get state of clicked checkbox and define new state for all checkboxes
	var new_state = false;
	if (clicked_checkbox.checked == true) {
		new_state = true;
	}
	// Loop through all checkboxes and set new state
	for (var i = 0; i < checkboxes.length; ++i) {
		if (checkboxes[i].type == "checkbox") {
			checkboxes[i].checked = new_state;
		}
	}
}



// **********************************************************************************
//   Function to add and remove file type inputs
//   (http://codingforums.com/showthread.php?t=65390)
// **********************************************************************************

function addFile(delTxt) {
	var root = document.getElementById('upload').getElementsByTagName('tr')[0].parentNode;
	var oR   = cE('tr');
	var oC   = cE('td');
	var oI   = cE('input');
	var oS   = cE('span');
	cA(oI,'type','file');
	cA(oI,'name','image[]');
	oS.style.cursor = 'pointer';

	oS.onclick = function() {
		this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
	}

	oS.appendChild(document.createTextNode(delTxt));
	oC.appendChild(oI);
	oC.appendChild(oS);
	oR.appendChild(oC);
	root.appendChild(oR);
}

function cE(el){
	this.obj = document.createElement(el);
	return this.obj;
}

function cA(obj,att,val) {
	obj.setAttribute(att,val);
	return;
}
