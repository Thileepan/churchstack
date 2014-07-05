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
	} else {
		formPostData += "req=1";//to list all churches
	}
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
