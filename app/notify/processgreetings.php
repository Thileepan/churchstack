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
	include_once($APPLICATION_PATH."plugins/carbon/src/Carbon/Carbon.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	//print_r($_GET);

	$shardedDB = urldecode($_GET["shardedDB"]);//Exclusively for running from command line 
	$timeZone = urldecode($_GET["timeZone"]);//Exclusively for running from command line 

	/*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX* /
	REMOVE THIS SOON
	/*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX* /
	 
	$shardedDB = "cs_cfaaa60bf132e18f76e387f960a81ee1";
	$timeZone = "Asia/Kolkata";
	/*XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX*/



	$notification_type = 2;//1->Events; 2->Birthday & Wedding Anniversary Greetings
	if(trim($_GET["notificationType"]) != "") {
		$notification_type = urldecode($_GET["notificationType"]);//Exclusively for running from command line 
	}
	$noti_obj = new Notification($APPLICATION_PATH, $shardedDB);
	$noti_obj->setTimeZone($timeZone);
	//Cleanup the notification reports older than 60 days.
	$cleanup_result = $noti_obj->cleanupOldGreetingsNotificationReports(60);
	$sms_email_report_cleanup_result = $noti_obj->cleanupOldEmailSMSCountReports(60);


	$dates_list = array();
	$current_time = Carbon::now($timeZone);
	$today = $current_time->year."-".$current_time->month."-".$current_time->day;

	$dates_list[0] = $today;
	if(!$current_time->isLeapYear() && ($current_time->month == 2 || $current_time->month == "2" || $current_time->month == "02") && ($current_time->day == 28 || $current_time->day == "28")) {//This is not leap year and it's feb-28, so fetch feb 29 birthdays also.
		$dates_list[1] = $current_time->year."-".$current_time->month."-29";
	}

	$email_bday_greetings_enabled = 1;
	$sms_bday_greetings_enabled = 1;
	$email_wedding_greetings_enabled = 1;
	$sms_wedding_greetings_enabled = 1;

	for($dc=0; $dc < COUNT($dates_list); $dc++)
	{
		$curr_date = $dates_list[$dc];
		$today = $curr_date;
		$birthday_list = array();
		$wedding_list = array();
		//Check if atleast one is enabled, else do not get list
		if($email_bday_greetings_enabled==1 || $sms_bday_greetings_enabled == 1 || $email_wedding_greetings_enabled==1 || $sms_wedding_greetings_enabled==1)
		{
			//Just checking one thing is enough because we will insert everything below;
			$is_noti_sent = $noti_obj->isGreetingsNotificationSent(1, 1, $today);


			if(!$is_noti_sent)
			{
				$birthday_list = $noti_obj->getPeopleWithBirthdayOn($today);
				$wedding_list = $noti_obj->getPeopleWithWeddingOn($today);

				$noti_obj->insertGreetingsNotificationReport(1, 1, $today);
				$noti_obj->insertGreetingsNotificationReport(1, 2, $today);
				$noti_obj->insertGreetingsNotificationReport(2, 1, $today);
				$noti_obj->insertGreetingsNotificationReport(2, 2, $today);
			}
			else
			{
				continue;
			}
		}
		else
		{
			continue;
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

		$commands = array();

		//$profile_list[] = array($profile_id, $first_name, $middle_name, $last_name, $mobile, $email, $email_notification, $sms_notification);
		/**/
		if(COUNT($birthday_list) > 0)
		{
			$birthday_email_list = array();
			$birthday_sms_list = array();
			for($e=0; $e < COUNT($birthday_list); $e++)
			{
				$curr_first_name = $birthday_list[$e][1];
				$curr_middle_name = $birthday_list[$e][2];
				$curr_last_name = $birthday_list[$e][3];
				$curr_mobile = $birthday_list[$e][4];
				$curr_email = $birthday_list[$e][5];
				$personal_email_noti_enabled = $birthday_list[$e][6];
				$personal_sms_noti_enabled = $birthday_list[$e][7];
				if($email_bday_greetings_enabled == 1 && $personal_email_noti_enabled == 1 && trim($curr_email) != "")//Check if Global Email Greeting and Personal notification is enabled
				{
					$birthday_email_list[] = array($curr_first_name, $curr_middle_name, $curr_last_name, $curr_mobile, $curr_email);
				}

				if($sms_bday_greetings_enabled == 1 && $personal_sms_noti_enabled == 1 && trim($curr_mobile) != "")//Check if Global SMS Greeting and Personal notification is enabled
				{
					$birthday_sms_list[] = array($curr_first_name, $curr_middle_name, $curr_last_name, $curr_mobile, $curr_email);
				}
			}

			if(COUNT($birthday_email_list) > 0)
			{
				$subject = "Happy Birthday {{FIRST_NAME}} {{MIDDLE_NAME}} {{LAST_NAME}}";
				$fromAddressType = "birthdaygreetings";
				$body = "Wishing you Happy Birthday {{FIRST_NAME}} {{MIDDLE_NAME}} {{LAST_NAME}}";
				$noti_obj->insertEmailSMSCountReport(1, "Birthday Greetings", $body, COUNT($birthday_email_list));
				$comma_separated_email_list = "";
				$delim_separated_first_name = "";
				$delim_separated_middle_name = "";
				$delim_separated_last_name = "";
				for($k=0; $k < COUNT($birthday_email_list); $k++)
				{
					$tmp = 0;
					$comma_separated_email_list = "";
					while($tmp < 25 && $k < COUNT($birthday_email_list))
					{
						//VERY IMPORTANT THAT THIS PART SHOULD BE IN THE BEGINNNIG....
						$curr_first_name = $birthday_email_list[$k][0];
						$curr_middle_name = $birthday_email_list[$k][1];
						$curr_last_name = $birthday_email_list[$k][2];
						$delim_separated_first_name .= ((trim($comma_separated_email_list)=="")? $curr_first_name : "/:/".$curr_first_name); 
						$delim_separated_middle_name .= ((trim($comma_separated_email_list)=="")? $curr_middle_name : "/:/".$curr_middle_name); 
						$delim_separated_last_name .= ((trim($comma_separated_email_list)=="")? $curr_last_name : "/:/".$curr_last_name); 
						//VERY IMPORTANT THAT ABOVE PART SHOULD BE IN THE BEGINNNIG....

						$curr_email = $birthday_email_list[$k][4];
						$comma_separated_email_list .= ((trim($comma_separated_email_list)=="")? $curr_email : ",".$curr_email); 

						$tmp++;
						$k++;
					}
					$k--;//To adjust the above extra increment...

					$commands[] = PHP_EXE_PATH.' "'.$email_sending_file.'" csvToEmails='.urlencode($comma_separated_email_list).' subject='.urlencode($subject).' emailBody='.urlencode($body).' fromAddressType='.$fromAddressType.' replyToEmail='.urlencode(DONOTREPLY_EMAIL).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
				}
			}

			/**/
			if(COUNT($birthday_sms_list) > 0 && $is_sms_enabled==1 && $sms_provider_id > 0)
			{
				$alertType = "birthdaygreetings";
				$sms_body = "Wishing you Happy Birthday {{FIRST_NAME}} {{MIDDLE_NAME}} {{LAST_NAME}}";
				$noti_obj->insertEmailSMSCountReport(2, "Birthday Greetings", $sms_body, COUNT($birthday_sms_list));
				$comma_separated_numbers_list = "";
				$delim_separated_first_name = "";
				$delim_separated_middle_name = "";
				$delim_separated_last_name = "";
				for($k=0; $k < COUNT($birthday_sms_list); $k++)
				{
					$tmp = 0;
					$comma_separated_numbers_list = "";
					while($tmp < 25 && $k < COUNT($birthday_sms_list))
					{
						$curr_first_name = $birthday_sms_list[$k][0];
						$curr_middle_name = $birthday_sms_list[$k][1];
						$curr_last_name = $birthday_sms_list[$k][2];
						$delim_separated_first_name .= ((trim($comma_separated_numbers_list)=="")? $curr_first_name : "/:/".$curr_first_name); 
						$delim_separated_middle_name .= ((trim($comma_separated_numbers_list)=="")? $curr_middle_name : "/:/".$curr_middle_name); 
						$delim_separated_last_name .= ((trim($comma_separated_numbers_list)=="")? $curr_last_name : "/:/".$curr_last_name); 

						$curr_num = $birthday_sms_list[$k][3];
						$comma_separated_numbers_list .= ((trim($comma_separated_numbers_list)=="")? $curr_num : ",".$curr_num); 

						$tmp++;
						$k++;
					}
					$k--;//To adjust the above extra increment...

					if($sms_provider_id==1)//Twilio: Repeat this for other providers
					{
						$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" accountSID='.urlencode($twilio_account_sid).' authToken='.urlencode($twilio_auth_token).' fromNumber='.urlencode($twilio_from_number).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' alertType='.urlencode($alertType).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
					}
					else if($sms_provider_id==2)//Nexmo
					{
						$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" apiKey='.urlencode($nexmo_api_key).' apiSecret='.urlencode($nexmo_api_secret).' fromNumber='.urlencode($nexmo_from_number).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' alertType='.urlencode($alertType).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
					}
					else if($sms_provider_id==3)//BhashSMS
					{
						$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" username='.urlencode($bhashsms_username).' password='.urlencode($bhashsms_password).' senderid='.urlencode($bhashsms_senderid).' priority='.urlencode($bhashsms_priority).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' alertType='.urlencode($alertType).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
					}
				}
			}
			/**/
		}


		/**/
		if(COUNT($wedding_list) > 0)
		{
			$wedding_email_list = array();
			$wedding_sms_list = array();
			for($e=0; $e < COUNT($wedding_list); $e++)
			{
				$curr_first_name = $wedding_list[$e][1];
				$curr_middle_name = $wedding_list[$e][2];
				$curr_last_name = $wedding_list[$e][3];
				$curr_mobile = $wedding_list[$e][4];
				$curr_email = $wedding_list[$e][5];
				$personal_email_noti_enabled = $wedding_list[$e][6];
				$personal_sms_noti_enabled = $wedding_list[$e][7];
				if($email_bday_greetings_enabled == 1 && $personal_email_noti_enabled == 1 && trim($curr_email) != "")//Check if Global Email Greeting and Personal notification is enabled
				{
					$wedding_email_list[] = array($curr_first_name, $curr_middle_name, $curr_last_name, $curr_mobile, $curr_email);
				}

				if($sms_bday_greetings_enabled == 1 && $personal_sms_noti_enabled == 1 && trim($curr_mobile) != "")//Check if Global SMS Greeting and Personal notification is enabled
				{
					$wedding_sms_list[] = array($curr_first_name, $curr_middle_name, $curr_last_name, $curr_mobile, $curr_email);
				}
			}

			if(COUNT($wedding_email_list) > 0)
			{
				$subject = "Happy Wedding {{FIRST_NAME}} {{MIDDLE_NAME}} {{LAST_NAME}}";
				$fromAddressType = "weddinggreetings";
				$body = "Wishing you Happy wedding {{FIRST_NAME}} {{MIDDLE_NAME}} {{LAST_NAME}}";
				$noti_obj->insertEmailSMSCountReport(1, "Wedding Anniversary Greetings", $body, COUNT($wedding_email_list));
				$comma_separated_email_list = "";
				$delim_separated_first_name = "";
				$delim_separated_middle_name = "";
				$delim_separated_last_name = "";
				for($k=0; $k < COUNT($wedding_email_list); $k++)
				{
					$tmp = 0;
					$comma_separated_email_list = "";
					while($tmp < 25 && $k < COUNT($wedding_email_list))
					{
						$curr_first_name = $wedding_email_list[$k][0];
						$curr_middle_name = $wedding_email_list[$k][1];
						$curr_last_name = $wedding_email_list[$k][2];
						$delim_separated_first_name .= ((trim($comma_separated_email_list)=="")? $curr_first_name : "/:/".$curr_first_name); 
						$delim_separated_middle_name .= ((trim($comma_separated_email_list)=="")? $curr_middle_name : "/:/".$curr_middle_name); 
						$delim_separated_last_name .= ((trim($comma_separated_email_list)=="")? $curr_last_name : "/:/".$curr_last_name); 

						$curr_email = $wedding_email_list[$k][4];
						$comma_separated_email_list .= ((trim($comma_separated_email_list)=="")? $curr_email : ",".$curr_email); 

						$tmp++;
						$k++;
					}
					$k--;//To adjust the above extra increment...

					$commands[] = PHP_EXE_PATH.' "'.$email_sending_file.'" csvToEmails='.urlencode($comma_separated_email_list).' subject='.urlencode($subject).' emailBody='.urlencode($body).' fromAddressType='.$fromAddressType.' replyToEmail='.urlencode(DONOTREPLY_EMAIL).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
				}
			}

			if(COUNT($wedding_sms_list) > 0 && $is_sms_enabled==1 && $sms_provider_id > 0)
			{
				$alertType = "weddinggreetings";
				$sms_body = "Wishing you Happy wedding {{FIRST_NAME}} {{MIDDLE_NAME}} {{LAST_NAME}}";
				$noti_obj->insertEmailSMSCountReport(2, "Wedding Anniversary Greetings", $sms_body, COUNT($wedding_sms_list));
				$comma_separated_numbers_list = "";
				$delim_separated_first_name = "";
				$delim_separated_middle_name = "";
				$delim_separated_last_name = "";
				for($k=0; $k < COUNT($wedding_sms_list); $k++)
				{
					$tmp = 0;
					$comma_separated_numbers_list = "";
					while($tmp < 25 && $k < COUNT($wedding_sms_list))
					{
						$curr_first_name = $wedding_sms_list[$k][0];
						$curr_middle_name = $wedding_sms_list[$k][1];
						$curr_last_name = $wedding_sms_list[$k][2];
						$delim_separated_first_name .= ((trim($comma_separated_numbers_list)=="")? $curr_first_name : "/:/".$curr_first_name); 
						$delim_separated_middle_name .= ((trim($comma_separated_numbers_list)=="")? $curr_middle_name : "/:/".$curr_middle_name); 
						$delim_separated_last_name .= ((trim($comma_separated_numbers_list)=="")? $curr_last_name : "/:/".$curr_last_name); 

						$curr_num = $wedding_sms_list[$k][3];
						$comma_separated_numbers_list .= ((trim($comma_separated_numbers_list)=="")? $curr_num : ",".$curr_num); 

						$tmp++;
						$k++;
					}
					$k--;//To adjust the above extra increment...

					if($sms_provider_id==1)//Twilio: Repeat this for other providers
					{
						$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" accountSID='.urlencode($twilio_account_sid).' authToken='.urlencode($twilio_auth_token).' fromNumber='.urlencode($twilio_from_number).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' alertType='.urlencode($alertType).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
					}
					else if($sms_provider_id==2)//Nexmo
					{
						$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" apiKey='.urlencode($nexmo_api_key).' apiSecret='.urlencode($nexmo_api_secret).' fromNumber='.urlencode($nexmo_from_number).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' alertType='.urlencode($alertType).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
					}
					else if($sms_provider_id==3)//BhashSMS
					{
						$commands[] = PHP_EXE_PATH.' "'.$sms_sending_file.'" username='.urlencode($bhashsms_username).' password='.urlencode($bhashsms_password).' senderid='.urlencode($bhashsms_senderid).' priority='.urlencode($bhashsms_priority).' smsBody='.urlencode($sms_body).' csvToNumbers='.urlencode($comma_separated_numbers_list).' alertType='.urlencode($alertType).' delimFirstNames='.urlencode($delim_separated_first_name).' delimMiddleNames='.urlencode($delim_separated_middle_name).' delimLastNames='.urlencode($delim_separated_last_name).' > /dev/null 2>/dev/null &';
					}
				}
			}
		}
		/**/

		if(COUNT($commands) > 0)
		{
			$threads = new Multithread( $commands );
			$threads->run();
			/** /
			foreach ( $threads->commands as $key=>$command )
			{
				echo "Command: ".$command."\n\n";
				echo "Sharded Output: ".$threads->output[$key]."\n";
				echo "Error: ".$threads->error[$key]."\n\n";
			}
			/**/
		}
	}
?>