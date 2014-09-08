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
	formPostData += '&churchName='+churchName;
	formPostData += '&churchDesc='+churchDesc;
	formPostData += '&churchAddr='+churchAddr;
	formPostData += '&landline='+landline;
	formPostData += '&mobile='+mobile;
	formPostData += '&email='+email;
	formPostData += '&website='+website;
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

