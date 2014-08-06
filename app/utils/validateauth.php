<?php
//$APPLICATION_PATH = "../";
if(!function_exists('validateSession'))
{
	function validateSession($APPLICATION_PATH)
	{
		session_start();
		//Invalid session redirector
		if(!isset($_SESSION['session_token_1']) || $_SESSION['session_token_1'] != md5($_SESSION['userID'].$_SESSION['username'].$_SESSION['email'].$_SESSION['loginTime']) || !isset($_SESSION['session_token_2']) || $_SESSION['session_token_2'] != md5($_SESSION['userID'].$_SESSION['churchID'].$_SESSION['email'].$_SESSION['loginTime']))
		{
			header('Location:'.$APPLICATION_PATH."signin.php");
			exit;
		}
	}
}

if(!function_exists('logOut'))
{
	function logOut($APPLICATION_PATH)
	{
		@include_once($APPLICATION_PATH."utils/utilfunctions.php");
		clearSession($APPLICATION_PATH);
		header('Location:'.$APPLICATION_PATH."signin.php");
		exit;
	}
}

validateSession($APPLICATION_PATH);
?>