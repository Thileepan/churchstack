<?php

class Notification
{
	protected $db_conn;
	private $APPLICATION_PATH;
	
	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
	}

	public function getEventsToNotifyNow()
	{
		$events_details = array();
		if($this->db_conn)
		{
			$notifications_list = array();
			$curr_time = time();
			$event_end_time = $curr_time+2764800;//32 days
			$curr_date = date('Y-m-d');
			$event_end_date = date('Y-m-d', $event_end_time);
			$events_obj = new Events($this->APPLICATION_PATH);
			$temp_events_list = $events_obj->getEventOccurrences($curr_date, $event_end_date, "");
			$unique_event_ids = array();
			for($v=0; $v < COUNT($temp_events_list); $v++)
			{
				if(!in_array($temp_events_list[$v]["eventID"], $unique_event_ids)) {
					$unique_event_ids[] = $temp_events_list[$v]["eventID"];
					$temp_events_list_unique[] = $temp_events_list[$v];
				}
			}
			for($u=0; $u < COUNT($temp_events_list_unique); $u++)
			{
				$temp_curr_event_id = $temp_events_list_unique[$u]["eventID"];
				$query_1 = 'select NOTIFICATION_ID, EVENT_ID, NOTIFICATION_PERIOD from EVENT_NOTIFICATIONS where NOTIFICATION_TYPE=1 and EVENT_ID=?';
				$result_1 = $this->db_conn->Execute($query_1, array($temp_curr_event_id));
				if($result_1) {
					if(!$result_1->EOF) {
						while(!$result_1->EOF)
						{
							$notification_id = $result_1->fields[0];
							$event_id = $result_1->fields[1];
							$not_period = $result_1->fields[2];
							$notifications_list[] = array($notification_id, $event_id, $not_period);
							
							$result_1->MoveNext();
						}
					}
				}
			}

			for($i=0; $i < COUNT($notifications_list); $i++)
			{
				$notification_id = $notifications_list[0];
				$event_id_to_notify = $notifications_list[1];
				$not_period = $notifications_list[2];

				$not_period = $not_period+300;//Sending 5 mins advance to avoid delay due to large number of alerts to be sent
				$event_expected_time = $curr_time+$not_period;
				$event_expected_startdate = date('Y-m-d H:i:s', $event_expected_time);
				//$event_expected_starttime = date('Hi', $event_expected_time);

				$is_event_time_reached = 0;
				for($cnt=0; $cnt < COUNT($temp_events_list); $cnt++)
				{
					if($event_id_to_notify==$temp_events_list[$cnt]["eventID"] && $temp_events_list[$cnt]["start"] <= $event_expected_time) {
						$is_event_time_reached = 1;
						break;
					}
				}

				if($is_event_time_reached!=1) {
					continue;
				}

				$query_2 = 'select * from EVENT_DETAILS where EVENT_ID=?';
				$result_2 = $this->db_conn->Execute($query_2, array($event_id_to_notify));
				if($result_2) {
					if(!$result_2->EOF) {
						while(!$result_2->EOF)
						{
							/** /
							EVENT_ID BIGINT UNSIGNED NOT NULL AUTO_INCREMENT=1,
							TITLE VARCHAR(255),
							DESCRIPTION VARCHAR(1024),
							EVENT_LOCATION VARCHAR(255),
							START_DATE DATE,
							END_DATE DATE,
							START_TIME SMALLINT,
							END_TIME SMALLINT,
							RRULE VARCHAR(255),
							PRIORITY TINYINT,
							ORGANISER VARCHAR(255),
							ACCESS_LEVEL TINYINT,
							/**/
							$event_id = $result_2->fields[0];
							$title = $result_2->fields[1];
							$description = $result_2->fields[2];
							$event_location = $result_2->fields[3];
							$start_date = $result_2->fields[4];
							$end_date = $result_2->fields[5];
							$start_time = $result_2->fields[6];
							$end_time = $result_2->fields[7];
							$rrule = $result_2->fields[8];
							$priority = $result_2->fields[9];
							$organiser = $result_2->fields[10];
							$access_level = $result_2->fields[11];
							$events_details[] = array($notification_id, $event_id, $title, $description, $event_location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level);
							
							$result_2->MoveNext();
						}
					}
				}
			}
		}
		else
		{
			$errorToThrow = "NO DB connection available";
			throw new Exception($errorToThrow);
		}

		return $events_details;
	}

	public function sendEmailAlerts()
	{
		$events_details = array();
		try {
			$events_details = getEventsToNotifyNow();
		} catch(Exception $exp) {
			throw $exp;
		}

		for($k=0; $k < COUNT($events_details); $k++)
		{
			$from_email = "notifications@churchstack.com";//Change it later
			$email_list = array();
			$event_participants = array();
			$notification_id = $events_details[$k][0];
			$event_id = $events_details[$k][1];
			if($this->db_conn)
			{
				$query_1 = 'select PARTICIPANT_TYPE, PARTICIPANT_ID from EVENT_PARTICIPANTS where EVENT_ID=?';
				$result_1 = $this->db_conn->Execute($query_1, array($event_id));
				if($result_1) {
					if(!$result_1->EOF) {
						while(!$result_1->EOF)
						{
							$participant_type = $result_1->fields[0];
							$participant_id = $result_1->fields[1];
							$event_participants[] = array($participant_type, $participant_id);
							
							$result_1->MoveNext();
						}
					}
				}
			}

			for($p=0; $p < COUNT($event_participants); $p++)
			{
				if($event_participants[$p][0] == 1)//Group
				{
					$group_id = $event_participants[$p][1];
					if($this->db_conn)
					{
						$query_2 = 'select distinct b.EMAIL,b.NAME,B.MIDDLE_NAME,B.LAST_NAME from GROUP_MEMBERS a, PROFILE_DETAILS b where a.GROUP_ID=? and a.PROFILE_ID=b.PROFILE_ID and b.EMAIL_ALERTS_ENABLED=1';
						$result_2 = $this->db_conn->Execute($query_1, array($group_id));
						if($result_2) {
							if(!$result_2->EOF) {
								while(!$result_2->EOF)
								{
									$email = $result_2->fields[0];
									$first_name = $result_2->fields[1];
									$middle_name = $result_2->fields[2];
									$last_name = $result_2->fields[3];
									$email_list[] = array($email, $first_name, $middle_name, $last_name);
									
									$result_2->MoveNext();
								}
							}
						}
					}
				}
				else if($event_participants[$p][0] == 2)//Individual
				{
					$profile_id = $event_participants[$p][1];
					if($this->db_conn)
					{
						$query_3 = 'select distinct EMAIL,NAME,MIDDLE_NAME,LAST_NAME from PROFILE_DETAILS where PROFILE_ID=? and EMAIL_ALERTS_ENABLED=1';
						$result_3 = $this->db_conn->Execute($query_3, array($profile_id));
						if($result_3) {
							if(!$result_3->EOF) {
								while(!$result_3->EOF)
								{
									$email = $result_3->fields[0];
									$first_name = $result_3->fields[1];
									$middle_name = $result_3->fields[2];
									$last_name = $result_3->fields[3];
									$email_list[] = array($email, $first_name, $middle_name, $last_name);
									
									$result_3->MoveNext();
								}
							}
						}
					}
				}
			}

			//$email_list = array_values(array_unique($email_list));

			//Getting the email template/content to be sent as email
			$subject = "";
			$content = "";
			$attachment_path = "";
			$use_template_file = 0;
			$template_file_path = "";
			/** / 
			if($this->db_conn)
			{
				$query_4 = 'select SUBJECT,CONTENT,ATTACHMENT_PATH,USE_TEMPLATE_FILE,TEMPLATE_FILE_PATH from EVENT_EMAIL_TEMPLATES where EVENT_ID=?';
				$result_4 = $this->db_conn->Execute($query_4, array($event_id));
				if($result_4) {
					if(!$result_4->EOF) {
						//while(!$result_4->EOF)
						{
							$subject = $result_4->fields[0];
							$content = $result_4->fields[1];
							$attachment_path = $result_4->fields[2];
							$use_template_file = $result_4->fields[3];
							$template_file_path = $result_4->fields[4];
							
							//$result_4->MoveNext();
						}
					}
				}
			}
			/**/

			//Assume notifications have been sent and update DB
			if($this->db_conn)
			{
				$query_5 = 'update EVENT_NOTIFICATIONS set IS_NOTIFICATION_SENT=1 where NOTIFICATION_ID=? and NOTIFICATION_TYPE=?';
				$result_5 = $this->db_conn->Execute($query_5, array($notification_id, 1));
			}

			//Sending emails to all the email addresses collected
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From:".$from_email . "\r\n";
			$successful_emails = array();
			$failed_emails = array();
			$csv_success_emails = "";
			$csc_failure_emails = "";
			$sent_time = time();
			for($n=0; $n < COUNT($email_list); $n++)
			{
				$email = $email_list[$n][0];
				$first_name = $email_list[$n][1];
				$middle_name = $email_list[$n][2];
				$last_name = $email_list[$n][3];
				$to_replace_array = array('FIRST_NAME', 'MIDDLE_NAME', 'LAST_NAME');
				$replace_with_array = array($first_name, $middle_name, $last_name);
				$replaced_subject = str_replace($to_replace_array, $replace_with_array, $subject);
				$replaced_content = str_replace($to_replace_array, $replace_with_array, $content);
				//Each line should be separated with a CRLF (\r\n). Lines should not be larger than 70 characters. (Check following URL)
				$replaced_content = wordwrap($replaced_content, 70, "\r\n");//mail() URL : http://php.net/manual/en/function.mail.php
				if(TRUE === mail($email, $replaced_subject, $replaced_content, $headers))
				{
					$successful_emails[] = $email;
					if($csv_success_emails!="") {
						$csv_success_emails .= ",";
					}
					$csv_success_emails .= $email;
				}
				else
				{
					$failed_emails[] = $email;
					if($csc_failure_emails!="") {
						$csc_failure_emails .= ",";
					}
					$csc_failure_emails .= $email;
				}
			}

			//Assume notifications have been sent and update DB
			if($this->db_conn)
			{
				if(trim($csv_success_emails) != "")
				{
					$query_6 = 'insert into NOTIFICATION_REPORT values(?,?,?,?,?,?)';
					$succ_message = "Mail sent successfully";
					$result_6 = $this->db_conn->Execute($query_6, array($notification_id, 1, $csv_success_emails, $sent_time, 1, $succ_message));
				}
				if(trim(($csc_failure_emails) != "")
				{
					$query_7 = 'insert into NOTIFICATION_REPORT values(?,?,?,?,?,?)';
					$fail_message = "Failed sending email";
					$result_7 = $this->db_conn->Execute($query_7, array($notification_id, 1, $csc_failure_emails, $sent_time, 0, $fail_message));
				}

			}
		}
	}

?>