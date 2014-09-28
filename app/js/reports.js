//global variables
doReportsFile = 'server/doreports';

function showProfileReportsScreen(reqFrom)
{
	req = reqFrom;
	var formPostData = 'req=1';
	formPostData += '&reqFrom=' + reqFrom;
	$.ajax({
		type:'POST',
		url:doReportsFile,
		data:formPostData,
		success:showProfileReportsScreenResponse,
		error:HandleAjaxError
	});
}

function showProfileReportsScreenResponse(response)
{
	document.getElementById('pageHeader').innerHTML = ((req == 1)?'Profile Reports':'Add Group Member');
	document.getElementById('pageContent').innerHTML = response;
}

function showSubscriptionReportsScreen()
{
	var formPostData = 'req=4';
	$.ajax({
		type:'POST',
		url:doReportsFile,
		data:formPostData,
		success:showSubscriptionReportsScreenResponse,
		error:HandleAjaxError
	});
}

function showSubscriptionReportsScreenResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'Subscription Reports';
	document.getElementById('pageContent').innerHTML = response;

	checkOrUncheckAllSubscriptionFields(1);
	$('#inputSubReportFromDate').datepicker({
		autoclose: true
	});
	$('#inputSubReportToDate').datepicker({
		autoclose: true
	});
}

function viewReportRequest(reportID)
{
	var formPostData = 'req=2';
	formPostData += "&reportID=" + reportID;
	reqFrom = 1;

	$.ajax({
		type:'POST',
		url:doReportsFile,
		data:formPostData,
		success:viewReportResponse,
		error:HandleAjaxError
	});
}

function viewReportResponse(response)
{
	showReportTableDiv(1, reqFrom);

	var table = '<table id="viewreport" class="table table-striped">';
	table += '</table>';
	document.getElementById('divReportTable').style.display = '';
	document.getElementById('divReportTable').innerHTML = table;
	/**/

	var dataObj = eval("(" + response + ")" );

	var columns = [];
	jQuery.each(dataObj.aoColumns, function(i, value){
		var obj = { sTitle: value };
		 columns.push(obj);
	});
	//console.log(dataObj.aaData);

	$('#viewreport').dataTable({

		"sDom": 'T<"clear">lfrtip',
		"oTableTools": {
            "aButtons": [
                "copy",
                "print",
				"csv",
				"xls",
				"pdf",
             ],
			"sSwfPath": "plugins/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
		"bProcessing": true,
		"aaData": dataObj.aaData,
		"aoColumns": columns,
		"aaSorting": [],
	//	"bScrollCollapse": true,
		"bFilter": true,
	//	"sPaginationType": "full_numbers",
		"iDisplayLength":100,
	//	"bJQueryUI": true,
	//	"aoColumnDefs": dataObj.aoColumnDefs
	});
	/**/
}

function showSearchFormDiv(reportType, reqFrom)
{
	document.getElementById('alertRow').style.display = 'none';
	if(reportType == 1) {
		document.getElementById('divProfileSearchForm').style.display = '';
		document.getElementById('divListReports').style.display = 'none';
	} else if(reportType == 2) {
		document.getElementById('divSubscriptionSearchForm').style.display = '';
	}
	
	document.getElementById('divReportTable').style.display = 'none';
	document.getElementById('divOptionBtn').style.display = 'none';
	if(reqFrom == 1) {
		document.getElementById('divReportsBy').style.display = '';
	}

	//var btnHTML = '<button class="btn btn-small btn-primary" type="button" onclick="showListReportsDiv();">List Report Templates</button>';
	//document.getElementById('divOptionBtn').innerHTML = btnHTML;	
}

function showListReportsDiv()
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('divProfileSearchForm').style.display = 'none';
	document.getElementById('divListReports').style.display = '';
	document.getElementById('divReportTable').style.display = 'none';

	var btnHTML = '<button class="btn btn-small btn-primary" type="button" onclick="showSearchFormDiv(-1, 1);">Show Search Form</button>';
	document.getElementById('divOptionBtn').innerHTML = btnHTML;
}

function showReportTableDiv(reportType, reqFrom)
{
	document.getElementById('alertRow').style.display = 'none';
	if(reportType == 1) {
		document.getElementById('divProfileSearchForm').style.display = 'none';
		document.getElementById('divListReports').style.display = 'none';		
	} else if(reportType == 2) {
		document.getElementById('divSubscriptionSearchForm').style.display = 'none';
	}

	document.getElementById('divReportTable').style.display = '';
	document.getElementById('divSearchBtn').style.display = '';
	document.getElementById('divLoadingSearchImg').style.display = 'none';
	document.getElementById('divOptionBtn').style.display = '';
	if(reqFrom == 1) {
		document.getElementById('divReportsBy').style.display = 'none';
	}

	var btnHTML;
	if(reqFrom == 2) {
		btnHTML = '<button class="btn btn-small btn-primary" type="button" id="divAddMemberBtn" onclick="addGroupMembers();">Add Members</button>&nbsp;';
	}
	btnHTML += '<button class="btn btn-small btn-primary" type="button" onclick="showSearchFormDiv('+reportType+', 2);">Back</button><BR><BR>';
	document.getElementById('divOptionBtn').innerHTML = btnHTML;
}

function addReportRuleRow()
{
	var maxRow = document.getElementById('maxReportRuleRowID').value;
    var newRowID = parseInt(maxRow) + 1;

	var rowDiv = document.createElement('div');
    var rowDivID = 'divAddRule-'+newRowID;
    rowDiv.setAttribute('id', rowDivID);
    rowDiv.setAttribute('class', 'row-fluid');
	document.getElementById('addRuleOuterDiv').appendChild(rowDiv);

	/*
	var imageDivID = 'divCloseImage-' + newRowID;
	var imageDiv = document.createElement('div');
	imageDiv.setAttribute('id', imageDivID);
	imageDiv.setAttribute('class', 'span1');
	imageDiv.innerHTML = '<i class="icon-remove curHand"></i>';
	rowDiv.appendChild(imageDiv);
	*/

	var reportTypeDivID = 'divReportType-'+newRowID;
	var reportTypeDiv = document.createElement('div');
	reportTypeDiv.setAttribute('id', reportTypeDivID);
	reportTypeDiv.setAttribute('class', 'span4');
	rowDiv.appendChild(reportTypeDiv);

	var reportSubTypeDivID = 'divReportSubType-'+newRowID;
	var reportSubTypeDiv = document.createElement('div');
	reportSubTypeDiv.setAttribute('id', reportSubTypeDivID);
	reportSubTypeDiv.setAttribute('class', 'span3');
	reportSubTypeDiv.setAttribute('style', 'display:none');
	rowDiv.appendChild(reportSubTypeDiv);

	var reportValueDivID = 'divReportValue-'+newRowID;
	var reportValueDiv = document.createElement('div');
	reportValueDiv.setAttribute('id', reportValueDivID);
	reportValueDiv.setAttribute('class', 'span3');
	//reportValueDiv.setAttribute('style', 'display:none');
	rowDiv.appendChild(reportValueDiv);	

	//removed: <option value="PROFILES">PROFILES</option>
	//removed: <option value="BIRTH_MARRIAGE_DATE">BIRTH OR MARRIAGE DATE</option>
	var ruleType = '<i class="icon-remove curHand" onclick="deleteReportRuleRow('+ newRowID +')"></i>&nbsp;<select onChange="changeRuleSubTypeAndValue('+ newRowID +');" id="selReportType-'+newRowID+'"><option value="GENDER">GENDER</option><option value="AGE">AGE</option><option value="BIRTH_DATE">BIRTH DATE</option><option value="MARRIAGE_DATE">MARRIAGE DATE</option><option value="MARITAL_STATUS">MARITAL STATUS</option><option value="BAPTISM">BAPTISM</option><option value="CONFIRMATION">CONFIRMATION</option></select>';
	//var ruleValue = '<select id="selRuleValueItem-' + newRowID + '"><option value="ALL">All</option><option value="FAMILY_HEAD">Family Head</option><option value="INDIVIDUAL">Individual</option></select>';
	var ruleValue = '<select id="selRuleValueItem-' + newRowID + '"><option value="MALE">Male</option><option value="FEMALE">Female</option></select>';
	document.getElementById(reportTypeDivID).innerHTML = ruleType;
	document.getElementById(reportSubTypeDivID).innerHTML = '';
	document.getElementById(reportValueDivID).innerHTML = ruleValue;
	document.getElementById('maxReportRuleRowID').value = newRowID;
	document.getElementById('reportRuleRowIDList').value += ","+newRowID;
}

function deleteReportRuleRow(rowID)
{
	var div = document.getElementById('divAddRule-'+rowID);
    if (div) {
        div.parentNode.removeChild(div);
    }
	//document.getElementById('maxReportRuleRowID').value = document.getElementById('maxReportRuleRowID').value - 1;
    var reportRuleRowIDArr = document.getElementById('reportRuleRowIDList').value.split(',');
    reportRuleRowIDArr.splice(reportRuleRowIDArr.indexOf(rowID.toString()), 1);
    document.getElementById('reportRuleRowIDList').value = reportRuleRowIDArr.join();    
}

function changeRuleSubTypeAndValue(rowID)
{
	var ruleValue, ruleSubType;
//	var index = document.getElementById('selReportType-' + rowID).selectedIndex;
	var selRuleTypeIndex = document.getElementById('selReportType-' + rowID).selectedIndex;
	var ruleType = document.getElementById('selReportType-' + rowID).options[selRuleTypeIndex].value;
	//alert(ruleType);

	if(ruleType == 'PROFILES') {
		ruleValue = '<select id="selRuleValueItem-'+ rowID +'">';
			ruleValue += '<option value="ALL">All</option>';
			ruleValue += '<option value="FAMILY_HEAD">Family Head</option>';
			ruleValue += '<option value="INDIVIDUAL">Individual</option>';
		ruleValue += '</select>';
	} else if(ruleType == 'GENDER') {
		ruleValue = '<select id="selRuleValueItem-'+ rowID +'">';
			ruleValue += '<option value="MALE">Male</option>';
			ruleValue += '<option value="FEMALE">Female</option>';
		ruleValue += '</select>';
	} else if(ruleType == 'AGE') {
		ruleSubType = '<select id="selRuleSubType-'+ rowID +'">';
			ruleSubType += '<option value="IS_LESS_THAN">Is Less Than</option>';
			ruleSubType += '<option value="IS">Is</option>';
			ruleSubType += '<option value="IS_GREATER_THAN">Is Greater Than</option>';
		ruleSubType += '</select>'
		ruleValue = '<input type="text" id="inputAge-'+ rowID +'" value="" placeholder="Age" />';
	} else if(ruleType == 'BIRTH_DATE') {
			ruleValue = '<input type="text" id="inputFromDate-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;';
			ruleValue += '<input type="text" id="inputToDate-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />';
	} else if(ruleType == 'MARRIAGE_DATE') {
			ruleValue = '<input type="text" id="inputMFromDate-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;';
			ruleValue += '<input type="text" id="inputMToDate-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />';
	} else if(ruleType == 'BIRTH_MARRIAGE_DATE') {
			ruleValue = '<input type="text" id="inputBMFromDate-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;';
			ruleValue += '<input type="text" id="inputBMToDate-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />';
	} else if(ruleType == 'MARITAL_STATUS') {
		ruleValue = '<select id="selRuleValueItem-'+ rowID +'">';
			ruleValue += '<option value="SINGLE">Single</option>';
			ruleValue += '<option value="MARRIED">Married</option>';
			ruleValue += '<option value="WIDOW">Widow</option>';
		ruleValue += '</select>';
	} else if(ruleType == 'BAPTISM') {
		ruleValue = '<select id="selRuleValueItem-'+ rowID +'">';
			ruleValue += '<option value="YES">Yes</option>';
			ruleValue += '<option value="NO">No</option>';
		ruleValue += '</select>';
	} else if(ruleType == 'CONFIRMATION') {
		ruleValue = '<select id="selRuleValueItem-'+ rowID +'">';
			ruleValue += '<option value="YES">Yes</option>';
			ruleValue += '<option value="NO">No</option>';
		ruleValue += '</select>';
	}
	document.getElementById('divReportValue-'+ rowID).innerHTML = ruleValue;

	if(ruleType == 'AGE') {
		document.getElementById('divReportSubType-'+ rowID).innerHTML = ruleSubType;
		document.getElementById('divReportSubType-'+ rowID).style.display = '';
	} else {
		document.getElementById('divReportSubType-'+ rowID).innerHTML = '';
		document.getElementById('divReportSubType-'+ rowID).style.display = 'none';
	}

	if(ruleType == 'BIRTH_DATE') {
		$('#inputFromDate-'+ rowID).datepicker({
			autoclose: true
		});
		$('#inputToDate-'+ rowID).datepicker({
			autoclose: true
		});
	}

	if(ruleType == 'MARRIAGE_DATE') {
		$('#inputMFromDate-'+ rowID).datepicker({
			autoclose: true
		});
		$('#inputMToDate-'+ rowID).datepicker({
			autoclose: true
		});
	}

	if(ruleType == 'BIRTH_MARRIAGE_DATE') {
		$('#inputBMFromDate-'+ rowID).datepicker({
			autoclose: true
		});
		$('#inputBMToDate-'+ rowID).datepicker({
			autoclose: true
		});
	}
}

function performSearch()
{
	//disable alerts
	document.getElementById('alertRow').style.display = 'none';
	
	var ruleType = '';
	var ruleSubType = '';
	var ruleValue = '';
	var tempRuleType = '';
	var ruleRowIDList = document.getElementById('reportRuleRowIDList').value;
	var ruleRowIDArr = ruleRowIDList.split(",");
	if(ruleRowIDArr.length > 0)
	{
		for(i=0; i<ruleRowIDArr.length; i++)
		{
			var j = parseInt(ruleRowIDArr[i]);
			if(tempRuleType != '')
			{
				tempRuleType += ',';
			}
			if(ruleSubType != '')
			{
				ruleSubType += ',';
			}
			if(ruleValue != '')
			{
				ruleValue += ',';
			}

			var selRuleTypeIndex = document.getElementById('selReportType-' + j).selectedIndex;
			ruleType = document.getElementById('selReportType-' + j).options[selRuleTypeIndex].value;
			tempRuleType += ruleType;

			//Profile
			if(ruleType != 'AGE')
			{
				ruleSubType += '0';
			}
			if(ruleType == 'PROFILES') {
				var index = document.getElementById('selRuleValueItem-' + j).selectedIndex;
				ruleValue += document.getElementById('selRuleValueItem-' + j).options[index].value;
			} else if(ruleType == 'GENDER') {
				var index = document.getElementById('selRuleValueItem-' + j).selectedIndex;
				ruleValue += document.getElementById('selRuleValueItem-' + j).options[index].value;
			} else if(ruleType == 'AGE') {
				var index = document.getElementById('selRuleSubType-' + j).selectedIndex;
				ruleSubType += document.getElementById('selRuleSubType-' + j).options[index].value;				
				ruleValue += document.getElementById('inputAge-' + j).value;
			} else if(ruleType == 'BIRTH_DATE') {
				var fromDate = document.getElementById('inputFromDate-' + j).value;
				var toDate = document.getElementById('inputToDate-' + j).value;
				ruleValue += fromDate + ':' + toDate;
			} else if(ruleType == 'MARRIAGE_DATE') {
				var fromDate = document.getElementById('inputMFromDate-' + j).value;
				var toDate = document.getElementById('inputMToDate-' + j).value;
				ruleValue += fromDate + ':' + toDate;
			} else if(ruleType == 'BIRTH_MARRIAGE_DATE') {
				var fromDate = document.getElementById('inputBMFromDate-' + j).value;
				var toDate = document.getElementById('inputBMToDate-' + j).value;
				ruleValue += fromDate + ':' + toDate;
			} else if(ruleType == 'MARITAL_STATUS') {
				var index = document.getElementById('selRuleValueItem-' + j).selectedIndex;
				ruleValue += document.getElementById('selRuleValueItem-' + j).options[index].value;
			} else if(ruleType == 'BAPTISM') {
				var index = document.getElementById('selRuleValueItem-' + j).selectedIndex;
				ruleValue += document.getElementById('selRuleValueItem-' + j).options[index].value;
			} else if(ruleType == 'CONFIRMATION') {
				var index = document.getElementById('selRuleValueItem-' + j).selectedIndex;
				ruleValue += document.getElementById('selRuleValueItem-' + j).options[index].value;
			}
		}

		//process column names to include in the report
		//reqFrom - 1; Report Page
		//reqFrom - 2; Add Group Member Page
		reqFrom = document.getElementById('hiddenReqFrom').value;
		var columnData = '';
		if(reqFrom == 1)
		{
			for(i=0; i<19; i++)
			{
				if(document.getElementById('column' + i).checked) {
					if(columnData.length > 0) {
						columnData += ',';
					}
					columnData += i;
				}
			}
		} else {
			columnData = '0,1,2';
		}

		if(columnData.length ==  0) {
			var alertMsg = 'Please select atleast one of the column field.';
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
			return false;
		}
		var includeInactiveProfile = ((document.getElementById('includeInactiveProfile').checked)?1:0);

		document.getElementById('divSearchBtn').style.display = 'none';
		document.getElementById('divLoadingSearchImg').style.display = '';

		var formPostData = 'req=3';
		formPostData += '&reqFrom=' + reqFrom;
		formPostData += '&ruleType=' + escString(tempRuleType);
		formPostData += '&ruleSubType=' + escString(ruleSubType);
		formPostData += '&ruleValue=' + escString(ruleValue);
		formPostData += '&columnData=' + escString(columnData);
		formPostData += '&includeInactiveProfile=' + includeInactiveProfile;

		$.ajax({
			type:'POST',
			url:doReportsFile,
			data:formPostData,
			success:viewReportResponse,
			error:HandleAjaxError
		});
	}
}

function performSubscriptionSearchResponse(response)
{
	var reqFrom = 1;
	var table = '<table id="viewreport" class="table table-striped">';
	table += '</table>';

	document.getElementById('divReportTable').style.display = '';
	document.getElementById('divReportTable').innerHTML = table;
	showReportTableDiv(2, reqFrom);
	/**/

	var dataObj = eval("(" + response + ")" );

	var columns = [];
	jQuery.each(dataObj.aoColumns, function(i, value){
		var obj = { sTitle: value };
		 columns.push(obj);
	});
	//console.log(dataObj.aaData);

	$('#viewreport').dataTable({

		"sDom": 'T<"clear">lfrtip',
		"oTableTools": {
            "aButtons": [
                "copy",
                "print",
				"csv",
				"xls",
				"pdf",
             ],
			"sSwfPath": "plugins/datatables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
        },
		"bProcessing": true,
		"aaData": dataObj.aaData,
		"aoColumns": columns,
		"aaSorting": [],
	//	"bScrollCollapse": true,
		"bFilter": true,
	//	"sPaginationType": "full_numbers",
		"iDisplayLength":100,
	//	"bJQueryUI": true,
	//	"aoColumnDefs": dataObj.aoColumnDefs
	});
	/**/

	$('#viewreport td:last').attr('style', 'background-color:lightyellow');
	$('#viewreport tr:last').attr('style', 'background-color:lightyellow;font-weight:bold;');
}

function performSubscriptionSearch()
{
	//disable alerts
	document.getElementById('alertRow').style.display = 'none';
	
	var fromDate = document.getElementById('inputSubReportFromDate').value;
	var toDate = document.getElementById('inputSubReportToDate').value;
	if(!validateDateFormat(fromDate) || !validateDateFormat(toDate))
	{
		var alertMsg = 'Please check From/To date is in valid format';
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	var subFields = '';
	var subFieldIdsArr = document.getElementById('hiddenSubFieldIds').value.split(",");
	var totalCount = subFieldIdsArr.length;
	if(totalCount > 0)
	{
		for(i=0; i<totalCount; i++)
		{
			var fieldID = subFieldIdsArr[i];
			if(document.getElementById('subcriptionFieldID-' + fieldID).checked)
			{
				if(subFields != "")
					subFields += ",";

				subFields += fieldID;
			}
		}
	}
	if(subFields == '') {
		var alertMsg = 'Please select atleast anyone subscription fields';
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, alertMsg);
		return false;
	}

	document.getElementById('divLoadingSearchImg').style.display = '';
	document.getElementById('divSearchBtn').style.display = 'none';

	var formPostData = 'req=5';
	formPostData += '&fromDate=' + convertDateToDBFormat(fromDate);
	formPostData += '&toDate=' + convertDateToDBFormat(toDate);
	formPostData += '&subFields=' + escString(subFields);

	$.ajax({
		type:'POST',
		url:doReportsFile,
		data:formPostData,
		success:performSubscriptionSearchResponse,
		error:HandleAjaxError
	});
}

function resetSubscriptionSearchForm()
{
	//window.location.href = "reports";
	showSubscriptionReportsScreen();
}

function resetSearchForm()
{
	//window.location.href = "reports";
	var reqFrom = 1;
	showProfileReportsScreen();
}

function checkOrUncheckAllSubscriptionFields(isSelectAll)
{
	if(isSelectAll) {
		document.getElementById('spanSelectAllLink').style.display = 'none';
		document.getElementById('spanUnselectAllLink').style.display = '';
	} else {
		document.getElementById('spanSelectAllLink').style.display = '';
		document.getElementById('spanUnselectAllLink').style.display = 'none';
	}

	var subFieldIdsArr = document.getElementById('hiddenSubFieldIds').value.split(",");
	var totalCount = subFieldIdsArr.length;
	if(totalCount > 0)
	{
		for(i=0; i<totalCount; i++)
		{
			var fieldID = subFieldIdsArr[i];
			if(isSelectAll)
				document.getElementById('subcriptionFieldID-' + fieldID).checked = true;
			else
				document.getElementById('subcriptionFieldID-' + fieldID).checked = false;
		}
	}
	/*
	else
	{
		alert("No subscription field is selected for search! Please select atleast one field.");
		return false;
	}
	*/
}