<?php

class Church
{
	protected $db_conn;
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		include_once($this->APPLICATION_PATH . 'utils/utilfunctions.php');
		include_once($this->APPLICATION_PATH . 'classes/class.settings.php');
		include_once($this->APPLICATION_PATH . 'classes/class.profiles.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, false);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
	}

	public function addChurchInformation($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $currency_id, $country_id, $referrer_church_id=0)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was some error while trying to add a church to the system";
		if($this->db_conn)
		{
			$curr_time = time();
			$currency_id = 1;//CHANGE THIS LATER
			$church_unique_hash = strtoupper(md5($curr_time.$church_name.rand(1, 10000).rand(1, 10000).rand(1, 10000)));
			$sharded_db_unique_part = md5($church_unique_hash.rand(1, 10000));
			$sharded_database = 'cs_'.$sharded_db_unique_part;
			
			$church_id = -1;
			$query = 'insert into CHURCH_DETAILS (CHURCH_ID, CHURCH_NAME, DESCRIPTION, ADDRESS, LANDLINE, MOBILE, EMAIL, WEBSITE, SIGNUP_TIME, LAST_UPDATE_TIME, SHARDED_DATABASE, CURRENCY_ID, UNIQUE_HASH, STATUS, COUNTRY_ID, REFERRER_CHURCH_ID) values(?,?,?,?,?,?,?,?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,?,?,?,?,?)';
			$result = $this->db_conn->Execute($query, array(0,$church_name,$church_desc,$church_addr,$landline,$mobile,$email,$website,$curr_time,$curr_time,$sharded_database,$currency_id,$church_unique_hash,1, $country_id, $referrer_church_id));
			if($result) {
				$query_1 = 'select CHURCH_ID, SHARDED_DATABASE from CHURCH_DETAILS where UNIQUE_HASH=? limit 1';
				$result_1 = $this->db_conn->Execute($query_1, array($church_unique_hash));
				if($result_1) {
					if(!$result_1->EOF) {
						$church_id = $result_1->fields[0];
						$sharded_database = $result_1->fields[1];
						if($church_id > 0)
						{
							$to_return[0] = 1;
							$to_return[1] = "The new church has been added successfully to the systemt";
							$to_return[2] = array("church_id"=>$church_id, "sharded_database"=>$sharded_database);
						}
					}
				}
			}
		}
		return $to_return;
	}

	public function updateChurchInformation($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $last_modified_time, $currency_id, $country_id)
	{
		if($this->db_conn)
		{
			$query = 'update CHURCH_DETAILS set CHURCH_NAME=?, DESCRIPTION=?, ADDRESS=?, LANDLINE=?, MOBILE=?, EMAIL=?, WEBSITE=?, LAST_UPDATE_TIME=?, CURRENCY_ID=?, COUNTRY_ID=? where CHURCH_ID=?';
			$result = $this->db_conn->Execute($query, array($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $last_modified_time, $currency_id, $country_id, $church_id));
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function getChurchInformation()
	{
		$church_details = array();
		if($this->db_conn)
		{
		   $query = 'select * from CHURCH_DETAILS';
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
					$church_name = $result->fields[0];
					$church_desc = $result->fields[1];
					$church_addr = $result->fields[2];
					$landline = $result->fields[3];
					$mobile = $result->fields[4];
					$email = $result->fields[5];
					$website = $result->fields[6];
					$church_details = array($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website);
				}
            }
        }
		return $church_details;
	}

	//Following were added by Nesan
	public function getInformationOfAChurch($church_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the church details due to errors";
		if($this->db_conn)
		{
		   $query = 'select * from CHURCH_DETAILS where CHURCH_ID=? limit 1';
		   $result = $this->db_conn->Execute($query, array($church_id));
            
           if($result) {
                if(!$result->EOF) {
					$church_details = array();
					$church_id = $result->fields[0];
					$church_name = $result->fields[1];
					$church_desc = $result->fields[2];
					$church_addr = $result->fields[3];
					$landline = $result->fields[4];
					$mobile = $result->fields[5];
					$email = $result->fields[6];
					$website = $result->fields[7];
					$signup_time = $result->fields[8];
					$last_update_time = $result->fields[9];
					$sharded_database = $result->fields[10];
					$currency_id = $result->fields[11];
					$unique_hash = $result->fields[12];
					$status = $result->fields[13];
					$country_id = $result->fields[14];
					$church_details = array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_update_time, $sharded_database, $currency_id, $unique_hash, $status, $country_id);
					$toReturn[0] = 1;
					$toReturn[1] = $church_details;
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "Unable to locate the account date right now, please try again later.";
				}
            } else {
					$toReturn[0] = 0;
					$toReturn[1] = "Unable to fetch the church information from the system.";
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to connect to the system, please try again later.";
		}
		return $toReturn;
	}

	public function getAllChurchesList($filterType=0, $expiring_in_days=6, $expiry_finding_range_seconds=86400)
	{
		/** /
			$filterType :
				0 or empty=> List all
				1 => list trial churches alone
				2 => list trial expired churches alone
				3 => list license expired churches alone
				4 => list paid and active churches alone
				5 => list On-Trial or (Paid & Active) churches
				6 => list deactivated churches
				7 => list trial expiring in XX days churches alone (Active Chuches only)
				8 => list license expiring in XX days churches alone (Active Chuches only)
				9 => list "XX days since trial expired" churches alone (Active Chuches only)
				10 => list "XX days since license expired" churches alone (Active Chuches only)
		/**/
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the churches list";
		if(($filterType==7 || $filterType==8) && ($expiring_in_days <= 0))
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Invalid input given";
			return $toReturnl;
		}
		if(($filterType==9 || $filterType==10) && ($expiring_in_days <= 0))
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Invalid input given";
			return $toReturnl;
		}
		$all_churches = array();
		if($filterType==7 || $filterType==8)
		{
			$expiring_in_seconds = $expiring_in_days*24*60*60;//Ignored for irrelevant filters
			$start_time_stamp = time()+$expiring_in_seconds;
			$expiry_finding_range_seconds = (($expiry_finding_range_seconds > 0)? $expiry_finding_range_seconds : 86400);//One day default
			$end_time_stamp = $start_time_stamp+$expiry_finding_range_seconds;
		}
		else if($filterType==9 || $filterType==10)
		{
			$seconds_since_expiry = $expiring_in_days*24*60*60;//Ignored for irrelevant filters
			$start_time_stamp = time()-$seconds_since_expiry;
			$expiry_finding_range_seconds = (($expiry_finding_range_seconds > 0)? $expiry_finding_range_seconds : 86400);//One day default
			$end_time_stamp = $start_time_stamp-$expiry_finding_range_seconds;
		}
		if($this->db_conn)
		{
		   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where cd.CHURCH_ID=ld.CHURCH_ID and ld.PLAN_TYPE=1 order by cd.CHURCH_ID DESC';
		   if($filterType==0 or trim($filterType) == "") {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where cd.CHURCH_ID=ld.CHURCH_ID and ld.PLAN_TYPE=1 order by cd.CHURCH_ID DESC';
		   } else if($filterType==1) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==2) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE < NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==3) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE < NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==4) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==5) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ((ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1) or (ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1)) GROUP BY cd.CHURCH_ID';
		   } else if($filterType==6) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS where STATUS!=1';
		   } else if($filterType==7) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE > FROM_UNIXTIME('.$start_time_stamp.') and ld.TRIAL_EXPIRY_DATE < FROM_UNIXTIME('.$end_time_stamp.') and ld.PLAN_TYPE=1 and cd.STATUS=1 GROUP BY cd.CHURCH_ID';
		   } else if($filterType==8) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE > FROM_UNIXTIME('.$start_time_stamp.') and ld.LICENSE_EXPIRY_DATE < FROM_UNIXTIME('.$end_time_stamp.')  and ld.PLAN_TYPE=1 and cd.STATUS=1 GROUP BY cd.CHURCH_ID';
		   } else if($filterType==9) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE < FROM_UNIXTIME('.$start_time_stamp.') and ld.TRIAL_EXPIRY_DATE > FROM_UNIXTIME('.$end_time_stamp.') and ld.PLAN_TYPE=1 and cd.STATUS=1 GROUP BY cd.CHURCH_ID';
		   } else if($filterType==10) {
			   $query = 'select cd.CHURCH_ID, cd.CHURCH_NAME, cd.DESCRIPTION, cd.ADDRESS, cd.LANDLINE, cd.MOBILE, cd.EMAIL, cd.WEBSITE, cd.SIGNUP_TIME, cd.LAST_UPDATE_TIME, cd.SHARDED_DATABASE, cd.CURRENCY_ID, cd.UNIQUE_HASH, cd.STATUS, cd.COUNTRY_ID, cd.REFERRER_CHURCH_ID, ld.CHURCH_ID, ld.PLAN_ID, ld.PLAN_TYPE, ld.LICENSE_EXPIRY_DATE, ld.LAST_INVOICE_ID, ld.LAST_PURCHASE_DATE, ld.IS_ON_TRIAL, ld.TRIAL_EXPIRY_DATE from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE < FROM_UNIXTIME('.$start_time_stamp.') and ld.LICENSE_EXPIRY_DATE > FROM_UNIXTIME('.$end_time_stamp.')  and ld.PLAN_TYPE=1 and cd.STATUS=1 GROUP BY cd.CHURCH_ID';
		   }
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
			   if(!$result->EOF)
			   {
					while(!$result->EOF) {
						$church_details = array();
						$church_id = $result->fields[0];
						$church_name = $result->fields[1];
						$church_desc = $result->fields[2];
						$church_addr = $result->fields[3];
						$landline = $result->fields[4];
						$mobile = $result->fields[5];
						$email = $result->fields[6];
						$website = $result->fields[7];
						$signup_time = $result->fields[8];
						$last_update_time = $result->fields[9];
						$sharded_database = $result->fields[10];
						$currency_id = $result->fields[11];
						$unique_hash = $result->fields[12];
						$status = $result->fields[13];
						$country_id = $result->fields[14];
						$referrer_church_id = $result->fields[15];
						$church_id = $result->fields[16];
						$plan_id = $result->fields[17];
						$plan_type = $result->fields[18];
						$license_expiry_date = $result->fields[19];
						$last_invoice_id = $result->fields[20];
						$last_purchase_date = $result->fields[21];
						$is_on_trial = $result->fields[22];
						$trial_expiry_date = $result->fields[23];
						$church_details = array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_update_time, $sharded_database, $currency_id, $unique_hash, $status, $country_id, $referrer_church_id, $church_id, $plan_id, $plan_type, $license_expiry_date, $last_invoice_id, $last_purchase_date, $is_on_trial, $trial_expiry_date);
						$all_churches[] = $church_details;

						$result->MoveNext();
					}
					$toReturn[0] = 1;
					$toReturn[1] = $all_churches;
			   }
            }
        }
		return $toReturn;
	}

	public function getChurchMiscDetails($church_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while trying to get the account info.";
		if($this->db_conn)
		{
		   $query = 'select A.CURRENCY_CODE, A.CURRENCY_NUMBER, A.CURRENCY_DESCRIPTION, B.COUNTRY_ISO_CODE, B.COUNTRY_NAME, B.COUNTRY_ISO3_CODE, B.COUNTRY_CALLING_CODE from CURRENCY_LIST as A, COUNTRY_LIST as B, CHURCH_DETAILS as C where C.CHURCH_ID=? and C.CURRENCY_ID=A.CURRENCY_ID and C.COUNTRY_ID=B.COUNTRY_ID';
		   $result = $this->db_conn->Execute($query, array($church_id));
            
           if($result) {
                if(!$result->EOF) {
					$church_misc_details = array();
					$currency_code = $result->fields[0];
					$currency_number = $result->fields[1];
					$currency_desc = $result->fields[2];
					$country_iso_code = $result->fields[3];
					$country_name = $result->fields[4];
					$country_iso3_code = $result->fields[5];
					$country_calling_code = $result->fields[6];
					$church_misc_details = array($currency_code, $currency_number, $currency_desc, $country_iso_code, $country_name, $country_iso3_code, $country_calling_code);

					$toReturn[0] = 1;
					$toReturn[1] = $church_misc_details;
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "No details associated with the account could be retrieved.";
				}
            } else {
				$toReturn[0] = 0;
				$toReturn[1] = "There was an error when fetching the account details";
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to get connection to the system.";
		}
		return $toReturn;
	}

	public function deactivateChurch($church_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to deactivate the church";
		if($this->db_conn)
		{
			$query = 'update CHURCH_DETAILS set STATUS=0 where CHURCH_ID=?';
			$result = $this->db_conn->Execute($query, array($church_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Church is deactivated now";
			}			
		}
		return $to_return;
	}

	public function activateChurch($church_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to activate the church";
		if($this->db_conn)
		{
			$query = 'update CHURCH_DETAILS set STATUS=1 where CHURCH_ID=?';
			$result = $this->db_conn->Execute($query, array($church_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Church is activated now";
			}			
		}
		return $to_return;
	}

	public function sendTrialExpiringEmail($trial_details, $target_email="", $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$trial_template_file = $this->APPLICATION_PATH."templates/email/trialexpiring.html";
		$trial_report = "";
		if(file_exists($trial_template_file))
		{
			$trial_report = trim(file_get_contents($trial_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the trial report";
		}

		//Replacing place holder with values
		$trial_report = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $trial_report);
		$trial_report = str_replace("{{TRIAL_REM_DAYS}}", $trial_details["remaining_trial_days"], $trial_report);
		$trial_report = str_replace("{{PRICING_URL}}", PRICING_URL, $trial_report);
		$trial_report = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $trial_report);
		$trial_report = str_replace("{{SALES_EMAIL}}", SALES_EMAIL, $trial_report);
		$trial_report = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $trial_report);
		$trial_report = str_replace("{{CHURCH_NAME}}", $trial_details["church_name"], $trial_report);

		$subject = PRODUCT_NAME." - How is your trial going?";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = ((trim($target_email) != "")? trim($target_email) : $trial_details["email"]);
			$contents_array[1] = $subject;
			$contents_array[2] = $trial_report;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}
		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_SALES);
		$recipients = array();
		$recipients['to_address'] = ((trim($target_email) != "")? trim($target_email) : $trial_details["email"]);
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($trial_report);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Trial report sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send trial report to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function sendLicenseExpiringEmail($lic_details, $target_email="", $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$lic_template_file = $this->APPLICATION_PATH."templates/email/licenseexpiring.html";
		$lic_report = "";
		if(file_exists($lic_template_file))
		{
			$lic_report = trim(file_get_contents($lic_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the trial report";
		}

		//Replacing place holder with values
		$lic_report = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $lic_report);
		$lic_report = str_replace("{{LIC_REM_DAYS}}", $lic_details["remaining_license_days"], $lic_report);
		$lic_report = str_replace("{{PRICING_URL}}", PRICING_URL, $lic_report);
		$lic_report = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $lic_report);
		$lic_report = str_replace("{{SALES_EMAIL}}", SALES_EMAIL, $lic_report);
		$lic_report = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $lic_report);
		$lic_report = str_replace("{{CHURCH_NAME}}", $lic_details["church_name"], $lic_report);

		$subject = PRODUCT_NAME." - Your account is due for renewal";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = ((trim($target_email) != "")? trim($target_email) : $lic_details["email"]);
			$contents_array[1] = $subject;
			$contents_array[2] = $lic_report;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}
		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_SALES);
		$recipients = array();
		$recipients['to_address'] = ((trim($target_email) != "")? trim($target_email) : $lic_details["email"]);
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($lic_report);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Trial report sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send trial report to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function getCurrencyDetails($currency_id="")
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the currency details";
		if($this->db_conn)
		{
			if(trim($currency_id) != "")
			{
				$query = 'select CURRENCY_ID, CURRENCY_CODE, CURRENCY_NUMBER, CURRENCY_DESCRIPTION, COUNTRY from CURRENCY_LIST where CURRENCY_ID=? limit 1';
				$result = $this->db_conn->Execute($query, array($currency_id));
			}
			else
			{
				$query = 'select CURRENCY_ID, CURRENCY_CODE, CURRENCY_NUMBER, CURRENCY_DESCRIPTION, COUNTRY from CURRENCY_LIST';
				$result = $this->db_conn->Execute($query);
			}

			if($result) {
				$currency_details = array();
				while(!$result->EOF) {
					$currency_id = $result->fields[0];
					$currency_code = $result->fields[1];
					$currency_number = $result->fields[2];
					$currency_description = $result->fields[3];
					$currency_country = $result->fields[4];
					$currency_details[] = array($currency_id, $currency_code, $currency_number, $currency_description, $currency_country);
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $currency_details;
			}
		}
		return $toReturn;
	}

	public function getCountryDetails($country_id="")
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the country details";
		if($this->db_conn)
		{
			if(trim($currency_id) != "")
			{
				$query = 'select COUNTRY_ID, COUNTRY_ISO_CODE, COUNTRY_NAME_CAPS, COUNTRY_NAME, COUNTRY_ISO3_CODE, COUNTRY_NUMERIC_CODE, COUNTRY_CALLING_CODE from COUNTRY_LIST where COUNTRY_ID=? limit 1';
				$result = $this->db_conn->Execute($query, array($country_id));
			}
			else
			{
				$query = 'select COUNTRY_ID, COUNTRY_ISO_CODE, COUNTRY_NAME_CAPS, COUNTRY_NAME, COUNTRY_ISO3_CODE, COUNTRY_NUMERIC_CODE, COUNTRY_CALLING_CODE from COUNTRY_LIST';
				$result = $this->db_conn->Execute($query);
			}

			if($result) {
				$country_details = array();
				while(!$result->EOF) {
					$country_id = $result->fields[0];
					$country_iso_code = $result->fields[1];
					$country_name_caps = $result->fields[2];
					$country_name = $result->fields[3];
					$country_iso3_code = $result->fields[4];
					$country_numeric_code = $result->fields[5];
					$country_calling_code = $result->fields[6];
					$country_details[] = array($country_id, $country_iso_code, $country_name_caps, $country_name, $country_iso3_code, $country_numeric_code, $country_calling_code);
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $country_details;
			}
		}
		return $toReturn;
	}

	public function getCountryInfoFromISOCode($country_iso_code)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the country details";
		if($this->db_conn)
		{
			$query = 'select COUNTRY_ID, COUNTRY_ISO_CODE, COUNTRY_NAME_CAPS, COUNTRY_NAME, COUNTRY_ISO3_CODE, COUNTRY_NUMERIC_CODE, COUNTRY_CALLING_CODE from COUNTRY_LIST  where COUNTRY_ISO_CODE=? limit 1';
			$result = $this->db_conn->Execute($query, array($country_iso_code));

			if($result) {
				if(!$result->EOF) {
					$currency_details = array();
					$country_id = $result->fields[0];
					$country_iso_code = $result->fields[1];
					$country_name_caps = $result->fields[2];
					$country_name = $result->fields[3];
					$country_iso3_code = $result->fields[4];
					$country_numeric_code = $result->fields[5];
					$country_calling_code = $result->fields[6];
					$currency_details = array($country_id, $country_iso_code, $country_name_caps, $country_name, $country_iso3_code, $country_numeric_code, $country_calling_code);
					$toReturn[0] = 1;
					$toReturn[1] = $currency_details;
				}
			}
		}
		return $toReturn;
	}

	public function getCountryInfoFromISO3Code($country_iso3_code)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the country details";
		if($this->db_conn)
		{
			$query = 'select COUNTRY_ID, COUNTRY_ISO_CODE, COUNTRY_NAME_CAPS, COUNTRY_NAME, COUNTRY_ISO3_CODE, COUNTRY_NUMERIC_CODE, COUNTRY_CALLING_CODE from COUNTRY_LIST  where COUNTRY_ISO3_CODE=? limit 1';
			$result = $this->db_conn->Execute($query, array($country_iso3_code));

			if($result) {
				if(!$result->EOF) {
					$currency_details = array();
					$country_id = $result->fields[0];
					$country_iso_code = $result->fields[1];
					$country_name_caps = $result->fields[2];
					$country_name = $result->fields[3];
					$country_iso3_code = $result->fields[4];
					$country_numeric_code = $result->fields[5];
					$country_calling_code = $result->fields[6];
					$currency_details = array($country_id, $country_iso_code, $country_name_caps, $country_name, $country_iso3_code, $country_numeric_code, $country_calling_code);
					$toReturn[0] = 1;
					$toReturn[1] = $currency_details;
				}
			}
		}
		return $toReturn;
	}

	public function sendTrialExpiredEmail($trial_details, $target_email="", $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$trial_template_file = $this->APPLICATION_PATH."templates/email/trialexpired.html";
		$trial_report = "";
		if(file_exists($trial_template_file))
		{
			$trial_report = trim(file_get_contents($trial_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the trial report";
		}

		//Replacing place holder with values
		$trial_report = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $trial_report);
		$trial_report = str_replace("{{TRIAL_REM_DAYS}}", $trial_details["days_since_expiry"], $trial_report);
		$trial_report = str_replace("{{PRICING_URL}}", PRICING_URL, $trial_report);
		$trial_report = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $trial_report);
		$trial_report = str_replace("{{SALES_EMAIL}}", SALES_EMAIL, $trial_report);
		$trial_report = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $trial_report);
		$trial_report = str_replace("{{CHURCH_NAME}}", $trial_details["church_name"], $trial_report);

		$subject = PRODUCT_NAME." - How did your trial go?";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = ((trim($target_email) != "")? trim($target_email) : $trial_details["email"]);
			$contents_array[1] = $subject;
			$contents_array[2] = $trial_report;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}
		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_SALES);
		$recipients = array();
		$recipients['to_address'] = ((trim($target_email) != "")? trim($target_email) : $trial_details["email"]);
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($trial_report);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Trial report sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send trial report to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function sendLicenseExpiredEmail($lic_details, $target_email="", $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$lic_template_file = $this->APPLICATION_PATH."templates/email/licenseexpired.html";
		$lic_report = "";
		if(file_exists($lic_template_file))
		{
			$lic_report = trim(file_get_contents($lic_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the trial report";
		}

		//Replacing place holder with values
		$lic_report = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $lic_report);
		$lic_report = str_replace("{{LIC_REM_DAYS}}", $lic_details["days_since_expiry"], $lic_report);
		$lic_report = str_replace("{{PRICING_URL}}", PRICING_URL, $lic_report);
		$lic_report = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $lic_report);
		$lic_report = str_replace("{{SALES_EMAIL}}", SALES_EMAIL, $lic_report);
		$lic_report = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $lic_report);
		$lic_report = str_replace("{{CHURCH_NAME}}", $lic_details["church_name"], $lic_report);

		$subject = PRODUCT_NAME." - Your license has expired";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = ((trim($target_email) != "")? trim($target_email) : $lic_details["email"]);
			$contents_array[1] = $subject;
			$contents_array[2] = $lic_report;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}
		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_SALES);
		$recipients = array();
		$recipients['to_address'] = ((trim($target_email) != "")? trim($target_email) : $lic_details["email"]);
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($lic_report);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "license reminder report sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send license reminder report to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}
}

?>