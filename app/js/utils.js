function menuLinkClicked(menuID)
{
	if(menuID == 1) {
		document.getElementById('mhome').className = "active";
		//document.getElementById('mhomeText').className = "label label-info";
		document.getElementById('homeLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('mhomeText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 2) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "active";
		//document.getElementById('msubscriptionText').className = "label label-info";
		document.getElementById('subscriptionLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('msubscriptionText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 3) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "active";
		//document.getElementById('meventsText').className = "label label-info";
		document.getElementById('eventsLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('meventsText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 4) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "active";
		//document.getElementById('msettingsText').className = "label label-info";
		document.getElementById('settingsLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('msettingsText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 5) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "active";
		//document.getElementById('mreportsText').className = "label label-info";
		document.getElementById('reportsLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('mreportsText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 6) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "active";
		//document.getElementById('mharvest').className = "";
		//document.getElementById('mdashboardText').className = "label label-info";
		document.getElementById('dashboardLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('mdashboardText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 7) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "active";
		//document.getElementById('mharvestText').className = "label label-info";
		//document.getElementById('mharvestText').setAttribute("style", "color:white;font-weight:bold;");
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "";
	} else if(menuID == 8) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "active";
		document.getElementById('mfunds').className = "";
		//document.getElementById('mgroupsText').className = "label label-info";
		document.getElementById('groupsLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('mgroupsText').setAttribute("style", "color:white;font-weight:bold;");
	} else if(menuID == 9) {
		document.getElementById('mhome').className = "";
		document.getElementById('msubscription').className = "";
		document.getElementById('mevents').className = "";
		document.getElementById('msettings').className = "";
		document.getElementById('mreports').className = "";
		document.getElementById('mdashboard').className = "";
		//document.getElementById('mharvest').className = "";
		document.getElementById('mgroups').className = "";
		document.getElementById('mfunds').className = "active";
		//document.getElementById('mfundsText').className = "label label-info";
		document.getElementById('fundsLink').setAttribute("style", "background-color:#3a87ad;");
		document.getElementById('mfundsText').setAttribute("style", "color:white;font-weight:bold;");
	}
}

function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : evt.keyCode;

	if(charCode==8 || charCode==13|| charCode==99|| charCode==118) // || charCode==46
	{
		return true;  
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57))
	{  
		return false; 
	}
	return true;
}

function convertDateToDBFormat(date)
{
	if(date != '') {
		var dateArr = date.split("/");
		var dd = dateArr[0];
		var mm = dateArr[1];
		var yy = dateArr[2];
		date = yy + '-' + mm + '-' + dd;
	} else {
		date = "0000-00-00";
	}
	return date;
}

function validateDateFormat(date)
{
	var isValid = false;
	if(date != '') {
		var dateArr = date.split("/");
		if(dateArr.length == 3)
		{
			var dd = dateArr[0];
			var mm = dateArr[1];
			var yy = dateArr[2];
//			if(dd > 0 && dd <31)
			isValid = true;
		}
	}
	return isValid;
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}