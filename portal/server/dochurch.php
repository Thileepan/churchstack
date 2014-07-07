<?php
$APPLICATION_PATH = "../../";
@include_once($APPLICATION_PATH."portal/utils/auth.php");
validateSession($APPLICATION_PATH);

@include $APPLICATION_PATH.'app/utils/JSON.php';
@include $APPLICATION_PATH.'app/utils/utilfunctions.php';
@include_once $APPLICATION_PATH . 'app/classes/class.church.php';
@include_once $APPLICATION_PATH . 'app/classes/class.license.php';

//process request
$req = $_REQUEST['req'];
if($req == 1 || $req == 3 || $req == 4 || $req == 5 || $req == 6)
{
	$church_obj = new Church($APPLICATION_PATH."app/");
	if($req==1) {
		$churches = $church_obj->getAllChurchesList(0);
	} else if($req==3){
		$churches = $church_obj->getAllChurchesList(1);
	} else if($req==4){
		$churches = $church_obj->getAllChurchesList(2);
	} else if($req==5){
		$churches = $church_obj->getAllChurchesList(3);
	} else if($req==6){
		$churches = $church_obj->getAllChurchesList(4);
	} else {
		$churches = $church_obj->getAllChurchesList(0);
	}
	$to_return = array();
	$to_return['aaData'] = array();
	for($c=0; $c < COUNT($churches[1]); $c++)
	{
		$curr_church = $churches[1][$c];
		$view_btn_html = '<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#churchDetailsModal" onclick="loadChurchData('.$curr_church[0].');">View</button>';

		$to_return['aaData'][] = array($curr_church[0], $curr_church[1], $curr_church[5], $curr_church[6], $curr_church[8], $curr_church[10], $view_btn_html);
	}
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 2)//get details of a church
{
	$church_id = trim($_REQUEST['ch_id']);
	$church_obj = new Church($APPLICATION_PATH."app/");
	$result_data = $church_obj->getInformationOfAChurch($church_id);
	$church_data = "";
	if($result_data[0]==0) {
		$church_data = $result_data[1];
	} else {
		$lic_obj = new License($APPLICATION_PATH."app/");
		$lic_obj->setChurchID($church_id);
		$lic_data = $lic_obj->getLicenseDetails();

		if (!isset($_SESSION["shardedDB"])) {
			$_SESSION["shardedDB"] = trim($result_data[1][10]);
		} else {
			unset($_SESSION["shardedDB"]);
			$_SESSION["shardedDB"] = trim($result_data[1][10]);
		}
		$profiles_obj = new Profiles($APPLICATION_PATH."app/");
		$prof_data = $profiles_obj->getProfCountGroupedByStatus();

		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">ID : '.$result_data[1][0].'</div>';
			$church_data .= '<div class="span6">Name : '.$result_data[1][1].'</div>';
		$church_data .= '</div>';
		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">Description : '.$result_data[1][2].'</div>';
			$church_data .= '<div class="span6">Address : '.$result_data[1][3].'</div>';
		$church_data .= '</div>';
		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">Landline : '.$result_data[1][4].'</div>';
			$church_data .= '<div class="span6">Mobile : '.$result_data[1][5].'</div>';
		$church_data .= '</div>';
		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">Email : '.$result_data[1][6].'</div>';
			$church_data .= '<div class="span6">Website : '.$result_data[1][7].'</div>';
		$church_data .= '</div>';
		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">Signed Up : '.$result_data[1][8].'</div>';
			$church_data .= '<div class="span6">Last Connected : '.$result_data[1][9].'</div>';
		$church_data .= '</div>';
		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">Sharded Database : '.$result_data[1][10].'</div>';
			$church_data .= '<div class="span6">Currency ID : '.$result_data[1][11].'</div>';
		$church_data .= '</div>';
		$church_data .= '<div class="row-fluid">';
			$church_data .= '<div class="span6">Unique Hash : '.$result_data[1][12].'</div>';
			$church_data .= '<div class="span6">Status : '.$result_data[1][13].'</div>';
		$church_data .= '</div>';
		if($lic_data[0]==1) {
			$church_data .= '<div class="row-fluid">';
				$church_data .= '<div class="span12"><b>License Details</b></div>';
			$church_data .= '</div>';
			for($w=0; $w < COUNT($lic_data[1]); $w++)
			{
				//array("church_id"=>$church_id, "plan_id"=>$plan_id, "plan_type"=>$plan_type, "lic_expiry_date"=>$lic_expiry_date, "lic_expiry_timestamp"=>$lic_expiry_timestamp, "last_invoice_id"=>$last_invoice_id, "last_purchase_date"=>$last_purchase_date, "last_purchase_timestamp"=>$last_purchase_timestamp, "is_on_trial"=>$is_on_trial, "trial_expiry_date"=>$trial_expiry_date, "trial_expiry_timestamp"=>$trial_expiry_timestamp, "allow_usage"=>$allow_usage, "remaining_trial_period_timestamp"=>$remaining_trial_period_timestamp, "remaining_trial_period_days"=>$remaining_trial_period_days);
				$church_data .= '<div class="row-fluid">';
					$church_data .= '<div class="span6">Plan ID : '.$lic_data[1][$w]['plan_id'].'</div>';
					$church_data .= '<div class="span6">Plan Type : '.$lic_data[1][$w]['plan_type'].'</div>';
				$church_data .= '</div>';
				$church_data .= '<div class="row-fluid">';
					$church_data .= '<div class="span6">License Expires On : '.$lic_data[1][$w]['lic_expiry_date'].'</div>';
					$church_data .= '<div class="span6">Last Invoice ID : '.$lic_data[1][$w]['last_invoice_id'].'</div>';
				$church_data .= '</div>';
				$church_data .= '<div class="row-fluid">';
					$church_data .= '<div class="span6">Last Purchased On : '.$lic_data[1][$w]['last_purchase_date'].'</div>';
					$church_data .= '<div class="span6">Is On Trial : '.$lic_data[1][$w]['is_on_trial'].'</div>';
				$church_data .= '</div>';
				$church_data .= '<div class="row-fluid">';
					$church_data .= '<div class="span6">Trial Days Remaining : '.$lic_data[1][$w]['remaining_trial_period_days'].'</div>';
					$church_data .= '<div class="span6">Allow Use Of System : '.$lic_data[1][$w]['allow_usage'].'</div>';
				$church_data .= '</div>';
				$church_data .= '<div class="row-fluid">';
					$church_data .= '<div class="span12">&nbsp;</div>';
				$church_data .= '</div>';
			}
		}
		if($prof_data[0]==1) {
			$church_data .= '<div class="row-fluid">';
				$church_data .= '<div class="span12"><b>Profile Stats</b></div>';
			$church_data .= '</div>';
			for($p=0; $p < COUNT($prof_data[1]); $p++)
			{
				$church_data .= '<div class="row-fluid">';
					$church_data .= '<div class="span6">Profile Status : '.$prof_data[1][$p][0].'</div>';
					$church_data .= '<div class="span6">Profile Count : '.$prof_data[1][$p][1].'</div>';
				$church_data .= '</div>';
			}
			$church_data .= '<div class="row-fluid">';
				$church_data .= '<div class="span12">&nbsp;</div>';
			$church_data .= '</div>';
		}
	}
	$to_return = array("rsno"=>1, "rslt"=>$church_data);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}

?>