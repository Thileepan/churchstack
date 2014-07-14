var doCoupon = 'server/docoupon.php';


function listAllCoupons(filterType)
{
	//var table = '<table id="listProfilesTable" class="table table-striped"><thead><tr><th>Member ID</th><th></th><th>Name</th><th>Date Of Birth</th><th>Age</th><th>Landline</th><th>Mobile Number</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('couponsList').innerHTML = document.getElementById("hidTableContentHTML").value;
	var formPostData = "";
	if(filterType==1) {
		formPostData += "req=1";//list all coupons
	} else if(filterType==3) {
		formPostData += "req=3";//list all coupons
	} else if(filterType==4) {
		formPostData += "req=4";//list all coupons
	} else if(filterType==5) {
		formPostData += "req=5";//list all coupons
	} else if(filterType==6) {
		formPostData += "req=6";//list all coupons
	} else if(filterType==7) {
		formPostData += "req=7";//list all coupons
	} else {
		formPostData += "req=1";
	}

	oTable = $('#couponsTable').dataTable( {
        "bAutoWidth": true,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doCoupon,
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

function loadCouponData(couponID)
{
	document.getElementById("couponDetailsBody").innerHTML = "Loading the data ...";
	var formPostData = "req=2";
	formPostData += "&coupon_id="+couponID;
	$.ajax({
		type:'POST',
		url:doCoupon,
		data:formPostData,
		success:showCouponDataFromResponse,
		error:HandleAjaxError
	});
	return false;
}

function showCouponDataFromResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno==0) {
		alert("Error : "+dataObj.rslt);
		return false;
	}
	document.getElementById("couponDetailsBody").innerHTML = dataObj.rslt;
	return false;
	//var profileID = dataObj.profileID;
}
