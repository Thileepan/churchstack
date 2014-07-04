<?php

$admin_email = "sktgthill@gmail.com,shijuchintu@gmail.com,help@churchstack.com"; // Only Change This Mail

	
if ($_POST['new_message']) {
	
	function valid_email($email) {
  		return (! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
	}
	
	$name = $_POST['name'];
	
	$email = $_POST['message_email'];
	$email = str_replace(' ', '', $email);
		
	$user_message = $_POST['message'];
	
	$phone = $_POST['phone'];
	
	if(valid_email($email)) {
				
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
							
		$message = 
"Name : $name  \n
Email :	$email \n
Phone : $phone \n
Meesage : $user_message\n\n
_____________________________________________
PLEASE DO NOT REPLY \n\n
www.churchstack.com - Online Church Management Software

";
		$mail_status = mail($admin_email , "Website Contact Form", $message,
		"From: \"Website Contact Form\" <no-reply@$host>\r\n" .
		"X-Mailer: PHP/" . phpversion());
		unset($_SESSION['ckey']);

		if($mail_status) {
			echo 1;
		} else {
			echo 2;
		}
			
	} else {
		
		echo 2;
		
	}
				
}

?>