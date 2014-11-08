<?php

class Notification
{
	protected $db_conn;
	private $APPLICATION_PATH;
	private $time_zone;
	
	public function __construct($APPLICATION_PATH, $shardedDBName="")
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true, $shardedDBName);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}


		$this->time_zone = "UTC";//Default is this
		$this->time_zone = ((isset($_SESSION["churchTimeZone"]) && trim($_SESSION["churchTimeZone"]) != "")? trim($_SESSION["churchTimeZone"]) : $this->time_zone);
	}

	public function setTimeZone($time_zone)
	{
		if(trim($time_zone) != "") {
			$this->time_zone = $time_zone;
		}
	}

	public function insertGreetingsNotificationReport($email_or_sms, $notify_for, $for_occurrence)
	{
		//$email_or_sms : 1->Email, 2->SMS
		//$notify_for : 1->Birthday, 2->Wedding Anniversary
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#EMAIL_BIRTHDAY_GREETINGS#";
		if($email_or_sms == 1) {
			if($notify_for==1) {
				$notification_type = "#EMAIL_BIRTHDAY_GREETINGS#";
			} else if($notify_for==2) {
				$notification_type = "#EMAIL_WEDDING_GREETINGS#";
			}
		} else if($email_or_sms == 2) {
			if($notify_for==1) {
				$notification_type = "#SMS_BIRTHDAY_GREETINGS#";
			} else if($notify_for==2) {
				$notification_type = "#SMS_WEDDING_GREETINGS#";
			}
		}
		if($this->db_conn)
		{
			$query = 'insert into GREETINGS_AUTO_NOTIFY_REPORTS (NOTIFICATION_TYPE, FOR_OCCURRENCE, UPDATED_ON) values(?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isGreetingsNotificationSent($email_or_sms, $notify_for, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		//$email_or_sms : 1->Email, 2->SMS
		//$notify_for : 1->Birthday, 2->Wedding Anniversary
		$notification_type = "#EMAIL_BIRTHDAY_GREETINGS#";
		if($email_or_sms == 1) {
			if($notify_for==1) {
				$notification_type = "#EMAIL_BIRTHDAY_GREETINGS#";
			} else if($notify_for==2) {
				$notification_type = "#EMAIL_WEDDING_GREETINGS#";
			}
		} else if($email_or_sms == 2) {
			if($notify_for==1) {
				$notification_type = "#SMS_BIRTHDAY_GREETINGS#";
			} else if($notify_for==2) {
				$notification_type = "#SMS_WEDDING_GREETINGS#";
			}
		}
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from GREETINGS_AUTO_NOTIFY_REPORTS where NOTIFICATION_TYPE=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function cleanupOldGreetingsNotificationReports($older_than_days=60)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the reports";
		$older_than_days = ((trim($older_than_days) != "" && trim($older_than_days) > 0)? trim($older_than_days) : 60);
		$updated_on_date_threshold = time()-($older_than_days*24*60*60);
		if($this->db_conn)
		{
			$query = 'delete from GREETINGS_AUTO_NOTIFY_REPORTS where UPDATED_ON < FROM_UNIXTIME(?)';
			$result = $this->db_conn->Execute($query, array($updated_on_date_threshold));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Reports deleted successfully";
			}			
		}
		return $to_return;
	}

	public function getPeopleWithBirthdayOn($date_of_birth)
	{
		$profile_list = array();
		if($this->db_conn)
		{
			$query = 'select PROFILE_ID, NAME, MIDDLE_NAME, LAST_NAME, MOBILE1, EMAIL, EMAIL_NOTIFICATION, SMS_NOTIFICATION from PROFILE_DETAILS where DATE_FORMAT(DOB, "%c-%d")=DATE_FORMAT("'.$date_of_birth.'", "%c-%d") and PROFILE_STATUS=1 and (EMAIL_NOTIFICATION=1 or SMS_NOTIFICATION=1)';
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
						$profile_id = $result->fields[0];
                        $first_name = $result->fields[1];
                        $middle_name = $result->fields[2];
                        $last_name = $result->fields[3];
                        $mobile = $result->fields[4];
                        $email = $result->fields[5];
                        $email_notification = $result->fields[6];
                        $sms_notification = $result->fields[7];
						$profile_list[] = array($profile_id, $first_name, $middle_name, $last_name, $mobile, $email, $email_notification, $sms_notification);

						$result->MoveNext();                        
                    }
                }
            }
        }
		return $profile_list;
	}

	public function getPeopleWithWeddingOn($wedding_date)
	{
		$profile_list = array();
		if($this->db_conn)
		{
			$query = 'select PROFILE_ID, NAME, MIDDLE_NAME, LAST_NAME, MOBILE1, EMAIL, EMAIL_NOTIFICATION, SMS_NOTIFICATION from PROFILE_DETAILS where DATE_FORMAT(MARRIAGE_DATE, "%c-%d")=DATE_FORMAT("'.$wedding_date.'", "%c-%d") and PROFILE_STATUS=1 and (EMAIL_NOTIFICATION=1 or SMS_NOTIFICATION=1) and MARITAL_STATUS=2';//Married only
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
						$profile_id = $result->fields[0];
                        $first_name = $result->fields[1];
                        $middle_name = $result->fields[2];
                        $last_name = $result->fields[3];
                        $mobile = $result->fields[4];
                        $email = $result->fields[5];
                        $email_notification = $result->fields[6];
                        $sms_notification = $result->fields[7];
						$profile_list[] = array($profile_id, $first_name, $middle_name, $last_name, $mobile, $email, $email_notification, $sms_notification);

						$result->MoveNext();                        
                    }
                }
            }
        }
		return $profile_list;
	}

	public function saveAnniversaryGreetingsSettings($is_birthday_email_greetings_enabled, $birthday_email_template_id, $is_birthday_sms_greetings_enabled, $birthday_sms_template_id, $is_wedding_email_greetings_enabled, $wedding_email_template_id, $is_wedding_sms_greetings_enabled, $wedding_sms_template_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to update settings";
		if($this->db_conn)
		{
			$query = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result = $this->db_conn->Execute($query, array("BIRTHDAY_GREETINGS", "EMAIL_GREETINGS_ENABLED", $is_birthday_email_greetings_enabled, ""));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}

			$query_1 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_1 = $this->db_conn->Execute($query_1, array("BIRTHDAY_GREETINGS", "EMAIL_GREETINGS_TEMPLATE_ID", $birthday_email_template_id, ""));
			if($result_1) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}			

			$query_2 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_2 = $this->db_conn->Execute($query_2, array("BIRTHDAY_GREETINGS", "SMS_GREETINGS_ENABLED", $is_birthday_sms_greetings_enabled, ""));
			if($result_2) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}

			$query_3 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_3 = $this->db_conn->Execute($query_3, array("BIRTHDAY_GREETINGS", "SMS_GREETINGS_TEMPLATE_ID", $birthday_sms_template_id, ""));
			if($result_3) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}

			$query_4 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_4 = $this->db_conn->Execute($query_4, array("WEDDING_GREETINGS", "EMAIL_GREETINGS_ENABLED", $is_wedding_email_greetings_enabled, ""));
			if($result_4) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}

			$query_5 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_5 = $this->db_conn->Execute($query_5, array("WEDDING_GREETINGS", "EMAIL_GREETINGS_TEMPLATE_ID", $wedding_email_template_id, ""));
			if($result_5) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}

			$query_6 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_6 = $this->db_conn->Execute($query_6, array("WEDDING_GREETINGS", "SMS_GREETINGS_ENABLED", $is_wedding_sms_greetings_enabled, ""));
			if($result_6) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}

			$query_7 = 'insert into GLOBAL_CONFIGURATION (FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE) values (?,?,?,?) ON DUPLICATE KEY UPDATE FEATURE_INT_VALUE=VALUES(FEATURE_INT_VALUE), FEATURE_STRING_VALUE=VALUES(FEATURE_STRING_VALUE)';//refer http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html
			$result_7 = $this->db_conn->Execute($query_7, array("WEDDING_GREETINGS", "SMS_GREETINGS_TEMPLATE_ID", $wedding_sms_template_id, ""));
			if($result_7) {
				$to_return[0] = 1;
				$to_return[1] = "Settings updated successfully";
			} else {
				return $to_return;
			}
		}
		return $to_return;
	}

	public function getAnniversaryGreetingsSettings()
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the anniversary greetings settings";
		if($this->db_conn)
		{
		   $query = 'select FEATURE_NAME, FEATURE_KEY, FEATURE_INT_VALUE, FEATURE_STRING_VALUE from GLOBAL_CONFIGURATION where FEATURE_NAME=? or FEATURE_NAME=?';
		   $result = $this->db_conn->Execute($query, array("BIRTHDAY_GREETINGS", "WEDDING_GREETINGS"));
            
           if($result) {
			   $greetings_details = array();
                while(!$result->EOF) {
					$feature_name = $result->fields[0];
					$feature_key = $result->fields[1];
					$feature_int_value = $result->fields[2];
					$feature_string_value = $result->fields[3];
					if($feature_name == "BIRTHDAY_GREETINGS") {
						if($feature_key == "EMAIL_GREETINGS_ENABLED") {
							$greetings_details["BIRTHDAY_EMAIL_GREETINGS_ENABLED"] = $feature_int_value;
						} else if ($feature_key == "EMAIL_GREETINGS_TEMPLATE_ID") {
							$greetings_details["BIRTHDAY_EMAIL_GREETINGS_TEMPLATE_ID"] = $feature_int_value;
						} else if ($feature_key == "SMS_GREETINGS_ENABLED") {
							$greetings_details["BIRTHDAY_SMS_GREETINGS_ENABLED"] = $feature_int_value;
						} else if ($feature_key == "SMS_GREETINGS_TEMPLATE_ID") {
							$greetings_details["BIRTHDAY_SMS_GREETINGS_TEMPLATE_ID"] = $feature_int_value;
						}
					} else if($feature_name == "WEDDING_GREETINGS") {
						if($feature_key == "EMAIL_GREETINGS_ENABLED") {
							$greetings_details["WEDDING_EMAIL_GREETINGS_ENABLED"] = $feature_int_value;
						} else if ($feature_key == "EMAIL_GREETINGS_TEMPLATE_ID") {
							$greetings_details["WEDDING_EMAIL_GREETINGS_TEMPLATE_ID"] = $feature_int_value;
						} else if ($feature_key == "SMS_GREETINGS_ENABLED") {
							$greetings_details["WEDDING_SMS_GREETINGS_ENABLED"] = $feature_int_value;
						} else if ($feature_key == "SMS_GREETINGS_TEMPLATE_ID") {
							$greetings_details["WEDDING_SMS_GREETINGS_TEMPLATE_ID"] = $feature_int_value;
						}
					}
					$result->MoveNext();
				}
				$toReturn[0] = 1;
				$toReturn[1] = $greetings_details;
            }
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to connect to the system, please try again later.";
		}
		return $toReturn;
	}

	public function insertEmailSMSCountReport($email_or_sms, $triggered_for, $raw_content, $count)
	{
		//$email_or_sms : 1->Email, 2->SMS
		//$triggered_for : this is a text like "Events" "Birthday Greetings" etc...
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		if($this->db_conn)
		{
			$query = 'insert into NOTIFICATIONS_EMAIL_SMS_REPORTS (REPORT_ID, EMAIL_OR_SMS, TRIGGERED_FOR, RAW_CONTENT, SENT_TIME, RECIPIENTS_COUNT) values(?,?,?,?, NOW(),?)';
			$result = $this->db_conn->Execute($query, array(0, $email_or_sms, $triggered_for, $raw_content, $count));
			echo $this->db_conn->errorMsg();
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function cleanupOldEmailSMSCountReports($older_than_days=60)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the reports";
		$older_than_days = ((trim($older_than_days) != "" && trim($older_than_days) > 0)? trim($older_than_days) : 60);
		$updated_on_date_threshold = time()-($older_than_days*24*60*60);
		if($this->db_conn)
		{
			$query = 'delete from NOTIFICATIONS_EMAIL_SMS_REPORTS where SENT_TIME < FROM_UNIXTIME(?)';
			$result = $this->db_conn->Execute($query, array($updated_on_date_threshold));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Reports deleted successfully";
			}			
		}
		return $to_return;
	}
}
?>