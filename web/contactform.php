<?php

$to_email = "sktgthill@gmail,bala.d7868@gmail.com,nesanjoseph@yahoo.com,shijuchintu@gmail.com";// Change your email address
	
if ($_POST['message']) {
	
	function valid_email($email) {
  		return (! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
	}
	
	$name = $_POST['name'];	
	$email = $_POST['email'];
	$email = str_replace(' ', '', $email);		
	$message = $_POST['message'];	
	$phone = $_POST['phone'];
	
	if(valid_email($email)) {
				
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
							
		$message = 
"$name - $email - $phone - has sent you message.\n\n
$message\n\n
_____________________________________________
PLEASE DO NOT REPLY \n\

";

        $mail_status = mail($to_email , "RapydCloud Contact Form", $message,
		"From: \"Website Contact Form\" <no-reply@$host>\r\n" .
		"X-Mailer: PHP/" . phpversion());
		//unset($_SESSION['ckey']);    
        
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