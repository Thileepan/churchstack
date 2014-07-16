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
if($req == 1 || $req == 3 || $req == 4 || $req == 5 || $req == 6 || $req == 7)
{
	$lic_obj = new License($APPLICATION_PATH."app/");
	if($req==1) {
		$coupons = $lic_obj->getAllCouponsList(0);
	} else if($req==3){
		$coupons = $lic_obj->getAllCouponsList(1);
	} else if($req==4){
		$coupons = $lic_obj->getAllCouponsList(2);
	} else if($req==5){
		$coupons = $lic_obj->getAllCouponsList(3);
	} else if($req==6){
		$coupons = $lic_obj->getAllCouponsList(4);
	} else if($req==7){
		$coupons = $lic_obj->getAllCouponsList(5);
	} else {
		$coupons = $lic_obj->getAllCouponsList(0);
	}
	$to_return = array();
	$to_return['aaData'] = array();
	for($c=0; $c < COUNT($coupons[1]); $c++)
	{
		$curr_coupon = $coupons[1][$c];
		$action_btn_html = '<div class="btn-group">';
			$action_btn_html .= '<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>';
			$action_btn_html .= '<ul class="dropdown-menu" role="menu">';
				$action_btn_html .= '<li><a href="#" onclick="couponActions(1, '.$curr_coupon[0].');">Terminate</a></li>';
				//$action_btn_html .= '<li><a href="#">Another action</a></li>';
				//$action_btn_html .= '<li><a href="#">Something else here</a></li>';
				//$action_btn_html .= '<li class="divider"></li>';
				//$action_btn_html .= '<li><a href="#" onclick="couponActions(2, '.$curr_coupon[0].');">Delete Permanently</a></li>';
			$action_btn_html .= '</ul>';
		$action_btn_html .= '</div>';
		$coupon_name_html = '<a style="cursor: pointer;" data-toggle="modal" data-target="#couponDetailsModal" onclick="loadCouponData('.$curr_coupon[0].');">'.$curr_coupon[1].'</a>';

		$to_return['aaData'][] = array($curr_coupon[0], $coupon_name_html, $curr_coupon[2], $curr_coupon[3], $curr_coupon[4], $curr_coupon[5], $curr_coupon[6], $curr_coupon[7], $curr_coupon[8], $action_btn_html);
	}
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 2)//get details of a coupon
{
	$coupon_id = trim($_REQUEST['coupon_id']);
	$lic_obj = new License($APPLICATION_PATH."app/");
	$result_data = $lic_obj->getCouponInformation($coupon_id);
	$coupon_data = "";
	if($result_data[0]==0) {
		$coupon_data = $result_data[1];
	} else {
		$coupon_data .= '<div class="row-fluid">';
			$coupon_data .= '<div class="span6">ID : '.$result_data[1][0].'</div>';
			$coupon_data .= '<div class="span6">Code : '.$result_data[1][1].'</div>';
		$coupon_data .= '</div>';
		$coupon_data .= '<div class="row-fluid">';
			$coupon_data .= '<div class="span6">Church ID : '.$result_data[1][2].'</div>';
			$coupon_data .= '<div class="span6">Discount Percentage : '.$result_data[1][3].' %</div>';
		$coupon_data .= '</div>';
		$coupon_data .= '<div class="row-fluid">';
			$coupon_data .= '<div class="span6">Discount Flat Amount : USD '.$result_data[1][4].'</div>';
			$coupon_data .= '<div class="span6">Minimum Subtotal : '.$result_data[1][5].'</div>';
		$coupon_data .= '</div>';
		$coupon_data .= '<div class="row-fluid">';
			$coupon_data .= '<div class="span6">Valid Till : '.$result_data[1][6].'</div>';
			$coupon_data .= '<div class="span6">Is Valid For All : '.$result_data[1][7].'</div>';
		$coupon_data .= '</div>';
		$coupon_data .= '<div class="row-fluid">';
			$coupon_data .= '<div class="span12"> Is Used : '.$result_data[1][8].'</div>';
		$coupon_data .= '</div>';
	}
	$to_return = array("rsno"=>1, "rslt"=>$coupon_data);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 8)//generate a coupon
{
	$is_valid_for_all = trim($_REQUEST['is_valid_for_all']);
	$church_id = trim($_REQUEST['ch_id']);
	$discoun_perc = trim($_REQUEST['discount_perc']);
	$discoun_flat_amt = trim($_REQUEST['discount_flat_amt']);
	$minimum_subtotal = trim($_REQUEST['minimum_subtotal']);
	$valid_till = trim($_REQUEST['valid_till']);
	$valid_till = str_replace('/', '-', $valid_till);
	$valid_till = strtotime($valid_till);
	$coupon_code_length=10;
	$lic_obj = new License($APPLICATION_PATH."app/");
	$coupon_result = $lic_obj->createCoupon($church_id, $is_valid_for_all, $discoun_perc, $discoun_flat_amt, $minimum_subtotal, $valid_till, $coupon_code_length);

	$rsno = 0;
	$msg = "Unable to generate coupon";
	$rslt = "";
	if($coupon_result[0]==1) {
		$rsno = 1;
		$msg = $coupon_result[1];
		$rslt = 
		$rslt .= '<div class="row-fluid">';
			$rslt .= '<div class="span12"><u><b>Coupon generated successfully, details below:</b></u></div>';
		$rslt .= '</div>';
		$rslt .= '<div class="row-fluid">';
			$rslt .= '<div class="span6">Coupon Code : <b>'.$coupon_result[2][0].'</b></div>';
			$rslt .= '<div class="span6">Church ID : '.$coupon_result[2][1].'</div>';
		$rslt .= '</div>';
		$rslt .= '<div class="row-fluid">';
			$rslt .= '<div class="span6">Discount Percentage : '.$coupon_result[2][2].' %</div>';
			$rslt .= '<div class="span6">Discount Flat Amount : USD '.$coupon_result[2][3].'</div>';
		$rslt .= '</div>';
		$rslt .= '<div class="row-fluid">';
			$rslt .= '<div class="span6">Minimum Subtotal : USD '.$coupon_result[2][4].'</div>';
			$rslt .= '<div class="span6">Valid Till : '.date("F j, Y, g:i a", $coupon_result[2][5]).'</div>';
		$rslt .= '</div>';
	} else {
		$rsno = 0;
		$msg = $coupon_result[1];
	}

	$to_return = array("rsno"=>1, "msg"=>$msg, "rslt"=>$rslt);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 9)//Terminate coupon
{
	$coupon_id = trim($_REQUEST['coupon_id']);
	$act_num = trim($_REQUEST['act_num']);
	$lic_obj = new License($APPLICATION_PATH."app/");
	$result_data = $lic_obj->terminateCouponID($coupon_id, 1);
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