var doCoupon = 'server/docoupon.php';


function listAllCoupons(filterType)
{
	//var table = '<table id="listProfilesTable" class="table table-striped"><thead><tr><th>Member ID</th><th></th><th>Name</th><th>Date Of Birth</th><th>Age</th><th>Landline</th><th>Mobile Number</th><th>Actions</th></tr></thead><tbody></tbody></table>';		
	document.getElementById('couponsList').innerHTML = document.getElementById("hidTableContentHTML").value;
	var formPostData = "";
	if(filterType==1) {
		formPostData += "req=1";//list all coupons
	} else if(filterType==3) {
		formPostData += "req=3";
	} else if(filterType==4) {
		formPostData += "req=4";
	} else if(filterType==5) {
		formPostData += "req=5";
	} else if(filterType==6) {
		formPostData += "req=6";
	} else if(filterType==7) {
		formPostData += "req=7";
	} else {
		formPostData += "req=1";
	}

	document.getElementById("currListingType").value = filterType;

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

function generateCoupon()
{
	document.getElementById("couponInfoDiv").innerHTML = "";
	document.getElementById("couponInfoDiv").style.display = "none";

	if(trim(document.getElementById("txtDiscountPerc").value) == "") {
		document.getElementById("txtDiscountPerc").value = 0;
	}
	if(trim(document.getElementById("txtDiscountFlat").value) == "") {
		document.getElementById("txtDiscountFlat").value = 0;
	}
	if(trim(document.getElementById("txtMinSubtotal").value) == "") {
		document.getElementById("txtMinSubtotal").value = 0;
	}
	if(trim(document.getElementById("txtValidTill").value) == "") {
		alert("Choose an expiry date for the coupon");
		return false;
	}

	
	var customCouponCode = "";
	document.getElementById("txtCustomCouponCode").value = trim(document.getElementById("txtCustomCouponCode").value);
	if(document.getElementById("txtCustomCouponCode").value != "") {
		customCouponCode = document.getElementById("txtCustomCouponCode").value;
	}

	var church_id = ((document.getElementById("selCouponFor").value==0)? document.getElementById("selChurch").value : 0);

	var formPostData = "req=8";
	formPostData += "&is_valid_for_all="+((document.getElementById("selCouponFor").value==1)? 1 : 0);
	formPostData += "&ch_id="+church_id;
	formPostData += "&discount_perc="+document.getElementById("txtDiscountPerc").value;
	formPostData += "&discount_flat_amt="+document.getElementById("txtDiscountFlat").value;
	formPostData += "&minimum_subtotal="+document.getElementById("txtMinSubtotal").value;
	formPostData += "&valid_till="+document.getElementById("txtValidTill").value;
	formPostData += "&custom_coupon_code="+customCouponCode;
	$.ajax({
		type:'POST',
		url:doCoupon,
		data:formPostData,
		success:showGeneratedCouponFromResponse,
		error:HandleAjaxError
	});
	return false;
}

function showGeneratedCouponFromResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno==0) {
		alert("Error : "+dataObj.msg);
		return false;
	}
	document.getElementById("couponInfoDiv").innerHTML = dataObj.rslt;
	document.getElementById("couponInfoDiv").style.display = "";
	document.getElementById("resetBtn").click();
	return false;
	//var profileID = dataObj.profileID;
}

function couponActions(actionType, couponID, couponCode)
{
	var reqType = 9;//Terminate
	if(actionType==1) {
		reqType = 9;//Terminate
		if(!confirm("Are you sure you want to TERMINATE the following coupon?\n\n"+couponCode)) {
			return false;
		}
	}
	else if(actionType==2) {
		reqType = 10;//Reactivate
		if(!confirm("Are you sure you want to REACTIVATE the following coupon?\n\n"+couponCode)) {
			return false;
		}
	}
	var formPostData = "req="+reqType;
	formPostData += "&act_num="+actionType;
	formPostData += "&coupon_id="+couponID;
	$.ajax({
		type:'POST',
		url:doCoupon,
		data:formPostData,
		success:respondCouponActions,
		error:HandleAjaxError
	});
	return false;
}

function respondCouponActions(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.actno == 1)
	{
		if(dataObj.rsno==0) {
			alert("Error : "+dataObj.msg);
			return false;
		} else {
			listAllCoupons(document.getElementById("currListingType").value);
		}
	}
	else if(dataObj.actno == 2)
	{
		if(dataObj.rsno==0) {
			alert("Error : "+dataObj.msg);
			return false;
		} else {
			listAllCoupons(document.getElementById("currListingType").value);
		}
	}
	return false;
	//var profileID = dataObj.profileID;
}
