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
			$this->db_conn->SetFetchMode(ADODB_FETCH_NUM);
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

	public function addMassNotification($type, $subject, $content, $is_draft, $created_by, $last_update_user, $last_updated_time, $notification_status, $participant_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to add the notification details.';

		if($this->db_conn)
		{
			$query = 'insert into MASS_NOTIFICATION_DETAILS (NOTIFICATION_TYPE, NOTIFICATION_SUBJECT, NOTIFICATION_CONTENT, IS_DRAFT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME, IS_NOTIFICATION_SENT) values (?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($type, $subject, $content, $is_draft, $created_by, $last_update_user, $last_updated_time, $notification_status));
			//echo $this->db_conn->ErrorMsg();
			if($result) {				
				$notification_id = $this->db_conn->Insert_ID();
				$result = $this->addMassParticipants($notification_id, $participant_details);
				if($result[0] == 1) {
					$return_data[0] = 1;
					$return_data[1] = 'Notification has been added successfully';
				} else {
					$return_data[0] = 0;
					$return_data[1] = 'Notification has been added successfully but the participants details are not updated.';
				}
			}
		}
		return $return_data;
	}

	public function addMassParticipants($notification_id, $participant_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Failed to add the notification participants';

		if($this->db_conn)
		{
			if(is_array($participant_details))
			{
				$total_participants = COUNT($participant_details);
				if($total_participants > 0)
				{
					$query = 'insert into MASS_NOTIFICATION_PARTICIPANTS (NOTIFICATION_ID, PARTICIPANT_TYPE, PARTICIPANT_ID) values ';
					$query_to_append = '';
					$query_values = array();
					//for($i=0; $i<$total_participants; $i++)
					foreach($participant_details as $key => $value)
					{
						for($i=0; $i<COUNT($value); $i++)
						{
							if($query_to_append != '') {
								$query_to_append .= ',';
							}
							$query_to_append .= '(?, ?, ?)';
							$query_values[] = $notification_id;
							$query_values[] = $key;//$participant_details[$i][0];
							//$query_values[] = implode(",", $value);//$participant_details[$i][1];
							$query_values[] = $value[$i][0];
						}
						
					}
					$query .= $query_to_append;

					$result = $this->db_conn->Execute($query, $query_values);
					if($result) {
						$return_data[0] = 1;
						$return_data[1] = 'Notification participants added successfully';
					}
				}
				else
				{
					$return_data[0] = 1;
					$return_data[1] = 'Safely ignoring the participants list as it is empty';
				}
			}			
			else
			{
				$return_data[0] = 1;
				$return_data[1] = 'Safely ignoring the participants list as it is empty';
			}
		}
		return $return_data;
	}

	public function updateMassNotification($notification_id, $type, $subject, $content, $is_draft, $created_by, $last_update_user, $last_updated_time, $notification_status, $participant_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to update the notification details.';

		if($this->db_conn)
		{
			$query = 'update MASS_NOTIFICATION_DETAILS set NOTIFICATION_TYPE=?, NOTIFICATION_SUBJECT=?, NOTIFICATION_CONTENT=?, IS_DRAFT=?, LAST_UPDATE_USER_ID=?, LAST_UPDATE_TIME=?, IS_NOTIFICATION_SENT=? where NOTIFICATION_ID=?';
			$result = $this->db_conn->Execute($query, array($type, $subject, $content, $is_draft, $last_update_user, $last_updated_time, $notification_status, $notification_id));
			if($result) {
				$result = $this->deleteMassNotificationParticipants($notification_id);
				if($result[0] == 1) {
					$result = $this->addMassParticipants($notification_id, $participant_details);
					if($result[0] == 1) {
						$return_data[0] = 1;
						$return_data[1] = 'Notification has been updated successfully';
					} else {
						$return_data[0] = 0;
						$return_data[1] = 'Notification has been updated successfully but the participants details are not updated.';
					}
				}
			}
		}
		return $return_data;
	}

	public function getMassNotificationInformation($notification_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to get the notification details.';

		if($this->db_conn)
		{
			$notification_details = array();
			$query1 = 'select NOTIFICATION_ID, NOTIFICATION_TYPE, NOTIFICATION_SUBJECT, NOTIFICATION_CONTENT, IS_DRAFT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME, IS_NOTIFICATION_SENT from MASS_NOTIFICATION_DETAILS where NOTIFICATION_ID=?';
			$query2 = 'select a.PARTICIPANT_TYPE, a.PARTICIPANT_ID, b.NAME, b.MIDDLE_NAME, b.LAST_NAME, b.EMAIL, b.MOBILE1 from MASS_NOTIFICATION_PARTICIPANTS as a, PROFILE_DETAILS as b where a.NOTIFICATION_ID=? and a.PARTICIPANT_ID=b.PROFILE_ID and a.PARTICIPANT_TYPE=1';
			$query3 = 'select a.PARTICIPANT_TYPE, a.PARTICIPANT_ID, b.GROUP_NAME from MASS_NOTIFICATION_PARTICIPANTS as a, GROUP_DETAILS as b where a.NOTIFICATION_ID=? and a.PARTICIPANT_ID=b.GROUP_ID and a.PARTICIPANT_TYPE=2';

			$result = $this->db_conn->Execute($query1, array($notification_id));
			
			if($result) {
                if(!$result->EOF) {
                    $notification_details[0] = array($result->fields[0], $result->fields[1], $result->fields[2], $result->fields[3], $result->fields[4], $result->fields[5], $result->fields[6], $result->fields[7], $result->fields[8]);
					$notification_details[1] = array();

					$result2 = $this->db_conn->Execute($query2, array($notification_id));
					$result3 = $this->db_conn->Execute($query3, array($notification_id));
					if($result2) {
						if(!$result2->EOF) {
							while(!$result2->EOF) {
								$notification_details[1][] = array($result2->fields[0], $result2->fields[1], $result2->fields[2], $result2->fields[3], $result2->fields[4], $result2->fields[5], $result2->fields[6]);
								$result2->MoveNext();
							}
						}
					}

					if($result3) {
						if(!$result3->EOF) {
							while(!$result3->EOF) {
								$notification_details[1][] = array($result3->fields[0], $result3->fields[1], $result3->fields[2]);
								$result3->MoveNext();
							}
						}
					}

					$return_data[0] = 1;
					$return_data[1] = $notification_details;
				}
				else {
					$return_data[0] = 0;
					$return_data[1] = 'No notification is available.';
				}
            }
		}
		return $return_data;
	}

	public function deleteMassNotificationDetails($notification_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to delete the notification details.';

		if($this->db_conn)
		{
			$return_data = $this->deleteMassNotificationParticipants($notification_id);
			if($return_data[0] == 1)
			{
				$query = 'delete from MASS_NOTIFICATION_DETAILS where NOTIFICATION_ID=?';
				$result = $this->db_conn->Execute($query, array($notification_id));
				if($result) {
					$return_data[0] = 1;
					$return_data[1] = 'Notification details has been deleted successfully.';
				}
			}
		}
		return $return_data;
	}

	private function deleteMassNotificationParticipants($notification_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to delete the notification participants.';

		if($this->db_conn)
		{
			$query = 'delete from MASS_NOTIFICATION_PARTICIPANTS where NOTIFICATION_ID=?';
			$result = $this->db_conn->Execute($query, array($notification_id));
			if($result) {
				$return_data[0] = 1;
				$return_data[1] = 'Notification participants has been deleted successfully.';
			}
		}
		return $return_data;
	}

	public function getAllMassNotification($filter_by_drafts, $filter_by_sent_items, $filter_by_notification_type)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the notification details.';

		if($this->db_conn)
		{
			$notification_details = array();
			//$query = 'select a.NOTIFICATION_ID, a.NOTIFICATION_TYPE, a.NOTIFICATION_SUBJECT, a.NOTIFICATION_CONTENT, a.IS_DRAFT, a.CREATED_BY, a.LAST_UPDATE_USER_ID, a.LAST_UPDATE_TIME, a.IS_NOTIFICATION_SENT, b.PARTICIPANT_TYPE, b.PARTICIPANT_ID from MASS_NOTIFICATION_DETAILS as a, MASS_NOTIFICATION_PARTICIPANTS as b where a.NOTIFICATION_ID=b.NOTIFICATION_ID';
			$query = 'select NOTIFICATION_ID, NOTIFICATION_TYPE, NOTIFICATION_SUBJECT, NOTIFICATION_CONTENT, IS_DRAFT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME, IS_NOTIFICATION_SENT from MASS_NOTIFICATION_DETAILS';

			if($filter_by_drafts) {
				$query .= ' where IS_DRAFT=1';
			} else if($filter_by_sent_items) {
				$query .= ' where IS_DRAFT=0';
			}

			if($filter_by_notification_type > 0) {
				$query .= ' and NOTIFICATION_TYPE ='.$notification_type;
			}
			
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
						$notification_details[] = array($result->fields[0], $result->fields[1], $result->fields[2], $result->fields[3], $result->fields[4], $result->fields[5], $result->fields[6], $result->fields[7], $result->fields[8]);

						$result->MoveNext();
                    }
					$return_data[0] = 1;
					$return_data[1] = $notification_details;
                }
				else
				{
					$return_data[0] = 0;
					$return_data[1] = 'No notification is available.';
				}
            }
		}
		return $return_data;
	}

	public function createTemplate($type, $name, $subject, $content, $created_by, $last_update_user, $last_update_time)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to add the template details.';

		if($this->db_conn)
		{
			$query = 'insert into TEMPLATES (TEMPLATE_TYPE, TEMPLATE_NAME, TEMPLATE_SUBJECT, TEMPLATE_CONTENT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME) values (?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($type, $name, $subject, $content, $created_by, $last_update_user, $last_update_time));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				$return_data[0] = 1;
				$return_data[1] = 'Template has been added successfully';				
			}
		}
		return $return_data;
	}

	public function updateTemplate($template_id, $type, $name, $subject, $content, $last_update_user, $last_update_time)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to update the template details.';

		if($this->db_conn)
		{
			$query = 'update TEMPLATES set TEMPLATE_TYPE=?, TEMPLATE_NAME=?, TEMPLATE_SUBJECT=?, TEMPLATE_CONTENT=?, LAST_UPDATE_USER_ID=?, LAST_UPDATE_TIME=? where TEMPLATE_ID=?';
			$result = $this->db_conn->Execute($query, array($type, $name, $subject, $content, $last_update_user, $last_update_time, $template_id));
			if($result) {
				$return_data[0] = 1;
				$return_data[1] = 'Template has been updated successfully';
			}
		}
		return $return_data;
	}

	public function deleteTemplate($template_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to delete the template';
		if($this->db_conn)
		{
			$query = 'delete from TEMPLATES where TEMPLATE_ID=?';
			$result = $this->db_conn->Execute($query, array($template_id));
			if($result) {
				$return_data[0] = 1;
				$return_data[1] = 'Template has been deleted successfully';
			}
		}
		return $return_data;
	}

	public function isTemplateNameExists($template_name)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to find if template with same exists already';

		if($this->db_conn)
		{
			$query = 'select TEMPLATE_ID from TEMPLATES where TEMPLATE_NAME=? limit 1';
			echo $template_name;
			$result = $this->db_conn->Execute($query, array($template_name));
			if($result) {
				if($result->EOF){
					$return_data[0] = 1;
					$return_data[1] = 'Template name not exists already';
					$return_data[2] = 0;
				} else {
					$return_data[0] = 1;
					$return_data[1] = 'Template name exists already';
					$return_data[2] = 1;
				}
			}
		}
		return $return_data;
	}

	public function getAllTemplates($filter_by_template_type)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the template details.';

		if($this->db_conn)
		{
			$template_details = array();
			$query = 'select TEMPLATE_ID, TEMPLATE_TYPE, TEMPLATE_NAME, TEMPLATE_SUBJECT, TEMPLATE_CONTENT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME from TEMPLATES';
			if($filter_by_template_type > 0) {
				$query .= ' where TEMPLATE_TYPE='.$filter_by_template_type;
			}
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $template_details[] = array($result->fields[0], $result->fields[1], $result->fields[2], $result->fields[3], $result->fields[4], $result->fields[5], $result->fields[6], $result->fields[7]);
						
						$result->MoveNext();
                    }
					$return_data[0] = 1;
					$return_data[1] = $template_details;
                }
				else
				{
					$return_data[0] = 0;
					$return_data[1] = 'No template is available.';
				}
            }
		}
		return $return_data;
	}

	public function getTemplateInformation($template_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to get the template details.';

		if($this->db_conn)
		{
			$template_details = array();
			$query = 'select TEMPLATE_ID, TEMPLATE_TYPE, TEMPLATE_NAME, TEMPLATE_SUBJECT, TEMPLATE_CONTENT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME from TEMPLATES where TEMPLATE_ID=?';
			$result = $this->db_conn->Execute($query, array($template_id));
			
			if($result) {
                if(!$result->EOF) {
                    $template_details = array($result->fields[0], $result->fields[1], $result->fields[2], $result->fields[3], $result->fields[4], $result->fields[5], $result->fields[6], $result->fields[7]);
					
					$return_data[0] = 1;
					$return_data[1] = $template_details;
                }
				else
				{
					$return_data[0] = 0;
					$return_data[1] = 'No template is available.';
				}
            }
		}
		return $return_data;
	}

	public function cleanupOldMassNotificationReports($older_than_days=60)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the reports";
		$older_than_days = ((trim($older_than_days) != "" && trim($older_than_days) > 0)? trim($older_than_days) : 60);
		$updated_on_date_threshold = time()-($older_than_days*24*60*60);
		if($this->db_conn)
		{
			$query_1 = 'delete MNP from MASS_NOTIFICATION_PARTICIPANTS as MNP, MASS_NOTIFICATION_DETAILS as MND where MND.IS_NOTIFICATION_SENT=1 and MND.LAST_UPDATE_TIME < FROM_UNIXTIME(?)';
			$result_1 = $this->db_conn->Execute($query_1, array($updated_on_date_threshold));
			if($result_1) {
				$query_2 = 'DELETE FROM MASS_NOTIFICATION_DETAILS WHERE where IS_NOTIFICATION_SENT=1 and  MND.LAST_UPDATE_TIME < FROM_UNIXTIME(?)';
				$result_2 = $this->db_conn->Execute($query_2, array($updated_on_date_threshold));
				if($result_2) {
					$to_return[0] = 1;
					$to_return[1] = "Reports deleted successfully";
				}
			}			
		}
		return $to_return;
	}

	public function getMassNotificationsToSend($email_or_sms=0)
	{
		//$email_or_sms : 0=>Both, 1=>Email, 2=>SMS
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the notification details.';

		if($this->db_conn)
		{
			$notification_details = array();
			$query = 'select NOTIFICATION_ID, NOTIFICATION_TYPE, NOTIFICATION_SUBJECT, NOTIFICATION_CONTENT, IS_DRAFT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME, IS_NOTIFICATION_SENT from MASS_NOTIFICATION_DETAILS where ';

			if($email_or_sms == 1) {
				$query .= ' NOTIFICATION_TYPE=1 and ';
			} else if($email_or_sms == 2) {
				$query .= ' NOTIFICATION_TYPE=2 and ';
			}

			$query .= ' IS_NOTIFICATION_SENT!=1';
			
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
						$notification_details[] = array($result->fields[0], $result->fields[1], $result->fields[2], $result->fields[3], $result->fields[4], $result->fields[5], $result->fields[6], $result->fields[7], $result->fields[8]);

						$result->MoveNext();
                    }
					$return_data[0] = 1;
					$return_data[1] = $notification_details;
                }
				else
				{
					$return_data[0] = 0;
					$return_data[1] = 'No notification is available.';
				}
            }
		}
		return $return_data;
	}

	public function markMassNotificationAsSent($notification_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to update';
		if($this->db_conn)
		{
			$query = 'update MASS_NOTIFICATION_DETAILS set IS_NOTIFICATION_SENT=1 where NOTIFICATION_ID=?';
			$result = $this->db_conn->Execute($query, array($notification_id));
			if($result) {
				$return_data[0] = 1;
				$return_data[1] = 'Data updated';
			}
		}
		return $return_data;
	}

	public function getMassCommRecipients($notification_id)
	{
		include_once $this->APPLICATION_PATH . 'classes/class.profiles.php';
		include_once $this->APPLICATION_PATH . 'classes/class.groups.php';

		$recipient_details = array();
		$unique_list_profile_ids = array();
		if($this->db_conn)
		{
			$profiles_obj = new Profiles($this->APPLICATION_PATH);
			$groups_obj = new Groups($this->APPLICATION_PATH);

			$all_profiles = $profiles_obj->getAllProfiles(1);
//						$profile_details[] = array($profile_id, $salution_id, $name, $unique_id, $dob, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $parent_profile_id, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $email_subscription, $sms_subscription); //(0-32)

			$query = 'select PARTICIPANT_TYPE, PARTICIPANT_ID from MASS_NOTIFICATION_PARTICIPANTS where NOTIFICATION_ID=?';
			$result = $this->db_conn->Execute($query, array($notification_id));

			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $participant_type = $result->fields[0];
                        $participant_id = $result->fields[1];

						if($participant_type == 1)
						{
							//individual profile
							$profile_id = $participant_id;
							if(in_array($profile_id, $unique_list_profile_ids)) {
								$result->MoveNext();
								continue;
							}
							for($p=0; $p < COUNT($all_profiles); $p++) {
								if($profile_id == $all_profiles[$p][0]) {
									$full_name = $all_profiles[$p][2]." ".$all_profiles[$p][26]." ".$all_profiles[$p][27];
									$recipient_details[] = array($full_name, $all_profiles[$p][18], $all_profiles[$p][31], $all_profiles[$p][16], $all_profiles[$p][32], $all_profiles[$p][2], $all_profiles[$p][26], $all_profiles[$p][27]);
									$unique_list_profile_ids[] = $profile_id;
									break;
								}
							}
							/** /
							$profile_details = $profiles_obj->getProfileNameAndEmailID($profile_id);
							if(is_array($profile_details) && COUNT($profile_details) > 0)
							{
								$recipient_details[] = $profile_details;
							}
							/**/
						}
						else if($participant_type == 2)
						{
							//group profile
							$group_id = $participant_id;
							$all_members_of_group = $groups_obj->getListOfGroupMembers($group_id);
							for($g=0; $g < COUNT($all_members_of_group); $g++)
							{
								if(in_array($all_members_of_group[$g][0], $unique_list_profile_ids)) {
									continue;
								}
								for($p=0; $p < COUNT($all_profiles); $p++) {
									if($all_members_of_group[$g][0] == $all_profiles[$p][0]) {
										$full_name = $all_profiles[$p][2]." ".$all_profiles[$p][26]." ".$all_profiles[$p][27];
										$recipient_details[] = array($full_name, $all_profiles[$p][18], $all_profiles[$p][31], $all_profiles[$p][16], $all_profiles[$p][32], $all_profiles[$p][2], $all_profiles[$p][26], $all_profiles[$p][27]);
										$unique_list_profile_ids[] = $all_members_of_group[$g][0];
										break;
									}
								}
							}

							/** /
							$group_member_details = $groups_obj->getGroupMembersNameAndEmailID($group_id);
							if(is_array($group_member_details) && COUNT($group_member_details) > 0)
							{
								for($i=0; $i<COUNT($group_member_details); $i++)
								{
									$recipient_details[] = $group_member_details[$i];
								}
							}
							/**/
						}
						else if($participant_type == 3)//Entire church
						{
							for($p=0; $p < COUNT($all_profiles); $p++) {
								if(in_array($all_profiles[$p][0], $unique_list_profile_ids)) {
									continue;
								}
								$full_name = $all_profiles[$p][2]." ".$all_profiles[$p][26]." ".$all_profiles[$p][27];
								$recipient_details[] = array($full_name, $all_profiles[$p][18], $all_profiles[$p][31], $all_profiles[$p][16], $all_profiles[$p][32], $all_profiles[$p][2], $all_profiles[$p][26], $all_profiles[$p][27]);
								$unique_list_profile_ids[] = $all_profiles[$p][0];
							}
						}
											
						$result->MoveNext();
                    }
                }
            }
		}
		//$recipient_details[] format is : array(fullname, email, emailNotiEnabled, mobile, smsNotiEnabled, firstname, middlename, lastname)
		return $recipient_details;
	}

	public function getMassNotificationContentOnly($notification_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to get the notification details.';

		if($this->db_conn)
		{
			$notification_details = array();
			$query1 = 'select NOTIFICATION_ID, NOTIFICATION_TYPE, NOTIFICATION_SUBJECT, NOTIFICATION_CONTENT, IS_DRAFT, CREATED_BY, LAST_UPDATE_USER_ID, LAST_UPDATE_TIME, IS_NOTIFICATION_SENT from MASS_NOTIFICATION_DETAILS where NOTIFICATION_ID=? limit 1';

			$result = $this->db_conn->Execute($query1, array($notification_id));
			
			if($result) {
                if(!$result->EOF) {
					$return_data[0] = 1;
					$return_data[1] = array($result->fields[0], $result->fields[1], $result->fields[2], $result->fields[3], $result->fields[4], $result->fields[5], $result->fields[6], $result->fields[7], $result->fields[8]);
				}
				else {
					$return_data[0] = 0;
					$return_data[1] = 'No notification is available.';
				}
            }
		}
		return $return_data;
	}
}
?>