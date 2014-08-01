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

	public function getCurrencyNames($currency_id)
	{
		$toReturn = array();
		if($this->db_conn)
		{
		   $query = 'select * from CURRENCY_LIST where CURRENCY_ID=? limit 1';
		   $result = $this->db_conn->Execute($query, array($currency_id));
            
           if($result) {
                if(!$result->EOF) {
					$currency_details = array();
					$currency_id = $result->fields[0];
					$currency_short_name = $result->fields[1];
					$currency_long_name = $result->fields[2];
					$currency_details = array($currency_id, $currency_short_name, $currency_long_name);
					$toReturn[0] = 1;
					$toReturn[1] = $currency_details;
				}
            }
        }
		return $toReturn;
	}
	
	public function getAllChurchesList($filterType=0)
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
		/**/
		$toReturn = array();
		$all_churches = array();
		if($this->db_conn)
		{
		   $query = 'select * from CHURCH_DETAILS order by CHURCH_ID DESC';
		   if($filterType==0 or trim($filterType) == "") {
			   $query = 'select * from CHURCH_DETAILS order by CHURCH_ID DESC';
		   } else if($filterType==1) {
			   $query = 'select cd.* from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==2) {
			   $query = 'select cd.* from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE < NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==3) {
			   $query = 'select cd.* from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE < NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==4) {
			   $query = 'select cd.* from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1';
		   } else if($filterType==5) {
			   $query = 'select cd.* from CHURCH_DETAILS as cd, LICENSE_DETAILS as ld where ld.CHURCH_ID=cd.CHURCH_ID and ((ld.IS_ON_TRIAL=1 and ld.TRIAL_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1) or (ld.IS_ON_TRIAL!=1 and ld.LICENSE_EXPIRY_DATE >= NOW() and ld.PLAN_TYPE=1 and cd.STATUS=1)) GROUP BY cd.CHURCH_ID';
		   } else if($filterType==6) {
			   $query = 'select * from CHURCH_DETAILS where STATUS!=1';
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
						$church_details = array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_update_time, $sharded_database, $currency_id, $unique_hash, $status, $country_id);
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
}

?>