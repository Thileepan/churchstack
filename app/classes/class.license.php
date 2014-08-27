<?php

class License
{
	protected $db_conn;
	protected $church_id = -1;
	protected $user_id = -1;
	protected $email_id = "";

	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		include_once($this->APPLICATION_PATH . 'utils/utilfunctions.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
	}

	public function getChurchID()
	{
		return $this->church_id;
	}

	public function getUserID()
	{
		return $this->user_id;
	}

	public function getEmailID()
	{
		return $this->email_id;
	}

	public function setChurchID($church_id)
	{
		$this->church_id = $church_id;
		if($this->email_id == "" || $this->user_id <= 0) {
			$query = 'select USER_ID, EMAIL from USER_DETAILS where CHURCH_ID=? and ROLE_ID=1 limit 1';
			$result = $this->db_conn->Execute($query, array($church_id));
			if($result) {
				if(!$result->EOF) {
					$this->user_id = $result->fields[0];
					$this->email_id = $result->fields[1];
				}
			}
		}
	}

	public function setUserID($user_id)
	{
		$this->user_id = $user_id;
		if($this->church_id <= 0 || $this->email_id == "") {
			$query = 'select CHURCH_ID, EMAIL from USER_DETAILS where USER_ID=? limit 1';
			$result = $this->db_conn->Execute($query, array($user_id));
			if($result) {
				if(!$result->EOF) {
					$this->church_id = $result->fields[0];
					$this->email_id = $result->fields[1];
				}
			}
		}
	}

	public function setEmailID($email_id)
	{
		$this->email_id = $email_id;
		if($this->church_id <= 0 || $this->user_id <= 0) {
			$query = 'select CHURCH_ID, USER_ID from USER_DETAILS where EMAIL=? limit 1';
			$result = $this->db_conn->Execute($query, array($email_id));
			if($result) {
				if(!$result->EOF) {
					$this->church_id = $result->fields[0];
					$this->user_id = $result->fields[1];
				}
			}
		}
	}	

	public function getLicenseDetails($plan_type="")
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to get the license details of the church";
		if($this->db_conn)
		{
			$arg_to_pass = array();
			if($this->church_id > 0) {
				$query = 'select * from LICENSE_DETAILS where CHURCH_ID=?';
				$arg_to_pass[0] = $this->church_id;
				if(trim($plan_type) != "") {
					$query .= ' and PLAN_TYPE=? limit 1';
					$arg_to_pass[1] = $plan_type;
				}
			} else if($this->user_id > 0) {
				$query = 'select LD.* from LICENSE_DETAILS as LD, USER_DETAILS as UD where LD.CHURCH_ID=UD.CHURCH_ID and UD.USER_ID=?';
				$arg_to_pass[0] = $this->user_id;
				if(trim($plan_type) != "") {
					$query .= ' and LD.PLAN_TYPE=?  limit 1';
					$arg_to_pass[1] = $plan_type;
				}
			} else if(strlen($this->email_id) > 5) {//to make sure atleast it is a@b.in
				$query = 'select LD.* from LICENSE_DETAILS as LD, USER_DETAILS as UD where LD.CHURCH_ID=UD.CHURCH_ID and UD.EMAIL=?';
				$arg_to_pass[0] = $this->email_id;
				if(trim($plan_type) != "") {
					$query .= '  and LD.PLAN_TYPE=?  limit 1';
					$arg_to_pass[1] = $plan_type;
				}
			}

			$result = $this->db_conn->Execute($query, $arg_to_pass);
            
			/* * /
			create table LICENSE_DETAILS (
				CHURCH_ID INTEGER UNSIGNED NOT NULL,
				PLAN_ID SMALLINT UNSIGNED,
				LICENSE_EXPIRY_DATE DATETIME,
				LAST_INVOICE_ID BIGINT UNSIGNED,
				LAST_PURCHASE_DATE DATETIME,
				IS_ON_TRIAL TINYINT UNSIGNED NOT NULL DEFAULT 1,
				TRIAL_EXPIRY_DATE DATETIME,
				constraint LICENSE_DETAILS_FK_1 FOREIGN KEY (CHURCH_ID) REFERENCES CHURCH_DETAILS (CHURCH_ID),
				constraint LICENSE_DETAILS_FK_2 FOREIGN KEY (PLAN_ID) REFERENCES LICENSE_PLANS (PLAN_ID)
			)
			/**/
			$church_details = array();
			if($result) {
				while(!$result->EOF) {
					$church_id = $result->fields[0];
					$plan_id = $result->fields[1];
					$plan_type = $result->fields[2];
					$lic_expiry_date = $result->fields[3];
					$lic_expiry_timestamp = strtotime($lic_expiry_date);
					$last_invoice_id = $result->fields[4];
					$last_purchase_date = $result->fields[5];
					$last_purchase_timestamp = strtotime($last_purchase_date);
					$is_on_trial = $result->fields[6];
					$trial_expiry_date = $result->fields[7];
					$trial_expiry_timestamp = strtotime($trial_expiry_date);

					//Checking if we can allow further access to his account
					$current_time = time();
					$allow_usage = (($current_time < $trial_expiry_timestamp)? 1 : 0 );

					//Trial period is over, check if he has a valid license
					if($allow_usage == 0) {
						$allow_usage = (($current_time < $lic_expiry_timestamp)? 1 : 0);
					}

					//Calculate trial period remaining
					$remaining_trial_period_timestamp = $trial_expiry_timestamp - $current_time;
					$remaining_trial_period_days = ceil($remaining_trial_period_timestamp/86400);//round fractions UP 

					$church_details[] = array("church_id"=>$church_id, "plan_id"=>$plan_id, "plan_type"=>$plan_type, "lic_expiry_date"=>$lic_expiry_date, "lic_expiry_timestamp"=>$lic_expiry_timestamp, "last_invoice_id"=>$last_invoice_id, "last_purchase_date"=>$last_purchase_date, "last_purchase_timestamp"=>$last_purchase_timestamp, "is_on_trial"=>$is_on_trial, "trial_expiry_date"=>$trial_expiry_date, "trial_expiry_timestamp"=>$trial_expiry_timestamp, "allow_usage"=>$allow_usage, "remaining_trial_period_timestamp"=>$remaining_trial_period_timestamp, "remaining_trial_period_days"=>$remaining_trial_period_days);

					$result->MoveNext();
				}
				$to_return[0] = 1;
				$to_return[1] = $church_details;
			}
		}

		return $to_return;
	}

	public function applyLicense($plan_id, $plan_type, $invoice_id, $purchase_timestamp)
	{
		@include_once($this->APPLICATION_PATH . 'plugins/thread/class.thread.php');
		include_once($this->APPLICATION_PATH . 'classes/class.church.php');
		include_once($this->APPLICATION_PATH . 'classes/class.utility.php');
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was some error while applying the license";
		$validity_in_seconds = 0;
		if($this->db_conn)
		{
			$query_1 = 'select VALIDITY_IN_SECONDS from LICENSE_PLANS where PLAN_ID=? and PLAN_TYPE=? limit 1';
			$result_1 = $this->db_conn->Execute($query_1, array($plan_id, $plan_type));
			if($result_1) {
				if(!$result_1->EOF) {
					$validity_in_seconds = $result_1->fields[0];
				}
			}

			$plan_type_exists = 0;
			$previous_expiry_date = $purchase_timestamp;//Just to have an initial value
			$just_date_of_prev_exp_date = date("d", $previous_expiry_date);//To charge automatically on a fixed date.
			$query_2 = 'select PLAN_ID, LICENSE_EXPIRY_DATE from LICENSE_DETAILS where CHURCH_ID=? and PLAN_TYPE=? limit 1';
			$result_2 = $this->db_conn->Execute($query_2, array($this->church_id, $plan_type));
			if($result_2) {
				if(!$result_2->EOF) {
					$plan_type_exists = 1;
					$prev_exp_date = $result_2->fields[1];//returns datetime format

					$previous_expiry_date = strtotime($prev_exp_date);//converting to timestamp
					$just_date_of_prev_exp_date = date("d", $previous_expiry_date);//To charge automatically on a fixed date.
				}
			}

			//$lic_expiry_date_to_set = $previous_expiry_date+$validity_in_seconds;
			if($validity_in_seconds == 2592000)//Monthly Plan
			{
				$util_obj =  new Utility($this->APPLICATION_PATH);
				$months_to_add = 1;
				$lic_expiry_date_to_set = $util_obj->addMonthsToTimestamp($previous_expiry_date, $months_to_add);//Add one month with same date or the last day of the next month (if date overflows to the 3rd month)
			}
			else
			{
				$lic_expiry_date_to_set = $previous_expiry_date+$validity_in_seconds;
			}

			$to_return[3] = array($lic_expiry_date_to_set);

			//Recalculating the license expiry date for referral cases
			$referral_bonus_seconds = 30*24*60*60;//30 days
			$is_referral_valid = 0;
			$referrer_church_id = 0;
			$referral_church_name = "";
			$referrer_church_name = "";
			if($plan_type==1)//Applicable only for subscription
			{
				$referrer_result = $this->isValidForReferralBenefits($this->church_id);
				if($referrer_result[0]==1)
				{
					$is_referral_valid = 1;
					$referrer_church_id = $referrer_result[1][0];
					$referral_church_name = $referrer_result[1][1];
					//$lic_expiry_date_to_set = $lic_expiry_date_to_set+$referral_bonus_seconds;
					$util_obj =  new Utility($this->APPLICATION_PATH);
					$months_to_add = 1;
					$lic_expiry_date_to_set = $util_obj->addMonthsToTimestamp($lic_expiry_date_to_set, $months_to_add);//Add one month with same date or the last day of the next month (if date overflows to the 3rd month)
					
					$referral_new_validity = $lic_expiry_date_to_set;
				}
			}

			$is_on_trial = 0;
			if($plan_type_exists == 1) {
				$query = 'update LICENSE_DETAILS set PLAN_ID=?, LICENSE_EXPIRY_DATE=FROM_UNIXTIME(?), LAST_INVOICE_ID=?, LAST_PURCHASE_DATE=FROM_UNIXTIME(?), IS_ON_TRIAL=? where CHURCH_ID=? and PLAN_TYPE=?';
				$result = $this->db_conn->Execute($query, array($plan_id, $lic_expiry_date_to_set, $invoice_id, $purchase_timestamp, $is_on_trial, $this->church_id, $plan_type));
				if($result) {
					$to_return[0] = 1;
					$to_return[1] = "License applied successfully";
				}			
			} else {
				$query_3 = 'insert into LICENSE_DETAILS values(?,?,?,FROM_UNIXTIME(?),?,FROM_UNIXTIME(?),?,FROM_UNIXTIME(?))';
				$result_3 = $this->db_conn->Execute($query_3, array($this->church_id, $plan_id, $plan_type, $lic_expiry_date_to_set, $invoice_id, $purchase_timestamp, $is_on_trial, $purchase_timestamp));
				if($result_3) {
					$to_return[0] = 1;
					$to_return[1] = "License applied successfully";
				}			
			}

			//Activate Church whatever be the current status of the church
			$church_obj = new Church($this->APPLICATION_PATH);
			$church_obj->activateChurch($this->church_id);

			//Handle referral program stuff
			if($is_referral_valid==1 && $referrer_church_id > 0)
			{
				$subs_extension_result = $this->extendChurchSubscriptionValidity($referrer_church_id, $referral_bonus_seconds);
				if($subs_extension_result[0] == 1)
				{
					$referrer_new_validity = $subs_extension_result[2];
					$referrer_church_name = $subs_extension_result[3];
					$to_return[2] = array($this->church_id, $referral_church_name, $referral_new_validity, $referrer_church_id, $referrer_church_name, $referrer_new_validity);
				}
				else
				{
					$to_return[2] = array();
				}
			}
			else
			{
				$to_return[2] = array();
			}
		}
		return $to_return;
	}

	public function getLicensePlanDetails($plan_id)
	{
		$toReturn = array(); 
		$toReturn[0] = 0;
		$toReturn[1] = "Error encountered while trying to get the license plan details";
		$query = 'select * from LICENSE_PLANS where PLAN_ID=? limit 1';
		$result = $this->db_conn->Execute($query, array($plan_id));
		if($result) {
			if(!$result->EOF) {
				$plan_id = $result->fields[0];
				$plan_name = $result->fields[1];
				$plan_description = $result->fields[2];
				$plan_type = $result->fields[3];
				$max_count = $result->fields[4];
				$pricing = $result->fields[5];
				$validity_in_seconds = $result->fields[6];
				$validity_in_days = $result->fields[7];
				$toReturn[0] = 1;
				$toReturn[1] = array("plan_id"=>$plan_id, "plan_name"=>$plan_name, "plan_description"=>$plan_description, "plan_type"=>$plan_type, "max_count"=>$max_count, "pricing"=>$pricing, "validity_in_seconds"=>$validity_in_seconds, "validity_in_days"=>$validity_in_days);
			}
		}
		return $toReturn;
	}

	public function putInitialTrialLicenseEntry()
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was an error while trying to create a trial account for the user.";
		$trial_start_timestamp = time();
		if($this->church_id > 0)
		{
			if($this->db_conn)
			{
				$plan_id = 1;//For Trial accounts
				$plan_type = 1;//subscription type
				$license_plan_details_arr = $this->getLicensePlanDetails($plan_id);
				$trial_seconds = 30*24*60*60;//30 days
				if($license_plan_details_arr[0] == 1) {
					$trial_seconds = $license_plan_details_arr[1]["validity_in_seconds"];
				}
				$license_expiry_date = $trial_start_timestamp+$trial_seconds;
				$trial_expiry_date = $trial_start_timestamp+$trial_seconds;
				$query = 'insert into LICENSE_DETAILS values(?,?,?,FROM_UNIXTIME(?),?,FROM_UNIXTIME(?),?,FROM_UNIXTIME(?))';
				$result = $this->db_conn->Execute($query, array($this->church_id, $plan_id, $plan_type, $license_expiry_date, 0, $trial_start_timestamp, 1, $trial_expiry_date));

				if($result) {
					$to_return[0] = 1;
					$to_return[1] = "Trial license activated";
				}
			}
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to recognize the church to create the trial account";
		}

		return $to_return;
	}

	public function writeInitialPurchaseReport($invoice_details_array, $invoiced_items_array, $is_refund)
	{
		@include_once($this->APPLICATION_PATH."classes/class.church.php");
		//"$invoice_details_array" should be in the following order : array($billing_full_name, $billing_address, $other_address, $phone, $currency_code, $subtotal, $additional_charge, $discount_percentage, $discount_amount, $tax_percentage, $tax_amount, $tax_2_percentage, $tax_2_amount, $vat_percentage, $vat_amount, $net_total, $coupon_code, $invoice_notes, $payment_gateway, $payment_mode, $ip_address, $email, ...)
		//"$invoiced_items_array" is a multidimentionsal array and every item in "$invoiced_items_array" should be like : array(plan_id, plan_name, plan_desc, plan_type, validity_period_text, validity_in_seconds, plan_cost, quantity, total_cost, is_autorenewal_enabled) ; //Other details will be filled in automatically here...
		$billing_full_name = $invoice_details_array[0];
		$billing_address = $invoice_details_array[1];
		$other_address = $invoice_details_array[2];
		$phone = $invoice_details_array[3];
		$currency_code = $invoice_details_array[4];
		$subtotal = $invoice_details_array[5];
		$additional_charge = $invoice_details_array[6];
		$discount_percentage = $invoice_details_array[7];
		$discount_amount = $invoice_details_array[8];
		$tax_percentage = $invoice_details_array[9];
		$tax_amount = $invoice_details_array[10];
		$tax_2_percentage = $invoice_details_array[11];
		$tax_2_amount = $invoice_details_array[12];
		$vat_percentage = $invoice_details_array[13];
		$vat_amount = $invoice_details_array[14];
		$net_total = $invoice_details_array[15];
		$coupon_code = $invoice_details_array[16];
		$invoice_notes = $invoice_details_array[17];
		$payment_gateway = $invoice_details_array[18];
		$payment_mode = $invoice_details_array[19];
		$ip_address = $invoice_details_array[20];
		$email = $invoice_details_array[21];

		$invoice_date = time();
		$church_obj = new Church($this->APPLICATION_PATH);
		$church_details = $church_obj->getInformationOfAChurch($this->church_id);
		if($church_details[0] != 1) {
			//return error....
		}
		$church_name = $church_details[1][1];

		$unique_hash = strtoupper(md5(time().$billing_full_name.$net_paid_amount.$ip_address.rand(1,10000).rand(1,10000)));
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while initiating the purchase process";
		if($this->church_id > 0)
		{
			if($this->db_conn)
			{
				$col_values_array = array();
				$col_values_array[0] = 0;//Invoice ID will be auto incremented by itself.
				$col_values_array[1] = $invoice_date;
				$col_values_array[2] = "";
				$col_values_array[3] = "";
				$col_values_array[4] = $unique_hash;
				$col_values_array[5] = $this->church_id;
				$col_values_array[6] = $church_name;
				$col_values_array[7] = $this->user_id;
				$col_values_array[8] = $email;//$this->email_id;
				$col_values_array[9] = $billing_full_name;
				$col_values_array[10] = $billing_address;
				$col_values_array[11] = $other_address;
				$col_values_array[12] = $phone;
				$col_values_array[13] = $currency_code;
				$col_values_array[14] = $subtotal;
				$col_values_array[15] = $additional_charge;
				$col_values_array[16] = $discount_percentage;
				$col_values_array[17] = $discount_amount;
				$col_values_array[18] = $tax_percentage;
				$col_values_array[19] = $tax_amount;
				$col_values_array[20] = $tax_2_percentage;
				$col_values_array[21] = $tax_2_amount;
				$col_values_array[22] = $vat_percentage;
				$col_values_array[23] = $vat_amount;
				$col_values_array[24] = $net_total;
				$col_values_array[25] = $coupon_code;
				$col_values_array[26] = $invoice_notes;
				$col_values_array[27] = $payment_gateway;
				$col_values_array[28] = $payment_mode;
				$col_values_array[29] = $ip_address;
				$col_values_array[30] = 0;//STATUS; 0=>Taken to the payment gateway, pending for the next status or confirmation from the pg.
				$col_values_array[31] = "Awaiting the confirmation of payment from the payment gateway";
				$col_values_array[32] = "";
				$col_values_array[33] = "";
				$col_values_array[34] = $invoice_date;
				$col_values_array[35] = ((trim($is_refund)!="")?trim($is_refund) : 0);//Default is NOT a refund
				$query_1 = "insert into INVOICE_REPORT values (?,FROM_UNIXTIME(?),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,FROM_UNIXTIME(?),?)";
				$result_1 = $this->db_conn->Execute($query_1, $col_values_array);
				if(!$result_1) {
					//return some error...
				}

				$invoice_id = -1;
				$query_2 = 'select INVOICE_ID from INVOICE_REPORT where UNIQUE_HASH=? limit 1';
				$result_2 = $this->db_conn->Execute($query_2, array($unique_hash));
				if($result_2) {
					if(!$result_2->EOF) {
						$invoice_id = $result_2->fields[0];
					}
				}

				if($invoice_id <= 0) {
					//return error...
				}

				for($i=0; $i < COUNT($invoiced_items_array); $i++)
				{
					//array(prod_id, prod_name, prod_desc, validity_period_text, validity_in_seconds, prod_cost, quantity, total_cost, is_autorenewal_enabled)
					//$next_due_date = $invoice_date+$validity_in_seconds;
					$col_values_array_2 = array();
					$sub_order_id = $invoice_id."_".$i;
					$col_values_array_2[0] = $invoice_id;
					$col_values_array_2[1] = $sub_order_id;
					$col_values_array_2[2] = $invoiced_items_array[$i][0];
					$col_values_array_2[3] = $invoiced_items_array[$i][1];
					$col_values_array_2[4] = $invoiced_items_array[$i][2];
					$col_values_array_2[5] = $invoiced_items_array[$i][3];
					$col_values_array_2[6] = $invoiced_items_array[$i][4];
					$col_values_array_2[7] = $invoiced_items_array[$i][5];
					$col_values_array_2[8] = $invoiced_items_array[$i][6];
					$col_values_array_2[9] = $invoiced_items_array[$i][7];
					$col_values_array_2[10] = $invoiced_items_array[$i][8];
					$col_values_array_2[11] = $invoiced_items_array[$i][9];
					$query_3 = "insert into INVOICED_ITEMS values (?,?,?,?,?,?,?,?,?,?,?,?)";
					$result_3 = $this->db_conn->Execute($query_3, $col_values_array_2);
					if($result_3) {
						$inv_rep_det_arr = array($invoice_id, $unique_hash);
						$toReturn[0] = 1;//Success status code
						$toReturn[1] = $inv_rep_det_arr;
					} else {
						//do something...
					}
				}
			}
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to recognize the account";
		}

		return $toReturn;
	}

	public function updatePurchaseReport($unique_hash, $transaction_id, $payment_mode, $purchase_status, $purchase_remarks, $pg_status_code, $pg_status_remarks)
	{
		@include_once($this->APPLICATION_PATH . 'conf/config.php');

		$last_update_date = time();
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was some error while updating the specified purchase/invoice record";
		if($this->db_conn)
		{
			//Getting the church id and coupon code used
			$uniq_hash_found = 0;
			$coupon_code_used = "";
			$query = 'select CHURCH_ID, COUPON_CODE from INVOICE_REPORT where UNIQUE_HASH=? limit 1';
			$result = $this->db_conn->Execute($query, array($unique_hash));
			if($result) {
				if(!$result->EOF) {
					$this->church_id = $result->fields[0];
					$coupon_code_used = $result->fields[1];
					$coupon_code_used = trim($coupon_code_used);
					$uniq_hash_found = 1;
				}
			}

			if($uniq_hash_found != 1)
			{
				$toReturn[0] = 0;
				$toReturn[1] = "Unable to find the specified transaction. Please check the transaction details";
				return $toReturn;
			}

			$col_values_array = array();
			$col_values_array[0] = $transaction_id;
			$col_values_array[1] = $payment_mode;
			$col_values_array[2] = $purchase_status;
			$col_values_array[3] = $purchase_remarks;
			$col_values_array[4] = $pg_status_code;
			$col_values_array[5] = $pg_status_remarks;
			$col_values_array[6] = $last_update_date;
			$col_values_array[7] = $unique_hash;
			$query_1 = "update INVOICE_REPORT set TRANSACTION_ID=?, PAYMENT_MODE=?, PURCHASE_STATUS_CODE=?, PURCHASE_STATUS_REMARKS=?, PG_STATUS_CODE=?, PG_STATUS_REMARKS=?, LAST_UPDATE_DATE=FROM_UNIXTIME(?) where UNIQUE_HASH=?";
			$result_1 = $this->db_conn->Execute($query_1, $col_values_array);
			if($result_1) {
				$toReturn[0] = 1;
				$toReturn[1] = "Successfully updated the purchase/invoice details";
				if($purchase_status == 1) {//Check this place once again....
					$toReturn[0] = 0;
					$toReturn[1] = "There was some error when trying to apply license as per the products purchased";
					//$inv_id_result = $this->getInvoiceIDFromUniqueHash($unique_hash);
					$inv_rep_result = $this->getInvoiceReportFromUniqueHash($unique_hash);
					if($inv_rep_result[0]==1)
					{
						$is_subscription_purchase_done = 0;
						$new_license_extra_details = array();
						$invoice_id = $inv_rep_result[1][0];
						$invoiced_items = $this->getInvoicedItemsList($invoice_id);
						if($invoiced_items[0]==1) {//Success case
							/** /
							$purchase_details_arr = array();
							$purchase_details_arr["invoice_id"] = $inv_rep_result[1][0];
							$purchase_details_arr["invoice_date"] = date("d M Y, h:i A", strtotime($inv_rep_result[1][1]));
							$purchase_details_arr["transaction_id"] = $inv_rep_result[1][2];
							$purchase_details_arr["email"] = $inv_rep_result[1][8];
							$purchase_details_arr["billing_name"] = $inv_rep_result[1][9];
							$purchase_details_arr["billing_addr"] = $inv_rep_result[1][10];
							$purchase_details_arr["subtotal"] = $inv_rep_result[1][14];
							$purchase_details_arr["discount_amount"] = $inv_rep_result[1][17];
							$purchase_details_arr["net_total"] = $inv_rep_result[1][24];
							$purchase_details_arr["payment_gateway"] = $inv_rep_result[1][27];
							$purchase_details_arr["payment_mode"] = $inv_rep_result[1][28];
							$purchase_details_arr["invoiced_items_array"] = array();
							/**/
							$is_atleast_one_update_successful = 0;
							$referral_stuff = array();
							for($p=0; $p < COUNT($invoiced_items[1]); $p++)
							{
								/** /
								$curr_item_array = array();
								$curr_item_array["item_name"] = $invoiced_items[1][$p][3];
								$curr_item_array["item_desc"] = $invoiced_items[1][$p][4];
								$curr_item_array["item_unit_price"] = $invoiced_items[1][$p][8];
								$curr_item_array["item_quantity"] = $invoiced_items[1][$p][9];
								$curr_item_array["item_total"] = $invoiced_items[1][$p][10];
								$purchase_details_arr["invoiced_items_array"][] = $curr_item_array;
								/**/
								$plan_id = $invoiced_items[1][$p][2];
								$plan_type = $invoiced_items[1][$p][5];
								$apply_lic_result = $this->applyLicense($plan_id, $plan_type, $invoice_id, $last_update_date);
								if($apply_lic_result[0]==1) {
									$is_atleast_one_update_successful = 1;
									if(COUNT($apply_lic_result) > 2) {
										$referral_stuff = $apply_lic_result[2];
									}

									if($plan_type==1 && COUNT($apply_lic_result) > 3) {
										$is_subscription_purchase_done = 1;
										$new_license_extra_details["new_validity_timestamp"] = $apply_lic_result[3][0];
										$new_license_extra_details["auto_renewal_status"] = $invoiced_items[1][$p][11];
									}
								}
							}

							//Terminating the coupon code used
							if($is_atleast_one_update_successful==1 && $coupon_code_used != "") {
								$this->terminateCouponCode($coupon_code_used, 0);//Terminate only if code is specific to a church
							}

							/************************************************************************************** /
							Sending email asynchronously
							/**************************************************************************************/
							$email_sending_file = __DIR__."/../notify/sendemail.php";//Take care of this part
							$email_sending_file = str_replace("\\", "/", $email_sending_file);
							$commands = array();

							$invoice_rep_email_content =  $this->prepareAndSendOrderDetailsEmail($invoice_id, "", 1);
							$fromAddressType = "sales";
							$commands[] = '"'.PHP_EXE_PATH.'" '.$email_sending_file.' csvToEmails='.urlencode($invoice_rep_email_content[1][0]).' subject='.urlencode($invoice_rep_email_content[1][1]).' emailBody='.urlencode($invoice_rep_email_content[1][2]).' fromAddressType='.$fromAddressType.' csvBCCEmails='.urlencode(INVOICE_COPY_TO_ADDRESS);

							if($is_subscription_purchase_done==1) {
								$thankyou_church_id = $inv_rep_result[1][5];
								$thankyou_church_name = $inv_rep_result[1][6];
								$thankyou_recipient_email = $inv_rep_result[1][8];
								$thankyou_details = array();
								$thankyou_details["new_validity"] = date("d M Y", $new_license_extra_details["new_validity_timestamp"]);
								$thankyou_details["church_name"] = $thankyou_church_name;
								$thankyou_details["auto_debit_status"] = (($new_license_extra_details["auto_renewal_status"]==1)?"Yes":"No");
								$thankyou_details["customer_email"] = $thankyou_recipient_email;
								$thankyou_details["amount_paid"] = $inv_rep_result[1][13]." ".$inv_rep_result[1][24];
								$thankyou_mail_content = $this->sendSubscriptionThankYouEmail($thankyou_details, 1);
								$fromAddressType = "sales";
								$commands[] = '"'.PHP_EXE_PATH.'" '.$email_sending_file.' csvToEmails='.urlencode($thankyou_mail_content[1][0]).' subject='.urlencode($thankyou_mail_content[1][1]).' emailBody='.urlencode($thankyou_mail_content[1][2]).' fromAddressType='.$fromAddressType;
							}

							if(COUNT($referral_stuff) > 0)
							{
								$referral_rewards_mail_details = array();
								$referral_rewards_mail_details["customer_email"] = $invoice_rep_email_content[1][0];//To Email
								$referral_rewards_mail_details["referrer_church_name"] = $referral_stuff[4];
								$referral_rewards_mail_details["referral_church_name"] = $referral_stuff[1];
								$referral_rewards_mail_details["new_validity"] = date("d M Y", $referral_stuff[2]);
								$referral_rew_email_content = $this->sendReferralRewardedSuccessEmail($referral_rewards_mail_details, 1);
								$fromAddressType = "info";
								$commands[] = '"'.PHP_EXE_PATH.'" '.$email_sending_file.' csvToEmails='.urlencode($referral_rew_email_content[1][0]).' subject='.urlencode($referral_rew_email_content[1][1]).' emailBody='.urlencode($referral_rew_email_content[1][2]).' fromAddressType='.$fromAddressType;
								
								$referrer_church_id = $referral_stuff[3];
								$users_obj = new Users($this->APPLICATION_PATH);
								$church_admin_details = $users_obj->getChurchAdminDetails($referrer_church_id);
								$referrer_email_address = "";
								if($church_admin_details[0]==1) {
									$referrer_email_address = $church_admin_details[1][3];
									$referrer_rewards_mail_details = array();
									$referrer_rewards_mail_details["customer_email"] = $referrer_email_address;//To Email
									$referrer_rewards_mail_details["referrer_church_name"] = $referral_stuff[4];
									$referrer_rewards_mail_details["referral_church_name"] = $referral_stuff[1];
									$referrer_rewards_mail_details["new_validity"] = date("d M Y", $referral_stuff[5]);
									$referrer_rew_email_content = $this->sendReferrerRewardedSuccessEmail($referrer_rewards_mail_details, 1);
									$fromAddressType = "info";
									$commands[] = '"'.PHP_EXE_PATH.'" '.$email_sending_file.' csvToEmails='.urlencode($referrer_rew_email_content[1][0]).' subject='.urlencode($referrer_rew_email_content[1][1]).' emailBody='.urlencode($referrer_rew_email_content[1][2]).' fromAddressType='.$fromAddressType;
								}
							}

							$threads = new Multithread( $commands );
							$threads->run();

							/** /
							foreach ( $threads->commands as $key=>$command )
							{
								//echo "Command: ".$command."\n";
								//echo "\nOutput: ".$threads->output[$key]."\n";
								//echo "Error: ".$threads->error[$key]."\n\n";
							}
							/**/
							/**************************************************************************************/

							if($is_atleast_one_update_successful==1) {
								$toReturn[0] = 1;
								$toReturn[1] = "License applied successfully";
							} else {
								$toReturn[0] = 0;
								$toReturn[1] = "There was some error while applying the license, please contact support to resolve the issues.";
							}
						}
					}
				}
			} else {
				$toReturn[0] = 0;
				$toReturn[1] = "Updating the purchase/invoice details failed";
			}
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "No DB Connection available";
		}

		return $toReturn;
	}

	public function getInvoicedItemsList($invoice_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "No items to list";
		if($this->db_conn)
		{
			$query = 'select * from INVOICED_ITEMS where INVOICE_ID=?';
			$result = $this->db_conn->Execute($query, array($invoice_id));
			if($result) {
                if(!$result->EOF) {
					$inv_items_array = array();
                    while(!$result->EOF)
                    {
                        $tmp_invoice_id = $result->fields[0];
                        $suborder_id = $result->fields[1];
						$plan_id = $result->fields[2];
						$plan_name = $result->fields[3];
                        $plan_desc = $result->fields[4];
                        $plan_type = $result->fields[5];
                        $validity_text = $result->fields[6];
						$validity_seconds = $result->fields[7];
						$plan_cost = $result->fields[8];
						$quantity = $result->fields[9];
						$total_cost = $result->fields[10];
						$is_auto_renewal = $result->fields[11];
						$inv_items_array[] = array($tmp_invoice_id, $suborder_id, $plan_id, $plan_name, $plan_desc, $plan_type, $validity_text, $validity_seconds, $plan_cost, $quantity, $total_cost, $is_auto_renewal);
                        
						$result->MoveNext();
                    }
					$toReturn[0] = 1;
					$toReturn[1] = $inv_items_array;
                }
            }
		}
		else
		{
			$toReturn[0] = 1;
			$toReturn[1] = "No DB Connection Available";
		}
		return $toReturn;
	}

	public function processCouponCode($coupon_code, $input_total)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "The coupon/promo code you have entered is either invalid or it has expired. Try using another coupon/promo code.";

		$church_id = 0;
		if($this->church_id > 0)
		{
			$church_id = $this->church_id;
		}
		if($this->db_conn)
		{
			$query = 'select DISCOUNT_PERCENTAGE, DISCOUNT_FLAT_AMOUNT, MINIMUM_SUBTOTAL from COUPONS where COUPON_CODE=? and (CHURCH_ID=? or VALID_FOR_ALL=1) and VALIDITY > SYSDATE() and IS_USED=0';
			$result = $this->db_conn->Execute($query, array($coupon_code, $church_id));
			if($result) {
                if(!$result->EOF) {
					$discount_details = array();
					$discount_percentage = $result->fields[0];
					$discount_flat_amount = $result->fields[1];
					$minimum_subtotal = $result->fields[2];
					if($input_total < floor($minimum_subtotal)) {//Be Generous !!! 
						$toReturn[0] = 0;
						$toReturn[1] = "The coupon code you have entered requires the subtotal to be ".ceil($minimum_subtotal)." or above";
						return $toReturn;
					}

					//$subtotal, $additional_charge, $tax_percentage, $tax_amount, $tax_2_percentage, $tax_2_amount, $vat_percentage, $vat_amount, $net_total, $coupon_code
					$discount_amount = 0;
					if($discount_percentage > 0) {
						$discount_amount = round($input_total*($discount_percentage/100), 2);
					} else if($discount_flat_amount > 0 && $input_total < $discount_flat_amount) {
						$discount_amount = round($discount_flat_amount, 2);
					}
					
					$output_total = round(($input_total-$discount_amount), 2);

					$discount_details = array();
					$discount_details[0] = $input_total;
					$discount_details[1] = $output_total;
					$discount_details[2] = $discount_amount;
					$discount_details[3] = $discount_percentage;

					$toReturn[0] = 1;
					$toReturn[1] = $discount_details;
                }
            }
		}
		else
		{
			$toReturn[0] = 1;
			$toReturn[1] = "No DB Connection Available";
		}

		return $toReturn;
	}

	public function generateCouponCode($length=10)
	{
		if($length <= 0) {
			$length = 10;
		}
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$chars_length = strlen($chars);
		$res = "";
		for ($i = 0; $i < $length; $i++) {
			$res .= $chars[mt_rand(0, $chars_length-1)];
		}

		return $res;
	}

	public function terminateCouponCode($coupon_code, $force_termination=0)
	{
		//$force_termination has to be "1" to terminate generic coupons also. This has to be "0" if only church specific coupons have to be terminated

		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Failed to terminate the coupon";

		$church_id = 0;
		if($this->church_id > 0) {
			$church_id = $this->church_id;
		}

		if($this->db_conn)
		{
			$is_coupon_exists = 0;
			$is_valid_for_all = 0;
			$query = 'select VALID_FOR_ALL from COUPONS where COUPON_CODE=? and (CHURCH_ID=? or VALID_FOR_ALL=1) limit 1';
			$result = $this->db_conn->Execute($query, array($coupon_code, $church_id));
			if($result) {
				if(!$result->EOF) {
					$is_valid_for_all = $result->fields[0];
					$is_coupon_exists = 1;
				}
			}

			if($force_termination==1 || ($is_coupon_exists==1 && $is_valid_for_all != 1)) {
				//$validity_to_set = time() - 86400;//Just to make sure the coupon is really terminated
				$query_2 = 'update COUPONS set IS_USED=1 where COUPON_CODE=?';
				$result_2 = $this->db_conn->Execute($query_2, array($coupon_code));
				if($result_2) {
					$toReturn[0] = 1;
					$toReturn[1] = "Coupon terminated successfully";
				}			
			}
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "No DB Connection Available";
		}

		return $toReturn;
	}

	public function createCoupon($church_id, $is_valid_for_all, $discount_percentage, $discount_flat_amount, $minimum_subtotal_required, $valid_till_timestamp, $coupon_code_length=10, $custom_coupon_code="")
	{
		if($coupon_code_length <= 0) {
			$coupon_code_length = 10;
		}
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to create coupon";
		if($this->db_conn)
		{
			$attempt = 0;
			$coupon_code = "";
			if(trim($custom_coupon_code) != "") {
				$coupon_code = trim($custom_coupon_code);
			}

			if(trim($custom_coupon_code) == "")
			{
				while($attempt < 10)
				{
					$coupon_code = $this->generateCouponCode($coupon_code_length);
					$query = 'select VALIDITY from COUPONS where COUPON_CODE=? limit 1';
					$result = $this->db_conn->Execute($query, array(trim($coupon_code)));
					if($result) {
						if(!$result->EOF) {
							//Nothing to do here...
						} else {
							break;
						}
					} else {
						break;
					}
					$attempt++;
				}

				if($attempt >= 10) {
					$toReturn[0] = 0;
					$toReturn[1] = "Too many failures while trying to generate a random coupon code!!!";
					return $toReturn;
				}
			}
			else
			{
				$query = 'select VALIDITY from COUPONS where COUPON_CODE=? limit 1';
				$result = $this->db_conn->Execute($query, array(trim($coupon_code)));
				if($result) {
					if(!$result->EOF) {
						$toReturn[0] = 0;
						$toReturn[1] = "The coupon code you have entered already exists!!!";
						return $toReturn;
					}
				}
			}

			if(trim($coupon_code) != "") {
				$query_2 = 'insert into COUPONS values(?,?,?,?,?,?,FROM_UNIXTIME(?),?,?)';
				$result_2 = $this->db_conn->Execute($query_2, array(0, trim($coupon_code), $church_id, $discount_percentage, $discount_flat_amount, $minimum_subtotal_required, $valid_till_timestamp, $is_valid_for_all, 0));
				if($result_2) {
					$toReturn[0] = 1;
					$toReturn[1] = "Coupon created successfully";
					$toReturn[2] = array(trim($coupon_code), $church_id, $discount_percentage, $discount_flat_amount, $minimum_subtotal_required, $valid_till_timestamp, $is_valid_for_all);
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "Unable to insert coupon to the table";
				}
			}
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "No DB Connection Available";
		}

		return $toReturn;
	}

	public function getAllPurchaseReports($input_inv_id=0)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "No payments to list";
		if($this->db_conn)
		{
		   if(trim($input_inv_id) == "" || $input_inv_id <= 0) {
			   $query = 'select * from INVOICE_REPORT order by INVOICE_ID DESC';
			   $result = $this->db_conn->Execute($query);
		   } else if(trim($input_inv_id) != "" && $input_inv_id > 0) {
			   $query = 'select * from INVOICE_REPORT where INVOICE_ID=?';
			   $result = $this->db_conn->Execute($query, array($input_inv_id));
		   } else {
			   $query = 'select * from INVOICE_REPORT order by INVOICE_ID DESC';
			   $result = $this->db_conn->Execute($query);
		   }
            
           if($result) {
			   if(!$result->EOF)
			   {
				   $all_invoices = array();
					while(!$result->EOF) {
						$invoice_details = array();
						$invoice_id = $result->fields[0];
						$invoice_date = $result->fields[1];
						$transaction_id = $result->fields[2];
						$reference_id = $result->fields[3];
						$unique_hash = $result->fields[4];
						$church_id = $result->fields[5];
						$church_name = $result->fields[6];
						$user_id = $result->fields[7];
						$email = $result->fields[8];
						$billing_name = $result->fields[9];
						$billing_address = $result->fields[10];
						$other_address = $result->fields[11];
						$phone = $result->fields[12];
						$currency_code = $result->fields[13];
						$subtotal = $result->fields[14];
						$additional_charge = $result->fields[15];
						$discount_percentage = $result->fields[16];
						$discount_amount = $result->fields[17];
						$tax_percentage = $result->fields[18];
						$tax_amount = $result->fields[19];
						$tax_2_percentage = $result->fields[20];
						$tax_2_amount = $result->fields[21];
						$vat_percentage = $result->fields[22];
						$vat_amount = $result->fields[23];
						$net_total = $result->fields[24];
						$coupon_code = $result->fields[25];
						$invoice_notes = $result->fields[26];
						$payment_gateway = $result->fields[27];
						$payment_mode = $result->fields[28];
						$ip_address = $result->fields[29];
						$purchase_status_code = $result->fields[30];
						$purchase_status_remarks = $result->fields[31];
						$pg_status_code = $result->fields[32];
						$pg_status_remarks = $result->fields[33];
						$last_update_date = $result->fields[34];
						$is_refund = $result->fields[35];

						$invoice_details = array($invoice_id, $invoice_date, $transaction_id, $reference_id, $unique_hash, $church_id, $church_name, $user_id, $email, $billing_name, $billing_address, $other_address, $phone, $currency_code, $subtotal, $additional_charge, $discount_percentage, $discount_amount, $tax_percentage, $tax_amount, $tax_2_percentage, $tax_2_amount, $vat_percentage, $vat_amount, $net_total, $coupon_code, $invoice_notes, $payment_gateway, $payment_mode, $ip_address, $purchase_status_code, $purchase_status_remarks, $pg_status_code, $pg_status_remarks, $last_update_date, $is_refund);

						$all_invoices[] = $invoice_details;

						$result->MoveNext();
					}
					$toReturn[0] = 1;
					$toReturn[1] = $all_invoices;
			   }
            }
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "No DB Connection Available";
		}
		return $toReturn;
	}
	
	public function getInvoiceIDFromUniqueHash($unique_hash)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was some error while trying to get the invoice id";
		if($this->db_conn)
		{
			if(trim($unique_hash) != "") {
				$query = 'select INVOICE_ID from INVOICE_REPORT where UNIQUE_HASH=? limit 1';
				$result = $this->db_conn->Execute($query, array(trim($unique_hash)));
				if($result) {
					if(!$result->EOF) {
						$to_return[0] = 1;
						$to_return[1] = $result->fields[0];
					}
				}
			}
		}
		return $to_return;
	}
	
	public function getInvoiceReportFromUniqueHash($unique_hash)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was some error while trying to get the invoice report";
		if($this->db_conn)
		{
			if(trim($unique_hash) != "") {
				$query = 'select INVOICE_ID, INVOICE_DATE, TRANSACTION_ID, REFERENCE_ID, UNIQUE_HASH, CHURCH_ID, CHURCH_NAME, USER_ID, EMAIL, BILLING_NAME, BILLING_ADDRESS, OTHER_ADDRESS, PHONE, CURRENCY_CODE, SUBTOTAL, ADDITIONAL_CHARGE, DISCOUNT_PERCENTAGE, DISCOUNT_AMOUNT, TAX_PERCENTAGE, TAX_AMOUNT, TAX_2_PERCENTAGE, TAX_2_AMOUNT, VAT_PERCENTAGE, VAT_AMOUNT, NET_TOTAL, COUPON_CODE, INVOICE_NOTES, PAYMENT_GATEWAY, PAYMENT_MODE, IP_ADDRESS, PURCHASE_STATUS_CODE, PURCHASE_STATUS_REMARKS, PG_STATUS_CODE, PG_STATUS_REMARKS, LAST_UPDATE_DATE, IS_REFUND from INVOICE_REPORT where UNIQUE_HASH=? limit 1';
				$result = $this->db_conn->Execute($query, array(trim($unique_hash)));
				if($result) {
					if(!$result->EOF) {
						$invoice_details = array();
						$invoice_id = $result->fields[0];
						$invoice_date = $result->fields[1];
						$transaction_id = $result->fields[2];
						$reference_id = $result->fields[3];
						$unique_hash = $result->fields[4];
						$church_id = $result->fields[5];
						$church_name = $result->fields[6];
						$user_id = $result->fields[7];
						$email = $result->fields[8];
						$billing_name = $result->fields[9];
						$billing_address = $result->fields[10];
						$other_address = $result->fields[11];
						$phone = $result->fields[12];
						$currency_code = $result->fields[13];
						$subtotal = $result->fields[14];
						$additional_charge = $result->fields[15];
						$discount_percentage = $result->fields[16];
						$discount_amount = $result->fields[17];
						$tax_percentage = $result->fields[18];
						$tax_amount = $result->fields[19];
						$tax_2_percentage = $result->fields[20];
						$tax_2_amount = $result->fields[21];
						$vat_percentage = $result->fields[22];
						$vat_amount = $result->fields[23];
						$net_total = $result->fields[24];
						$coupon_code = $result->fields[25];
						$invoice_notes = $result->fields[26];
						$payment_gateway = $result->fields[27];
						$payment_mode = $result->fields[28];
						$ip_address = $result->fields[29];
						$purchase_status_code = $result->fields[30];
						$purchase_status_remarks = $result->fields[31];
						$pg_status_code = $result->fields[32];
						$pg_status_remarks = $result->fields[33];
						$last_update_date = $result->fields[34];
						$is_refund = $result->fields[35];

						$invoice_details = array($invoice_id, $invoice_date, $transaction_id, $reference_id, $unique_hash, $church_id, $church_name, $user_id, $email, $billing_name, $billing_address, $other_address, $phone, $currency_code, $subtotal, $additional_charge, $discount_percentage, $discount_amount, $tax_percentage, $tax_amount, $tax_2_percentage, $tax_2_amount, $vat_percentage, $vat_amount, $net_total, $coupon_code, $invoice_notes, $payment_gateway, $payment_mode, $ip_address, $purchase_status_code, $purchase_status_remarks, $pg_status_code, $pg_status_remarks, $last_update_date, $is_refund);

						$to_return[0] = 1;
						$to_return[1] = $invoice_details;
					}
				}
			}
		}
		return $to_return;
	}

	public function getAllCouponsList($filterType=0)
	{
		/** /
			$filterType :
				0 or empty=> List all
				1 => list active coupons (church specific)
				2 => list expired (unused) coupons (church specific)
				3 => list used/terminated (which in turn expired) coupons (church specific)
				4 => list active coupons (which is valid for all)
				5 => list expired/terminated coupons (which was valid for all)
		/**/
		$toReturn = array();
		$all_coupons = array();
		if($this->db_conn)
		{
		   $query = 'select * from COUPONS order by COUPON_ID DESC';
		   if($filterType==0 or trim($filterType) == "") {
			   $query = 'select * from COUPONS order by COUPON_ID DESC';
		   } else if($filterType==1) {
			   $query = 'select * from COUPONS where VALID_FOR_ALL!=1 and IS_USED=0 and VALIDITY > NOW()';
		   } else if($filterType==2) {
			   $query = 'select * from COUPONS where VALID_FOR_ALL!=1 and IS_USED=0 and VALIDITY <= NOW()';
		   } else if($filterType==3) {
			   $query = 'select * from COUPONS where VALID_FOR_ALL!=1 and IS_USED=1';
		   } else if($filterType==4) {
			   $query = 'select * from COUPONS where VALID_FOR_ALL=1 and IS_USED=0 and VALIDITY > NOW()';
		   } else if($filterType==5) {
			   $query = 'select * from COUPONS where VALID_FOR_ALL=1 and (IS_USED=1 or VALIDITY <= NOW())';
		   }
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
			   if(!$result->EOF)
			   {
					while(!$result->EOF) {
						$coupon_details = array();
						$coupon_id = $result->fields[0];
						$coupon_code = $result->fields[1];
						$church_id = $result->fields[2];
						$disc_perc = $result->fields[3];
						$disc_flat_amt = $result->fields[4];
						$min_subtotal = $result->fields[5];
						$validity = $result->fields[6];
						$valid_for_all = $result->fields[7];
						$is_used = $result->fields[8];
						$coupon_details = array($coupon_id, $coupon_code, $church_id, $disc_perc, $disc_flat_amt, $min_subtotal, $validity, $valid_for_all, $is_used);
						$all_coupons[] = $coupon_details;

						$result->MoveNext();
					}
					$toReturn[0] = 1;
					$toReturn[1] = $all_coupons;
			   }
            }
        }
		return $toReturn;
	}
	
	public function getCouponInformation($coupon_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Error while getting the coupon information";
		if($this->db_conn)
		{
		   $query = 'select COUPON_ID, COUPON_CODE, CHURCH_ID, DISCOUNT_PERCENTAGE, DISCOUNT_FLAT_AMOUNT, MINIMUM_SUBTOTAL, VALIDITY, VALID_FOR_ALL, IS_USED from COUPONS where COUPON_ID=?';
		   $result = $this->db_conn->Execute($query, array($coupon_id));
            
           if($result) {
			   if(!$result->EOF)
			   {
					$coupon_details = array();
					$coupon_id = $result->fields[0];
					$coupon_code = $result->fields[1];
					$church_id = $result->fields[2];
					$disc_perc = $result->fields[3];
					$disc_flat_amt = $result->fields[4];
					$min_subtotal = $result->fields[5];
					$validity = $result->fields[6];
					$valid_for_all = $result->fields[7];
					$is_used = $result->fields[8];
					$coupon_details = array($coupon_id, $coupon_code, $church_id, $disc_perc, $disc_flat_amt, $min_subtotal, $validity, $valid_for_all, $is_used);

					$toReturn[0] = 1;
					$toReturn[1] = $coupon_details;
			   }
            }
        }
		return $toReturn;
	}

	public function terminateCouponID($coupon_id, $force_termination=0)
	{
		//$force_termination has to be "1" to terminate generic coupons also. This has to be "0" if only church specific coupons have to be terminated

		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Failed to terminate the coupon";

		$church_id = 0;
		if($this->church_id > 0) {
			$church_id = $this->church_id;
		}

		if($this->db_conn)
		{
			$is_coupon_exists = 0;
			$is_valid_for_all = 0;
			$query = 'select VALID_FOR_ALL from COUPONS where COUPON_ID=? and (CHURCH_ID=? or VALID_FOR_ALL=1) limit 1';
			$result = $this->db_conn->Execute($query, array($coupon_id, $church_id));
			if($result) {
				if(!$result->EOF) {
					$is_valid_for_all = $result->fields[0];
					$is_coupon_exists = 1;
				}
			}

			if($force_termination==1 || ($is_coupon_exists==1 && $is_valid_for_all != 1)) {
				//$validity_to_set = time() - 86400;//Just to make sure the coupon is really terminated
				$query_2 = 'update COUPONS set IS_USED=1 where COUPON_ID=?';
				$result_2 = $this->db_conn->Execute($query_2, array($coupon_id));
				if($result_2) {
					$toReturn[0] = 1;
					$toReturn[1] = "Coupon terminated successfully";
				}			
			}
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "No DB Connection Available";
		}

		return $toReturn;
	}

	public function reactivateCouponID($coupon_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Failed to reactivate the coupon";

		$church_id = 0;
		if($this->church_id > 0) {
			$church_id = $this->church_id;
		}

		if($this->db_conn)
		{
			$query_2 = 'update COUPONS set IS_USED=0 where COUPON_ID=?';
			$result_2 = $this->db_conn->Execute($query_2, array($coupon_id));
			if($result_2) {
				$toReturn[0] = 1;
				$toReturn[1] = "Coupon reactivated successfully";
			}			
		}
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "No DB Connection Available";
		}

		return $toReturn;
	}

	public function sendInvoiceEmail($purchase_details_arr, $target_email, $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$inv_template_file = $this->APPLICATION_PATH."templates/email/invoice.html";
		$invoice_report = "";
		if(file_exists($inv_template_file))
		{
			$invoice_report = trim(file_get_contents($inv_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the invoice report";
		}

		//Prepare the html string for invoiced items
		$invoiced_items_row = "";
		$single_item_row = "";
		$single_item_comm_start = "<!--ITEM_ROW_START";
		$single_item_comm_end = "ITEM_ROW_END-->";
		$single_item_html_start_pos = strpos($invoice_report, $single_item_comm_start);
		$single_item_html = substr($invoice_report, $single_item_html_start_pos+strlen($single_item_comm_start));
		$single_item_html_end_pos = strpos($single_item_html, $single_item_comm_end);
		$single_item_html = substr($single_item_html, 0, strlen($single_item_html)-(strlen($single_item_html) - $single_item_html_end_pos));
		$single_item_row = $single_item_html;
		for($k=0; $k < COUNT($purchase_details_arr["invoiced_items_array"]); $k++)
		{
			$single_item_row = $single_item_html;
			$single_item_row = str_replace("{{ITEM_NAME}}", $purchase_details_arr["invoiced_items_array"][$k]["item_name"], $single_item_row);
			$single_item_row = str_replace("{{ITEM_DESC}}", $purchase_details_arr["invoiced_items_array"][$k]["item_desc"], $single_item_row);
			$single_item_row = str_replace("{{ITEM_UNIT_PRICE}}", $purchase_details_arr["invoiced_items_array"][$k]["item_unit_price"], $single_item_row);
			$single_item_row = str_replace("{{ITEM_QUANTITY}}", $purchase_details_arr["invoiced_items_array"][$k]["item_quantity"], $single_item_row);
			$single_item_row = str_replace("{{ITEM_TOTAL}}", $purchase_details_arr["invoiced_items_array"][$k]["item_total"], $single_item_row);
			$invoiced_items_row .= $single_item_row;
		}

		//Replacing place holder with values
		$invoice_report = str_replace("<!--ALL_ITEMS_ROWS-->", $invoiced_items_row, $invoice_report);
		$invoice_report = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $invoice_report);
		$invoice_report = str_replace("{{CUSTOMER_NAME}}", $purchase_details_arr["billing_name"], $invoice_report);
		$invoice_report = str_replace("{{BILLING_ADDRESS}}", $purchase_details_arr["billing_addr"], $invoice_report);
		$invoice_report = str_replace("{{CUSTOMER_EMAIL}}", $purchase_details_arr["email"], $invoice_report);
		$invoice_report = str_replace("{{ORDER_NUMBER}}", $purchase_details_arr["invoice_id"], $invoice_report);
		$invoice_report = str_replace("{{TRANSACTION_ID}}", $purchase_details_arr["transaction_id"], $invoice_report);
		$invoice_report = str_replace("{{ORDER_DATE}}", $purchase_details_arr["invoice_date"], $invoice_report);
		$invoice_report = str_replace("{{AMOUNT_PAID}}", $purchase_details_arr["net_total"], $invoice_report);
		$invoice_report = str_replace("{{SUBTOTAL}}", $purchase_details_arr["subtotal"], $invoice_report);
		$invoice_report = str_replace("{{DISCOUNT}}", $purchase_details_arr["discount_amount"], $invoice_report);
		$invoice_report = str_replace("{{NET_TOTAL}}", $purchase_details_arr["net_total"], $invoice_report);
		$invoice_report = str_replace("{{PAYMENT_GATEWAY}}", $purchase_details_arr["payment_gateway"], $invoice_report);
		$invoice_report = str_replace("{{PAYMENT_MODE}}", $purchase_details_arr["payment_mode"], $invoice_report);


		$subject = "Payment Received - Your Invoice Details";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = ((trim($target_email) != "")? trim($target_email) : $purchase_details_arr["email"]);
			$contents_array[1] = $subject;
			$contents_array[2] = $invoice_report;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}
		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_SALES);
		$recipients = array();
		$recipients['to_address'] = ((trim($target_email) != "")? trim($target_email) : $purchase_details_arr["email"]);
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($invoice_report);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Invoice report sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send invoice report to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function prepareAndSendOrderDetailsEmail($invoice_id, $target_email="", $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was some problem while preparing the invoice report and emailing it to the specified recipient";
		$inv_rep_result = $this->getAllPurchaseReports($invoice_id);
		if($inv_rep_result[0]==1)
		{
			$purchase_details_arr = array();
			$purchase_details_arr["invoice_id"] = $inv_rep_result[1][0][0];
			$purchase_details_arr["invoice_date"] = date("d M Y, h:i A", strtotime($inv_rep_result[1][0][1]));
			$purchase_details_arr["transaction_id"] = $inv_rep_result[1][0][2];
			$purchase_details_arr["email"] = $inv_rep_result[1][0][8];
			$purchase_details_arr["billing_name"] = $inv_rep_result[1][0][9];
			$purchase_details_arr["billing_addr"] = $inv_rep_result[1][0][10];
			$purchase_details_arr["subtotal"] = $inv_rep_result[1][0][14];
			$purchase_details_arr["discount_amount"] = $inv_rep_result[1][0][17];
			$purchase_details_arr["net_total"] = $inv_rep_result[1][0][24];
			$purchase_details_arr["payment_gateway"] = $inv_rep_result[1][0][27];
			$purchase_details_arr["payment_mode"] = $inv_rep_result[1][0][28];
			$purchase_details_arr["invoiced_items_array"] = array();

			$invoiced_items = $this->getInvoicedItemsList($invoice_id);
			if($invoiced_items[0]==1)
			{
				for($p=0; $p < COUNT($invoiced_items[1]); $p++)
				{
					$curr_item_array = array();
					$curr_item_array["item_name"] = $invoiced_items[1][$p][3];
					$curr_item_array["item_desc"] = $invoiced_items[1][$p][4];
					$curr_item_array["item_unit_price"] = $invoiced_items[1][$p][8];
					$curr_item_array["item_quantity"] = $invoiced_items[1][$p][9];
					$curr_item_array["item_total"] = $invoiced_items[1][$p][10];
					$purchase_details_arr["invoiced_items_array"][] = $curr_item_array;
				}
				$emailing_result = $this->sendInvoiceEmail($purchase_details_arr, $target_email, $just_return_contents);
				if($just_return_contents==1) {
					return $emailing_result;
				}
				if($emailing_result[0]==1) {
					$to_return[0] = 1;
					$to_return[1] = "Invoice report emailed successfully to the recipient";
				} else {
					$to_return = $emailing_result;
				}
			}
			else
			{
				$to_return = $invoiced_items;
			}
		}
		else
		{
			$to_return = $inv_rep_result;
		}

		return $to_return;
	}
	
	public function isValidForReferralBenefits($church_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while trying to get the referral info.";
		$referrer_church_id = 0;
		$data_found = 0;
		if($this->db_conn)
		{
		   $query = 'select cd.REFERRER_CHURCH_ID, cd.CHURCH_NAME from LICENSE_DETAILS as ld, CHURCH_DETAILS as cd where ld.CHURCH_ID=? and ld.CHURCH_ID=cd.CHURCH_ID and ld.IS_ON_TRIAL=1 and ld.PLAN_TYPE=1 limit 1';
		   $result = $this->db_conn->Execute($query, array($church_id));
            
           if($result) {
                if(!$result->EOF) {
					$referrer_church_id = $result->fields[0];
					$curr_church_name = $result->fields[1];
					if($referrer_church_id > 0)
					{
						$toReturn[0] = 1;
						$toReturn[1] = array($referrer_church_id, $curr_church_name);
					}
					else
					{
						$toReturn[0] = 0;
						$toReturn[1] = "No referrer is associated with the account.";
					}
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "No details associated with the account could be retrieved.";
				}
            } else {
				$toReturn[0] = 0;
				$toReturn[1] = "There was an error when fetching the account details";
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to get connection to the system.";
		}
		return $toReturn;
	}

	public function extendChurchSubscriptionValidity($church_id, $seconds_to_extend)
	{
		include_once($this->APPLICATION_PATH."classes/class.utility.php");
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while trying extend the subscription.";
		if($this->db_conn)
		{
		   $query = 'select LD.LICENSE_EXPIRY_DATE, LD.IS_ON_TRIAL, LD.TRIAL_EXPIRY_DATE, CD.CHURCH_NAME from LICENSE_DETAILS as LD, CHURCH_DETAILS as CD where LD.CHURCH_ID=? and LD.PLAN_TYPE=1 and LD.CHURCH_ID=CD.CHURCH_ID limit 1';
		   $result = $this->db_conn->Execute($query, array($church_id));
            
           if($result) {
                if(!$result->EOF) {
					$lic_expiry_date = $result->fields[0];
					$is_on_trial = $result->fields[1];
					$trial_expiry_date = $result->fields[2];
					$church_name = $result->fields[3];

					$expiry_date_to_set = time();
					if($is_on_trial==1)
					{
						//$expiry_date_to_set = strtotime($trial_expiry_date)+$seconds_to_extend;
						$expiry_date_to_set = strtotime($trial_expiry_date);
						if($seconds_to_extend == 2592000)//One Month
						{
							$util_obj =  new Utility($this->APPLICATION_PATH);
							$months_to_add = 1;
							$expiry_date_to_set = $util_obj->addMonthsToTimestamp($expiry_date_to_set, $months_to_add);//Add one month with same date or the last day of the next month (if date overflows to the 3rd month)
						}
						else
						{
							$expiry_date_to_set = strtotime($trial_expiry_date)+$seconds_to_extend;
						}

						$query_2 = 'update LICENSE_DETAILS set TRIAL_EXPIRY_DATE=FROM_UNIXTIME(?), LICENSE_EXPIRY_DATE=FROM_UNIXTIME(?) where CHURCH_ID=? and PLAN_TYPE=1';
						$result_2 = $this->db_conn->Execute($query_2, array($expiry_date_to_set, $expiry_date_to_set, $church_id));
						if($result_2) {
							$toReturn[0] = 1;
							$extended_days = floor($seconds_to_extend/(24*60*60));
							$toReturn[1] = $extended_days." more days added to the subscription validity";
							$toReturn[2] = $expiry_date_to_set;
							$toReturn[3] = $church_name;
						}
					}
					else
					{
						//$expiry_date_to_set = strtotime($lic_expiry_date)+$seconds_to_extend;
						$expiry_date_to_set = strtotime($lic_expiry_date);
						if($seconds_to_extend == 2592000)//One Month
						{
							$util_obj =  new Utility($this->APPLICATION_PATH);
							$months_to_add = 1;
							$expiry_date_to_set = $util_obj->addMonthsToTimestamp($expiry_date_to_set, $months_to_add);//Add one month with same date or the last day of the next month (if date overflows to the 3rd month)
						}
						else
						{
							$expiry_date_to_set = strtotime($lic_expiry_date)+$seconds_to_extend;
						}
						$query_2 = 'update LICENSE_DETAILS set LICENSE_EXPIRY_DATE=FROM_UNIXTIME(?) where CHURCH_ID=? and PLAN_TYPE=1';
						$result_2 = $this->db_conn->Execute($query_2, array($expiry_date_to_set, $church_id));
						if($result_2) {
							$toReturn[0] = 1;
							$extended_days = floor($seconds_to_extend/(24*60*60));
							$toReturn[1] = $extended_days." more days added to the subscription validity";
							$toReturn[2] = $expiry_date_to_set;
							$toReturn[3] = $church_name;
						}
					}
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "No details associated with the account could be retrieved.";
				}
            } else {
				$toReturn[0] = 0;
				$toReturn[1] = "There was an error when fetching the account details";
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to get connection to the system.";
		}
		return $toReturn;
	}

	public function sendReferralRewardedSuccessEmail($user_details, $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$referral_prog_template_file = $this->APPLICATION_PATH."templates/email/referralrewarded.html";
		$referral_prog_letter = "";
		if(file_exists($referral_prog_template_file))
		{
			$referral_prog_letter = trim(file_get_contents($referral_prog_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the referral program email";
		}

		//Replacing place holder with values
		$referral_prog_letter = str_replace("{{PRODUCT_WEBSITE}}", PRODUCT_WEBSITE, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{NEW_VALIDITY}}", $user_details["new_validity"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{REFERRER_CHURCH_NAME}}", $user_details["referrer_church_name"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{REFERRAL_CHURCH_NAME}}", $user_details["referral_church_name"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{REFERRAL_PROGRAM_URL}}", REFERRAL_PROGRAM_URL, $referral_prog_letter);

		$subject = "You have been rewarded - ".PRODUCT_NAME."'s Referral Program";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = $user_details["customer_email"];
			$contents_array[1] = $subject;
			$contents_array[2] = $referral_prog_letter;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}

		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_INFO);
		$recipients = array();
		$recipients['to_address'] = $user_details["customer_email"];
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($referral_prog_letter);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Referral rewards email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send referral rewards email to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function sendReferrerRewardedSuccessEmail($user_details, $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$referral_prog_template_file = $this->APPLICATION_PATH."templates/email/referrerrewarded.html";
		$referral_prog_letter = "";
		if(file_exists($referral_prog_template_file))
		{
			$referral_prog_letter = trim(file_get_contents($referral_prog_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the referrer rewards email";
		}

		//Replacing place holder with values
		$referral_prog_letter = str_replace("{{PRODUCT_WEBSITE}}", PRODUCT_WEBSITE, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{NEW_VALIDITY}}", $user_details["new_validity"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{REFERRER_CHURCH_NAME}}", $user_details["referrer_church_name"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{REFERRAL_CHURCH_NAME}}", $user_details["referral_church_name"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{REFERRAL_PROGRAM_URL}}", REFERRAL_PROGRAM_URL, $referral_prog_letter);

		$subject = "You have been rewarded - ".PRODUCT_NAME."'s Referral Program";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = $user_details["customer_email"];
			$contents_array[1] = $subject;
			$contents_array[2] = $referral_prog_letter;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}
		
		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_INFO);
		$recipients = array();
		$recipients['to_address'] = $user_details["customer_email"];
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($referral_prog_letter);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Referrer rewards email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send referrer rewards email to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function sendSubscriptionThankYouEmail($user_details, $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$thankyou_template_file = $this->APPLICATION_PATH."templates/email/paidthankyou.html";
		$thankyou_letter = "";
		if(file_exists($thankyou_template_file))
		{
			$thankyou_letter = trim(file_get_contents($thankyou_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the referral program email";
		}

		//Replacing place holder with values
		$thankyou_letter = str_replace("{{PRODUCT_WEBSITE}}", PRODUCT_WEBSITE, $thankyou_letter);
		$thankyou_letter = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $thankyou_letter);
		$thankyou_letter = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $thankyou_letter);
		$thankyou_letter = str_replace("{{NEW_VALIDITY}}", $user_details["new_validity"], $thankyou_letter);
		$thankyou_letter = str_replace("{{CHURCH_NAME}}", $user_details["church_name"], $thankyou_letter);
		$thankyou_letter = str_replace("{{AUTO_DEBIT_STATUS}}", $user_details["auto_debit_status"], $thankyou_letter);
		$thankyou_letter = str_replace("{{AMOUNT_PAID}}", $user_details["amount_paid"], $thankyou_letter);

		$subject = "Thank You for upgrading/renewing your ".PRODUCT_NAME."'s account";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = $user_details["customer_email"];
			$contents_array[1] = $subject;
			$contents_array[2] = $thankyou_letter;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}

		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_SALES);
		$recipients = array();
		$recipients['to_address'] = $user_details["customer_email"];
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($thankyou_letter);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Thank you email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send thank you email to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function getCurrentSubscriptionPlanDetails()
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "No details could be fetched";
		if($this->church_id <= 0) {
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to identify the target account";
			return $toReturn;
		}
		if($this->db_conn)
		{
			$query = 'select ii.INVOICE_ID, ii.SUBORDER_ID, ii.PLAN_ID, ii.PLAN_NAME, ii.PLAN_DESCRIPTION, ii.PLAN_TYPE, ii.VALIDITY_PERIOD_TEXT, ii.VALIDITY_IN_SECONDS, ii.PLAN_COST, ii.QUANTITY, ii.TOTAL_COST, ii.IS_AUTORENEWAL_ENABLED from INVOICED_ITEMS as ii, LICENSE_DETAILS as ld, INVOICE_REPORT as ir where ld.CHURCH_ID=? and ld.CHURCH_ID=ir.CHURCH_ID and ld.PLAN_TYPE=1 and ld.LAST_INVOICE_ID=ir.INVOICE_ID and ii.INVOICE_ID=ir.INVOICE_ID and ii.PLAN_TYPE=1 limit 1';
			$result = $this->db_conn->Execute($query, array($this->church_id));
			if($result) {
                if(!$result->EOF) {
					$inv_items_array = array();
                    if(!$result->EOF)
                    {
                        $tmp_invoice_id = $result->fields[0];
                        $suborder_id = $result->fields[1];
						$plan_id = $result->fields[2];
						$plan_name = $result->fields[3];
                        $plan_desc = $result->fields[4];
                        $plan_type = $result->fields[5];
                        $validity_text = $result->fields[6];
						$validity_seconds = $result->fields[7];
						$plan_cost = $result->fields[8];
						$quantity = $result->fields[9];
						$total_cost = $result->fields[10];
						$is_auto_renewal = $result->fields[11];
						$inv_items_array = array($tmp_invoice_id, $suborder_id, $plan_id, $plan_name, $plan_desc, $plan_type, $validity_text, $validity_seconds, $plan_cost, $quantity, $total_cost, $is_auto_renewal);
                    }
					$toReturn[0] = 1;
					$toReturn[1] = $inv_items_array;
                }
            }
		}
		else
		{
			$toReturn[0] = 1;
			$toReturn[1] = "No DB Connection Available";
		}
		return $toReturn;
	}
}

?>