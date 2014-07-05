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
		"iDisplayLength":2,
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
