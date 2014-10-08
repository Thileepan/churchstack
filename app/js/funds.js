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
	formPostData += '&fundName=' + escString(fundName);
	formPostData += '&fundDesc=' + escString(fundDesc);
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
	var dataObj = eval("(" + response + ")" );
	if(dataObj[0] == 1) {
		var alertType = 1;
		listAllFunds();
	}
	else {
		var alertType = 2;		
	}
	var msgToDisplay = dataObj[1];

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
	formPostData += '&batchName=' + escString(batchName);
	formPostData += '&batchDesc=' + escString(batchDesc);
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
	var formPostData = 'req=10&batchID=' + batchID + '&batchName=' + escString(batchName);
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

	var formPostData = 'req=11&batchID=' + batchID;
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
	$('#myStat').circliful();
}

function getAddOrEditContributionForm(isUpdate, batchID, batchName, contributionID)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('summaryDiv').className = 'tab-pane';
	document.getElementById('addContributionDiv').className = 'tab-pane active';
	document.getElementById('listContributionDiv').className = 'tab-pane';

	var formPostData = 'req=12&isEdit=' + isUpdate + '&batchID=' + batchID + '&batchName=' + escString(batchName) + '&contributionID=' + contributionID;

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
	$('#inputContributionDate').datepicker({
		autoclose: true
	});

	var rowID = 1;
	getProfileList();	
	getFundList(rowID);
}

function listAllContributions(batchID)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('summaryDiv').className = 'tab-pane';
	document.getElementById('addContributionDiv').className = 'tab-pane';
	document.getElementById('listContributionDiv').className = 'tab-pane active';

	var currencyCode = "USD";
	if(document.getElementById("txtCurrencyCode")) {
		currencyCode = document.getElementById("txtCurrencyCode").value;
	}

	var table = '<table id="listContributionTable" class="table table-condensed"><thead><tr><th></th><th>Contribution ID</th><th>Date</th><th>Name</th><th>Transaction Type</th><th>Total Amount ('+currencyCode+')</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('listContributionDiv').innerHTML = table;
	
	oTable = $('#listContributionTable').dataTable( {
		"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "2%", "bVisible":false},
			{ "sWidth": "15%" },
			{ "sWidth": "15%"  },
			{ "sWidth": "10%"  },
			{ "sWidth": "10%"  },
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
					console.log(aData);
					oTable.fnOpen( nTr, showContributionSplitDetails(aData[1]), 'details' );
				}
			});
		},
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=13&batchID=" + batchID,
                "success": fnCallback
            } );		
        }
	});
}

function showContributionSplitDetails(contributionID)
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
		url:doFundsFile,
		data:formPostData,
	});
}

function onChangeTransactionType(obj)
{
	if(obj.selectedIndex == 0) {
		document.getElementById('divPaymentMode').style.display = '';
		document.getElementById('divReferenceNumber').style.display = '';
	} else {
		document.getElementById('divPaymentMode').style.display = 'none';
		document.getElementById('divReferenceNumber').style.display = 'none';
	}	
}

function getProfileList()
{
	var formPostData = "req=15";
	formPostData += "&loadProfilesOnly=1";

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:getProfileListResponse,
		error:HandleAjaxError
	});
}

function getProfileListResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var profiles = dataObj;

	var totalProfiles = profiles.length;
	if(totalProfiles > 0)
	{
		var sourceList = new Array();
		for(i=0; i<totalProfiles; i++)
		{
			var id = profiles[i][0];
			var name = profiles[i][2];
			sourceList.push({"id":id, "name":name});
		}

		$('#inputProfileName').typeahead({
			source: sourceList,
			display: 'name',
			val: 'id',
			itemSelected: onSelectProfileName
		});
	}
}

function onSelectProfileName(item, val, text)
{
	//console.log(item + "::" + val + "::" + text);
	document.getElementById('inputHiddenProfileID').value = val;
}

function addOrUpdateContribution(val)
{
	isUpdate = val;
	var batchID	= document.getElementById('inputHiddenBatchID').value; 
	var profileID = document.getElementById('inputHiddenProfileID').value;
	var contributionDate = document.getElementById('inputContributionDate').value;
	contributionDate = convertDateToDBFormat(contributionDate);
	var transactionTypeIndex = document.getElementById('inputTransactionType').selectedIndex;
	var transactionType = document.getElementById('inputTransactionType').options[transactionTypeIndex].value;
	var paymentMode = 0;
	var referenceNumber = '';
	var totalAmount = 0;
	if(transactionType == 1) {
		var paymentModeIndex = document.getElementById('inputPaymentMode').selectedIndex;
		paymentMode = document.getElementById('inputPaymentMode').options[paymentModeIndex].value;
		referenceNumber = document.getElementById('inputReferenceNumber').value;
	}

	var alertMsg = '';
	if(profileID == '') {
		alertMsg = 'Please enter a valid profile name';		
	} else if(contributionDate == '') {
		alertMsg = 'Please enter a valid contribution date';
	}

	if(alertMsg != '') {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var fundIDList = '';
	var amountList = '';
	var notesList = '';
	var transactionRowIDArr = document.getElementById('transactionRowIDList').value.split(',');
	if(transactionRowIDArr.length > 0) {
		for(var i=0; i<transactionRowIDArr.length; i++)
		{
			if(fundIDList != '') {
				fundIDList += '<:|:>';
			}
			if(amountList != '') {
				amountList += '<:|:>';
			}
			if(notesList != '') {
				notesList += '<:|:>';
			}
			var index = document.getElementById('fundName-' + transactionRowIDArr[i]).selectedIndex;
			fundIDList += document.getElementById('fundName-' + transactionRowIDArr[i]).options[index].value;
			amountList += document.getElementById('amount-' + transactionRowIDArr[i]).value;
			notesList += document.getElementById('notes-' + transactionRowIDArr[i]).value;
		}
	}

	totalAmount = calReceivedAmount();

	var formPostData = 'req=16';
	formPostData += '&isUpdate=' + isUpdate;
	formPostData += '&batchID=' + batchID;
	formPostData += '&profileID=' + profileID;
	formPostData += '&contributionDate=' + contributionDate;
	formPostData += '&transactionType=' + transactionType;
	formPostData += '&paymentMode=' + paymentMode;
	formPostData += '&referenceNumber=' + referenceNumber;
	formPostData += '&totalAmount=' + totalAmount;
	formPostData += '&fundIDList=' + fundIDList;
	formPostData += '&amountList=' + amountList;
	formPostData += '&notesList=' + notesList;
	if(isUpdate) {
		formPostData += '&contributionID=' + contributionID;
	}

	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:addOrUpdateContributionResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateContributionResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno == 1) {
		var alertType = 1;
		$('#batchTab a[href="#listContributionTab"]').tab('show');
		listAllContributions(dataObj.batchID);
	}
	else {
		var alertType = 2;
	}
	var msgToDisplay = dataObj.rslt;

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function addTransactionRow()
{
	var maxRow = document.getElementById('maxTransactionRowID').value;
    var newRowID = parseInt(maxRow) + 1;

	var rowDiv = document.createElement('div');
    var rowDivID = 'divTransactionRow-'+newRowID;
    rowDiv.setAttribute('id', rowDivID);
    rowDiv.setAttribute('class', 'row-fluid');
	document.getElementById('addTransactionRowOuterDiv').appendChild(rowDiv);

	var fundDivID = 'divFundName-'+newRowID;
	var fundDiv = document.createElement('div');
	fundDiv.setAttribute('id', fundDivID);
	fundDiv.setAttribute('class', 'span4');
	rowDiv.appendChild(fundDiv);

	var amountDivID = 'divAmount-'+newRowID;
	var amountDiv = document.createElement('div');
	amountDiv.setAttribute('id', amountDivID);
	amountDiv.setAttribute('class', 'span4');
	rowDiv.appendChild(amountDiv);

	var notesDivID = 'divNotes-'+newRowID;
	var notesDiv = document.createElement('div');
	notesDiv.setAttribute('id', notesDivID);
	notesDiv.setAttribute('class', 'span4');
	rowDiv.appendChild(notesDiv);	

	//var fundDivContent = '<input type="text" id="fundName-'+newRowID+'" placeholder="Fund Name" value="" />';
	var fundDivContent = '<select id="fundName-'+newRowID+'">';
		fundDivContent += '<option>Select Fund</option>';
	fundDivContent += '</select>';
	var amountDivContent = '<input type="text" id="amount-'+newRowID+'" placeholder="Amount" value="" onblur="calReceivedAmount();" onchange="calReceivedAmount();" />';
	var notesDivContent = '<input type="text" id="notes-'+newRowID+'" placeholder="Notes" value="" />&nbsp;<i class="icon-remove curHand" onclick="deleteTransactionRow('+ newRowID +')"></i>';

	document.getElementById(fundDivID).innerHTML = fundDivContent;
	document.getElementById(amountDivID).innerHTML = amountDivContent;
	document.getElementById(notesDivID).innerHTML = notesDivContent;
	document.getElementById('maxTransactionRowID').value = newRowID;
	document.getElementById('transactionRowIDList').value += ","+newRowID;

	getFundList(newRowID);
}

function deleteTransactionRow(rowID)
{
	var div = document.getElementById('divTransactionRow-'+rowID);
    if (div) {
        div.parentNode.removeChild(div);
    }
	var transactionRowIDArr = document.getElementById('transactionRowIDList').value.split(',');
    transactionRowIDArr.splice(transactionRowIDArr.indexOf(rowID.toString()), 1);
    document.getElementById('transactionRowIDList').value = transactionRowIDArr.join();    
}

function getFundList(rowID)
{
	var isFundLoadedAlready = document.getElementById('inputHiddenIsFundLoadedAlready').value;
	if(isFundLoadedAlready)
	{
		var optionList = document.getElementById('inputHiddenOptionList').value;
		if(optionList !== '') {
			var id = '#fundName-' + rowID;
			$(id).append(optionList);
			return true;
		}
	}
	
	var formPostData = "req=17";
	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:getFundListResponse,
		error:HandleAjaxError
	});
}

function getFundListResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var funds = dataObj;
	
	var optionList = '';
	if(funds[0] == 1)
	{
		var totalFunds = funds[1].length;
		for(var i=0; i<totalFunds; i++)
		{
			var fundID = funds[1][i][0];
			var fundName = funds[1][i][1];
			optionList += '<option value="'+ fundID +'">'+ fundName +'</option>';
		}
		document.getElementById('inputHiddenIsFundLoadedAlready').value = 1;
		document.getElementById('inputHiddenOptionList').value = optionList;
		$('#fundName-1').append(optionList);
	}	
}

function calReceivedAmount()
{
	var amount = 0;
	var transactionRowIDArr = document.getElementById('transactionRowIDList').value.split(',');
	if(transactionRowIDArr.length > 0) {		
		for(var i=0; i<transactionRowIDArr.length; i++)
		{
			var id = 'amount-' + transactionRowIDArr[i];
			if(document.getElementById(id).value != '')
			{
				console.log(isNaN(document.getElementById(id).value));
				if(!isNaN(document.getElementById(id).value)) {
					amount += parseInt(document.getElementById(id).value);
				}
			}						
		}
		document.getElementById('spanCurrentAmount').innerHTML = amount;
	}
	return amount;
}

function deleteContributionConfirmation(contributionID)
{
	var msgToDisplay = 'Please confirm to delete the contribution?';
	var actionTakenCallBack = "deleteContributionRequest(" + contributionID + ")";
	var actionCancelCallBack = "cancelContributionDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelContributionDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteContributionRequest(contributionID)
{
	tempContributionID = contributionID
	var formPostData = 'req=18&contributionID=' + contributionID;
	$.ajax({
		type:'POST',
		url:doFundsFile,
		data:formPostData,
		success:deleteContributionResponse,
		error:HandleAjaxError
	});
}

function deleteContributionResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = 'Contribution has been deleted successfully.';

		listAllContributions(tempContributionID);
	}
	else {
		var alertType = 2;
		var msgToDisplay = 'Unable to delete the contribution.';
	}

	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}