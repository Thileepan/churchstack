<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
//include $APPLICATION_PATH.'utils/utilfunctions.php';
include_once $APPLICATION_PATH . '/classes/class.funds.php';
//include_once $APPLICATION_PATH . '/classes/class.profiles.php';
include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	//List Funds
	$fund_obj = new Funds($APPLICATION_PATH);
	$funds = $fund_obj->getAllFunds();
	
	$is_results_available = false;
	if(is_array($funds) && $funds[0] == 1)
	{
		$funds = $funds[1];
		$total_funds = COUNT($funds);
		if($total_funds > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_funds; $i++)
			{
				$fund_id = $funds[$i][0];
				$fund_name = $funds[$i][1];
				$fund_desc = $funds[$i][2];
				$visibility = $funds[$i][3];

				$visibility_string = '<span class="label '.(($visibility == 1)?"label-success":"label-important").'">'.(($visibility == 1)?"Active":"InActive").'</span>';
				$actions = '<i class="curHand icon-pencil" onclick="getFundForm(1, '.$fund_id.');"></i>&nbsp;&nbsp;<i class="curHand icon-eye-close" style="display:'.(($visibility == 0)?'none':'').'" onclick="changeFundVisibility('.$fund_id.', 0);"></i><i class="curHand icon-eye-open" style="display:'.(($visibility == 0)?'':'none').'" onclick="changeFundVisibility('.$fund_id.', 1);"></i>&nbsp;&nbsp;<i class="curHand icon-trash" onclick="deleteFundConfirmation('.$fund_id.', \''.$fund_name.'\')"></i>';
				$to_return['aaData'][] = array($fund_name, $fund_desc, $visibility_string, $actions);
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
else if($req == 2)
{
	//fund add/edit form
	$fund_visibility_values = array('Hide', 'Show');

	$is_update = trim($_POST['isEdit']);
	$fund_id = trim($_POST['fundID']);
	
	if($is_update) {
		$error = false;
		$fund_obj = new Funds($APPLICATION_PATH);	
		$fund_details = $fund_obj->getFundInformation($fund_id);
		if(is_array($fund_details) && COUNT($fund_details) > 0)
		{
			if($fund_details[0] == 1)
			{
				$fund = $fund_details[1];
				$fund_name = $fund[1];
				$fund_desc = $fund[2];
				$fund_visibility = $fund[3];
			}
			else {
				$error = true;
			}
		} else {
			$error = true;			
		}

		if($error) {
			$to_return = '<span class="text-error">'.$fund_details[1].'</span>';
			echo $to_return;
			exit;
		}
	}

	$to_return = '';
	$to_return .= '<div class="row-fluid">';
		$to_return .= '<div class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';
				$to_return .= '<div class="control-group">';
						$to_return .= '<label class="control-label" for="inputFundName">Fund Name</label>';
							$to_return .= '<div class="controls">';
								$to_return .= '<input type="text" class="span10" id="inputFundName" placeholder="Fund Name" value="'.$fund_name.'">';
							$to_return .= '</div>';
					$to_return .= '</div>';
					$to_return .= '<div class="control-group">';
						$to_return .= '<label class="control-label" for="inputFundDesc">Description</label>';
							$to_return .= '<div class="controls">';
								$to_return .= '<textarea class="span10" id="inputFundDesc" placeholder="Fund Description">'.$fund_desc.'</textarea>';
							$to_return .= '</div>';
					$to_return .= '</div>';
					$to_return .= '<div class="control-group">';
						$to_return .= '<label class="control-label" for="inputFundVisibility">Visibility</label>';
							$to_return .= '<div class="controls">';
								$to_return .= '<select class="span10" id="inputFundVisibility">';
									for($i=0; $i<COUNT($fund_visibility_values); $i++)
									{
										$to_return .= '<option '.(($fund_visibility == $i)?'selected':(($i==1)?'selected':'')).'>'.$fund_visibility_values[$i].'</option>';
									}
								$to_return .= '</select>';
							$to_return .= '</div>';
					$to_return .= '</div>';
					$to_return .= '<div class="form-actions" align="center">';
						$to_return .= '<button class="btn btn-primary" type="submit" onclick="addOrUpdateFund('.$is_update.');">'.(($is_update)?'Update Fund':'Add Fund').'</button>&nbsp;';
						if(!$is_update) {
							$to_return .= '<button class="btn" type="reset">Reset</button>';
						}
						$to_return .= '<input type="hidden" id="inputHiddenFundID" value="'.$fund_id.'" />';
					$to_return .= '</div>';
			$to_return .= '</form>';
		$to_return .= '</div>';
	$to_return .= '</div>';	

	echo $to_return;
	exit;
}
else if($req == 3)
{
	//add/update fund details
	$is_update = trim($_POST['isUpdate']);
	$fund_id = trim($_POST['fundID']);
	$fund_name = trim($_POST['fundName']);
	$fund_description = trim($_POST['fundDesc']);
	$fund_visibility = trim($_POST['visibility']);

	$funds_obj = new Funds($APPLICATION_PATH);
	if($is_update) {
		$status = $funds_obj->updateFund($fund_id, $fund_name, $fund_description, $fund_visibility);
	} else {
		$status = $funds_obj->addFund($fund_name, $fund_description, $fund_visibility);
	}

	echo $status;
	exit;
}
else if($req == 4)
{
	//change fund visibility status
	$fund_id = trim($_POST['fundID']);
	$visibility = trim($_POST['visibilityStatus']);

	$funds_obj = new Funds($APPLICATION_PATH);
	$status = $funds_obj->updateFundVisibility($fund_id, $visibility);

	echo $status;
	exit;
}
else if($req == 5)
{
	//delete the fund
	$fund_id = trim($_POST['fundID']);

	$funds_obj = new Funds($APPLICATION_PATH);
	$status = $funds_obj->deleteFund($fund_id);

	echo $status;
	exit;
}
else if($req == 6)
{
	//list all batches
	$fund_obj = new Funds($APPLICATION_PATH);
	$batches = $fund_obj->getAllBatches();
	
	$is_results_available = false;
	if(is_array($batches) && $batches[0] == 1)
	{
		$batches = $batches[1];
		$total_batch = COUNT($batches);
		if($total_batch > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_batch; $i++)
			{
				$batch_id = $batches[$i][0];
				$batch_name = $batches[$i][1];
				$batch_info = '<a href="#" onclick="showBatchDetails('.$batch_id.')">'.$batch_name . '</a>';//(<a href="#">#'.$batch_id.'</a>)';
				$batch_desc = $batches[$i][2];
				$batch_created_time = $batches[$i][3];
				//$last_updated_time = $batches[$i][4];
				$expected_amount = $batches[$i][5];
				$received_amount = $batches[$i][6];

				$actions = '<i class="curHand icon-pencil" onclick="getBatchForm(1, '.$batch_id.');"></i>&nbsp;&nbsp;<i class="curHand icon-trash" onclick="deleteBatchConfirmation('.$batch_id.', \''.$batch_name.'\')"></i>';
				$to_return['aaData'][] = array($batch_info, $batch_desc, $batch_created_time, $expected_amount, $received_amount, $actions);
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
	//get add/edit batch form
	$is_update = trim($_POST['isEdit']);
	$batch_id = trim($_POST['batchID']);
	
	if($is_update) {
		$fund_obj = new Funds($APPLICATION_PATH);	
		$batch_details = $fund_obj->getBatchInformation($batch_id);
		if(is_array($batch_details) && COUNT($batch_details) > 0)
		{
			if($batch_details[0] == 1)
			{
				$batch = $batch_details[1];
				$batch_name = $batch[1];
				$batch_desc = $batch[2];
				$expected_amount = $batch[3];
			}
			else {
				$error = true;
			}
		} else {
			$error = true;			
		}

		if($error) {
			$to_return = '<span class="text-error">'.$batch_details[1].'</span>';
			echo $to_return;
			exit;
		}
		
	}

	$to_return = '';
	$to_return .= '<div class="row-fluid">';
		$to_return .= '<div class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputBatchName">Batch Name</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span10" id="inputBatchName" placeholder="Batch Name" value="'.$batch_name.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputBatchDesc">Description</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<textarea class="span10" id="inputBatchDesc" placeholder="Batch Description">'.$batch_desc.'</textarea>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputExpectedAmount">Expected Amount</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span10" id="inputExpectedAmount" placeholder="Expected Amount" value="'.$expected_amount.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="form-actions" align="center">';
					$to_return .= '<button class="btn btn-primary" type="submit" onclick="addOrUpdateBatch('.$is_update.');">'.(($is_update)?'Update Batch':'Add Batch').'</button>&nbsp;';
					if(!$is_update) {
						$to_return .= '<button class="btn" type="reset">Reset</button>';
					}
					$to_return .= '<input type="hidden" id="inputHiddenBatchID" value="'.$batch_id.'" />';
				$to_return .= '</div>';
			$to_return .= '</form>';
		$to_return .= '</div>';
	$to_return .= '</div>';	

	echo $to_return;
	exit;
}
else if($req == 8)
{
	//add/edit batch details
	$is_update = trim($_POST['isUpdate']);
	$batch_id = trim($_POST['batchID']);
	$batch_name = trim($_POST['batchName']);
	$batch_description = trim($_POST['batchDesc']);
	$expected_amount = trim($_POST['expectedAmount']);
	
	$dt = Carbon::now('Asia/Calcutta');
	if($is_update) {
		$last_updated_time = $dt->toDateTimeString();
	} else {
		$batch_created_time = $dt->toDateTimeString();
	}
	
	$funds_obj = new Funds($APPLICATION_PATH);
	if($is_update) {
		$status = $funds_obj->updateBatch($batch_id, $batch_name, $batch_description, $expected_amount, $last_updated_time);
	} else {
		$status = $funds_obj->addBatch($batch_name, $batch_description, $expected_amount, $batch_created_time);
	}

	echo $status;
	exit;
}
else if($req == 9)
{
	//delete the batch
	$batch_id = trim($_POST['batchID']);

	$funds_obj = new Funds($APPLICATION_PATH);
	$status = $funds_obj->deleteBatch($batch_id);

	echo $status;
	exit;
}
else if($req == 10)
{
	$to_return = '';
	$to_return .= '<div class="tabbable">';
		$to_return .= '<ul class="nav nav-tabs">';
			$to_return .= '<li id="summaryTab" class="active" onclick="getBatchSummary('.$batch_id.')"><a href="#summaryTab" data-toggle="tab">Summary</a></li>';
			$to_return .= '<li id="addContributionTab" onclick="getAddOrUpdateContributionForm('.$is_update.', '.$batch_id.', '.$contribution_id.');"><a href="#addContributionTab" data-toggle="tab">Add Contribution</a></li>';
			$to_return .= '<li id="listContributionTab" onclick="listAllContributions('.$batch_id.');"><a href="#listContributionTab" data-toggle="tab">List Contributions</a></li>';
		$to_return .= '</ul>';
		$to_return .= '<div class="tab-content">';
			$to_return .= '<div class="tab-pane active" id="summaryDiv">';
			$to_return .= '</div>';
			$to_return .= '<div class="tab-pane" id="addContributionDiv">';
			$to_return .= '</div>';
			$to_return .= '<div class="tab-pane" id="listContributionDiv">';
			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	echo $to_return;
	exit;
}
else if($req == 11)
{
	//get batch summary details
}
else if($req == 12)
{
	//get add/update contribution form
}
else if($req == 13)
{
	//list contribution details

	$fund_obj = new Funds($APPLICATION_PATH);
	$contribution_result = $fund_obj->getAllContributions();
	
	$is_results_available = false;
	if(is_array($contribution_result) && $contribution_result[0] == 1)
	{
		$contributions = $contribution_result[1];
		$total_contributions = COUNT($contributions[1]);
		if($total_contributions > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_contributions; $i++) {
				
				$actions = '<i class="curHand icon-trash" onclick="deleteSubscriptionConfirmation('.$subscriptions[$i][0].','.$subscriptions[$i][1].')"></i>&nbsp;&nbsp;';			
				
				$to_return['aaData'][] = array('<img src="plugins/datatables/examples/examples_support/details_open.png" />', $contributions[$i][1], $contributions[$i][3], $contributions[$i][5], $contributions[$i][6], $contributions[$i][9], $actions);
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
else if($req == 14)
{
	//list contribution split details

	$contribution_id = trim($_POST['contributionID']);

	$fund_obj = new Funds($APPLICATION_PATH);
	$split_details = $fund_obj->getContributionSplitDetails($contribution_id);

	if(is_array($split_details))
	{
		$total_splits = COUNT($split_details);		
		if($total_splits > 0)
		{
			for($i=0; $i<$total_splits; $i++)
			{
				$to_return .= '<div class="row-fluid"><div class="span12">';
					$to_return .= '<table class="table table-condensed">';
						$to_return .= '<tbody>';
							$to_return .= '<tr>';
								$to_return .= '<td>'.$split_details[$i][3].'</td>';
								$to_return .= '<td>'.$split_details[$i][4].'</td>';
								$to_return .= '<td>'.$split_details[$i][5].'</td>';
							$to_return .= '</tr>';
						$to_return .= '</tbody>';
					$to_return .= '</table>';
				$to_return .= '</div>';
			}
		}
	}

	echo $to_return;
	exit;
}
?>