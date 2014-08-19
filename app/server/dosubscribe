<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include $APPLICATION_PATH.'utils/utilfunctions.php';
include_once $APPLICATION_PATH . '/classes/class.subscription.php';
include_once $APPLICATION_PATH . '/classes/class.profiles.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	//List subscription fieldsleadlead

	$sub_obj = new Subscription($APPLICATION_PATH);
	$subscription_fields = $sub_obj->getAllSubscriptionFields();

	$to_return .= '<div id="divAddFieldForm" style="display:none">';
		$to_return .= '<fieldset>';
			$to_return .= '<legend>Add New Field</legend>';
			$to_return .= '<input type="text" id="inputAddFieldName" placeholder="Field Name"><BR>';
			$to_return .= '<button type="submit" class="btn btn-primary" onclick="addOrUpdateField(0);">Add</button>&nbsp;';
			$to_return .= '<button type="submit" class="btn" onclick="hideAddNewFieldForm();">Cancel</button>';
		$to_return .= '</fieldset>';
	$to_return .= '</div>';

	$to_return .= '<div class="pull-right" id="divAddFieldBtn">';
		$to_return .= '<button class="btn btn-small btn-primary" type="button" onclick="showAddNewFieldForm();">Add New Field</button>';
	$to_return .= '</div>';
	$to_return .= '<BR>';
	$to_return .= '<table id="subfields" class="table table-striped">';
		$to_return .= '<thead>';
			$to_return .= '<tr>';
				$to_return .= '<th>ID</th>';
				$to_return .= '<th>Field Name</th>';
				$to_return .= '<th>Field Visibility</th>';
				$to_return .= '<th>Actions</th>';
			$to_return .= '</tr>';
		$to_return .= '</thead>';
		$to_return .= '<tbody>';

	$is_fields_available = false;
	if(is_array($subscription_fields))
	{
		$total_fields = COUNT($subscription_fields);
		if($total_fields > 0)
		{
			$is_fields_available = true;
			for($i=0; $i<$total_fields; $i++)
			{
				$is_hide = $subscription_fields[$i][2];
				$to_return .= '<tr>';						
					$to_return .= '<td>'.$subscription_fields[$i][0].'</td>';
					$to_return .= '<td>';
						$to_return .= '<span id="spnShowFieldNameInfo-'.$i.'">'.$subscription_fields[$i][1].'</span>';
						$to_return .= '<span id="spnEditFieldNameInfo-'.$i.'" style="display:none"><input type="hidden" value="'.$subscription_fields[$i][0].'" id="inputEditFieldID-'.$i.'" /><input type="text" value="'.$subscription_fields[$i][1].'" id="inputEditFieldName-'.$i.'" /></span>';
					$to_return .= '</td>';
					$to_return .= '<td><span class="label '.(($is_hide)?"label-important":"label-success").'">'.(($is_hide)?"InActive":"Active").'</span></td>';
					$to_return .= '<td>';
						$to_return .= '<span id="spnActionInfo-'.$i.'"><i class="curHand icon-pencil" onclick="showEditFieldInfoRow('.$i.');"></i>&nbsp;&nbsp;<i class="curHand icon-eye-close" style="display:'.(($is_hide)?'none':'').'" onclick="hideOrShowField(1, '.$i.');"></i><i class="curHand icon-eye-open" style="display:'.(($is_hide)?'':'none').'" onclick="hideOrShowField(0, '.$i.');"></i></span>';
						$to_return .= '<span id="spnSaveButton-'.$i.'" style="display:none"><button class="btn btn-small btn-success" onclick="addOrUpdateField(1);">Save</button>&nbsp;<button class="btn btn-small" onclick="hideEditFieldInfoRow('.$i.');">Cancel</button></span>';
					$to_return .= '</td>';
				$to_return .= '</tr>';
			}
		}
	}
	
	if( !$is_fields_available )
	{
		$to_return .= '<tr>';
			$to_return .= '<td colspan="4" align="center">'.'No subscription fields available'.'</td>';
		$to_return .= '</tr>';
	}
		$to_return .= '<tr style="display:none"><td colspan="4"><input type="hidden" value="" id="hiddenLastEditedRow" /></td></tr>';
		$to_return .= '</tbody>';
	$to_return .= '</table>';
		
	echo $to_return;
	exit;
}
else if($req == 2)
{
	$field_name = trim($_POST['fieldName']);
	$field_id = trim($_POST['fieldID']);
	$is_update = trim($_POST['isUpdate']);

	$sub_obj = new Subscription($APPLICATION_PATH);
	if($is_update) {
		$status = $sub_obj->updateField($field_id, $field_name);		
	} else {
		$status = $sub_obj->addNewField($field_name);
	}

	echo $status;
	exit;
}
else if($req == 3)
{
	$field_id = trim($_POST['fieldID']);
	$is_hide = trim($_POST['isHide']);

	$sub_obj = new Subscription($APPLICATION_PATH);
	$status = $sub_obj->hideOrShowField($field_id, $is_hide);

	echo $status;
	exit;
}
else if($req == 4)
{
	//add/edit subscription form
	$isUpdate = trim($_POST['isEdit']);
	$profiles_obj = new Profiles($APPLICATION_PATH);
	$parent_list = $profiles_obj->getAllParentProfiles();

	$sub_obj = new Subscription($APPLICATION_PATH);
	$subscription_fields = $sub_obj->getActiveSubscriptionFields();

	if($isUpdate) {
		$subscription_id = trim($_POST['subscriptionID']);
		$prev_unique_id = trim($_POST['prevProfileID']);
		$profile_info = $profiles_obj->getProfileInformation($prev_unique_id);
		if(is_array($profile_info))
		{
			$profile_name = $profile_info[2];
		}
		$subscription_details = $sub_obj->getSubscriptionInformation($subscription_id);
		$prev_unique_id_with_name = $profile_name . '-STC' . appendZeroInUniqueID($prev_unique_id);
		$date_of_sub = formatDateOfBirth($subscription_details[0][19], false, true);
	}
	
	$total = 0;
	$to_return = '';
	$to_return .= '<div class="span12">';
		$to_return .= '<form class="form-horizontal" onsubmit="return false;">';

		$to_return .= '<table class="span6">';
			$to_return .= '<tr>';
				$to_return .= '<td class="span6" colspan="2">';
					$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputProfileID">Family Head</label>';
						$to_return .= '<div class="controls">';
						/*
							$to_return .= '<select class="span12" id="inputProfileID">';
							if(is_array($parent_list))
							{*/
								$parent_list_count = COUNT($parent_list);
								//$to_return .= '<option value="-1">Select Family Head</option>';
								$parent_id = '';
								$parent_name = '';
								$parent_unique_id = '';
								if($parent_list_count > 0) {
									for($i=0; $i<$parent_list_count; $i++) {
										//$to_return .= '<option value="'.$parent_list[$i][0].'">'.$parent_list[$i][1].'</option>';

										if($parent_id != '') {
											$parent_id .= ',';
										}
										if($parent_name != '') {
											$parent_name .= ',';
										}
										if($parent_unique_id != '') {
											$parent_unique_id .= ',';
										}
										$parent_id .= $parent_list[$i][0];
										$parent_name .= $parent_list[$i][1];
										$parent_unique_id .= $parent_list[$i][1].'-'.'STC' . appendZeroInUniqueID($parent_list[$i][2]);
									}
								}
							//}
							//$to_return .= '</select>';
							$to_return .= '<input type="hidden" id="hiddenParentID" value="'.$parent_id.'" />';
							$to_return .= '<input type="hidden" id="hiddenParentName" value="'.$parent_name.'" />';
							$to_return .= '<input type="hidden" id="hiddenParentUniqueID" value="'.$parent_unique_id.'" />';
							$to_return .= '<input type="hidden" id="selectedProfileID" value="'.$prev_unique_id.'" />';
							$to_return .= '<input type="text" class="span12" id="inputProfileID" data-provide="typeahead" autocomplete="off" placeholder="Type Family Head Member ID" value="'.$prev_unique_id_with_name.'">';
							$to_return .= '<input type="hidden" id="hiddenSubscriptionID" value="0" />';
						$to_return .= '</div>';
					$to_return .= '</div>';
				$to_return .= '</td>';
			$to_return .= '</tr>';
			$to_return .= '<tr>';
				$to_return .= '<td class="span6" colspan="2">';
					$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputSubcriptionMonth">Subscription Date</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span6" id="inputSubcriptionMonth" value="'.(($isUpdate)?$date_of_sub:date('d/m/Y')).'" data-date-format="dd/mm/yyyy" />';
						$to_return .= '</div>';
					$to_return .= '</div>';
				$to_return .= '</td>';
			$to_return .= '</tr>';
		$fieldIDs = '';
		if(is_array($subscription_fields))
		{
			$total_fields = COUNT($subscription_fields);
			if($total_fields > 0)
			{
				for($i=0; $i<$total_fields; $i++)
				{
					$field_id = $subscription_fields[$i][0];
					if($isUpdate) {
						$field_value = $subscription_details[0][$field_id-1];
						$total = $total + $field_value;
					}

					$j = $i + 1;
					$is_even = (($j % 2 == 0)?true:false);
					if(!$is_even)
					{
						$to_return .= '<tr>';
					}
							$to_return .= '<td class="span3">';
								$to_return .= '<div style="padding-bottom:2px;">';
									$to_return .= '<label class="control-label" for="inputFieldID-'.$subscription_fields[$i][0].'">'.$subscription_fields[$i][1].'</label>';
									$to_return .= '<div class="controls">';
										$to_return .= '<input type="text" id="inputFieldID-'.$subscription_fields[$i][0].'" class="input-mini" onkeypress="return isNumberKey(event);" onkeyup="calSubscriptionTotal(this);" onblur="calSubscriptionTotal(this);" value="'.(($isUpdate)?$field_value:"").'" />';
									$to_return .= '</div>';
								$to_return .= '</div>';
							$to_return .= '</td>';
					if($is_even)
					{
						$to_return .= '</tr>';
					}

					if($fieldIDs != '') {
						$fieldIDs .= ',';
					}
					$fieldIDs .= $subscription_fields[$i][0];
				}
				
			}
		}
			$to_return .= '<tr><td class="span6" colspan="2" style="margin-top:20px;border-top: 2px solid grey;">';
				$to_return .= '<h4><p class="text-info text-right"><strong>Total: Rs. <span id="spanSubscriptionTotal">'.$total.'</span></strong></p></h4>';
				$to_return .= '<input type="hidden" id="hiddenFieldIDs" value="'.$fieldIDs.'">';
			$to_return .= '</td></tr>';
			if(!$isUpdate)
			{
				$to_return .= '<tr>';
					$to_return .= '<td class="span6" colspan="2">';
						$to_return .= '<div class="form-actions">';
						$to_return .= '<button class="btn btn-primary" onclick="addOrUpdateNewSubscription(0, 2);">'.(($isUpdate)?'Update Subscription':'Add Subscription').'</button>&nbsp;';
							$to_return .= '<button class="btn" type="reset">Reset</button>';							
						$to_return .= '</div>';
					$to_return .= '</td>';
				$to_return .= '</tr>';
			}
		$to_return .= '</table>';
		$to_return .= '</form>';
	$to_return .= '</div>';

	
	echo $to_return;
	exit;
}
else if($req == 5)
{
	$isUpdate = trim($_POST['isUpdate']);
	$profile_id = trim($_POST['profileID']);
	//$date_of_sub = date("Y-m-d");
	$date_of_sub = trim($_POST['subscriptionDate']);
	$fieldIDStr = trim($_POST['fieldIDStr']);
	$fieldValStr = trim($_POST['fieldValStr']);
	$subscription_id = trim($_POST['subscriptionID']);
	
	$fieldIDArr = explode(",", $fieldIDStr);
	$fieldValArr = explode(",", $fieldValStr);
	//print_r($fieldIDArr);
	//print_r($fieldValArr);

	$total_amount = 0;
	$values = array();
	for($i=0; $i<20; $i++)
	{
		if(in_array(($i+1), $fieldIDArr))
		{
			$total_amount = $total_amount + $fieldValArr[$i];
			$values[] = $fieldValArr[$i];
		} else {
			$values[] = 0;
		}
		/*
		$total_field = COUNT($fieldIDArr);
		for($j=0; $j<$total_field; $j++)
		{

		}
		*/
	}
	//print_r($values);exit;
	//echo $val1.":::". $val2.":::". $val3.":::". $val4.":::". $val5.":::". $val6.":::". $val7.":::". $val8.":::". $val9.":::". $val10.":::". $val11.":::". $val12.":::". $val13.":::". $val14.":::". $val15.":::". $val16.":::". $val17.":::". $val18.":::". $val19.":::". $val20;exit;

	$sub_obj = new Subscription($APPLICATION_PATH);
	if(!$isUpdate) {
		$status = $sub_obj->addNewSubscription($profile_id, $date_of_sub, $values[0], $values[1], $values[2], $values[3], $values[4], $values[5], $values[6], $values[7], $values[8], $values[9], $values[10], $values[11], $values[12], $values[13], $values[14], $values[15], $values[16], $values[17], $values[18], $values[19], $total_amount);
	} else {
		$status = $sub_obj->updateSubscription($subscription_id, $profile_id, $date_of_sub, $values[0], $values[1], $values[2], $values[3], $values[4], $values[5], $values[6], $values[7], $values[8], $values[9], $values[10], $values[11], $values[12], $values[13], $values[14], $values[15], $values[16], $values[17], $values[18], $values[19], $total_amount);
	}

	echo $status;
	exit;
}
else if($req == 6)
{
	//list all subscriptions
	$profile_id = trim($_POST['profileID']);
	$sub_obj = new Subscription($APPLICATION_PATH);
	$subscriptions = $sub_obj->getAllSubscriptions($profile_id);
	//print_r($subscriptions);

	$is_results_available = false;
	if(is_array($subscriptions))
	{
		$total_subscriptions = COUNT($subscriptions);
		if($total_subscriptions > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_subscriptions; $i++) {
				
				$unique_id = 'STC'.appendZeroInUniqueID($subscriptions[$i][5]);

				$actions = '<div class="dropdown">';
				//$actions .= '<i class="curHand icon-pencil" onclick="getSubscriptionForm(1, '.$subscriptions[$i][0].')"></i>&nbsp;&nbsp;';
				$actions .= '<a href="#subModal" role="button" data-toggle="modal"><i class="curHand icon-pencil" onclick="getSubscriptionForm(1, '.$subscriptions[$i][0].', '.$subscriptions[$i][1].')"></i></a>&nbsp;';
				$actions .= '<i class="curHand icon-trash" onclick="deleteSubscriptionConfirmation('.$subscriptions[$i][0].','.$subscriptions[$i][1].')"></i>&nbsp;&nbsp;';
				$actions .= '</div>';
				
				$to_return['aaData'][] = array('<img src="plugins/datatables/examples/examples_support/details_open.png" />', $subscriptions[$i][0], '<a href="#">'.$unique_id.'</a>', $subscriptions[$i][2], formatDateOfBirth($subscriptions[$i][3]), $subscriptions[$i][4], $actions);
			}
		}
	}
	
	if( !$is_results_available )
	{
		$to_return['aaData'] = array();
	}

	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 7)
{
	//detailed information on subscription
	$subscription_id = trim($_POST['subscriptionID']);
	$sub_obj = new Subscription($APPLICATION_PATH);
	$subscription_fields = $sub_obj->getAllSubscriptionFields();
	$subscription_details = $sub_obj->getSubscriptionInformation($subscription_id);

	$total_fields = COUNT($subscription_fields);
	$fields_per_row = 4;
	$total_row = ceil($total_fields / $fields_per_row);
	$remaining_field_count = ($total_row % $fields_per_row);
	if(is_array($subscription_details))
	{
		$to_return .= '<div class="row-fluid"><div class="span12">';
		if($total_fields > 0)
		{
			$j = 1;
			for($i=0; $i<$total_fields; $i++)
			{
				if(($j == 1) || ($j % $fields_per_row == 1)) {
					$to_return .= '<div class="row-fluid">';
				}
					$to_return .= '<div class="span2"><span class="muted pull-right">';
						$to_return .= $subscription_fields[$i][1];
					$to_return .= '</span></div>';
					$to_return .= '<div class="span1"><p class="pull-left">';
						$to_return .= $subscription_details[0][$i];
					$to_return .= '</p></div>';

				if(($j == $total_field_value) || ($j % $fields_per_row == 0)) {
					$to_return .= '</div>';
				}

				$j++;
			}
		}
		$to_return .= '</div></div>';
	}

	echo $to_return;
	exit;
}
else if($req == 8)
{
	//delete specific subscription details
	$subscription_id = trim($_POST['subscriptionID']);
	$sub_obj = new Subscription($APPLICATION_PATH);
	$status = $sub_obj->deleteSubscription($subscription_id);
	echo $status;
	exit;
}
?>