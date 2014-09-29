<?php

class Notification
{
	protected $db_conn;
	private $APPLICATION_PATH;
	
	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
		/*
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
		*/
	}

	public function sendEventRemainders()
	{
		include_once $this->APPLICATION_PATH . '/conf/config.php';
		include_once $this->APPLICATION_PATH . '/classes/class.events.php';
		include_once($this->APPLICATION_PATH . '/classes/class.email.php');

		$events_obj = new Events($this->APPLICATION_PATH);
		$event_notification_details = $events_obj->getEventsToNotifyNow();
		//print_r($event_notification_details);
		//exit;
		
		//Send Emails
		$total_events = COUNT($event_notification_details);
		if($total_events > 0)
		{
			for($i=0; $i<$total_events; $i++)
			{
				$total_recipients = COUNT($event_notification_details[$i]["event_email_recipients"]);
				if($total_recipients > 0)
				{
					for($j=0; $j<$total_recipients; $j++)
					{
						$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_NOTIFICATIONS);
						$recipients = array();
						$recipients['to_address'] = $event_notification_details[$i]["event_email_recipients"][$j];
						$recipients['reply_to_address'] = DONOTREPLY_EMAIL;
						$subject = "Reminder: ".$event_notification_details[$i]["event_title"]." @ ".$event_notification_details[$i]["event_date_time"];
						$email_obj->setRecipients($recipients);
						$email_obj->setSubject($subject);
						$email_obj->setBody($this->constructEventReminderEmailBody($event_notification_details[$i]));
						$to_return = $email_obj->sendEmail();						
					}
				}
			}
		}
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
}
?>