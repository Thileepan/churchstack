var doChurch = 'server/dochurch.php';


function listAllChurches(filterType)
{
	//var table = '<table id="listProfilesTable" class="table table-striped"><thead><tr><th>Member ID</th><th></th><th>Name</th><th>Date Of Birth</th><th>Age</th><th>Landline</th><th>Mobile Number</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('churchesList').innerHTML = document.getElementById("hidTableContentHTML").value;
	var formPostData = "";
	if(filterType==1) {
		formPostData += "req=1";
	} else if(filterType==3) {
		formPostData += "req=3";//trial churches alone
	} else if(filterType==4) {
		formPostData += "req=4";//trial expired churches alone
	} else if(filterType==5) {
		formPostData += "req=5";//License expired churches alone
	} else if(filterType==6) {
		formPostData += "req=6";//paid and active churches alone
	} else if(filterType==7) {
		formPostData += "req=11";//list deactivated churches alone
	} else {
		formPostData += "req=1";//to list all churches
	}
	document.getElementById("currListingType").value = filterType;
	oTable = $('#churchesTable').dataTable( {
        "bAutoWidth": true,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doChurch,
		"iDisplayLength":25,
		"word-wrap":"nowrap",
//		"aaSorting": 2,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": formPostData,
                "error": HandleAjaxError,
                "success": fnCallback
            } );
        }
	});
}

function loadChurchData(churchID)
{
	document.getElementById("churchDetailsBody").innerHTML = "Loading the data ...";
	var formPostData = "req=2";
	formPostData += "&ch_id="+churchID;
	$.ajax({
		type:'POST',
		url:doChurch,
		data:formPostData,
		success:showChurchDataFromResponse,
		error:HandleAjaxError
	});
	return false;
}

function showChurchDataFromResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno==0) {
		alert("Error : "+dataObj.rslt);
		return false;
	}
	document.getElementById("churchDetailsBody").innerHTML = dataObj.rslt;
	return false;
	//var profileID = dataObj.profileID;
}

function churchActions(actionType, churchID, churchName)
{
	var reqType = 7;//Open div to Extend Validity
	var daysToExtend = 0;
	if(actionType==1) {//open div Extend Validity
		reqType = 7;//open div Extend Validity
		document.getElementById("extendValidityFailureSpan").innerHTML = "";
		document.getElementById("extendValiditySuccessSpan").innerHTML = "";
		document.getElementById("extendValidityFailureSpan").style.display = "none";
		document.getElementById("extendValiditySuccessSpan").style.display = "none";
		document.getElementById("extendBtnSpan").style.display = "";
		document.getElementById("extendProgSpan").style.display = "none";
		document.getElementById("txtValidityDays").value = 30;//Default
		document.getElementById("txtChurchIDToExtend").value = churchID;
		document.getElementById("txtChurchNameToExtend").value = churchName;
		document.getElementById("spanChurchNameToExtend").innerHTML = churchName;
		return false;
	} else if(actionType==2) {
		reqType = 8;//Deactivate
		if(!confirm("Are you sure you want to DEACTIVATE the following church?\n\n"+churchName+" (ID : "+churchID+")" )) {
			return false;
		}
	} else if(actionType==3) {
		reqType = 9;//Re-activate
		if(!confirm("Are you sure you want to RE-ACTIVATE the following church?\n\n"+churchName+" (ID : "+churchID+")" )) {
			return false;
		}
	} else if(actionType==4) {
		reqType = 10;//Extend validity
		document.getElementById("extendValidityFailureSpan").innerHTML = "";
		document.getElementById("extendValiditySuccessSpan").innerHTML = "";
		document.getElementById("extendValidityFailureSpan").style.display = "none";
		document.getElementById("extendValiditySuccessSpan").style.display = "none";
		document.getElementById("extendBtnSpan").style.display = "none";
		document.getElementById("extendProgSpan").style.display = "";
		daysToExtend = document.getElementById("txtValidityDays").value;
		churchID = document.getElementById("txtChurchIDToExtend").value;
		churchName = document.getElementById("txtChurchIDToExtend").value;
		if(!confirm("Are you sure you want to extend the validity the following church?\n\n"+churchName+" (ID : "+churchID+")" )) {
			return false;
		}
	}
	var formPostData = "req="+reqType;
	formPostData += "&act_num="+actionType;
	formPostData += "&church_id="+churchID;
	formPostData += "&church_name="+churchName;
	formPostData += "&days_to_extend="+daysToExtend;
	$.ajax({
		type:'POST',
		url:doChurch,
		data:formPostData,
		success:respondChurchActions,
		error:HandleAjaxError
	});
	return false;
}

function respondChurchActions(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.actno == 2)
	{
		if(dataObj.rsno==0) {
			alert("Error : "+dataObj.msg);
			return false;
		} else {
			listAllChurches(document.getElementById("currListingType").value);
		}
	}
	else if(dataObj.actno == 3)
	{
		if(dataObj.rsno==0) {
			alert("Error : "+dataObj.msg);
			return false;
		} else {
			listAllChurches(document.getElementById("currListingType").value);
		}
	}
	else if(dataObj.actno == 4)
	{
		document.getElementById("extendValidityFailureSpan").innerHTML = "";
		document.getElementById("extendValiditySuccessSpan").innerHTML = "";
		document.getElementById("extendValidityFailureSpan").style.display = "none";
		document.getElementById("extendValiditySuccessSpan").style.display = "none";
		document.getElementById("extendBtnSpan").style.display = "none";
		document.getElementById("extendProgSpan").style.display = "";
		daysToExtend = document.getElementById("txtValidityDays").value;

		
		document.getElementById("extendProgSpan").style.display = "none";
		document.getElementById("extendBtnSpan").style.display = "";
		if(dataObj.rsno==0) {
			document.getElementById("extendValidityFailureSpan").innerHTML = dataObj.msg;
			document.getElementById("extendValidityFailureSpan").style.display = "";
			return false;
		} else {
			document.getElementById("extendValiditySuccessSpan").innerHTML = dataObj.msg;
			document.getElementById("extendValiditySuccessSpan").style.display = "";
			return false;
		}
	}
	return false;
	//var profileID = dataObj.profileID;
}
