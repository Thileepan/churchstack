<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	validateSession($APPLICATION_PATH);

	$page_id=4;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/coupon.js"></script>
	<div class="row-fluid">
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(1);">List All</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(3);">Active</button>
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(4);">Used</button>
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllCoupons(5);">Expired</button>
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
	<form>

	<script type="text/javascript">
		listAllCoupons(1);
		//$('.datepicker').datepicker();
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>