var doLogin = 'server/dologin.php';

function validateLogin(type)
{
	document.getElementById("errorDiv").innerHTML = "";
	document.getElementById("successDiv").innerHTML = "";

	if(type==1)
	{
		document.getElementById("loginBtnDiv").style.display = "none";
		document.getElementById("loginProgDiv").style.display = "";
	}
	else if(type==2)
	{
		document.getElementById("accessBtnDiv").style.display = "none";
		document.getElementById("loginProgDiv").style.display = "";
	}
	var formPostData = "";
	if(type==1) {
		formPostData += "req=1";
		formPostData += "&loginPwd="+document.getElementById("loginPwd").value;
	} else if(type==2) {
		formPostData += "req=2";
		formPostData += "&accessPwd="+document.getElementById("accessPwd").value;
	}
	$.ajax({
		type:'POST',
		url:doLogin,
		data:formPostData,
		success:processLoginFromResponse,
		error:HandleAjaxError
	});
	return false;
}

function processLoginFromResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.type==1) {
		if(dataObj.rsno==0) {
			document.getElementById("errorDiv").innerHTML = dataObj.msg;
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("loginBtnDiv").style.display = "";
			document.getElementById("loginProgDiv").style.display = "none";
			document.getElementById("loginPwd").value = "";
			document.getElementById("loginPwd").focus();
			return false;
		} else if(dataObj.rsno==1) {
			document.getElementById("errorDiv").innerHTML = "";
			document.getElementById("successDiv").innerHTML = dataObj.msg;
			document.getElementById("loginPwdText").style.display = "none";
			document.getElementById("accessPwdText").style.display = "";
			document.getElementById("loginBtnDiv").style.display = "none";
			document.getElementById("accessBtnDiv").style.display = "";
			document.getElementById("loginProgDiv").style.display = "none";
			document.getElementById("accessPwd").value = "";
			document.getElementById("accessPwd").focus();
			return false;
		}
	} else if(dataObj.type==2) {
		if(dataObj.rsno==0) {
			document.getElementById("errorDiv").innerHTML = dataObj.msg;
			document.getElementById("successDiv").innerHTML = "";
			document.getElementById("accessBtnDiv").style.display = "";
			document.getElementById("loginProgDiv").style.display = "none";
			document.getElementById("accessPwd").value = "";
			document.getElementById("accessPwd").focus();
			return false;
		} else if(dataObj.rsno==1) {
			window.location = "church.php";
			/** /
			document.getElementById("errorDiv").innerHTML = "";
			document.getElementById("successDiv").innerHTML = dataObj.msg;
			document.getElementById("loginBtnDiv").style.display = "none";
			document.getElementById("accessBtnDiv").style.display = "";
			document.getElementById("loginProgDiv").style.display = "";
			return false;
			/**/
		}
	}
	return false;
}
