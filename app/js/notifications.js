//global variables
doNotificationsFile = 'server/donotifications';

function getComposeMessageForm(msgType, isEdit, msgID)
{
	/** /
	document.getElementById('composeMessage').className = 'active';
	document.getElementById('listDrafts').className = '';
	document.getElementById('listSentItems').className = '';
	document.getElementById('createTemplates').className = '';
	document.getElementById('listTemplates').className = '';
	/**/
	document.getElementById('alertRow').style.display = 'none';

	tempMsgID = msgID;
	tempMsgType = msgType;
	tempIsEdit = isEdit;
	if(!isEdit) {
		msgID = 0;
	}

	var formPostData = 'req=1';
	formPostData += '&msgType=' + msgType;
	formPostData += '&isEdit=' + isEdit;
	formPostData += '&msgID=' + msgID;	
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:getComposeMessageFormResponse,
		error:HandleAjaxError
	});
}

function getComposeMessageFormResponse(response)
{
//	document.getElementById('pageHeader').innerHTML = ((tempMsgType == 1)?'Email':'SMS');
	document.getElementById('pageContent').innerHTML = response;

	$(document).ready(function() {
	  $('#inputEmailMessage').summernote({
		  height: 150,                 // set editor height

		  minHeight: null,             // set minimum height of editor
		  maxHeight: null,             // set maximum height of editor

		  focus: true,                 // set focus to editable area after initializing summernote
		});	
	});

	//load individual and groups
	var reqFrom = 'notifications';
	var reqForSMS = false;
	getParticipantsList(reqFrom, reqForSMS);

	if(tempIsEdit) {
		getMessageInformation(tempMsgID);
	}
}

function onSelectingParticipants(item, val, text)
{
	addToTag(val, text);
}

function addToTag(val, text)
{
	//Initialize Tags
	$('#selectedPariticipants').tagsinput({itemValue:'id', itemText:'text'});
	//Hack: As Tags doesn't work with Typeahead, removing tags css properties here alone - Thileepan
	$('#inputTo').parent('div').children('div').css({'border': '0px', 'box-shadow:':'none'});
	$('#inputTo').val('');
	$('#selectedPariticipants').parent('div').children('div').children('input').hide();
	$('#selectedPariticipants').tagsinput('add', { id: val , text: text  });	
}

function onChangeNotificationType()
{
	var reqFrom = 'notifications';
	var reqForSMS;
	var index = document.getElementById('inputNotificationType').selectedIndex;
	var notificationType = document.getElementById('inputNotificationType').options[index].value;

	/********************************************************************************************** /
	First reset & hide everything and then show fields as per the neeed
	/***********************************************************************************************/
	document.getElementById('divSubject').style.display = 'none';
	document.getElementById('divEmailMessage').style.display = 'none';
	document.getElementById('divSMSMessage').style.display = 'none';
	document.getElementById("divSMSDisabledMsg").style.display = "none";
	document.getElementById("divAllButtons").style.display = "none";
	document.getElementById("divToPeople").style.display = "none";
	/************************************************************************************************/
	if(notificationType == 1) {
		reqForSMS = false;
		document.getElementById('divSubject').style.display = '';
		document.getElementById('divEmailMessage').style.display = '';
//		document.getElementById('divSMSMessage').style.display = 'none';
		document.getElementById("divToPeople").style.display = "";
		document.getElementById("divAllButtons").style.display = "";
	} else {
		reqForSMS = true;
//		document.getElementById('divSubject').style.display = 'none';
//		document.getElementById('divEmailMessage').style.display = 'none';

		if(document.getElementById("isSMSConfigEnabled").value == 1)
		{
			document.getElementById("divToPeople").style.display = "";
			document.getElementById('divSMSMessage').style.display = '';
			document.getElementById("divAllButtons").style.display = "";
		}
		else
		{
			document.getElementById("divSMSDisabledMsg").style.display = "";
			return false;
		}
	}
	//$('#selectedPariticipants').tagsinput('removeAll');
	//$('#selectedPariticipants').tagsinput();
	getParticipantsList(reqFrom, reqForSMS);
}

function calculateSMSRemainingChars()
{
	var totalCharLength = document.getElementById('inputSMSMessage').value.length;
	var allowedCharLength = document.getElementById('smsAllowedCharLength').value;
	var remainingCharLength = allowedCharLength - totalCharLength;
	if(remainingCharLength < 0) {
		$('#spanAllowedCharText').addClass("text-error");
	} else {
		$('#spanAllowedCharText').removeClass("text-error");
	}
	document.getElementById('spanAllowedCharText').innerHTML = remainingCharLength;
}

function sendMessage(isDraft)
{
	var isEdit = document.getElementById('hiddenIsEdit').value;
	if(isEdit) {
		var notificationID = document.getElementById('notificationID').value;
	}
	var index = document.getElementById('inputNotificationType').selectedIndex;
	var msgType = document.getElementById('inputNotificationType').options[index].value;
	var participantsList = $('#selectedPariticipants').val();
	var subject = $('#inputSubject').val();
	if(msgType == 1) {
		var msg = $('#inputEmailMessage').code();
	} else {
		msg = $('#inputSMSMessage').val();
	}
	var errMsg = '';

	//no need validate to save content as draft
	if(!isDraft)
	{
		if(participantsList == '') {
			errMsg = 'Please select atleast one receipient to send message';
		} else if(subject == '' && msgType == 1) {
			errMsg = 'Subject can\'t be empty';
		}
	}
	if((msg == '' || msg == '<p><br></p>')) {
		errMsg = 'Message can\'t be empty';
	}
	if(isEdit && msgType == 2) {
		subject = '';
	}
	
	if(errMsg != '') {
		var resultToUI = getAlertDiv(2, errMsg);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}
	tempIsDraft = isDraft;
	var formPostData = 'req=2';
	formPostData += '&msgType=' + msgType;
	formPostData += '&subject=' + escString(subject);
	formPostData += '&msg=' + escString(msg);
	formPostData += '&participantsList=' + escString(participantsList);
	formPostData += '&isDraft=' + isDraft;
	formPostData += '&isEdit=' + isEdit;
	if(isEdit) {
		formPostData += '&msgID=' + notificationID;
	}
	
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:sendMessageResponse,
		error:HandleAjaxError
	});
}

function sendMessageResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno == 1) {
		var alertType = 1;
		//var msgType = 1; //Email
		var isEdit = 0;
		//getComposeMessageForm(msgType, isEdit);
		if(tempIsDraft == 1) {
			listAllDrafts();
		} else {
			listAllSentItems();
		}
	}
	else {
		var alertType = 2;
	}
	var msgToDisplay = dataObj.msg;

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function discardMessageConfirmation()
{
	var msgToDisplay = 'Do you really want to discard this message?';
	var actionTakenCallBack = "getComposeMessageForm(1, 0)";
	var actionCancelCallBack = "cancelDiscardMessageRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes", "No", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelDiscardMessageRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function listAllMessages(type, filterByDrafts, filterBySentItems)
{
//	document.getElementById('pageHeader').innerHTML = 'Drafts';
	document.getElementById('alertRow').style.display = 'none';

	if(filterByDrafts) {
		var table = '<table id="messageList" class="table table-condensed table-striped"><thead><tr><th>Type</th><th>Subject</th><th>Content</th><th>Actions</th></tr></thead></table>';
	} else if(filterBySentItems) {
		var table = '<table id="messageList" class="table table-condensed table-striped"><thead><tr><th>Type</th><th>Subject</th><th>Content</th><th>Status</th><th>Actions</th></tr></thead></table>';
	}
	document.getElementById('pageContent').innerHTML = table;

	oTable = $('#messageList').dataTable( {
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doNotificationsFile,
		"aaSorting": [],
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=3&filterByDraft="+filterByDrafts+"&filterBySentItems="+filterBySentItems+"&filterByNotificationType="+type,
                "success": fnCallback
            } );
		
        }
	});
}

function listAllDrafts()
{
	var type = -1;
	var filterByDrafts = 1;
	var filterBySentItems = 0;

	/** /
	document.getElementById('composeMessage').className = '';
	document.getElementById('listDrafts').className = 'active';
	document.getElementById('listSentItems').className = '';
	document.getElementById('createTemplates').className = '';
	document.getElementById('listTemplates').className = '';
	/**/
	document.getElementById('alertRow').style.display = 'none';
	listAllMessages(type, filterByDrafts, filterBySentItems);
}

function listAllSentItems()
{
	var type = -1;
	var filterByDrafts = 0;
	var filterBySentItems = 1;

	/** /
	document.getElementById('composeMessage').className = '';
	document.getElementById('listDrafts').className = '';
	document.getElementById('listSentItems').className = 'active';
	document.getElementById('createTemplates').className = '';
	document.getElementById('listTemplates').className = '';
	/**/
	document.getElementById('alertRow').style.display = 'none';
	listAllMessages(type, filterByDrafts, filterBySentItems);
}

function deleteMessageConfirmation(notificationID, isReqFromDraft)
{
	var msgToDisplay = 'Do you really want to delete this message?';
	var actionTakenCallBack = "deleteMessageRequest("+notificationID+", "+isReqFromDraft+")";
	var actionCancelCallBack = "cancelDiscardMessageRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes", "No", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function deleteMessageRequest(notificationID, isReqFromDraft)
{
	var formPostData = 'req=4';
	formPostData += '&msgID=' + notificationID;
	tempIsReqFromDraft = isReqFromDraft;
	
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:deleteMessageResponse,
		error:HandleAjaxError
	});
}

function deleteMessageResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj[0] == 1) {
		var alertType = 1;
		if(tempIsReqFromDraft) {
			listAllDrafts();
		} else {
			listAllSentItems();
		}
	}
	else {
		var alertType = 2;
	}
	var msgToDisplay = dataObj[1];

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function getMessageInformation(msgID)
{
	var formPostData = 'req=5';
	formPostData += '&msgID=' + msgID;
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:getMessageInformationResponse,
		error:HandleAjaxError
	});
}

function getMessageInformationResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj[0] == 1) {
		var notificationObj = dataObj[1];
		var notificationDetails = notificationObj[0];
		var notificationID = notificationDetails[0];
		var notificationType = notificationDetails[1];
		var notificationSubject = notificationDetails[2];
		var notificationContent = notificationDetails[3];

		document.getElementById('inputSubject').value = notificationSubject;
		if(notificationType == 1) {
			$('#inputEmailMessage').code(notificationContent);
		} else {
			document.getElementById('inputSMSMessage').value = notificationContent;
		}

		if(notificationObj[1] != null) {
			var notificationParticipants = notificationObj[1];
			for(var j=0; j<notificationParticipants.length; j++)
			{
				var participantType = notificationParticipants[j][0];
				var participantID = notificationParticipants[j][1];				
				var participantName = notificationParticipants[j][2];
				if(participantType == 1) {
					var participantMiddleName = notificationParticipants[j][3];
					var participantLastName = notificationParticipants[j][4];
					var participantEmail = notificationParticipants[j][5];
					var participantMobile = notificationParticipants[j][6];
					var val = participantType + "<:|:>" + participantID + "<:|:>" + participantEmail;
					if(notificationType == 1) {
						var text = participantName +" "+participantMiddleName+" "+participantLastName + " (" + participantEmail + ")";
					} else if(notificationType == 2) {
						var text = participantName +" "+participantMiddleName+" "+participantLastName + " (" + participantMobile + ")";
					}
				} else if(participantType == 2) {
					var val = participantType + "<:|:>" + participantID + "<:|:>" + participantName;
					var text = participantName;
				}				
				
				addToTag(val, text);
			}
		}
	} else {
		var alertType = 2;
		var msgToDisplay = dataObj[1];
		var resultToUI = getAlertDiv(alertType, msgToDisplay);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
	}
}

function getAddOrEditTemplateForm(templateType, isEdit, templateID)
{
	tempIsEdit = isEdit;
	tempTemplateID = templateID;
	/** /
	document.getElementById('composeMessage').className = '';
	document.getElementById('listDrafts').className = '';
	document.getElementById('listSentItems').className = '';
	document.getElementById('createTemplates').className = 'active';
	document.getElementById('listTemplates').className = '';
	/**/
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=6';
	formPostData += '&isEdit=' + isEdit;
	formPostData += '&templateType=' + templateType;
	if(isEdit) {
		formPostData += '&templateID=' + templateID;
	}
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:getAddOrEditTemplateFormResponse,
		error:HandleAjaxError
	});
}

function getAddOrEditTemplateFormResponse(response)
{
//	document.getElementById('pageHeader').innerHTML = "Create New Template";
	document.getElementById('pageContent').innerHTML = response;

	$(document).ready(function() {
	  $('#inputEmailMessage').summernote({
		  height: 150,                 // set editor height

		  minHeight: null,             // set minimum height of editor
		  maxHeight: null,             // set maximum height of editor

		  focus: true,                 // set focus to editable area after initializing summernote
		});	
	});

	if(tempIsEdit) {
		getTemplateInformation(tempTemplateID);
	}
}

function onChangeTemplateType()
{
	var index = document.getElementById('inputTemplateType').selectedIndex;
	var templateType = document.getElementById('inputTemplateType').options[index].value;
	if(templateType == 1) {
		document.getElementById('divSubject').style.display = '';
		document.getElementById('divEmailMessage').style.display = '';
		document.getElementById('divSMSMessage').style.display = 'none';
	} else {
		document.getElementById('divSubject').style.display = 'none';
		document.getElementById('divEmailMessage').style.display = 'none';
		document.getElementById('divSMSMessage').style.display = '';
	}	
}

function discardTemplateConfirmation()
{
	var msgToDisplay = 'Do you really want to discard this message?';
	var actionTakenCallBack = "getAddOrEditTemplateForm(1, 0)";
	var actionCancelCallBack = "cancelDiscardTemplateRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes", "No", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelDiscardTemplateRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function createOrUpdateTemplate()
{
	var isEdit = document.getElementById('hiddenIsEdit').value;
	var templateID = document.getElementById('templateID').value;
	var index = document.getElementById('inputTemplateType').selectedIndex;
	var templateType = document.getElementById('inputTemplateType').options[index].value;
	var templateName = document.getElementById('inputTemplateName').value;	
	var subject = $('#inputSubject').val();
	if(templateType == 1) {
		var msg = $('#inputEmailMessage').code();
	} else {
		var msg = $('#inputSMSMessage').val();
	}

	var formPostData = 'req=7';
	formPostData += '&isEdit=' + isEdit;	
	formPostData += '&templateType=' + templateType;
	formPostData += '&templateName=' + escString(templateName);
	formPostData += '&subject=' + escString(subject);
	formPostData += '&msg=' + escString(msg);
	if(isEdit) {
		formPostData += '&templateID=' + templateID;
	}
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:createOrUpdateTemplateResponse,
		error:HandleAjaxError
	});
}

function createOrUpdateTemplateResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno == 1) {
		var alertType = 1;
		var templateType = 1; //Email
		var isEdit = 0;
		getAddOrEditTemplateForm(templateType, isEdit);
	}
	else {
		var alertType = 2;		
	}
	var msgToDisplay = dataObj.msg;

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function getTemplateInformation(templateID)
{
	var formPostData = 'req=8';
	formPostData += '&templateID=' + templateID;
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:getTemplateInformationResponse,
		error:HandleAjaxError
	});
}

function getTemplateInformationResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj[0] == 1) {
		var templateDetails = dataObj[1];
		console.log(templateDetails);
		var templateID = templateDetails[0];
		var templateType = templateDetails[1];
		var templateName = templateDetails[2];
		var templateSubject = templateDetails[3];
		var templateContent = templateDetails[4];

		document.getElementById('inputTemplateName').value = templateName;
		document.getElementById('inputSubject').value = templateSubject;
		if(templateType == 1) {
			$('#inputEmailMessage').code(templateContent);
		} else {
			document.getElementById('inputSMSMessage').value = templateContent;
		}
	} else {
		var alertType = 2;
		var msgToDisplay = dataObj[1];
		var resultToUI = getAlertDiv(alertType, msgToDisplay);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
	}
}

function listAllTemplates(type)
{
	/** /
	document.getElementById('composeMessage').className = '';
	document.getElementById('listDrafts').className = '';
	document.getElementById('listSentItems').className = '';
	document.getElementById('createTemplates').className = '';
	document.getElementById('listTemplates').className = 'active';
	/**/

//	document.getElementById('pageHeader').innerHTML = 'Templates';
	document.getElementById('alertRow').style.display = 'none';

	var table = '<table id="templateList" class="table table-condensed table-striped"><thead><tr><th>Type</th><th>Name</th><th>Subject</th><th>Content</th><th>Actions</th></tr></thead></table>';
	document.getElementById('pageContent').innerHTML = table;

	oTable = $('#templateList').dataTable( {
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
		"aaSorting": [],
        "sAjaxSource": doNotificationsFile,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=9&filterByTemplateType="+type,
                "success": fnCallback
            } );
		
        }
	});
}

function deleteTemplateConfirmation(templateID)
{
	var msgToDisplay = 'Do you really want to delete this template?';
	var actionTakenCallBack = "deleteTemplateRequest("+templateID+")";
	var actionCancelCallBack = "cancelDiscardTemplateRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Yes", "No", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function deleteTemplateRequest(templateID)
{
	var formPostData = 'req=10';
	formPostData += '&templateID=' + templateID;
	
	$.ajax({
		type:'POST',
		url:doNotificationsFile,
		data:formPostData,
		success:deleteTemplateResponse,
		error:HandleAjaxError
	});
}

function deleteTemplateResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj[0] == 1) {
		var alertType = 1;
		var type = -1; //list all templates
		listAllTemplates(type);
	}
	else {
		var alertType = 2;
	}
	var msgToDisplay = dataObj[1];

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function notiHighlightSelectedSubMenu(menu)
{
	//First set empty class for all the menus
	document.getElementById('composeMessage').className = '';
	document.getElementById('listDrafts').className = '';
	document.getElementById('listSentItems').className = '';
	document.getElementById('createTemplates').className = '';
	document.getElementById('listTemplates').className = '';
	document.getElementById('greetingsConfig').className = '';
	document.getElementById('smsConfig').className = '';
	document.getElementById('reportSummary').className = '';

	//Now set the active class for the selected menu
	var classNameToSet = 'active';
	if(menu == 1) {
		document.getElementById('composeMessage').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Send an email/SMS";
	} else if(menu == 2) {
		document.getElementById('listDrafts').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "List saved drafts";
	} else if(menu == 3) {
		document.getElementById('listSentItems').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "List sent emails & SMS";
	} else if(menu == 4) {
		document.getElementById('createTemplates').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Create an email/SMS template";
	} else if(menu == 5) {
		document.getElementById('listTemplates').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "List all stored templates";
	} else if(menu == 6) {
		document.getElementById('greetingsConfig').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Configure Birthday & Wedding Anniversary Greetings";
	} else if(menu == 7) {
		document.getElementById('smsConfig').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "Configure SMS Gateway/Provider";
	} else if(menu == 8) {
		document.getElementById('reportSummary').className = classNameToSet;
		document.getElementById('pageHeader').innerHTML = "All Emails & SMS Sent Summary";
	}
}

function listAllEMailSMSCountSummary()
{
	document.getElementById('alertRow').style.display = 'none';

	var table = '<table id="repSummary" class="table table-condensed table-striped"><thead><tr><th>Email Or SMS</th><th>Triggered For</th><th>Raw Content</th><th>Sent On</th><th>Recipients Count</th></tr></thead></table>';
	document.getElementById('pageContent').innerHTML = table;

	oTable = $('#repSummary').dataTable( {
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
		"bAutoWidth":false,
		"iDisplayLength":100,
        "sAjaxSource": doNotificationsFile,
		"aaSorting": [],
		"aoColumns": [
			{ "sWidth": "10%" },
			{ "sWidth": "20%"  },
			{ "sWidth": "40%" },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
		],
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=11",
                "success": fnCallback
            } );
		
        }
	});
}
