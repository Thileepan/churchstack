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
<form name="ch_form" id="ch_form" method="post">
	<input type="hidden" id="hidTableContentHTML" name="hidTableContentHTML" value="<?php echo htmlentities($table_html); ?>"/>
<form>
	
	<script type="text/javascript">
		listAllChurches(1);
		//$('.datepicker').datepicker();
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>