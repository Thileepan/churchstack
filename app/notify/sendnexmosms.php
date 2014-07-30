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

	$numbers_list_csv = base64_decode($_GET["csvToNumbers"]);
	if(trim($numbers_list_csv) != "")
	{
		$numbers_list_array = explode(",", $numbers_list_csv);
	}
	$api_key = base64_decode($_GET["apiKey"]);
	$api_secret = base64_decode($_GET["apiSecret"]);
	$from_number = base64_decode($_GET["fromNumber"]);
	$sms_body = base64_decode($_GET["smsBody"]);

	//Set and Send SMS
	$sms = new NexmoMessage($api_key, $api_secret);

	for($e=0; $e < COUNT($numbers_list_array); $e++)
	{
		$to_number = $numbers_list_array[$e];
		$info = $sms->sendText( $to_number, $from_number, $sms_body );
		//echo $sms->displayOverview($info);
	}
?>
