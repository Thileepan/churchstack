<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.events.php");
	include_once($APPLICATION_PATH."classes/class.sms.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	//print_r($_GET);

	$shardedDB = base64_decode($_GET["shardedDB"]);//Exclusively for running from command line 
	$events_obj = new Events($APPLICATION_PATH, $shardedDB);
	//$events_list = $events_obj->listEventDetailsToNotifyNow();

	/** /
	$events_list[0] = 1;
	$events_list[1] = array(0=>array(0=>array("1@jujukin.com","2@jujukin.com","3@jujukin.com","4@jujukin.com","5@jujukin.com","6@jujukin.com","7@jujukin.com","8@jujukin.com","9@jujukin.com","10@jujukin.com","11@jujukin.com","12@jujukin.com","13@jujukin.com","14@jujukin.com","15@jujukin.com","16@jujukin.com"), 1=>"this is the subject", 2=>"this is th ebody"), 1=>array(0=>array("nesanjoseph@gmail.com"),  1=>"this is oho subject", 2=>"this is oho ebody"));
	/**/
	/** /
	$events_list[0] = 1;
	$events_list[1] = array(0=>array(3=>array("+919962131353","+919176131353"), 4=>"testing the message"), 1=>array(3=>array("+919962241353"),  4=>"Another test message"));
	/**/

	$email_sending_file = $APPLICATION_PATH."notify/sendemail.php";

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
	else if($sms_provider_id==2)//BhashSMS
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

	if($events_list[0]==1)
	{
		$commands = array();
		for($e=0; $e < COUNT($events_list[1]); $e++)
		{
			$emails_count = COUNT($events_list[1][$e][0]);
			$subject = $events_list[1][$e][1];
			$body = $events_list[1][$e][2];
			$fromAddressType = "eventreminder";
			$comma_separated_email_list = "";
			for($k=0; $k < $emails_count; $k++)
			{
				$curr_email = $events_list[1][$e][0][$k];
				$comma_separated_email_list .= ((trim($comma_separated_email_list)=="")? $curr_email : ",".$curr_email); 
				if($emails_count > 25)
				{
					if(($k%25==0 && $k > 0) || $k+25 > $emails_count)
					{
						$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.base64_encode($comma_separated_email_list).' subject='.base64_encode($subject).' emailBody='.base64_encode($body).' fromAddressType='.$fromAddressType;
						$comma_separated_email_list = "";
					}
				}
				else
				{
					$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.base64_encode($comma_separated_email_list).' subject='.base64_encode($subject).' emailBody='.base64_encode($body).' fromAddressType='.$fromAddressType;
					$comma_separated_email_list = "";
				}
			}

			//For SMS
			if($is_sms_enabled==1 && $sms_provider_id > 0)
			{
				$sms_numbers_count = COUNT($events_list[1][$e][3]);
				$sms_body = $events_list[1][$e][4];
				$comma_separated_num_list = "";
				for($k=0; $k < $sms_numbers_count; $k++)
				{
					$curr_to_number = $events_list[1][$e][3][$k];
					$comma_separated_num_list .= ((trim($comma_separated_num_list)=="")? $curr_to_number : ",".$curr_to_number); 
					if($sms_numbers_count > 25)
					{
						if(($k%25==0 && $k > 0) || $k+25 > $sms_numbers_count)
						{
							if($sms_provider_id==1)//Twilio: Repeat this for other providers
							{
								$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$sms_sending_file.' accountSID='.base64_encode($twilio_account_sid).' authToken='.base64_encode($twilio_auth_token).' fromNumber='.base64_encode($twilio_from_number).' smsBody='.base64_encode($sms_body).' csvToNumbers='.base64_encode($comma_separated_num_list);
							}
							else if($sms_provider_id==2)//BhashSMS
							{
								$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$sms_sending_file.' username='.base64_encode($bhashsms_username).' password='.base64_encode($bhashsms_password).' senderid='.base64_encode($bhashsms_senderid).' priority='.base64_encode($bhashsms_priority).' smsBody='.base64_encode($sms_body).' csvToNumbers='.base64_encode($comma_separated_num_list);
							}
							$comma_separated_num_list = "";
						}
					}
					else
					{
							if($sms_provider_id==1)//Twilio: Repeat this for other providers
							{
								$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$sms_sending_file.' accountSID='.base64_encode($twilio_account_sid).' authToken='.base64_encode($twilio_auth_token).' fromNumber='.base64_encode($twilio_from_number).' smsBody='.base64_encode($sms_body).' csvToNumbers='.base64_encode($comma_separated_num_list);
							}
							else if($sms_provider_id==2)//BhashSMS
							{
								$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$sms_sending_file.' username='.base64_encode($bhashsms_username).' password='.base64_encode($bhashsms_password).' senderid='.base64_encode($bhashsms_senderid).' priority='.base64_encode($bhashsms_priority).' smsBody='.base64_encode($sms_body).' csvToNumbers='.base64_encode($comma_separated_num_list);
							}
							$comma_separated_num_list = "";
					}
				}
			}
			//For SMS
		}

		$threads = new Multithread( $commands );
		$threads->run();
		/** /
		foreach ( $threads->commands as $key=>$command )
		{
			//echo "Command: ".$command."\n\n";
			//echo "Sharded Output: ".$threads->output[$key]."\n";
			//echo "Error: ".$threads->error[$key]."\n\n";
		}
		/**/
	}
?>