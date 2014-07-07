<?php
	$APPLICATION_PATH = "../";

	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	validateSession($APPLICATION_PATH);

	$page_id=2;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/user.js"></script>

	<div class="row-fluid">
		<div id="usersList" class="span12">
			<table id="usersTable" class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>Church ID</th>
						<th>Username</th>
						<th>Email</th>
						<th>Role</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript">
		listAllUsers();
		//$('.datepicker').datepicker();
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>