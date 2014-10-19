<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	@include_once($APPLICATION_PATH."app/classes/class.church.php");
	@include_once($APPLICATION_PATH."app/classes/class.license.php");
	validateSession($APPLICATION_PATH);

	$page_id=5;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
	$submenu_id = (isset($_REQUEST["si"])? trim($_REQUEST["si"]) : 1);

	$church_id_list = array();
	$church_name_list = array();
	$church_country_list = array();
	$church_address_list = array();
	$church_sharded_db = array();
	$DELIMITER = "-:-";
	if($submenu_id==1)//Manual Invoice
	{
		$church_obj = new Church($APPLICATION_PATH."app/");
		$churches_res = $church_obj->getAllChurchesList(0);//List all churches
		if($churches_res[0]==1) {
			for($i=0; $i < COUNT($churches_res[1]); $i++)
			{
				//array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_update_time, $sharded_database, $currency_id, $unique_hash, $status, $country_id);
				$church_id_list[] = $churches_res[1][$i][0];
				$church_name_list[] = $churches_res[1][$i][1];
				$church_country_list[] = $churches_res[1][$i][14];
				$church_address_list[] = $churches_res[1][$i][3];
				$church_sharded_db[] = $churches_res[1][$i][10];
			}
		}
	}

	$all_license_plans = array();
	$lic_obj = new License($APPLICATION_PATH."app/");
	$lic_plans_res = $lic_obj->getAllLicensePlanDetails();
	if($lic_plans_res[0] == 1) {
		$all_license_plans = $lic_plans_res[1];
	}

?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/tools.js"></script>
	<div class="row-fluid">
		<div class="span2">
			<ul class="nav nav-pills nav-stacked">
				<li<?php echo (($submenu_id==1)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."portal/tools.php?si=1"; ?>">Manual Invoice</a></li>
				<li<?php echo (($submenu_id==2)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."portal/tools.php?si=2"; ?>">Calculate Service Tax</a></li>
			</ul>
		</div>

		<div class="span10" style="display:<?php echo (($submenu_id==1)? '': 'none'); ?>;">
			<div class="row-fluid" id="alertRow" style="display: none;">
				<div id="alertDiv" class="span12"></div>
			</div>
			<div class="row-fluid">
				<form name="invoiceForm" id="invoiceForm" class="form-horizontal" action="server/dotools.php" method="post" enctype="multipart/form-data" onsubmit="return false;">
					<div class="row-fluid">
						<div class="span12">
							<div style="padding-bottom:6px;"><label class="control-label" for="selInvoiceFor">Create Invoice For</label><div class="controls">
									<select id="selChurch" onchange="return loadChurchBasicData();">
										<?php
											echo '<option value="0" selected>Select Church</option>';
											for($c=0; $c < COUNT($church_id_list); $c++)
											{
												echo '<option value="'.$church_id_list[$c].'">'.$church_id_list[$c].' : '.$church_name_list[$c].', '.$church_address_list[$c].'</option>';
											}
										?>
									</select>
									<select id="selChurchName" style="display:none;">
										<?php
											echo '<option value="0" selected>Select Church</option>';
											for($c=0; $c < COUNT($church_name_list); $c++)
											{
												echo '<option value="'.$church_name_list[$c].'">'.$church_name_list[$c].'</option>';
											}
										?>
									</select>
									<select id="selChurchShardedDB" style="display:none;">
										<?php
											echo '<option value="0" selected>Select DB</option>';
											for($c=0; $c < COUNT($church_sharded_db); $c++)
											{
												echo '<option value="'.$church_sharded_db[$c].'">'.$church_sharded_db[$c].'</option>';
											}
										?>
									</select>
									<input type="hidden" id="shardedDB" value="">
									&nbsp;&nbsp;<button class="btn btn-primary" type="submit" onclick="return loadChurchBasicData();">Reload & Refill Data</button>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="alert alert-warning" role="alert" id="invWarningDiv" style="display:;"><B>READ THIS CAREFULLY:</B><BR/>
								<ul>
									<li>License will be applied IMMEDIATELY once invoice is updated thro' this form</li>
									<li>Calculation is NOT done automatically, you have to type everything manually</li>
									<li>An email will be sent immediately to the customer with the details of this invoice, so fillup all the fields carefully. Most of the details provided here will be seen by him</li>
									<li>Review the entire form thoroughly before you submit. If you do not know what to give in some fields, leave them as they are.</li>
									<li>If you have any doubts, contact Nesan/Thileepan!</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtBillName">Billing Name</label><div class="controls"><input type="text" id="txtBillName" placeholder="Billing Name" value=""></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtEmail">Email Address</label><div class="controls"><input type="email" id="txtEmail" placeholder="Email Address" value=""></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtBillingAddress">Billing Address</label><div class="controls"><textarea id="txtBillingAddress" placeholder="Billing Address"></textarea></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtInvoiceNotes">Invoice Notes</label><div class="controls"><textarea id="txtInvoiceNotes" placeholder="Invoice Notes"></textarea></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtTransactionID">Transaction ID</label><div class="controls"><input type="text" id="txtTransactionID" placeholder="Transaction ID" value=""></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtPurchaseRemarks">Purchase Remarks</label><div class="controls"><input type="text" id="txtPurchaseRemarks" placeholder="Purchase Remarks" value="Payment received & transaction is successful"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid" style="display:none;">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtPGStatusCode">PG Status Code</label><div class="controls"><input type="text" id="txtPGStatusCode" placeholder="PG Status Code" value="1"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtPGStatusRemarks">PG Status Remarks</label><div class="controls"><input type="text" id="txtPGStatusRemarks" placeholder="PG Status Remarks" value="--NA--"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid" style="display:none;">
						<div class="span12">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtOtherAddress">Shipping Address</label><div class="controls"><textarea id="txtOtherAddress" placeholder="Shipping Address"></textarea></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtPhoneNumber">Phone Number</label><div class="controls"><input type="text" id="txtPhoneNumber" placeholder="Phone Number" value=""></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="selCurrencyCode">Currency</label>
								<div class="controls">
									<select type="text" id="selCurrencyCode"><option value="USD">United States Dollars (USD)</option><option value="INR" selected>Indian Rupees (INR)</option></select>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div style="padding-bottom:6px;">
								<table border="1" style="border: solid 1px;" cellpadding="3">
									<tr style="background-color:grey;">
										<td style="width:10px;">Check</td>
										<td>Select Plan</td>
										<td>Plan Name</td>
										<td>Plan Description</td>
										<td>Plan Cost</td>
										<td>Quantity</td>
										<td>Total</td>
										<td>Auto Renew?</td>
									</tr>
									<tr>
										<td><input type="checkbox" id="chkPlan_1" value="1" checked></td>
										<td>
											<?php
												$plan_overview_select_box_options = "";
												$plan_id_select_box_options = "";
												$plan_name_select_box_options = "";
												$plan_desc_select_box_options = "";
												$plan_type_select_box_options = "";
												$plan_cost_select_box_options = "";
												$plan_validity_text_select_box_options = "";
												$plan_validity_seconds_select_box_options = "";
												for($p=0; $p < COUNT($all_license_plans); $p++)
												{
													$select_box_options .= '<option value="'.$all_license_plans[$p]["plan_id"].'">'.'ID:'.$all_license_plans[$p]["plan_id"].'; '.$all_license_plans[$p]['plan_name'].'</option>';
													$plan_id_select_box_options .= '<option value="'.$all_license_plans[$p]["plan_id"].'">'.$all_license_plans[$p]["plan_id"].'</option>';
													$plan_name_select_box_options .= '<option value="'.$all_license_plans[$p]["plan_name"].'">'.$all_license_plans[$p]["plan_name"].'</option>';
													$plan_desc_select_box_options .= '<option value="'.$all_license_plans[$p]["plan_description"].'">'.$all_license_plans[$p]["plan_description"].'</option>';
													$plan_type_select_box_options .= '<option value="'.$all_license_plans[$p]["plan_type"].'">'.$all_license_plans[$p]["plan_type"].'</option>';
													$plan_cost_select_box_options .= '<option value="'.$all_license_plans[$p]["pricing"].'">'.$all_license_plans[$p]["pricing"].'</option>';
													$plan_validity_text_select_box_options .= '<option value="'.$all_license_plans[$p]["validity_in_days"].'">'.$all_license_plans[$p]["validity_in_days"].' Days</option>';
													$plan_validity_seconds_select_box_options .= '<option value="'.$all_license_plans[$p]["validity_in_seconds"].'">'.$all_license_plans[$p]["validity_in_seconds"].'</option>';
												}
											?>
											<select id="selPlanId_1" onChange="selPlanOnChange(this);" style="width:280px;">
												<?php echo $select_box_options;?>
											</select>
											<select id="selPlanName_1" style="width:280px; display:none;">
												<?php echo $plan_name_select_box_options;?>
											</select>
											<select id="selPlanDesc_1" style="width:280px; display:none;">
												<?php echo $plan_desc_select_box_options;?>
											</select>
											<select id="selPlanType_1" style="width:280px; display:none;">
												<?php echo $plan_type_select_box_options;?>
											</select>
											<select id="selPlanCost_1" style="width:280px; display:none;">
												<?php echo $plan_cost_select_box_options;?>
											</select>
											<select id="selPlanValidityText_1" style="width:280px; display:none;">
												<?php echo $plan_validity_text_select_box_options;?>
											</select>
											<select id="selPlanValiditySeconds_1" style="width:280px; display:none;">
												<?php echo $plan_validity_seconds_select_box_options;?>
											</select>
										</td>
										<td><input type="text" id="txtPlanName_1" placeholder="Plan Name" value="" style="width:150px;"></td>
										<td><input type="text" id="txtPlanDesc_1" placeholder="Plan Description" value="" style="width:250px;"></td>
										<td><input type="text" id="txtPlanCost_1" placeholder="Plan Cost" value="0" style="width:60px;"></td>
										<td><input type="text" id="txtPlanQuantity_1" placeholder="Quantity" value="1" style="width:30px;"></td>
										<td><input type="text" id="txtPlanTotalCost_1" placeholder="Total Cost" value="0" style="width:60px;"></td>
										<td><input type="checkbox" id="chkAutoRenew_1" value="0"></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtSubtotal">Subtotal</label><div class="controls"><input type="text" id="txtSubtotal" placeholder="Subtotal" value="0"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtAddlCharge">Additional Charge</label><div class="controls"><input type="text" id="txtAddlCharge" placeholder="Additional Charge" value="0"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtDiscountAmount">Discount Amount</label><div class="controls"><input type="text" id="txtDiscountAmount" placeholder="Discount Amount" value="0"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtDiscountPerc">Discount Percentage</label><div class="controls"><input type="text" id="txtDiscountPerc" placeholder="Discount Percentage" value="0"> <b>%</b></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtServiceTaxAmount">Service Tax Amount</label><div class="controls"><input type="text" id="txtServiceTaxAmount" placeholder="Service Tax Amount" value="0"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtServiceTaxRate">Service Tax Rate</label><div class="controls"><input type="text" id="txtServiceTaxRate" placeholder="Service Tax Rate" value="12.36">%</div>
							</div>
						</div>
					</div>
					<div class="row-fluid" style="display:none;">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtTax2Rate">Tax-2 Rate</label><div class="controls"><input type="text" id="txtTax2Rate" placeholder="Tax-2 Rate" value="0">%</div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtTax2Amount">Tax-2 Amount</label><div class="controls"><input type="text" id="txtTax2Amount" placeholder="Tax-2 Amount" value="0"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid" style="display:none;">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtVatRate">VAT Rate</label><div class="controls"><input type="text" id="txtVatRate" placeholder="VAT Rate" value="0">%</div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtVatAmount">VAT Amount</label><div class="controls"><input type="text" id="txtVatAmount" placeholder="VAT Amount" value="0"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtNetAmount">Net Total (Amount Received)</label><div class="controls"><input type="text" id="txtNetAmount" placeholder="Net Total" value="0"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtVatAmount">VAT Amount</label><div class="controls"><input type="text" id="txtVatAmount" placeholder="VAT Amount" value="0"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtPaymentGateway">Payment Gateway</label><div class="controls"><input type="text" id="txtPaymentGateway" placeholder="Payment Gateway" value="--NA--"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtPaymentMode">Payment Mode</label><div class="controls"><input type="text" id="txtPaymentMode" placeholder="Payment Mode" value="Cash"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid" style="display:none;">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtCouponCode">Coupon Code</label><div class="controls"><input type="text" id="txtCouponCode" placeholder="Coupon Code" value=""></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtIPAddress">IP Address</label><div class="controls"><input type="text" id="txtIPAddress" placeholder="IP address" value="localhost"></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<!-- div class="form-actions"><button class="btn btn-primary" data-toggle="modal" data-target="#invoiceDetailsModal" type="submit" onclick="return previewInvoiceDetails();">Update Invoice & Apply License</button>&nbsp;<button class="btn" type="reset" id="resetBtn">Reset Form</button></div -->
							<div class="form-actions"><button class="btn btn-primary" type="submit" onclick="return previewInvoiceDetails();">Update Invoice & Apply License</button>&nbsp;<button class="btn" type="reset" id="resetBtn">Reset Form</button></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<!-- Button trigger modal -->
<!--button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#churchDetailsModal">
  Launch demo modal
</button -->

<!-- Modal -->
<div class="bigModal fade" id="invoiceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><!-- span class="sr-only">Close</span--></button>
        <h4 class="modal-title" id="invoiceDetailsModalLabel">Review the detail once again before moving forward</h4>
      </div>
      <div class="modal-body" id="invoiceDetailsBody">
		<div class="row-fluid">
			<div class="span6">Church Name : <span id="popupChurchName"></span></div>
			<div class="span6">Church ID : <span id="popupChurchID"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Billing Name : <span id="popupBillingName"></span></div>
			<div class="span6">Email Address : <span id="popupEmailAddress"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Billing Address : <span id="popupBillingAddress"></span></div>
			<div class="span6">Invoice Notes : <span id="popupInvoiceNotes"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Transaction ID : <span id="popupTransactionID"></span></div>
			<div class="span6">Purchase Remarks : <span id="popupRemarks"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Phone Number : <span id="popupPhone"></span></div>
			<div class="span6">Currency Code : <span id="popupCurrencyCode"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Phone Number : <span id="popupPhone"></span></div>
			<div class="span6">Currency Code : <span id="popupCurrencyCode"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<div style="padding-bottom:6px;">
					<table border="1" style="border: solid 1px;" cellpadding="3">
						<tr style="background-color:grey;">
							<td style="width:10px;">S.no</td>
							<td>Select Plan</td>
							<td>Plan Name</td>
							<td>Plan Description</td>
							<td>Plan Cost</td>
							<td>Quantity</td>
							<td>Total</td>
							<td>Auto Renew?</td>
						</tr>
						<tr>
							<td><span>1</span></td>
							<td><span id="popupPlanID_1"></span></td>
							<td><span id="popupPlanName_1"></span></td>
							<td><span id="popupPlanDesc_1"></span></td>
							<td><span id="popupPlanCost_1"></span></td>
							<td><span id="popupPlanQuantity_1"></span></td>
							<td><span id="popupPlanTotalCost_1"></span></td>
							<td><span id="popupAutoRenew_1"></span></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">Subtotal : <span id="popupSubtotal"></span></div>
			<div class="span6">Additional Charge : <span id="popupAddlCharge"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Discount Amount : <span id="popupDiscountAmount"></span></div>
			<div class="span6">Discount Percentage : <span id="popupDiscountPercentage"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Service Tax Amount : <span id="popupSTAmount"></span></div>
			<div class="span6">Service Tax Rate : <span id="popupSTRate"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Net Total (Amount Received) : <span id="popupNetTotal"></span></div>
			<div class="span6">VAT Amount : <span id="popupVATAmount"></span></div>
		</div>
		<div class="row-fluid">
			<div class="span6">Payment Gateway : <span id="popupPGateway"></span></div>
			<div class="span6">Payment Mode : <span id="popupPMode"></span></div>
		</div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updateInvoiceAndApplyLicense();">Update Invoice & Apply License</button>
      </div>
    </div>
  </div>
</div>
	<form name="invoice_form" id="invoice_form" method="post">
		<input type="hidden" id="hidTableContentHTML" name="hidTableContentHTML" value="<?php echo htmlentities($table_html); ?>"/>
		<input type="hidden" id="currListingType" name="currListingType" value="1">
	<form>

	<script type="text/javascript">
		$('#invoiceDetailsModal').modal({ show: false})
		selPlanOnChange(document.getElementById("selPlanId_1"));
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>