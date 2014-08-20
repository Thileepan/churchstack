<?php

class Utility
{
	//protected $db_conn;
	private $APPLICATION_PATH;
	private $geoLiteCityDatFile;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        //include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		include_once($this->APPLICATION_PATH . 'utils/utilfunctions.php');
		$this->geoLiteCityDatFile = $this->APPLICATION_PATH."plugins/geoip-api-php/GeoLiteCity/GeoLiteCity.dat";
		//$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, false);
		/** /
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
		/**/
	}

	public function getCountryCodeFromIP($ip_address)
	{
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoip.inc");
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoipcity.inc");

		$gi = geoip_open($this->geoLiteCityDatFile, GEOIP_STANDARD);
		$country_code = geoip_country_code_by_addr($gi, $ip_address);
		geoip_close($gi);
		return $country_code;
	}

	public function getRecordsFromIP($ip_address)
	{
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoip.inc");
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoipcity.inc");

		$gi = geoip_open($this->geoLiteCityDatFile, GEOIP_STANDARD);
		$record = geoip_record_by_addr($gi, $ip_address);
		geoip_close($gi);
		return $record;
	}

	public function downloadHTMLAsPDF($input_html, $target_file, $force_download=0, $paper="a4", $orientation="portrait")
	{
		require_once($this->APPLICATION_PATH."plugins/dompdf/dompdf_config.inc.php");

		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Failed to convert the data to PDF";
		if(trim($target_file) == "") {
			$toReturn[0] = 0;
			$toReturn[1] = "Target file name is empty";
			return $toReturn;
		}
		if(trim($input_html) == "") {
			$toReturn[0] = 0;
			$toReturn[1] = "Input data is empty";
			return $toReturn;
		}
		if ( get_magic_quotes_gpc() ) {
			$input_html = stripslashes($input_html);
		}
		$attachment_value = (($force_download==1)? true : false);
		$paper = ((trim($paper) != "")? trim($paper) : "a4");
		$orientation = ((trim($orientation) != "")? trim($orientation) : "portrait");
		$dompdf = new DOMPDF();
		$dompdf->load_html($input_html);
		$dompdf->set_paper($paper, $orientation);
		$dompdf->render();
		$dompdf->stream($target_file, array("Attachment" => $attachment_value));
		$toReturn[0] = 1;
		$toReturn[1] = "PDF streaming initiated";
		return $toReturn;
	}

	public function setFreshSessionData($user_id, $church_id)
	{
		include_once($APPLICATION_PATH."classes/class.users.php");
		include_once($APPLICATION_PATH."classes/class.church.php");
		include_once($APPLICATION_PATH."classes/class.license.php");
		include_once($APPLICATION_PATH."classes/class.license.php");
		include_once($APPLICATION_PATH."classes/class.profiles.php");
		$users_obj = new Users($APPLICATION_PATH);
		$users_details = $users_obj->getUserInformation($user_id, $church_id);
		session_start();
		$_SESSION['lastFreshSessionUpdatedTime'] = time();

		array($user_id, $church_id, $user_name, $email, $role_id, $unique_hash, $pwd_reset_hash, $pwd_reset_expiry, $status);
		//$_SESSION['userID'] = $users_details[1][0];
		$_SESSION['userID'] = $user_id;
		//$church_id = $users_details[1][1];
		$_SESSION['churchID'] = $church_id;
		$_SESSION['username'] = $users_details[1][2];
		$_SESSION['email'] = $users_details[1][3];
		$_SESSION['roleID'] = $users_details[1][4];
		//$_SESSION['password'] = $users_details[1][5];
		$_SESSION['loginTime'] = time();
		$_SESSION['session_token_1'] = md5($_SESSION['userID'].$_SESSION['username'].$_SESSION['email'].$_SESSION['loginTime']);
		$_SESSION['session_token_2'] = md5($_SESSION['userID'].$_SESSION['churchID'].$_SESSION['email'].$_SESSION['loginTime']);
		//$_SESSION['shardedDB'] = 'cs01_churchstack';

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
			$profiles_obj = new Profiles($APPLICATION_PATH);//It will work because shardeddb session will be set above
			$_SESSION["churchCurrentActiveProfilesCount"] = $profiles_obj->getProfilesCount(1, 0);//List All Active Profiles
		}
	}
}

?>