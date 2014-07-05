<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include_once $APPLICATION_PATH . '/classes/class.reports.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	//list reports
	$reports_obj = new Reports($APPLICATION_PATH);
	$report_list = $reports_obj->listAllReports();
//	print_r($report_list);

	/*
	$to_return .= '<div class="pull-right" id="divListTemplatesBtn">';
		$to_return .= '<button class="btn btn-small btn-primary" type="button" onclick="showSearchForm();">List Default Templates</button>';
	$to_return .= '</div>';
	$to_return .= '<BR>';
	*/

	$req_from = trim($_POST['reqFrom']);
	$to_return .= '<div id="divProfileSearchForm" class="row-fluid">';
		$to_return .= '<div class="span12">';			
				$to_return .= '<div class="row-fluid">';
					$to_return .= '<div class="offset2 span10">';
						$to_return .= '<p class="muted">&nbsp;&nbsp;&nbsp;&nbsp;Add Rules &nbsp; <i class="icon-plus-sign curHand" onclick="addReportRuleRow()"></i></p>';						
						$to_return .= '<div id="addRuleOuterDiv">';
							$to_return .= '<div class="row-fluid" id="divAddRule-1">';
								//$to_return .= '<div class="span1"></div>';
								$to_return .= '<div class="span4" id="divReportType-1">';
									$to_return .= '&nbsp;&nbsp;&nbsp;&nbsp;<select onChange="changeRuleSubTypeAndValue(1);" id="selReportType-1" disabled>';
										$to_return .= '<option value="PROFILES">PROFILES</option>';
										$to_return .= '<option value="GENDER">GENDER</option>';
										$to_return .= '<option value="AGE">AGE</option>';
										$to_return .= '<option value="BIRTH_DATE">BIRTH DATE</option>';
										$to_return .= '<option value="MARRIAGE_DATE">MARRIAGE DATE</option>';
										$to_return .= '<option value="BIRTH_MARRIAGE_DATE">BIRTH OR MARRIAGE DATE</option>';
										$to_return .= '<option value="MARITAL_STATUS">MARITAL STATUS</option>';
										$to_return .= '<option value="BAPTISM">BAPTISM</option>';
										$to_return .= '<option value="CONFIRMATION">CONFIRMATION</option>';
									$to_return .= '</select>';
								$to_return .= '</div>';+
								$to_return .= '<div class="span3" id="divReportSubType-1" style="display:none">';
									//$to_return .= '<select><option value="-1">All</option><option value="1">Family Head</option><option value="2">Individual</option></select>';
								$to_return .= '</div>';
								$to_return .= '<div class="span3" id="divReportValue-1">';
									$to_return .= '<select id="selRuleValueItem-1"><option value="ALL">All</option><option value="FAMILY_HEAD">Family Head</option><option value="INDIVIDUAL">Individual</option></select>';
								$to_return .= '</div>';
							$to_return .= '</div>'; //end of rule1
						$to_return .= '</div>';
						$to_return .= '<input type="hidden" id="maxReportRuleRowID" value="1" />';
						$to_return .= '<input type="hidden" id="reportRuleRowIDList" value="1" />';					
				$to_return .= '</div>';
			$to_return .= '</div>';
			
			$to_return .= '<div class="row-fluid"><div class="offset2 span10">&nbsp;<label class="checkboxs">&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="includeInactiveProfile">&nbsp;Include inactive profiles in the reports</label></div></div>';

			
			if($req_from == 1)
			{
				$to_return .= '<div id="addColumnOuterDiv">';
					$to_return .= '<div class="row-fluid" id="divAddColumn-1">';
						$to_return .= '<div class="offset2 span10">';
							$to_return .= '<hr>';
							$to_return .= '<p class="muted">&nbsp;&nbsp;&nbsp;&nbsp;Add Columns &nbsp;</p>';
							$to_return .= '<div class="span4">';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column0" checked>Member ID</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column1" checked>Family Head Name</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column2" checked>Profile Name</label>';	
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column3">Date of Birth</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column18">Age</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column4">Gender</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column5">Relationship</label>';
							$to_return .= '</div>';
							$to_return .= '<div class="span4">';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column6">Marital Status</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column7">Date of Marriage</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column8">Place of Marriage</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column9">Baptised</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column10">Confirmation</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column11">Occupation</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column12">Is Another Church Member</label>';
							$to_return .= '</div>';
							$to_return .= '<div class="span3">';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column13">Address</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column14">Contact Numbers</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column15">Email</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column16">Profile Status</label>';
								$to_return .= '<label class="checkbox"><input type="checkbox" id="column17">Notes</label>';
							$to_return .= '</div>';						
						$to_return .= '</div>';
						$to_return .= '<div class="span3"></div>';
					$to_return .= '</div>';
				$to_return .= '</div>';
			}
			/**/
			
			$to_return .= '<HR>';
			$to_return .= '<div class="row-fluid">';
				$to_return .= '<div id="divSearchBtn" class="offset5 span7">';
					$to_return .= '<button class="btn btn-primary" onclick="performSearch();">Search</button>&nbsp;';
					$to_return .= '<button class="btn" onclick="resetSearchForm();">Cancel</button>';
					$to_return .= '<input type="hidden" id="hiddenReqFrom" value="'.$req_from.'" />';
				$to_return .= '</div>';
				$to_return .= '<div id="divLoadingSearchImg" class="offset5 span7" style="display:none">';
					$to_return .= '<span><img src="images/ajax-loader.gif" />&nbsp;Please wait...</span>';
				$to_return .= '</div>';
			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>'; //end of search form

	$to_return .= '<div id="divListReports" style="display:none">';
		$to_return .= '<table id="listreports" class="table bordered-table table-striped">';
			$to_return .= '<thead>';
				$to_return .= '<tr>';
					$to_return .= '<th>ID</th>';
					$to_return .= '<th>Title</th>';
					$to_return .= '<th>Description</th>';
					$to_return .= '<th>Actions</th>';
				$to_return .= '</tr>';
			$to_return .= '</thead>';
			$to_return .= '<tbody>';

			$is_report_available = false;
			if(is_array($report_list))
			{
				$total_reports = COUNT($report_list);
				if($total_reports > 0)
				{
					$is_report_available = true;
					for($i=0; $i<$total_reports; $i++)
					{
						$to_return .= '<tr>';
							$to_return .= '<td>'.$report_list[$i][0].'</td>';
							$to_return .= '<td>'.$report_list[$i][1].'</td>';
							$to_return .= '<td>'.$report_list[$i][2].'</td>';
							$to_return .= '<td><button class="btn btn-small btn-warning" onclick="viewReportRequest('.$report_list[$i][0].');">View Report</button>&nbsp;<button class="btn btn-small" style="display:none">Delete Report</button></td>';
						$to_return .= '</tr>';
					}
				}
			}

			if(!$is_report_available)
			{
				$to_return .= '<tr>';
					$to_return .= '<td colspan="4" align="center">'.'No profile settings available'.'</td>';
				$to_return .= '</tr>';
			}

			$to_return .= '</tbody>';
		$to_return .= '</table>';
	$to_return .= '</div>';

	$to_return .= '<div class="row-fluid" id="divReportTable"><div class="span12" style="display:none"></div></div>';

	echo $to_return;
	exit;
}
else if($req == 2 || $req == 3)
{
	$req_from = trim($_POST['reqFrom']);
	if($req == 2) {
		$report_id = trim($_POST['reportID']);
	} else {
		$rule_type = explode(',', trim($_POST['ruleType']));
		$rule_sub_type = explode(',', trim($_POST['ruleSubType']));
		$rule_value = explode(',', trim($_POST['ruleValue']));
		$report_columns = explode(',', trim($_POST['columnData']));
		$include_inactive_profile = trim($_POST['includeInactiveProfile']);

		if(COUNT($rule_type) > 0)
		{
			for($i=0; $i<COUNT($rule_type); $i++)
			{
				$report_rules[] = array($rule_type[$i], $rule_sub_type[$i], $rule_value[$i]);
			}
		}
	}
	
	$reports_obj = new Reports($APPLICATION_PATH);

	if($req == 2) {
		$report_rules = $reports_obj->getReportRules($report_id);
		$report_columns = $reports_obj->getReportColumns($report_id);
	}
	//print_r($report_rules);
	//print_r($report_columns);
	$reports = $reports_obj->generateReports($report_rules, $report_columns, $include_inactive_profile, $req_from);
	//print_r($reports);exit;

	$is_results_available = false;
	$to_return['aoColumns'] = array();
	if(is_array($reports))
	{
		$column_names = $reports[0];
		$column_values = $reports[1];
//		print_r($column_values);

		if(is_array($column_names))
		{
			$column_count = COUNT($column_names);
			$to_return['aoColumns'] = $column_names;
			//$to_return['aoColumnDefs'][] = array("sWidth"=> "10%", "aTargets"=> [0]);
		}
		
		if(is_array($column_values))
		{
			$row_count = COUNT($column_values);
			if($row_count > 0)
			{
				$to_return['aaData'] = $column_values;//array();
				$is_results_available = true;
				/*
				for($i=0; $i<$row_count; $i++) {

					$row_data = array();
					for($j=0; $j<$column_count; $j++) {
						array_push($row_data, $column_values[$j]);
					}
//					$to_return['aaData'][$i] = $row_data;
					array_push($to_return['aaData'], $row_data);
				}
				*/
			}
		}
	}

//	print_r($to_return['aaData']);
//	$to_return['sEcho'] = 1;
//	$to_return['iTotalRecords'] = 1;
//	$to_return['iTotalDisplayRecords'] = 1;
//	$to_return['aoColumns'][] = $to_return_column;//array('sTitle'=>'Heading');
//	$to_return['aoColumnDefs'][] = array("sWidth"=> "10%", "aTargets"=> [0],"sWidth"=> "10%", "aTargets"=> [1]);

	//$to_return = array('sEcho'=>1, 'iTotalRecords'=>1, 'iTotalDisplayRecords'=>1, 'aaData' => $to_return['aaData']);
	//$to_return = array('aaData'=>$to_return['aaData']);

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
else if($req == 4)
{
	include_once $APPLICATION_PATH . '/classes/class.subscription.php';

	$sub_obj = new Subscription($APPLICATION_PATH);
	$subscription_fields = $sub_obj->getActiveSubscriptionFields();

	$to_return = '';
	$to_return .= '<div class="row-fluid" id="divSubscriptionSearchForm">';
		$to_return .= '<div class="span12">';			
			$to_return .= '<div align="center">';
				$to_return .= '<table width="50%">';
					$to_return .= '<tr>';
						$to_return .= '<td>From Date</td>';
						$to_return .= '<td><input type="text" id="inputSubReportFromDate" value="" placeholder="From Date" data-date-format="dd/mm/yyyy" /></td>';
					$to_return .= '</tr>';
					$to_return .= '<tr>';
						$to_return .= '<td>To Date</td>';
						$to_return .= '<td><input type="text" id="inputSubReportToDate" value="" placeholder="To Date" data-date-format="dd/mm/yyyy" /></td>';
					$to_return .= '</tr>';
					$to_return .= '<tr>';
						$to_return .= '<td>Subscription Fields</td>';
						$to_return .= '<td>';
						/*
							$to_return .= '<select multiple>';
								if(is_array($subscription_fields))
								{
									$to_return .= '<option value=-1>All</option>';
									for($i=0; $i<COUNT($subscription_fields); $i++) {
										$to_return .= '<option value="'.$subscription_fields[$i][0].'">'.$subscription_fields[$i][1].'</option>';
									}
								}
							$to_return .= '</select><BR><span style="font-size:10px;">Choose multiple option using SHIFT key</span>';
						*/
							if(is_array($subscription_fields))
							{
								//print_r($subscription_fields);
								$to_return .= '<span id="spanSelectAllLink"><a href="#" onclick="checkOrUncheckAllSubscriptionFields(1);"><u>Select All</u></a><BR></span>';
								$to_return .= '<span style="display:none" id="spanUnselectAllLink"><a href="#" onclick="checkOrUncheckAllSubscriptionFields(0);"><u>Unselect All</u></a><BR></span>';
								$to_return .= '<table width="100%"><tr>';
								$total_fields = COUNT($subscription_fields);
								$total_columns = 3; //hardcoded value;
								$num_of_fields_per_column = ceil($total_fields/$total_columns);
								//echo "AAA:::".$num_of_fields_per_column."BBB:::".$total_fields;
							
								$j = 0;
								$sub_field_ids = '';
 								for($i=1; $i<=$total_fields; $i++)
								{
									$open_tag = false;
									$close_tag = false;
									$temp = ($i % $num_of_fields_per_column);
									if($temp == 0) {
										$close_tag = true;
									}else if($temp == 1) {
										$open_tag = true;
									}

									if($open_tag) {
										$to_return .= '<td>';
									}

									$to_return .= '<input type="checkbox" id="subcriptionFieldID-'.$subscription_fields[$j][0].'" value="'.$subscription_fields[$j][0].'" />&nbsp;'.$subscription_fields[$j][1].'<BR>';

									if(strlen($sub_field_ids) > 0) {
										$sub_field_ids .= ",";
									}
									$sub_field_ids .= $subscription_fields[$j][0];

									if($close_tag) {
										$to_return .= '</td>';
									}
									$j++;
								}
								$to_return .= '</tr></table>';
							}
						$to_return .= '</td>';
					$to_return .= '</tr>';
					$to_return .= '<tr height="50px"><td></td><td>';
						$to_return .= '<div id="divSearchBtn">';
							$to_return .= '<button class="btn btn-primary" onclick="performSubscriptionSearch();">Search</button>&nbsp;';
							$to_return .= '<button class="btn" onclick="resetSubscriptionSearchForm();">Cancel</button>';
							$to_return .= '<input type="hidden" id="hiddenSubFieldIds" value="'.$sub_field_ids.'">';
						$to_return .= '</div>';
						$to_return .= '<div id="divLoadingSearchImg" style="display:none">';
							$to_return .= '<span><img src="images/ajax-loader.gif" />&nbsp;Please wait...</span>';
						$to_return .= '</div>';
					$to_return .= '</td></tr>';
				$to_return .= '</table>';
			$to_return .= '</div>';
		$to_return .= '</div>';		
	$to_return .= '</div>';
	$to_return .= '<div class="row-fluid" id="divReportTable"><div class="span12" style="display:none"></div></div>';

	echo $to_return;
	exit;
}
else if($req == 5)
{
	//perform subscription reports
	$from_date = trim($_POST['fromDate']);
	$to_date = trim($_POST['toDate']);
	$sub_fields = trim($_POST['subFields']);
	$sub_fields_arr = explode(",", $sub_fields);

	$reports_obj = new Reports($APPLICATION_PATH);
	$reports = $reports_obj->generateSubscriptionReports($from_date, $to_date, $sub_fields_arr);

	$is_results_available = false;
	$to_return['aoColumns'] = array();
	if(is_array($reports))
	{
		$column_names = $reports[0];
		$column_values = $reports[1];
//		print_r($column_values);

		if(is_array($column_names))
		{
			$column_count = COUNT($column_names);
			$to_return['aoColumns'] = $column_names;
		}
		
		if(is_array($column_values))
		{
			$row_count = COUNT($column_values);
			if($row_count > 0)
			{
				$to_return['aaData'] = $column_values;
				$is_results_available = true;
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

?>