<?php
$APPLICATION_PATH = "../../";
@include_once($APPLICATION_PATH."portal/utils/auth.php");
validateSession($APPLICATION_PATH);

include $APPLICATION_PATH.'app/utils/JSON.php';
include $APPLICATION_PATH.'app/utils/utilfunctions.php';
include_once $APPLICATION_PATH . 'app/classes/class.church.php';
include_once $APPLICATION_PATH . 'app/classes/class.license.php';

//process request
$req = $_REQUEST['req'];
if($req == 1 || $req == 3 || $req == 4 || $req == 5 || $req == 6 || $req == 11)
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
	} else if($req==11){
		$churches = $church_obj->getAllChurchesList(6);
	} else {
		$churches = $church_obj->getAllChurchesList(0);
	}
	$to_return = array();
	$to_return['aaData'] = array();
	for($c=0; $c < COUNT($churches[1]); $c++)
	{
		$curr_church = $churches[1][$c];
		$eligible_for_extension = 0;
		$current_state = $curr_church[13];
		if($current_state==1) {
			$eligible_for_extension = 1;
		}
		$church_name_html = '<a style="cursor: pointer;" data-toggle="modal" data-target="#churchDetailsModal" onclick="loadChurchData('.$curr_church[0].');" nowrap>'.$curr_church[1].'</a>';
		$view_btn_html = '<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#churchDetailsModal" onclick="loadChurchData('.$curr_church[0].');">View</button>';
		$action_btn_html = '<div class="btn-group">';
			$action_btn_html .= '<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>';
			$action_btn_html .= '<ul class="dropdown-menu" role="menu">';
				if($eligible_for_extension==1) {
					$action_btn_html .= '<li><a href="#" data-toggle="modal" data-target="#extendValidityModal" onclick="churchActions(1, '.$curr_church[0].',\''.$curr_church[1].'\');">Extend Validity</a></li>';
				} else {
					$action_btn_html .= '<li class="disabled"><a href="#">Extend Validity</a></li>';
				}
				//$action_btn_html .= '<li><a href="#">Another action</a></li>';
				//$action_btn_html .= '<li><a href="#">Something else here</a></li>';
				$action_btn_html .= '<li class="divider"></li>';
				if($current_state==1) {
					$action_btn_html .= '<li><a href="#" onclick="churchActions(2, '.$curr_church[0].',\''.$curr_church[1].'\');">Deactivate</a></li>';
				} else {
					$action_btn_html .= '<li><a href="#" onclick="churchActions(3, '.$curr_church[0].',\''.$curr_church[1].'\');">Re-Activate</a></li>';
				}
			$action_btn_html .= '</ul>';
		$action_btn_html .= '</div>';

		$to_return['aaData'][] = array($curr_church[0], $church_name_html, $curr_church[5], $curr_church[6], $curr_church[8], $curr_church[10], $action_btn_html);
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
else if($req == 8)//Deactivate church
{
	$church_id = trim($_REQUEST['church_id']);
	$act_num = trim($_REQUEST['act_num']);
	$church_obj = new Church($APPLICATION_PATH."app/");
	$result_data = $church_obj->deactivateChurch($church_id);
	$rsno = $result_data[0];
	$msg = $result_data[1];
	$to_return = array("actno"=>$act_num, "rsno"=>$rsno, "msg"=>$msg);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 9)//Re-activate church
{
	$church_id = trim($_REQUEST['church_id']);
	$act_num = trim($_REQUEST['act_num']);
	$church_obj = new Church($APPLICATION_PATH."app/");
	$result_data = $church_obj->activateChurch($church_id);
	$rsno = $result_data[0];
	$msg = $result_data[1];
	$to_return = array("actno"=>$act_num, "rsno"=>$rsno, "msg"=>$msg);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 10)//Extend validity
{
	$church_id = trim($_REQUEST['church_id']);
	$act_num = trim($_REQUEST['act_num']);
	$church_name = trim($_REQUEST['church_name']);
	$days_to_extend = trim($_REQUEST['days_to_extend']);
	$seconds_to_extend = $days_to_extend*24*60*60;
	$lic_obj = new License($APPLICATION_PATH."app/");
	$result_data = $lic_obj->extendChurchSubscriptionValidity($church_id, $seconds_to_extend);
	$rsno = $result_data[0];
	$msg = $result_data[1];
	$to_return = array("actno"=>$act_num, "rsno"=>$rsno, "msg"=>$msg);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}

?>