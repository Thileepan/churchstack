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

	var btnHTML = "";
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
	reportTypeDiv.setAttribute('class', 'span3');
	rowDiv.appendChild(reportTypeDiv);

	var reportValueDivID = 'divReportValue-'+newRowID;
	var reportValueDiv = document.createElement('div');
	reportValueDiv.setAttribute('id', reportValueDivID);
	reportValueDiv.setAttribute('class', 'span9');
	//reportValueDiv.setAttribute('style', 'display:none');
	rowDiv.appendChild(reportValueDiv);	

	/** /
	var reportSubTypeDivID = 'divReportSubType-'+newRowID;
	var reportSubTypeDiv = document.createElement('div');
	reportSubTypeDiv.setAttribute('id', reportSubTypeDivID);
	reportSubTypeDiv.setAttribute('class', 'span1');
	reportSubTypeDiv.setAttribute('style', 'display:none');
	rowDiv.appendChild(reportSubTypeDiv);
	/**/

	//removed: <option value="PROFILES">PROFILES</option>
	//removed: <option value="BIRTH_MARRIAGE_DATE">BIRTH OR MARRIAGE DATE</option>
	var ruleType = '<i class="icon-remove curHand" onclick="deleteReportRuleRow('+ newRowID +')"></i>&nbsp;<select onChange="changeRuleSubTypeAndValue('+ newRowID +');" id="selReportType-'+newRowID+'"></select>';
	//var ruleType = '<i class="icon-remove curHand" onclick="deleteReportRuleRow('+ newRowID +')"></i>&nbsp;<select onChange="changeRuleSubTypeAndValue('+ newRowID +');" id="selReportType-'+newRowID+'"><option value="GENDER">GENDER</option><option value="AGE">AGE</option><option value="BIRTH_DATE">BIRTH DATE</option><option value="MARRIAGE_DATE">MARRIAGE DATE</option><option value="MARITAL_STATUS">MARITAL STATUS</option><option value="BAPTISM">BAPTISM</option><option value="CONFIRMATION">CONFIRMATION</option></select>';
	//var ruleValue = '<select id="selRuleValueItem-' + newRowID + '"><option value="ALL">All</option><option value="FAMILY_HEAD">Family Head</option><option value="INDIVIDUAL">Individual</option></select>';
	var ruleValue = '<select id="selRuleValueItem-' + newRowID + '"><option value="MALE">Male</option><option value="FEMALE">Female</option></select>';
	document.getElementById(reportTypeDivID).innerHTML = ruleType;
//	document.getElementById(reportSubTypeDivID).innerHTML = '';
	document.getElementById(reportValueDivID).innerHTML = ruleValue;
	document.getElementById('maxReportRuleRowID').value = newRowID;
	document.getElementById('reportRuleRowIDList').value += ","+newRowID;

	//Inserting custom field options
	initialReportFiltersSelectBox = document.getElementById("selReportType-1");
	initialReportFiltersSelectBoxLenth = document.getElementById("selReportType-1").length;
	newReportFiltersSelectBox = document.getElementById("selReportType-"+newRowID);
	var oldOptCount = 0;
	var newOptCount = 0;
	while(oldOptCount < initialReportFiltersSelectBoxLenth)
	{
		if(initialReportFiltersSelectBox[oldOptCount].value != "PROFILES")
		{
			newReportFiltersSelectBox.options[newOptCount] = new Option(initialReportFiltersSelectBox[oldOptCount].text, initialReportFiltersSelectBox[oldOptCount].value);
			newOptCount++;
		}
		oldOptCount++;
	}
	return false;
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

	var idsToSetDatePicker = new Array();

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
		/** /
		ruleSubType = '<select id="selRuleSubType-'+ rowID +'">';
			ruleSubType += '<option value="IS_LESS_THAN">Is Less Than</option>';
			ruleSubType += '<option value="IS">Is</option>';
			ruleSubType += '<option value="IS_GREATER_THAN">Is Greater Than</option>';
		ruleSubType += '</select>'
		ruleValue = '<input type="text" id="inputAge-'+ rowID +'" value="" placeholder="Age" />';
		/**/
		ruleValue = '<select id="selRuleSubType-'+ rowID +'">';
			ruleValue += '<option value="IS_LESS_THAN">Is Less Than</option>';
			ruleValue += '<option value="IS">Is</option>';
			ruleValue += '<option value="IS_GREATER_THAN">Is Greater Than</option>';
		ruleValue += '</select>'
		ruleValue += '&nbsp;&nbsp;<input type="text" id="inputAge-'+ rowID +'" value="" placeholder="Age" />';
	} else if(ruleType == 'BIRTH_DATE') {
			ruleValue = '<input type="text" id="inputFromDate-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;&nbsp;';
			ruleValue += '<input type="text" id="inputToDate-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />';
	} else if(ruleType == 'MARRIAGE_DATE') {
			ruleValue = '<input type="text" id="inputMFromDate-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;&nbsp;';
			ruleValue += '<input type="text" id="inputMToDate-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />';
	} else if(ruleType == 'BIRTH_MARRIAGE_DATE') {
			ruleValue = '<input type="text" id="inputBMFromDate-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;&nbsp;';
			ruleValue += '<input type="text" id="inputBMToDate-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />';
	} else if(ruleType == 'MARITAL_STATUS') {
		ruleValue = '<select id="selRuleValueItem-'+ rowID +'">';
			ruleValue += '<option value="SINGLE">Single</option>';
			ruleValue += '<option value="MARRIED">Married</option>';
			ruleValue += '<option value="WIDOW">Widow</option>';
			ruleValue += '<option value="WIDOWER">Widower</option>';
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
	} else {//CUSTOM PROFILE FIELDS
		var fieldType = document.getElementById('customFieldTypeWithFieldID-'+ ruleType).value;
		var fieldCSVOptions = document.getElementById('customFieldCSVOptionsWithFieldID-'+ ruleType).value;
		if(fieldType == 1)//textbox
		{
			ruleValue = '<input type="text" id="customFieldTextboxValue-'+ rowID +'" placeholder="contains the text...">';
		}
		else if(fieldType == 2)//numbers
		{
			ruleValue = '<select id="customFieldNumbersFilterSelBox-'+ rowID +'">';
				ruleValue += '<option value="lessorequalto">is less than or equal to</option>';
				ruleValue += '<option value="equalto">is equal to</option>';
				ruleValue += '<option value="greaterorequalto">is greater than or equal to</option>';
			ruleValue += '</select>&nbsp;&nbsp;';
			ruleValue += '<input type="number" id="customFieldNumbersValue-'+ rowID +'" placeholder="enter the number">';
		}
		else if(fieldType == 3)//password
		{
			ruleValue = '<input type="password" id="customFieldPasswordValue-'+ rowID +'" placeholder="contains the string...">';
		}
		else if(fieldType == 4)//date
		{
			ruleValue = '<input type="text" id="customFieldDateFromValue-'+ rowID +'" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" />&nbsp;&nbsp;';
			ruleValue += '<input type="text" id="customFieldDateToValue-'+ rowID +'" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" />&nbsp;&nbsp;';
			ruleValue += '<input type="checkbox" id="customFieldDateIgnoreYear-'+ rowID +'" value="1"/>&nbsp;Ignore year';
			idsToSetDatePicker.push('customFieldDateFromValue-'+ rowID);
			idsToSetDatePicker.push('customFieldDateToValue-'+ rowID);
		}
		else if(fieldType == 5)//Link/Url
		{
			ruleValue = '<input type="text" id="customFieldURLValue-'+ rowID +'" placeholder="contains the string...">';
		}
		else if(fieldType == 6)//dropdown
		{
			var cusFieldOptionsArray = fieldCSVOptions.split(",");
			ruleValue = '<select id="customFieldDropboxSelBox-'+ rowID +'">';
				for(var db=0; db < cusFieldOptionsArray.length; db++)
				{
					ruleValue += '<option value="'+db+'">'+cusFieldOptionsArray[db]+'</option>';
				}
			ruleValue += '</select>';
		}
		else if(fieldType == 7)//tickbox
		{
			ruleValue = '<select id="customFieldTickboxSelBox-'+ rowID +'">';
				ruleValue += '<option value="1">Yes</option>';
				ruleValue += '<option value="0">No</option>';
			ruleValue += '</select>';
		}
		else if(fieldType == 8)//textarea
		{
			ruleValue = '<input type="text" id="customFieldTextAreaValue-'+ rowID +'" placeholder="contains the text...">';
		}
	}
	document.getElementById('divReportValue-'+ rowID).innerHTML = ruleValue;


	/** /
	if(ruleType == 'AGE') {
		document.getElementById('divReportSubType-'+ rowID).innerHTML = ruleSubType;
		document.getElementById('divReportSubType-'+ rowID).style.display = '';
	} else {
		document.getElementById('divReportSubType-'+ rowID).innerHTML = '';
		document.getElementById('divReportSubType-'+ rowID).style.display = 'none';
	}
	/**/

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

	for(var d=0; d < idsToSetDatePicker.length; d++)
	{
		var curr_id = idsToSetDatePicker[d];
		$('#'+curr_id).datepicker({
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
	var delimCustomFieldIDs = "";
	var delimCustomFieldTypes = "";
	var delimCustomFieldTextboxContains = "";
	var delimCustomFieldNumberSelFilterValue = "";
	var delimCustomFieldNumberValue = "";
	var delimCustomFieldDateFrom = "";
	var delimCustomFieldDateTo = "";
	var delimCustomFieldDateIgnoreYear = "";
	var delimCustomFieldURLContains = "";
	var delimCustomFieldDropboxValue = "";
	var delimCustomFieldTickboxValue = "";
	var delimCustomFieldTextAreaContains = "";

	var customFieldsFilterCount = 0;
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
			} else {

				//Custom fields
				var currCustomFieldID = ruleType;
				var currCustomFieldType = document.getElementById('customFieldTypeWithFieldID-'+ ruleType).value;
				var currCustomFieldTextboxContains = "";
				var currCustomFieldNumberSelFilterValue = "";
				var currCustomFieldNumberValue = "";
				var currCustomFieldDateFrom = "";
				var currCustomFieldDateTo = "";
				var currCustomFieldDateIgnoreYear = "";
				var currCustomFieldURLContains = "";
				var currCustomFieldDropboxValue = "";
				var currCustomFieldTickboxValue = "";
				var currCustomFieldTextAreaContains = "";
				if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 1) {//Textbox
					currCustomFieldTextboxContains = document.getElementById("customFieldTextboxValue-"+j).value;
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 2) {//Numbers
					currCustomFieldNumberSelFilterValue = document.getElementById("customFieldNumbersFilterSelBox-"+j).value;
					currCustomFieldNumberValue = document.getElementById("customFieldNumbersValue-"+j).value;
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 3) {//Password
					//
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 4) {//Date
					currCustomFieldDateFrom = document.getElementById("customFieldDateFromValue-"+j).value;
					currCustomFieldDateTo = document.getElementById("customFieldDateToValue-"+j).value;
					currCustomFieldDateIgnoreYear = ((document.getElementById("customFieldDateIgnoreYear-"+j).checked)? 1 : 0);
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 5) {//Link/URL
					currCustomFieldURLContains = document.getElementById("customFieldURLValue-"+j).value;
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 6) {//Dropdown
					currCustomFieldDropboxValue = document.getElementById("customFieldDropboxSelBox-"+j).value;
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 7) {//Tickbox
					currCustomFieldTickboxValue = document.getElementById("customFieldTickboxSelBox-"+j).value;
				} else if(document.getElementById("customFieldTypeWithFieldID-"+ruleType).value == 8) {//Textarea
					currCustomFieldTextAreaContains = document.getElementById("customFieldTextAreaValue-"+j).value;
				}

				delimCustomFieldIDs = ((customFieldsFilterCount == 0)? currCustomFieldID : delimCustomFieldIDs+"/:/"+currCustomFieldID);
				delimCustomFieldTypes = ((customFieldsFilterCount == 0)? currCustomFieldType : delimCustomFieldTypes+"/:/"+currCustomFieldType);
				delimCustomFieldTextboxContains = ((customFieldsFilterCount == 0)? currCustomFieldTextboxContains : delimCustomFieldTextboxContains+"/:/"+currCustomFieldTextboxContains);
				delimCustomFieldNumberSelFilterValue = ((customFieldsFilterCount == 0)? currCustomFieldNumberSelFilterValue : delimCustomFieldNumberSelFilterValue+"/:/"+currCustomFieldNumberSelFilterValue);
				delimCustomFieldNumberValue = ((customFieldsFilterCount == 0)? currCustomFieldNumberValue : delimCustomFieldNumberValue+"/:/"+currCustomFieldNumberValue);
				delimCustomFieldDateFrom = ((customFieldsFilterCount == 0)? currCustomFieldDateFrom : delimCustomFieldDateFrom+"/:/"+currCustomFieldDateFrom);
				delimCustomFieldDateTo = ((customFieldsFilterCount == 0)? currCustomFieldDateTo : delimCustomFieldDateTo+"/:/"+currCustomFieldDateTo);
				delimCustomFieldDateIgnoreYear = ((customFieldsFilterCount == 0)? currCustomFieldDateIgnoreYear : delimCustomFieldDateIgnoreYear+"/:/"+currCustomFieldDateIgnoreYear);
				delimCustomFieldURLContains = ((customFieldsFilterCount == 0)? currCustomFieldURLContains : delimCustomFieldURLContains+"/:/"+currCustomFieldURLContains);
				delimCustomFieldDropboxValue = ((customFieldsFilterCount == 0)? currCustomFieldDropboxValue : delimCustomFieldDropboxValue+"/:/"+currCustomFieldDropboxValue);
				delimCustomFieldTickboxValue = ((customFieldsFilterCount == 0)? currCustomFieldTickboxValue : delimCustomFieldTickboxValue+"/:/"+currCustomFieldTickboxValue);
				delimCustomFieldTextAreaContains = ((customFieldsFilterCount == 0)? currCustomFieldTextAreaContains : delimCustomFieldTextAreaContains+"/:/"+currCustomFieldTextAreaContains);

				customFieldsFilterCount++;
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
		formPostData += '&delimCustomFieldIDs=' + escString(delimCustomFieldIDs);
		formPostData += '&delimCustomFieldTypes=' + escString(delimCustomFieldTypes);
		formPostData += '&delimCustomFieldTextboxContains=' + escString(delimCustomFieldTextboxContains);
		formPostData += '&delimCustomFieldNumberSelFilterValue=' + escString(delimCustomFieldNumberSelFilterValue);
		formPostData += '&delimCustomFieldNumberValue=' + escString(delimCustomFieldNumberValue);
		formPostData += '&delimCustomFieldDateFrom=' + escString(delimCustomFieldDateFrom);
		formPostData += '&delimCustomFieldDateTo=' + escString(delimCustomFieldDateTo);
		formPostData += '&delimCustomFieldDateIgnoreYear=' + escString(delimCustomFieldDateIgnoreYear);
		formPostData += '&delimCustomFieldURLContains=' + escString(delimCustomFieldURLContains);
		formPostData += '&delimCustomFieldDropboxValue=' + escString(delimCustomFieldDropboxValue);
		formPostData += '&delimCustomFieldTickboxValue=' + escString(delimCustomFieldTickboxValue);
		formPostData += '&delimCustomFieldTextAreaContains=' + escString(delimCustomFieldTextAreaContains);

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

function resetSearchForm(reqFrom)
{
	//window.location.href = "reports";
//	var reqFrom = 1;
	//from group module
	if(reqFrom == 2) {
		listAllGroups();
		return true;
	}
	showProfileReportsScreen(reqFrom);
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