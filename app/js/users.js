//global variables
doUsers = '../server/dousers';

function forgotPassword(type)
{
	/** /
	document.getElementById("errorDiv").innerHTML = "";
	document.getElementById("successDiv").innerHTML = "";
	/**/
	if(type==1)
	{
		document.getElementById("errorResultDiv").style.display = "none";
		document.getElementById("errorResultMsg").innerHTML = "";
		document.getElementById("successResultDiv").style.display = "none";
		document.getElementById("successResultMsg").innerHTML = "";
		document.getElementById("txtEmail").value = trim(document.getElementById("txtEmail").value);
		if(document.getElementById("txtEmail").value == "") {
			return false;
		}
		var forgotPwdBtn = $('#btnForgotPwd');
		forgotPwdBtn.button('loading');
		doUsers = document.getElementById("doUsersHttpdFile").value;
		/** /
		document.getElementById("continueBtnDiv").style.display = "none";
		document.getElementById("continueProgDiv").style.display = "";
		/**/
	}
	else if(type==2)
	{
		document.getElementById("errorResultDiv").style.display = "none";
		document.getElementById("errorResultMsg").innerHTML = "";
		document.getElementById("successResultDiv").style.display = "none";
		//document.getElementById("successResultMsg").innerHTML = "";

		document.getElementById("txtEmail").value = trim(document.getElementById("txtEmail").value);
		document.getElementById("txtPassword").value = trim(document.getElementById("txtPassword").value);
		document.getElementById("txtConfirmPassword").value = trim(document.getElementById("txtConfirmPassword").value);
		if(document.getElementById("txtEmail").value=="" || document.getElementById("txtPassword").value == "" || document.getElementById("txtConfirmPassword").value=="") {
			document.getElementById("errorResultMsg").innerHTML = "All of the above fields are mandatory, fill up all the fields to reset the password";
			document.getElementById("errorResultDiv").style.display = "";
			return false;
		}
		if(document.getElementById("txtPassword").value = trim(document.getElementById("txtPassword").value))
		if(document.getElementById("txtPassword").value != document.getElementById("txtConfirmPassword").value) {
			document.getElementById("errorResultMsg").innerHTML = "The passwords you have entered do not match. Make sure you enter the same password in both the password fields.";
			document.getElementById("errorResultDiv").style.display = "";
			document.getElementById("txtPassword").value = "";
			document.getElementById("txtConfirmPassword").value = "";
			return false;
		}
		/** /
		if(document.getElementById("txtEmail").value=="" || document.getElementById("txtPassword").value == "" || document.getElementById("txtConfirmPassword").value=="") {
			document.getElementById("errorDiv").innerHTML = "All of the above fields are mandatory, fill up all the fields to reset the password";
			return false;
		}
		if(document.getElementById("txtPassword").value = trim(document.getElementById("txtPassword").value))
		if(document.getElementById("txtPassword").value != document.getElementById("txtConfirmPassword").value) {
			document.getElementById("errorDiv").innerHTML = "The passwords you have entered do not match. Make sure you enter the same password in both the password fields.";
			document.getElementById("txtPassword").value = "";
			document.getElementById("txtConfirmPassword").value = "";
			return false;
		}
		document.getElementById("continueBtnDiv").style.display = "none";
		document.getElementById("continueProgDiv").style.display = "";
		/**/
		var resetPwdBtn = $('#btnContinue');
		resetPwdBtn.button('loading');
		doUsers = document.getElementById("doUsersHttpdFile").value;
	}
	else if(type==3)
	{
		document.getElementById("emailLost").value = trim(document.getElementById("emailLost").value);
		if(document.getElementById("emailLost").value == "") {
			return false;
		}
		var forgotPwdBtn = $('#btnForgotPwd');
		forgotPwdBtn.button('loading');
		doUsers = document.getElementById("doUsersHttpdFile").value;
	}
	var formPostData = "";
	if(type==1) {
		formPostData += "req=1";
		formPostData += "&type="+type;
		formPostData += "&email="+document.getElementById("txtEmail").value;
	} else if(type==2) {
		formPostData += "req=2";
		formPostData += "&type="+type;
		formPostData += "&email="+document.getElementById("txtEmail").value;
		formPostData += "&pwd="+document.getElementById("txtPassword").value;
		formPostData += "&globalSSToken="+document.getElementById("globalSessionSecToken").value;
	} else if(type==3) {
		formPostData += "req=1";
		formPostData += "&type="+type;
		formPostData += "&email="+document.getElementById("emailLost").value;
	}
	$.ajax({
		type:'POST',
		url:doUsers,
		data:formPostData,
		success:processForgotPwdResponse,
		error:HandleAjaxError
	});
	return false;
}

function processForgotPwdResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.type==1) {
		if(dataObj.rsno==0) {
			/** /
			document.getElementById("errorDiv").innerHTML = dataObj.msg;
			document.getElementById("successDiv").innerHTML = "";
			//document.getElementById("continueBtnDiv").style.display = "";
			//document.getElementById("continueProgDiv").style.display = "none";
			/**/
			document.getElementById("errorResultMsg").innerHTML = dataObj.msg;
			document.getElementById("errorResultDiv").style.display = "";
			document.getElementById("successResultDiv").style.display = "none";
			document.getElementById("successResultMsg").innerHTML = "";
			document.getElementById("txtEmail").focus();
			var forgotPwdBtn = $('#btnForgotPwd');
			forgotPwdBtn.button('reset');
			return false;
		} else if(dataObj.rsno==1) {
			/** /
			document.getElementById("errorDiv").innerHTML = "";
			document.getElementById("successDiv").innerHTML = dataObj.msg;
			//document.getElementById("continueBtnDiv").style.display = "none";
			//document.getElementById("continueProgDiv").style.display = "none";
			/**/
			document.getElementById("emailDiv").style.display = "none";
			document.getElementById("errorResultMsg").innerHTML = "";
			document.getElementById("errorResultDiv").style.display = "none";
			document.getElementById("successResultMsg").innerHTML = dataObj.msg;
			document.getElementById("successResultDiv").style.display = "";
			var forgotPwdBtn = $('#btnForgotPwd');
			forgotPwdBtn.button('reset');
			return false;
		}
	} else 	if(dataObj.type==2) {
		if(dataObj.rsno==0) {
			var resetPwdBtn = $('#btnContinue');
			resetPwdBtn.button('reset');
			document.getElementById("errorResultMsg").innerHTML = dataObj.msg;
			document.getElementById("errorResultDiv").style.display = "";
			document.getElementById("successResultDiv").style.display = "none";
			//document.getElementById("successResultMsg").innerHTML = "";
			/** /
			document.getElementById("errorDiv").innerHTML = dataObj.msg;
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("continueBtnDiv").style.display = "";
			document.getElementById("continueProgDiv").style.display = "none";
			/**/
			return false;
		} else if(dataObj.rsno==1) {
			var resetPwdBtn = $('#btnContinue');
			resetPwdBtn.button('reset');
			document.getElementById("emailDiv").style.display = "none";
			document.getElementById("errorResultDiv").style.display = "none";
			document.getElementById("errorResultMsg").innerHTML = "";
			//document.getElementById("successResultMsg").innerHTML = "";//Data is already there
			document.getElementById("successResultDiv").style.display = "";
			/** /
			document.getElementById("errorDiv").innerHTML = "";
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("continueBtnDiv").style.display = "none";
			document.getElementById("continueProgDiv").style.display = "none";
			document.getElementById("emailDiv").style.display = "none";
			document.getElementById("introHeaderDiv").style.display = "none";
			document.getElementById("finalMsgDiv").style.display = "";
			/**/
			return false;
		}
	} else 	if(dataObj.type==3) {
		if(dataObj.rsno==0) {
			var forgotPwdBtn = $('#btnForgotPwd');
			forgotPwdBtn.button('reset');
			alert(dataObj.msg);
			return false;
		} else if(dataObj.rsno==1) {
			var forgotPwdBtn = $('#btnForgotPwd');
			forgotPwdBtn.button('reset');
			alert(dataObj.msg);
			document.getElementById("emailLost").value = "";
			return false;
		}
	}

	return false;
}
