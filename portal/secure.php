<?php
	session_start();
	if($_SERVER['REQUEST_URI'] == "/portal/secure.php") {
		@require '../portal/error/404.php';
		exit;
	}
	$PORTAL_FOLDER = "portal/";
	$LOGIN_PWD = "21232f297a57a5a743894a0e4a801fc3";
	$ACCESS_PWD = "21232f297a57a5a743894a0e4a801fc3";
?>