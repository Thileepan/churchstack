//global variables
serverFile = 'server/doserver';

function onChangeMaritalStatus(obj)
{
	//alert(obj.selectedIndex);
	if(obj.selectedIndex == 2) {
		document.getElementById('divMarriageDate').style.display='';
		document.getElementById('divMarriagePlace').style.display = '';
	} else {
		document.getElementById('divMarriageDate').style.display='none';
		document.getElementById('divMarriagePlace').style.display = 'none';
	}
}

function onChangeRelationShipStatus(obj)
{
	//relationship with familyhead - wife
	if(obj.selectedIndex == 2)
	{
		//var parentProfileIndex = document.getElementById('inputParentID').selectedIndex;
		var selectedParentID = document.getElementById('selectedParentID').value;
		if(selectedParentID > 0) {
			var parentProfileID = selectedParentID;//document.getElementById('inputParentID').options[parentProfileIndex].value;
			document.getElementById('inputMartialStatus').selectedIndex = 2; //married status
			document.getElementById('divMarriageDate').style.display = '';
			document.getElementById('divMarriagePlace').style.display = '';
			document.getElementById('inputMarriageDate').value = document.getElementById('hidParentMarriageDate-' + parentProfileID).value;
			document.getElementById('inputMarriagePlace').value = document.getElementById('hidParentMarriagePlace-' + parentProfileID).value;
		}
	}
	else
	{
		document.getElementById('inputMartialStatus').selectedIndex = 0;
		document.getElementById('divMarriageDate').style.display = 'none';
		document.getElementById('divMarriagePlace').style.display = 'none';
		document.getElementById('inputMarriageDate').value = '';
		document.getElementById('inputMarriagePlace').value = '';
	}
}

function onChangeParentID(obj)
{
	if(obj.selectedIndex > 0) {
		document.getElementById('inputUniqueID').value = document.getElementById('hidParentUniqueID-' + obj.value).value;
		document.getElementById('inputAddress1').value = document.getElementById('hidParentAddr1-' + obj.value).value;
		document.getElementById('inputAddress2').value = document.getElementById('hidParentAddr2-' + obj.value).value;
		document.getElementById('inputAddress3').value = document.getElementById('hidParentAddr3-' + obj.value).value;
		document.getElementById('inputArea').value = document.getElementById('hidParentArea-' + obj.value).value;
		document.getElementById('inputPincode').value = document.getElementById('hidParentPincode-' + obj.value).value;

		var relationshipIndex = document.getElementById('inputRelationship').selectedIndex;
		if(relationshipIndex == 2)
		{
			document.getElementById('inputMartialStatus').selectedIndex = 2; //married status
			document.getElementById('divMarriageDate').style.display = '';
			document.getElementById('divMarriagePlace').style.display = '';
			document.getElementById('inputMarriageDate').value = document.getElementById('hidParentMarriageDate-' + obj.value).value;
			document.getElementById('inputMarriagePlace').value = document.getElementById('hidParentMarriagePlace-' + obj.value).value;
		}
		else
		{
			document.getElementById('inputMartialStatus').selectedIndex = 0;
			document.getElementById('divMarriageDate').style.display = 'none';
			document.getElementById('divMarriagePlace').style.display = 'none';
			document.getElementById('inputMarriageDate').value = '';
			document.getElementById('inputMarriagePlace').value = '';
		}
	}
	else 
	{
		if(document.getElementById('hiddenIsUpdateReq').value == 1) {
			if(document.getElementById('hiddenIsFamilyHead').value == -1) {
				document.getElementById('inputUniqueID').value = document.getElementById('hiddenUniqueID').value;
			} else {
				document.getElementById('inputUniqueID').value = document.getElementById('hiddenMaxUniqueID').value;
			}			
		} else {
			document.getElementById('inputUniqueID').value = document.getElementById('hiddenMaxUniqueID').value;
		}
		
		document.getElementById('inputAddress1').value = "";
		document.getElementById('inputAddress2').value = "";
		document.getElementById('inputAddress3').value = "";
		document.getElementById('inputArea').value = "";
		document.getElementById('inputPincode').value = "";
	}
}

function popOver(id)
{
	//"#example"
	$(id).popover();  
}

function popOut(id)
{
	//"#example"
	$(id).popover('destroy');  
}

function trim(str) {
        return str.replace(/^\s+|\s+$/g,"");
}

$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback ){
    if ( typeof sNewSource != 'undefined' ){
        oSettings.sAjaxSource = sNewSource;
    }
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;

    oSettings.fnServerData( oSettings.sAjaxSource, null, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );

        /* Got the data - add it to the table */
        for ( var i=0 ; i<json.aaData.length ; i++ ){
            that.oApi._fnAddData( oSettings, json.aaData[i] );
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
        that.fnDraw( that );
        that.oApi._fnProcessingDisplay( oSettings, false );

        /* Callback user function - for event handlers etc */
        if ( typeof fnCallback == 'function' ){
            fnCallback( oSettings );
        }
    });
}

function listAllProfiles(profileStatus)
{
	//profileStatus - 1; Active
	//profileStatus - 2; In-Active
	//profileStatus - 3; All

	document.getElementById('listProfiles').className = 'active';
	document.getElementById('addNewProfile').className = '';
	document.getElementById('importProfiles').className = '';
	document.getElementById('backBtnDiv').style.display = 'none';
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('pageContent').innerHTML = '';
	document.getElementById('pageContent').style.display = 'none';
	document.getElementById('listProfilesContent').style.display = '';

	//get profiles filter option value from cookie if exists
	var cName = 'cs_list_profiles_filter_value';
	var cValue = getCookie(cName);
	if(cValue != '') {
		profileStatus = cValue;
	}
	
	var table = '<div class="pull-right">';
		table += 'Filter by profile status: ';
		table += '<select onchange="filterProfilesList(this)">';
			table += '<option value="1"'+ ((profileStatus == 1 || cValue == '')?'selected':'') +'>Active</option>';
			table += '<option value="2"'+ ((profileStatus == 2)?'selected':'') +'>Inactive</option>';
			table += '<option value="3"'+ ((profileStatus == 3)?'selected':'') +'>All</option>';
		table += '</select>';
	table += '</div>';
	table += '<table id="listProfilesTable" class="table table-striped"><thead><tr><th>Profile ID</th><th></th><th>Name</th><th>Date Of Birth</th><th>Age</th><th>Landline</th><th>Mobile Number</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('listProfilesContent').innerHTML = table;
	
	oTable = $('#listProfilesTable').dataTable( {
		"aoColumns": [
			{ "sWidth": "10%" },
			{ "sWidth": "5%"  },
			{ "sWidth": "25%" },
			{ "sWidth": "10%"  },
			{ "sWidth": "10%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "10%"  },
		],
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": "server/doserver",
		"iDisplayLength":100,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=1&profileStatus=" + profileStatus,
                "success": fnCallback
            } );
        }
	});
}

function getAddOrEditProfileForm(val, profileID)
{
	isEdit = val;
	document.getElementById('listProfiles').className = '';
	document.getElementById('addNewProfile').className = ((isEdit)?'':'active');
	document.getElementById('importProfiles').className = '';
	document.getElementById('backBtnDiv').style.display = 'none';
	document.getElementById('loadingDiv').style.display = '';
	document.getElementById('alertRow').style.display = 'none';	

	var formPostData = 'req=2&isEdit=' + isEdit;
	if(isEdit) {
		formPostData += '&profileID=' + profileID;
	}
	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:getAddOrEditProfileFormResponse,
		error:HandleAjaxError
	});
}

function getAddOrEditProfileFormResponse(response)
{
	document.getElementById('loadingDiv').style.display = 'none';
	document.getElementById('pageHeader').innerHTML = ((isEdit)?'Edit Profile':'Add New Profile');
	if(isEdit) {
		document.getElementById('backBtnDiv').style.display = '';
		document.getElementById('listProfilesContent').style.display = 'none';
	} else {
		document.getElementById('listProfilesContent').innerHTML = '';
		document.getElementById('listProfilesContent').style.display = 'none';		
	}
	document.getElementById('pageContent').style.display = '';
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputSalutation').focus();
	$('#inputDOB').datepicker({
		autoclose: true
	});
	$('#inputMarriageDate').datepicker({
		autoclose: true
	});
	
	/*
	//browse file for upload	
	$('input[id=myPhotoPath]').change(function() {
		$('#inputMyPhotoPath').val($(this).val());
		var previewImgID = 'imgPreviewMyPhoto';
		document.getElementById('divPreviewMyPhoto').style.display = '';
		previewProfileImage(this, previewImgID);
	});

	$('input[id=familyPhotoPath]').change(function() {
		$('#inputFamilyPhotoPath').val($(this).val());
		var previewImgID = 'imgPreviewFamilyPhoto';
		document.getElementById('divPreviewFamilyPhoto').style.display = '';
		previewProfileImage(this, previewImgID);
	});
	*/

	var parentIDArr = (document.getElementById('hiddenParentID').value).split(",");
	var parentNameArr = (document.getElementById('hiddenParentName').value).split(",");
	var parentUniqueIDArr = (document.getElementById('hiddenParentUniqueID').value).split(",");

	if(parentIDArr.length > 0)
	{
		var sourceList = new Array();
		for(i=0; i<parentIDArr.length; i++)
		{
			//console.log(parentIDArr[i]);
			//sourceList.push({"id":parentIDArr[i], "name:"parentNameArr[i]});
			sourceList.push({"id":parentIDArr[i], "name":parentUniqueIDArr[i]});
		}
	}

	$('#inputParentID').typeahead({
		source: sourceList,
		display: 'name',
		val: 'id',
		itemSelected: onSelectingParentID
    });


	//Custom Fields
	var fieldIDAndTypeArr = document.getElementById('hidddenFieldIDAndType').value.split(",");
	if(fieldIDAndTypeArr.length > 0)
	{
		for(i=0; i<fieldIDAndTypeArr.length; i++)
		{
			var fieldArr = fieldIDAndTypeArr[i].split("::");
			if(fieldArr.length > 0)
			{
				var fieldID = fieldArr[0];
				var fieldType = fieldArr[1];

				if(fieldType == 4) {
					$('#' + fieldID).datepicker({
						autoclose: true
					});
				}				
			}
		}
	}
}

function validateSelectedParentID()
{
	/*
	var parentInfo =  (document.getElementById('inputParentID').value).split("-");
	var parentUniqueIDArr = (document.getElementById('hiddenParentUniqueID').value).split(",");
	if (parentInfo instanceof Array) {
		if(parentInfo.length == 2) {
			var uniqueID = parentInfo[2];
			if(inArray(uniqueID, parentUniqueIDArr))
			{
				
			}
		}
	}
	*/
}

function onSelectingParentID(item, val, text) {
	//alert("Val:::" + val);
    //console.log(item);
    //$('.alert').show().html('You selected <strong>' + val + '</strong>: <strong>' + text + '</strong>');
	document.getElementById('selectedParentID').value = val;

	document.getElementById('inputUniqueID').value = document.getElementById('hidParentUniqueID-' + val).value;
	document.getElementById('inputAddress1').value = document.getElementById('hidParentAddr1-' + val).value;
	document.getElementById('inputAddress2').value = document.getElementById('hidParentAddr2-' + val).value;
	document.getElementById('inputAddress3').value = document.getElementById('hidParentAddr3-' + val).value;
	document.getElementById('inputArea').value = document.getElementById('hidParentArea-' + val).value;
	document.getElementById('inputPincode').value = document.getElementById('hidParentPincode-' + val).value;
	document.getElementById('inputLandline').value = document.getElementById('hidParentHomePhone-' + val).value;

	var relationshipIndex = document.getElementById('inputRelationship').selectedIndex;
	if(relationshipIndex == 2)
	{
		document.getElementById('inputMartialStatus').selectedIndex = 2; //married status
		document.getElementById('divMarriageDate').style.display = '';
		document.getElementById('divMarriagePlace').style.display = '';
		document.getElementById('inputMarriageDate').value = document.getElementById('hidParentMarriageDate-' + val).value;
		document.getElementById('inputMarriagePlace').value = document.getElementById('hidParentMarriagePlace-' + val).value;
	}
	else
	{
		document.getElementById('inputMartialStatus').selectedIndex = 0;
		document.getElementById('divMarriageDate').style.display = 'none';
		document.getElementById('divMarriagePlace').style.display = 'none';
		document.getElementById('inputMarriageDate').value = '';
		document.getElementById('inputMarriagePlace').value = '';
	}
}

function HandleAjaxError(obj,errorType)
{
	alert("Unable to process your request. Please try again!");
	//console.log(obj);
	//alert(obj.statusText);
	//alert(obj.status + ':::::' + errorType);
	//return false;
	//console.log(obj);
}

function addOrUpdateProfileAjaxError(obj,errorType)
{
	alert("Unable to process your request. Please try again!");
}

function appendZero(val)
{
	return (val.length == 1)?'0'+val:val;
}

function addOrUpdateProfile(val)
{
	isUpdate = val;
	var salutationIndex = document.getElementById('inputSalutation').selectedIndex;
	var salutationID = document.getElementById('inputSalutation').options[salutationIndex].value;
	var firstName = document.getElementById('inputFirstName').value;
	var middleName = document.getElementById('inputMiddleName').value;
	var lastName = document.getElementById('inputLastName').value;
	//var parentProfileIndex = document.getElementById('inputParentID').selectedIndex;
	//var parentProfileID = document.getElementById('inputParentID').options[parentProfileIndex].value;
	var parentProfileID = document.getElementById('selectedParentID').value;
	var uniqueID = document.getElementById('inputUniqueID').value;
	var dob = document.getElementById('inputDOB').value;
	var genderIndex = document.getElementById('inputGender').selectedIndex;
	var genderID = document.getElementById('inputGender').options[genderIndex].value;
	var relationshipIndex = document.getElementById('inputRelationship').selectedIndex;
	var relationshipID = document.getElementById('inputRelationship').options[relationshipIndex].value;
	var maritalIndex = document.getElementById('inputMartialStatus').selectedIndex;
	var maritalStatusID = document.getElementById('inputMartialStatus').options[maritalIndex].value;
	var marriageDate = document.getElementById('inputMarriageDate').value;
	var marriagePlace = document.getElementById('inputMarriagePlace').value;
	var address1 = document.getElementById('inputAddress1').value;
	var address2 = document.getElementById('inputAddress2').value;
	var address3 = document.getElementById('inputAddress3').value;
	var area = document.getElementById('inputArea').value;
	var pincode = document.getElementById('inputPincode').value;
	var landline = document.getElementById('inputLandline').value;
	var workPhone = document.getElementById('inputWorkPhone').value;
	var mobile1 = document.getElementById('inputMobile1').value;
//	var mobile2 = document.getElementById('inputMobile2').value;
	var email = document.getElementById('inputEmail').value;
	var profileStatusIndex = document.getElementById('inputProfileStatus').selectedIndex;
	var profileStatusID = document.getElementById('inputProfileStatus').options[profileStatusIndex].value;
	var notes = document.getElementById('inputNotes').value;
	var isBabtisedIndex = document.getElementById('inputBabtised').selectedIndex;
	var isBabtised = document.getElementById('inputBabtised').options[isBabtisedIndex].value;
	var isConfirmedIndex = document.getElementById('inputConfirmation').selectedIndex;
	var isConfirmed = document.getElementById('inputConfirmation').options[isConfirmedIndex].value;
	var occupation = document.getElementById('inputOccupation').value;
	var isAnotherChurchMemberIndex = document.getElementById('inputIsAnotherChurchMember').selectedIndex;
	var isAnotherChurchMember = document.getElementById('inputIsAnotherChurchMember').options[isAnotherChurchMemberIndex].value;
	var profileID = document.getElementById('hiddenProfileID').value;	

	//format dates
	dob = convertDateToDBFormat(dob);
	marriageDate = convertDateToDBFormat(marriageDate);	
	uniqueID = uniqueID.substring(3);

	//notifications
	var smsNotification = ((document.getElementById('inputSMSNotification').checked)?1:0);
	var emailNotification = ((document.getElementById('inputEmailNotification').checked)?1:0);
	
	var alertMsg = '';
	if(firstName == '')	{
		alertMsg = 'Name is missing';
	}
	/*
	else if(uniqueID == '') {
		alertMsg = 'Unique ID is missing';
	}
	else if (isNaN(uniqueID)) {
		alertMsg = 'MemberID should be numeric';
	}*/

	//custom profile fields
	var customFields = '';
	var fieldIDAndTypeArr = document.getElementById('hidddenFieldIDAndType').value;
	if(fieldIDAndTypeArr != '')
	{
		fieldIDAndTypeArr = fieldIDAndTypeArr.split(",");
		if(fieldIDAndTypeArr.length > 0)
		{
			for(i=0; i<fieldIDAndTypeArr.length; i++)
			{
				var fieldArr = fieldIDAndTypeArr[i].split("::");
				if(fieldArr.length > 0)
				{
					var fieldID = fieldArr[0];
					var fieldType = fieldArr[1];
					var fieldName = fieldArr[2];
					var isRequired = parseInt(fieldArr[3]);
					var fieldValue;
					if(fieldType == 7) {
						fieldValue = ((document.getElementById(fieldID).checked)?1:0);
					} else {
						fieldValue = document.getElementById(fieldID).value;
					}					
					
					if(isRequired) {
						if(fieldValue == '') {
							alertMsg = fieldName + ' is missing';
							break;
						}
					}

					if(fieldValue != '')
					{
						if(fieldID == 2) {
							if(isNaN(fieldValue))
							{
								alertMsg = fieldName + ' is not a valid number';
								break;
							}
						}
					}

					if(fieldType == 4) {
						fieldValue = convertDateToDBFormat(fieldValue);
					}
					if(fieldValue != '' || fieldValue == 0) {
						if(customFields != '') {
							customFields += '<:|:>';
						}
						customFields += fieldID.split('-')[1] + "::" + fieldValue;					
					}
				}
			}
		}
	}

	if(alertMsg.length > 0) {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		$('html,body').scrollTop(0);
		return false;
	}
	
	var formPostData = 'req=6';
	formPostData += '&salutationID=' + salutationID;
	formPostData += '&firstName=' + escString(firstName);
	formPostData += '&middleName=' + escString(middleName);
	formPostData += '&lastName=' + escString(lastName);
	formPostData += '&parentID=' + parentProfileID;
	formPostData += '&uniqueID=' + uniqueID;
	formPostData += '&dob=' + dob;
	formPostData += '&genderID=' + genderID;
	formPostData += '&relationshipID=' + relationshipID;
	formPostData += '&maritalStatusID=' + maritalStatusID;
	formPostData += '&marriageDate=' + marriageDate;
	formPostData += '&marriagePlace=' + escString(marriagePlace);
	formPostData += '&address1=' + escString(address1);
	formPostData += '&address2=' + escString(address2);
	formPostData += '&address3=' + escString(address3);
	formPostData += '&area=' + escString(area);
	formPostData += '&pincode=' + escString(pincode);
	formPostData += '&landline=' + escString(landline);
	formPostData += '&workPhone=' + escString(workPhone);
	formPostData += '&mobile1=' + escString(mobile1);
//	formPostData += '&mobile2=' + mobile2;
	formPostData += '&email=' + escString(email);
	formPostData += '&profileStatusID=' + profileStatusID;
	formPostData += '&notes=' + escString(notes);
	formPostData += '&isBabtised=' + isBabtised;
	formPostData += '&isConfirmed=' + isConfirmed;
	formPostData += '&occupation=' + escString(occupation);
	formPostData += '&isAnotherChurchMember=' + isAnotherChurchMember;
	formPostData += '&isUpdate=' + isUpdate;	
	formPostData += '&profileID=' + profileID;
	formPostData += '&customFields=' + customFields;
	formPostData += '&smsNotification=' + smsNotification;
	formPostData += '&emailNotification=' + emailNotification;
	
	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:addOrUpdateProfileResponse,
		error:addOrUpdateProfileAjaxError		
	});
}

function addOrUpdateProfileResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno == 1)
	{
		var profileID = dataObj.profile_id;
		var alertType = 1;
		//var msgToDisplay = (isUpdate)?'Profile has been updated successfully! ':'Profile has been created successfully ';
		var msgToDisplay = dataObj.msg;
		//msgToDisplay += '<a href="#" onclick="showProfileDetails(' + profileID +');">View Profile</a>';
		//listAllProfiles(1);
		showProfileDetails(profileID);
	} else {
		var alertType = 2;
		//var msgToDisplay = (isUpdate)?'Profile failed to update.':'Profile failed to create.';		
		var msgToDisplay = dataObj.msg;
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function getAlertDiv(alertType, alertMsg, actionToDo, actionTakenMsg, actionCancelMsg, actionTakenCallBack, actionCancelCallBack)
{
	if(typeof(actionToDo) === 'undefined') actionToDo = 0;
	if(typeof(actionTakenMsg) === 'undefined') actionTakenMsg = '';
	if(typeof(actionCancelMsg) === 'undefined') actionCancelMsg = '';

	var className;
	var title;
	if(alertType == 1) {
		className = 'alert-success';
		title = 'Well done!';
	} else if(alertType == 2) {
		className = 'alert-error';
		title = 'Oh snap!'
	} else if(alertType == 3) {
		className = 'alert-info';
		title = 'Heads up!';
	} else if(alertType == 4) {
		className = '';
		title = 'Warning!';
	} 

	var alertDiv = '<div class="alert ' + className + '"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>' + title + '</strong>&nbsp;'+ alertMsg;
	if(actionToDo) {
		alertDiv += '<br><button class="btn btn-danger" type="button" onclick="'+actionTakenCallBack+'">'+actionTakenMsg+'</button>&nbsp;<button class="btn" type="button" onclick="'+actionCancelCallBack+'">'+actionCancelMsg+'</button>';
	}
	alertDiv += '</div>';
	return alertDiv;
}

function deleteProfileConfirmation(profileID, uniqueID, profileName, isHead)
{
	var msgToDisplay = 'You are requesting to delete <a href="#">' + profileName + ' (' + uniqueID + ')</a> profile.';
	if(isHead) {
		msgToDisplay += ' This profile is family head and it will delete your dependant profile also.';
	}

	msgToDisplay += ' Please confirm your request?';
	var actionTakenCallBack = "deleteProfile(" + profileID + "," + isHead + ")";
	var actionCanelCallBack = "cancelProfileDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Delete", "Cancel", actionTakenCallBack, actionCanelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelProfileDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteProfile(profileID, isHead)
{
	var formPostData = 'req=9&profile=' + profileID + '&isProfileHead=' + isHead;

	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:deleteProfileResponse,
		error:HandleAjaxError
	});
}

function deleteProfileResponse(response)
{
	var resultToUI;
	if(response) {
		resultToUI = getAlertDiv(1, 'Profile has been deleted successfully!');
		listAllProfiles(1);
	} else {
		resultToUI = getAlertDiv(2, 'Profile failed to delete.');
	}
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function authenticateUser()
{
	/* Example to get timezone : can be removed later* /
	var tz = jstz.determine();
	alert(tz.name());
	return false;
	/**/
	var user = document.getElementById('inputUser').value;
	var pass = document.getElementById('inputPwd').value;
	var formPostData = 'req=authenticate&username=' + escString(user) + '&password=' + escString(pass);
	var errorMessage = '';

	if(user == '') {
		document.getElementById('inputUser').focus();
		errorMessage = 'Please enter a valid email address';
	} else if(!isValidEmail(user)) {
		document.getElementById('inputUser').focus();
		errorMessage = 'Please enter a valid email address';
	} else if(pass == '') {
		document.getElementById('inputPwd').focus();
		errorMessage = 'Please enter a valid password';
	}

	if(errorMessage != '') {
		noty({type: 'error', text: errorMessage});
		return false;
	}
	
	var loginBtn = $('#btnSignIn');
	loginBtn.button('loading');
//	document.getElementById('divSignInBtn').style.display = 'none';
//	document.getElementById('divLoadingSearchImg').style.display = '';

	$.ajax({
		type:'POST',
		url:'server/doauth',
		data:formPostData,
		success:authenticateUserResponse,
		error:HandleAjaxError
	});
}

function authenticateUserResponse(response)
{
	var resultToUI;
	var dataObj = eval("(" + response + ")" );
	var isAuthValid = dataObj.isAuthValid;
	var allowLogin = dataObj.allowLogin;
	var loginMessage = dataObj.loginMessage;
	var userStatusNumber = dataObj.userStatusNumber;//make use of this if needed
	var churchStatusNumber = dataObj.churchStatusNumber;//make use of this if needed
	if(isAuthValid == 1 && allowLogin == 1) {
		window.location.href = 'dashboard';
	} else {
		noty({type: 'error', text: loginMessage});
		var loginBtn = $('#btnSignIn');
		loginBtn.button('reset');
	}
	return false;
}

function showProfileDetails(id)
{
	profileID = id;
	var formPostData = 'req=10&profile=' + profileID;
	document.getElementById('backBtnDiv').style.display = '';
	document.getElementById('alertRow').style.display = 'none';

	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:showProfileDetailsResponse,
		error:HandleAjaxError
	});
}

function showProfileDetailsResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'View Profile';
	document.getElementById('backBtnDiv').style.display = '';
	document.getElementById('listProfilesContent').style.display = 'none';
	document.getElementById('pageContent').style.display = '';
	document.getElementById('pageContent').innerHTML = response;
	showProfileSummary(profileID);
}

function showProfileSummary(profileID)
{
	_profileID = profileID;
	document.getElementById('profileDiv').className = 'tab-pane active';
	document.getElementById('subscriptionDiv').className = 'tab-pane';
	document.getElementById('harvestDiv').className = 'tab-pane';
	var formPostData = 'req=11&profile=' + profileID;

	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:getProfileSummaryResponse,
		error:HandleAjaxError
	});
}

function getProfileSummaryResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'View Profile';
	document.getElementById('profileDiv').innerHTML = response;

	var profilePhotoPath = $("#profilePhotoPath");
	profilePhotoPath.on('change', function(){
		document.getElementById('btnProfilePhotoUpload').click();
	});

	$('#existFamilyHeadLink').tooltip();
	$('#newFamilyHeadLink').tooltip();

	var options = { 
		beforeSend: function() 
		{
			//document.getElementById('spanImportBtn').style.display = 'none';
			//document.getElementById('spanImportProg').style.display = '';

			//$("#progress").show();
			//clear everything
			//$("#bar").width('0%');
			//$("#message").html("");
			//$("#percent").html("0%");
		},
		uploadProgress: function(event, position, total, percentComplete) 
		{
			//$("#bar").width(percentComplete+'%');
			//$("#percent").html(percentComplete+'%');		
		},
		success: function() 
		{
			//$("#bar").width('100%');
			//$("#percent").html('100%');
			//document.getElementById('spanImportProg').innerHTML = 'Import is in progress. Don\'t refresh the page. Please wait...';
			//alert("sktgr");
			showProfileSummary(_profileID);

		},
		complete: function(response) 
		{
			$("#message").html("<font color='green'>"+response.responseText+"</font>");
			document.getElementById('spanImportProg').innerHTML =  (response.responseText);
		},
		error: function()
		{
			$("#message").html("<font color='red'> ERROR: unable to upload files</font>");

		}
	};

	$("#profilePhotoForm").ajaxForm(options);
}

function getImportProfileForm()
{
	document.getElementById('listProfiles').className = '';
	document.getElementById('addNewProfile').className = '';
	document.getElementById('importProfiles').className = 'active';
//	document.getElementById('listUsers').className = '';
//	document.getElementById('addNewUser').className = '';
	//document.getElementById('loadingDiv').style.display = '';
	document.getElementById('backBtnDiv').style.display = 'none';
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=12';

	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:getImportProfileFormResponse,
		error:HandleAjaxError
	});
}

function getImportProfileFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'Import Your Profiles';
	document.getElementById('listProfilesContent').innerHTML = '';
	document.getElementById('listProfilesContent').style.display = 'none';
	document.getElementById('pageContent').style.display = '';
	document.getElementById('pageContent').innerHTML = response;

	//browse file for upload
	$('input[id=filePath]').change(function() {
		console.log($(this));
		$('#inputImportFilePath').val($(this).val());
	});

	var options = { 
		beforeSend: function() 
		{
			document.getElementById('spanImportBtn').style.display = 'none';
			document.getElementById('spanImportProg').style.display = '';

			//$("#progress").show();
			//clear everything
			$("#bar").width('0%');
			$("#message").html("");
			$("#percent").html("0%");
		},
		uploadProgress: function(event, position, total, percentComplete) 
		{
			$("#bar").width(percentComplete+'%');
			$("#percent").html(percentComplete+'%');		
		},
		success: function() 
		{
			$("#bar").width('100%');
			$("#percent").html('100%');
			document.getElementById('spanImportProg').innerHTML = 'Import is in progress. Don\'t refresh the page. Please wait...';
//			alert("sktgr");

		},
		complete: function(response) 
		{
			$("#message").html("<font color='green'>"+response.responseText+"</font>");
			document.getElementById('spanImportProg').innerHTML =  (response.responseText);
		},
		error: function()
		{
			$("#message").html("<font color='red'> ERROR: unable to upload files</font>");

		}
	};

	$("#myForm").ajaxForm(options);
}

function migrateProfile(option, profileID, parentProfileID, parentProfileStatus)
{
	var resultToUI, formPostData;
	gRequest = option;
	gProfileID = profileID;
	
	document.getElementById('alertRow').style.display = 'none';
	if(gRequest == 1)
	{
		//change this profile as family head
		/*
		if(parentProfileStatus == 1)
		{
			resultToUI = getAlertDiv(2, 'Please change the family head profile status into inactive/expired and then try again.');
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = resultToUI;
			return false;
		}
		else
		*/		
		{
			formPostData = 'req=14&option=1';
			formPostData += '&profileID=' + profileID;
			formPostData += '&parentProfileID=' + parentProfileID;
			formPostData += '&parentProfileStatus' + parentProfileStatus;			
		}
	}
	else
	{
		//change this profile into new family head
		formPostData = 'req=14&option=2';
		formPostData += '&profileID=' + profileID;
	}

	$.ajax({
		type:'POST',
		url:serverFile,
		data:formPostData,
		success:migrateProfileResponse,
		error:HandleAjaxError
	});
}

function migrateProfileResponse(response)
{
	//var dataObj = eval("(" + response + ")" );
	//var status = dataObj.status;
	//var profileID = dataObj.profileID;

	document.getElementById('alertRow').style.display = '';
	if(response)
	{
		var message = ((gRequest == 1)?'Profile has been migrated as family head and dependants are linked to this new head however the relationship status with family head will not be changed automatically.':'Profile has been migrated successfully into new family head.');
		document.getElementById('alertDiv').innerHTML = getAlertDiv(1, message);
		showProfileSummary(gProfileID);
	}
	else
	{
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, 'Unable to process your request. Please try again.');
	}
}

function goBackProfilePage()
{
	document.getElementById('listProfilesContent').style.display = '';
	document.getElementById('pageContent').style.display = 'none'; 
	document.getElementById('pageHeader').innerHTML = 'All Profiles';
	document.getElementById('listProfiles').className = 'active';
	document.getElementById('backBtnDiv').style.display = 'none';
}

function previewProfileImage(input, previewImgID) {
//	files = input.target.files;
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#' + previewImgID).attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

function signUpNewAccount()
{
	//document.getElementById('alertRow').style.display = 'none';


	document.getElementById('church').value = trim(document.getElementById('church').value);
//	document.getElementById('location').value = trim(document.getElementById('location').value);
	document.getElementById('name').value = trim(document.getElementById('name').value);
	document.getElementById('email').value = trim(document.getElementById('email').value);
//	document.getElementById('phone').value = trim(document.getElementById('phone').value);
	document.getElementById('referrerEmail').value = trim(document.getElementById('referrerEmail').value);
	document.getElementById('password').value = trim(document.getElementById('password').value);
	var churchName = document.getElementById('church').value;
	var churchLocation = "";//document.getElementById('location').value;
	var name = document.getElementById('name').value;
	var email = document.getElementById('email').value;
	var phone = "";//document.getElementById('phone').value;
	var referrerEmail = trim(document.getElementById('referrerEmail').value);
	var password = document.getElementById('password').value;
	var securityText = trim(document.getElementById('securityText').value);

	var formPostData = 'req=signup';
	formPostData += '&churchName=' + escString(churchName);
	formPostData += '&churchLocation=' + escString(churchLocation);
	formPostData += '&name=' + escString(name);
	formPostData += '&email=' + escString(email);
	formPostData += '&phone=' + escString(phone);
	formPostData += '&referrerEmail=' + escString(referrerEmail);
	formPostData += '&password=' + escString(password);
	formPostData += '&securityText=' + securityText;

	var errorMessage = '';
	if(churchName == '') {
		document.getElementById('church').focus();
		errorMessage = 'Church Name cannot be empty.';
	} else if(email == '') {
		document.getElementById('email').focus();
		errorMessage = 'Email field cannot be empty.';
	} else if(!isValidEmail(email)) {
		document.getElementById('email').focus();
		errorMessage = 'Enter a valid email address in the Email field';
	} else if(document.getElementById('password').value == "") {
		document.getElementById('password').focus();
		errorMessage = 'Password field cannot be empty';
	} else if(document.getElementById('password').value != document.getElementById('confirmPassword').value) {
		document.getElementById('confirmPassword').focus();
		errorMessage = 'Passwords do not match.';
	} else if(name == '') {
		document.getElementById('name').focus();
		errorMessage = 'Name field cannot be empty.';
	} else if(securityText == '') {
		document.getElementById('securityText').focus();
		errorMessage = 'Security check field cannot be empty. Type the characters shown in the security image.';
	}

	if(errorMessage != '') {
		noty({type: 'error', text: errorMessage});
		return false;
	}

	/** /
	document.getElementById('spanSignUpBtn').style.display = 'none';
	document.getElementById('spanLoadingImg').style.display = '';
	/**/

	var signUpBtn = $('#btnSignUp');
	signUpBtn.button('loading');

	$.ajax({
		type:'POST',
		url:'server/doauth',
		data:formPostData,
		success:signUpNewAccountResponse,
		error:HandleAjaxError
	});
}

function signUpNewAccountResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var resultCode = dataObj[0];
	var resultMessage = dataObj[1];

	if(resultCode == 1)
	{
		window.location.href = 'user/moredata';
	}
	else
	{
		var signUpBtn = $('#btnSignUp');
		signUpBtn.button('reset');
		noty({type: 'error', text: resultMessage});
	}
	/** /
	document.getElementById('alertRow').style.display = '';
	document.getElementById('spanSignUpBtn').style.display = '';
	document.getElementById('spanLoadingImg').style.display = 'none';

	if(resultCode == 1) {
		var resultToUI = getAlertDiv(1, resultMessage);
		//var accountDetails = dataObj[2];
	} else {
		var resultToUI = getAlertDiv(2, resultMessage);
	}
	document.getElementById('alertDiv').innerHTML = resultToUI;	
	$("html, body").animate({ scrollTop: 0 }, 1000);
	/**/
}

function reloadSignupCaptcha()
{
	var randomNum = Math.random();//used to fix cache issues in firefox and IE
	document.getElementById("captchaSpan").innerHTML = '<img src="plugins/simplecaptcha/image?'+randomNum+'" alt="security image" />';
	return false;
}

function saveChurchMiscDetails()
{
	var churchDesc = trim(document.getElementById('churchDesc').value);
	var churchAddr = trim(document.getElementById('churchAddr').value);
	var churchEmail = trim(document.getElementById('churchEmail').value);
	var churchLandLine = trim(document.getElementById('churchLandLine').value);
	var churchMobile = trim(document.getElementById('churchMobile').value);
	var churchWebsite = trim(document.getElementById('churchWebsite').value);
	var churchCountryID = trim(document.getElementById('churchCountryID').value);
	var churchTimeZone = trim(document.getElementById('churchTimeZone').value);
	var churchCurrencyID = trim(document.getElementById('churchCurrencyID').value);

	var formPostData = 'req=savechurchmiscdetails';
	formPostData += '&churchDesc=' + escString(churchDesc);
	formPostData += '&churchAddr=' + escString(churchAddr);
	formPostData += '&churchEmail=' + escString(churchEmail);
	formPostData += '&churchLandLine=' + escString(churchLandLine);
	formPostData += '&churchMobile=' + escString(churchMobile);
	formPostData += '&churchWebsite=' + escString(churchWebsite);
	formPostData += '&churchCountryID=' + churchCountryID;
	formPostData += '&churchTimeZone=' + churchTimeZone;
	formPostData += '&churchCurrencyID=' + churchCurrencyID;

	var saveBtn = $('#btnSaveDetails');
	saveBtn.button('loading');

	$.ajax({
		type:'POST',
		url:'../server/doauth',
		data:formPostData,
		success:saveChurchMiscDetailsResponse,
		error:HandleAjaxError
	});
}

function saveChurchMiscDetailsResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.resno == 1)
	{
		window.location.href = '../dashboard';
	}
	else
	{
		var saveBtn = $('#btnSaveDetails');
		saveBtn.button('reset');
		alert(dataObj.msg);
	}

	return false;
}

function filterProfilesList(obj)
{
	var index = obj.selectedIndex;
	var profileStatus = obj.options[index].value;
	
	//set cookie to remember the filter option.
	var exDays = 1;
	var cName = 'cs_list_profiles_filter_value';
	var cValue = profileStatus;
	setCookie(cName, cValue, exDays);

	listAllProfiles(profileStatus);
}