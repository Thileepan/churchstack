<?php

$APPLICATION_PATH = "../";
include_once $APPLICATION_PATH . '/classes/class.church.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	//church information add/edit form page

	$is_update = $_REQUEST['isUpdate'];
	if($is_update)
	{
		$church_obj = new Church($APPLICATION_PATH);
		$church_details = $church_obj->getChurchInformation();
		$church_id = $church_details[0];
		$church_name = $church_details[1];
		$church_desc = $church_details[2];
		$church_addr = $church_details[3];
		$landline = $church_details[4];
		$mobile = $church_details[5];
		$email = $church_details[6];
		$website = $church_details[7];
		$signup_time = $church_details[8];
		$last_modified_time = $church_details[9];
		$sharded_db = $church_details[10];
	}

	$to_return = '';
	$to_return .= '<div class="row-fluid">';
		$to_return .= '<div id="harvestFormDiv" class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputChurchName">Name of the Church</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span8" id="inputChurchName" placeholder="Church Name" value="'.$church_name.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputChurchDesc">About Church</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<textarea class="span8" id="inputChurchDesc" placeholder="About Church">'.$church_desc.'</textarea>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputChurchAddress">Address</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<textarea class="span8" id="inputChurchAddress" placeholder="Address">'.$church_addr.'</textarea>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputLandline">Landline</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span8" id="inputLandline" placeholder="Landline" value="'.$landline.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputMobile">Mobile</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span8" id="inputMobile" placeholder="Mobile" value="'.$mobile.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEmail">Email</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span8" id="inputEmail" placeholder="Email" value="'.$email.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputWebsite">Website</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span8" id="inputWebsite" placeholder="Website" value="'.$website.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="span12">';
					  $to_return .= '<div class="form-actions">';
						$to_return .= '<button class="btn btn-primary" onclick="addOrUpdateChurchInfo('.$is_update.');">'.(($is_update)?'Update':'Save Changes').'</button>&nbsp;';
						if(!$is_update) {
							$to_return .= '<button class="btn" type="reset">Reset</button>';
						} else {
							$to_return .= '<button class="btn" onclick="getChurchInformation();">Cancel</button>';
						}						
					  $to_return .= '</div>';
					$to_return .= '</div>';
				$to_return .= '</div>';
			$to_return .= '</form>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	echo $to_return;
	exit;
}
else if($req == 2)
{
	//add or update church information in database
	$church_name = trim($_POST['churchName']);
	$church_desc = trim($_POST['churchDesc']);
	$church_addr = trim($_POST['churchAddr']);
	$landline = trim($_POST['landline']);
	$mobile = trim($_POST['mobile']);
	$email = trim($_POST['email']);
	$website = trim($_POST['website']);
	$is_update = trim($_POST['isUpdate']);
	$signup_time = time();
	$last_modified_time = time();
	$currency_id = trim($_POST['currencyID']);
	$country_id = trim($_POST['countryID']);
	
	$church_obj = new Church($APPLICATION_PATH);
	if(!$is_update) {
		$status = $church_obj->addChurchInformation($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $currency_id, $country_id, 0);
	} else {
		$status = $church_obj->updateChurchInformation($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $last_modified_time, $currency_id, $country_id);
	}

	echo $status;
	exit;
}
else if($req == 3)
{
	//get church information
	$church_obj = new Church($APPLICATION_PATH);
	$church_details = $church_obj->getChurchInformation();
	//print_r($church_details);

	$to_return = '';
	if(is_array($church_details) && COUNT($church_details) > 0)
	{
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span8">';
				$to_return .= '<div>';
					$to_return .= '<b>';
						$to_return .= $church_details[0];
					$to_return .= '</b>&nbsp;&nbsp;&nbsp;<a href="#" onclick="getChurchInformationForm(1)">(Edit)</a>';
					$to_return .= '<BR><span class="muted">'.$church_details[1].'</span>';
				$to_return .= '</div><BR>';
				$to_return .= '<div>';
					$to_return .= '<pre>'.$church_details[2];
					if($church_details[3] != "") {
						$to_return .= '<BR><abbr title="Phone">PH:</abbr>&nbsp;'.$church_details[3];
					}
					if($church_details[4] != "") {
						$to_return .= '<BR><abbr title="Mobile">M:</abbr>&nbsp;'.$church_details[4];
					}
					$to_return .= "<BR>".$church_details[5];
					$to_return .= "<BR><a href='".$church_details[6]."' target='_blank'>".$church_details[6]."</a></pre>";
				$to_return .= '</div><BR>';
			$to_return .= '</div>';
		$to_return .= '</div>';
	}
	else
	{
		$to_return .= '<div class="row-fluid">';
			$to_return .= '<div class="span12">';
				$to_return .= '<div class="alert alert-info"><b>Heads up!</b> You haven\'t added any information about your church yet. <a href="#" onclick="getChurchInformationForm(0);"><u>Click Here</u></a> to add.</div>';
			$to_return .= '</div>';
		$to_return .= '</div>';
	}
	echo $to_return;
	exit;
}

?>