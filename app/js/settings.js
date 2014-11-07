//global variables
doSettingsFile = 'server/dosettings';

function listProfileOptions(opt)
{
	var settingName = "";
	if(opt == 1) {
		var settingName = "Salutations";
		document.getElementById('listSalutationOptions').className = 'active';
		document.getElementById('listRelationshipOptions').className = '';
		document.getElementById('listMaritalOptions').className = '';
		document.getElementById('listProfileStatusOptions').className = '';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	} else if(opt == 2) {
		var settingName = "Relationships";
		document.getElementById('listSalutationOptions').className = '';
		document.getElementById('listRelationshipOptions').className = 'active';
		document.getElementById('listMaritalOptions').className = '';
		document.getElementById('listProfileStatusOptions').className = '';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	} else if(opt == 3) {
		var settingName = "Marital Statuses";
		document.getElementById('listSalutationOptions').className = '';
		document.getElementById('listRelationshipOptions').className = '';
		document.getElementById('listMaritalOptions').className = 'active';
		document.getElementById('listProfileStatusOptions').className = '';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	} else if(opt == 4) {
		var settingName = "Profile Statuses";
		document.getElementById('listSalutationOptions').className = '';
		document.getElementById('listRelationshipOptions').className = '';
		document.getElementById('listMaritalOptions').className = '';
		document.getElementById('listProfileStatusOptions').className = 'active';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	}
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=1&opt=' + opt+'&settingName='+settingName;
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:listProfileOptionsResponse,
		error:HandleAjaxError
	});	
}

function listProfileOptionsResponse(response)
{
	document.getElementById('pageContent').innerHTML = response;
}

function addOrUpdateOption(val)
{
	isUpdate = val;
	var optionID;
	var optionValue = '';
	settingID = document.getElementById('hiddenSettingID').value;

	if(isUpdate) {
		var rowID = document.getElementById('hiddenLastEditedRow').value;
		optionValue = document.getElementById('inputEditOptionValue-' + rowID).value;
		optionID = document.getElementById('inputEditOptionID-' + rowID).value; 
	} else {
		optionValue = document.getElementById('inputAddOptionValue').value;
	}
	
	if(optionValue == "") {
		var alertMsg = 'Please enter any value.';
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var formPostData = 'req=2&isUpdate=' + isUpdate + '&optionValue=' + escString(optionValue) + '&settingID=' + escString(settingID);
	if(isUpdate) {
		formPostData += '&optionID=' + escString(optionID);
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:addOrUpdateOptionResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateOptionResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Option has been updated successfully':'Option has been added successfully';

		listProfileOptions(settingID);
	}
	else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Unable to update the option':'Unable to add the option';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function showEditOptionInfoRow(rowID)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('divAddOptionBtn').style.display = '';
	document.getElementById('divAddOptionForm').style.display = 'none';
	var lastEditedRow = document.getElementById('hiddenLastEditedRow').value;
	if(lastEditedRow != "")
	{
		document.getElementById('spnShowOptionValueInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnEditOptionValueInfo-' + lastEditedRow).style.display = 'none';
		document.getElementById('spnActionInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnSaveButton-' + lastEditedRow).style.display = 'none';
	}
	document.getElementById('spnShowOptionValueInfo-' + rowID).style.display = 'none';
	document.getElementById('spnEditOptionValueInfo-' + rowID).style.display = '';
	document.getElementById('inputEditOptionValue-' + rowID).focus();
	document.getElementById('inputEditOptionValue-' + rowID).select();
	document.getElementById('spnActionInfo-' + rowID).style.display = 'none';
	document.getElementById('spnSaveButton-' + rowID).style.display = '';
	document.getElementById('hiddenLastEditedRow').value = rowID;
}

function hideEditOptionInfoRow(rowID)
{
	document.getElementById('spnShowOptionValueInfo-' + rowID).style.display = '';
	document.getElementById('spnEditOptionValueInfo-' + rowID).style.display = 'none';
	document.getElementById('spnActionInfo-' + rowID).style.display = '';
	document.getElementById('spnSaveButton-' + rowID).style.display = 'none';
	document.getElementById('hiddenLastEditedRow').value = "";
}

function showAddOptionForm()
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('divAddOptionBtn').style.display = 'none';
	document.getElementById('divAddOptionForm').style.display = '';
	document.getElementById('inputAddOptionValue').focus();
	var lastEditedRow = document.getElementById('hiddenLastEditedRow').value;
	if(lastEditedRow != "")
	{
		document.getElementById('spnShowOptionValueInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnEditOptionValueInfo-' + lastEditedRow).style.display = 'none';
		document.getElementById('spnActionInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnSaveButton-' + lastEditedRow).style.display = 'none';
	}
}

function hideAddOptionForm()
{
	document.getElementById('inputAddOptionValue').value = '';
	document.getElementById('divAddOptionBtn').style.display='';
	document.getElementById('divAddOptionForm').style.display='none';
}

function deleteOption(rowID, askConfirmation)
{
	//var rowID = document.getElementById('hiddenLastEditedRow').value;
	var optionID = document.getElementById('inputEditOptionID-' + rowID).value;
	var settingID = document.getElementById('hiddenSettingID').value;
	var optionName = document.getElementById('inputEditOptionValue-' + rowID).value;
	var settingName = document.getElementById('hiddenSettingName').value;
	if(askConfirmation) {
		var msgToDisplay = 'You are requesting to delete the option "<b>'+ optionName + '</b>" from the field "<b>'+settingName+'</b>". Deleting this option might have impact on the profiles which are assigned with this option for <b>'+settingName+'</b>.<BR>Are you sure you want to delete this option?<BR>';
		var actionTakenCallBack = "deleteOption(" + rowID+ ", 0)";
		var actionCanelCallBack = "cancelFieldOptionDeleteRequest()";
		var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes, Delete", "No, Cancel", actionTakenCallBack, actionCanelCallBack);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		$('html,body').scrollTop(0);
		return false;
	}
	setID = settingID;

	var formPostData = 'req=3&settingID=' + settingID + '&optionID=' + optionID;
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:deleteOptionResponse,
		error:HandleAjaxError
	});
}

function deleteOptionResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Requested setting is deleted successfully!';

		listProfileOptions(setID);
	}
	else {
		var alertType = 2;
		var msgToDisplay = 'Unable to delete the requested setting';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function listAllUsers()
{
	document.getElementById('listSalutationOptions').className = '';
	document.getElementById('listRelationshipOptions').className = '';
	document.getElementById('listMaritalOptions').className = '';
	document.getElementById('listProfileStatusOptions').className = '';
	document.getElementById('listProfileCustomFields').className = '';
	document.getElementById('listUsers').className = 'active';
	document.getElementById('addNewUser').className = '';
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=5';
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:listAllUsersResponse,
		error:HandleAjaxError
	});
}

function listAllUsersResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'List Of Users';
	document.getElementById('pageContent').innerHTML = response;
}

function addOrUpdateUser(val)
{
	isUpdate = val;
	var user = document.getElementById('inputUser').value;
	var pwd = document.getElementById('inputPwd').value;
	var confirmPwd = document.getElementById('inputCPwd').value;
	var userStatusIndex = document.getElementById('inputUserStatus').selectedIndex;
	var userStatus = document.getElementById('inputUserStatus').options[userStatusIndex].value;

	if(isUpdate) {
		var userID = document.getElementById('hiddenUserID').value;
		var prevUserName = document.getElementById('hiddenUserName').value;
	}

	var alertMsg = '';
	if(user == '') {
		alertMsg = 'User Name is missing';
	} else if(pwd == '') {
		alertMsg = 'Password is missing';
	} else if(confirmPwd == '') {
		alertMsg = 'Confirm Password is missing';
	} else if(pwd != confirmPwd) {
		alertMsg = 'Password doesn\'t match';
	}

	if(alertMsg.length > 0) {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var formPostData = 'req=6&userName=' + escString(user) + '&password=' + escString(pwd) + '&isUpdate=' + isUpdate +'&userStatus=' + escString(userStatus);
	if(isUpdate) {
		formPostData += '&userID=' + escString(userID) + "&prevUser=" + escString(prevUserName);
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:addOrUpdateUserResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateUserResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var alertType = ((dataObj[0] == 0)?2:1);
	var resultMsg = dataObj[1];

	if(dataObj[0] == 1) {
		if(!isUpdate) {
			listAllUsers();
		}		
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function GetAddOrEditUserForm(val, userID)
{
	isEdit = val;
//	document.getElementById('listProfiles').className = '';
//	document.getElementById('addNewProfile').className = '';
//	document.getElementById('importProfiles').className = '';
	document.getElementById('listSalutationOptions').className = '';
	document.getElementById('listRelationshipOptions').className = '';
	document.getElementById('listMaritalOptions').className = '';
	document.getElementById('listProfileStatusOptions').className = '';
	document.getElementById('listProfileCustomFields').className = '';
	document.getElementById('listUsers').className = '';
	document.getElementById('addNewUser').className = ((isEdit)?'':'active');
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=4&isEdit=' + isEdit;
	if(isEdit) {
		formPostData += '&userID=' + userID;
	}
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:GetAddOrEditUserFormResponse,
		error:HandleAjaxError
	});
}

function GetAddOrEditUserFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = ((isEdit)?'Edit User':'Add New User');
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputUser').focus();
}

function deleteUser(userID)
{
	var formPostData = 'req=7&user=' + userID;

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:deleteUserResponse,
		error:HandleAjaxError
	});
}

function deleteUserResponse(response)
{
	var resultToUI;
	if(response == 1) {
		resultToUI = getAlertDiv(1, 'User has been deleted successfully!');
		listAllUsers();
	} else {
		var msgToDisplay = ((response == 2)?'You can\'t delete the loggedin user.':'User failed to delete.');
		resultToUI = getAlertDiv(2, msgToDisplay);
	}
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function deleteUserConfirmation(userID, userName)
{
	var msgToDisplay = 'You are requesting to delete <a href="#">' + userName+ '</a> user. Please confirm your request?';
	var actionTakenCallBack = "deleteUser(" + userID + ")";
	var actionCanelCallBack = "cancelUserDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Delete", "Cancel", actionTakenCallBack, actionCanelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelUserDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function listProfileAllCustomFields()
{
	document.getElementById('listSalutationOptions').className = '';
	document.getElementById('listRelationshipOptions').className = '';
	document.getElementById('listMaritalOptions').className = '';
	document.getElementById('listProfileStatusOptions').className = '';
	document.getElementById('listProfileCustomFields').className = 'active';
	document.getElementById('listUsers').className = '';
	document.getElementById('addNewUser').className = '';
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=8';
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:listProfileAllCustomFieldsResponse,
		error:HandleAjaxError
	});
}

function listProfileAllCustomFieldsResponse(response)
{
	document.getElementById('pageContent').innerHTML = response;
}

function GetAddOrEditCustomFieldForm(isEdit, fieldID)
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';

	tempIsEdit = isEdit;
	var formPostData = 'req=9';
	formPostData += '&isEdit=' + isEdit;
	if(isEdit) {
		formPostData += '&fieldID=' + fieldID;
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:GetAddOrEditCustomFieldFormResponse,
		error:HandleAjaxError
	});
}

function GetAddOrEditCustomFieldFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = ((tempIsEdit)?'Edit Custom Field':'Add New Custom Field');
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputFieldName').focus();
}

function showOrHideFieldOptions(obj)
{
	var index = obj.selectedIndex;
	var selectedFieldID = obj.options[index].value;
	if(selectedFieldID == 6) {
		document.getElementById('divFieldOptions').style.display = '';
	} else {
		document.getElementById('divFieldOptions').style.display = 'none';
	}
	if(selectedFieldID  == 7) {
		document.getElementById('divIsFieldRequired').style.display = 'none';
	} else {
		document.getElementById('divIsFieldRequired').style.display = '';
	}
}

function addOrUpdateCustomFields(isEdit)
{
	isUpdate = isEdit;

	if(isUpdate) {
		var fieldID = document.getElementById('hiddenFieldID').value;
	}

	var fieldName = document.getElementById('inputFieldName').value;	
	if(isUpdate) {
		var fieldType = document.getElementById('hiddenFieldType').value;
	} else {
		var fieldTypeIndex = document.getElementById('inputFieldType').selectedIndex;
		var fieldType = document.getElementById('inputFieldType').options[fieldTypeIndex].value;
	}	
	var fieldOptions = document.getElementById('inputFieldOptions').value;
	var validationString = ((document.getElementById('inputValidationString').value != '')?document.getElementById('inputValidationString').value:'');
	var isRequired = ((document.getElementById('inputIsRequired').checked)?1:0);
	var displayOrder = 0;
	var fieldHelpMsg = '';

	var alertMsg = '';
	if(fieldName == '') {
		alertMsg = 'Field Name is missing';
	}
	if(fieldType == 6) {
		if(fieldOptions == '') {
			alertMsg = 'Field Options is missing';
		}
	}
	
	if(alertMsg.length > 0) {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var formPostData = 'req=10';
	formPostData += '&isUpdate=' + isUpdate;
	formPostData += '&fieldName=' + escString(fieldName);
	formPostData += '&fieldType=' + escString(fieldType);
	formPostData += '&fieldOptions=' + escString(fieldOptions);
	formPostData += '&isRequired=' + isRequired;
	formPostData += '&validationString=' + escString(validationString);
	formPostData += '&displayOrder=' + escString(displayOrder);
	formPostData += '&fieldHelpMsg=' + escString(fieldHelpMsg);
	if(isUpdate) {
		formPostData += '&fieldID=' + escString(fieldID);
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:addOrUpdateCustomFieldsResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateCustomFieldsResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Custom field has been updated successfully!':'Custom field has been created successfully';
		listProfileAllCustomFields();
	} else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Custom field failed to update.':'Custom field failed to create.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function deleteCustomFieldConfirmation(fieldID, fieldName)
{
	var msgToDisplay = 'You are requesting to delete <a href="#">' + fieldName+ '</a> custom field. Please confirm your request?';
	var actionTakenCallBack = "deleteCustomField(" + fieldID + ")";
	var actionCanelCallBack = "cancelCustomFieldDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Delete", "Cancel", actionTakenCallBack, actionCanelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelCustomFieldDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteCustomField(fieldID)
{
	var formPostData = 'req=11&fieldID=' + fieldID;

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:deleteCustomFieldResponse,
		error:HandleAjaxError
	});
}

function deleteCustomFieldResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var alertType = ((dataObj[0] == 0)?2:1);
	var resultMsg = dataObj[1];

	if(dataObj[0] == 1) {
		listProfileAllCustomFields();
	}
	var resultToUI = getAlertDiv(alertType, resultMsg);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function highlightSelectedMenu(menu)
{
	//First set empty class for all the menus
	document.getElementById('listSalutationOptions').className = '';
	document.getElementById('listRelationshipOptions').className = '';
	document.getElementById('listMaritalOptions').className = '';
	document.getElementById('listProfileStatusOptions').className = '';
	document.getElementById('listProfileCustomFields').className = '';
	document.getElementById('listUsers').className = '';
	document.getElementById('addNewUser').className = '';
	document.getElementById('smsConfig').className = '';
	document.getElementById('greetingsConfig').className = '';

	//Now set the active class for the selected menu
	var classNameToSet = 'active';
	if(menu == 1) {
		document.getElementById('listSalutationOptions').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Modify Salutations";
	} else if(menu == 2) {
		document.getElementById('listRelationshipOptions').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Modify Relationships";
	} else if(menu == 3) {
		document.getElementById('listMaritalOptions').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Modify Marital Statuses";
	} else if(menu == 4) {
		document.getElementById('listProfileStatusOptions').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Modify Profile Statuses";
	} else if(menu == 5) {
		document.getElementById('listProfileCustomFields').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Modify Custom Profile Fields";
	} else if(menu == 6) {
		document.getElementById('listUsers').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "List Login Users";
	} else if(menu == 7) {
		document.getElementById('addNewUser').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Create New Login User";
	} else if(menu == 8) {
		document.getElementById('smsConfig').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Configure SMS Gateway/Provider";
	} else if(menu == 9) {
		document.getElementById('greetingsConfig').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Configure Birthday & Anniversary Greetings";
	}
}

function getSMSConfigForm()
{
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=12';
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:getSMSConfigFormResponse,
		error:HandleAjaxError
	});	
}

function getSMSConfigFormResponse(response)
{
	document.getElementById('pageContent').innerHTML = response;

	loadSMSConfig(1, 0);
}

function loadSMSConfig(tabType, doNotHideAlertDiv)
{
	if(doNotHideAlertDiv!=1) {
		document.getElementById('alertRow').style.display = 'none';
	}
	if(tabType==1)
	{
		document.getElementById('currentGatewayDiv').className = 'tab-pane active';
		document.getElementById('chooseGatewayDiv').className = 'tab-pane';

		//Special thing...
		document.getElementById('chooseGatewayTab').className = '';
		document.getElementById('currentGatewayTab').className = 'active';
	}
	else if(tabType==2)
	{
		document.getElementById('currentGatewayDiv').className = 'tab-pane';
		document.getElementById('chooseGatewayDiv').className = 'tab-pane active';

		//Special thing...
		document.getElementById('chooseGatewayTab').className = 'active';
		document.getElementById('currentGatewayTab').className = '';
	}
	var formPostData = 'req=13&tabType='+tabType;

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:loadSMSConfigResponse,
		error:HandleAjaxError
	});
}

function loadSMSConfigResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.tabType==1)
	{
		document.getElementById("currentGatewayDiv").innerHTML = dataObj.divHTML;
	}
	else if(dataObj.tabType==2)
	{
		document.getElementById("chooseGatewayDiv").innerHTML = dataObj.divHTML;
	}
	return false;
}

function chooseSMSGateway(gatewayType, isEdit, askConfirm)
{
	document.getElementById('alertRow').style.display = 'none';
	if(document.getElementById("isAtleastOneGatewayChosen") && document.getElementById("isAtleastOneGatewayChosen").value == 1 && askConfirm == 1) {
		msgToDisplay = 'There is an SMS gateway already active for your account. Choosing a different gateway now will delete the currently active configuration permanently. Are you sure you want to use a different SMS gateway?';
		var actionTakenCallBack = "chooseSMSGateway("+gatewayType+", "+isEdit+", 0)";
		var actionCancelCallBack = "cancelChangeSMSGatewayRequest()";
		var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes, I'm Sure", "No, Cancel", actionTakenCallBack, actionCancelCallBack);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		$('html,body').scrollTop(0);
		return false;

	}
	var formPostData = 'req=14&gatewayType='+gatewayType+'&isEdit='+isEdit;

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:chooseSMSGatewayResponse,
		error:HandleAjaxError
	});
}


function chooseSMSGatewayResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	document.getElementById("chooseGatewayDiv").innerHTML = dataObj.divHTML;
}

function saveTwilioConfig(saveType)
{
	document.getElementById('alertRow').style.display = 'none';
	if(saveType != 3 && saveType != 4)
	{
		txtAccountSID = trim(document.getElementById("txtAccountSID").value);
		txtAuthToken = trim(document.getElementById("txtAuthToken").value);
		txtFromNumber = trim(document.getElementById("txtFromNumber").value);
		txtTestToNumber = trim(document.getElementById("txtTestToNumber").value);
		txtMessage = trim(document.getElementById("txtMessage").value);
	}

	if(saveType == 1)
	{
		if(txtAccountSID == "" || txtAuthToken == "" || txtFromNumber == "")
		{
			var alertMsg = "The following fields are required and cannot be left empty : Account SID, Auth Token, From Number";
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}
	else if(saveType == 2)
	{
		if(txtAccountSID == "" || txtAuthToken == "" || txtFromNumber == "" || txtTestToNumber == "" || txtMessage == "")
		{
			var alertMsg = "All the fields have to be filled up to test the configuration";
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}
	else if(saveType == 3)//Confirm Delete existing twilio config
	{
		msgToDisplay = 'Deleting this configuration will also deactivate your SMS notifications. Do you want to delete it anyway?';
		var actionTakenCallBack = "saveTwilioConfig(4)";
		var actionCancelCallBack = "cancelTwilioDeleteRequest()";
		var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes, Delete", "No, Cancel", actionTakenCallBack, actionCancelCallBack);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		$('html,body').scrollTop(0);
		return false;
	}
	else if(saveType == 4)//Delete existing twilio config
	{
		//Anything else?
	}
	
	var formPostData = 'req=15&saveType='+saveType;
	if(saveType == 1 || saveType == 2)
	{
		formPostData += '&txtAccountSID='+escString(txtAccountSID);
		formPostData += '&txtAuthToken='+escString(txtAuthToken);
		formPostData += '&txtFromNumber='+escString(txtFromNumber);
		formPostData += '&txtTestToNumber='+escString(txtTestToNumber);
		formPostData += '&txtMessage='+escString(txtMessage);
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:saveTwilioConfigResponse,
		error:HandleAjaxError
	});
}

function saveTwilioConfigResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.saveType == 1)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 2)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 3)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 4)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	return false;
}

function cancelTwilioDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}


function saveNexmoConfig(saveType)
{
	document.getElementById('alertRow').style.display = 'none';
	if(saveType != 3 && saveType != 4)
	{
		txtAPIKey = trim(document.getElementById("txtAPIKey").value);
		txtAPISecret = trim(document.getElementById("txtAPISecret").value);
		txtFromNumber = trim(document.getElementById("txtFromNumber").value);
		txtTestToNumber = trim(document.getElementById("txtTestToNumber").value);
		txtMessage = trim(document.getElementById("txtMessage").value);
	}

	if(saveType == 1)
	{
		if(txtAPIKey == "" || txtAPISecret == "" || txtFromNumber == "")
		{
			var alertMsg = "The following fields are required and cannot be left empty : API Key, API Secret, From Number";
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}
	else if(saveType == 2)
	{
		if(txtAPIKey == "" || txtAPISecret == "" || txtFromNumber == "" || txtTestToNumber == "" || txtMessage == "")
		{
			var alertMsg = "All the fields have to be filled up to test the configuration";
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}
	else if(saveType == 3)//Confirm Delete existing Nexmo config
	{
		msgToDisplay = 'Deleting this configuration will also deactivate your SMS notifications. Do you want to delete it anyway?';
		var actionTakenCallBack = "saveNexmoConfig(4)";
		var actionCancelCallBack = "cancelNexmoDeleteRequest()";
		var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes, Delete", "No, Cancel", actionTakenCallBack, actionCancelCallBack);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		$('html,body').scrollTop(0);
		return false;
	}
	else if(saveType == 4)//Delete existing Nexmo config
	{
		//Anything else?
	}
	
	var formPostData = 'req=16&saveType='+saveType;
	if(saveType == 1 || saveType == 2)
	{
		formPostData += '&txtAPIKey='+escString(txtAPIKey);
		formPostData += '&txtAPISecret='+escString(txtAPISecret);
		formPostData += '&txtFromNumber='+escString(txtFromNumber);
		formPostData += '&txtTestToNumber='+escString(txtTestToNumber);
		formPostData += '&txtMessage='+escString(txtMessage);
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:saveNexmoConfigResponse,
		error:HandleAjaxError
	});
}

function saveNexmoConfigResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.saveType == 1)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 2)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 3)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 4)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	return false;
}

function cancelNexmoDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function saveBhashSMSConfig(saveType)
{
	document.getElementById('alertRow').style.display = 'none';
	if(saveType != 3 && saveType != 4)
	{
		txtUsername = trim(document.getElementById("txtUsername").value);
		txtPassword = trim(document.getElementById("txtPassword").value);
		txtSenderID = trim(document.getElementById("txtSenderID").value);
		txtPriority = trim(document.getElementById("txtPriority").value);
		txtTestToNumber = trim(document.getElementById("txtTestToNumber").value);
		txtMessage = trim(document.getElementById("txtMessage").value);
	}

	if(saveType == 1)
	{
		if(txtUsername == "" || txtPassword == "" || txtSenderID == "")
		{
			var alertMsg = "The following fields are required and cannot be left empty : Username, Password, Sender ID, Priority";
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}
	else if(saveType == 2)
	{
		if(txtUsername == "" || txtPassword == "" || txtSenderID == "" || txtTestToNumber == "" || txtMessage == "")
		{
			var alertMsg = "All the fields have to be filled up to test the configuration";
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}
	else if(saveType == 3)//Confirm Delete existing bhashSMS config
	{
		msgToDisplay = 'Deleting this configuration will also deactivate your SMS notifications. Do you want to delete it anyway?';
		var actionTakenCallBack = "saveBhashSMSConfig(4)";
		var actionCancelCallBack = "cancelBhashSMSDeleteRequest()";
		var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes, Delete", "No, Cancel", actionTakenCallBack, actionCancelCallBack);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		$('html,body').scrollTop(0);
		return false;
	}
	else if(saveType == 4)//Delete existing BhashSMS config
	{
		//Anything else?
	}
	
	var formPostData = 'req=17&saveType='+saveType;
	if(saveType == 1 || saveType == 2)
	{
		formPostData += '&txtUsername='+escString(txtUsername);
		formPostData += '&txtPassword='+escString(txtPassword);
		formPostData += '&txtSenderID='+escString(txtSenderID);
		formPostData += '&txtPriority='+escString(txtPriority);
		formPostData += '&txtTestToNumber='+escString(txtTestToNumber);
		formPostData += '&txtMessage='+escString(txtMessage);
	}

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:saveBhashSMSConfigResponse,
		error:HandleAjaxError
	});
}

function saveBhashSMSConfigResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.saveType == 1)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 2)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 3)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	else if(dataObj.saveType == 4)
	{
		if(dataObj.rsno == 1) {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			loadSMSConfig(1, 1);
		} else {
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
	}
	return false;
}

function cancelBhashSMSDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function cancelChangeSMSGatewayRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function getGreetingsConfigForm()
{
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=18';
	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:getGreetingsConfigFormResponse,
		error:HandleAjaxError
	});	
}

function getGreetingsConfigFormResponse(response)
{
	document.getElementById('pageContent').innerHTML = response;

//	loadSMSConfig(1, 0);
}

function clickBirthdayEmailGreetings(elem)
{
	if(elem.checked)
	{
		document.getElementById("divBirthdayEmailTemplatesList").style.display = "";
		document.getElementById("divBirthdayEmailTemplatesPreview").style.display = "";
	}
	else
	{
		document.getElementById("divBirthdayEmailTemplatesList").style.display = "none";
		document.getElementById("divBirthdayEmailTemplatesPreview").style.display = "none";
	}
}

function clickBirthdaySMSGreetings(elem)
{
	if(elem.checked)
	{
		document.getElementById("divBirthdaySMSTemplatesList").style.display = "";
		document.getElementById("divBirthdaySMSTemplatesPreview").style.display = "";
	}
	else
	{
		document.getElementById("divBirthdaySMSTemplatesList").style.display = "none";
		document.getElementById("divBirthdaySMSTemplatesPreview").style.display = "none";
	}
}

function clickWeddingEmailGreetings(elem)
{
	if(elem.checked)
	{
		document.getElementById("divWeddingEmailTemplatesList").style.display = "";
		document.getElementById("divWeddingEmailTemplatesPreview").style.display = "";
	}
	else
	{
		document.getElementById("divWeddingEmailTemplatesList").style.display = "none";
		document.getElementById("divWeddingEmailTemplatesPreview").style.display = "none";
	}
}

function clickWeddingSMSGreetings(elem)
{
	if(elem.checked)
	{
		document.getElementById("divWeddingSMSTemplatesList").style.display = "";
		document.getElementById("divWeddingSMSTemplatesPreview").style.display = "";
	}
	else
	{
		document.getElementById("divWeddingSMSTemplatesList").style.display = "none";
		document.getElementById("divWeddingSMSTemplatesPreview").style.display = "none";
	}
}

function saveAnniversaryGreetingsConfig()
{
	document.getElementById('alertRow').style.display = 'none';

	var isBirthdayEmailGreetingsEnabled=0;
	var isBirthdaySMSGreetingsEnabled=0;
	var isWeddingEmailGreetingsEnabled=0;
	var isWeddingSMSGreetingsEnabled=0;
	var birthdayGreetingsEmailTemplateId=0;
	var birthdayGreetingsSMSTemplateId=0;
	var weddingGreetingsEmailTemplateId=0;
	var weddingGreetingsSMSTemplateId=0;

	if(document.getElementById("chkBirthdayEmailGreetings").checked) {
		isBirthdayEmailGreetingsEnabled = 1;
		birthdayGreetingsEmailTemplateId = document.getElementById("selBDayEmailTemplates").value;
		if(document.getElementById("selBDayEmailTemplates").selectedIndex == 0) {
			var alertMsg = 'Please select an email template for sending birthday greetings. If no templates is available yet, create one and use it. Greetings can be sent only using a template.';
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}

	if(document.getElementById("chkBirthdaySMSGreetings").checked) {
		isBirthdaySMSGreetingsEnabled = 1;
		birthdayGreetingsSMSTemplateId = document.getElementById("selBDaySMSTemplates").value;
		if(document.getElementById("selBDaySMSTemplates").selectedIndex == 0) {
			var alertMsg = 'Please select an SMS template for sending birthday greetings. If no templates is available yet, create one and use it. Greetings can be sent only using a template.';
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}

	if(document.getElementById("chkWeddingEmailGreetings").checked) {
		isWeddingEmailGreetingsEnabled = 1;
		weddingGreetingsEmailTemplateId = document.getElementById("selWeddingEmailTemplates").value;
		if(document.getElementById("selWeddingEmailTemplates").selectedIndex == 0) {
			var alertMsg = 'Please select an email template for sending wedding anniversary greetings. If no templates is available yet, create one and use it. Greetings can be sent only using a template.';
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}

	if(document.getElementById("chkWeddingSMSGreetings").checked) {
		isWeddingSMSGreetingsEnabled = 1;
		weddingGreetingsSMSTemplateId = document.getElementById("selWeddingSMSTemplates").value;
		if(document.getElementById("selWeddingSMSTemplates").selectedIndex == 0) {
			var alertMsg = 'Please select an SMS template for sending wedding anniversary greetings. If no templates is available yet, create one and use it. Greetings can be sent only using a template.';
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
	}

	var formPostData = 'req=19';
	formPostData += '&isBirthdayEmailGreetingsEnabled='+isBirthdayEmailGreetingsEnabled;
	formPostData += '&isBirthdaySMSGreetingsEnabled='+isBirthdaySMSGreetingsEnabled;
	formPostData += '&isWeddingEmailGreetingsEnabled='+isWeddingEmailGreetingsEnabled;
	formPostData += '&isWeddingSMSGreetingsEnabled='+isWeddingSMSGreetingsEnabled;
	formPostData += '&birthdayGreetingsEmailTemplateId='+birthdayGreetingsEmailTemplateId;
	formPostData += '&birthdayGreetingsSMSTemplateId='+birthdayGreetingsSMSTemplateId;
	formPostData += '&weddingGreetingsEmailTemplateId='+weddingGreetingsEmailTemplateId;
	formPostData += '&weddingGreetingsSMSTemplateId='+weddingGreetingsSMSTemplateId;

	$.ajax({
		type:'POST',
		url:doSettingsFile,
		data:formPostData,
		success:saveAnniversaryGreetingsConfigResponse,
		error:HandleAjaxError
	});

	return false;
}

function saveAnniversaryGreetingsConfigResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno == 1) {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
	} else {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
	}
	return false;
}

function cancelFieldOptionDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}
