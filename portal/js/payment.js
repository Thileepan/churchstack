var doPayment = 'server/dopayment.php';


function listAllPayments()
{
	document.getElementById('paymentsList').innerHTML = document.getElementById("hidTableContentHTML").value;
	oTable = $('#paymentsTable').dataTable( {
		/* * /
		"aoColumns": [
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
			{ "sWidth": "8%" },
			{ "sWidth": "8%" },
			{ "sWidth": "7%" },
			{ "sWidth": "7%" },
		],
		/**/
        "bAutoWidth": true,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doPayment,
		"iDisplayLength":25,
//		"aaSorting": 2,
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=1",
                "error": HandleAjaxError,
                "success": fnCallback
            } );
        }
	});
}

function loadPaymentData(invoiceID)
{
	document.getElementById("paymentDetailsBody").innerHTML = "Loading the data ...";
	var formPostData = "req=2";
	formPostData += "&inv_id="+invoiceID;
	$.ajax({
		type:'POST',
		url:doPayment,
		data:formPostData,
		success:showInvoiceDataFromResponse,
		error:HandleAjaxError
	});
	return false;
}

function showInvoiceDataFromResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno==0) {
		alert("Error : "+dataObj.rslt);
		return false;
	}
	document.getElementById("paymentDetailsBody").innerHTML = dataObj.rslt;
	return false;
	//var profileID = dataObj.profileID;
}

function paymentActions(actionType, invoiceID, emailID)
{
	var reqType = 3;//Open div
	if(actionType==1) {
		reqType = 3;//Open Div
		document.getElementById("emailFailureSpan").innerHTML = "";
		document.getElementById("emailSuccessSpan").innerHTML = "";
		document.getElementById("emailFailureSpan").style.display = "none";
		document.getElementById("emailSuccessSpan").style.display = "none";
		document.getElementById("sendEmailBtnSpan").style.display = "";
		document.getElementById("sendEmailProgSpan").style.display = "none";
		document.getElementById("txtEmailInvoice").value = emailID;
		document.getElementById("txtInvoiceIDToEmail").value = invoiceID;
		return false;
	} else if(actionType==2) {
		reqType = 4;//Email Invoice
		document.getElementById("emailFailureSpan").innerHTML = "";
		document.getElementById("emailSuccessSpan").innerHTML = "";
		document.getElementById("emailFailureSpan").style.display = "none";
		document.getElementById("emailSuccessSpan").style.display = "none";
		document.getElementById("sendEmailBtnSpan").style.display = "none";
		document.getElementById("sendEmailProgSpan").style.display = "";
	} else {
		return false;
	}
	/** /
	else if(actionType==2) {
		reqType = 10;//Reactivate
		if(!confirm("Are you sure you want to REACTIVATE the following coupon?\n\n"+couponCode)) {
			return false;
		}
	}
	/**/
	var formPostData = "req="+reqType;
	formPostData += "&act_num="+actionType;
	formPostData += "&invoice_id="+document.getElementById("txtInvoiceIDToEmail").value;
	formPostData += "&email="+document.getElementById("txtEmailInvoice").value;
	$.ajax({
		type:'POST',
		url:doPayment,
		data:formPostData,
		success:respondPaymentActions,
		error:HandleAjaxError
	});
	return false;
}

function respondPaymentActions(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.actno == 2)
	{
		document.getElementById("sendEmailProgSpan").style.display = "none";
		document.getElementById("sendEmailBtnSpan").style.display = "";
		if(dataObj.rsno==0) {
			document.getElementById("emailFailureSpan").innerHTML = dataObj.msg;
			document.getElementById("emailFailureSpan").style.display = "";
			return false;
		} else {
			document.getElementById("emailSuccessSpan").innerHTML = dataObj.msg;
			document.getElementById("emailSuccessSpan").style.display = "";
			return false;
		}
	}
	/** /
	else if(dataObj.actno == 2)
	{
		if(dataObj.rsno==0) {
			alert("Error : "+dataObj.msg);
			return false;
		} else {
			listAllCoupons(document.getElementById("currListingType").value);
		}
	}
	/**/
	return false;
	//var profileID = dataObj.profileID;
}
