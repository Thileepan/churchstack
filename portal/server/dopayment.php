<?php

$APPLICATION_PATH = "../../";
session_start();
@include_once($APPLICATION_PATH."portal/utils/auth.php");
validateSession($APPLICATION_PATH);

@include $APPLICATION_PATH.'app/utils/JSON.php';
@include $APPLICATION_PATH.'app/utils/utilfunctions.php';
@include_once $APPLICATION_PATH . 'app/classes/class.license.php';

//process request
$req = $_REQUEST['req'];
if($req == 1)//List All Payments
{
	$license_obj = new License($APPLICATION_PATH."app/");
	$purchase_list = $license_obj->getAllPurchaseReports();
	$to_return = array();
	$to_return['aaData'] = array();
	for($c=0; $c < COUNT($purchase_list[1]); $c++)
	{
		$curr_purchase = $purchase_list[1][$c];
		//$view_btn_html = '<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#paymentDetailsModal" onclick="loadPaymentData('.$curr_purchase[0].');">View</button>';
		$trans_id_html = '<a style="cursor: pointer;" data-toggle="modal" data-target="#paymentDetailsModal" onclick="loadPaymentData('.$curr_purchase[0].');">'.$curr_purchase[2].'</a>';
		$invoice_id_html = '<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#paymentDetailsModal" onclick="loadPaymentData('.$curr_purchase[0].');">'.$curr_purchase[0].'</button>';

		$is_trans_id_available = 0;
		if(trim($curr_purchase[2]) != "") {
			$is_trans_id_available = 1;
		}
		$action_btn_html = '<div class="btn-group">';
			$action_btn_html .= '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions <span class="caret"></span></button>';
			$action_btn_html .= '<ul class="dropdown-menu" role="menu">';
				if($is_trans_id_available==1) {
					$action_btn_html .= '<li><a href="#" data-toggle="modal" data-target="#emailInvoiceModal" onclick="paymentActions(1, '.$curr_purchase[0].', \''.$curr_purchase[8].'\');">Email Invoice</a></li>';
				} else {
					$action_btn_html .= '<li class="disabled"><a href="#">Email Invoice</a></li>';
				}
				//$action_btn_html .= '<li><a href="#">Another action</a></li>';
				//$action_btn_html .= '<li><a href="#">Something else here</a></li>';
				/** /
				$action_btn_html .= '<li class="divider"></li>';
				if($eligible_for_reactivation==1) {
					$action_btn_html .= '<li><a href="#" onclick="couponActions(2, '.$curr_coupon[0].',\''.$curr_coupon[1].'\');">Reactivate</a></li>';
				} else {
					$action_btn_html .= '<li class="disabled"><a href="#">Reactivate</a></li>';
				}
				/**/
			$action_btn_html .= '</ul>';
		$action_btn_html .= '</div>';

		$to_return['aaData'][] = array($invoice_id_html, $trans_id_html, $curr_purchase[3], $curr_purchase[5], $curr_purchase[8], $curr_purchase[14], $curr_purchase[17], $curr_purchase[24], $curr_purchase[25], $curr_purchase[27], $curr_purchase[31], $curr_purchase[33], $curr_purchase[34], $curr_purchase[35], $action_btn_html);

	}
	//insert into invoice_report values(0, CURDATE(), 'JNYYSH7923', 'BHBHBH999',  '98huhpwe', 2, 'CH shbd', 2, 'nes@wds.sd', 'Bill ane', 'bill add', 'otuer add', '21212', 'USD', 232, 2, 1.22, 4, 12.54, 5, 1.87, 6, 4.50, 8, 432, 'hiuty', 'notessd', 'Paypal', 'cred card', '1201.121.12.1', 1, 'Sucess', 'X8172', 'PGSUccc', CURDATE(), 0);

	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 2)//get details of a payment
{
	$invoice_id = trim($_REQUEST['inv_id']);
	$lic_obj = new License($APPLICATION_PATH."app/");
	//getInvoicedItemsList
	$lic_data = $lic_obj->getAllPurchaseReports($invoice_id);
	$to_return = "";
	if($lic_data[0]==0) {
		$to_return = $lic_data[1];
	} else if(COUNT($lic_data[1]) > 0) {
		//$church_id = $lic_data[1][0][5];//because only one church is associated with a payment
		//$lic_obj->setChurchID($church_id);
		$inv_items_data = $lic_obj->getInvoicedItemsList($invoice_id);

		//$church_obj = new Church($APPLICATION_PATH."app/");
		//$church_data = $church_obj->getInformationOfAChurch($church_id);
		$to_return = '<div class="row-fluid">';
			$to_return .= '<div class="span4">Inv. ID : '.$lic_data[1][0][0].'</div>';
			$to_return .= '<div class="span4">Inv. Date : '.$lic_data[1][0][1].'</div>';
			$to_return .= '<div class="span4">Trans. ID : '.$lic_data[1][0][2].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Ref. ID : '.$lic_data[1][0][3].'</div>';
			$to_return .= '<div class="span4">Unique Hash : '.$lic_data[1][0][4].'</div>';
			$to_return .= '<div class="span4">Currency : '.$lic_data[1][0][13].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Subtotal : '.$lic_data[1][0][14].'</div>';
			$to_return .= '<div class="span4">Addl. Charge : '.$lic_data[1][0][15].'</div>';
			$to_return .= '<div class="span4">Discount Perc. : '.$lic_data[1][0][16].'%</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Discount Amt. : '.$lic_data[1][0][17].'</div>';
			$to_return .= '<div class="span4">Tax Perc.: '.$lic_data[1][0][18].'%</div>';
			$to_return .= '<div class="span4">Tax Amt. : '.$lic_data[1][0][19].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Tax-2 Perc. : '.$lic_data[1][0][20].'%</div>';
			$to_return .= '<div class="span4">Tax-2 Amt. : '.$lic_data[1][0][21].'</div>';
			$to_return .= '<div class="span4">Vat Perc. : '.$lic_data[1][0][22].'%</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Vat Amt. : '.$lic_data[1][0][23].'</div>';
			$to_return .= '<div class="span4">Net Total : '.$lic_data[1][0][24].'</div>';
			$to_return .= '<div class="span4">Coupon : '.$lic_data[1][0][25].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Notes : '.$lic_data[1][0][26].'</div>';
			$to_return .= '<div class="span4">Pay. Gateway : '.$lic_data[1][0][27].'</div>';
			$to_return .= '<div class="span4">Payment Mode : '.$lic_data[1][0][28].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">IP Addr. : '.$lic_data[1][0][29].'</div>';
			$to_return .= '<div class="span4">Native Status : '.$lic_data[1][0][30].'</div>';
			$to_return .= '<div class="span4">Native Remarks : '.$lic_data[1][0][31].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">PG Status : '.$lic_data[1][0][32].'</div>';
			$to_return .= '<div class="span4">PG Remarks : '.$lic_data[1][0][33].'</div>';
			$to_return .= '<div class="span4">Last Updated On : '.$lic_data[1][0][34].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span12">Is Refund : '.$lic_data[1][0][35].'</div>';
		$to_return .= '</div>';
		if($inv_items_data[0]==1) {
			$to_return .= '<div class="row-fluid">';
				$to_return .= '<div class="span12"><b>Invoiced Items</b></div>';
			$to_return .= '</div>';
			for($p=0; $p < COUNT($inv_items_data[1]); $p++)
			{
				$inv_items_array[] = array($tmp_invoice_id, $suborder_id, $plan_id, $plan_name, $plan_desc, $plan_type, $validity_text, $validity_seconds, $plan_cost, $quantity, $total_cost, $is_auto_renewal);
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="span4">Suborder ID : '.$inv_items_data[1][$p][1].'</div>';
					$to_return .= '<div class="span4">Plan ID : '.$inv_items_data[1][$p][2].'</div>';
					$to_return .= '<div class="span4">Plan Name : '.$inv_items_data[1][$p][3].'</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="span4">Plan Desc. : '.$inv_items_data[1][$p][4].'</div>';
					$to_return .= '<div class="span4">Plan Type : '.$inv_items_data[1][$p][5].'</div>';
					$to_return .= '<div class="span4">Validity : '.$inv_items_data[1][$p][6].'</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="span4">Plan Cost : '.$inv_items_data[1][$p][8].'</div>';
					$to_return .= '<div class="span4">Quantity : '.$inv_items_data[1][$p][9].'</div>';
					$to_return .= '<div class="span4">Total Cost : '.$inv_items_data[1][$p][10].'</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="span12">Auto Renewal : '.$inv_items_data[1][$p][11].'</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="span12">&nbsp;</div>';
				$to_return .= '</div>';
			}
		}
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span12"><b>Church Details</b></div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Church ID : '.$lic_data[1][0][5].'</div>';
			$to_return .= '<div class="span4">Church Name : '.$lic_data[1][0][6].'</div>';
			$to_return .= '<div class="span4">User ID : '.$lic_data[1][0][7].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Email : '.$lic_data[1][0][8].'</div>';
			$to_return .= '<div class="span4">Billing Name : '.$lic_data[1][0][9].'</div>';
			$to_return .= '<div class="span4">Billing Addr. : '.$lic_data[1][0][10].'</div>';
		$to_return .= '</div>';
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span4">Phone : '.$lic_data[1][0][12].'</div>';
			$to_return .= '<div class="span8">Other Addr. : '.$lic_data[1][0][11].'</div>';
		$to_return .= '</div>';
	}
	$to_return = array("rsno"=>1, "rslt"=>$to_return);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 4)//Email an Invoice
{
	$invoice_id = trim($_REQUEST['invoice_id']);
	$act_num = trim($_REQUEST['act_num']);
	$email = trim($_REQUEST['email']);
	$lic_obj = new License($APPLICATION_PATH."app/");
	$result_data = $lic_obj->prepareAndSendOrderDetailsEmail($invoice_id, $email);
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