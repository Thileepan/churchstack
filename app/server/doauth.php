<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include_once $APPLICATION_PATH . '/classes/class.users.php';
include_once $APPLICATION_PATH . '/classes/class.church.php';
include_once $APPLICATION_PATH . '/classes/class.license.php';
include_once($APPLICATION_PATH . 'classes/class.profiles.php');
//include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';

//process request
$req = trim($_POST['req']);

if($req == 'authenticate')
{
	$user_name = trim($_POST['username']);
	$email = ((trim($_POST['email'])!="")? trim($_POST['email']) : $user_name );
	$password = md5(trim($_POST['password']));
	
	$users_obj = new Users($APPLICATION_PATH);
	$login_result = $users_obj->isAuthenticatedUser($email, $password);
	$is_auth_valid = 0;
	$allow_login = 0;
	$login_message = "Invalid login credentials, unable to log you in.";
	$status_num = 0;
	$church_status_num = 0;
	if($login_result[0]==1)  {

		//$user_details = $users_obj->getUserInformationUsingName($user_name);

		session_start();
		$_SESSION['userID'] = $login_result[2][0];
		$church_id = $login_result[2][1];
		$_SESSION['churchID'] = $church_id;
		$_SESSION['username'] = $login_result[2][2];
		$_SESSION['email'] = $login_result[2][3];
		$_SESSION['roleID'] = $login_result[2][4];
		//$_SESSION['password'] = $login_result[2][5];
		$_SESSION['loginTime'] = time();
		$_SESSION['session_token_1'] = md5($_SESSION['userID'].$_SESSION['username'].$_SESSION['email'].$_SESSION['loginTime']);
		$_SESSION['session_token_2'] = md5($_SESSION['userID'].$_SESSION['churchID'].$_SESSION['email'].$_SESSION['loginTime']);
		//$_SESSION['shardedDB'] = 'cs01_churchstack';
		$is_auth_valid = 1;
		$login_message = $login_result[1];
		$status_num = $login_result[2][6];
		$allow_login = (($status_num==1)? 1 : 0);//NOTE: We may have to work on this part to show various messages and permissions.

		//Getting church details
		$church_obj = new Church($APPLICATION_PATH);
		$church_result = $church_obj->getInformationOfAChurch($church_id);
		if($church_result[0]==1)
		{
			$_SESSION['churchName'] = $church_result[1][1];
			$_SESSION['churchDesc'] = $church_result[1][2];
			$_SESSION['churchAddr'] = $church_result[1][3];
			$_SESSION['churchEmail'] = $church_result[1][6];
			$_SESSION['churchWebsite'] = $church_result[1][7];
			$_SESSION['shardedDB'] = $church_result[1][10];
			$_SESSION['churchCurrencyID'] = $church_result[1][11];
			$church_status_num = $church_result[1][13];
			$_SESSION['churchStatus'] = $church_status_num;
			$_SESSION['churchCountryID'] = $church_result[1][14];

			//Get license details
			$lic_obj = new License($APPLICATION_PATH);
			$lic_obj->setChurchID($church_id);
			$license_result = $lic_obj->getLicenseDetails(1);//plan type=1 indicates subscription/validity 
			if($license_result[0]==1)
			{
				$curr_plan_id = $license_result[1][0]["plan_id"];
				$plan_details = $lic_obj->getLicensePlanDetails($curr_plan_id);
				$_SESSION["maxProfileCount"] = -1;
				if($plan_details[0]==1) {
					$_SESSION["maxProfileCount"] = $plan_details[1]["max_count"];
				}
				$_SESSION["currentPlanID"] = $curr_plan_id;
				$_SESSION['licenseExpiryDate'] = $license_result[1][0]["lic_expiry_date"];
				$_SESSION['licenseExpiryTimestamp'] = $license_result[1][0]["lic_expiry_timestamp"];
				$_SESSION['isOnTrial'] = $license_result[1][0]["is_on_trial"];
				$_SESSION['trialExpiryDate'] = $license_result[1][0]["trial_expiry_date"];
				$_SESSION['trialExpiryTimestamp'] = $license_result[1][0]["trial_expiry_timestamp"];
				$_SESSION['allowChurchUsage'] = $license_result[1][0]["allow_usage"];
				$_SESSION['remainingTrialPeriodTimestamp'] = $license_result[1][0]["remaining_trial_period_timestamp"];
				$_SESSION['remainingTrialPeriodDays'] = $license_result[1][0]["remaining_trial_period_days"];
			}

			//Get other miscellaneous details...
			$misc_details = $church_obj->getChurchMiscDetails($church_id);
			if($misc_details[0]==1)
			{
				$_SESSION['currencyCode'] = $misc_details[1][0];
				$_SESSION['currencyNumber'] = $misc_details[1][1];
				$_SESSION['currencyDesc'] = $misc_details[1][2];
				$_SESSION['countryISOCode'] = $misc_details[1][3];
				$_SESSION['countryName'] = $misc_details[1][4];
				$_SESSION['countryISO3Code'] = $misc_details[1][5];
				$_SESSION['countryCallingCode'] = $misc_details[1][6];
			}

			//Get active profiles count
			$profiles_obj = new Profiles($APPLICATION_PATH);
			$_SESSION["churchCurrentActiveProfilesCount"] = $profiles_obj->getProfilesCount(1, 0);//List All Active Profiles
		}
	} else {
		$is_auth_valid = 0;
		$login_message = $login_result[1];
		$status_num = 0;
		$allow_login = 0;
	}
	$to_return = array("isAuthValid"=>$is_auth_valid, "allowLogin"=>$allow_login, "loginMessage"=>$login_message, "userStatusNumber"=>$status_num, "churchStatusNumber"=>$church_status_num);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 'signup')
{
	session_start();
	$church_name = trim($_POST['churchName']);
	$church_location = trim($_POST['churchLocation']);
	$first_name = trim($_POST['name']);
	$middle_name = trim($_POST['']);
	$last_name = trim($_POST['']);
	$email = trim($_POST['email']);
	$mobile = trim($_POST['phone']);
	$referrer_email = trim($_POST['referrerEmail']);
	$password = trim($_POST['password']);
	$password = md5($password);
	$security_text = trim($_POST['securityText']);

	$signup_result = array();
	$signup_result[0] = 0;
	$signup_result[1] = "Unable to sign you up because the system has encountered some error";
	if($security_text == trim($_SESSION['cap_sec_text']))
	{
		$users_obj = new Users($APPLICATION_PATH);
		$signup_result = $users_obj->signUpWithChurchDetails($church_name, $church_location, $first_name, $middle_name, $last_name, $email, $mobile, $referrer_email, $password);
	}
	else
	{
		$signup_result[0] = 0;
		$signup_result[1] = "The security code you have entered is wrong. Correct it or get new image if you are not able to read the shown image.";
	}
	$json = new Services_JSON();
	$encode_obj = $json->encode($signup_result);
	unset($json);

	echo $encode_obj;
	exit;
}
?>