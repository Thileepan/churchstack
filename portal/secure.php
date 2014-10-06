<?php
	session_start();
	if($_SERVER['REQUEST_URI'] == "/portal/secure.php") {
		@require '../portal/error/404.php';
		exit;
	}
	$PORTAL_FOLDER = "portal/";
	$LOGIN_PWD = "16d8dbe45f074dc44cfd0fc4846e03d1";
	$ACCESS_PWD = "c1b3a13d67c727c063fbccfdfccda859";
?>