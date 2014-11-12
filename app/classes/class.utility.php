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
		include_once($this->APPLICATION_PATH."classes/class.users.php");
		include_once($this->APPLICATION_PATH."classes/class.church.php");
		include_once($this->APPLICATION_PATH."classes/class.license.php");
		include_once($this->APPLICATION_PATH."classes/class.profiles.php");
		$users_obj = new Users($this->APPLICATION_PATH);
		$users_details = $users_obj->getUserInformation($user_id, $church_id);
		session_start();
		$_SESSION['lastFreshSessionUpdatedTime'] = time();

		//$_SESSION['userID'] = $users_details[1][0];
		$_SESSION['userID'] = $user_id;
		//$church_id = $users_details[1][1];
		$_SESSION['churchID'] = $church_id;
		$_SESSION['username'] = $users_details[1][2];
		$_SESSION['email'] = $users_details[1][3];
		$_SESSION['roleID'] = $users_details[1][4];
		//$_SESSION['password'] = $users_details[1][5];
		$_SESSION['userStatus'] = $users_details[1][8];
		$_SESSION['loginTime'] = time();
		$_SESSION['session_token_1'] = md5($_SESSION['userID'].$_SESSION['username'].$_SESSION['email'].$_SESSION['loginTime']);
		$_SESSION['session_token_2'] = md5($_SESSION['userID'].$_SESSION['churchID'].$_SESSION['email'].$_SESSION['loginTime']);
		//$_SESSION['shardedDB'] = 'cs01_churchstack';

		//Getting church details
		$church_obj = new Church($this->APPLICATION_PATH);
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
			$_SESSION['churchTimeZone'] = $church_result[1][16];

			//Get license details
			$lic_obj = new License($this->APPLICATION_PATH);
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
			$profiles_obj = new Profiles($this->APPLICATION_PATH);//It will work because shardeddb session will be set above
			$_SESSION["churchCurrentActiveProfilesCount"] = $profiles_obj->getProfilesCount(1, 0);//List All Active Profiles
		}
	}

	public function generateRandomCode($length=32)
	{
		if($length <= 0) {
			$length = 32;
		}
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$chars_length = strlen($chars);
		$res = "";
		for ($i = 0; $i < $length; $i++) {
			$res .= $chars[mt_rand(0, $chars_length-1)];
		}

		return $res;
	}

	protected function hex2bin($hexdata)
    {
        $bindata = '';

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
    }

	function encrypt($str, $isBinary = false, $key='')
    {
		if(trim($key) != "")
		{
			$key = md5($key);//default : second half (last 16 chars) of md5 of our product name completely in small case 
			$key = substr($key, 16, 16);
	        $iv = strrev($key);
		}
		else
		{
			$key = 'fbd983e24917d98f';//default : second half (last 16 chars) of md5 of our product name completely in small case 
	        $iv = strrev($key);
		}
        $str = $isBinary ? $str : utf8_decode($str);

        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);

        mcrypt_generic_init($td, $key, $iv);
        $encrypted = mcrypt_generic($td, $str);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $isBinary ? $encrypted : bin2hex($encrypted);
    }

	function decrypt($code, $isBinary = false, $key='')
    {
		if(trim($key) != "")
		{
			$key = md5($key);//default : second half (last 16 chars) of md5 of our product name completely in small case 
			$key = substr($key, 16, 16);
	        $iv = strrev($key);
		}
		else
		{
			$key = 'fbd983e24917d98f';//default : second half (last 16 chars) of md5 of our product name completely in small case 
	        $iv = strrev($key);
		}
        $code = $isBinary ? $code : $this->hex2bin($code);

        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);

        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $code);

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return $isBinary ? trim($decrypted) : utf8_encode(trim($decrypted));
    }

	public function addMonthsToTimestamp($time_stamp, $months_to_add=1)
	{
		$datetime_obj = new DateTime();
		$datetime_obj->setTimestamp($time_stamp);

		$year = $datetime_obj->format('Y');
		$month = $datetime_obj->format('n');
		$day = $datetime_obj->format('d');

		$year += floor($months_to_add/12);
		$months_to_add = $months_to_add%12;
		$month += $months_to_add;
		if($month > 12) {
			$year ++;
			$month = $month % 12;
			if($month === 0)
			$month = 12;
		}

		if(!checkdate($month, $day, $year)) {
			$datetime_obj_to_return = DateTime::createFromFormat('Y-n-j', $year.'-'.$month.'-1');
			$datetime_obj_to_return->modify('last day of');
		}else {
			$datetime_obj_to_return = DateTime::createFromFormat('Y-n-d', $year.'-'.$month.'-'.$day);
		}
		$datetime_obj_to_return->setTime($datetime_obj->format('H'), $datetime_obj->format('i'), $datetime_obj->format('s'));
		//echo $datetime_obj_to_return->format('Y-m-d H:i:s');
		return $datetime_obj_to_return->getTimestamp();
	}

	public function getTimeZonesList()
	{
		$zones_array = array();
		$timestamp = time();
		foreach(timezone_identifiers_list() as $key => $zone) {
			//date_default_timezone_set($zone);
			$zones_array[$key]['zone'] = $zone;
			$zones_array[$key]['diff_from_GMT'] = 'GMT ' . date('P', $timestamp);
		}
		return $zones_array;
  }
}

?>