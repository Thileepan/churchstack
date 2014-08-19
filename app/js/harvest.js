//global variables
doHarvestFile = 'server/doharvest';

function getHarvestForm(val, harID, profileID)
{
	isUpdate = val;
	harvestID = harID;
	screenID = document.getElementById('screenID').value;
	if(!isUpdate)
	{
		document.getElementById('addHarvest').className = ((isUpdate)?'':'active');
		document.getElementById('listHarvest').className = '';
		document.getElementById('alertRow').style.display = 'none';
	}
	
	var formPostData = "req=1";
	formPostData += "&isEdit=" + isUpdate;
	if(isUpdate) {
		formPostData += "&harvestID=" + harvestID;
		formPostData += "&prevProfileID=" + profileID;
	}

	$.ajax({
		type:'POST',
		url:doHarvestFile,
		data:formPostData,
		success:getHarvestFormResponse,
		error:HandleAjaxError
	});
}

function getHarvestFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = (isUpdate)?"Edit Harvest":"Add Harvest";
	if(isUpdate) {
		document.getElementById('modalBody').innerHTML = response;
		if(screenID == 1)
		{
			document.getElementById('subscriptionSaveBtn').style.display = 'none';
			document.getElementById('harvestSaveBtn').style.display = '';
		}
		document.getElementById('hiddenHarvestID').value = harvestID;
		document.getElementById('harvestFormDiv').className = "span10";
	} else {
		document.getElementById('pageContent').innerHTML = response;
	}	

	var parentIDArr = (document.getElementById('hiddenParentID').value).split(",");
	var parentNameArr = (document.getElementById('hiddenParentName').value).split(",");
	var parentUniqueIDArr = (document.getElementById('hiddenParentUniqueID').value).split(",");

	if(parentIDArr.length > 0)
	{
		var sourceList = new Array();
		for(i=0; i<parentIDArr.length; i++)
		{
			sourceList.push({"id":parentIDArr[i], "name":parentUniqueIDArr[i]});
		}
	}
	$('#inputProfileID').typeahead({
		source: sourceList,
		display: 'name',
		val: 'id',
		itemSelected: onSelectingProfileID
	});
	document.getElementById('inputProfileID').focus();
}

function onSelectingProfileID(item, val, text) {
	document.getElementById('selectedProfileID').value = val;
}

function listAllHarvests(profileID)
{
	//console.log(profileID);
	document.getElementById('alertRow').style.display = 'none';
	
	var table = '<table id="harvestList" class="table table-condensed"><thead><tr><th>Member ID</th><th>Name</th><th>Date</th><th>Description</th><th>Amount</th><th>Actions</th></tr></thead></table>';
	
	if(profileID > 0)	{
		document.getElementById('pageHeader').innerHTML = "List Harvests";
		document.getElementById('profileDiv').className = 'tab-pane';
		document.getElementById('subscriptionDiv').className = 'tab-pane';
		document.getElementById('harvestDiv').className = 'tab-pane active';
		document.getElementById('harvestDiv').innerHTML = table;		
	} else {
		document.getElementById('addHarvest').className = '';
		document.getElementById('listHarvest').className = 'active';
		document.getElementById('pageHeader').innerHTML = "List Harvests";
		document.getElementById('pageContent').innerHTML = table;
	}

	oTable = $('#harvestList').dataTable( {
		"aoColumns": [
			{ "sWidth": "10%" },
			{ "sWidth": "15%"  },
			{ "sWidth": "25%" },
			{ "sWidth": "20%"  },
			{ "sWidth": "22%"  },
			{ "sWidth": "5%"  }
		],
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doHarvestFile,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=3&profileID="+profileID,
                "success": fnCallback
            } );
		
        }
	});
}

function addOrUpdateHarvest(val)
{
	isUpdate = val;
	profileID = document.getElementById('selectedProfileID').value;
	screenID = document.getElementById('screenID').value;
	if(profileID == 0 || profileID == '')
	{
		var resultToUI = getAlertDiv(2, 'Please choose a valid family head');
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}
	var itemDesc = document.getElementById('inputItemDesc').value;
	var itemAmount = document.getElementById('inputItemAmount').value;
	
	var formPostData = "req=2";
	formPostData += "&isUpdate=" + isUpdate;
	formPostData += "&profileID=" + profileID;
	formPostData += "&itemDesc=" + itemDesc;
	formPostData += "&itemAmount=" + itemAmount;
	if(isUpdate) {
		formPostData += "&harvestID=" + document.getElementById('hiddenHarvestID').value;
	}

	$.ajax({
		type:'POST',
		url:doHarvestFile,
		data:formPostData,
		success:addOrUpdateHarvestResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateHarvestResponse(response)
{
	if(response == 1) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Harvest has been updated successfully!':'Harvest has been created successfully';
		//reload the entire harvest list after updating the harvest details
		if(isUpdate) {
			document.getElementById('subModalCloseBtn').click();
			if(screenID == 2) {				
				listAllHarvests(0);
			} else {
				listAllHarvests(profileID);
			}
		}
	} else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Harvest failed to update.':'Harvest failed to create.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function deleteHarvestConfirmation(harvestID)
{
	var msgToDisplay = 'Please confirm your delete request?';
	var actionTakenCallBack = "deleteHarvestRequest(" + harvestID + ")";
	var actionCancelCallBack = "cancelHarvestDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelHarvestDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteHarvestRequest(harvestID, profileID)
{
	profileID = profileID
	var formPostData = "req=4";
	formPostData += "&harvestID=" + harvestID;
	
	$.ajax({
		type:'POST',
		url:doHarvestFile,
		data:formPostData,
		success:deleteHarvestResponse,
		error:HandleAjaxError
	});
}

function deleteHarvestResponse(response)
{
	if(response == 1)
	{
		var alertType = 1;
		var msgToDisplay = 'Harvest has been deleted successfully';
		var screenID = document.getElementById('screenID').value;
		if(screenID == 2) {				
			listAllHarvests(0);
		} else {
			listAllHarvests(profileID);
		}
	}
	else
	{
		var alertType = 2;
		var msgToDisplay = 'Harvest has been failed to delete.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}