<?php

$PATH = __DIR__."/";
@include_once($PATH."classes/class.users.php");
include_once($PATH."classes/class.license.php");
include_once($PATH."classes/class.email.php");
include_once($PATH."classes/class.notification.php");
include_once($PATH."plugins/twilio/Services/Twilio.php");

/**/
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
$signup_details["church_name"] = "CSI Good Shepherd Church";
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
$lic_obj->setChurchID(68);
$invoice_details_array = array("Nesan R", "16 C, Thisaikaval St., Arumuganeri, Thootrhukudi Dist, Tamil Nadu, India, Chennai - 600 002", "Other", "0991901", "USD", 199, 1, 3, 6, 2, 4, 0, 0, 0, 0, 234, "S73MN4L57W", "Some notes", "PayPal", "Credit Card", "10.0.0.114", "soleetin@yeollershusrd.com");
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
$email_to_create = "multis@soletttin.com";
$res = $users_obj->signUpWithChurchDetails("St. Andrews ", "Madras", "sss", "jjnjn", "uhhu", $email_to_create, "9810374244", "12345@12345.com");

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