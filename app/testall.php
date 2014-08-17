<?php
$PATH = __DIR__."/";
//$PATH = "./";
include_once($PATH."classes/class.users.php");
include_once($PATH."classes/class.license.php");
include_once($PATH."classes/class.email.php");
include_once($PATH."classes/class.notification.php");
include_once($PATH."plugins/twilio/Services/Twilio.php");
include_once($PATH."classes/class.sharded.php");
include_once($PATH . 'plugins/thread/class.thread.php');
include_once($PATH."classes/class.church.php");
include_once($PATH."classes/class.utility.php");
include_once($PATH."classes/class.events.php");

/** /
error_reporting(-1);
ini_set("display_errors", "1");
$util = new Utility($PATH);
$input_html = "Hellos.....";
$target_file = "testpdf.pdf";
$force_download = 1;
$res = $util->downloadHTMLAsPDF($input_html, $target_file, $force_download);
print_r($res);
/**/

/** /
$events_obj = new Events($PATH);
$res = $events_obj->getEventsToNotifyNow();
echo "<pre>";
print_r($res);
echo "</pre>";
/**/
/* * /
$util_obj  = new Utility($PATH);
$ip_to_pass = $_SERVER["REMOTE_ADDR"];
$ip_to_pass = "37.10.29.31";
$country_code = $util_obj->getCountryCodeFromIP($ip_to_pass);
echo "--".$country_code;
$records = $util_obj->getRecordsFromIP($ip_to_pass);
echo $records->country_code;echo "<BR/>";
echo $records->country_code3;echo "<BR/>";
echo $records->country_name;echo "<BR/>";
echo $records->region;echo "<BR/>";
echo $records->city;echo "<BR/>";
echo $records->postal_code;echo "<BR/>";
echo $records->latitude;echo "<BR/>";
echo $records->longitude;echo "<BR/>";
echo $records->area_code;echo "<BR/>";
echo $records->dma_code;echo "<BR/>";
echo $records->metro_code;echo "<BR/>";
echo $records->continent_code;echo "<BR/>";


$church_obj  = new Church($PATH);
$result = $church_obj->getCountryInfoFromISO3Code($records->country_code3);
print_r($result);
/** /
$church_obj  = new Church($PATH);
$result = $church_obj->getAllChurchesList(7, 6, 8640000);

print_r($result);
/**/

/************************************************************************************** /
Sending email asynchronously
/************************************************************************************** /
$users_obj = new Users($PATH);
$signup_details = array();
$signup_details["customer_email"] = "nesanjoseph@yahoo.com";
$signup_details["first_name"] = "nesan";
$signup_details["last_name"] = "r";
$signup_details["church_name"] = "St.Marks";
$signup_details["church_addr"] = "Kovilambakkam";
$email_sending_file = $PATH."notify/sendemail.php";

$commands = array();

$welcome_email_content = $users_obj->sendSignupWelcomeEmail($signup_details, 1);
$fromAddressType = "info";
$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.urlencode($welcome_email_content[1][0]).' subject='.urlencode($welcome_email_content[1][1]).' emailBody='.urlencode($welcome_email_content[1][2]).' fromAddressType='.$fromAddressType;

/** /
$comma_separated_email_list = "nesanjoseph@yahoo.com";
$subject = "utut subject";
$body = "test body";
$fromAddressType = "soething";
$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.base64_encode($comma_separated_email_list).' subject='.base64_encode($subject).' emailBody='.base64_encode($body).' fromAddressType='.$fromAddressType;
/** /
$threads = new Multithread( $commands );
$threads->run();

foreach ( $threads->commands as $key=>$command )
{
	//echo "Command: ".$command."\n";
	echo "\nOutput: ".($threads->output[$key])."\n";
	//echo "Error: ".$threads->error[$key]."\n\n";
}
/**/
/**************************************************************************************/

/** /
$users_obj = new Users($PATH);
$signup_details = array();
$signup_details["customer_email"] = "nesanjoseph@yahoo.com";
$signup_details["first_name"] = "Nesan";
$signup_details["last_name"] = "Rajendran";
$signup_details["church_name"] = "Great church";
$signup_details["referrer_church_name"] = "Mattehs";
$signup_details["referral_church_name"] = "Johns";
$signup_details["new_validity"] = "22 Jun 2015";
$res = $users_obj->sendReferrerRewardedSuccessEmail($signup_details);
print_r($res);
/**/

/** /
$sharded_obj = new Sharded($PATH, "CS_95a18fe48afd74aef574ed917d30b853");
$result = $sharded_obj->cleanupAllTables();
print_r($result);
/**/


/** /
$sid = "AC8ac594144120c306d08a168840bb2ef6"; // Your Account SID from www.twilio.com/user/account
$token = "36cf1edb845cb960fe40bb04c4e7b66b"; // Your Auth Token from www.twilio.com/user/account

$client = new Services_Twilio($sid, $token);
$message = $client->account->messages->sendMessage(
  '+12626313131', // From a valid Twilio number
  '+919962131353', // Text this number
  "Hello Dude!"
);

print $message->sid;
/**/

/** /
echo "<pre>";
print_r($argv);
echo "</pre>";
echo __DIR__;
/**/
/** /
$users_obj = new Users($PATH);
$signup_details = array();
$signup_details["customer_email"] = "nesanjoseph@yahoo.com";
$signup_details["first_name"] = "Nesan";
$signup_details["last_name"] = "Rajendran";
$signup_details["church_name"] = "Great church";
$signup_details["church_addr"] = "Nanmangalam";
$res = $users_obj->sendSignupWelcomeEmail($signup_details);
print_r($res);
/**/
/** /
$note_obj = new Notification($PATH);
$event_detaila_array = array();
$event_detaila_arr["event_organizers_array"] = array("Nesan");
$event_detaila_arr["event_attendees_array"] = array("Vijaya");
$event_detaila_arr["event_title"] = "Need to discuss ChurchStack";
$event_detaila_arr["event_desc"] = "Need to discuss ChurchStack in detail";
$event_detaila_arr["event_date_time"] = "Thu 13 October, 2014 3:00 PM to 5:00 PM";
$event_detaila_arr["event_place"] = "Some place God thinks good for us";
$event_detaila_arr["event_email_recipients"] = array("nesanjoseph@gmail.com");
$note_result = $note_obj->sendEventReminderEmail($event_detaila_arr);
print_r($note_result);
/**/

/** /
$lic_obj = new License($PATH);
$lic_obj->sendInvoiceEmail(array());
/**/


/** /
$lic_obj = new License($PATH);
$lic_obj->setChurchID(28);
$coup_res = $lic_obj->processCouponCode("U9ESFM69P1", 100);
print_r($coup_res);
exit;
/**/

/** /
$lic_obj = new License($PATH);

$church_id=28;
$is_valid_for_all=0;
$discount_percentage=50;
$discount_flat_amount=0;
$minimum_subtotal_required=0;
$valid_till_timestamp=time()+36000;
$coupon_code_length=10;
$coupon_result = $lic_obj->createCoupon($church_id, $is_valid_for_all, $discount_percentage, $discount_flat_amount, $minimum_subtotal_required, $valid_till_timestamp, $coupon_code_length);

print_r($coupon_result);
/**/

/** /
$lic_obj = new License($PATH);
$lic_obj->setChurchID(16);
$invoice_details_array = array("Nesan R", "16 C, Thisaikaval St., Arumuganeri, Thootrhukudi Dist, Tamil Nadu, India, Chennai - 600 002", "Other", "0991901", "USD", 199, 1, 3, 6, 2, 4, 0, 0, 0, 0, 234, "S73MN4L57W", "Some notes", "PayPal", "Credit Card", "10.0.0.114", "nesanjoseph@gmail.com");
$invoiced_items_array = array();
$invoiced_items_array[0] = array(1, "Standard", "ST DES", 1, "1 year", 600, 30, 2, 60, 0);
$invoiced_items_array[1] = array(2, "Gold SMS", "GD DES", 2, "1 year", 600, 10, 2, 20, 0);
$res = $lic_obj->writeInitialPurchaseReport($invoice_details_array, $invoiced_items_array, $is_refund);
print_r($res)."<BR><BR>";
$unique_hash =  $res[1][1];
$transaction_id = "T551sdss3617";
$ivs = $lic_obj->updatePurchaseReport($unique_hash, $transaction_id, "cr3edi=t", 1, "payment received successfully", "P121", "The payment went through successfully...");
print_r($ivs);
/**/

/** /
$users_obj = new Users($PATH);
//$email_to_create = "nesanjoseesoseoshshs".rand(1,1000)."@yahoossstrss.com";
$email_to_create = "nesanjoseph@gmail.com";
$res = $users_obj->signUpWithChurchDetails("The New Church", "Madras", "sss", "jjnjn", "uhhu", $email_to_create, "9810374244", "", "nnn");

print_r($res);
/**/

/* * /
	$to_return = array();
	$to_return[0] = 0;
	$to_return[1] = "An error occurred while trying to create a dedicated setup, errors have been logged for analyzing.";
	@include "db/dbutil.php";
	$username = "root";
	$password = "admin";
	$host = "127.0.0.1";
	$db_name = "loco";
	$sharded_sql_file = "sql/sharded.sql";
	$db_output_file = "log/sharded_".$db_name.".log";
	$data_written_size = file_put_contents($db_output_file, "");
	$db_output_file_content = "";
	$create_db_command = 'mysql -u '.$username.' -p'.$password.' -h '.$host.' -A  -e "create database '.$db_name.' collate latin1_general_cs;"; > '.$db_output_file.' 2>&1 ';

	$outputs = array();
	$ret_val = "";
	$last_line_output = exec($create_db_command, $outputs, $ret_val);

	if(file_exists($db_output_file))
	{
		$db_output_file_content = trim(file_get_contents($db_output_file));
	}
	if(trim($db_output_file_content) != "")
	{
		$to_return[0] = 0;
		$to_return[1] = "Some error occurred while trying to create a dedicated setup, errors have been logged for analyzing.";
		return $to_return;
	}

	$create_tables_command = 'mysql -u '.$username.' -p'.$password.' -h '.$host.' -D '.$db_name.' -A  -e "source '.$sharded_sql_file.';"; > '.$db_output_file.' 2>&1 ';
	$outputs = array();
	$ret_val = "";
	$last_line_output = exec($create_tables_command, $outputs, $ret_val);
	$db_output_file_content = "";
	if(file_exists($db_output_file))
	{
		$db_output_file_content = trim(file_get_contents($db_output_file));
	}
	if(trim($db_output_file_content) != "")
	{
		$to_return[0] = 0;
		$to_return[1] = "Some error occurred while trying to create a dedicated setup, errors have been logged for analyzing.";
		return $to_return;
	}
	$to_return[0] = 1;
	$to_return[1] = "A dedicated setup has been successfully created for the user.";

	return $to_return;
	/**/

/** /
for($i=0; $i < 1; $i++)
{
$email_obj = new Email($PATH, EMAIL_FROM_DONOTREPLY);
$recipients = array();
$recipients['to_address'] = "nesanjoseph@yahoo.com";
$email_obj->setRecipients($recipients);
$email_obj->setSubject("set subjecting... to ..");

$body = 'bhbhbhbh';

$email_obj->setBody($body);
$result = $email_obj->sendEmail();
print_r($result);
}

	/** /
	public function setRecipients($recipients)
	{
		$this->to_address = $recipients['to_address'];
		$this->reply_to_address = $recipients['reply_to_address'];
		$this->reply_to_name = $recipients['reply_to_name'];
		$this->cc_address = $recipients['cc_address'];
		$this->bcc_address = $recipients['bcc_address'];
	}
	/**/
/**/
?>