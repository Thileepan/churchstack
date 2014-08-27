<?php
error_reporting(-1);
ini_set("display_errors", "1");
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);

	/** /
	if(trim($_SERVER['DOCUMENT_ROOT']) != "") {
		@require $APPLICATION_PATH.'error/404';
		exit;
	}
	/**/

	include_once($APPLICATION_PATH."conf/config.php");
	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.church.php");
	include_once($APPLICATION_PATH."classes/class.license.php");
	include_once($APPLICATION_PATH."classes/class.users.php");
	include_once($APPLICATION_PATH."classes/class.autonotifyreports.php");
	include_once($APPLICATION_PATH."classes/class.filelogger.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	//parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	$expiring_in_days = 1;//$_GET["expiringindays"];
	$expiring_in_days = (($expiring_in_days > 0)? $expiring_in_days : 1);//default 1
	$expiry_finding_range = 86400;//1 days in seconds

	//$sharded_db_processing_file = $APPLICATION_PATH."notify/processsharded.php";
	$church_obj = new Church($APPLICATION_PATH);
	echo "asasas";
	$churches_result = $church_obj->getAllChurchesList(8, $expiring_in_days, $expiry_finding_range);//7=>list license expiring in XX days churches alone (Active Chuches only)

	print_r($churches_result);

	$log_obj = new FileLogger($APPLICATION_PATH);
	if($churches_result[0]==1)
	{
		for($i=0; $i < COUNT($churches_result[1]); $i++)
		{
			$church_id = $churches_result[1][$i][0];
			$lic_expiry_date = $churches_result[1][$i][19];

			//Check if payment has been recieved or not
			$autoNotifyRep_obj = new AutoNotifyReports($APPLICATION_PATH);
			$is_charged = $autoNotifyRep_obj->isMonthlyRecurringPaymentReceived($church_id, $lic_expiry_date);
			echo $is_charged."<--";
			if($is_charged) {
				//Avoid charging again
				continue;
			} else {
				$log_obj->info("Initiating to charge the monthly recurring amount for church ID :".$church_id."; ");
				$lic_obj = new License($APPLICATION_PATH);
				$lic_obj->setChurchID($church_id);
				$subscription_details = $lic_obj->getCurrentSubscriptionPlanDetails();
				if($subscription_details[0] == 1)
				{
					//Write payment gateway interface codes to charge the monthly bill as per the plan.

					echo "Charging...";

					//Following has to be called finally...
					$autoNotifyRep_obj->insertMonthlyRecurringPaymentReceivedReport($church_id, $lic_expiry_date);
				}
				else
				{
					$log_obj->error("Subscription details could not be retrieved for Church ID :".$church_id."; Error : ".$subscription_details[1]);
				}
			}
		}
	}
?>