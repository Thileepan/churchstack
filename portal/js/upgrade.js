var doUpgrade = 'server/doupgrade.php';

function runUpgrade()
{
	document.getElementById("upgradeInfoDiv").innerHTML = "";
	document.getElementById("upgradeInfoDiv").style.display = "none";

	if(!confirm("Are you sure you want to upgrade the above churches?")) {
		return false;
	}

	if(!confirm("Have you made sure the upgrade SQL file is perfect to do an upgrade? Was it well tested manually? Shall we proceed without any more questions?")) {
		return false;
	}

	document.getElementById("btnUpgrade").style.display = "none";
	document.getElementById("progText").style.display = "";
	var formPostData = "req=1";
	formPostData += "&upgradeFile="+escString(document.getElementById("txtUpgradeFilePath").value);
	$.ajax({
		type:'POST',
		url:doUpgrade,
		data:formPostData,
		success:runUpgradeResponse,
		error:HandleAjaxError
	});
	return false;
}

function runUpgradeResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	document.getElementById("btnUpgrade").style.display = "";
	document.getElementById("progText").style.display = "none";
	if(dataObj.rsno==0) {
		alert("Error : "+dataObj.msg);
		return false;
	}
	document.getElementById("upgradeInfoDiv").innerHTML = dataObj.rslt;
	document.getElementById("upgradeInfoDiv").style.display = "";
	return false;
}
