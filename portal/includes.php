<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	validateSession($APPLICATION_PATH);

	@include_once($APPLICATION_PATH."app/classes/class.license.php");
	@include_once($APPLICATION_PATH."app/classes/class.church.php");
	@include_once($APPLICATION_PATH."app/db/dbutil.php");
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/utils.js"></script>
