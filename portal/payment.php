<?php
	$APPLICATION_PATH = "../";
	$page_id=3;
	@include($APPLICATION_PATH."admin/header.php");
	@include($APPLICATION_PATH."admin/includes.php");
?>
<script src="<?php echo $APPLICATION_PATH; ?>admin/js/payment.js"></script>

	<div class="row-fluid">
		<div id="paymentsList" class="span12">
			<?php
			$table_html = '<table id="paymentsTable" class="table table-striped">';
				$table_html .= '<thead>';
					$table_html .= '<tr>';
						$table_html .= '<th>Inv. ID</th>';
						$table_html .= '<th>Trans. ID</th>';
						$table_html .= '<th>Ref. ID</th>';
						$table_html .= '<th>Ch. ID</th>';
						$table_html .= '<th>Email</th>';
						$table_html .= '<th>Subtotal</th>';
						$table_html .= '<th>Discount</th>';
						$table_html .= '<th>Total</th>';
						$table_html .= '<th>Coupon</th>';
						$table_html .= '<th>Gateway</th>';
						$table_html .= '<th>Remarks</th>';
						$table_html .= '<th>PG Remarks</th>';
						$table_html .= '<th>Updated</th>';
						$table_html .= '<th>IsRefund</th>';
						$table_html .= '<th>View Details</th>';
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
<div class="bigModal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><!-- span class="sr-only">Close</span--></button>
        <h4 class="modal-title" id="paymentDetailsModalLabel">Invoice Details</h4>
      </div>
      <div class="modal-body" id="paymentDetailsBody">
			Loading data...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!-- button type="button" class="btn btn-primary">Save changes</button -->
      </div>
    </div>
  </div>
</div>
	<form name="ch_form" id="ch_form" method="post">
		<input type="hidden" id="hidTableContentHTML" name="hidTableContentHTML" value="<?php echo htmlentities($table_html); ?>"/>
	<form>

	<script type="text/javascript">
		listAllPayments();
		//$('.datepicker').datepicker();
	</script>
<?php
	@include($APPLICATION_PATH."admin/footer.php")
?>