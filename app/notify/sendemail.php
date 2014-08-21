<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	
	if(trim($_SERVER['DOCUMENT_ROOT']) != "") {
		@require $APPLICATION_PATH.'error/404';
		exit;
	}

	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."plugins/PHPMailer/class.phpmailer.php");
	include_once($APPLICATION_PATH."classes/class.email.php");
	include_once($APPLICATION_PATH."conf/config.php");

//	print_r($argv);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

//	print_r($_GET);
	//echo $argv[1]."<BR>";

	$emails_list_csv = urldecode($_GET["csvToEmails"]);
	if(trim($emails_list_csv) != "")
	{
		$emails_list_array = explode(",", $emails_list_csv);
	}
	$cc_email_list_csv = urldecode($_GET["csvCCEmails"]);
	$bcc_email_list_csv = urldecode($_GET["csvBCCEmails"]);
	$subject = urldecode($_GET["subject"]);
	$body = urldecode($_GET["emailBody"]);
	$from_addr_type = $_GET["fromAddressType"];
	$from_address = EMAIL_FROM_INFO;
	if($from_addr_type=="eventreminder") {
		$from_address = EMAIL_FROM_DONOTREPLY;
	} else if($from_addr_type=="info") {
		$from_address = EMAIL_FROM_INFO;
	} else if($from_addr_type=="sales") {
		$from_address = EMAIL_FROM_SALES;
	} else if($from_addr_type=="account") {
		$from_address = EMAIL_FROM_SALES;
	}

	//Set and Send Email		
	$email_obj = new Email($APPLICATION_PATH, $from_address);
	$email_obj->setSubject($subject);
	$email_obj->setBody($body);
	for($e=0; $e < COUNT($emails_list_array); $e++)
	{
		$recipients = array();
		$recipients['to_address'] = $emails_list_array[$e];
		$recipients['cc_address'] = $cc_email_list_csv;
		$recipients['bcc_address'] = $bcc_email_list_csv;
		$email_obj->setRecipients($recipients);
		$email_result = $email_obj->sendEmail();
		//echo $email_result[1];
		/** /
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send email to the specified email address. ".$email_result[1];
		}
		/**/
	}
?>
