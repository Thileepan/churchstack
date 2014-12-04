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
	include_once($APPLICATION_PATH."classes/class.sms.php");
	include_once($APPLICATION_PATH."classes/class.notification.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	//print_r($_GET);

	$shardedDB = urldecode($_GET["shardedDB"]);//Exclusively for running from command line 
	$timeZone = urldecode($_GET["timeZone"]);//Exclusively for running from command line 
	$churchName = urldecode($_GET["churchName"]);//Exclusively for running from command line 

	/*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX* /
	REMOVE THIS SOON
	/*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX* /
	 
	$shardedDB = "cs_cfaaa60bf132e18f76e387f960a81ee1";
	$timeZone = "Asia/Kolkata";
	/*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/

	$events_obj = new Events($APPLICATION_PATH, $shardedDB);
	$events_obj->setTimeZone($timeZone);

	//Cleanup the notification reports older than 60 days.
	$cleanup_result = $events_obj->cleanupOldEmailNotificationReports(60);
	$cleanup_result = $events_obj->cleanupOldSMSNotificationReports(60);

	$noti_obj = new Notification($APPLICATION_PATH, $shardedDB);
	//Cleanup the notification reports older than 60 days.
	$sms_email_report_cleanup_result = $noti_obj->cleanupOldEmailSMSCountReports(60);

	$events_list = array();
	$events_list = $events_obj->getEventsToNotifyNow();

	/** /
	$events_list[0] = 1;
	$events_list[1] = array(0=>array(3=>array("+1", "+2", "+3", "+4", "+5", "+6"), 4=>"testing the message"), 1=>array(3=>array("+919962241353", "+919962131353", "+919962131353"),  4=>"Another test message"));
	/**/
	/** /
	$sms_arrays = array("+1", "+2", "+3", "+4", "+5", "+6");
	$sms_message = "testing the message";
	$events_list[0] = 1;
	$events_list[1] = array(0=>array(0=>array("1@jujukin.com","2@jujukin.com","3@jujukin.com","4@jujukin.com","5@jujukin.com","6@jujukin.com","7@jujukin.com","8@jujukin.com","9@jujukin.com","10@jujukin.com","11@jujukin.com","12@jujukin.com","13@jujukin.com","14@jujukin.com","15@jujukin.com","16@jujukin.com"), 1=>"this is the subject", 2=>"this is th ebody", 3=>$sms_arrays, 4=>$sms_message), 1=>array(0=>array("nesanjoseph@gmail.com"),  1=>"this is oho subject", 2=>"this is oho ebody"));
	/**/

	if(!is_array($events_list) || (is_array($events_list) && COUNT($events_list) <= 0)) {
		exit;
	}

	$email_sending_file = $APPLICATION_PATH."notify/sendemail.php";
	$sms_sending_file = "";

	//SMS Stuff
	$sms_obj = new SMS($APPLICATION_PATH, $shardedDB);
	$is_sms_enabled = 0;
	$sms_provider_id = 0;
	$sms_config_result = $sms_obj->getSMSConfiguration();
	if($sms_config_result[0]==1)
	{
		$is_sms_enabled = $sms_config_result[1]["SMS_ENABLED"];
		if($is_sms_enabled == 1)
		{
			$sms_provider_id = $sms_config_result[1]["SMS_PROVIDER_ID"];
		}
	}

	if($sms_provider_id==1)//Twilio
	{
		$twilio_account_sid = "";
		$twilio_auth_token = "";
		$twilio_from_number = "";
		$twilio_config_result = $sms_obj->getTwilioConfig(1);//list active alone
		if($twilio_config_result[0]==1)
		{
			if(COUNT($twilio_config_result[1]) > 0)
			{
				//taking the first config alone
				$twilio_account_sid = $twilio_config_result[1][0][1];
				$twilio_auth_token = $twilio_config_result[1][0][2];
				$twilio_from_number = $twilio_config_result[1][0][3];
			}
		}
		$sms_sending_file = $APPLICATION_PATH."notify/sendtwiliosms.php";
	}
	else if($sms_provider_id==2)//Nexmo
	{
		$nexmo_api_key = "";
		$nexmo_api_secret = "";
		$nexmo_from_number = "";
		$nexmo_config_result = $sms_obj->getNexmoConfig(1);//list active alone
		if($nexmo_config_result[0]==1)
		{
			if(COUNT($nexmo_config_result[1]) > 0)
			{
				//taking the first config alone
				$nexmo_api_key = $nexmo_config_result[1][0][1];
				$nexmo_api_secret = $nexmo_config_result[1][0][2];
				$nexmo_from_number = $nexmo_config_result[1][0][3];
			}
		}
		$sms_sending_file = $APPLICATION_PATH."notify/sendnexmosms.php";
	}
	else if($sms_provider_id==3)//BhashSMS
	{
		$bhashsms_username="";
		$bhashsms_password="";
		$bhashsms_senderid="";
		$bhashsms_priority="";
		$bhashsms_config_result = $sms_obj->getBhashSMSConfig(1);//list active alone
		if($bhashsms_config_result[0]==1)
		{
			if(COUNT($bhashsms_config_result[1]) > 0)
			{
				//taking the first config alone
				$bhashsms_username = $bhashsms_config_result[1][0][1];
				$bhashsms_password = $bhashsms_config_result[1][0][2];
				$bhashsms_senderid = $bhashsms_config_result[1][0][3];
				$bhashsms_priority = $bhashsms_config_result[1][0][4];
			}
		}
		$sms_sending_file = $APPLICATION_PATH."notify/sendbhashsms.php";
	}
	//SMS Stuff

	if(COUNT($events_list))
	{
		$commands = array();
		for($e=0; $e < COUNT($events_list); $e++)
		{
			$event_id = $events_list[$e]["event_id"];
			$event_exact_occur_time = $events_list[$e]["exact_occurrence_time"];
			if($events_list[$e]["notification_type"] == 1)//Email
			{
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
				$noti_obj->insertEmailSMSCountReport(1, "Events", $body, $emails_count);

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

					$commands[] = PHP_EXE_PATH.' "'.$email_sending_file.'" csvToEmails='.urlencode($comma_separated_email_list).' subject='.urlencode($subject).' emailBody='.urlencode($body).' fromAddressType='.$fromAddressType.' replyToEmail='.urlencode(DONOTREPLY_EMAIL).' churchName='.urlencode($churchName).' > /dev/null 2>/dev/null &';
				}
			}
			else if($events_list[$e]["notification_type"] == 2)//SMS
			{
				$is_sms_sent = $events_obj->isSMSNotificationSent($event_id, $event_exact_occur_time);
				if($is_sms_sent) {
					//Avoid sending duplicate reminder
					continue;
				} else {
					$events_obj->insertSMSNotificationReport($event_id, $event_exact_occur_time);
				}
				$sms_count = COUNT($events_list[$e]["event_sms_recipients"]);
				$carbon_obj = $events_list[$e]["event_date_time"];
				$human_read_date_time = $carbon_obj->__toString();
				$sms_body = "Reminder: ".trim($events_list[$e]["event_title"])." @ ".$human_read_date_time;
				if(trim($events_list[$e]["event_place"]) != "") {
					$sms_body .= " at ".trim($events_list[$e]["event_place"]);
				}


				if($is_sms_enabled==1 && $sms_provider_id > 0)
				{
					$noti_obj->insertEmailSMSCountReport(2, "Events", $sms_body, $sms_count);
					$comma_separated_numbers_list = "";
					for($s=0; $s < $sms_count; $s++)
					{
						$tmp = 0;
						$comma_separated_numbers_list = "";
						while($tmp < 150 && $s < $sms_count)
						{
							$curr_number = $events_list[$e]["event_sms_recipients"][$s];
							$comma_separated_numbers_list .= ((trim($comma_separated_numbers_list)=="")? $curr_number : ",".$curr_number); 
							$tmp++;
							$s++;
						}
						$s--;//To adjust the above extra increment...

						if($sms_provider_id==1)//Twilio: Repeat this for other providers
						{
							$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" accountSID='.urlencode($twilio_account_sid).' authToken='.urlencode($twilio_auth_token).' fromNumber='.urlencode($twilio_from_number).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' churchName='.urlencode($churchName).' > /dev/null 2>/dev/null &';
						}
						else if($sms_provider_id==2)//Nexmo
						{
							$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" apiKey='.urlencode($nexmo_api_key).' apiSecret='.urlencode($nexmo_api_secret).' fromNumber='.urlencode($nexmo_from_number).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' churchName='.urlencode($churchName).' > /dev/null 2>/dev/null &';
						}
						else if($sms_provider_id==3)//BhashSMS
						{
							$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" username='.urlencode($bhashsms_username).' password='.urlencode($bhashsms_password).' senderid='.urlencode($bhashsms_senderid).' priority='.urlencode($bhashsms_priority).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' churchName='.urlencode($churchName).' > /dev/null 2>/dev/null &';
						}
					}
				}
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