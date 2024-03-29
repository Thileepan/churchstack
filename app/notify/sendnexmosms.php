<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."plugins/nexmo/src/NexmoMessage.php");
	include_once($APPLICATION_PATH."conf/config.php");

//	print_r($argv);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

//	print_r($_GET);
	//echo $argv[1]."<BR>";

	$numbers_list_csv = urldecode($_GET["csvToNumbers"]);
	if(trim($numbers_list_csv) != "")
	{
		$numbers_list_array = explode(",", $numbers_list_csv);
	}
	$api_key = urldecode($_GET["apiKey"]);
	$api_secret = urldecode($_GET["apiSecret"]);
	$from_number = urldecode($_GET["fromNumber"]);
	$sms_body = urldecode($_GET["smsBody"]);

	$alert_type = urldecode($_GET["alertType"]);

	$delim_first_names = urldecode($_GET["delimFirstNames"]);
	if(trim($delim_first_names) != "")
	{
		$first_names_array = explode("/:/", $delim_first_names);
	}

	$delim_middle_names = urldecode($_GET["delimMiddleNames"]);
	if(trim($delim_middle_names) != "")
	{
		$middle_names_array = explode("/:/", $delim_middle_names);
	}
	$delim_last_names = urldecode($_GET["delimLastNames"]);
	if(trim($delim_last_names) != "")
	{
		$last_names_array = explode("/:/", $delim_last_names);
	}

	//Set and Send SMS
	$sms = new NexmoMessage($api_key, $api_secret);

	for($e=0; $e < COUNT($numbers_list_array); $e++)
	{
		$to_number = $numbers_list_array[$e];
		if($alert_type=="birthdaygreetings" || $alert_type=="weddinggreetings" || $alert_type=="masscommunication")
		{
			$modified_sms_body = $sms_body;
			$modified_sms_body = str_replace("{{FIRST_NAME}}", $first_names_array[$e], $modified_sms_body);
			$modified_sms_body = str_replace("{{MIDDLE_NAME}}", $middle_names_array[$e], $modified_sms_body);
			$modified_sms_body = str_replace("{{LAST_NAME}}", $last_names_array[$e], $modified_sms_body);
			$info = $sms->sendText( $to_number, $from_number, $modified_sms_body);
		}
		else
		{
			$info = $sms->sendText( $to_number, $from_number, $sms_body );
		}
		//echo $sms->displayOverview($info);
	}
?>
