<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	@include_once($APPLICATION_PATH."app/classes/class.church.php");
	validateSession($APPLICATION_PATH);

	$page_id=4;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
	$submenu_id = (isset($_REQUEST["si"])? trim($_REQUEST["si"]) : 1);

	$church_id_list = array();
	$church_name_list = array();
	$church_country_list = array();
	$DELIMITER = "-:-";
	if($submenu_id==2)//Generate Coupon
	{
		$church_obj = new Church($APPLICATION_PATH."app/");
		$churches_res = $church_obj->getAllChurchesList(0);//List all churches
		if($churches_res[0]==1) {
			for($i=0; $i < COUNT($churches_res[1]); $i++)
			{
				array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_update_time, $sharded_database, $currency_id, $unique_hash, $status, $country_id);
				$church_id_list[] = $churches_res[1][$i][0];
				$church_name_list[] = $churches_res[1][$i][1];
				$church_country_list[] = $churches_res[1][$i][14];
			}
		}
	}
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/coupon.js"></script>
<script>
	function couponForOnChange()
	{
		if(document.getElementById("selCouponFor").value == 1) {
			document.getElementById("selChurchDiv").style.display = "none";
		} else if (document.getElementById("selCouponFor").value == 0) {
			document.getElementById("selChurchDiv").style.display = "";
		}
	}
</script>
	<div class="row-fluid">
		<div class="span2">
			<ul class="nav nav-pills nav-stacked">
				<li<?php echo (($submenu_id==1)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."portal/coupon.php?si=1"; ?>">List Coupons</a></li>
				<li<?php echo (($submenu_id==2)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."portal/coupon.php?si=2"; ?>">Generate Coupons</a></li>
			</ul>
		</div>

		<div class="span10" style="display:<?php echo (($submenu_id==2)? '': 'none'); ?>;">
			<div class="row-fluid" id="alertRow" style="display: none;">
				<div id="alertDiv" class="span12"></div>
			</div>
			<div class="row-fluid">
				<form id="couponForm" class="form-horizontal" action="server/docoupon.php" method="post" enctype="multipart/form-data" onsubmit="return false;">
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="selCouponFor">Coupon For</label><div class="controls"><select id="selCouponFor" onchange="Javascript: couponForOnChange();"><option value="0">A Church</option><option value="1">All Churches</option></select></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;" id="selChurchDiv"><label class="control-label" for="selChurch">Select Church</label>
								<div class="controls">
									<select id="selChurch">
										<?php
											for($c=0; $c < COUNT($church_id_list); $c++)
											{
												echo '<option value="'.$church_id_list[$c].'">'.$church_id_list[$c].' : '.$church_name_list[$c].', '.$church_country_list[$c].'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtDiscountPerc">Discount Percentage</label><div class="controls"><input type="text" id="txtDiscountPerc" placeholder="Discount Percentage" value=""> %</div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtDiscountFlat">Discount Flat Amt (USD)</label><div class="controls"><input type="text" id="txtDiscountFlat" placeholder="Discount Flat Amount" value=""></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtMinSubtotal">Minimum Subtotal (USD)</label><div class="controls"><input type="text" id="txtMinSubtotal" placeholder="Minimum Subtotal" value="0"></div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtValidTill">Valid Till</label><div class="controls"><input type="text" id="txtValidTill" data-date-format="dd/mm/yyyy" placeholder="Valid Till" value=""></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div style="padding-bottom:6px;">
								<label class="control-label" for="txtCustomCouponCode" style="color:#F41A12;">Use This Coupon Code</label><div class="controls"><input type="text" id="txtCustomCouponCode" placeholder="Enter Custom Coupon Code" value="" style="color:#F41A12; font-weight:bold;">&nbsp;&nbsp;&nbsp;<span style="color:#F41A12;">Leave this empty if you want to generate a random code</span></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="form-actions"><button class="btn btn-primary" type="submit" onclick="return generateCoupon();">Generate Coupon</button>&nbsp;<button class="btn" type="reset" id="resetBtn" onclick="Javascript: document.getElementById('selChurchDiv').style.display='';">Reset</button></div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="alert alert-info" role="alert" id="couponInfoDiv" style="display:none;"></div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="span10" style="display:<?php echo (($submenu_id==1)? '': 'none'); ?>;">
			<div class="row-fluid">
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(1);">List All</button>
					</div>
					<div class="btn-group">
						<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(3);">Active</button>
						<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(5);">Used</button>
						<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(4);">Expired</button>
						<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(6);">Active (Global)</button>
						<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(7);">Expired (Global)</button>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div id="couponsList" class="span12">
					<?php
					$table_html = '<table id="couponsTable" class="table table-striped">';
						$table_html .= '<thead>';
							$table_html .= '<tr>';
								$table_html .= '<th>Coupon ID</th>';
								$table_html .= '<th>Coupon Code</th>';
								$table_html .= '<th>Church ID</th>';
								$table_html .= '<th>Discount Perc.</th>';
								$table_html .= '<th>Discount Flat Amt.</th>';
								$table_html .= '<th>Minimum Subtotal</th>';
								$table_html .= '<th>Valid Till</th>';
								$table_html .= '<th>Is Generic</th>';
								$table_html .= '<th>Is Used</th>';
								$table_html .= '<th>View</th>';
							$table_html .= '</tr>';
						$table_html .= '</thead>';
						$table_html .= '<tbody>';
						$table_html .= '</tbody>';
					$table_html .= '</table>';
					echo $table_html;
				?>
				</div>
			</div>
		<div>
	</div>
<!-- Button trigger modal -->
<!--button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#churchDetailsModal">
  Launch demo modal
</button -->

<!-- Modal -->
<div class="bigModal fade" id="couponDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><!-- span class="sr-only">Close</span--></button>
        <h4 class="modal-title" id="couponDetailsModalLabel">Coupon Details</h4>
      </div>
      <div class="modal-body" id="couponDetailsBody">
			Loading data...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!-- button type="button" class="btn btn-primary">Save changes</button -->
      </div>
    </div>
  </div>
</div>
	<form name="coupon_form" id="coupon_form" method="post">
		<input type="hidden" id="hidTableContentHTML" name="hidTableContentHTML" value="<?php echo htmlentities($table_html); ?>"/>
		<input type="hidden" id="currListingType" name="currListingType" value="1">
	<form>

	<script type="text/javascript">
		<?php
			if($submenu_id==1)
			{
		?>
				listAllCoupons(1);
		<?php
			}
			else if($submenu_id==2)
			{
		?>
				$('#txtValidTill').datepicker({
					autoclose: true
				});
		<?php
			}
		?>
		//$('.datepicker').datepicker();
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>