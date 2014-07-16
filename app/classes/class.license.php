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
			$query = 'select USER_ID, EMAIL from USER_DETAILS where CHURCH_ID=? limit 1';
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
			$query_2 = 'select PLAN_ID, LICENSE_EXPIRY_DATE from LICENSE_DETAILS where CHURCH_ID=? and PLAN_TYPE=? limit 1';
			$result_2 = $this->db_conn->Execute($query_2, array($this->church_id, $plan_type));
			if($result_2) {
				if(!$result_2->EOF) {
					$plan_type_exists = 1;
					$prev_exp_date = $result_2->fields[1];//returns datetime format

					$previous_expiry_date = strtotime($prev_exp_date);//converting to timestamp
				}
			}

			$lic_expiry_date_to_set = $previous_expiry_date+$validity_in_seconds;
			$is_on_trial = 0;
			if($plan_type_exists == 1) {
				$query = 'update LICENSE_DETAILS set PLAN_ID=?, LICENSE_EXPIRY_DATE=FROM_UNIXTIME(?), LAST_INVOICE_ID=?, LAST_PURCHASE_DATE=FROM_UNIXTIME(?), IS_ON_TRIAL=? where CHURCH_ID=? and PLAN_TYPE=?';
				$result = $this->db_conn->Execute($query, array($plan_id, $lic_expiry_date_to_set, $invoice_id, $purchase_timestamp, $is_on_trial, $this->church_id, $plan_type));
				if($result) {
					return true;
				}			
			} else {
				$query_3 = 'insert into LICENSE_DETAILS values(?,?,?,FROM_UNIXTIME(?),?,FROM_UNIXTIME(?),?,FROM_UNIXTIME(?))';
				$result_3 = $this->db_conn->Execute($query_3, array($this->church_id, $plan_id, $plan_type, $lic_expiry_date_to_set, $invoice_id, $purchase_timestamp, $is_on_trial, $purchase_timestamp));
				if($result_3) {
					return true;
				}			
			}
		}
		return false;
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
		//"$invoice_details_array" should be in the following order : array($billing_full_name, $billing_address, $other_address, $phone, $currency_code, $subtotal, $additional_charge, $discount_percentage, $discount_amount, $tax_percentage, $tax_amount, $tax_2_percentage, $tax_2_amount, $vat_percentage, $vat_amount, $net_total, $coupon_code, $invoice_notes, $payment_gateway, $payment_mode, $ip_address, ...)
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
				$col_values_array[8] = $this->email_id;
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
					$inv_id_result = $this->getInvoiceIDFromUniqueHash($unique_hash);
					if($inv_id_result[0]==1)
					{
						$invoice_id = $inv_id_result[1];
						$invoiced_items = $this->getInvoicedItemsList($invoice_id);
						if($invoiced_items[0]==1) {//Success case
							$is_atleast_one_update_successful = 0;
							for($p=0; $p < COUNT($invoiced_items[1]); $p++)
							{
								$plan_id = $invoiced_items[1][$p][2];
								$plan_type = $invoiced_items[1][$p][5];
								if($this->applyLicense($plan_id, $plan_type, $invoice_id, $last_update_date)) {
									$is_atleast_one_update_successful = 1;
								}
							}

							//Terminating the coupon code used
							if($is_atleast_one_update_successful==1 && $coupon_code_used != "") {
								$this->terminateCouponCode($coupon_code_used, 0);//Terminate only if code is specific to a church
							}

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

	public function createCoupon($church_id, $is_valid_for_all, $discount_percentage, $discount_flat_amount, $minimum_subtotal_required, $valid_till_timestamp, $coupon_code_length=10)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to create coupon";
		if($this->db_conn)
		{
			$attempt = 0;
			$coupon_code = "";
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
}

?>