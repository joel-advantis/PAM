/******************************************************************************
 *  PATROL Agent Manager
 *
 *  JavaScript Function Library
 *
 *  Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved.
 ********************************************************************************/
 
 
function Trim(orgString){
  return LTrim(RTrim(orgString))
}

function LTrim(orgString){
  return orgString.replace(/^\s+/,'')
}

function RTrim(orgString){
  return orgString.replace(/\s+$/,'')
}

//function login(f) {

//	f['md5'].value = hex_hmac_md5(f['userName'].value, f['password'].value);
//	f['password'].value = '';
//	return true;
//}

function disable_button(f) {

	f.disabled = true;

}

function enable_button(f) {

	f.disabled = false;

}

function change_db_password(f) {
	
	oldPassword = Trim(f.password.value);
	newPassword = Trim (f.newPassword.value);
	newPasswordConfirm = Trim (f.newPasswordConfirm.value);

	if ((oldPassword == "") || (newPassword == "") || (newPasswordConfirm == "")) {
		alert ("All fields are required");
		return false;
	}

	if (newPassword != newPasswordConfirm) {
		alert ("New Password doesn't match");
		return false;
	}
	if (newPassword == oldPassword) {
		alert ("New password cannot be the same as old password")
		return false;
	}
	// Check the newpassword field - 4 characters ... 
	passwordLength = newPassword.length;
	if ((passwordLength < 4) || (passwordLength > 32)) {
		alert ("Password should be between 4 and 32 characters")
		return false;
	}
	f.passwordChange.value = 1;
	return true;
}

function change_password(f) {

	oldPassword = Trim(f.password.value);
	newPassword = Trim (f.newPassword.value);
	newPasswordConfirm = Trim (f.newPasswordConfirm.value);
	userName = Trim (f.userName.value);

	if ((oldPassword == "") || (newPassword == "") || (newPasswordConfirm == "")) {
		alert ("All fields are required");
		return false;
	}

	if (newPassword != newPasswordConfirm) {
		alert ("New Password doesn't match");
		return false;
	}
	if (newPassword == oldPassword) {
		alert ("New password cannot be the same as old password")
		return false;
	}
	// Check the newpassword field - 4 characters ... 
	passwordLength = newPassword.length;
	if ((passwordLength < 4) || (passwordLength > 10)) {
		alert ("Password should be between 4 and 10 characters")
		return false;
	}

	f['md5'].value = hex_hmac_md5(f['userName'].value, f['password'].value);
	f['password'].value = '';

	f['md5NewPassword'].value = hex_hmac_md5(f['userName'].value, f['newPassword'].value);
	f['newPassword'].value = '';

	f['newPasswordConfirm'].value = '';

	f.passwordChange.value = 1;
	return true;
}

function rejectRules(field) {

	theForm    = document.forms[0];
	theForm2    = document.forms[1];
	Approvercomment = "";
	Approvercomment = Trim (theForm.Approvercomment.value);

	if (Approvercomment == "") {
		alert ("You need to enter a comment reject a request");
		return false;
	}
	if (confirm("Are you sure you want to reject the selected request?")) {

		theForm2.requestToReject.value = theForm.requestId.value;
		theForm2.Approvercomment.value = theForm.Approvercomment.value;
		theForm2.submit();
	}
	else {
		return false;
	}
}


function approveRules(field) {

	theForm    = document.forms[0];
	theForm2   = document.forms[1];

	requestToApprove = "";

	for (i = 0; i < field.length; i++) {
		if (field[i].checked) {
			requestToApprove = field[i].value;
		}
	}

	if (requestToApprove == "") {

		if (field.checked) {
			requestToApprove = field.value;
		}
		if (!requestToApprove) {
			alert ("No requests selected");
			return false;
		}
	}

	Approvercomment = Trim (theForm.Approvercomment.value);


	if (Approvercomment == "") {
		alert ("You need to enter a comment to submit it for approval");
		return false;
	}

	theForm2.requestId.value = requestToApprove;
	theForm2.Approvercomment.value = Approvercomment;
	theForm2.submit();

}

function populate_ruleset () {

	theForm   = document.forms[0];
	
	l = theForm.list3; 
	for (var i=0; i<l.options.length; i++) {
		var o = l.options[i];
		if (o.selected) {	
			selection = o.value;
			selection = selection.toString();
			if (o.value != "previous_selection") {
				theForm.enteredRuleset.value = replace (o.value, "__cfg", ".cfg");
				break;
			}
		}
	}
}

function submit_to_pcm() {

	theForm   = document.forms[0];
	theForm2  = document.forms[1];

	finalruleset = "";

	Approvercomment = Trim (theForm.Approvercomment.value);
	if (Approvercomment == "") {
		alert ("You need to enter a comment to approve a request");
		return false;
	}
	finalruleset = Trim (theForm.enteredRuleset.value);
	finalruleset = finalruleset.toString();
	var te = new RegExp(".*\\.cfg$");

	if (!finalruleset.match(te)) {
		alert("Ruleset Should end with .cfg");
		return false;
	}


	if (finalruleset == "") {
		alert ("Ruleset Cannot be Blank, either select a ruleset or enter one manually");
		return false;
	}

	if (confirm("Are you sure you want to approve the selected request and UPDATE PCM?")) {

		theForm2.rulesetName.value = finalruleset;
		theForm2.requestToApprove.value = theForm.requestId.value;
		theForm2.Approvercomment.value = Approvercomment;
		theForm.finalApproval.disabled = true;
		theForm2.submit();
	}
	else {
		return false;
	}
}

function checkAll(field) {
	for (i = 0; i < field.length; i++) field[i].checked = true ;
	field.checked = true;
}

function uncheckAll(field) {
	for (i = 0; i < field.length; i++) field[i].checked = false ;
	field.checked = false;
}

function ChangeLoginLink() {
   var newtitle = "logout";
   var head1 = document.getElementById("loginbanner");
   head1.firstChild.nodeValue=newtitle;
   document.links[0].href = "logout.php";
}

function populate (val) {

	theForm   = document.forms[0];
	if (val == 1) {
		theForm.message.value = theForm.totalmessage.value;
	}
	else {
		theForm.message.value = theForm.messageForHost.value;
	}
}

function validate_message_wording() {

	theForm   = document.forms[0];
	//theForm2  = document.forms[1];

	paramValue = "";
	message = "";
	if (theForm.param) {
		paramValue = theForm.param.value;
	}
	if (theForm.message) {
		message = Trim(theForm.message.value);
	}
	if (paramValue == "") {
		alert ("Parameter must be selected");
		return false;
	}
	if (message == "") {
		alert ("Message Cannot be blank");
		return false;
	}


	disable_button(theForm.submitMsg);
    theForm.newRule.value = 1;
	theForm.submit();
}

function validate_Description() {
	
	theForm    = document.forms[0];
	description = Trim (theForm.description.value);

	paramValue = "";
	if (theForm.paramSelected) {
		paramValue = theForm.paramSelected.value;
	}

	if (paramValue == "") {
		alert ("Parameter must be selected in order to update description");
		return false;
	}

	if (description == "") {
		alert ("Description is Blank");
		return false;
	}

	theForm.updateDescription.value = 1;
	theForm.submit();

}

function validate_threshold() {

	theForm   = document.forms[0];
	//theForm2  = document.forms[0];
	paramValue = "";
	rangeSelected = "";
	errorMessage = "";

	alarm1Min = ""; alarm1Max = "";	alarm1Cycle = "";
	alarm2Min = ""; alarm2Max = "";	alarm2Cycle = "";
	alarm3Min = ""; alarm3Max = "";	alarm3Cycle = "";

	if (theForm.param) {
		paramValue = theForm.param.value;
	}
	
	// Validate that the user selected a parameter
	if (paramValue == "") {
		errorMessage += "Parameter must be selected \n";
	}


	//Check if the parameter is inactive
	boxParam = eval(theForm.paramEnable)
	if (boxParam.checked == true) {
		theForm.paramEnable.value      = "1";
	}
	else {
		theForm.paramEnable.value      = "0";
	}

	// Validate ALARM1
	box1 = eval(theForm.list1)
	if (box1.checked == true) {

		alarm1Min = (theForm.alarm1Min.value);
		if ((alarm1Min == "") || (isNaN(alarm1Min))) {	
			errorMessage += "First Alarm range: Minimum value is empty or not a number \n";	
		}

		if (theForm.alarm1range.value == "0") {
			// in Between got selected
			alarm1Max = (theForm.alarm1Max.value);
			if ((alarm1Max == "") || (isNaN(alarm1Max))) {	
				errorMessage += "First Alarm range: Maximum value is empty or not a number \n";	
			}
			alarm1Min = parseFloat(alarm1Min);
			alarm1Max = parseFloat(alarm1Max);

			if (alarm1Min > alarm1Max) {
				errorMessage += "First Alarm range: Minimum can't be larger than Maximum\n";	
			}
			//theForm2.alarm1Max.value      = theForm.alarm1Max.value;

		}
		alarm1Cycle = Trim (theForm.alarm1Cycle.value);
		if ((alarm1Cycle == "") || (isNaN (alarm1Cycle))) {	
			theForm.alarm1Cycle.value = 1;
		}

	}
	// Validate ALARM2
	box2 = eval(theForm.list2)
	if (box2.checked == true) {

		alarm2Min = Trim (theForm.alarm2Min.value);
		if ((alarm2Min == "") || (isNaN(alarm2Min))) {	
			errorMessage += "Second Alarm range: Minimum value is empty or not a number \n";	
		}
		if (theForm.alarm2range.value == "0") {
			// in Between got selected
			alarm2Max = Trim (theForm.alarm2Max.value);
			if ((alarm2Max == "") || (isNaN(alarm2Max))) {	
				errorMessage += "Second Alarm range: Maximum value is empty or not a number \n";	
			}

			alarm2Min = parseFloat(alarm2Min);
			alarm2Max = parseFloat(alarm2Max);

			if (alarm2Min > alarm2Max) {
				errorMessage += "Second Alarm range: Minimum can't be larger than Maximum\n";	
			}
		}
		alarm2Cycle = Trim (theForm.alarm2Cycle.value);
		if ((alarm2Cycle == "") || (isNaN (alarm2Cycle))) {	
			theForm.alarm2Cycle.value = 1;
		}
	}
	// Validate ALARM3

	box3 = eval(theForm.list3)
	if (box3.checked == true) {
		alarm3Min = Trim (theForm.alarm3Min.value);
		if ((alarm3Min == "") || (isNaN(alarm3Min))) {	
			errorMessage += "Third Alarm range: Minimum value is empty or not a number \n";	
		}
		if (theForm.alarm3range.value == "0") {
			// in Between got selected
			alarm3Max = Trim (theForm.alarm3Max.value);
			if ((alarm3Max == "") || (isNaN(alarm3Max))) {	
				errorMessage += "Third Alarm range: Maximum value is empty or not a number \n";	
			}

			alarm3Min = parseFloat(alarm3Min);
			alarm3Max = parseFloat(alarm3Max);

			if (alarm3Min > alarm3Max) {
				errorMessage += "Third Alarm range: Minimum can't be larger than Maximum \n";	
			}
		}
		alarm3Cycle = Trim (theForm.alarm3Cycle.value);
		if ((alarm3Cycle == "") || (isNaN (alarm3Cycle))) {	
			theForm.alarm3Cycle.value = 1;
		}
	}

	// Cross threshold validation
	if ((alarm1Max != "") && (alarm2Min != "")) {
		if (alarm1Max > alarm2Min) {
			errorMessage += "Maximum value for first alarm range can't be larger than minimum value for second alarm range \n";
		}
	}
	
	if ((alarm1Min != "") && (alarm3Min != "")) {
		if (alarm1Min < alarm3Min) {
			errorMessage += "Minimum value for first alarm range can't be smaller than minimum value for third alarm3 range\n";
		}
	}

	if ((alarm2Min != "") && (alarm3Min != "")) {
		if (alarm2Min < alarm3Min) {
			errorMessage += "Minimum value for second alarm range can't be smaller than minimum value for third alarm range\n";
		}
	}

	if ((alarm1Max != "") && (alarm3Max != "")) {
		if (alarm1Max > alarm3Max) {
			errorMessage += "Maximum value for first alarm range can't be larger than maximum value for third alarm range\n";
		}
	}

	if ((alarm2Max != "") && (alarm3Max != "")) {
		if (alarm2Max > alarm3Max) {
			errorMessage += "Maximum value for second alarm range can't be larger than maximum value for third alarm range\n";
		}
	}

	// End of cross threshold validation
	if (errorMessage != "") {
		alert("Please correct the following problems:\n" + errorMessage);
		return false;
	}

	//theForm2.changeType.value = theForm.changeType.value;
	//theForm2.appClass.value = theForm.appClass.value;
	disable_button(theForm.submitThrehold);
    theForm.newRule.value = 1;
	//theForm2.submit();
	theForm.submit();
}

function validate_polltime_changes() {

	theForm   = document.forms[0];
	// theForm2  = document.forms[1];
	paramValue = "";
	collValue  = "";
	errorMessage = "";
	pollTime = "";

	if (theForm.collParam) {
		paramValue = Trim(theForm.collParam.value);
		// theForm2.collParam.value      = theForm.collParam.value;
		// theForm2.category.value   = theForm.category.value;		
	}
	
	// Validate that the user selected a parameter
	if (paramValue == "") {
		errorMessage += "Collector Parameter must be selected \n";
	}

	//Check if the parameter is inactive
	boxParam = eval(theForm.paramEnable)
	if (boxParam.checked == true) {
		// theForm2.paramEnable.value      = "1";
		theForm.paramEnable.value      = "1";
		// Check if polltime was entered
		pollTime = Trim (theForm.pollTime.value);
		if ((pollTime == "") || (isNaN(pollTime))) {	
			errorMessage += "Polltime is empty or not a number \n";	
		}
		// theForm2.pollTime.value = pollTime;
	}
	else {
		//theForm2.paramEnable.value      = "0";
		theForm.paramEnable.value      = "0";
	}

	if (errorMessage != "") {
		alert("Please correct the following problems " + errorMessage);
		return false;
	}

	//theForm2.changeType.value = theForm.changeType.value;
	//theForm2.appClass.value = theForm.appClass.value;
	disable_button(theForm.submitPoll);
    theForm.newRule.value = 1;
	theForm.submit();

}

function determine_message(field, rowNumber) {
	
	trid = 'row' + rowNumber;

	box = eval(field);
	if (box.checked == true) {
   		document.getElementById(trid).style.display =  "BLOCK";
	}
	else {
   		document.getElementById(trid).style.display =  "none";
	}
}

function hide_alarm_range(field) {
	box = eval(field.paramEnable);

	for (i = 1; i < 4; i++) {

		trid = 'row' + i;
		if (box.checked == true) {
			field.list1.checked = true;
			field.list2.checked = true;
			field.list3.checked = true;
			field.list1.disabled = false;
			field.list2.disabled = false;
			field.list3.disabled = false;
   			document.getElementById(trid).style.display =  "BLOCK";
		}
		else {
			field.list1.checked = false;
			field.list2.checked = false;
			field.list3.checked = false;
			field.list1.disabled = true;
			field.list2.disabled = true;
			field.list3.disabled = true;
   			document.getElementById(trid).style.display =  "none";
		}
	}
}

function hide_pollTime(field) {

	box = eval(field.paramEnable);
	if (box.checked == true) {
   		document.getElementById('row1').style.display =  "BLOCK";
	}
	else {
   		document.getElementById('row1').style.display =  "none";
	}
}


function check_equal(col) {
	theForm   = document.forms[0];
	if (col == 1) {
		equal = theForm.alarm1range.value;
	}
	if (col == 2) {
		equal = theForm.alarm2range.value;
	}
	if (col == 3) {
		equal = theForm.alarm3range.value;
	}

	firstColumn = "col1" + col;
	secondColumn =  "col2" + col;
	if (equal == 1) {
		document.getElementById(firstColumn).style.display =  "none";
		document.getElementById(secondColumn).style.display =  "none";
	}
	else {
		document.getElementById(firstColumn).style.display =  "block";
		document.getElementById(secondColumn).style.display =  "block";
	}
}

function hasOptions(obj) {
	if (obj!=null && obj.options!=null) { 
		return true; 
	}
	return false;
}

function sortSelect(obj) {
	var o = new Array();
	if (!hasOptions(obj)) { 
		return; 
	}
	for (var i=0; i<obj.options.length; i++) {
		o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected) ;
	}
	if (o.length==0) { 
		return; 
	}
	o = o.sort( 
		function(a,b) { 
			if ((a.text+"").toUpperCase < (b.text+"").toUpperCase) { 
				return -1; 
			}
			if ((a.text+"").toUpperCase > (b.text+"").toUpperCase) { 
				return 1; 
			}
			return 0;
			} 
		);

	for (var i=0; i<o.length; i++) {
		obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
	}
}

function moveSelectedOptions(from,to) {

	if (!hasOptions(from)) { 
		return; 
	}
	// Move them over
	
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			to.options[index] = new Option( o.text, o.value, false, false);
		}
	}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
		}
	}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

function moveSelectedGroup(from,to) {

	if (!hasOptions(from)) { 
		return; 
	}
	// Move them over
	
	allSelection = ",";
	for (var i=0; i<to.options.length; i++) {
		var o = to.options[i];
		allSelection = allSelection + "," + o.value;
	}
	allSelection = allSelection + ",";
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if ((o.selected) && (o.value != "previous_selection")) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			var te = new RegExp("," + o.value + ",");
			if (!allSelection.match(te)) {
				to.options[index] = new Option( o.value, o.value, false, false);
			}
		}
	}

	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(to);
	}
	to.selectedIndex = -1;
}

function moveSelectedAgentGroup(from,to) {

	theForm   = document.forms[0];

	if (!hasOptions(from)) { 
		return; 
	}
	// Move them over
	
	allSelection = ",";
	for (var i=0; i<to.options.length; i++) {
		var o = to.options[i];
		allSelection = allSelection + "," + o.value;
	}
	allSelection = allSelection + ",";
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if ((o.selected) && (o.value != "previous_selection")) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			var te = new RegExp("," + o.value + ",");
			if (!allSelection.match(te)) {
				to.options[index] = new Option( o.value, o.value, false, false);
			}
		}
	}

	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(to);
	}
	to.selectedIndex = -1;

	groupsToSubmit = "";
	for (var i=0; i<theForm.list4.options.length; i++) {
		var o = theForm.list4.options[i];
		groupsToSubmit  = groupsToSubmit + " " + o.value;
	}

	theForm.groups.value	    = groupsToSubmit;
    theForm.submit();
}

function moveSelectedParameters(application,from,to) {

	if (!hasOptions(from)) { 
		return; 
	}
	// Move them over
		

	if (from.options[0].value.match(/:/)) { 
		// Parameter with application class
		stripClass = 1;
	} else {
		// Parameter without application class
		stripClass = 0; 
	}
	
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			//alert('text'+o.text);
			//alert('value'+o.value);

			if (!stripClass) { 
				fqParam = application+":"+o.value; 
				oldClass = application;
			}
			else { 
				splitParam = o.value.split(":");
				oldClass = splitParam[0];
				fqParam = splitParam[1]; 				
			}
			
			//alert ('fqParam'+fqParam);

			// Need to confirm with Joel

			// to.options[index] = new Option( o.text, fqParam, false, false);
			
			// Only copy the parameter if it belongs with this application
			if (application == oldClass) {
				to.options[index] = new Option( fqParam, fqParam, false, false);
			}

		}
	}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
		}
	}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

function moveSelectedApps(from,to) {

	if (!hasOptions(from)) { 
		return; 
	}
	// Move them over
		
	if (from.options[0].value.match(/:/)) { 
		// App with trailing slash
		stripClass = 1;
	} else {
		// App without trailing slash
		stripClass = 0; 
	}
	
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
		if (o.selected) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			//alert('text'+o.text);
			//alert('value'+o.value);

			if (!stripClass) { 
				fqParam = o.value+":"; 
			}
			else { 
				splitParam = o.value.split(":");
				fqParam = splitParam[0]; 
			}
			
			//alert ('fqParam'+fqParam);

			to.options[index] = new Option( o.text, fqParam, false, false);
		}
	}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
		}
	}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

function removeSelectedItem(from) {

	if (!hasOptions(from)) { 
		return; 
	}
	
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
		}
	}
}

function removeSelectedGroup(from) {

	theForm    = document.forms[0];

	if (!hasOptions(from)) { 
		return; 
	}
	
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
		if (o.selected) {
			from.options[i] = null;
		}
	}
    theForm.submit();
}

function display_options_for_this (from,to,label) {

	if (!hasOptions(from)) { 
		return; 
	}
	// Move them over

	// reset previous value
	for (var i=0; i<to.options.length; i++) {
		var o = to.options[i];
		if (!hasOptions(from)) { 
			var index = 0; 
		} else { 
			var index=from.options.length; 
		}
		from.options[index] = new Option( o.text, o.value, false, false);
		
	}
	for (var i=(to.options.length-1); i>=0; i--) {
		var o = from.options[i];
		to.options[i] = null;
	}

        	
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
 		var te = new RegExp("\\b" + o.value + "\\b");

		if (label.match(te)) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			to.options[index] = new Option( o.text, o.value, false, false);
		}
	}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
 		var te = new RegExp("\\b" + o.value + "\\b");

		if (label.match(te)) {
			from.options[i] = null;
		}
	}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}

function update_select_without_delete (from,to,label) {

	if (!hasOptions(from)) { 
		return; 
	}

	// reset previous value
        	
	for (var i=0; i<from.options.length; i++) {
		var o = from.options[i];
 		var te = new RegExp("\\b" + o.value + "\\b");

		if (label.match(te)) {
			if (!hasOptions(to)) { 
				var index = 0; 
			} else { 
				var index=to.options.length; 
			}
			to.options[index] = new Option( o.text, o.value, false, false);
		}
	}
	// Delete them from original
	for (var i=(from.options.length-1); i>=0; i--) {
		var o = from.options[i];
 		var te = new RegExp("\\b" + o.value + "\\b");

		if (label.match(te)) {
			from.options[i] = null;
		}
	}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(from);
		sortSelect(to);
	}
	from.selectedIndex = -1;
	to.selectedIndex = -1;
}


function populate_users(userName, firstName, lastName, email, groupName) {

	theForm   = document.forms[0];
	theForm.reset();
	theForm.username.value  = userName;
	theForm.firstname.value = firstName;
	theForm.lastname.value  = lastName;
	theForm.email.value = email;
	display_options_for_this (theForm.list1,theForm.list2,groupName);
	
	
}


function deleteRules(field) {

	theForm    = document.forms[0];
	rulesToDelete = "";

	for (i = 0; i < field.length; i++) {

		box = eval(field[i]);
		if (box.checked == true) {
			rulesToDelete =  rulesToDelete + box.value + " ";
		}
	}
	if (rulesToDelete == "") {
		box = eval(field);
		if (box.checked == true) {
			rulesToDelete =  rulesToDelete + box.value + " ";
		}
		else {
			alert ("No Rules selected");
			return false;
		}
	}
	if (confirm("Are you sure you want to delete the selected rules?")) {
		theForm.rulesToDelete.value = rulesToDelete;
		disable_button(theForm.deleteRequest);
		theForm.submit();
	}
	else {
		return false;
	}

}

 function submitReport() {


	theForm    = document.forms[0];
	theForm1   = document.forms[1];
	hostsToSubmit = "";

    var tot = theForm.elements['manualAgents[]'].options.length;
	var i = 0;
	for(i=0;i < tot; i++) {
	    if (theForm.elements['manualAgents[]'].options[i].selected) {
	        	hostsToSubmit = hostsToSubmit + theForm.elements['manualAgents[]'].options[i].value + "#";
                // break;
	     }
	}
	
	if (hostsToSubmit == "") {
		alert ("No servers selected selected");
		return false;
	}
    theForm1.hostsToReport.value = hostsToSubmit;
    theForm1.reportType.value = theForm.reportType.value; 
    theForm1.filterList.value = theForm.filterList.value; 
	theForm1.submit();
}

 function submitRules(field) {


	theForm    = document.forms[0];
	rulesToSubmit = "";
	hostsToSubmit = "";
	// groupsToSubmit = "";
	// allGroupServersValString = allGroupServersValString.toString();

	comments = Trim (theForm.comments.value);

	if (comments == "") {
		alert ("You need to enter a comment in order to submit the changes ");
		return false;
	}
	for (i = 0; i < field.length; i++) {

		box = eval(field[i]);
		if (box.checked == true) {
			rulesToSubmit =  rulesToSubmit + box.value + " ";
		}
	}
	if (rulesToSubmit == "") {
		box = eval(field);
		if (box.checked == true) {
			rulesToSubmit =  rulesToSubmit + box.value + " ";
		}
		else {
			alert ("No Change rules selected");
			return false;
		}
	}

	hostsToSubmit = "";

    var tot = theForm.elements['manualAgents[]'].options.length;
	var i = 0;
	for(i=0;i < tot; i++) {
	    if (theForm.elements['manualAgents[]'].options[i].selected) {
	        	hostsToSubmit = "SELECTED";
                break;
	     }
	}
	
	if (hostsToSubmit == "") {
		alert ("No servers selected selected");
		return false;
	}

	if (confirm("Are you sure you want to apply the selected changes to the selected servers?")) {
		theForm.rulesToSubmit.value = rulesToSubmit;
		disable_button(theForm.submitRequest);
		theForm.submit();
	}
	else {
		return false;
	}

}

function add_user() {

	theForm    = document.forms[0];
	f          = theForm;
	username   = "";
	totalGroup = "";
	password  = "";
	password2  = "";

	username = Trim(theForm.username.value);
	if (username == "") {
		alert ("User name is a required field, Please enter it");
		return false;
	}

	userLength = username.length;
	if ((userLength < 2) || (userLength > 11)) {
		alert ("User name should be between 2 and 10 characters")
		return false;
	}

	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		totalGroup += o.value + ",";
	}

	password = Trim(theForm.password.value);
	password2 = Trim(theForm.password2.value);
	
	if (password != password2) {
		alert ("Password field doesnt match");
		return false;
	}

	if (password != "") {
		passwordLength = password.length;
		if ((passwordLength < 4) || (passwordLength > 32)) {
			alert ("Password should be between 4 and 32 characters")
			return false;
		}
		f['md5'].value = hex_hmac_md5(f['username'].value, f['password'].value);
		theForm.randomPass.value = 0;
	}
	else {
		var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
		var string_length = 4;
		var randomstring = '';
		for (var i=0; i<string_length; i++) {
			var rnum = Math.floor(Math.random() * chars.length);
			randomstring += chars.substring(rnum,rnum+1);
		}
		password = randomstring;
		theForm.password.value = password;
		f['md5'].value = hex_hmac_md5(f['username'].value, f['password'].value);
		theForm.randomPass.value = 1;
	}
	theForm.totalGroup.value = totalGroup;
	theForm.action.value = 1;
	disable_button(theForm.addUser);
	disable_button(theForm.deleteUser);
	disable_button(theForm.resetUser);
	theForm.submit();


}

function delete_user() {
	theForm    = document.forms[0];
	username = Trim(theForm.username.value);
	if (username == "") {
		alert ("You need to specify a user to delete");
		return false;
	}
	
	if (confirm("Are you sure you want to delete " + username + " ?")) {
		theForm.action.value = 2;
		disable_button(theForm.addUser);
		disable_button(theForm.deleteUser);
		disable_button(theForm.resetUser);
		theForm.submit();
	}
	else {
		return false;
	}
}

function populate_groups(groupName, userName, id) {

	theForm   = document.forms[0];
	theForm.reset();
	theForm.groupname.value  = groupName;
	theForm.groupSelect.value = groupName;
	theForm.id.value         = id;

	display_options_for_this (theForm.list1,theForm.list2,userName);
	enable_button(theForm.updateGroup);
	if (groupName != "administrator") {
		enable_button(theForm.deleteGroup);
		theForm.groupname.disabled = false;
		theForm.groupname.focus();
	}
	else {
		disable_button(theForm.deleteGroup);
		theForm.groupname.disabled = true;
		theForm.list2.focus();
	}
	
}

function disable_button_group() {

	theForm    = document.forms[0];

	disable_button(theForm.updateGroup);
	disable_button(theForm.deleteGroup);
	theForm.groupname.disabled = false

}

function add_group(allGroups) {

	theForm    = document.forms[0];
	totalUsers = "";

	groupname   = Trim(theForm.groupname.value);
	if (groupname == "") {
		alert ("Group name is a required field, Please enter it");
		return false;
	}	
	var te = new RegExp("\\b" + groupname + "\\b");
	if (allGroups.match(te)) {
		alert ("Group already exists, please choose a new name");
		return false;
	}
	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		totalUsers += o.value + ",";
	}

	theForm.action.value = 1;
	theForm.totalUsers.value = totalUsers;
	theForm.submit();
}

function delete_group(allGroups) {

	theForm    = document.forms[0];

	groupname   = Trim(theForm.groupname.value);
	if (groupname == "") {
		alert ("No groups were selected for deletion");
		return false;
	}	
	var te = new RegExp("\\b" + groupname + "\\b");
	if (!allGroups.match(te)) {
		alert ("Group Doesn't exist");
		return false;
	}

	theForm.action.value = 2;
	theForm.submit();
}

function update_group(allGroups) {

	id = theForm.id.value
	totalUsers = "";
	groupSelect = theForm.groupSelect.value;

	if (id == "") {
		alert ("Nothing to Update, Error");
		return false;
	}
	groupname   = Trim(theForm.groupname.value);
	if (groupname == "") {
		alert ("Group name Not entered, Nothing to update");
		return false;
	}
	
	// We need to make sure the newgroup that they entered doesn't already exist
	if (groupSelect != groupname) {
		var te = new RegExp("\\b" + groupname + "\\b");
		if (allGroups.match(te)) {
			alert ("Group already exists, please choose a new name");
			return false;
		}
	}
	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		totalUsers += o.value + ",";
	}

	if ((groupname == "administrator") && (totalUsers == "")) {
		alert("Administrator group must have at least 1 user");
		return false;
	}

	theForm.groupname.disabled = false
	theForm.totalUsers.value = totalUsers;
	theForm.action.value = 3;
	theForm.submit();

}


function dedupe_list(listToSort,enableSort)
{
	//var count = 0;
	var listvalues = new Array();
	var newlist = new Array();
	listToSort = listToSort.toString();	
	listToSort = listToSort.replace(/\r/gi, "\n");
	listToSort = listToSort.replace(/\n+/gi, "\n");

	listvalues = listToSort.split(",");
	
	var hash = new Object();
	
	for (var i=0; i<listvalues.length; i++) {
		if (hash[listvalues[i].toLowerCase()] != 1) {
			newlist = newlist.concat(listvalues[i]);
			//alert ("value: ("+listvalues[i]+")\nnewlist: \n"+newlist);
			hash[listvalues[i].toLowerCase()] = 1
		}
		//else { 
		//	count++; 
		//}
	}
	if (enableSort) {
		newlist.sort();
	}
        return (newlist);
}

function replace(string,text,by) {
    var strLength = string.length;
    var txtLength = text.length;
    
    if ((strLength == 0) || (txtLength == 0)) return string;

    var i = string.indexOf(text);
    if ((!i) && (text != string.substring(0,txtLength))) return string;
    if (i == -1) return string;

    var newstr = string.substring(0,i) + by;

    if (i+txtLength < strLength)
        newstr += replace(string.substring(i+txtLength,strLength),text,by);

    return newstr;
}

function navigate_next_level(allGroupServersList) {

	newString = allGroupServersList.toString();
	theForm    = document.forms[0];
	l = theForm.list3; 
	for (var i=0; i<l.options.length; i++) {
		var o = l.options[i];
		if (o.selected) {
			selection = o.value;
			if (selection == "previous_selection") {
				previousSelection = theForm.previousSelection.value;
				previousSelection = previousSelection.toString();
				previousSelectionArray = previousSelection.split(",");
				selection = previousSelectionArray.pop();
				selection = previousSelectionArray.pop();
				previousString = previousSelectionArray.join(",");
				theForm.previousSelection.value = previousString;
			}
			if (selection == "") {
				// alert("Attempting to display root level");
				// We may be missing a few groups at the group level
				var te = new RegExp(
				"(?:^|,)([^\\.\\W,]+)(?=,|$)", "g");
			}
			else {
		//		var te = new RegExp(
		//		"(?:^|,)" + selection + "\\.([^\\.\\W,]+)(?=,|$)", "g");
				var te = new RegExp(
				"(?:^|,)" + selection + "\\.([\\w\\-]+)(?=,|$)", "g");
			}
			
			dupeList = newString.match(te);
			var newOptions = new Array ();
			if (dupeList != null) {
				newOptions = dedupe_list(dupeList,1);
			} 

		}
	}
	document.all('groupSelected').innerText = selection;

	// Delete them from original
	 for (var i=(l.options.length-1); i>=0; i--) {
		l.options[i] = null;
	}

        	

	previousSelection = theForm.previousSelection.value;
	previousSelection = previousSelection.toString();
	previousSelectionArray = previousSelection.split(",");
	previousSelectionArray.push(selection);
	previousString = previousSelectionArray.join(",");
	theForm.previousSelection.value = previousString;

	l.options[0] = new Option( "..", "previous_selection", false, false);
	
	for (var i=0; i<newOptions.length; i++) {

		var child = newOptions[i];

// alert(child);
		if (!hasOptions(l)) { 
			var index = 0; 
		} else { 
			var index=l.options.length; 
		}

		if (child != selection) {
			var te = new RegExp("\\b" + child + "\\b");
			if (!previousString.match(te)) {
				display = replace (child, selection + ".", ""); 
				display = replace(display, "__cfg", ".cfg");
				l.options[index] = new Option( display, child, false, false);
			}
		}
	}
}

function update_group_right() {

	theForm    = document.forms[0];
	if (confirm("Are you sure you want to update that group ?")) {
		totalCategories = "";
		totalGroups = "";
		totalAgents = "";
		for (var i=0; i<theForm.list2.options.length; i++) {
			var o = theForm.list2.options[i];
			totalCategories  = totalCategories + "," + o.value;
		}

		for (var i=0; i<theForm.list4.options.length; i++) {
			var o = theForm.list4.options[i];
			totalGroups  = totalGroups + "," + o.value;
		}

		// We need to get all the field for cat, group and agent
		theForm.totalCategories.value = totalCategories;
		theForm.totalGroups.value = totalGroups;
		theForm.action.value = 1;
		theForm.submit();
	}
	else {
		return false;
	}
}

function populate_categories(categoryName, categoryType,allAppClass,categorydescription) {

	if (categoryType == "") { return false; }

	theForm   = document.forms[0];
	mergeSelected = theForm.mergeSelected.value;	

	theForm.categorydescription.value = categorydescription;

	if (categoryType != 1) {
		theForm.categoryname.value = categoryName;
		theForm.categorytype.value = categoryType;
	}
	if (mergeSelected != 1) {
		display_options_for_this (theForm.list1,theForm.list2,allAppClass); 		
	}
	else {
		update_select_without_delete (theForm.list1,theForm.list2,allAppClass);
	}

	theForm.mergeCategory.disabled = false;
	theForm.mergeSelected.value = 0;	
	theForm.categoryType.value = categoryType;
	theForm.categoryname.focus();

		
}

function set_variable_merge() {
	
	theForm   = document.forms[0];
	theForm.mergeCategory.disabled = true;
	theForm.mergeSelected.value = 1;	
}

function add_category(allCategoriesString) {

	allCategoriesString += ",";

	theForm   = document.forms[0];
	categoryName = Trim (theForm.categoryname.value);	
	totalAppClass = "";

	if (categoryName == "") {
		alert("Category Name is a required field");
		return false;
	}

	var te = new RegExp("," + categoryName + ",");
	if (allCategoriesString.match(te)) {
		alert ("Category already exists, please choose a new name");
		return false;
	}

	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		totalAppClass += o.value + ",";
	}
	
	theForm.totalAppClass.value = totalAppClass;
	theForm.action.value = 1;
	theForm.categorySelectedFocus.value = 0;
	theForm.submit();
}

function delete_category(allCategoriesString) {

	theForm   = document.forms[0];
	categoryName = Trim (theForm.categoryname.value);	
	categoryType = theForm.categoryType.value 

	if (categoryName == "") {
		alert("No category selected for Deletion");
		return false;
	}

	if (categoryType != "2") {
		// alert("You cannot delete an out of the box category");
		// return false;
	}

	var te = new RegExp("," + categoryName + ",");
	catNotValid = 1;
	if (allCategoriesString.match(te)) {
		catNotValid = 0;
	}

	if (catNotValid == "1") {
		alert ("Category name invalid");
		return false;
	}
	theForm.action.value = 2;
	theForm.categorySelectedFocus.value = 0;
	theForm.submit();

}

function update_category() {

	theForm   = document.forms[0];
	categoryName = Trim (theForm.categoryname.value);	
	categoryType = theForm.categoryType.value 
	totalAppClass = "";

	if (categoryName == "") {
		alert("No category selected to Update");
		return false;
	}

	if (categoryType == "1") {
		// alert("You cannot update an out of the box category");
		// return false;
	}

	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		totalAppClass += o.value + ",";
	}
	
	theForm.totalAppClass.value = totalAppClass;
	theForm.action.value = 3;
	theForm.categorySelectedFocus.value = 0;
	theForm.submit();
}

function hide_outofbox(hide) {

	theForm   = document.forms[0];

	if (hide == 1) {
		theForm.hideOutOfBox.value = 1;
	}
	else {
		theForm.hideOutOfBox.value = 0;
	}

	theForm.submit();
	
}

function validate_agents(allAgents,shouldAlert) {

	theForm = document.forms[0];
	listvalues = allAgents.split(",");
	updateRTE('manualAgents');
	agentEntered = theForm.manualAgents.value;
	agentEntered  = agentEntered.toString();
	agentEntered = replace (agentEntered, "<BR>", "\n");
	agentEntered = replace (agentEntered, "&nbsp;", "");
	agentEntered = agentEntered.split("\n");

	invalidAgents = "";
	validAgents = "";
	for(var j=0; j < agentEntered.length; j++) {

		agentEntered[j] = agentEntered[j].toLowerCase();
		agentEntered[j] = Trim (agentEntered[j]);
		agentEntered[j] = replace (agentEntered[j], "<p>", "");
		agentEntered[j] = replace (agentEntered[j], "</p>", "");
		agentEntered[j] = replace (agentEntered[j], "<br>", "");
		agentEntered[j] = replace (agentEntered[j], "</br>", "");
		agentEntered[j] = replace (agentEntered[j], "<em>", "");
		agentEntered[j] = replace (agentEntered[j], "</em>", "");

		if (agentEntered[j] == "") {
			continue;
		}
		agentEnteredArray = agentEntered[j].split(" ");
		hostname = agentEnteredArray[0];
		regexHostname = 0;
		
		// If hostname contains a wildcard, convert it to a regular expression
		if ((hostname.indexOf ("*") != -1)||(hostname.indexOf ("?") != -1)) { 
			regexHostname =1;
			hostname = replace (hostname,".","\\.");
			hostname = replace (hostname,"*","\.*");
			hostname = replace (hostname,"?","\.");
		}
		
		portEntered = agentEntered[j].indexOf(" ");
		if (portEntered == "-1" ) {
			portNumber = "";	
 		}
		else {
			portNumber = agentEnteredArray[1];
		}
	
		isValid = 0;
		for (var i=0; i<listvalues.length; i++) {
			listvalues[i] = Trim (listvalues[i]);
			listvalues[i] = listvalues[i].toLowerCase();
			validListArray = listvalues[i].split(" ");
			validHostName = validListArray[0];
			validPort     = validListArray[1];
			
			// Check whether the hostname is an expression or actual hostname 
			if (regexHostname) { 
				condition = validHostName.match("^"+hostname+"$"); 
				//alert ("Regex result for hostname ("+hostname+"): "+condition);
			} else {
				condition = validHostName == hostname; 
				//alert ("Non-regex");
			}
			
			if (condition) {
				// matched the name, now just need to verify the port
				if (portNumber != "") {
					if (validPort == portNumber) {					
						isValid = 1;
						validAgents = validAgents + "<em>" + validHostName + " " + validPort + "</em><br>\n";
						if (!regexHostname) {break};
					}
				} else {
					isValid = 1;
					validAgents = validAgents + "<em>" + validHostName + " " + validPort + "</em><br>\n";
					if (!regexHostname) {break};

				}
			}
		}
		if (isValid != 1) {
			invalidAgents = invalidAgents + agentEntered[j] + "<br>\n";
		}
	}
	if (shouldAlert == 1) {

 		if (invalidAgents != "") {
			alert ("The Following agents are invalid\n" + replace (invalidAgents, "<br>", ""));
		}
		else {
			//alert ("AGENTS VALIDATED");
		}
		totalAgents = invalidAgents + validAgents;
 	}
	else {
		totalAgents = validAgents;
	}

	totalAgents = totalAgents.split("<br>\n");

	totalAgents = dedupe_list (totalAgents,0);

	totalAgents = totalAgents.join("<br>\n");
	theForm.manualAgents.value = totalAgents;
	enableDesignMode('manualAgents', totalAgents, "false");
}	

function pcm_submit_report(reportType) {

	theForm = document.forms[0];
	theForm2 = document.forms[1];

	theForm2.reportType.value = reportType;
	theForm2.filterList.value = theForm.filterList.value;
	theForm2.submit();	
}

function change_submit(changeType) {

	theForm = document.forms[0];
	theForm.changeType.value = changeType;
	theForm.submit();	
}

function get_host_settings(agentsForUser) {

	theForm = document.forms[0];
	listvalues = agentsForUser.split(",");

	hostTemplate = theForm.hostTemplate.value;
	hostTemplate = Trim (hostTemplate);
	hostTemplate = hostTemplate.toLowerCase();

	if (hostTemplate == "") {
		alert("No host entered");
		return false;
	}
	isValid = 0;
	for (var i=0; i<listvalues.length; i++) {
		listvalues[i] = listvalues[i].toLowerCase();
		if (listvalues[i] == hostTemplate) {
			isValid = 1;
		}
	}
	if (!isValid) {
		alert("Host doesn't exist or you don't have the appropriate rights to view it");
		return false;
	}
	else {
        // We need to set the chosen instance
		theForm.submit();
	}
}

function populate_hostTemplate() {

	theForm = document.forms[0];
	theForm.hostTemplate.value = theForm.host.value;
	
}

function validate_generic_changes() {

	theForm   = document.forms[0];
	theForm2  = document.forms[1];

	ruleName = "";
	ruleName = theForm.ruleName.value;
	ruleValue = theForm.ruleValue.value;
	
	if ((ruleName == "") || (ruleValue == "")) {
		alert ("Rules and values are required");
		return false;
	}
	theForm2.changeType.value = theForm.changeType.value;
	theForm2.ruleName.value    = theForm.ruleName.value;
	theForm2.ruleValue.value     = theForm.ruleValue.value;
	theForm2.operationSelected.value      = theForm.operationSelected.value;
	theForm2.submit();
}

function populate_request_id(requestId,groupTotalString,hostTotalString,comments,changeControl,priority) {

	theForm   = document.forms[0];
	// theForm.priority.value = priority;
	theForm.changeControl.value = changeControl;
	hostTotalString = replace(hostTotalString, ",", "<br>");
	theForm.manualAgents.value = hostTotalString;
	theForm.requestId.value = requestId;
	enableDesignMode('manualAgents', hostTotalString, "false");
	
	// We need to remove everything from the group select box
	for (var i=(theForm.list4.options.length-1); i>=0; i--) {
		theForm.list4.options[i] = null;
	}

	if (groupTotalString != "") {
		groupTotalString = groupTotalString.split(",");
		for (var i=0; i<groupTotalString.length; i++) {
			theForm.list4.options[i] = new Option(groupTotalString[i], groupTotalString[i], "", "");
		}
	}
	theForm.schedule.disabled = false;

	lastSelection = theForm.lastSelection.value;
	if (lastSelection != "") {
		document.all(lastSelection).style.backgroundColor = '#CCC';
	}

	document.all(requestId).style.backgroundColor = "#E4E4E4";
	theForm.lastSelection.value = requestId;

}

function validate_date(dateEntered) {

	var today = new Date()
	var month = today.getMonth() +1 
	var day = today.getDate()
	var year = today.getFullYear()
	var hours = today.getHours();
	var minutes = today.getMinutes();
	var seconds = today.getSeconds();
	var currentDate = new Date(year, month, day, hours, minutes, seconds);
        epocCurrent = currentDate.getTime()/1000.0;
 
	dateEntered = Trim (dateEntered.toString());
	dateEnteredDiv = dateEntered.split(" ");
	if (dateEnteredDiv.length < 2) {
		return false;
	}
	date = dateEnteredDiv[0];
	time = dateEnteredDiv[1];
	dateArray = date.split("/");
	if (dateArray.length < 3) {
		return false;
	}
	monthEntered = dateArray[0];
	dayEntered   = dateArray[1];
	yearEntered   = dateArray[2];
	timeArray = time.split(":");
	if (timeArray.length < 3) {
		return false;
	}

	hourEntered = timeArray[0];
	minEntered = timeArray[1];
	secEntered = timeArray[2];

	if ((monthEntered < 1) || (monthEntered > 12) || (dayEntered < 1) || (dayEntered > 31) || (yearEntered < year)  || (yearEntered > 2020) || (hourEntered < 0) || (hourEntered > 23) || (minEntered > 59) || (minEntered < 0) || (secEntered < 0) || (secEntered > 59)) {
		return false;
	}

       var checkDate = new Date(yearEntered, monthEntered, dayEntered, hourEntered, minEntered, secEntered);
       checkDateEpoc = checkDate.getTime()/1000.0;
       if (epocCurrent > checkDateEpoc) {
		return false;
       }
       return checkDateEpoc;
}

function submit_schedule() {

	theForm   = document.forms[0];
	beginTime = theForm.startTime.value;
	finalTime = theForm.endTime.value;
	hostsToSubmit = theForm.manualAgents.value;
	requestId = Trim (theForm.requestId.value);

	if (requestId == "") {
		alert("Request Not selected");
		return false;
	}

	if ((beginTime == "") || (finalTime == "") || (hostsToSubmit == "")) {
		alert("Start time, end time and agent to schedule are required fields");
		return false;
	}
	beginTime = validate_date(beginTime);
	if (!beginTime) {
		alert ("Start time is invalid");
		return false;
	}
	finalTime = validate_date(finalTime);
	if (!finalTime) {
		alert ("End time is invalid");
		return false;
	}
	if (finalTime < beginTime) {
		alert("Start Date is greater than end date");
		return false;
	}

	// We need to update the group field
	totalGroups = "";
	for (var i=0; i<theForm.list4.options.length; i++) {
		var o = theForm.list4.options[i];
		totalGroups += o.value + ",";
	}


	if (confirm("Are you sure you want to schedule Request " + requestId + "?")) {
		theForm.action.value = 1;
		theForm.groups.value = totalGroups;
		theForm.hosts.value  = hostsToSubmit;
		theForm.submit();
	}
	else {
		return false;
	}
}

function set_filter_selection(reportType) {

	if (reportType != 6) {
		return 1;
	}
	theForm    = document.forms[0];
	dataPointTime = theForm.dataPointTime.value;
	dataPointTime = Trim(dataPointTime);
	if ((isNaN(dataPointTime)) || (dataPointTime == "")) {
        	alert ("Invalid Value for the most recent data point field, it should be a positive numeric value"); 
		return 0;
	}

        appSelected = "";
	var tot = theForm.elements['filterAppClass[]'].options.length;
	var i = 0;
	for(i=0;i < tot; i++) {
		if (theForm.elements['filterAppClass[]'].options[i].selected) {
			appSelected += theForm.elements['filterAppClass[]'].options[i].value;
			appSelected += ",";
	     	}
	}	
	theForm.filterAppClassSelect.value = appSelected;
	return 1;
}

function toggle_parameters(state) {

	theForm    = document.forms[0];
	theForm.showParameters.value = state;
	previousSelection = "";
	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		previousSelection += o.value + ",";
	}
	
	theForm.previousSelection.value = previousSelection;
	theForm.submit();
}

function remember_value() {

	theForm    = document.forms[0];

	previousSelection = "";
	for (var i=0; i<theForm.list2.options.length; i++) {
		var o = theForm.list2.options[i];
		previousSelection += o.value + ",";
	}
	
	theForm.previousSelection.value = previousSelection;
	theForm.submit();

}

function populate_categories_submit(categoryName,categoryDescription,categoryType) {

	theForm   = document.forms[0];
	theForm.categoryname.value = categoryName;
	theForm.categorydescription.value = categoryDescription;
	theForm.categorySelected.value = 1;
	theForm.categoryType.value = categoryType
	theForm.submit();
		
}


function validate_notification() {

	theForm   = document.forms[0];
    theForm.newRule.value = 1;    	
	disable_button(theForm.submitMsg);
	theForm.submit();
}

function time_error (message) {
	alert (message);
	return null;
}

function isdefined( variable) {
    //alert ("isdefined");

    return (typeof(window[variable]) == "undefined")?  false: true;
}

// Parse time for blackouts
function parse_time (timeString) {

	//alert ("parse_time");

	timeArray = String(timeString ? timeString : '').split(' ');
	clockString = timeArray[0];
	clockArray = String(clockString ? clockString : '').split(':');

	hours = clockArray[0];
	minutes = clockArray[1];
	meridian = timeArray[1];

	if ((hours<0) || (hours>23)) {
		time_error ("Invalid value for hours.  Please enter time like this - 08:15 or 17:35");
		return null;
	}

	//alert ("Entered hour is "+hours);


	// if ((meridian == 'PM') || (meridian == 'pm')) {
	// 	if (hours == "12") { 
	// 		hours = 12; 
	// 	} else {
	// 		hours = (parseInt(hours) + 12);
	// 	}
	// } else if ((meridian == 'AM') || (meridian == 'am')) {
	// 	if (hours == "12") { hours = 0; }
	// } else {
	// 	// Invalid time format
	// 	time_error ("Invalid time format.  Please enter time like this - 3:35 PM");
	// 	return null;
	// }
	
	//alert ("Meridian is "+meridian);
	//alert ("Hour is now "+hours);
	
	
	if ((minutes < 0) || (minutes > 59)) {
		time_error ("Invalid value for minutes.  Please enter time like this - 08:15 or 17:35");
		return null;
	}
	
	var timeInSeconds = (hours*3600) + (minutes*60);

	return timeInSeconds;
}


function validate_blackout() {

	//alert("validate blackout");

	theForm   = document.forms[0];
	action = "";

	if (theForm.action) {
		for (var i = 0; i < theForm.action.length; i++) {
			if (theForm.action[i].checked) {
				action =  theForm.action[i].value;
				break;
			}
		}
	}

	startDay = 0;
	endDay = 0;

	if (action == "DELETE_ALL") {
		theForm.newRule.value = 1;
		theForm.submit();
		return;
	}

	// Make sure that start and end times have a day selected
    	if(theForm.startday) {	
		for (var i = 0; i < 7; i++) {
		       if (theForm.startday[i].checked) {
				startDay = 1;
		       }

		       if (theForm.endday[i].checked) {
				endDay = 1;
		       }
		}
	}	


	if ((theForm.starttime.value != "") && (theForm.endtime.value != "")) {
		// alert ("Start and end time OK"+theForm.starttime.value);
		
	} else {
		alert ("Please select a start and end time.");
		return null;
	}

    	if ((startDay == 0) || (endDay == 0)) { 
    		alert ("Please select the days of the week.");
    		return null;
    	}


	startTime = theForm.starttime.value;
	endTime = theForm.endtime.value;
	

	startTimeSeconds = parse_time (startTime);
	endTimeSeconds = parse_time (endTime);
	
	if ((startTimeSeconds == null) || (endTimeSeconds == null)) {
		return null;
	}
	
	// Bump the end time up to the end of the minute
	endTimeSeconds = endTimeSeconds + 59;
	
    theForm.newRule.value = 1;
	disable_button(theForm.submitMsg);
	theForm.submit();
}

function set_param_focus() {

	theForm   = document.forms[0];
    theForm.setfocus.value = 1;
    theForm.submit();
}

function rogue(agentId) {

	theForm   = document.forms[0];
    theForm.removeRogue.value = agentId;
    theForm.submit();
}

function validate_ping() {

	theForm   = document.forms[0];
    actionSelected = "";
    if(theForm.action) {	
		for (var i = 0; i < 3; i++) {
		       if (theForm.action[i].checked) {
				  actionSelected = theForm.action[i].value;
                  break;
		       }
        }
	}
    pingHosts = theForm.pingHosts.value;
    pingHosts = Trim(pingHosts)
    if (((actionSelected == "MERGE") || (actionSelected == "REPLACE")) && (pingHosts == "")) {
    
        alert("Host List is empty, in order to MERGE or REPLACE, you will need to have at least 1 host");
        return false;
    }

    theForm.newRule.value = 1;
	disable_button(theForm.submitMsg);
	theForm.submit();
}

function determine_process(field, rowNumber,general) {
	
	theForm   = document.forms[0];

	trid = 'row' + rowNumber;
	box = eval(field);

	if (box.checked == true) {
   		document.getElementById(trid).style.display =  "BLOCK";
        if (general == "1") {
            field.checked = false;
        }
        else {
            previousValue = theForm.oneSelected.value;
            if (previousValue == "") {
                theForm.oneSelected.value = 1;
            }
            else {
                previousValue = previousValue.toString();
                previousValue = parseFloat(previousValue);
                theForm.oneSelected.value = previousValue + 1;
            }
        }
	}
	else {
   		document.getElementById(trid).style.display =  "none";
        if (general != "1") {
            previousValue = theForm.oneSelected.value;
            if (previousValue == "") {
                  theForm.oneSelected.value = 0;
            }
            else {
                  previousValue = previousValue.toString();
                  previousValue = parseFloat(previousValue);
                  theForm.oneSelected.value = previousValue - 1;
            }
        }
	}


}

function validate_process_count(count) {
    
	theForm   = document.forms[0];
    processCount = theForm.count.value;
    someThingchecked = "0";
    newProcess = theForm.newProcess.value;
    newCount = theForm.newCount.value;
    processDelete = "";
    if (newProcess != "") {
        // need to validate the process name no dots or space
	    var te = new RegExp("\\.");
	    if (newProcess.match(te)) {
            alert("Process name cannot have a dot (.)");
            return false;
        }
    
	    var te = new RegExp(" ");
	    if (newProcess.match(te)) {
            alert("Process name cannot have a space in it");
            return false;
        }

    }
    if ((newProcess != "") && ((newCount == "") || (isNaN(newCount)))) {
        alert ("Process Count is empty or not a number");
        return false;
    }
    // Need to add some validation
    
    var tot = theForm.elements['processToDelete[]'].options.length;
    var i = 0;
    var j = 0;
    for(i=0;i < tot; i++) {
       	if (theForm.elements['processToDelete[]'].options[i].selected) {
            processDelete = "1";
            break;
    	}
    }
    oneSelected = theForm.oneSelected.value;

    if (((oneSelected == "0") || (oneSelected == "")) && (newProcess == "") && (processDelete == "")) {
        alert("No process selected for modification or add or delete, nothing to do");
        return false;
    }

    theForm.newRule.value = 1;
	disable_button(theForm.submitMsg);
	theForm.submit();

}

function update_count() {

	theForm   = document.forms[0];
    var tot = theForm.elements['manualAgents[]'].options.length;
    var i = 0;
    var j = 0;
	for(i=0;i < tot; i++) {
	     	if (theForm.elements['manualAgents[]'].options[i].selected) {
                j++;
	     	}
	}	
    theForm.numberOfAgent.value = j + " server(s) selected ";
}

/*****************************************************************************
 * Copyright © 2005 Advantis Management Solutions, Inc. All rights reserved. *
 *****************************************************************************/
