//global variables
doTools = 'server/dotools.php';

function loadChurchBasicData()
{
	var churchID = document.getElementById("selChurch").value;
	document.invoiceForm.reset();
	selPlanOnChange(document.getElementById("selPlanId_1"));
	document.getElementById("selChurch").value = churchID;
	document.getElementById("selChurchName").selectedIndex = document.getElementById("selChurch").selectedIndex;
	document.getElementById("selChurchShardedDB").selectedIndex = document.getElementById("selChurch").selectedIndex;
	document.getElementById("shardedDB").value = document.getElementById("selChurchShardedDB").value;
	
	if(churchID <= 0) {
		alert("Select a church for which license has to be applied");
		return false;
	}
	var formPostData = "";
	formPostData += "req=1";
	formPostData += "&churchID="+churchID;

	$.ajax({
		type:'POST',
		url:doTools,
		data:formPostData,
		success:loadChurchBasicDataResponse,
		error:HandleAjaxError
	});
	return false;
}

function loadChurchBasicDataResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno==0) {
		alert(dataObj.msg);
		return false;
	} else if(dataObj.rsno==1) {
		document.getElementById("txtBillName").value = dataObj.user_data.full_name;
		document.getElementById("txtEmail").value = dataObj.user_data.email;
		document.getElementById("txtBillingAddress").value = dataObj.church_data.address;
		document.getElementById("txtPhoneNumber").value = dataObj.church_data.phone;
		document.getElementById("txtTransactionID").value = dataObj.randTransactionID;
		return false;
	}
	return false;
}

function selPlanOnChange(srcElem)
{
	var currSelIndex = srcElem.selectedIndex;
	document.getElementById("selPlanName_1").selectedIndex = currSelIndex;
	document.getElementById("selPlanDesc_1").selectedIndex = currSelIndex;
	document.getElementById("selPlanType_1").selectedIndex = currSelIndex;
	document.getElementById("selPlanCost_1").selectedIndex = currSelIndex;
	document.getElementById("selPlanValidityText_1").selectedIndex = currSelIndex;
	document.getElementById("selPlanValiditySeconds_1").selectedIndex = currSelIndex;

	document.getElementById("txtPlanName_1").value = document.getElementById("selPlanName_1").value;
	document.getElementById("txtPlanDesc_1").value = document.getElementById("selPlanDesc_1").value;
	document.getElementById("txtPlanCost_1").value = document.getElementById("selPlanCost_1").value;
//	document.getElementById("txtPlanQuantity_1").value = 1;

	document.getElementById("txtPlanTotalCost_1").value = document.getElementById("txtPlanCost_1").value * document.getElementById("txtPlanQuantity_1").value;
}

function previewInvoiceDetails()
{

	$('#invoiceDetailsModal').modal({ show: false})
	if(document.getElementById("selChurch").selectedIndex == 0) {
		alert("Select the church to update invoice");
		document.getElementById("selChurch").focus();
		return false;
	}
	if(trim(document.getElementById("txtBillName").value) == "") {
		alert("Billing Name cannot be empty");
		document.getElementById("txtBillName").focus();
		return false;
	}
	if(trim(document.getElementById("txtEmail").value) == "") {
		alert("Email cannot be empty");
		document.getElementById("txtEmail").focus();
		return false;
	}
	if(trim(document.getElementById("txtBillingAddress").value) == "") {
		alert("Billing Address cannot be empty");
		document.getElementById("txtBillingAddress").focus();
		return false;
	}
	if(trim(document.getElementById("txtTransactionID").value) == "") {
		alert("Transaction ID cannot be empty, type something");
		document.getElementById("txtTransactionID").focus();
		return false;
	}
	if(trim(document.getElementById("txtPhoneNumber").value) == "") {
		alert("Phone number cannot be empty, type something");
		document.getElementById("txtPhoneNumber").focus();
		return false;
	}
	if(!document.getElementById("chkPlan_1").checked) {
		alert("At least one product has to be selected to generate invoice");
		return false;
	}
	if(trim(document.getElementById("txtPlanName_1").value) == "") {
		alert("Product/Plan Name cannot be empty, type something");
		document.getElementById("txtPlanName_1").focus();
		return false;
	}
	if(trim(document.getElementById("txtPlanDesc_1").value) == "") {
		alert("Product/Plan Description cannot be empty, type something");
		document.getElementById("txtPlanDesc_1").focus();
		return false;
	}
	if(trim(document.getElementById("txtPlanCost_1").value) == "") {
		alert("Product/Plan Cost cannot be empty, type something");
		document.getElementById("txtPlanCost_1").focus();
		return false;
	}
	if(trim(document.getElementById("txtPlanQuantity_1").value) == "") {
		alert("Product/Plan Quantity cannot be empty, type something");
		document.getElementById("txtPlanQuantity_1").focus();
		return false;
	}
	if(trim(document.getElementById("txtPlanTotalCost_1").value) == "") {
		alert("Product/Plan total cost cannot be empty, type something");
		document.getElementById("txtPlanTotalCost_1").focus();
		return false;
	}
	if(trim(document.getElementById("txtSubtotal").value) == "") {
		alert("Suubtotal cannot be zero, type something");
		document.getElementById("txtSubtotal").focus();
		return false;
	}
	if(trim(document.getElementById("txtSubtotal").value) == "") {
		alert("Suubtotal cannot be zero, type something");
		document.getElementById("txtSubtotal").focus();
		return false;
	}

	$('#invoiceDetailsModal').modal('show');
	
	document.getElementById("popupChurchName").innerHTML = document.getElementById("selChurchName").value;
	document.getElementById("popupChurchID").innerHTML = document.getElementById("selChurch").value;
	document.getElementById("popupBillingName").innerHTML = document.getElementById("txtBillName").value;
	document.getElementById("popupEmailAddress").innerHTML = document.getElementById("txtEmail").value;
	document.getElementById("popupBillingAddress").innerHTML = document.getElementById("txtBillingAddress").value;
	document.getElementById("popupInvoiceNotes").innerHTML = document.getElementById("txtInvoiceNotes").value;
	document.getElementById("popupTransactionID").innerHTML = document.getElementById("txtTransactionID").value;
	document.getElementById("popupRemarks").innerHTML = document.getElementById("txtPurchaseRemarks").value;
	document.getElementById("popupPhone").innerHTML = document.getElementById("txtPhoneNumber").value;
	document.getElementById("popupCurrencyCode").innerHTML = document.getElementById("selCurrencyCode").value;
	document.getElementById("popupPlanID_1").innerHTML = document.getElementById("selPlanId_1").value;
	document.getElementById("popupPlanName_1").innerHTML = document.getElementById("txtPlanName_1").value;
	document.getElementById("popupPlanDesc_1").innerHTML = document.getElementById("txtPlanDesc_1").value;
	document.getElementById("popupPlanCost_1").innerHTML = document.getElementById("txtPlanCost_1").value;
	document.getElementById("popupPlanQuantity_1").innerHTML = document.getElementById("txtPlanQuantity_1").value;
	document.getElementById("popupPlanTotalCost_1").innerHTML = document.getElementById("txtPlanTotalCost_1").value;
	document.getElementById("popupAutoRenew_1").innerHTML = ((document.getElementById("chkAutoRenew_1").checked)? "Yes" : "No");
	document.getElementById("popupSubtotal").innerHTML = document.getElementById("txtSubtotal").value;
	document.getElementById("popupAddlCharge").innerHTML = document.getElementById("txtAddlCharge").value;
	document.getElementById("popupDiscountAmount").innerHTML = document.getElementById("txtDiscountAmount").value;
	document.getElementById("popupDiscountPercentage").innerHTML = document.getElementById("txtDiscountPerc").value;
	document.getElementById("popupSTAmount").innerHTML = document.getElementById("txtServiceTaxAmount").value;
	document.getElementById("popupSTRate").innerHTML = document.getElementById("txtServiceTaxRate").value;
	document.getElementById("popupNetTotal").innerHTML = document.getElementById("txtNetAmount").value;
	document.getElementById("popupVATAmount").innerHTML = document.getElementById("txtVatAmount").value;
	document.getElementById("popupPGateway").innerHTML = document.getElementById("txtPaymentGateway").value;
	document.getElementById("popupPMode").innerHTML = document.getElementById("txtPaymentMode").value;
	
	return false;
}

function updateInvoiceAndApplyLicense()
{
	if(!confirm("Are you sure you want to update invoice and apply license for this church?\n\nAre all the values correct? THIS UPDATE IS VERY CRITICAL AND CANNOT BE UNDONE!")) {
		return false;
	}
	var formPostData = "";
	formPostData += "req=2";
	formPostData += "&church_id="+escString(document.getElementById("selChurch").value);
	formPostData += "&sharded_db="+escString(document.getElementById("shardedDB").value);
	formPostData += "&billing_full_name="+escString(document.getElementById("txtBillName").value);
	formPostData += "&billing_address="+escString(document.getElementById("txtBillingAddress").value);
	formPostData += "&other_address="+escString(document.getElementById("txtOtherAddress").value);
	formPostData += "&phone="+escString(document.getElementById("txtPhoneNumber").value);
	formPostData += "&currency_code="+escString(document.getElementById("selCurrencyCode").value);
	formPostData += "&subtotal="+escString(document.getElementById("txtSubtotal").value);
	formPostData += "&additional_charge="+escString(document.getElementById("txtAddlCharge").value);
	formPostData += "&discount_percentage="+escString(document.getElementById("txtDiscountPerc").value);
	formPostData += "&discount_amount="+escString(document.getElementById("txtDiscountAmount").value);
	formPostData += "&tax_percentage="+escString(document.getElementById("txtServiceTaxRate").value);
	formPostData += "&tax_amount="+escString(document.getElementById("txtServiceTaxAmount").value);
	formPostData += "&tax_2_percentage="+escString(document.getElementById("txtTax2Rate").value);
	formPostData += "&tax_2_amount="+escString(document.getElementById("txtTax2Amount").value);
	formPostData += "&vat_percentage="+"0";
	formPostData += "&vat_amount="+escString(document.getElementById("txtVatAmount").value);
	formPostData += "&net_total="+escString(document.getElementById("txtNetAmount").value);
	formPostData += "&coupon_code="+escString(document.getElementById("txtCouponCode").value);
	formPostData += "&invoice_notes="+escString(document.getElementById("txtInvoiceNotes").value);
	formPostData += "&payment_gateway="+escString(document.getElementById("txtPaymentGateway").value);
	formPostData += "&payment_mode="+escString(document.getElementById("txtPaymentMode").value);
	formPostData += "&ip_address="+escString(document.getElementById("txtIPAddress").value);
	formPostData += "&email="+escString(document.getElementById("txtEmail").value);
	formPostData += "&plan_id="+escString(document.getElementById("selPlanId_1").value);
	formPostData += "&plan_name="+escString(document.getElementById("txtPlanName_1").value);
	formPostData += "&plan_desc="+escString(document.getElementById("txtPlanDesc_1").value);
	formPostData += "&plan_type="+escString(document.getElementById("selPlanType_1").value);
	formPostData += "&validity_period_text="+escString(document.getElementById("selPlanValidityText_1").value);
	formPostData += "&validity_in_seconds="+escString(document.getElementById("selPlanValiditySeconds_1").value);
	formPostData += "&plan_cost="+escString(document.getElementById("txtPlanCost_1").value);
	formPostData += "&quantity="+escString(document.getElementById("txtPlanQuantity_1").value);
	formPostData += "&total_cost="+escString(document.getElementById("txtPlanTotalCost_1").value);
	formPostData += "&is_autorenewal_enabled="+((document.getElementById("chkAutoRenew_1").checked)? 1 : 0);
	formPostData += "&transaction_id="+escString(document.getElementById("txtTransactionID").value);

	formPostData += "&payment_gateway="+escString(document.getElementById("txtPaymentGateway").value);
	formPostData += "&payment_mode="+escString(document.getElementById("txtPaymentMode").value);
	formPostData += "&pg_status_code="+escString(document.getElementById("txtPGStatusCode").value);
	formPostData += "&pg_status_remarks="+escString(document.getElementById("txtPGStatusRemarks").value);
	formPostData += "&purchase_status_code="+1;
	formPostData += "&purchase_status_remarks="+escString(document.getElementById("txtPurchaseRemarks").value);

	$.ajax({
		type:'POST',
		url:doTools,
		data:formPostData,
		success:updateInvoiceAndApplyLicenseResponse,
		error:HandleAjaxError
	});
	return false;
}

function updateInvoiceAndApplyLicenseResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	if(dataObj.rsno==0) {
		alert(dataObj.msg);
		return false;
	} else if(dataObj.rsno==1) {
		alert(dataObj.msg);
		window.location.href = 'tools.php';
		return false;
	}
	return false;
}
