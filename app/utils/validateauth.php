<?php
//$APPLICATION_PATH = "../";
if(!function_exists('validateSession'))
{
	function validateSession($APPLICATION_PATH)
	{
		include_once($APPLICATION_PATH."conf/config.php");
		include_once($APPLICATION_PATH."utils/utilfunctions.php");

		/******************************************************************************************************* /
		THE FOLLOWING REDIRECTION WILL BE MADE WHEN WORKING ON SYSTEM UPGRADE AND MAINTENANCE.
		see config.php file for the following variable
		/*******************************************************************************************************/
		if(SYSTEM_UNDER_MAINTENANCE == 1) {
			header('Location:'.$APPLICATION_PATH."user/maintenance");
			exit;
		}
		/*******************************************************************************************************/

		session_start();
		//Invalid session redirector
		if(!isset($_SESSION['session_token_1']) || $_SESSION['session_token_1'] != md5($_SESSION['userID'].$_SESSION['username'].$_SESSION['email'].$_SESSION['loginTime']) || !isset($_SESSION['session_token_2']) || $_SESSION['session_token_2'] != md5($_SESSION['userID'].$_SESSION['churchID'].$_SESSION['email'].$_SESSION['loginTime']))
		{
			header('Location:'.$APPLICATION_PATH."index");
			exit;
		}
		else
		{
			if(isset($_SESSION['lastActivity']) && (time()-$_SESSION['lastActivity']) > IDLE_SECONDS_LOGOUT)//Checking the idle seconds
			{
				header('Location:'.$APPLICATION_PATH."index");
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
		
		//Licensing related stuff...
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
							if(!startsWith($_SERVER['REQUEST_URI'], "/info") && !startsWith($_SERVER['REQUEST_URI'], "/server/doinfo") && !startsWith($_SERVER['REQUEST_URI'], "/app/info") && !startsWith($_SERVER['REQUEST_URI'], "/app/server/doinfo")) {//Only if accessed from somewhere else. because it will go into loop if called from info file. This is because this file is included in info file also.
								header('Location:'.$APPLICATION_PATH."info?licexp=1#billing-plan");
								exit;
							}
						}
					}
					else
					{
						if(!startsWith($_SERVER['REQUEST_URI'], "/info") && !startsWith($_SERVER['REQUEST_URI'], "/server/doinfo") && !startsWith($_SERVER['REQUEST_URI'], "/app/info") && !startsWith($_SERVER['REQUEST_URI'], "/app/server/doinfo")) {//Only if accessed from somewhere else. because it will go into loop if called from info file. This is because this file is included in info file also.
							header('Location:'.$APPLICATION_PATH."info?licexp=1#billing-plan");
							exit;
						}
						//payment page
					}
				}
				else
				{
					if(isset($_SESSION['licenseExpiryTimestamp']))
					{
						if($_SESSION['licenseExpiryTimestamp'] < time())
						{
							if(!startsWith($_SERVER['REQUEST_URI'], "/info") && !startsWith($_SERVER['REQUEST_URI'], "/server/doinfo") && !startsWith($_SERVER['REQUEST_URI'], "/app/info") && !startsWith($_SERVER['REQUEST_URI'], "/app/server/doinfo")) {//Only if accessed from somewhere else. because it will go into loop if called from info file. This is because this file is included in info file also.
								header('Location:'.$APPLICATION_PATH."info?licexp=1#billing-plan");
								exit;
							}
						}
					}
					else
					{
						if(!startsWith($_SERVER['REQUEST_URI'], "/info") && !startsWith($_SERVER['REQUEST_URI'], "/server/doinfo") && !startsWith($_SERVER['REQUEST_URI'], "/app/info") && !startsWith($_SERVER['REQUEST_URI'], "/app/server/doinfo")) {//Only if accessed from somewhere else. because it will go into loop if called from info file. This is because this file is included in info file also.
							header('Location:'.$APPLICATION_PATH."info?licexp=1#billing-plan");
							exit;
						}
					}
				}
			}
			else
			{
				if(!startsWith($_SERVER['REQUEST_URI'], "/info") && !startsWith($_SERVER['REQUEST_URI'], "/server/doinfo") && !startsWith($_SERVER['REQUEST_URI'], "/app/info") && !startsWith($_SERVER['REQUEST_URI'], "/app/server/doinfo")) {//Only if accessed from somewhere else. because it will go into loop if called from info file. This is because this file is included in info file also.
					header('Location:'.$APPLICATION_PATH."info?licexp=1#billing-plan");
					exit;
				}
			}
		}

		//Church Deactivated....
		if(isset($_SESSION["churchStatus"]) && $_SESSION["churchStatus"] != 1)
		{
			$_SESSION["errorToShow"] = "Your church account has been deactivated.";
			header('Location:'.$APPLICATION_PATH."error/denied");
		}
		//User is deactivated
		else if(isset($_SESSION["userStatus"]) && $_SESSION["userStatus"] != 1)
		{
			$_SESSION["errorToShow"] = "Your account has been suspended.";
			header('Location:'.$APPLICATION_PATH."error/denied");
		}

		//Some more details are required, usually happens for the first time login
		if($_SESSION["churchTimeZone"] == "" || $_SESSION["churchCountryID"] == "" || $_SESSION["churchCurrencyID"] == "")
		{
			if($_SERVER['REQUEST_URI'] != "/user/moredata" && $_SERVER['REQUEST_URI'] != "/app/user/moredata") {//Only if accessed from somewhere else. because it will go into loop if called from moredata file. This is because this file is included in moredata file also.
				header('Location:'.$APPLICATION_PATH."user/moredata");
				exit;
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
		header('Location:'.$APPLICATION_PATH."index");
		exit;
	}
}

validateSession($APPLICATION_PATH);
?>