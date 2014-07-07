<?php

function clearSession($APPLICATION_PATH)
{
	session_start();
	foreach($_SESSION as $k => $v)
	{
		unset($k);
	}
	// Unset all of the session variables.
	$_SESSION = array();
	session_destroy();

	$_SESSION['loginPassword'] = '';
	$_SESSION['accessPassword'] = '';
}

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
	clearSession($APPLICATION_PATH);
	header('Location:'.$APPLICATION_PATH."portal/index.php");
	exit;
}
?>