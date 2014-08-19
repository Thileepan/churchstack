//global variables
doUsers = '../server/dousers';

function forgotPassword(type)
{
	document.getElementById("errorDiv").innerHTML = "";
	document.getElementById("successDiv").innerHTML = "";

	if(type==1)
	{
		document.getElementById("txtEmail").value = trim(document.getElementById("txtEmail").value);
		if(document.getElementById("txtEmail").value == "") {
			return false;
		}
		document.getElementById("continueBtnDiv").style.display = "none";
		document.getElementById("continueProgDiv").style.display = "";
	}
	else if(type==2)
	{
		document.getElementById("txtEmail").value = trim(document.getElementById("txtEmail").value);
		document.getElementById("txtPassword").value = trim(document.getElementById("txtPassword").value);
		document.getElementById("txtConfirmPassword").value = trim(document.getElementById("txtConfirmPassword").value);
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
			document.getElementById("errorDiv").innerHTML = dataObj.msg;
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("continueBtnDiv").style.display = "";
			document.getElementById("continueProgDiv").style.display = "none";
			document.getElementById("txtEmail").focus();
			return false;
		} else if(dataObj.rsno==1) {
			document.getElementById("errorDiv").innerHTML = "";
			document.getElementById("successDiv").innerHTML = dataObj.msg;
			document.getElementById("continueBtnDiv").style.display = "none";
			document.getElementById("continueProgDiv").style.display = "none";
			document.getElementById("emailDiv").style.display = "none";
			return false;
		}
	} else 	if(dataObj.type==2) {
		if(dataObj.rsno==0) {
			document.getElementById("errorDiv").innerHTML = dataObj.msg;
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("continueBtnDiv").style.display = "";
			document.getElementById("continueProgDiv").style.display = "none";
			return false;
		} else if(dataObj.rsno==1) {
			document.getElementById("errorDiv").innerHTML = "";
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("continueBtnDiv").style.display = "none";
			document.getElementById("continueProgDiv").style.display = "none";
			document.getElementById("emailDiv").style.display = "none";
			document.getElementById("introHeaderDiv").style.display = "none";
			document.getElementById("finalMsgDiv").style.display = "";
			return false;
		}
	}

	return false;
}
