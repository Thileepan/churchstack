//global variables
doSettingsFile = 'server/dosettings';

function listProfileOptions(opt)
{
	if(opt == 1) {
		document.getElementById('listSalutationOptions').className = 'active';
		document.getElementById('listRelationshipOptions').className = '';
		document.getElementById('listMaritalOptions').className = '';
		document.getElementById('listProfileStatusOptions').className = '';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	} else if(opt == 2) {
		document.getElementById('listSalutationOptions').className = '';
		document.getElementById('listRelationshipOptions').className = 'active';
		document.getElementById('listMaritalOptions').className = '';
		document.getElementById('listProfileStatusOptions').className = '';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	} else if(opt == 3) {
		document.getElementById('listSalutationOptions').className = '';
		document.getElementById('listRelationshipOptions').className = '';
		document.getElementById('listMaritalOptions').className = 'active';
		document.getElementById('listProfileStatusOptions').className = '';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	} else if(opt == 4) {
		document.getElementById('listSalutationOptions').className = '';
		document.getElementById('listRelationshipOptions').className = '';
		document.getElementById('listMaritalOptions').className = '';
		document.getElementById('listProfileStatusOptions').className = 'active';
		document.getElementById('listProfileCustomFields').className = '';
		document.getElementById('listUsers').className = '';
		document.getElementById('addNewUser').className = '';
	}
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=1&opt=' + opt;
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

	var formPostData = 'req=2&isUpdate=' + isUpdate + '&optionValue=' + optionValue + '&settingID=' + settingID;
	if(isUpdate) {
		formPostData += '&optionID=' + optionID;
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

function deleteOption(rowID)
{
	//var rowID = document.getElementById('hiddenLastEditedRow').value;
	var optionID = document.getElementById('inputEditOptionID-' + rowID).value;
	var settingID = document.getElementById('hiddenSettingID').value;
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

	var formPostData = 'req=6&userName=' + user + '&password=' + pwd + '&isUpdate=' + isUpdate +'&userStatus=' + userStatus;
	if(isUpdate) {
		formPostData += '&userID=' + userID + "&prevUser=" + prevUserName;
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
	document.getElementById('pageHeader').innerHTML = 'List Of Custom Profile Fields';
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
	formPostData += '&fieldName=' + fieldName;
	formPostData += '&fieldType=' + fieldType;
	formPostData += '&fieldOptions=' + fieldOptions;
	formPostData += '&isRequired=' + isRequired;
	formPostData += '&validationString=' + validationString;
	formPostData += '&displayOrder=' + displayOrder;
	formPostData += '&fieldHelpMsg=' + fieldHelpMsg;
	if(isUpdate) {
		formPostData += '&fieldID=' + fieldID;
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