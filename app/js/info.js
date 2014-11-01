//global variables
doInfoFile = 'server/doinfo';

function getChurchInformationForm(isUpdate)
{
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=1&isUpdate='+isUpdate;
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:getChurchInformationFormResponse,
		error:HandleAjaxError
	});	
}

function getChurchInformationFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = "My Church";
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputChurchName').focus();
}

function addOrUpdateChurchInfo(isUpdate)
{
	isEdit = isUpdate;
	var churchName = document.getElementById('inputChurchName').value;
	var churchDesc = document.getElementById('inputChurchDesc').value;
	var churchAddr = document.getElementById('inputChurchAddress').value;
	var landline = document.getElementById('inputLandline').value;
	var mobile = document.getElementById('inputMobile').value;
	var email = document.getElementById('inputEmail').value;
	var website = document.getElementById('inputWebsite').value;
	var countryID = document.getElementById('inputCountryID').value;
	var currencyID = document.getElementById('inputCurrencyID').value;
	var timeZone = document.getElementById('inputTimeZone').value;

	if(churchName == "")
	{
		var resultToUI = getAlertDiv(2, 'Please choose a valid church name');
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}

	var formPostData = 'req=2';
	formPostData += '&isUpdate='+isUpdate;
	formPostData += '&churchName='+escString(churchName);
	formPostData += '&churchDesc='+escString(churchDesc);
	formPostData += '&churchAddr='+escString(churchAddr);
	formPostData += '&landline='+escString(landline);
	formPostData += '&mobile='+escString(mobile);
	formPostData += '&email='+escString(email);
	formPostData += '&website='+escString(website);
	formPostData += '&countryID='+countryID;
	formPostData += '&currencyID='+currencyID;
	formPostData += '&timeZone='+timeZone;

	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:addOrUpdateChurchInfoResponse,
		error:HandleAjaxError
	});	
}

function addOrUpdateChurchInfoResponse(response)
{
	if(response[0]==1) {
		getChurchInformation();
	} else {
		var msg = ((isEdit)?'Unable to update the church information':'Unable to add the church information');
		var resultToUI = getAlertDiv(2, msg);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}
}

function getChurchInformation()
{
	document.getElementById('myChurch').className = 'active';
	document.getElementById('billing').className = '';
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=3';
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:getChurchInformationResponse,
		error:HandleAjaxError
	});
}

function getChurchInformationResponse(response)
{
	document.getElementById('pageHeader').innerHTML = "My Church";
	document.getElementById('pageContent').innerHTML = response;
}

function getBillingDetails()
{
	document.getElementById('myChurch').className = '';
	document.getElementById('billing').className = 'active';
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=4';
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:getBillingDetailsResponse,
		error:HandleAjaxError
	});
}

function getBillingDetailsResponse(response)
{
	document.getElementById('pageHeader').innerHTML = "Billing";
	document.getElementById('pageContent').innerHTML = response;
}

function getLoginCredentialsForm()
{
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=5';
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:getLoginCredentialsFormResponse,
		error:HandleAjaxError
	});	
}

function getLoginCredentialsFormResponse(response)
{
	document.getElementById('pageHeader').innerHTML = "My Login Credentials";
	document.getElementById('pageContent').innerHTML = response;

	showHideLoginCredTabs(1);
}

function showHideLoginCredTabs(tabType)
{
	document.getElementById('alertRow').style.display = 'none';
	if(tabType==1)
	{
		document.getElementById('changePasswordDiv').className = 'tab-pane active';
		document.getElementById('changeEmailDiv').className = 'tab-pane';
	}
	else if(tabType==2)
	{
		document.getElementById('changePasswordDiv').className = 'tab-pane';
		document.getElementById('changeEmailDiv').className = 'tab-pane active';
	}
	var formPostData = 'req=6&tabType='+tabType;

	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:showHideLoginCredTabsResponse,
		error:HandleAjaxError
	});
}

function showHideLoginCredTabsResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.tabType==1)
	{
		document.getElementById("changePasswordDiv").innerHTML = dataObj.divHTML;
		document.getElementById("txtNewPassword").focus();
	}
	else if(dataObj.tabType==2)
	{
		document.getElementById("changeEmailDiv").innerHTML = dataObj.divHTML;
		document.getElementById("txtNewEmail").focus();
	}
	return false;
}

function updateLoginCredentials(tabType)
{
	document.getElementById('alertRow').style.display = 'none';
	var errorMessage = "";
	var formPostData = 'req=7&tabType='+tabType;

	if(tabType==1)
	{
		document.getElementById('txtNewPassword').value = trim(document.getElementById('txtNewPassword').value);
		document.getElementById('txtConfirmPassword').value = trim(document.getElementById('txtConfirmPassword').value);
		if(document.getElementById('txtNewPassword').value == "") {
			document.getElementById('txtNewPassword').focus();
			errorMessage = 'Password field cannot be empty';
		} else if(document.getElementById('txtNewPassword').value != document.getElementById('txtConfirmPassword').value) {
			document.getElementById('txtConfirmPassword').focus();
			errorMessage = 'Passwords do not match.';
		}

		formPostData += "&newPassword="+escString(document.getElementById('txtNewPassword').value);
	}
	else if(tabType==2)
	{
		document.getElementById('txtNewEmail').value = trim(document.getElementById('txtNewEmail').value);
		if(document.getElementById('txtNewEmail').value == "" || !isValidEmail(document.getElementById('txtNewEmail').value)) {
			document.getElementById('txtNewEmail').focus();
			errorMessage = 'You have to enter a valid email address inorder to update the email address used to login to your account.';
		}
		formPostData += "&newEmail="+escString(document.getElementById('txtNewEmail').value);
	}

	if(errorMessage.length > 0) {
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = getAlertDiv(2, errorMessage);
		$('html,body').scrollTop(0);
		return false;
	}

	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:updateLoginCredentialsResponse,
		error:HandleAjaxError
	});
}

function updateLoginCredentialsResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.tabType==1)
	{
		if(dataObj.rsno == 1)
		{
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
			document.getElementById('txtNewPassword').value = "";
			document.getElementById('txtConfirmPassword').value = "";
		}
		else
		{
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
		$('html,body').scrollTop(0);
	}
	else if(dataObj.tabType==2)
	{
		if(dataObj.rsno == 1)
		{
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(1, dataObj.msg);
		}
		else
		{
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = getAlertDiv(2, dataObj.msg);
		}
		$('html,body').scrollTop(0);
	}
	return false;
}

function infoTabsChangeClass(idToBeActive)
{
	//First set empty for all the tabs class
	document.getElementById("myChurch").className = "";
	document.getElementById("notifications").className = "";
	document.getElementById("billing").className = "";
	document.getElementById("logininfo").className = "";
	document.getElementById("myInvoices").className = "";

	//Now set the active class for the selected tab
	document.getElementById(idToBeActive).className = "active";
}

function getBillingPlans()
{
	document.getElementById('alertRow').style.display = 'none';
	var formPostData = 'req=8';
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:getBillingPlansResponse,
		error:HandleAjaxError
	});
}

function getBillingPlansResponse(response)
{
	document.getElementById('pageHeader').innerHTML = "Plans";
	document.getElementById('pageContent').innerHTML = response;
}

function getInvoicesList()
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('pageHeader').innerHTML = "My Invoices/Payments";
	var table = '<table id="listInvoicesTable" class="table table-striped"><thead><tr><th>Order ID</th><th>Transaction ID</th><th>Date</th><th>Amount Paid</th><th>Status</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('pageContent').innerHTML = table;
	
	oTable = $('#listInvoicesTable').dataTable( {
		/*"aoColumns": [
			{ "sWidth": "5%" },
			{ "sWidth": "30%"  },
			{ "sWidth": "20%"  },
			{ "sWidth": "30%" },
			{ "sWidth": "15%"  },			
		],*/
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": "server/doinfo",
		"iDisplayLength":100,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=9",
                "success": fnCallback
            } );
        }
	});
}

function showInvoiceReport(invoiceID)
{
	document.getElementById("invoiceDetailsBody").innerHTML = "Loading the data ...";
	var formPostData = "req=10";
	formPostData += "&invoice_id="+invoiceID;
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:showInvoiceDataFromResponse,
		error:HandleAjaxError
	});
	return false;
}

function showInvoiceDataFromResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	document.getElementById("invoiceDetailsBody").innerHTML = dataObj.rslt;
	return false;
}

function downloadInvoiceReportPDF(invoiceID, transactionID)
{
	var formPostData = "req=11";
	formPostData += "&invoice_id="+invoiceID;
	formPostData += "&transaction_id="+transactionID;
	$.ajax({
		type:'POST',
		url:doInfoFile,
		data:formPostData,
		success:downloadInvoiceReportPDFResponse,
		error:HandleAjaxError
	});
	return false;
}

function downloadInvoiceReportPDFResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	document.getElementById("pdfInputHtml").value = dataObj.input_html;
	document.getElementById("pdfTargetFile").value = dataObj.target_file;
	document.pdfForm.submit();
	return false;
}
