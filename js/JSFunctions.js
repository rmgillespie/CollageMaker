// Google plus one function
(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();


/* Redirect Time functions */
var Timer = 4;

function Redirect(URL) {
	location.href = URL;
}

function Change_Timer_Value() {
	if (Timer == 1) {
		clearInterval(IntervalID);
	}
	Timer = Timer - 1;
	document.getElementById("Timer").innerHTML = Timer;
}
/* End of Redirect Time functions */


/* Table search and sort functions*/
$(function () {
	$('input#id_search').quicksearch('table tbody tr');
});
	
	
$(document).ready(function() { // Call the tablesorter plugin 
	$("#FacebookFriends").tablesorter({ // sort on the first column
		sortList: [[0,0]]
	});
});
/* End of table search and sort functions*/


/* Loading overlay functions*/
function ToggleLoadingOverlay() {
	if (document.getElementById('Overlay').style.display == "none") {
		document.getElementById('Overlay').style.display = "block";
		document.getElementById('Spinner').style.display = "block";
	} else {
		document.getElementById('Overlay').style.display = "none";
		document.getElementById('Spinner').style.display = "none";
	}
}	
/* End of loading overlay functions*/
	
	
/* Generic functions */
function ValueExists(ArrayObj, SearchFor) {
	for (var i = 0; i < ArrayObj.length; i++) {
		if (ArrayObj[i] == SearchFor) {
			return true;
			var Found = true;
			break;
		} else if ((i == (ArrayObj.length - 1)) && (!Found)) {
			if (ArrayObj[i] != SearchFor) {
				return false;
			}
		}
	}
}
/* End of generic functions */


/* Form functions */
function radioSizeChanged(input) {
	document.getElementById('height').value = input.value;
	document.getElementById('width').value = input.value;
}

var SelectedFriendIDs = new Array();
function constructFriendIDArray(UserID) {
	SelectedFriendIDs.push(UserID);
}

function MyPhotosCheckBoxModifed(UserID) {
	if (document.getElementById('usercheckbox').checked) {
		SelectedFriendIDs.push(UserID);
	} else {
		var IndexOfID = SelectedFriendIDs.indexOf(UserID); 
		SelectedFriendIDs.splice(IndexOfID, 1); 
	}
	FriendArrayModifed();
}

function GetRowID(t) {
var ID = t.parentNode.id;	
if (ValueExists(SelectedFriendIDs, ID)) { // If ID already exists
	SelectedFriendIDs.splice(SelectedFriendIDs.indexOf(ID), 1); // Remove ID of firend
	$("#".concat(ID)).addClass('NotSelected').removeClass('Selected');
	$("#".concat(ID).concat("Cell")).addClass('FriendCellNotSelected').removeClass('FriendCellSelected');
} else {
	SelectedFriendIDs.push(ID);
	$("#".concat(ID)).addClass('Selected').removeClass('NotSelected');
	$("#".concat(ID).concat("Cell")).addClass('FriendCellSelected').removeClass('FriendCellNotSelected');
}
FriendArrayModifed();
event.preventDefault();
}

function Done(Action) {
	var form = document.createElement("form");
	form.setAttribute("method", "post");
	form.setAttribute("action", Action);

	var SelectedFriendsList = "";
	for (var i = 0; i < SelectedFriendIDs.length; i++) {
		if (i == SelectedFriendIDs.length-1) {
			SelectedFriendsList += SelectedFriendIDs[i];
		} else {
			SelectedFriendsList += SelectedFriendIDs[i] + ",";
		}
	}
	
	var selectedfriendsfield = document.createElement("input");
	selectedfriendsfield.setAttribute("type", "hidden");
	selectedfriendsfield.setAttribute("name", "selectedfriends");
	selectedfriendsfield.setAttribute("value", SelectedFriendsList);
	form.appendChild(selectedfriendsfield);

	var blackandwhitefield = document.createElement("input");
	blackandwhitefield.setAttribute("type", "hidden");
	blackandwhitefield.setAttribute("name", "blackandwhite");
	blackandwhitefield.setAttribute("value", document.getElementById('blackandwhite').checked);
	form.appendChild(blackandwhitefield);

	var onlytaggedphotosfield = document.createElement("input");
	onlytaggedphotosfield.setAttribute("type", "hidden");
	onlytaggedphotosfield.setAttribute("name", "onlytaggedphotos");
	onlytaggedphotosfield.setAttribute("value", document.getElementById('onlytaggedphotos').checked);
	form.appendChild(onlytaggedphotosfield);

	var widthfield = document.createElement("input");
	widthfield.setAttribute("type", "hidden");
	widthfield.setAttribute("name", "width");
	widthfield.setAttribute("value", document.getElementById('width').value);
	form.appendChild(widthfield);

	var heightfield = document.createElement("input");
	heightfield.setAttribute("type", "hidden");
	heightfield.setAttribute("name", "height");
	heightfield.setAttribute("value", document.getElementById('height').value);
	form.appendChild(heightfield);

	var bordercolorfield = document.createElement("input");
	bordercolorfield.setAttribute("type", "hidden");
	bordercolorfield.setAttribute("name", "bordercolor");
	bordercolorfield.setAttribute("value", document.getElementById('bordercolor').value);
	form.appendChild(bordercolorfield);

	var bordersizefield = document.createElement("input");
	bordersizefield.setAttribute("type", "hidden");
	bordersizefield.setAttribute("name", "bordersize");
	bordersizefield.setAttribute("value", document.getElementById('bordersize').value);
	form.appendChild(bordersizefield);

	var sendemailfield = document.createElement("input");
	sendemailfield.setAttribute("type", "hidden");
	sendemailfield.setAttribute("name", "sendemail");
	sendemailfield.setAttribute("value", document.getElementById('sendemail').checked);
	form.appendChild(sendemailfield);
	document.body.appendChild(form);
	
	var emailfield = document.createElement("input");
	emailfield.setAttribute("type", "hidden");
	emailfield.setAttribute("name", "email");
	emailfield.setAttribute("value", document.getElementById('email').value);
	form.appendChild(emailfield);
	document.body.appendChild(form);

	var rowsfield = document.createElement("input");
	rowsfield.setAttribute("type", "hidden");
	rowsfield.setAttribute("name", "rows");
	rowsfield.setAttribute("value", document.getElementById('rows').value);
	form.appendChild(rowsfield);
	document.body.appendChild(form);
	
	ToggleLoadingOverlay();
	form.submit();
}

var WidthValid = true; var HeightValid = true; var BorderValud = false; var EmailValid = false; var RowsValid = true;
function FriendArrayModifed() {
	if (SelectedFriendIDs.length >= 1) {
		document.getElementById('Output').innerHTML = '';
		if (document.getElementById('sendemail').checked) { 
			TempEmailValidation = EmailValid;
		} else {
			TempEmailValidation = true;
		}
	
		if ((WidthValid)&&(HeightValid)&&(RowsValid)&&(TempEmailValidation)&&(SelectedFriendIDs.length >= 1)) {
			document.getElementById('submit').disabled = false;
		} else {
			document.getElementById('submit').disabled = true;
		}
	} else {
		document.getElementById('Output').innerHTML = 'Please select 1 or more friends.';
		document.getElementById('submit').disabled = true;
	}
}


function Validate(input) {
	ID = input.id;
	Element = document.getElementById(ID);

	switch (ID) {
		case "width": 
			if ((input.value <= 1980) && (input.value >= 800)) {
				WidthValid = true;
				Element.style.color = '#3B7DC1';
				document.getElementById('Output').innerHTML = '';
			} else {
				WidthValid = false;
				Element.style.color = '#FF0000';
				document.getElementById('Output').innerHTML = 'Please enter a width between 840 and 1980 pixels.';
			}
			break;
		case "height": 
			if ((input.value <= 1980)&&(input.value >= 800)) {
				HeightValid = true;
				Element.style.color = '#3B7DC1';
				document.getElementById('Output').innerHTML = '';
			} else {
				HeightValid = false;
				Element.style.color = '#FF0000';
				document.getElementById('Output').innerHTML = 'Please enter a height between 840 and 1980 pixels.';
			}	
			break;
		case "bordersize":
			if ((input.value <= 10)&&(input.value >= 0)) {
				BorderValid = true;
				Element.style.color = '#3B7DC1';
				document.getElementById('Output').innerHTML = '';
			} else {
				BorderValid = false;
				Element.style.color = '#FF0000';
				document.getElementById('Output').innerHTML = 'Please enter a border size between 0 and 10 pixels.';
			}
			break;
		case "email":
			if (/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(input.value)) {
				EmailValid = true;
				Element.style.color = '#000';
				document.getElementById('Output').innerHTML = '';
			} else {
				EmailValid = false;
				Element.style.color = '#FF0000';
				document.getElementById('Output').innerHTML = 'Please enter a valid email address.';
			}
			break;
		case "rows":
			if ((input.value <= 10)&&(input.value >= 1)) {
				RowsValid = true;
				Element.style.color = '#3B7DC1';
				document.getElementById('Output').innerHTML = '';
			} else {
				RowsValid = false;
				Element.style.color = '#FF0000';
				document.getElementById('Output').innerHTML = 'Please enter a value between 1 and 10.';
			}
			break;
	}

	SubmitButton = document.getElementById('submit');
	if (document.getElementById('sendemail').checked) { 
		TempEmailValidation = EmailValid;
	} else {
		TempEmailValidation = true;
	}
	
	if ((WidthValid)&&(HeightValid)&&(RowsValid)&&(TempEmailValidation)&&(SelectedFriendIDs.length >= 1)) {
		SubmitButton.disabled = false;
	} else {
		SubmitButton.disabled = true;
	}
}


function SendEmailChanged() {
	if (document.getElementById('sendemail').checked) { //True if the user wants to send an email
		document.getElementById('EmailField').style.display = 'block';
	} else {
		document.getElementById('EmailField').style.display = 'none';
	}
	
	SubmitButton = document.getElementById('submit');
	if (document.getElementById('sendemail').checked) { 
		TempEmailValidation = EmailValid;
	} else {
		TempEmailValidation = true;
	}
	
	if ((WidthValid)&&(HeightValid)&&(RowsValid)&&(TempEmailValidation)&&(SelectedFriendIDs.length >= 1)) {
		SubmitButton.disabled = false;
	} else {
		SubmitButton.disabled = true;
	}
}