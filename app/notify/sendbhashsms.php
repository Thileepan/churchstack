<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."conf/config.php");
	include_once($APPLICATION_PATH."plugins/phpcurl/src/Curl/Curl.php");

//	print_r($argv);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

//	print_r($_GET);
	//echo $argv[1]."<BR>";

	$numbers_list_csv = urldecode($_GET["csvToNumbers"]);
	/**/
	if(trim($numbers_list_csv) != "")
	{
		$numbers_list_array = explode(",", $numbers_list_csv);
	}
	/**/
	$username = urldecode($_GET["username"]);
	$password = urldecode($_GET["password"]);
	$senderid = urldecode($_GET["senderid"]);
	$priority = urldecode($_GET["priority"]);
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

	if($alert_type=="birthdaygreetings" || $alert_type=="weddinggreetings" || $alert_type=="masscommunication")
	{
		$curl = new \Curl\Curl();
		$url_to_access = "http://bhashsms.com/api/sendmsg.php";
		for($e=0; $e < COUNT($numbers_list_array); $e++)
		{
			$current_number = $numbers_list_array[$e];
			$modified_sms_body = $sms_body;
			$modified_sms_body = str_replace("{{FIRST_NAME}}", $first_names_array[$e], $modified_sms_body);
			$modified_sms_body = str_replace("{{MIDDLE_NAME}}", $middle_names_array[$e], $modified_sms_body);
			$modified_sms_body = str_replace("{{LAST_NAME}}", $last_names_array[$e], $modified_sms_body);

			$get_array = array("user"=>$username, "pass"=>$password, "sender"=>$senderid, "phone"=>$current_number,"text"=>$modified_sms_body,"priority"=>$priority,"stype"=>"normal");
			$curl->get($url_to_access,$get_array);
			/**/
			if ($curl->error) {
				echo 'Error: ' . $curl->error_code . ': ' . $curl->error_message;
			} else {
				echo $curl->response;
			}
		}
	}
	else
	{
		$curl = new \Curl\Curl();
		$url_to_access = "http://bhashsms.com/api/sendmsg.php";
		$get_array = array("user"=>$username, "pass"=>$password, "sender"=>$senderid, "phone"=>$numbers_list_csv,"text"=>$sms_body,"priority"=>$priority,"stype"=>"normal");
		$curl->get($url_to_access,$get_array);
		/**/
		if ($curl->error) {
			echo 'Error: ' . $curl->error_code . ': ' . $curl->error_message;
		} else {
			echo $curl->response;
		}
	}

	/**/
?>

