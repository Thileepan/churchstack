<?php

$APPLICATION_PATH = "../../";
session_start();
@include_once($APPLICATION_PATH."portal/utils/auth.php");
validateSession($APPLICATION_PATH);

@include $APPLICATION_PATH.'app/utils/JSON.php';
@include $APPLICATION_PATH.'app/utils/utilfunctions.php';
@include_once $APPLICATION_PATH . 'app/classes/class.users.php';
@include_once $APPLICATION_PATH . 'app/classes/class.church.php';
@include_once $APPLICATION_PATH . 'app/classes/class.license.php';

//process request
$req = $_REQUEST['req'];
if($req == 1)//Load Church basic data
{
	$church_id = trim($_POST["churchID"]);
	$church_obj = new Church($APPLICATION_PATH."app/");
	$response_array = array();
	$response_array["rsno"] = 0;
	$response_array["msg"] = "Unable to get the church data";
	$church_data = $church_obj->getInformationOfAChurch($church_id);

	if($church_data[0]==1) {
		$response_array["rsno"] = 1;
		$response_array["msg"] = "Successful";
		$response_array["randTransactionID"] = "CS".time();
		$response_array["church_data"] = array();
		$response_array["church_data"]["address"] = $church_data[1][3];
		$response_array["church_data"]["phone"] = ((trim($church_data[1][5]) != "")? trim($church_data[1][5]) : trim($church_data[1][4]));

		$response_array["user_data"] = array();

		$users_obj =  new Users($APPLICATION_PATH."app/");
		$user_details = $users_obj->getChurchAdminDetails($church_id);

		if($user_details[0]==1) {
			$response_array["user_data"]["email"] = $user_details[1][3];
			$response_array["user_data"]["full_name"] = $user_details[1][10];
		}
	} else {
		$response_array["rsno"] = 0;
		$response_array["msg"] = $church_data[1];
	}

	$json = new Services_JSON();
	$encode_obj = $json->encode($response_array);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 2)//Validate selected plan and Update invoice
{
	$church_id = trim($_POST["church_id"]);
	$sharded_db = trim(stripslashes(rawurldecode($_POST["sharded_db"])));
	$billing_full_name = trim(stripslashes(rawurldecode($_POST["billing_full_name"])));
	$billing_address = trim(stripslashes(rawurldecode($_POST["billing_address"])));
	$other_address = trim(stripslashes(rawurldecode($_POST["other_address"])));
	$phone = trim(stripslashes(rawurldecode($_POST["phone"])));
	$currency_code = trim(stripslashes(rawurldecode($_POST["currency_code"])));
	$subtotal = trim(stripslashes(rawurldecode($_POST["subtotal"])));
	$additional_charge = trim(stripslashes(rawurldecode($_POST["additional_charge"])));
	$discount_percentage = trim(stripslashes(rawurldecode($_POST["discount_percentage"])));
	$discount_amount = trim(stripslashes(rawurldecode($_POST["discount_amount"])));
	$tax_percentage = trim(stripslashes(rawurldecode($_POST["tax_percentage"])));
	$tax_amount = trim(stripslashes(rawurldecode($_POST["tax_amount"])));
	$tax_2_percentage = trim(stripslashes(rawurldecode($_POST["tax_2_percentage"])));
	$tax_2_amount = trim(stripslashes(rawurldecode($_POST["tax_2_amount"])));
	$vat_percentage = trim(stripslashes(rawurldecode($_POST["vat_percentage"])));
	$vat_amount = trim(stripslashes(rawurldecode($_POST["vat_amount"])));
	$net_total = trim(stripslashes(rawurldecode($_POST["net_total"])));
	$coupon_code = trim(stripslashes(rawurldecode($_POST["coupon_code"])));
	$invoice_notes = trim(stripslashes(rawurldecode($_POST["invoice_notes"])));
	$payment_gateway = trim(stripslashes(rawurldecode($_POST["payment_gateway"])));
	$payment_mode = trim(stripslashes(rawurldecode($_POST["payment_mode"])));
	$ip_address = trim(stripslashes(rawurldecode($_POST["ip_address"])));
	$email = trim(stripslashes(rawurldecode($_POST["email"])));
	$plan_id = trim(stripslashes(rawurldecode($_POST["plan_id"])));
	$plan_name = trim(stripslashes(rawurldecode($_POST["plan_name"])));
	$plan_desc = trim(stripslashes(rawurldecode($_POST["plan_desc"])));
	$plan_type = trim(stripslashes(rawurldecode($_POST["plan_type"])));
	$validity_period_text = trim(stripslashes(rawurldecode($_POST["validity_period_text"])));
	$validity_in_seconds = trim(stripslashes(rawurldecode($_POST["validity_in_seconds"])));
	$plan_cost = trim(stripslashes(rawurldecode($_POST["plan_cost"])));
	$quantity = trim(stripslashes(rawurldecode($_POST["quantity"])));
	$total_cost = trim(stripslashes(rawurldecode($_POST["total_cost"])));
	$is_autorenewal_enabled = trim($_POST["is_autorenewal_enabled"]);
	$transaction_id = trim(stripslashes(rawurldecode($_POST["transaction_id"])));
	$payment_gateway = trim(stripslashes(rawurldecode($_POST["payment_gateway"])));
	$payment_mode = trim(stripslashes(rawurldecode($_POST["payment_mode"])));
	$pg_status_code = trim(stripslashes(rawurldecode($_POST["pg_status_code"])));
	$pg_status_remarks = trim(stripslashes(rawurldecode($_POST["pg_status_remarks"])));
	$purchase_status_code = trim(stripslashes(rawurldecode($_POST["purchase_status_code"])));
	$purchase_status_remarks = trim(stripslashes(rawurldecode($_POST["purchase_status_remarks"])));


	/**/
	$response_array = array();
	$response_array["rsno"] = 0;
	$response_array["msg"] = "Unable to update license";

	$invoice_details_array = array($billing_full_name, $billing_address, $other_address, $phone, $currency_code, $subtotal, $additional_charge, $discount_percentage, $discount_amount, $tax_percentage, $tax_amount, $tax_2_percentage, $tax_2_amount, $vat_percentage, $vat_amount, $net_total, $coupon_code, $invoice_notes, $payment_gateway, $payment_mode, $ip_address, $email);
	$invoiced_items_array = array();
	$invoiced_items_array[0] = array($plan_id, $plan_name, $plan_desc, $plan_type, $validity_period_text, $validity_in_seconds, $plan_cost, $quantity, $total_cost, $is_autorenewal_enabled);
	$is_refund = 0;
	$lic_obj = new License($APPLICATION_PATH."app/");
	$lic_obj->setChurchID($church_id);
	$lic_obj->setShardedDB($sharded_db);
	$is_plan_change_permitted = $lic_obj->validateLicensePlanChange($plan_id);
	if($is_plan_change_permitted[0]==1) {
		$write_initial_result = $lic_obj->writeInitialPurchaseReport($invoice_details_array, $invoiced_items_array, $is_refund);
		if($write_initial_result[0]==1) {
			$unique_hash =  $write_initial_result[1][1];
			$final_result = $lic_obj->updatePurchaseReport($unique_hash, $transaction_id, $payment_mode, $purchase_status_code, $purchase_status_remarks, $pg_status_code, $pg_status_remarks);
			if($final_result[0]==1) {
				$response_array["rsno"] = 1;
				$response_array["msg"] = "Invoice updated, invoice mail triggerred & license applied.<BR/><BR/>Go and check the details of the particular church to know about the new validity.";//$final_result[1];
			} else {
				$response_array["rsno"] = 0;
				$response_array["msg"] = $final_result[1];
			}

		} else {
			$response_array["rsno"] = 0;
			$response_array["msg"] = $write_initial_result[1];
		}
	} else {
		$response_array["rsno"] = 0;
		$response_array["msg"] = $is_plan_change_permitted[1];
	}

	$json = new Services_JSON();
	$encode_obj = $json->encode($response_array);
	unset($json);

	echo $encode_obj;
	exit;
	/**/
}
else if($req == 3)
{
	$amount_including_ST = trim($_POST["amount_including_ST"]);
	$lic_obj = new License($APPLICATION_PATH."app/");
	$result = $lic_obj->calculateServiceTaxSplitup($amount_including_ST);

	$html_data = "";
	$html_data .= '<div class="row-fluid">';
		$html_data .= '<div class="span12">Amount WITHOUT Service Tax : '.$result["amount_without_ST"].'</div>';
	$html_data .= '</div>';
	$html_data .= '<div class="row-fluid">';
		$html_data .= '<div class="span12">Service Tax Amount : '.$result["service_tax_amount"].'</div>';
	$html_data .= '</div>';

	$to_return = array("rslt"=>$html_data);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
?>