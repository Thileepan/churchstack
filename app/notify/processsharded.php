<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.events.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	//print_r($_GET);

	$shardedDB = base64_decode($_GET["shardedDB"]);//Exclusively for running from command line 
	$events_obj = new Events($APPLICATION_PATH, $shardedDB);
	$events_list = array();
	//$events_list = $events_obj->listEventDetailsToNotifyNow();

	/** /
	$events_list[0] = 1;
	$events_list[1] = array(0=>array(0=>array("1@jujukin.com","2@jujukin.com","3@jujukin.com","4@jujukin.com","5@jujukin.com","6@jujukin.com","7@jujukin.com","8@jujukin.com","9@jujukin.com","10@jujukin.com","11@jujukin.com","12@jujukin.com","13@jujukin.com","14@jujukin.com","15@jujukin.com","16@jujukin.com"), 1=>"this is the subject", 2=>"this is th ebody"), 1=>array(0=>array("nesanjoseph@gmail.com"),  1=>"this is oho subject", 2=>"this is oho ebody"));
	/**/

	if(!is_array($events_list) || (is_array($events_list) && COUNT($events_list) < 2) || $events_list[0]==0 || COUNT($events_list[1]) <= 0) {
		exit;
	}

	$email_sending_file = $APPLICATION_PATH."notify/sendemail.php";


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
				$tmp = 0;
				$comma_separated_email_list = "";
				while($tmp < 150 && $k < $emails_count)
				{
					$curr_email = $events_list[1][$e][0][$k];
					$comma_separated_email_list .= ((trim($comma_separated_email_list)=="")? $curr_email : ",".$curr_email); 
					$tmp++;
					$k++;
				}
				$k--;//To adjust the above extra increment...

				$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.base64_encode($comma_separated_email_list).' subject='.base64_encode($subject).' emailBody='.base64_encode($body).' fromAddressType='.$fromAddressType;
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