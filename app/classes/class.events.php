<?php

class Events
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

		$this->event_id = -1;

		$this->time_zone = "UTC";//Default is this
		$this->time_zone = ((isset($_SESSION["churchTimeZone"]) && trim($_SESSION["churchTimeZone"]) != "")? trim($_SESSION["churchTimeZone"]) : $this->time_zone);
	}

	public function setTimeZone($time_zone)
	{
		if(trim($time_zone) != "") {
			$this->time_zone = $time_zone;
		}
	}

	public function addEvent($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level, $participant_details, $notification_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Failed to add the event';

		if($this->db_conn)
		{
			$this->db_conn->StartTrans();

			$this->event_id = $this->addEventDetails($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level);
			
			//event inserted successfully.
			if($this->event_id > 0)
			{
				$return_data = $this->addEventParticipants($participant_details);
				if($return_data[0] != 1) {
					$this->db_conn->FailTrans();					
				} else {
					$return_data = $this->addEventNotifications($notification_details);
					if($return_data[0] != 1) {
						$this->db_conn->FailTrans();
					}
				}

				//no failed transactions detected
				if(!$this->db_conn->HasFailedTrans())
				{
					$return_data[0] = 1;
					$return_data[1] = 'Event has been added successfully';
				}
				
				//transactions will complete here. Either commit/rollback.
				//echo $this->db_conn->CompleteTrans();
				$this->db_conn->CompleteTrans();
				return $return_data;
			}
		}
		return $return_data;
	}

	public function addEventDetails($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level)
	{
		$event_id = -1;
		if($this->db_conn)
		{
			$query = 'insert into EVENT_DETAILS (TITLE, DESCRIPTION, EVENT_LOCATION, START_DATE, END_DATE, START_TIME, END_TIME, RRULE, PRIORITY, ORGANISER, ACCESS_LEVEL) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level));
			if($result)
			{
				//return the last inserted event_id
				return $this->db_conn->Insert_ID();
			}
		}
		return $event_id;		
	}

	private function addEventParticipants($participant_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Failed to add the event participants';

		if($this->db_conn)
		{
			if(is_array($participant_details))
			{
				$total_participants = COUNT($participant_details);
				if($total_participants > 0)
				{
					$query = 'insert into EVENT_PARTICIPANTS (EVENT_ID, PARTICIPANT_TYPE, PARTICIPANT_ID) values ';
					$query_to_append = '';
					$query_values = array();
					for($i=0; $i<$total_participants; $i++)
					{
						if($query_to_append != '') {
							$query_to_append .= ',';
						}
						$query_to_append .= '(?, ?, ?)';
						$query_values[] = $this->event_id;
						$query_values[] = $participant_details[$i][0];
						$query_values[] = $participant_details[$i][1];
					}
					$query .= $query_to_append;

					$result = $this->db_conn->Execute($query, $query_values);
					if($result) {
						$return_data[0] = 1;
						$return_data[1] = 'Event participants added successfully';
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

	private function addEventNotifications($notification_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Failed to add the event notifications';
		if(!is_array($notification_details) || COUNT($notification_details) <= 0)
		{
			$return_data[0] = 1;
			$return_data[1] = 'Event has been added/updated successfully';
			return $return_data;
		}

		if($this->db_conn)
		{
			if(is_array($notification_details))
			{
				$total_notifications = COUNT($notification_details);
				if($total_notifications > 0)
				{
					$query = 'insert into EVENT_NOTIFICATIONS (EVENT_ID, NOTIFICATION_TYPE, NOTIFICATION_PERIOD) values ';
					$query_to_append = '';
					$query_values = array();
					for($i=0; $i<$total_notifications; $i++)
					{
						if($query_to_append != '') {
							$query_to_append .= ',';
						}
						$query_to_append .= '(?, ?, ?)';
						$query_values[] = $this->event_id;
						$query_values[] = $notification_details[$i][0];
						$query_values[] = $notification_details[$i][1];
					}
					$query .= $query_to_append;

					$result = $this->db_conn->Execute($query, $query_values);
					echo $this->db_conn->ErrorMsg();
					if($result) {
						$return_data[0] = 1;
						$return_data[1] = 'Event notifications has been added successfully';
					}
				}
			}			
		}
		return $return_data;
	}

	public function updateEvent($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level, $participant_details, $notification_details)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Failed to update the event';

		$status = $this->updateEventDetails($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level);
		if($status)
		{
			if($this->deleteEventParticipants())
			{
				$return_data = $this->addEventParticipants($participant_details);
				if($return_data[0] == 1)
				{
					if($this->deleteEventNotifications()) {
						$this->addEventNotifications($notification_details);
						$return_data[0] = 1;
						$return_data[1] = 'Event has been updated successfully';
					}
				}			
			}
		}
		return $return_data;
	}

	private function updateEventDetails($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level)
	{
		if($this->db_conn)
		{
			$query = 'update EVENT_DETAILS set TITLE=?, DESCRIPTION=?, EVENT_LOCATION=?, START_DATE=?, END_DATE=?, START_TIME=?, END_TIME=?, RRULE=?, PRIORITY=?, ORGANISER=?, ACCESS_LEVEL=? where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level, $this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	private function updateEventParticipants($participant_type, $participant_period, $participant_email)
	{
		if($this->db_conn)
		{
			$query = 'update EVENT_PARTICIPANTS set PARTICIPANT_TYPE=?, PARTICIPANT_PERIOD=?, PARTICIPANT_EMAIL=? where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($participant_type, $participant_period, $participant_email, $this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	private function updateEventNotifications($notification_type, $notification_period)
	{
		if($this->db_conn)
		{
			$query = 'update EVENT_NOTIFICATIONS set NOTIFICATION_TYPE=?, NOTIFICATION_PERIOD=? where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($notification_type, $notification_period, $this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteEvent()
	{
		//delete event releated table entries first before deleting the main table.
		if($this->deleteEventParticipants())
		{
			if($this->deleteEventNotifications())
			{
				if($this->deleteEventDetails()) {
					return true;
				}
			}
		}
		return false;
	}

	private function deleteEventParticipants()
	{
		if($this->db_conn)
		{
			$query = 'delete from EVENT_PARTICIPANTS where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	private function deleteEventNotifications()
	{
		if($this->db_conn)
		{
			$query = 'delete from EVENT_NOTIFICATIONS where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteEventDetails()
	{
		if($this->db_conn)
		{
			$query = 'delete from EVENT_DETAILS where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getEventInformation($event_id)
	{
		$event_details = array();
		if($this->db_conn)
		{
			$query1 = 'select * from EVENT_DETAILS where EVENT_ID = ?';
			$query2 = 'select a.PARTICIPANT_ID, a.PARTICIPANT_TYPE, CONCAT_WS(" ", b.NAME, b.MIDDLE_NAME, b.LAST_NAME) from EVENT_PARTICIPANTS as a, PROFILE_DETAILS as b where a.PARTICIPANT_ID = b.PROFILE_ID and a.EVENT_ID = ?';
			$query3 = 'select * from EVENT_NOTIFICATIONS where EVENT_ID = ?';
			$result1 = $this->db_conn->Execute($query1, array($event_id));

			if($result1) {
                if(!$result1->EOF) {
					$event_id = $result1->fields[0];
					$title = $result1->fields[1];
					$description = $result1->fields[2];
					$location = $result1->fields[3];
					$start_date = $result1->fields[4];
					$end_date = $result1->fields[5];
					$start_time = $result1->fields[6];
					$end_time = $result1->fields[7];
					$rrule = $result1->fields[8];
					$priority = $result1->fields[9];
					$organiser = $result1->fields[10];
					$access_level = $result1->fields[11];

					$result2 = $this->db_conn->Execute($query2, array($event_id));
					if($result2) {
						if(!$result2->EOF) {
							while(!$result2->EOF)
							{
								$participant_type = $result2->fields[0];
								$participant_id = $result2->fields[1];
								$participant_name = $result2->fields[2];
								$participants[] = array($participant_type, $participant_id, $participant_name);
								$result2->MoveNext();
							}
						}
					}
					
					$result3 = $this->db_conn->Execute($query3, array($event_id));
					if($result3) {
						if(!$result3->EOF) {
							while(!$result3->EOF)
							{
								$notification_id = $result3->fields[1];
								$notification_type = $result3->fields[2];
								$notification_period = $result3->fields[3];
								$notifications[] = array($notification_id, $notification_type, $notification_period);
								$result3->MoveNext();
							}
						}
					}
					$event_details = array($event_id, $title, $description, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level, $participants, $notifications);
				}
            }
		}
		return $event_details;
	}

	public function getAllEvents($list_all_events, $start_date, $end_date, $start_time='0000', $end_time='0000', $event_status=1)
	{
		$event_details = array();
		if($this->db_conn)
		{
			if(!$list_all_events) {
				//$query = 'select * from EVENT_DETAILS where (START_DATE < ? and END_DATE > ?) || (START_DATE > ? and START_DATE < ? and END_DATE = ?)';
				//$result = $this->db_conn->Execute($query, array($end_date, $start_date, $start_date, $end_date, '0000-00-00'));
				$query = 'select * from EVENT_DETAILS where (START_DATE < ? and END_DATE > ?) || (START_DATE > ? and START_DATE < ? and END_DATE = ?) || (START_DATE < ? and END_DATE = ?)';
				$result = $this->db_conn->Execute($query, array($end_date, $start_date, $start_date, $end_date, '0000-00-00', $start_date, '0000-00-00'));
			} else {				
				if($event_status == 1) {
					//upcoming events
					$query = 'select * from EVENT_DETAILS where (END_DATE >= ? and END_DATE != ?) || (START_DATE >= ? and END_DATE = ?) || (START_DATE <= ? and END_DATE = ?)';
					$result = $this->db_conn->Execute($query, array($end_date, '0000-00-00', $end_date, '0000-00-00', $end_date, '0000-00-00'));
				} else if($event_status == 2) {
					//past events
					$query = 'select * from EVENT_DETAILS where (END_DATE < ? and END_DATE != ?)';
					$result = $this->db_conn->Execute($query, array($end_date, '0000-00-00'));
				}				
			}

			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $event_id = $result->fields[0];
                        $title = $result->fields[1];
						$description = $result->fields[2];
						$location = $result->fields[3];
                        $start_date = $result->fields[4];
                        $end_date = $result->fields[5];
						$start_time = $result->fields[6];
						$end_time = $result->fields[7];
						$rrule = $result->fields[8];
						$priority = $result->fields[9];
						$organiser = $result->fields[10];
						$access_level = $result->fields[11];
						$event_details[] = array($event_id, $title, $description, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level);
                        
						$result->MoveNext();
                    }
                }
            }
		}
		return $event_details;
	}

	public function getEventOccurrences($start_date, $end_date, $time_zone, $virtualLimit=50)
	{
		include_once $this->APPLICATION_PATH . 'classes/class.recurr.php';
		include_once $this->APPLICATION_PATH . 'utils/utilfunctions.php';

		$event_info = array();
		$list_all_events = false;
		$event_details = $this->getAllEvents($list_all_events, $start_date, $end_date);
		//print_r($event_details);
		$total_events = COUNT($event_details);
		//echo "eventdetails::::::::".$total_events;
		if(is_array($event_details) && $total_events > 0)
		{
			$time_zone = $_SESSION['churchTimeZone'];
			$dt = Carbon::now($time_zone);
			$today = $dt->year."-".$dt->month."-".$dt->day;
			$today_timestamp = $dt->timestamp;

			// Recurr Transformer Initialization
			$type = 2;
			$recurr_obj = new RecurrInterface($this->APPLICATION_PATH);
			$recurr_obj->setUp($type);
			$recurr_obj->setTimeZone($this->time_zone);

			for($i=0; $i<$total_events; $i++)
			{
				//$start_date = $event_details[$i][4];
				$event_id = $event_details[$i][0];
				$rrule = $event_details[$i][8];
				$title = $event_details[$i][1];
				$start_date_db = $event_details[$i][4];
				$end_date_db = $event_details[$i][5];
				$start_time = convertRailwayTimeToFullTime($event_details[$i][6]);
				$end_time = convertRailwayTimeToFullTime($event_details[$i][7]);
				$location = $event_details[$i][3];
				$organiser = $event_details[$i][10];

				if($rrule != '')
				{
					$recurr_obj->setRRule($rrule);
					$recurr_obj->setStartDate($start_date_db);
					$recurr_obj->setEndDate($end_date_db);
					$recurr_obj->setBetweenConstraintDate($start_date, $end_date, true);
					$recurr_obj->setVirtualLimit($virtualLimit);
					$occurrences = $recurr_obj->getOccurrences();
					$rrule_text = $recurr_obj->getRRuleText();
					//print_r($occurrences);

					if(is_array($occurrences))
					{
						$total_occurrence = COUNT($occurrences);
						if($total_occurrence > 0)
						{
							for($j=0; $j<$total_occurrence; $j++)
							{
								$event_date = $occurrences[$j]->getStart()->format("Y-m-d");
								//echo $event_date.":::".$start_date.":::".$end_date."<BR>";
								
								if($event_date >= $start_date && $event_date <= $end_date)
								{
									$event_start_date = $event_date. ' ' . $start_time;
									$event_end_date = $event_date. ' '. $end_time;
									$event_info[] = array('start'=>$event_start_date, 'end'=>$event_end_date, 'allDay'=>false, "eventID"=>$event_id, 'title'=>$title, 'info'=>$rrule_text, 'location'=>$location, 'organiser'=>$organiser);
								}
							}
						}
					}
				}
				else
				{
					$event_start_date = $start_date_db. ' ' . $start_time;
					$event_end_date = $end_date_db. ' '. $end_time;
					$event_info[] = array('start'=>$event_start_date, 'end'=>$event_end_date, 'allDay'=>false, "eventID"=>$event_id, 'title'=>$title, 'info'=>'today only', 'location'=>$location, 'organiser'=>$organiser);
				}
			}
		}

		return $event_info;
	}

	public function getNextImmediateEventDate($is_obj_initialized, $recurr_obj, $start_date, $end_date, $start_time, $end_time, $time_zone, $rrule, $virtualLimit, $today_with_time)
	{
		$event_date = '';
		if(!$is_obj_initialized)
		{
			include_once $this->APPLICATION_PATH . 'classes/class.recurr.php';

			// Recurr Transformer Initialization
			$type = 2;
			$recurr_obj = new RecurrInterface($this->APPLICATION_PATH);
			$recurr_obj->setUp($type);
			$recurr_obj->setTimeZone($this->time_zone);
		} else {
			if(!is_object($recurr_obj)) {
				return $event_date;
			}
		}
		
		if($rrule != '')
		{
			$recurr_obj->setRRule($rrule);
			$recurr_obj->setStartDate($start_date);
			$recurr_obj->setEndDate($end_date);
			$recurr_obj->setStartTime($start_time);
			$recurr_obj->setEndTime($end_time);
			$recurr_obj->setAfterConstraintDate($today_with_time, true);
			$recurr_obj->setVirtualLimit($virtualLimit);
			$occurrences = $recurr_obj->getOccurrences();
			//print_r($occurrences);

			if(is_array($occurrences))
			{
				$total_occurrence = COUNT($occurrences);
				if($total_occurrence > 0)
				{
					$event_date = $occurrences[0]->getStart()->format("Y-m-d");	
					$event_date .= " ".$start_time;					
				}
			}
		}

		return $event_date;
	}

	public function getUpcomingEventNotificationDetails($today)
	{
		$event_details = array();
		if($this->db_conn)
		{
			$query = 'select a.EVENT_ID, a.TITLE, a.DESCRIPTION, a.EVENT_LOCATION, a.START_DATE, a.END_DATE, a.START_TIME, a.END_TIME, a.RRULE, a.PRIORITY, a.ORGANISER, a.ACCESS_LEVEL, b.NOTIFICATION_ID, b.NOTIFICATION_TYPE, b.NOTIFICATION_PERIOD from EVENT_DETAILS as a, EVENT_NOTIFICATIONS as b where ((a.END_DATE >= ? and a.END_DATE != ?) || (a.START_DATE >= ? and a.END_DATE = ?) || (a.START_DATE <= ? and a.END_DATE = ?)) and a.EVENT_ID=b.EVENT_ID';
			$result = $this->db_conn->Execute($query, array($today, '0000-00-00', $today, '0000-00-00', $today, '0000-00-00'));

			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $event_id = $result->fields[0];
                        $title = $result->fields[1];
						$description = $result->fields[2];
						$location = $result->fields[3];
                        $start_date = $result->fields[4];
                        $end_date = $result->fields[5];
						$start_time = $result->fields[6];
						$end_time = $result->fields[7];
						$rrule = $result->fields[8];
						$priority = $result->fields[9];
						$organiser = $result->fields[10];
						$access_level = $result->fields[11];
						$notification_id = $result->fields[12];
						$notification_type = $result->fields[13];
						$notification_period = $result->fields[14];						
						$event_details[] = array($event_id, $title, $description, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level, $notification_id, $notification_type, $notification_period);
                        
						$result->MoveNext();
                    }
                }
            }
		}
		return $event_details;
	}

	public function getEventsToNotifyNow()
	{
		//$notification_type=>1 : EMAIL
		//$notification_type=>2 : SMS
		include_once $this->APPLICATION_PATH . 'plugins/carbon/src/Carbon/Carbon.php';
		include_once $this->APPLICATION_PATH . 'classes/class.recurr.php';
		include_once $this->APPLICATION_PATH . 'utils/utilfunctions.php';
		
		$time_zone = $this->time_zone;
		$current_time = Carbon::now($this->time_zone);
		$today = $current_time->year."-".$current_time->month."-".$current_time->day;	
		$today_with_time = $today." ".$current_time->hour.":".$current_time->minute.":".$current_time->second;

		$to_return = array();
		//$notification_type = 1; //EMAIL NOTIFICATIONS
		$event_details = $this->getUpcomingEventNotificationDetails($today);
		if(is_array($event_details))
		{
			$total_events = COUNT($event_details);
			if($total_events > 0)
			{
				//Recurr Transformer Initialization
				$type = 2;
				$is_obj_initialized = true;
				$recurr_obj = new RecurrInterface($this->APPLICATION_PATH);
				$recurr_obj->setUp($type);
				$recurr_obj->setTimeZone($this->time_zone);
				for($i=0; $i<$total_events; $i++)
				{
					//find the next immediate event occurrence
					$next_event_date = '-';
					$virtualLimit = 1;
					$event_id = $event_details[$i][0];
					$title = $event_details[$i][1];
					$description = $event_details[$i][2];
					$location = $event_details[$i][3];
                    $start_date = $event_details[$i][4];
					$end_date = $event_details[$i][5];
					$start_time = convertRailwayTimeToFullTime($event_details[$i][6]);
					$end_time = convertRailwayTimeToFullTime($event_details[$i][7]);
					$rrule = $event_details[$i][8];
					$priority = $event_details[$i][9];
					$organiser = $event_details[$i][10];
					$access_level = $event_details[$i][11];
					$notification_id = $event_details[$i][12];
					$notification_type = $event_details[$i][13];
					$notification_period = $event_details[$i][14];
					
					if($rrule != '') {
						$next_event_date = $this->getNextImmediateEventDate($is_obj_initialized, $recurr_obj, $today, $end_date, $start_time, $end_time, $this->time_zone, $rrule, $virtualLimit, $today_with_time);					
					} else {
						$next_event_date = $start_date." ".$start_time;
					}

					if($next_event_date != '-')
					{
						$event_time = Carbon::createFromFormat('Y-m-d H:i:s', $next_event_date, $this->time_zone);
						$diff_in_seconds = $current_time->diffInSeconds($event_time);
						//echo "Current Time::".$current_time."<BR>";
						//echo "Next Event Time::".$event_time."<BR>";
						//echo "Diff In Seconds::".$diff_in_seconds."<BR>";
						//exit;

						$notification_period = $notification_period+2100;//Taking list 35 mins advance to avoid possible missing of notifications. Cron job runs every 30 mins when making this change.
						if($notification_period >= $diff_in_seconds)
						{
							$notification_details = array();
							$notification_details["event_attendees"] = array();
							$notification_details["event_email_recipients"] = array();
							$notification_details["event_sms_recipients"] = array();
							$notification_details["notification_type"] = $notification_type;
							$recipient_details = $this->getEventRecipients($event_id);
							for($j=0; $j<COUNT($recipient_details); $j++)
							{
								$notification_details["event_attendees"][] = $recipient_details[$j][0];
								if($notification_type == 1 && $recipient_details[$j][2] == 1) {//Email Notification enabled
									$notification_details["event_email_recipients"][] = $recipient_details[$j][1];
								} else if($notification_type == 2 && $recipient_details[$j][4] == 1) {//SMS Notification enabled
									$notification_details["event_sms_recipients"][] = $recipient_details[$j][3];
								}
							}
							$notification_details["event_organizers"] = $organiser;
							$notification_details["event_title"] = $title;
							$notification_details["event_desc"] = $description;
							$notification_details["event_date_time"] = $event_time;
							$notification_details["event_place"] = $location;

							//Following details are very important and should be unique for an event through out the events life time.
							$notification_details["event_id"] = $event_id;
							$notification_details["exact_occurrence_time"] = $next_event_date;//This will be used to avoid sending duplicate emails for the same event.

							$to_return[] = $notification_details;
						}
					}
				}
			}
			
		}
		return $to_return;
	}

	public function getEventRecipients($event_id)
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

			$query = 'select PARTICIPANT_TYPE, PARTICIPANT_ID from EVENT_PARTICIPANTS where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($event_id));

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
									$recipient_details[] = array($full_name, $all_profiles[$p][18], $all_profiles[$p][31], $all_profiles[$p][16], $all_profiles[$p][32]);
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
										$recipient_details[] = array($full_name, $all_profiles[$p][18], $all_profiles[$p][31], $all_profiles[$p][16], $all_profiles[$p][32]);
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
		return $recipient_details;
	}

	public function constructEventReminderEmailBody($event_details_arr)
	{
		$event_remainder = "";
		$event_rem_template_file = $this->APPLICATION_PATH."templates/email/eventreminder.html";		
		if(file_exists($event_rem_template_file))
		{
			$event_remainder = trim(file_get_contents($event_rem_template_file));
			//Prepare the html string for event organizers
			$organizers_row = "";
			$single_organizer_row = "";
			$single_organizer_comm_start = "<!--ORGANIZER_ROW_START";
			$single_organizer_comm_end = "ORGANIZER_ROW_END-->";
			$single_organizer_html_start_pos = strpos($event_remainder, $single_organizer_comm_start);
			$single_organizer_html = substr($event_remainder, $single_organizer_html_start_pos+strlen($single_organizer_comm_start));
			$single_organizer_html_end_pos = strpos($single_organizer_html, $single_organizer_comm_end);
			$single_organizer_html = substr($single_organizer_html, 0, strlen($single_organizer_html)-(strlen($single_organizer_html) - $single_organizer_html_end_pos));
			$single_organizer_row = $single_organizer_html;
			for($k=0; $k < COUNT($event_details_arr["event_organizers_array"]); $k++)
			{
				$single_organizer_row = $single_organizer_html;
				$single_organizer_row = str_replace("{{EVENT_ORGANIZER}}", $event_details_arr["event_organizers_array"][$k], $single_organizer_row);
				$organizers_row .= $single_organizer_row;
			}

			//Prepare the html string for event attendees
			$attendees_row = "";
			$single_attendee_row = "";
			$single_attendee_comm_start = "<!--ATTENDEE_ROW_START";
			$single_attendee_comm_end = "ATTENDEE_ROW_END-->";
			$single_attendee_html_start_pos = strpos($event_remainder, $single_attendee_comm_start);
			$single_attendee_html = substr($event_remainder, $single_attendee_html_start_pos+strlen($single_attendee_comm_start));
			$single_attendee_html_end_pos = strpos($single_attendee_html, $single_attendee_comm_end);
			$single_attendee_html = substr($single_attendee_html, 0, strlen($single_attendee_html)-(strlen($single_attendee_html) - $single_attendee_html_end_pos));
			$single_attendee_row = $single_attendee_html;
			for($k=0; $k < COUNT($event_details_arr["event_attendees_array"]); $k++)
			{
				$single_attendee_row = $single_attendee_html;
				$single_attendee_row = str_replace("{{EVENT_ATTENDEE}}", $event_details_arr["event_attendees_array"][$k], $single_attendee_row);
				$attendees_row .= $single_attendee_row;
			}

			//Replacing place holder with values
			$event_remainder = str_replace("<!--ALL_ORGANIZERS_ROWS-->", $organizers_row, $event_remainder);
			$event_remainder = str_replace("<!--ALL_ATTENDEES_ROWS-->", $attendees_row, $event_remainder);
			$event_remainder = str_replace("{{EVENT_TITLE}}", $event_details_arr["event_title"], $event_remainder);
			$event_remainder = str_replace("{{EVENT_DESC}}", $event_details_arr["event_desc"], $event_remainder);
			$event_remainder = str_replace("{{EVENT_DATE_AND_TIME}}", $event_details_arr["event_date_time"], $event_remainder);
			$event_remainder = str_replace("{{EVENT_PLACE}}", $event_details_arr["event_place"], $event_remainder);
		}
		return $event_remainder;		
	}

	public function insertEmailNotificationReport($event_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#EMAIL_EVENT_REMINDER#";
		if($this->db_conn)
		{
			$query = 'insert into EVENT_AUTO_NOTIFY_REPORTS (NOTIFICATION_TYPE, EVENT_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $event_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isEmailNotificationSent($event_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#EMAIL_EVENT_REMINDER#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from EVENT_AUTO_NOTIFY_REPORTS where NOTIFICATION_TYPE=? and EVENT_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $event_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function cleanupOldEmailNotificationReports($older_than_days=60)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the reports";
		$older_than_days = ((trim($older_than_days) != "" && trim($older_than_days) > 0)? trim($older_than_days) : 60);
		$updated_on_date_threshold = time()-($older_than_days*24*60*60);
		$notification_type = "#EMAIL_EVENT_REMINDER#";
		if($this->db_conn)
		{
			$query = 'delete from EVENT_AUTO_NOTIFY_REPORTS where UPDATED_ON < FROM_UNIXTIME(?)';
			$result = $this->db_conn->Execute($query, array($updated_on_date_threshold));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Reports deleted successfully";
			}			
		}
		return $to_return;
	}

	public function insertSMSNotificationReport($event_id, $for_occurrence)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to insert the report";
		$notification_type = "#SMS_EVENT_REMINDER#";
		if($this->db_conn)
		{
			$query = 'insert into EVENT_AUTO_NOTIFY_REPORTS (NOTIFICATION_TYPE, EVENT_ID, FOR_OCCURRENCE, UPDATED_ON) values(?,?,?, NOW())';
			$result = $this->db_conn->Execute($query, array($notification_type, $event_id, $for_occurrence));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Report inserted successfully";
			}			
		}
		return $to_return;
	}

	public function isSMSNotificationSent($event_id, $for_occurrence)
	{
		$toReturn = true;//keep this as default
		$notification_type = "#SMS_EVENT_REMINDER#";
		if($this->db_conn)
		{
			$query = 'select FOR_OCCURRENCE from EVENT_AUTO_NOTIFY_REPORTS where NOTIFICATION_TYPE=? and EVENT_ID=? and FOR_OCCURRENCE=? limit 1';
			$result = $this->db_conn->Execute($query, array($notification_type, $event_id, $for_occurrence));

			if($result) {
				if($result->EOF) {
					$toReturn = false;
				}
			}
		}
		return $toReturn;
	}

	public function cleanupOldSMSNotificationReports($older_than_days=60)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to delete the reports";
		$older_than_days = ((trim($older_than_days) != "" && trim($older_than_days) > 0)? trim($older_than_days) : 60);
		$updated_on_date_threshold = time()-($older_than_days*24*60*60);
		$notification_type = "#SMS_EVENT_REMINDER#";
		if($this->db_conn)
		{
			$query = 'delete from EVENT_AUTO_NOTIFY_REPORTS where UPDATED_ON < FROM_UNIXTIME(?)';
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