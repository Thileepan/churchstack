<?php

class SMS
{
	protected $db_conn;
	protected $sms_provider_id;
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH, $shardedDBName="")
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true, $shardedDBName);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
	}

	public function getSMSConfiguration()
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the SMS configuration";
		if($this->db_conn)
		{
		   $query = 'select FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE from GLOBAL_CONFIGURATION where FEATURE_NAME=?';
		   $result = $this->db_conn->Execute($query, array("SMS"));
            
           if($result) {
			   $sms_details = array();
                while(!$result->EOF) {
					$feature_key = $result->fields[0];
					$feature_int_value = $result->fields[1];
					$feature_string_value = $result->fields[2];
					if($feature_key == "SMS_ENABLED") {
						$sms_details["SMS_ENABLED"] = $feature_int_value;
					} else if($feature_key == "SMS_PROVIDER_ID") {
						$sms_details["SMS_PROVIDER_ID"] = $feature_int_value;
					}
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $sms_details;
            }
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to connect to the system, please try again later.";
		}
		return $toReturn;
	}

	public function disableSMSFeature()
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to disable SMS feature";
		if($this->db_conn)
		{
			$query = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array("SMS", "SMS_ENABLED", 0, ""));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "SMS feature disabled";
			}			
		}
		return $to_return;
	}
	
	public function enableSMSFeature()
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to enable SMS feature";
		if($this->db_conn)
		{
			$query = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array("SMS", "SMS_ENABLED", 1, ""));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "SMS feature enabled";
			}			
		}
		return $to_return;
	}

	public function updateSMSProvider($sms_provider_id=0)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to update SMS provider ID";
		if($this->db_conn)
		{
			$query = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array("SMS", "SMS_PROVIDER_ID", $sms_provider_id, ""));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "SMS provider updated";
			}			
		}
		return $to_return;
	}

	public function getTwilioConfig($list_type=0)
	{
		/** /
		$list_type:
		0 => all
		1 => active
		2 => inactive
		/**/
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the Twilio configuration";
		if($this->db_conn)
		{
		   $query = 'select CONFIG_ID, ACCOUNT_SID, AUTH_TOKEN, FROM_NUMBER, STATUS from TWILIO_CONFIGURATION';
		   if($list_type==1) {
			   $query = 'select CONFIG_ID, ACCOUNT_SID, AUTH_TOKEN, FROM_NUMBER, STATUS from TWILIO_CONFIGURATION where STATUS=1';
		   } else if($list_type==2) {
			   $query = 'select CONFIG_ID, ACCOUNT_SID, AUTH_TOKEN, FROM_NUMBER, STATUS from TWILIO_CONFIGURATION where STATUS=0';
		   }
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
			   $twilio_details = array();
                while(!$result->EOF) {
					$config_id = $result->fields[0];
					$account_sid = $result->fields[1];
					$auth_token = $result->fields[2];
					$from_number = $result->fields[3];
					$status = $result->fields[4];
					$twilio_details[] = array($config_id, $account_sid, $auth_token, $from_number, $status);
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $twilio_details;
            }
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to connect to the system, please try again later.";
		}
		return $toReturn;
	}

	public function addTwilioConfig($account_sid, $auth_token, $from_number, $status)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to add twilio config";
		if($this->db_conn)
		{
			$query = 'insert into TWILIO_CONFIGURATION (CONFIG_ID, ACCOUNT_SID, AUTH_TOKEN, FROM_NUMBER, STATUS) values (?,?,?,?,?) ON DUPLICATE KEY UPDATE STATUS=VALUES(STATUS)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array(0, $account_sid, $auth_token, $from_number, $status));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Twilio configuration saved";
			}			
		}
		return $to_return;
	}

	public function modifyTwilioConfig($config_id, $account_sid, $auth_token, $from_number, $status)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to update twilio config";
		if($this->db_conn)
		{
			$query = 'update TWILIO_CONFIGURATION set ACCOUNT_SID=?, AUTH_TOKEN=?, FROM_NUMBER=?, STATUS=? where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($account_sid, $auth_token, $from_number, $status, $config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Twilio configuration updated successfully";
			}			
		}
		return $to_return;
	}

	public function deleteTwilioConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the twilio config";
		if($this->db_conn)
		{
			$query = 'delete from TWILIO_CONFIGURATION where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Twilio configuration deleted successfully";
			}			
		}
		return $to_return;
	}
	
	public function enableTwilioConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to enable twilio config";
		if($this->db_conn)
		{
			$query = 'update TWILIO_CONFIGURATION set STATUS=1 where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Twilio configuration enabled successfully";
			}			
		}
		return $to_return;
	}

	public function disableTwilioConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to disable twilio config";
		if($this->db_conn)
		{
			$query = 'update TWILIO_CONFIGURATION set STATUS=0 where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Twilio configuration disabled successfully";
			}			
		}
		return $to_return;
	}

	public function getBhashSMSConfig($list_type=0)
	{
		/** /
		$list_type:
		0 => all
		1 => active
		2 => inactive
		/**/
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the BhashSMS configuration";
		if($this->db_conn)
		{
		   $query = 'select CONFIG_ID, USERNAME, PASSWORD, SENDERID, PRIORITY, STATUS from BHASHSMS_CONFIGURATION';
		   if($list_type==1) {
			   $query = 'select CONFIG_ID, USERNAME, PASSWORD, SENDERID, PRIORITY, STATUS from BHASHSMS_CONFIGURATION where STATUS=1';
		   } else if($list_type==2) {
			   $query = 'select CONFIG_ID, USERNAME, PASSWORD, SENDERID, PRIORITY, STATUS from BHASHSMS_CONFIGURATION where STATUS=0';
		   }
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
			   $bhashSMS_details = array();
                while(!$result->EOF) {
					$config_id = $result->fields[0];
					$username = $result->fields[1];
					$password = $result->fields[2];
					$senderid = $result->fields[3];
					$priority = $result->fields[4];
					$status = $result->fields[5];
					$bhashSMS_details[] = array($config_id, $username, $password, $senderid, $priority, $status);
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $bhashSMS_details;
            }
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to connect to the system, please try again later.";
		}
		return $toReturn;
	}

	public function addBhashSMSConfig($username, $password, $senderid, $priority, $status)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to add BhashSMS config";
		if($this->db_conn)
		{
			$query = 'insert into BHASHSMS_CONFIGURATION (CONFIG_ID, USERNAME, PASSWORD, SENDERID, PRIORITY, STATUS) values (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE STATUS=VALUES(STATUS)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array(0, $username, $password, $senderid, $priority, $status));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "BhashSMS configuration saved";
			}			
		}
		return $to_return;
	}

	public function modifyBhashSMSConfig($config_id, $username, $password, $senderid, $priority, $status)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to update BhashSMS config";
		if($this->db_conn)
		{
			$query = 'update BHASHSMS_CONFIGURATION set USERNAME=?, PASSWORD=?, SENDERID=?, PRIORITY=?, STATUS=? where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($username, $password, $senderid, $priority, $status, $config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "BhashSMS configuration updated successfully";
			}			
		}
		return $to_return;
	}

	public function deleteBhashSMSConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the BhashSMS config";
		if($this->db_conn)
		{
			$query = 'delete from BHASHSMS_CONFIGURATION where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "BhashSMS configuration deleted successfully";
			}			
		}
		return $to_return;
	}
	
	public function enableBhashSMSConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to enable BhashSMS config";
		if($this->db_conn)
		{
			$query = 'update BHASHSMS_CONFIGURATION set STATUS=1 where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "BhashSMS configuration enabled successfully";
			}			
		}
		return $to_return;
	}

	public function disableBhashSMSConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to disable BhashSMS config";
		if($this->db_conn)
		{
			$query = 'update BHASHSMS_CONFIGURATION set STATUS=0 where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "BhashSMS configuration disabled successfully";
			}			
		}
		return $to_return;
	}

	public function getNexmoConfig($list_type=0)
	{
		/** /
		$list_type:
		0 => all
		1 => active
		2 => inactive
		/**/
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the Nexmo configuration";
		if($this->db_conn)
		{
		   $query = 'select CONFIG_ID, API_KEY, API_SECRET, FROM_NUMBER, STATUS from NEXMO_CONFIGURATION';
		   if($list_type==1) {
			   $query = 'select CONFIG_ID, API_KEY, API_SECRET, FROM_NUMBER, STATUS from NEXMO_CONFIGURATION where STATUS=1';
		   } else if($list_type==2) {
			   $query = 'select CONFIG_ID, API_KEY, API_SECRET, FROM_NUMBER, STATUS from NEXMO_CONFIGURATION where STATUS=0';
		   }
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
			   $nexmo_details = array();
                while(!$result->EOF) {
					$config_id = $result->fields[0];
					$api_key = $result->fields[1];
					$api_secret = $result->fields[2];
					$from_number = $result->fields[3];
					$status = $result->fields[4];
					$nexmo_details[] = array($config_id, $api_key, $api_secret, $from_number, $status);
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $nexmo_details;
            }
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to connect to the system, please try again later.";
		}
		return $toReturn;
	}

	public function addNexmoConfig($api_key, $api_secret, $from_number, $status)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to add nexmo config";
		if($this->db_conn)
		{
			$query = 'insert into NEXMO_CONFIGURATION (CONFIG_ID, API_KEY, API_SECRET, FROM_NUMBER, STATUS) values (?,?,?,?,?) ON DUPLICATE KEY UPDATE STATUS=VALUES(STATUS)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array(0, $api_key, $api_secret, $from_number, $status));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Nexmo configuration saved";
			}			
		}
		return $to_return;
	}

	public function modifyNexmoConfig($config_id, $api_key, $api_secret, $from_number, $status)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to update nexmo config";
		if($this->db_conn)
		{
			$query = 'update NEXMO_CONFIGURATION set API_KEY=?, API_SECRET=?, FROM_NUMBER=?, STATUS=? where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($api_key, $api_secret, $from_number, $status, $config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Nexmo configuration updated successfully";
			}			
		}
		return $to_return;
	}

	public function deleteNexmoConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the nexmo config";
		if($this->db_conn)
		{
			$query = 'delete from NEXMO_CONFIGURATION where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Nexmo configuration deleted successfully";
			}			
		}
		return $to_return;
	}
	
	public function enableNexmoConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to enable nexmo config";
		if($this->db_conn)
		{
			$query = 'update NEXMO_CONFIGURATION set STATUS=1 where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Nexmo configuration enabled successfully";
			}			
		}
		return $to_return;
	}

	public function disableNexmoConfig($config_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to disable nexmo config";
		if($this->db_conn)
		{
			$query = 'update NEXMO_CONFIGURATION set STATUS=0 where CONFIG_ID=?';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array($config_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Nexmo configuration disabled successfully";
			}			
		}
		return $to_return;
	}
}

?>