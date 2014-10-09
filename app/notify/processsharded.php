<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	
	if(trim($_SERVER['DOCUMENT_ROOT']) != "") {
		@require $APPLICATION_PATH.'error/404';
		exit;
	}

	include_once($APPLICATION_PATH."conf/config.php");
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.events.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	//print_r($_GET);

	$shardedDB = urldecode($_GET["shardedDB"]);//Exclusively for running from command line 
	$timeZone = urldecode($_GET["timeZone"]);//Exclusively for running from command line 
	$events_obj = new Events($APPLICATION_PATH, $shardedDB);
	$events_obj->setTimeZone($timeZone);
	
	//Cleanup the notification reports older than 60 days.
	$cleanup_result = $events_obj->cleanupOldEmailNotificationReports(60);

	$events_list = array();
	$events_list = $events_obj->getEventsToNotifyNow();

	/** /
	$events_list[0] = 1;
	$events_list[1] = array(0=>array(0=>array("1@jujukin.com","2@jujukin.com","3@jujukin.com","4@jujukin.com","5@jujukin.com","6@jujukin.com","7@jujukin.com","8@jujukin.com","9@jujukin.com","10@jujukin.com","11@jujukin.com","12@jujukin.com","13@jujukin.com","14@jujukin.com","15@jujukin.com","16@jujukin.com"), 1=>"this is the subject", 2=>"this is th ebody"), 1=>array(0=>array("nesanjoseph@gmail.com"),  1=>"this is oho subject", 2=>"this is oho ebody"));
	/**/

	if(!is_array($events_list) || (is_array($events_list) && COUNT($events_list) <= 0)) {
		exit;
	}

	$email_sending_file = $APPLICATION_PATH."notify/sendemail.php";


	if(COUNT($events_list))
	{
		$commands = array();
		for($e=0; $e < COUNT($events_list); $e++)
		{
			$event_id = $events_list[$e]["event_id"];
			$event_exact_occur_time = $events_list[$e]["exact_occurrence_time"];
			$is_email_sent = $events_obj->isEmailNotificationSent($event_id, $event_exact_occur_time);
			if($is_email_sent) {
				//Avoid sending duplicate reminder
				continue;
			} else {
				$events_obj->insertEmailNotificationReport($event_id, $event_exact_occur_time);
			}
			$emails_count = COUNT($events_list[$e]["event_email_recipients"]);
			$carbon_obj = $events_list[$e]["event_date_time"];
			$human_read_date_time = $carbon_obj->__toString();
			$event_body_input_array = array();
			$event_body_input_array["event_organizers_array"] = array($events_list[$e]["event_organizers"]);
			$event_body_input_array["event_attendees_array"] = $events_list[$e]["event_attendees"];
			$event_body_input_array["event_title"] = $events_list[$e]["event_title"];
			$event_body_input_array["event_desc"] = $events_list[$e]["event_desc"];
			$event_body_input_array["event_date_time"] = $human_read_date_time;
			$event_body_input_array["event_place"] = $events_list[$e]["event_place"];
			$body = $events_obj->constructEventReminderEmailBody($event_body_input_array);

			$subject = "Reminder: ".$events_list[$e]["event_title"]." @ ".$human_read_date_time;
			$fromAddressType = "eventreminder";
			$comma_separated_email_list = "";
			for($k=0; $k < $emails_count; $k++)
			{
				$tmp = 0;
				$comma_separated_email_list = "";
				while($tmp < 150 && $k < $emails_count)
				{
					$curr_email = $events_list[$e]["event_email_recipients"][$k];
					$comma_separated_email_list .= ((trim($comma_separated_email_list)=="")? $curr_email : ",".$curr_email); 
					$tmp++;
					$k++;
				}
				$k--;//To adjust the above extra increment...

				$commands[] = PHP_EXE_PATH.' "'.$email_sending_file.'" csvToEmails='.urlencode($comma_separated_email_list).' subject='.urlencode($subject).' emailBody='.urlencode($body).' fromAddressType='.$fromAddressType.' replyToEmail='.urlencode(DONOTREPLY_EMAIL).' > /dev/null 2>/dev/null &';
			}

		}

		$threads = new Multithread( $commands );
		$threads->run();
		/** /
		foreach ( $threads->commands as $key=>$command )
		{
			//echo "Command: ".$command."\n\n";
			echo "Sharded Output: ".$threads->output[$key]."\n";
			//echo "Error: ".$threads->error[$key]."\n\n";
		}
		/**/
	}
?>