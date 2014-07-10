<?php

function validateSession($APPLICATION_PATH)
{
	@include($APPLICATION_PATH."portal/secure.php");
	//Invalid session redirector
	if(isset($_SESSION["loginPassword"]) && trim($_SESSION["loginPassword"]) == $LOGIN_PWD && isset($_SESSION["accessPassword"]) && trim($_SESSION["accessPassword"]) == $ACCESS_PWD) {
		//allow
	} else {
		header('Location:'.$APPLICATION_PATH."portal/index.php");
		exit;
	}
	//Invalid session redirector
}

function logOut($APPLICATION_PATH)
{
	@include_once($APPLICATION_PATH."app/utils/utilfunctions.php");
	clearSession($APPLICATION_PATH);
	header('Location:'.$APPLICATION_PATH."portal/index.php");
	exit;
}
?>