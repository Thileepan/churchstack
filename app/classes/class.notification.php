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
}
?>