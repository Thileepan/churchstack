<?php
//$APPLICATION_PATH = "../";
if(!function_exists('validateSession'))
{
	function validateSession($APPLICATION_PATH)
	{
		include_once($APPLICATION_PATH."conf/config.php");
		session_start();
		//Invalid session redirector
		if(!isset($_SESSION['session_token_1']) || $_SESSION['session_token_1'] != md5($_SESSION['userID'].$_SESSION['username'].$_SESSION['email'].$_SESSION['loginTime']) || !isset($_SESSION['session_token_2']) || $_SESSION['session_token_2'] != md5($_SESSION['userID'].$_SESSION['churchID'].$_SESSION['email'].$_SESSION['loginTime']))
		{
			header('Location:'.$APPLICATION_PATH."signin");
			exit;
		}
		else
		{
			if(isset($_SESSION['lastActivity']) && (time()-$_SESSION['lastActivity']) > IDLE_SECONDS_LOGOUT)//Checking the idle seconds
			{
				header('Location:'.$APPLICATION_PATH."signin");
				exit;
			}
		}
		
		//Very important to reject access after a long idle hours
		$_SESSION['lastActivity'] = time();

		//Very Important to refresh the session data after some specified time period
		if(isset($_SESSION['lastFreshSessionUpdatedTime']) && (time()-$_SESSION['lastFreshSessionUpdatedTime']) > SESSION_DATA_REFRESH_SECONDS)
		{
			include_once($APPLICATION_PATH."classes/class.utility.php");
			$util_obj = new Utility($APPLICATION_PATH);
			$util_obj->setFreshSessionData(trim($_SESSION['userID']), trim($_SESSION['churchID']));
		}
		
		if(isset($_SESSION['allowChurchUsage']) && $_SESSION['allowChurchUsage'] != 1)
		{
			if(isset($_SESSION['isOnTrial']))
			{
				if($_SESSION['isOnTrial']==1)
				{
					if(isset($_SESSION['trialExpiryTimestamp']))
					{
						if($_SESSION['trialExpiryTimestamp'] < time())
						{
							header('Location:'.$APPLICATION_PATH."purchase/subscribe");
						}
					}
					else
					{
						header('Location:'.$APPLICATION_PATH."purchase/subscribe");
						//payment page
					}
				}
				else
				{
					if(isset($_SESSION['licenseExpiryTimestamp']))
					{
						if($_SESSION['licenseExpiryTimestamp'] < time())
						{
							header('Location:'.$APPLICATION_PATH."purchase/subscribe");
						}
					}
					else
					{
						header('Location:'.$APPLICATION_PATH."purchase/subscribe");
					}
				}
			}
			else
			{
				header('Location:'.$APPLICATION_PATH."purchase/subscribe");
			}
		}
	}
}

if(!function_exists('logOut'))
{
	function logOut($APPLICATION_PATH)
	{
		@include_once($APPLICATION_PATH."utils/utilfunctions.php");
		clearSession($APPLICATION_PATH);
		header('Location:'.$APPLICATION_PATH."signin");
		exit;
	}
}

validateSession($APPLICATION_PATH);
?>