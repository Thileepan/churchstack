<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	validateSession($APPLICATION_PATH);

	$page_id=1;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/church.js"></script>

	<div class="row-fluid">
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllChurches(1);">List All</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllChurches(3);">On Trial</button>
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllChurches(4);">Trial Expired</button>
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllChurches(5);">License Expired</button>
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllChurches(6);">Licensed</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-default" onclick="JavaScript: listAllChurches(7);">Deactivated</button>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div id="churchesList" class="span12">
		<?php
			$table_html = '<table id="churchesTable" class="table table-striped">';
				$table_html .= '<thead>';
					$table_html .= '<tr>';
						$table_html .= '<th>ID</th>';
						$table_html .= '<th>Name</th>';
						$table_html .= '<th>Mobile</th>';
						$table_html .= '<th>Email</th>';
						$table_html .= '<th>Signed Up</th>';
						$table_html .= '<th>Database</th>';
						$table_html .= '<th>Actions</th>';
					$table_html .= '</tr>';
				$table_html .= '</thead>';
				$table_html .= '<tfoot>';
					$table_html .= '<tr>';
						$table_html .= '<th>ID</th>';
						$table_html .= '<th>Name</th>';
						$table_html .= '<th>Mobile</th>';
						$table_html .= '<th>Email</th>';
						$table_html .= '<th>Signed Up</th>';
						$table_html .= '<th>Database</th>';
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
<div class="bigModal fade" id="churchDetailsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><!-- span class="sr-only">Close</span--></button>
        <h4 class="modal-title" id="churchDetailsModalLabel">Church Details</h4>
      </div>
      <div class="modal-body" id="churchDetailsBody">
			Loading data...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!-- button type="button" class="btn btn-primary">Save changes</button -->
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="extendValidityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" width="1000" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><!-- span class="sr-only">Close</span--></button>
        <h4 class="modal-title" id="extendValidityModalLabel">Extend Church's License Validity</h4>
      </div>
      <div class="modal-body" id="extendValidityBody">
		<div style="text-align:center; height:30px;" id="extendValidityResultDiv" style="display:none;">
			<span class="label label-success" id="extendValiditySuccessSpan" style="display:none;"></span>
			<span class="label label-danger" id="extendValidityFailureSpan" style="display:none;"></span>
		</div>
		<div class="input-group" style="text-align:center;">
			<span class="input-group-addon">Extend the validity of <b><span id="spanChurchNameToExtend"></span></b> by &nbsp;</span>
			<input type="text" class="form-control" placeholder="No. Of Days" id="txtValidityDays" style="width:30px;">&nbsp;&nbsp;Days
		</div>
		<div style="text-align:center;" id="divExtendBtn">
			<span id="extendBtnSpan"><button class="btn btn-primary btn-lg" onclick="Javascript: churchActions(4, '', '');">Extend Validity</button></span>
			<span id="extendProgSpan" style="display:none;"><img src="<?php echo $APPLICATION_PATH;?>app/images/ajax-loader.gif">&nbsp;Processing Now...</span>
			<input type="hidden" id="txtChurchIDToExtend" value="0">
			<input type="hidden" id="txtChurchNameToExtend" value="">
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
	<input type="hidden" id="currListingType" name="currListingType" value="1">
<form>
	
	<script type="text/javascript">
		listAllChurches(1);
		//$('.datepicker').datepicker();
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>