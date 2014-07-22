//global variables
doFundsFile = 'server/dofunds.php';

/* Formating function for row details */
function showSubscriptionDetails ( subscriptionID )
{
	$.when(getSubscriptionDetails(subscriptionID)).done(function(response){
		sOut = response;
	});
	return sOut;
}

function getSubscriptionDetails(subscriptionID)
{
	var formPostData = 'req=7&subscriptionID=' + subscriptionID;
	return $.ajax({
		async: false,
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		//success:listSubscriptionDetailsResponse,
		//error:HandleAjaxError
	});
}

function listAllFunds()
{
	document.getElementById('listFunds').className = 'active';
	document.getElementById('listBatches').className = '';
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('pageHeader').innerHTML = 'Manage Funds';

	var table = '<button class="btn btn-small btn-primary pull-right" onclick="">Add New Fund</button><BR><BR><table id="listFundsTable" class="table table-striped"><thead><tr><th>S.No.</th><th>Fund Name</th><th>Visibility</th><th>Description</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('pageContent').innerHTML = table;
	
	oTable = $('#listFundsTable').dataTable( {
		/*"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "30%"  },
			{ "sWidth": "20%"  },
			{ "sWidth": "30%" },
			{ "sWidth": "15%"  },			
		],*/
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": "server/dofunds.php",
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

function listAllFundsResponse(response)
{
	
}

function addOrUpdateField(val)
{
	isUpdate = val;
	var fieldID;
	var fieldName = '';
	if(isUpdate) {
		var rowID = document.getElementById('hiddenLastEditedRow').value;
		fieldName = document.getElementById('inputEditFieldName-' + rowID).value;
		fieldID = document.getElementById('inputEditFieldID-' + rowID).value; 
	} else {
		fieldName = document.getElementById('inputAddFieldName').value;
	}
	
	if(fieldName == "") {
		var alertMsg = 'Field Name is missing.';
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var formPostData = 'req=2&isUpdate=' + isUpdate + '&fieldName=' + fieldName;
	if(isUpdate) {
		formPostData += '&fieldID=' + fieldID;
	}

	$.ajax({
		type:'POST',
		url:doSubscribeFile,
		data:formPostData,
		success:addOrUpdateFieldResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateFieldResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Field has been updated successfully':'Field has been added successfully';

		listSubscriptionFields();
	}
	else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Unable to update the field':'Unable to add the field';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function hideOrShowField(req, rowID)
{
	var fieldID = document.getElementById('inputEditFieldID-' + rowID).value; 
	var formPostData = 'req=3&fieldID=' + fieldID + '&isHide=' + req;
	$.ajax({
		type:'POST',
		url:doSubscribeFile,
		data:formPostData,
		success:hideOrShowFieldResponse,
		error:HandleAjaxError
	});
}

function hideOrShowFieldResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Updated successfully';

		listSubscriptionFields();
	}
	else {
		var alertType = 2;
		var msgToDisplay = 'Update failed';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function showEditFieldInfoRow(rowID)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('divAddFieldBtn').style.display = '';
	document.getElementById('divAddFieldForm').style.display = 'none';
	var lastEditedRow = document.getElementById('hiddenLastEditedRow').value;
	if(lastEditedRow != "")
	{
		document.getElementById('spnShowFieldNameInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnEditFieldNameInfo-' + lastEditedRow).style.display = 'none';
		document.getElementById('spnActionInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnSaveButton-' + lastEditedRow).style.display = 'none';
	}
	document.getElementById('spnShowFieldNameInfo-' + rowID).style.display = 'none';
	document.getElementById('spnEditFieldNameInfo-' + rowID).style.display = '';
	document.getElementById('inputEditFieldName-' + rowID).focus();
	document.getElementById('inputEditFieldName-' + rowID).select();
	document.getElementById('spnActionInfo-' + rowID).style.display = 'none';
	document.getElementById('spnSaveButton-' + rowID).style.display = '';
	document.getElementById('hiddenLastEditedRow').value = rowID;
}

function hideEditFieldInfoRow(rowID)
{
	document.getElementById('spnShowFieldNameInfo-' + rowID).style.display = '';
	document.getElementById('spnEditFieldNameInfo-' + rowID).style.display = 'none';
	document.getElementById('spnActionInfo-' + rowID).style.display = '';
	document.getElementById('spnSaveButton-' + rowID).style.display = 'none';
	document.getElementById('hiddenLastEditedRow').value = "";
}

function showAddNewFieldForm()
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('divAddFieldBtn').style.display = 'none';
	document.getElementById('divAddFieldForm').style.display = '';
	document.getElementById('inputAddFieldName').focus();
	var lastEditedRow = document.getElementById('hiddenLastEditedRow').value;
	if(lastEditedRow != "")
	{
		document.getElementById('spnShowFieldNameInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnEditFieldNameInfo-' + lastEditedRow).style.display = 'none';
		document.getElementById('spnActionInfo-' + lastEditedRow).style.display = '';
		document.getElementById('spnSaveButton-' + lastEditedRow).style.display = 'none';
	}
}

function hideAddNewFieldForm()
{
	document.getElementById('divAddFieldBtn').style.display='';
	document.getElementById('divAddFieldForm').style.display='none';
}

function getSubscriptionForm(val, subscrID, profileID)
{
	isUpdate = val;
	subscriptionID = subscrID;
	screenID = document.getElementById('screenID').value;
	if(!isUpdate)
	{
		document.getElementById('listSubscriptionFields').className = '';
		document.getElementById('addSubscription').className = ((isUpdate)?'':'active');
		document.getElementById('listSubscriptions').className = '';
		document.getElementById('alertRow').style.display = 'none';
	}
	
	var formPostData = "req=4";
	formPostData += "&isEdit=" + isUpdate;
	if(isUpdate) {
		formPostData += "&subscriptionID=" + subscriptionID;
		formPostData += "&prevProfileID=" + profileID;
	}

	$.ajax({
		type:'POST',
		url:doSubscribeFile,
		data:formPostData,
		success:getSubscriptionFormResponse,
		error:HandleAjaxError
	});
}

function getSubscriptionFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = (isUpdate)?"Edit Subscription":"Add Subscription";
	if(isUpdate) {
		document.getElementById('modalBody').innerHTML = response;
		if(screenID == 1)
		{
			document.getElementById('subscriptionSaveBtn').style.display = '';
			document.getElementById('harvestSaveBtn').style.display = 'none';
		}
		document.getElementById('hiddenSubscriptionID').value = subscriptionID;
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
			//console.log(parentIDArr[i]);
			//sourceList.push({"id":parentIDArr[i], "name:"parentNameArr[i]});
			//sourceList.push({"id":parentIDArr[i], "name":parentNameArr[i]+"-"+parentUniqueIDArr[i]});
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

	$('#inputSubcriptionMonth').datepicker({
		autoclose: true
	});
}

function onSelectingProfileID(item, val, text) {
	document.getElementById('selectedProfileID').value = val;
}

function listAllSubscriptions(profileID)
{
	//console.log(profileID);
	document.getElementById('alertRow').style.display = 'none';
	
	var table = '<table id="subscriptionList" class="table table-condensed"><thead><tr><th></th><th>Subscription ID</th><th>Member ID</th><th>Name</th><th>Date</th><th>Total Amount</th><th>Actions</th></tr></thead></table>';
	
	if(profileID > 0)	{
		document.getElementById('pageHeader').innerHTML = "List Subscriptions";
		document.getElementById('profileDiv').className = 'tab-pane';
		document.getElementById('subscriptionDiv').className = 'tab-pane active';
		document.getElementById('harvestDiv').className = 'tab-pane';
		document.getElementById('subscriptionDiv').innerHTML = table;		
	} else {
		document.getElementById('listSubscriptionFields').className = '';
		document.getElementById('addSubscription').className = '';
		document.getElementById('listSubscriptions').className = 'active';
		document.getElementById('pageHeader').innerHTML = "List Subscriptions";
		document.getElementById('pageContent').innerHTML = table;
	}

	oTable = $('#subscriptionList').dataTable( {
		"aoColumns": [
			{ "sWidth": "10%" },
			{ "sWidth": "3%", "bVisible":false  },
			{ "sWidth": "15%"  },
			{ "sWidth": "25%" },
			{ "sWidth": "20%"  },
			{ "sWidth": "22%"  },
			{ "sWidth": "5%"  }
		],
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doSubscribeFile,
		"fnDrawCallback": function () {
			$('body table tbody td').on( 'click', 'img', function (e) {

		//		alert("SKTG");
				//console.log($(this));
				var nTr = $(this).parents('tr')[0];
				if ( oTable.fnIsOpen(nTr) )
				{
					/* This row is already open - close it */
					this.src = "plugins/datatables/examples/examples_support/details_open.png";
					oTable.fnClose( nTr );
				}
				else
				{
					/* Open this row */
					this.src = "plugins/datatables/examples/examples_support/details_close.png";
					var aData = oTable.fnGetData( nTr );
					oTable.fnOpen( nTr, showSubscriptionDetails(aData[1]), 'details' );
				}
			});
		},
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=6&profileID="+profileID,
                "success": fnCallback
            } );
		
        }
	});
}

function addOrUpdateNewSubscription(val)
{
	isUpdate = val;
	profileID = document.getElementById('selectedProfileID').value;
	screenID = document.getElementById('screenID').value;
	if(profileID == 0)
	{
		var resultToUI = getAlertDiv(2, 'Please choose a valid family head');
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}
	var subscriptionDate = document.getElementById('inputSubcriptionMonth').value;
	if(validateDateFormat(subscriptionDate)) {
		subscriptionDate = convertDateToDBFormat(subscriptionDate);
	} else {
		var resultToUI = getAlertDiv(2, 'Please choose valid subscription date');
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}

	var hiddenFieldIDs = document.getElementById('hiddenFieldIDs').value;
	var fieldIDArr = hiddenFieldIDs.split(",");
	var totalFields = fieldIDArr.length;
	var fieldVal = '';

	if(totalFields > 0) {
		for(i=0; i<totalFields; i++)
		{
			if(fieldVal != '')
			{
				fieldVal += ",";
			}
			var val = parseInt(document.getElementById('inputFieldID-' + fieldIDArr[i]).value);
			if(isNaN(val)) {
				val = 0;
			}
			fieldVal += val;
		}
	}
	
	var formPostData = "req=5";
	formPostData += "&isUpdate=" + isUpdate;
	formPostData += "&profileID=" + profileID;
	formPostData += "&subscriptionDate=" + subscriptionDate;
	formPostData += "&fieldIDStr=" + hiddenFieldIDs;
	formPostData += "&fieldValStr=" + fieldVal;
	if(isUpdate) {
		formPostData += "&subscriptionID=" + document.getElementById('hiddenSubscriptionID').value;
	}

	$.ajax({
		type:'POST',
		url:doSubscribeFile,
		data:formPostData,
		success:addOrUpdateNewSubscriptionResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateNewSubscriptionResponse(response)
{
	if(response == 1) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Subscription has been updated successfully!':'Subscription has been created successfully';
		//reload the entire subscription list after updating the subscription details
		if(isUpdate) {
			document.getElementById('subModalCloseBtn').click();
			if(screenID == 2) {				
				listAllSubscriptions(0);
			} else {
				listAllSubscriptions(profileID);
			}
		}
		else
		{
			getSubscriptionForm(0);
		}
	} else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Subscription failed to update.':'Subscription failed to create.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function calSubscriptionTotal(obj)
{
	var total = 0;
	var fieldIDs = document.getElementById('hiddenFieldIDs').value;
	var fieldIDArr = fieldIDs.split(",");
	var totalFields = fieldIDArr.length;

	if(totalFields > 0)
	{
		for(i=0; i<totalFields; i++)
		{
			var amount = parseInt(document.getElementById('inputFieldID-' + fieldIDArr[i]).value);
			if(!isNaN(amount))
			{
				total += amount;
				//console.log(total);
			}
		}
	}
	document.getElementById('spanSubscriptionTotal').innerHTML = total;
}

function deleteSubscriptionConfirmation(subscriptionID)
{
	var msgToDisplay = 'Please confirm your delete request?';
	var actionTakenCallBack = "deleteSubscriptionRequest(" + subscriptionID + ")";
	var actionCanelCallBack = "cancelSubscriptionDelteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCanelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelSubscriptionDelteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteSubscriptionRequest(subscriptionID, profileID)
{
	profileID = profileID
	var formPostData = "req=8";
	formPostData += "&subscriptionID=" + subscriptionID;
	
	$.ajax({
		type:'POST',
		url:doSubscribeFile,
		data:formPostData,
		success:deleteSubscriptionResponse,
		error:HandleAjaxError
	});
}

function deleteSubscriptionResponse(response)
{
	if(response == 1)
	{
		var alertType = 1;
		var msgToDisplay = 'Subscription has been deleted successfully';
		var screenID = document.getElementById('screenID').value;
		if(screenID == 2) {				
			listAllSubscriptions(0);
		} else {
			listAllSubscriptions(profileID);
		}
	}
	else
	{
		var alertType = 2;
		var msgToDisplay = 'Subscription has been failed to delete.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}