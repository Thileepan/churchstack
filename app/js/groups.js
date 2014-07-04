//global variables
doGroupFile = 'server/dogroups.php';

function getAddOrEditGroupForm(isEdit, groupID)
{
	document.getElementById('pageHeader').innerHTML = ((!isEdit)?'Add New Group':'Update Group');
	document.getElementById('addGroup').className = 'active';
	document.getElementById('listGroup').className = '';

	var formPostData = "req=1&isEdit=" + isEdit;
	if(isEdit) {
		formPostData += "&groupID=" + groupID;
	}

	$.ajax({
		type:'POST',
		url:doGroupFile,
		data:formPostData,
		success:getAddOrEditGroupFormResponse,
		error:HandleAjaxError
	});
}

function getAddOrEditGroupFormResponse(response)
{
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputGroupName').focus();
}

function addOrUpdateGroup(val)
{
	isUpdate = val;

	if(isUpdate) {
		var groupID = document.getElementById('hidInputGroupID').value;
	}

	var groupName = document.getElementById('inputGroupName').value;
	var desc = document.getElementById('inputGroupDesc').value;
	
	var errMsg = '';
	if(groupName == '') {
		errMsg = 'Group Name can\'t be empty.';
	}
	if(errMsg != '')
	{
		var resultToUI = getAlertDiv(2, errMsg);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}

	var formPostData = "req=2";
	formPostData += "&name=" + groupName;
	formPostData += "&desc=" + desc;
	formPostData += "&isEdit=" + isUpdate;
	if(isUpdate) {
		formPostData += "&groupID=" + groupID;
	}
	
	$.ajax({
		type:'POST',
		url:doGroupFile,
		data:formPostData,
		success:addOrUpdateGroupResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateGroupResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Group has been updated successfully!':'Group has been created successfully';
		if(!isUpdate) {
			getAddOrEditGroupForm(0);
		}
	} else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Group failed to update.':'Group failed to create.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function listAllGroups()
{
	document.getElementById('pageHeader').innerHTML = 'List Groups';
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('addGroup').className = '';
	document.getElementById('listGroup').className = 'active';
	document.getElementById('divOptionBtn').style.display = 'none';	
	
	var table = '<table id="groupList" class="table table-condensed"><thead><tr><th>Group</th><th>Description</th><th>Actions</th></tr></thead></table>';
	document.getElementById('pageContent').innerHTML = table;

	oTable = $('#groupList').dataTable( {
		"aoColumns": [
			{ "sWidth": "25%" },
			{ "sWidth": "40%"  },
			{ "sWidth": "35%" }
		],
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doGroupFile,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=3",
                "success": fnCallback
            } );
		
        }
	});
}

function deleteGroupConfirmation(groupID, groupName)
{
	var msgToDisplay = 'Please confirm to delete the group \'' + groupName + '\'';
	var actionTakenCallBack = "deleteGroupRequest(" + groupID + ")";
	var actionCancelCallBack = "cancelGroupDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelGroupDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteGroupRequest(groupID)
{
	var formPostData = 'req=4&groupID=' + groupID;
	
	$.ajax({
		type:'POST',
		url:doGroupFile,
		data:formPostData,
		success:deleteGroupResponse,
		error:HandleAjaxError
	});
}

function deleteGroupResponse(response)
{
	if(response == 1) {
		var alertType = 1;
		var msgToDisplay = 'Group has been deleted successfully';		
		listAllGroups();
	} else {
		var alertType = 2;
		var msgToDisplay = 'Failed to delete the group';
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function showAddGroupMemberScreen(groupID)
{
	var reqFrom = 2;
	document.getElementById('hiddenGroupID').value = groupID;
	showProfileReportsScreen(reqFrom);
}

function selectAllGroupMembers(obj)
{
	var totalMembers = document.getElementById('hiddenTotalMembers').value;

	for(i=0; i<totalMembers; i++)
	{
		if(obj.checked) {
			if(document.getElementById('inputSelectMemberID-' + i)) {
				document.getElementById('inputSelectMemberID-' + i).checked = true;
			} else {
				console.log(i);
			}
		} else {
			document.getElementById('inputSelectMemberID-' + i).checked = false;
		}
	}
}

function addGroupMembers()
{
	var groupID = document.getElementById('hiddenGroupID').value;
	var profileID = '';
	var totalMembers = document.getElementById('hiddenTotalMembers').value;
	for(i=0; i<totalMembers; i++)
	{
		if(document.getElementById('inputSelectMemberID-' + i).checked)
		{
			if(profileID != '')
			{
				profileID += ',';
			}
			profileID += document.getElementById('inputSelectMemberID-' + i).value;
		}
	}
	if(profileID == '') {
		alert('Please select atleast one member to add in this group');
		return false;
	}
	
	var formPostData = 'req=5';
	formPostData += '&groupID=' + groupID;
	formPostData += '&profileID=' + profileID;
	
	$.ajax({
		type:'POST',
		url:doGroupFile,
		data:formPostData,
		success:addGroupMembersResponse,
		error:HandleAjaxError
	});
}

function addGroupMembersResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Members has been added to group successfully';
		listAllGroups();
	} else {
		var alertType = 2;
		var msgToDisplay = 'Failed to add members into group. Please try again!';
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function listAllGroupMembers(groupID)
{
	var table = '<table id="groupMemberList" class="table table-condensed"><thead><tr><th>S.No.</th><th>Member ID</th><th>Name</th></tr></thead></table>';
	document.getElementById('pageContent').innerHTML = table;

	oTable = $('#groupMemberList').dataTable( {
		"aoColumns": [
			{ "sWidth": "20%" },
			{ "sWidth": "40%" },
			{ "sWidth": "40%" },			
		],
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doGroupFile,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=6&groupID=" + groupID,
                "success": fnCallback
            } );
		
        }
	});
}