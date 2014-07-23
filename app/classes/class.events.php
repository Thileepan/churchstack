<?php

class Events
{
	protected $db_conn;
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

		$this->event_id = -1;
	}

	public function addEvent($title, $desc, $start_date, $end_date, $repeat_type, $repeat_period, $repeat_on, $repeat_on_day, $repeat_on_date, $repeat_on_month, $priority, $organiser, $status, $access_level, $participant_type, $participant_period, $participant_email, $notification_type, $notification_period)
	{
		$this->event_id = $this->addEventDetails($title, $desc, $start_date, $end_date, $repeat_type, $repeat_period, $repeat_on, $repeat_on_day, $repeat_on_date, $repeat_on_month, $priority, $organiser, $status, $access_level);
		
		//event inserted successfully.
		if($this->event_id > 0)
		{
			$this->addEventParticipants($participant_type, $participant_period, $participant_email);
			$this->addEventNotifications($notification_type, $notification_period);
		}
	}

	public function addEventDetails($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level)
	{
		if($this->db_conn)
		{
			$query = 'insert into EVENT_DETAILS (TITLE, DESCRIPTION, EVENT_LOCATION, START_DATE, END_DATE, START_TIME, END_TIME, RRULE, PRIORITY, ORGANISER, ACCESS_LEVEL) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($title, $desc, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level));
			echo $this->db_conn->ErrorMsg();
			if($result)
			{
				//return the last inserted event_id
				//$event_id = $this->db_conn->Insert_ID();
				return true;
			}
		}
		//return $event_id;
		return false;
	}

	private function addEventParticipants($participant_type, $participant_period, $participant_email)
	{
		if($this->db_conn)
		{
			$query = 'insert into EVENT_PARTICIPANTS (EVENT_ID, PARTICIPANT_TYPE, PARTICIPANT_ID, PARTICIPANT_EMAIL) values (?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($this->event_id, $participant_type, $participant_period, $participant_email));
			if($result) {
				return true;
			}
		}
		return false;
	}

	private function addEventNotifications($event_id, $notification_type, $notification_period)
	{
		if($this->db_conn)
		{
			$query = 'insert into EVENT_NOTIFICATIONS (EVENT_ID, NOTIFICATION_TYPE, NOTIFICATION_PERIOD) values (?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($this->event_id, $notification_type, $notification_period));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateEvent($title, $desc, $start_date, $end_date, $repeat_type, $repeat_period, $repeat_on, $repeat_on_day, $repeat_on_date, $repeat_on_month, $priority, $organiser, $status, $access_level, $participant_type, $participant_period, $participant_email, $notification_type, $notification_period)
	{
		$this->updateEventDetails($title, $desc, $start_date, $end_date, $repeat_type, $repeat_period, $repeat_on, $repeat_on_day, $repeat_on_date, $repeat_on_month, $priority, $organiser, $status, $access_level);
		$this->updateEventParticipants($participant_type, $participant_period, $participant_email);
		$this->updateEventNotifications($notification_type, $notification_period);
	}

	private function updateEventDetails($title, $desc, $location, $start_date, $end_date, $rrule, $priority, $organiser, $status, $access_level)
	{
		if($this->db_conn)
		{
			$query = 'update EVENT_DETAILS set TITLE=?, DESCRIPTION=?, LOCATION=?, START_DATE=?, END_DATE=?, RRULE=?, PRIORITY=?, ORGANISER=?, STATUS=?, ACCESS_LEVEL=? where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($title, $desc, $location, $start_date, $end_date, $rrule, $priority, $organiser, $status, $access_level, $this->event_id));
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
			$query = 'delete * from EVENT_PARTICIPANTS where EVENT_ID=?';
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
			$query = 'delete * from EVENT_NOTIFICATIONS where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($this->event_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteEventDetails($event_id)
	{
		if($this->db_conn)
		{
			$query = 'delete from EVENT_DETAILS where EVENT_ID=?';
			$result = $this->db_conn->Execute($query, array($event_id));
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
			$query = 'select * from EVENT_DETAILS where EVENT_ID = ?';
			$result = $this->db_conn->Execute($query, array($event_id));

			if($result) {
                if(!$result->EOF) {
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
					$event_details = array($event_id, $title, $description, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level);
				}
            }
		}
		return $event_details;
	}

	public function getAllEvents($listAllEvents, $start_date, $end_date, $start_time='0000', $end_time='0000', $event_status=1)
	{
		$event_details = array();
		if($this->db_conn)
		{
			if(!$listAllEvents) {
				$query = 'select * from EVENT_DETAILS where (START_DATE < ? and END_DATE > ?) || (START_DATE > ? and START_DATE < ? and END_DATE = ?)';
				$result = $this->db_conn->Execute($query, array($end_date, $start_date, $start_date, $end_date, '0000-00-00'));
			} else {				
				if($event_status == 1) {
					//upcoming events
					$query = 'select * from EVENT_DETAILS where (END_DATE > ? and END_DATE != ?) || (START_DATE >= ? and END_DATE = ?)';
				} else if($event_status == 2) {
					//past events
					$query = 'select * from EVENT_DETAILS where (END_DATE < ? and END_DATE != ?) || (START_DATE < ? and END_DATE = ?)';
				}
				$result = $this->db_conn->Execute($query, array($end_date, '0000-00-00', $end_date, '0000-00-00'));
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

	public function getEventOccurrences($start_date, $end_date, $time_zone)
	{
		include_once $this->APPLICATION_PATH . '/classes/class.recurr.php';

		$event_info = array();
		$listAllEvents = false;
		$event_details = $this->getAllEvents($listAllEvents, $start_date, $end_date);
		//print_r($event_details);
		$total_events = COUNT($event_details);
		//echo "eventdetails::::::::".$total_events;
		if(is_array($event_details) && $total_events > 0)
		{
			// Recurr Transformer Initialization
			$type = 2;
			$recurr_obj = new RecurrInterface($this->APPLICATION_PATH);
			$recurr_obj->setUp($type);
			$recurr_obj->setTimeZone($time_zone);

			for($i=0; $i<$total_events; $i++)
			{
				//$start_date = $event_details[$i][4];
				$event_id = $event_details[$i][0];
				$rrule = $event_details[$i][8];
				$title = $event_details[$i][1];
				$start_date_db = $event_details[$i][4];
				$end_date_db = $event_details[$i][5];
				$start_time = $event_details[$i][6];
				$end_time = $event_details[$i][7];

				if($rrule != '')
				{
					$recurr_obj->setRRule($rrule);
					$recurr_obj->setStartDate($start_date_db);
					$occurrences = $recurr_obj->getOccurrences();
					//print_r($occurrences);

					$total_occurrence = COUNT($occurrences);
					if(is_array($occurrences) && $total_occurrence > 0)
					{
						for($j=0; $j<$total_occurrence; $j++)
						{
							$event_date = $occurrences[$j]->format("Y-m-d");
							//echo $event_date.":::".$start_date.":::".$end_date."<BR>";
							
							if($event_date >= $start_date && $event_date <= $end_date)
							{
								if(strlen($start_time) > 3) {
									$hour = substr($start_time, 0, 2);
									$min = substr($start_time, 2, 2);
								} else {
									$hour = "0".substr($start_time, 0, 1);
									$min = substr($start_time, 1, 2);
								}
								$sec = '00';
								$event_start_date_alone = $event_date;
								$event_date .= ' ' . $hour . ':' . $min . ':' . $sec;
								$event_start_time_alone = $hour.''.$min;
								$event_info[] = array('start'=>$event_date, 'title'=>$title, 'allDay'=>false, "eventID"=>$event_id, "startDateAlone"=>$start_date_db, "startTimeAlone"=>$event_start_time_alone);
							}						
						}
					}
				}
				else
				{
					$event_info[] = array('start'=>$start_date_db, 'title'=>$title, 'allDay'=>false, "eventID"=>$event_id, "startDateAlone"=>$event_start_date_alone, "startTimeAlone"=>$start_time);
				}
			}
		}

		return $event_info;
	}
}

?>