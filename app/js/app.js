//global variables
serverFile = 'server/doserver.php';

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

function listAllProfiles(opt)
{
	document.getElementById('listProfiles').className = 'active';
	document.getElementById('addNewProfile').className = '';
	document.getElementById('importProfiles').className = '';
	document.getElementById('backBtnDiv').style.display = 'none';
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('pageContent').innerHTML = '';
	document.getElementById('pageContent').style.display = 'none';
	document.getElementById('listProfilesContent').style.display = '';

	if(opt == 1) {
		var table = '<table id="listProfilesTable" class="table table-striped"><thead><tr><th>Member ID</th><th></th><th>Name</th><th>Date Of Birth</th><th>Age</th><th>Landline</th><th>Mobile Number</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
		document.getElementById('listProfilesContent').innerHTML = table;
	}
	
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
        "sAjaxSource": "server/doserver.php",
		"iDisplayLength":100,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=1",
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
					var fieldValue = document.getElementById(fieldID).value;
					
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
					if(fieldValue != '') {
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
		return false;
	}
	
	var formPostData = 'req=6';
	formPostData += '&salutationID=' + salutationID;
	formPostData += '&firstName=' + firstName;
	formPostData += '&middleName=' + middleName;
	formPostData += '&lastName=' + lastName;
	formPostData += '&parentID=' + parentProfileID;
	formPostData += '&uniqueID=' + uniqueID;
	formPostData += '&dob=' + dob;
	formPostData += '&genderID=' + genderID;
	formPostData += '&relationshipID=' + relationshipID;
	formPostData += '&maritalStatusID=' + maritalStatusID;
	formPostData += '&marriageDate=' + marriageDate;
	formPostData += '&marriagePlace=' + marriagePlace;
	formPostData += '&address1=' + address1;
	formPostData += '&address2=' + address2;
	formPostData += '&address3=' + address3;
	formPostData += '&area=' + area;
	formPostData += '&pincode=' + pincode;
	formPostData += '&landline=' + landline;
	formPostData += '&workPhone=' + workPhone;
	formPostData += '&mobile1=' + mobile1;
//	formPostData += '&mobile2=' + mobile2;
	formPostData += '&email=' + email;
	formPostData += '&profileStatusID=' + profileStatusID;
	formPostData += '&notes=' + notes;
	formPostData += '&isBabtised=' + isBabtised;
	formPostData += '&isConfirmed=' + isConfirmed;
	formPostData += '&occupation=' + occupation;
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
	if(response) {
		var profileID = response;
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Profile has been updated successfully! ':'Profile has been created successfully ';
		msgToDisplay += '<a href="#" onclick="showProfileDetails(' + profileID +');">View Profile</a>';
		//listAllProfiles(1);
		if(!isUpdate) {
			getAddOrEditProfileForm(1, profileID);
		}
	} else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Profile failed to update.':'Profile failed to create.';		
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
		listAllProfiles(0);
	} else {
		resultToUI = getAlertDiv(2, 'Profile failed to delete.');
	}
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function authenticateUser()
{
	var user = document.getElementById('inputUser').value;
	var pass = document.getElementById('inputPwd').value;
	var formPostData = 'req=authenticate&username=' + user + '&password=' + pass;
	if(user == "" || pass == "")
	{
		var resultToUI = getAlertDiv(2, 'Username and Password cannot be empty.');
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}

	document.getElementById('divSignInBtn').style.display = 'none';
	document.getElementById('divLoadingSearchImg').style.display = '';

	$.ajax({
		type:'POST',
		url:'server/doauth.php',
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
		//resultToUI = getAlertDiv(1, 'Profile has been deleted successfully!');
		//window.location.href = 'http://localhost/Profilestack';//../index.php';
		window.location.href = 'dashboard.php';
	} else {
		//resultToUI = getAlertDiv(2, 'Invalid Username or Password.');
		resultToUI = getAlertDiv(2, loginMessage);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;

		document.getElementById('divSignInBtn').style.display = '';
		document.getElementById('divLoadingSearchImg').style.display = 'none';
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