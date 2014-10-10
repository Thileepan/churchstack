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

	$numbers_list_csv = base64_decode($_GET["csvToNumbers"]);
	/** /
	if(trim($numbers_list_csv) != "")
	{
		$numbers_list_array = explode(",", $numbers_list_csv);
	}
	/**/
	$username = base64_decode($_GET["username"]);
	$password = base64_decode($_GET["password"]);
	$senderid = base64_decode($_GET["senderid"]);
	$priority = base64_decode($_GET["priority"]);
	$sms_body = base64_decode($_GET["smsBody"]);

	use \Curl\Curl;
	$curl = new Curl();
	$url_to_access = "http://bhashsms.com/api/sendmsg.php";
	$get_array = array("user"=>$username, "pass"=>$password, "sender"=>$senderid, "phone"=>$numbers_list_csv,"text"=>$sms_body,"priority"=>$priority,"stype"=>"normal");
	$curl->get($url_to_access,$get_array);
	/**/
	if ($curl->error) {
		echo 'Error: ' . $curl->error_code . ': ' . $curl->error_message;
	} else {
		echo $curl->response;
	}
	/**/
?>

