function menuLinkClicked(menuID)
{
	document.getElementById('mhome').className = "";
	document.getElementById('homeLink').setAttribute("style", "");

	document.getElementById('msubscription').className = "";
	document.getElementById('subscriptionLink').setAttribute("style", "");

	document.getElementById('mevents').className = "";
	document.getElementById('eventsLink').setAttribute("style", "");

	document.getElementById('msettings').className = "";
	document.getElementById('settingsLink').setAttribute("style", "");

	document.getElementById('mreports').className = "";
	document.getElementById('reportsLink').setAttribute("style", "");

	document.getElementById('mdashboard').className = "";
	document.getElementById('dashboardLink').setAttribute("style", "");

	document.getElementById('mnotifications').className = '';
	document.getElementById('notificationsLink').setAttribute("style", "");

	//document.getElementById('mharvest').className = "";
	document.getElementById('mgroups').className = "";
	document.getElementById('groupsLink').setAttribute("style", "");

	document.getElementById('mfunds').className = "";
	document.getElementById('fundsLink').setAttribute("style", "");

	document.getElementById('menuMyAccount').className = "";
	document.getElementById('menuMyAccountLink').setAttribute("style", "");

	var activeClassName = "active";
	var styleMenuLink = "background-color:#3a87ad;";
	var styleMenuText = "color:white;font-weight:bold;";

	if(menuID == 1) {
		document.getElementById('mhome').className = activeClassName;
		document.getElementById('homeLink').setAttribute("style", styleMenuLink);
		document.getElementById('mhomeText').setAttribute("style", styleMenuText);
	} else if(menuID == 2) {
		document.getElementById('msubscription').className = activeClassName;
		document.getElementById('subscriptionLink').setAttribute("style", styleMenuLink);
		document.getElementById('msubscriptionText').setAttribute("style", styleMenuText);
	} else if(menuID == 3) {
		document.getElementById('mevents').className = activeClassName;
		document.getElementById('eventsLink').setAttribute("style", styleMenuLink);
		document.getElementById('meventsText').setAttribute("style", styleMenuText);
	} else if(menuID == 4) {
		document.getElementById('msettings').className = activeClassName;
		document.getElementById('settingsLink').setAttribute("style", styleMenuLink);
		document.getElementById('msettingsText').setAttribute("style", styleMenuText);
	} else if(menuID == 5) {
		document.getElementById('mreports').className = activeClassName;
		document.getElementById('reportsLink').setAttribute("style", styleMenuLink);
		document.getElementById('mreportsText').setAttribute("style", styleMenuText);
	} else if(menuID == 6) {
		document.getElementById('mdashboard').className = activeClassName;
		document.getElementById('dashboardLink').setAttribute("style", styleMenuLink);
		document.getElementById('mdashboardText').setAttribute("style", styleMenuText);
	} else if(menuID == 7) {
	} else if(menuID == 8) {
		document.getElementById('mgroups').className = activeClassName;
		document.getElementById('groupsLink').setAttribute("style", styleMenuLink);
		document.getElementById('mgroupsText').setAttribute("style", styleMenuText);
	} else if(menuID == 9) {
		document.getElementById('mfunds').className = activeClassName;
		document.getElementById('fundsLink').setAttribute("style", styleMenuLink);
		document.getElementById('mfundsText').setAttribute("style", styleMenuText);
	} else if(menuID == 10) {
		document.getElementById('mnotifications').className = activeClassName;
		document.getElementById('notificationsLink').setAttribute("style", styleMenuLink);
		document.getElementById('mnotificationsText').setAttribute("style", styleMenuText);
	} else if(menuID == 11) {
		document.getElementById('menuMyAccount').className = activeClassName;
		document.getElementById('menuMyAccountLink').setAttribute("style", styleMenuLink);
		document.getElementById('menuMyAccountText').setAttribute("style", styleMenuText);
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

function isValidEmail(emailStr) 
{
	var permittedEmailChars = /^([a-zA-Z0-9_@\~\-\=\+\.\?\*])*$/;
	emailPattern = /^([a-zA-Z0-9_])+([\._\-\~\=\+\?\*][a-zA-Z0-9]+)*@([a-zA-Z0-9])+([\._\-][a-zA-Z0-9]+)*(\.[a-zA-Z]+)+$/;
	/**/
	if(!(permittedEmailChars).test(emailStr))
	{
		//alert(lang_ary['The email address you have entered has invalid characters. An email address can contain only [A-Z][a-z][0-9][-@_.\+\*\?\~\=] characters.']);
		return false;
	}
	/**/
	if(!emailPattern.test(emailStr))
	{
		//alert("Email address entered does not seem to be valid");
		return false;
	}
	return true;
}

function escString(encStr)
{
	encStr = escape(encStr);
	encStr = encStr.replace(/\//g,"%2F");
	encStr = encStr.replace(/\?/g,"%3F");
	encStr = encStr.replace(/=/g,"%3D");
	encStr = encStr.replace(/&/g,"%26");
	encStr = encStr.replace(/@/g,"%40");
	encStr = encStr.replace(/\+/g,"%2B");
	return encStr;
}

function setCookie(cName, cValue, exDays)
{
	var d = new Date();
    d.setTime(d.getTime() + (exDays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cName + "=" + cValue + "; " + expires;
}

function getCookie(cName)
{
	var name = cName + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}