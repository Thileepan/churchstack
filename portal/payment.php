<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	validateSession($APPLICATION_PATH);

	$page_id=3;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/payment.js"></script>

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
						$table_html .= '<th>Total</th>';
						$table_html .= '<th>Remarks</th>';
						$table_html .= '<th>PG Remarks</th>';
						$table_html .= '<th>Updated</th>';
						$table_html .= '<th>IsRefund</th>';
						$table_html .= '<th>Actions</th>';
					$table_html .= '</tr>';
				$table_html .= '</thead>';
				$table_html .= '<tfoot>';
					$table_html .= '<tr>';
						$table_html .= '<th>Inv. ID</th>';
						$table_html .= '<th>Trans. ID</th>';
						$table_html .= '<th>Ref. ID</th>';
						$table_html .= '<th>Ch. ID</th>';
						$table_html .= '<th>Email</th>';
						$table_html .= '<th>Subtotal</th>';
						$table_html .= '<th>Total</th>';
						$table_html .= '<th>Remarks</th>';
						$table_html .= '<th>PG Remarks</th>';
						$table_html .= '<th>Updated</th>';
						$table_html .= '<th>IsRefund</th>';
						$table_html .= '<th>Actions</th>';
					$table_html .= '</tr>';
				$table_html .= '</tfoot>';
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
<div class="modal fade" id="emailInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><!-- span class="sr-only">Close</span--></button>
        <h4 class="modal-title" id="emailInvoiceModalLabel">Email This Invoice</h4>
      </div>
      <div class="modal-body" id="emailInvoiceBody">
		<div style="text-align:center; height:30px;" id="emailResultDiv" style="display:none;">
			<span class="label label-success" id="emailSuccessSpan" style="display:none;"></span>
			<span class="label label-danger" id="emailFailureSpan" style="display:none;"></span>
		</div>
		<div class="input-group" style="text-align:center;">
			<span class="input-group-addon">Email Address</span>
			<input type="text" class="form-control" placeholder="Email Address" id="txtEmailInvoice">
		</div>
		<div style="text-align:center;" id="divSendEmailBtn">
			<span id="sendEmailBtnSpan"><button class="btn btn-primary btn-lg" onclick="Javascript: paymentActions(2, '', '');">Send Email</button></span>
			<span id="sendEmailProgSpan" style="display:none;"><img src="<?php echo $APPLICATION_PATH;?>app/images/ajax-loader.gif">&nbsp;Sending now...</span>
			<input type="hidden" id="txtInvoiceIDToEmail" value="0">
		</div>
		<div style="text-align:center; height:10px;">
		</div>
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
	@include($APPLICATION_PATH."portal/footer.php")
?>