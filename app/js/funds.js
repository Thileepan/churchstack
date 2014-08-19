//global variables
doFundsFile = 'server/dofunds';

function listAllFunds()
{
	document.getElementById('listFunds').className = 'active';
	document.getElementById('listBatches').className = '';
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('pageHeader').innerHTML = 'Manage Funds';

	var table = '<button class="btn btn-small btn-primary pull-right" onclick="getFundForm(0)">Add New Fund</button><BR><BR><table id="listFundsTable" class="table table-striped"><thead><tr><th>Fund Name</th><th>Description</th><th>Visibility</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
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
        "sAjaxSource": "server/dofunds",
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

function getFundForm(val, fundID)
{
	isUpdate = val;
	
	var formPostData = "req=2";
	formPostData += "&isEdit=" + isUpdate;
	if(isUpdate) {
		formPostData += "&fundID=" + fundID;
	}

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:getFundFormResponse,
		error:HandleAjaxError
	});
}

function getFundFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = (isUpdate)?"Edit Fund":"Add New Fund";
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputFundName').focus();
}

function addOrUpdateFund(val)
{
	isUpdate = val;
	var fundID;
	if(isUpdate) {
		fundID = document.getElementById('inputHiddenFundID').value; 
	}
	var fundName = document.getElementById('inputFundName').value;
	var fundDesc = document.getElementById('inputFundDesc').value;
	var visibility = document.getElementById('inputFundVisibility').selectedIndex;

	if(fundName == '') {
		var alertMsg = 'Fund Name is missing.';
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var formPostData = 'req=3';
	formPostData += '&isUpdate=' + isUpdate;
	formPostData += '&fundName=' + fundName;
	formPostData += '&fundDesc=' + fundDesc;
	formPostData += '&visibility=' + visibility;
	if(isUpdate) {
		formPostData += '&fundID=' + fundID;
	}

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:addOrUpdateFundResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateFundResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Fund has been updated successfully':'Fund has been added successfully';
		listAllFunds();
	}
	else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Unable to update the fund':'Unable to add the fund';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function changeFundVisibility(fundID, visibilityStatus)
{
	var formPostData = 'req=4&fundID=' + fundID + '&visibilityStatus=' + visibilityStatus;
	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:changeFundVisibilityResponse,
		error:HandleAjaxError
	});
}

function changeFundVisibilityResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Fund visibility has been updated successfully.';

		listAllFunds();
	}
	else {
		var alertType = 2;
		var msgToDisplay = 'Fund visibility updated was failed.';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function deleteFundConfirmation(fundID, fundName)
{
	var msgToDisplay = 'Please confirm to delete the fund \'' + fundName + '\'';
	var actionTakenCallBack = "deleteFundRequest(" + fundID + ")";
	var actionCancelCallBack = "cancelFundDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelFundDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteFundRequest(fundID)
{
	var formPostData = 'req=5&fundID=' + fundID;
	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:deleteFundResponse,
		error:HandleAjaxError
	});
}

function deleteFundResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Fund has been deleted successfully.';

		listAllFunds();
	}
	else {
		var alertType = 2;
		var msgToDisplay = 'Unable to delete the fund.';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function listAllBatches()
{
	document.getElementById('listFunds').className = '';
	document.getElementById('listBatches').className = 'active';
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('pageHeader').innerHTML = 'Manage Batches';

	var table = '<button class="btn btn-small btn-primary pull-right" onclick="getBatchForm(0)">Add New Batch</button><BR><BR><table id="listBatchesTable" class="table table-striped"><thead><tr><th>Batch</th><th>Description</th><th>Created Time</th><th>Expected Amount</th><th>Received Amount</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('pageContent').innerHTML = table;
	
	oTable = $('#listBatchesTable').dataTable( {
		/*"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "30%"  },
			{ "sWidth": "20%"  },
			{ "sWidth": "30%" },
			{ "sWidth": "15%"  },			
		],*/
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": "server/dofunds",
		"iDisplayLength":100,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=6",
                "success": fnCallback
            } );
        }
	});
}

function getBatchForm(val, batchID)
{
	isUpdate = val;
	
	var formPostData = "req=7";
	formPostData += "&isEdit=" + isUpdate;
	if(isUpdate) {
		formPostData += "&batchID=" + batchID;
	}

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:getBatchFormResponse,
		error:HandleAjaxError
	});
}

function getBatchFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = (isUpdate)?"Edit Batch":"Add New Batch";
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputBatchName').focus();
}

function addOrUpdateBatch(val)
{
	isUpdate = val;
	var batchID;
	if(isUpdate) {
		batchID = document.getElementById('inputHiddenBatchID').value; 
	}
	var batchName = document.getElementById('inputBatchName').value;
	var batchDesc = document.getElementById('inputBatchDesc').value;
	var expectedAmount = document.getElementById('inputExpectedAmount').value;

	if(batchName == '') {
		var alertMsg = 'Batch Name is missing.';
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var formPostData = 'req=8';
	formPostData += '&isUpdate=' + isUpdate;
	formPostData += '&batchName=' + batchName;
	formPostData += '&batchDesc=' + batchDesc;
	formPostData += '&expectedAmount=' + expectedAmount;
	if(isUpdate) {
		formPostData += '&batchID=' + batchID;
	}

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:addOrUpdateBatchResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateBatchResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Batch has been updated successfully':'Batch has been added successfully';
		listAllBatches();
	}
	else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Unable to update the batch':'Unable to add the batch';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function deleteBatchConfirmation(batchID, batchName)
{
	var msgToDisplay = 'This will delete the batch including all the contributions added to them. Please confirm to delete the batch \'' + batchName + '\'';
	var actionTakenCallBack = "deleteBatchRequest(" + batchID + ")";
	var actionCancelCallBack = "cancelBatchDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelBatchDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteBatchRequest(batchID)
{
	var formPostData = 'req=9&batchID=' + batchID;
	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:deleteBatchResponse,
		error:HandleAjaxError
	});
}

function deleteBatchResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Batch has been deleted successfully.';

		listAllBatches();
	}
	else {
		var alertType = 2;
		var msgToDisplay = 'Unable to delete the batch.';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function showBatchDetails(batchID, batchName)
{
	_batchID = batchID;
	var formPostData = 'req=10&batchID=' + batchID + '&batchName=' + batchName;
	document.getElementById('alertRow').style.display = 'none';

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:showBatchDetailsResponse,
		error:HandleAjaxError
	});
}

function showBatchDetailsResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'Batch Summary';
	document.getElementById('pageContent').style.display = '';
	document.getElementById('pageContent').innerHTML = response;
	getBatchSummary(_batchID);	
}

function getBatchSummary(batchID)
{
	document.getElementById('alertRow').style.display = 'none';	
	document.getElementById('summaryDiv').className = 'tab-pane active';
	document.getElementById('addContributionDiv').className = 'tab-pane';
	document.getElementById('listContributionDiv').className = 'tab-pane';

	var formPostData = 'req=11';
	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:getBatchSummaryResponse,
		error:HandleAjaxError
	});
}

function getBatchSummaryResponse(response)
{
	document.getElementById('summaryDiv').innerHTML = response;
}

function getAddOrEditContributionForm(isUpdate, batchID, batchName, contributionID)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('summaryDiv').className = 'tab-pane';
	document.getElementById('addContributionDiv').className = 'tab-pane active';
	document.getElementById('listContributionDiv').className = 'tab-pane';

	var formPostData = 'req=12&isEdit=' + isUpdate + '&batchID=' + batchID + '&batchName=' + batchName + '&contributionID=' + contributionID;

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:getAddOrEditContributionFormResponse,
		error:HandleAjaxError
	});
}

function getAddOrEditContributionFormResponse(response)
{
	document.getElementById('addContributionDiv').innerHTML = response;
}

function listAllContributions(batchID)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('summaryDiv').className = 'tab-pane';
	document.getElementById('addContributionDiv').className = 'tab-pane';
	document.getElementById('listContributionDiv').className = 'tab-pane active';

	var table = '<table id="listContributionTable" class="table table-condensed"><thead><tr><th></th><th>Date</th><th>Batch</th><th>Name</th><th>Transaction Type</th><th>Notes</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('listContributionDiv').innerHTML = table;
	
	oTable = $('#listContributionTable').dataTable( {
		"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%" },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
		],
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doFundsFile,
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
					oTable.fnOpen( nTr, showContributionSplitDetails(aData[1]), 'details' );
				}
			});
		},
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=13",
                "success": fnCallback
            } );		
        }
	});
}

function showContributionSplitDetails()
{
	$.when(getContributionSplitDetails(contributionID)).done(function(response){
		sOut = response;
	});
	return sOut;
}

function getContributionSplitDetails(contributionID)
{
	var formPostData = 'req=14&contributionID=' + contributionID;
	return $.ajax({
		async: false,
		type:'POST',
		url:doFunds,
		data:formPostData,
	});
}