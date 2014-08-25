<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	
	if(trim($_SERVER['DOCUMENT_ROOT']) != "") {
		@require $APPLICATION_PATH.'error/404';
		exit;
	}

	include_once($APPLICATION_PATH."conf/config.php");
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.church.php");
	include_once($APPLICATION_PATH."classes/class.license.php");
	include_once($APPLICATION_PATH."classes/class.users.php");
	include_once($APPLICATION_PATH."classes/class.autonotifyreports.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	//parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	$expiring_in_days = 6;//$_GET["expiringindays"];
	$expiring_in_days = (($expiring_in_days > 0)? $expiring_in_days : 6);//default 6
	$expiry_finding_range = 86400;//1 days in seconds

	//$sharded_db_processing_file = $APPLICATION_PATH."notify/processsharded.php";
	$church_obj = new Church($APPLICATION_PATH);
	$users_obj = new Users($APPLICATION_PATH);
	$churches_result = $church_obj->getAllChurchesList(7, $expiring_in_days, $expiry_finding_range);//7=>list trial expiring in XX days churches alone (Active Chuches only)

	$email_sending_file = $APPLICATION_PATH."notify/sendemail.php";
	$commands = array();
	if($churches_result[0]==1)
	{
		for($i=0; $i < COUNT($churches_result[1]); $i++)
		{
			$church_id = $churches_result[1][$i][0];
			$trial_expiry_date = $churches_result[1][$i][23];

			//Check if email has already been sent or not
			$autoNotifyRep_obj = new AutoNotifyReports($APPLICATION_PATH);
			$is_notification_sent = $autoNotifyRep_obj->isTrialExpiringNotificationSent($church_id, $trial_expiry_date);
			if($is_notification_sent) {
				//Avoid sending another email...
				continue;
			} else {
				$autoNotifyRep_obj->insertTrialExpiringNotifyReport($church_id, $trial_expiry_date);
			}
			
			$church_admin_result = array();
			$church_admin_result = $users_obj->getChurchAdminDetails($church_id);
			if($church_admin_result[0]==1)
			{
				$to_email = $church_admin_result[1][3];
				$trial_details = array();
				$trial_details["remaining_trial_days"] = $expiring_in_days;
				$trial_details["church_name"] = $churches_result[1][$i][1];
				$trial_details["email"] = $to_email;
				$email_contents = $church_obj->sendTrialExpiringEmail($trial_details, $to_email, 1);
				if($email_contents[0]==1)
				{
					$body = $email_contents[1][2];
					$fromAddressType = "account";
					$subject = $email_contents[1][1];
					$commands[] = PHP_EXE_PATH.' '.$email_sending_file.' csvToEmails='.urlencode($to_email).' subject='.urlencode($subject).' emailBody='.urlencode($body).' fromAddressType='.$fromAddressType;
				}
			}
		}
	}

	$churches_result = array();//resetting
	$churches_result = $church_obj->getAllChurchesList(8, $expiring_in_days, $expiry_finding_range);
	if($churches_result[0]==1)
	{
		for($i=0; $i < COUNT($churches_result[1]); $i++)
		{
			$church_id = $churches_result[1][$i][0];
			$license_expiry_date = $churches_result[1][$i][19];

			//Check if email has already been sent or not
			$autoNotifyRep_obj = new AutoNotifyReports($APPLICATION_PATH);
			$is_notification_sent = $autoNotifyRep_obj->isLicenseExpiringNotificationSent($church_id, $license_expiry_date);//8=>list license expiring in XX days churches alone (Active Chuches only)
			if($is_notification_sent) {
				//Avoid sending another email...
				continue;
			} else {
				$is_notification_sent = $autoNotifyRep_obj->insertLicenseExpiringNotifyReport($church_id, $license_expiry_date);
			}

			$church_admin_result = array();
			$church_admin_result = $users_obj->getChurchAdminDetails($church_id);
			if($church_admin_result[0]==1)
			{
				$to_email = $church_admin_result[1][3];
				$lic_expiry_details = array();
				$lic_expiry_details["remaining_license_days"] = $expiring_in_days;
				$lic_expiry_details["church_name"] = $churches_result[1][$i][1];
				$lic_expiry_details["email"] = $to_email;
				$email_contents = $church_obj->sendLicenseExpiringEmail($lic_expiry_details, $to_email, 1);
				if($email_contents[0]==1)
				{
					$body = $email_contents[1][2];
					$fromAddressType = "account";
					$subject = $email_contents[1][1];
					$commands[] = PHP_EXE_PATH.' '.$email_sending_file.' csvToEmails='.urlencode($to_email).' subject='.urlencode($subject).' emailBody='.urlencode($body).' fromAddressType='.$fromAddressType;
				}
			}
		}
	}

	$threads = new Multithread( $commands );
	$threads->run();
	/** /
	foreach ( $threads->commands as $key=>$command )
	{
		//echo "Command: ".$command."\n";
		echo "Output: ".$threads->output[$key];
		//echo "Error: ".$threads->error[$key]."\n\n";
	}
	/**/
?>