<?php

$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include $APPLICATION_PATH.'utils/utilfunctions.php';
include_once $APPLICATION_PATH . '/classes/class.profiles.php';
include_once $APPLICATION_PATH . '/classes/class.settings.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	@include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';
	//use Carbon\Carbon;
	
	//List all profiles
	$profiles_obj = new Profiles($APPLICATION_PATH);
	$profiles = $profiles_obj->getAllProfiles();
	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	$salutation_values = $settings_obj->getOptions(1);
	$re_arranged_salutation = array();
	if(is_array($salutation_values))
	{
		foreach($salutation_values as $key => $value)
		{
			for($k=0; $k<2; $k++)
			{
				if($k == 0)
					$sal_id = $value[$k];
				if($k == 1)
					$sal_value = $value[$k];
			}
			$re_arranged_salutation[$sal_id] = $sal_value;
		}
	}

	$is_results_available = false;
	if(is_array($profiles))
	{
		$profile_count = COUNT($profiles);
		if($profile_count > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$profile_count; $i++) {
				
				$is_profile_head = (($profiles[$i][25] == -1)?1:0);
				$unique_id = 'STC'.appendZeroInUniqueID($profiles[$i][3]);
				$parent_head_icon = (($is_profile_head)?'<i class="icon-user"></i>':'');

				$actions = '<div class="dropdown">';
					$actions .= '<i class="curHand icon-pencil" onclick="getAddOrEditProfileForm(1, '.$profiles[$i][0].')"></i>&nbsp;&nbsp;';
					$actions .= '<i class="curHand icon-trash" onclick="deleteProfileConfirmation('.$profiles[$i][0].',\''. $unique_id. '\', \''.$profiles[$i][2].'\','. $is_profile_head .')"></i>&nbsp;&nbsp;';
				$actions .= '</div>';				
				
				$age = '-';
				$date_of_birth = $profiles[$i][4];
				if($date_of_birth != '0000-00-00') {
					$dob_arr = explode('-', $profiles[$i][4]);
					$age = Carbon::createFromDate($dob_arr[0], $dob_arr[1], $dob_arr[2])->age;
				}

				$salutation_id = $profiles[$i][1];
				if($salutation_id != "") {
					$salutation_value = $re_arranged_salutation[$salutation_id];
				}
				$profile_name = $salutation_value .'. '. $profiles[$i][2];

				$to_return['aaData'][] = array($unique_id, $parent_head_icon, '<a href="#" onclick="showProfileDetails('.$profiles[$i][0].')">'.$profile_name.'</a>', formatDateOfBirth($profiles[$i][4]), $age, $profiles[$i][15], $profiles[$i][16], $actions);
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
	//Add/Edit profile Form
	$gender_list = array('Male', 'Female');
	$babtised_list = array('Yes', 'No');
	$confirmation_list = array('Yes', 'No');
	$another_church_member_list = array('Yes', 'No');
	$name_append = 'STC';

	$is_update = trim($_POST['isEdit']);
	date_default_timezone_set('UTC');
	
	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	$salutation_list = $settings_obj->getOptions(1);
	$relation_list = $settings_obj->getOptions(2);
	$marital_list = $settings_obj->getOptions(3);
	$profile_status_list = $settings_obj->getOptions(4);
	
	$profiles_obj = new Profiles($APPLICATION_PATH);
	$parent_list = $profiles_obj->getAllParentProfiles();
	/*
	//if(!$is_update)
	{
		$max_unique_id = $profiles_obj->getMaxProfileUniqueID();
		if($max_unique_id != -1) {
			$max_unique_id++;
			$max_unique_id = $name_append . appendZeroInUniqueID($max_unique_id);			
		} else {
			//echo "Error";exit; //TODO Error Handling
		}
	}
	*/

	if($is_update) {
		$profile_id = trim($_POST['profileID']);
		
		$profile_info = $profiles_obj->getProfileInformation($profile_id);
		$unique_id = $name_append.appendZeroInUniqueID($profile_info[3]);
		$date_of_birth = $profile_info[4];
		$marriage_date = $profile_info[8];
		
		if($date_of_birth != '') {
			$dob_arr = explode('-', $date_of_birth);
			$date_of_birth = $dob_arr[2]. '/'. $dob_arr[1]. '/' .$dob_arr[0];
		}
		
		if($marriage_date != '') {
			$marriage_date_arr = explode('-', $profile_info[8]);
			$marriage_date = $marriage_date_arr[2]. '/'. $marriage_date_arr[1]. '/' .$marriage_date_arr[0];		
		}
	}

	//Custom Fields
	$field_details = $settings_obj->getAllCustomProfileFields();
	$total_custom_fields = COUNT($field_details);

	$toReturn = '';
	$toReturn .= '<form id="profileForm" class="form-horizontal" action="server/doserver.php" method="post" enctype="multipart/form-data" onsubmit="return false;">'; 
				$toReturn .= '<div class="row-fluid">';
					$toReturn .= '<div class="span6">';
					  $toReturn .= '<p class="text-left text-info"><b>Personal Information</b></p>';
					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputSalutation">Salutation</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<select id="inputSalutation">';
									if(is_array($salutation_list)) {
										$salutation_list_count = COUNT($salutation_list);
										if($salutation_list_count > 0)
										{
											for($i=0; $i<$salutation_list_count; $i++) {
												$toReturn .= '<option value="'.$salutation_list[$i][0].'" '.(($salutation_list[$i][0] == $profile_info[1])?"selected":"").'>'.$salutation_list[$i][1].'</option>';
											}
										}
									}
								$toReturn .= '</select>';
								$toReturn .= '&nbsp;';
								
							$toReturn .= '</div>';
					  $toReturn .= '</div>';

					  $toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputFirstName">Name</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<input type="text" id="inputFirstName" placeholder="First" value="'.$profile_info[2].'" style="width:16%">&nbsp;';
								$toReturn .= '<input type="text" id="inputMiddleName" placeholder="Middle" value="'.$profile_info[26].'" style="width:16%">&nbsp;';
								$toReturn .= '<input type="text" id="inputLastName" placeholder="Last" value="'.$profile_info[27].'" style="width:16%">&nbsp;';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  
					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputParentID">Family Head</label>';
							$toReturn .= '<div class="controls">';
							/*
								$toReturn .= '<select id="inputParentID" onchange="onChangeParentID(this);">';
									if(is_array($parent_list))
									{*/   
										$parent_list_count = COUNT($parent_list);
										//$toReturn .= '<option value="-1" '.((!$is_update)?'selected':'').'>Select Family Head</option>'; */
										$parent_id = '';
										$parent_name = '';
										$parent_unique_id = '';
										$selected_parent_name = '';
										if($parent_list_count > 0) {
											for($i=0; $i<$parent_list_count; $i++) {
												$is_selected = (($parent_list[$i][0] == $profile_info[25])?'selected':'');
												//$toReturn .= '<option value="'.$parent_list[$i][0].'" '.$is_selected.'>'.$parent_list[$i][1].' [ '. $name_append . appendZeroInUniqueID($parent_list[$i][2]).' ]</option>';

												$parent_marriage_date = $parent_list[$i][9];
												if($parent_marriage_date != '') {
													$parent_marriage_date_arr = explode('-', $parent_marriage_date);
													$parent_marriage_date = $parent_marriage_date_arr[2]. '/'. $parent_marriage_date_arr[1]. '/' .$parent_marriage_date_arr[0];		
												}

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
												$temp_parent_unique_id = $name_append . appendZeroInUniqueID($parent_list[$i][2]);
												$parent_unique_id .= $parent_list[$i][1].'-'.$temp_parent_unique_id;

												if($parent_list[$i][0] == $profile_info[25])
												{
													$selected_parent_name = $parent_list[$i][1] . "-" . $temp_parent_unique_id;
												}
												
												$hidden_parent_unique_id .= '<input type="hidden" id="hidParentUniqueID-'.$parent_list[$i][0].'" value="'.$name_append . appendZeroInUniqueID($parent_list[$i][2]).'">';
												$hidden_parent_addr .= '<input type="hidden" id="hidParentAddr1-'.$parent_list[$i][0].'" value="'.$parent_list[$i][3].'"><input type="hidden" id="hidParentAddr2-'.$parent_list[$i][0].'" value="'.$parent_list[$i][4].'"><input type="hidden" id="hidParentAddr3-'.$parent_list[$i][0].'" value="'.$parent_list[$i][5].'"><input type="hidden" id="hidParentArea-'.$parent_list[$i][0].'" value="'.$parent_list[$i][6].'"><input type="hidden" id="hidParentPincode-'.$parent_list[$i][0].'" value="'.$parent_list[$i][7].'"><input type="hidden" id="hidParentMarriageDate-'.$parent_list[$i][0].'" value="'.$parent_marriage_date.'"><input type="hidden" id="hidParentMarriagePlace-'.$parent_list[$i][0].'" value="'.$parent_list[$i][10].'">';
											}
										}/*
									}
									
								$toReturn .= '</select>';
								*/
								//$toReturn .= '<span><BR><a href="#" class="muted" onclick=""><u>Search with Member ID</u></a></span>';
								
								$toReturn .= '<input type="hidden" id="hiddenParentID" value="'.$parent_id.'" />';
								$toReturn .= '<input type="hidden" id="hiddenParentName" value="'.$parent_name.'" />';
								$toReturn .= '<input type="hidden" id="hiddenParentUniqueID" value="'.$parent_unique_id.'" />';
								$toReturn .= '<input type="text" id="inputParentID" data-provide="typeahead" autocomplete="off" placeholder="Leave Blank to add Family Head" onblur="validateSelectedParentID();" value="'.$selected_parent_name.'">';
								$toReturn .= '<input type="hidden" id="selectedParentID" value="'.(($is_update)?$profile_info[25]:"-1").'" />';
								$toReturn .= $hidden_parent_unique_id;
								$toReturn .= $hidden_parent_addr;
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;display:none;">';
							$toReturn .= '<label class="control-label" for="inputUniqueID">Member ID</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<input type="text" id="inputUniqueID" placeholder="Member ID" value="'.(($is_update)?$unique_id:$max_unique_id).'" disabled>';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputDOB">Date Of Birth</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<input type="text" value="'.$date_of_birth.'" data-date-format="dd/mm/yyyy" id="inputDOB">';
							//$toReturn .= '<span class="help-block" stlye="margin-top: 10x;"><p class="text-info" style="margin-bottom:0px;">26 Years Old</p></span>';								
							$toReturn .= '</div>';							

					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputGender">Gender</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<select id="inputGender" >';
								for($i=0; $i<2; $i++) {
									$toReturn .= '<option value="'.($i+1).'" '.(($profile_info[5] == $i+1)?"selected":"").'>'.$gender_list[$i].'</option>';
								}
							$toReturn .= '</select>';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					 $toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputRelationship">Relationship</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<select id="inputRelationship" onchange="onChangeRelationShipStatus(this)">';
								$toReturn .= '<option value="-1">I\'m not sure</option>';
								if(is_array($relation_list)) {
									$relation_list_count = COUNT($relation_list);
									if($relation_list_count > 0)
									{
										for($i=0; $i<$relation_list_count; $i++) {
											$toReturn .= '<option value="'.$relation_list[$i][0].'" '.(($relation_list[$i][0] == $profile_info[6])?"selected":"").'>'.$relation_list[$i][1].'</option>';
										}
									}
								}
							$toReturn .= '</select>';
						$toReturn .= '</div>';
					$toReturn .= '</div>';

					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputMartialStatus">Martial Status</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<select  id="inputMartialStatus" onkeyup="onChangeMaritalStatus(this)" onkeydown="onChangeMaritalStatus(this)" onchange="onChangeMaritalStatus(this)">';
									$is_married = false;
									$toReturn .= '<option value="-1">I\'m not sure</option>';
									if(is_array($marital_list)) {
										$marital_list_count = COUNT($marital_list);
										if($marital_list_count > 0)
										{
											for($i=0; $i<$marital_list_count; $i++) {
												$toReturn .= '<option value="'.$marital_list[$i][0].'" '.(($marital_list[$i][0] == $profile_info[7])?"selected":"").'>'.$marital_list[$i][1].'</option>';

												if($profile_info[7] == 2) {
													$is_married = true;
												}
											}
										}
									}
								$toReturn .= '</select>';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;display:'.(($is_married)?"":"none").'" id="divMarriageDate">';
							$toReturn .= '<label class="control-label" for="inputMarriageDate">Date of Marriage</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<input type="text" id="inputMarriageDate" placeholder="Date Of Marriage" value="'.$marriage_date.'" data-date-format="dd/mm/yyyy" >';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;display:'.(($is_married)?"":"none").'" id="divMarriagePlace">';
							$toReturn .= '<label class="control-label" for="inputMarriagePlace">Place of Marriage</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<input type="text" id="inputMarriagePlace" placeholder="Place Of Marriage" value="'.$profile_info[9].'" >';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					 $toReturn .= '<p class="text-left text-info"><b>Other Information</b></p>';
					 $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputBabtised">Baptised</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<select id="inputBabtised">';
									$toReturn .= '<option value="-1">I\'m not sure</option>';
									if(is_array($babtised_list)) {
										$babtised_list_count = COUNT($babtised_list);
										if($babtised_list_count > 0)
										{
											for($i=0; $i<$babtised_list_count; $i++) {
												$toReturn .= '<option value="'.($i+1).'" '.((($i+1) == $profile_info[21])?"selected":"").'>'.$babtised_list[$i].'</option>';
											}
										}
									}
								$toReturn .= '</select>';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputConfirmation">Confirmation</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<select id="inputConfirmation">';
									$toReturn .= '<option value="-1">I\'m not sure</option>';
									if(is_array($confirmation_list)) {
										$confirmation_list_count = COUNT($confirmation_list);
										if($confirmation_list_count > 0)
										{
											for($i=0; $i<$confirmation_list_count; $i++) {
												$toReturn .= '<option value="'.($i+1).'" '.((($i+1) == $profile_info[22])?"selected":"").'>'.$confirmation_list[$i].'</option>';
											}
										}
									}
								$toReturn .= '</select>';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputOccupation">Occupation</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<input type="text" id="inputOccupation" placeholder="Occupation" value="'.$profile_info[23].'" >';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
					  $toReturn .= '<div style="padding-bottom:6px;">';
							$toReturn .= '<label class="control-label" for="inputIsAnotherChurchMember">Is Another Church Member</label>';
							$toReturn .= '<div class="controls">';
								$toReturn .= '<select id="inputIsAnotherChurchMember">';
									$toReturn .= '<option value="-1">I\'m not sure</option>';
									if(is_array($another_church_member_list)) {
										$another_church_member_list_count = COUNT($another_church_member_list);
										if($another_church_member_list_count > 0)
										{
											for($i=0; $i<$another_church_member_list_count; $i++) {
												$toReturn .= '<option value="'.($i+1).'" '.((($i+1) == $profile_info[24])?"selected":"").'>'.$another_church_member_list[$i].'</option>';
											}
										}
									}
								$toReturn .= '</select>';
							$toReturn .= '</div>';
					  $toReturn .= '</div>';
				$toReturn .= '</div>'; //end of span6

				$toReturn .= '<div class="span6">';
					$toReturn .= '<p class="text-left text-info"><b>Contact Information</b></p>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputAddress1">Address 1</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputAddress1" placeholder="Address 1" value="'.$profile_info[10].'">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputAddress2">Address 2</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputAddress2" placeholder="Address 2" value="'.$profile_info[11].'">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputAddress3">Address 3</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputAddress3" placeholder="Address 3" value="'.$profile_info[12].'">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputArea">Area</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputArea" placeholder="Area" value="'.$profile_info[13].'">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputPincode">Pincode</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputPincode" placeholder="Pincode" value="'.$profile_info[14].'">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputMobile1">Mobile</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputMobile1" placeholder="Mobile" value="'.$profile_info[16].'" >';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputLandline">Home</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputLandline" placeholder="Home Phone" value="'.$profile_info[15].'" >';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputWorkPhone">Work</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputWorkPhone" placeholder="Work Phone" value="'.$profile_info[28].'" >';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					/*
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputMobile2">Mobile 2</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="text" id="inputMobile2" placeholder="Secondary Mobile" value="'.$profile_info[17].'" >';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					*/
					
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputEmail">Email</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="email" id="inputEmail" placeholder="Email" value="'.$profile_info[18].'" >';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputProfileStatus">Profile Status</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<select id="inputProfileStatus" >';
								//$toReturn .= '<option value="-1"></option>';
								if(is_array($profile_status_list))
								{
									$profile_status_list_count = COUNT($profile_status_list);
									for($i=0; $i<$profile_status_list_count; $i++) {
										$toReturn .= '<option value="'.$profile_status_list[$i][0].'" '.(($profile_status_list[$i][0] == $profile_info[19])?"selected":"").'>'.$profile_status_list[$i][1].'</option>';
									}
								}
							$toReturn .= '</select>';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputNotes">Notes</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<textarea rows="3" id="inputNotes" placeholder="Notes">'.$profile_info[20].'</textarea>';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
				$toReturn .= '</div>'; //end of span6
			$toReturn .= '</div>';

			$toReturn .= '<div class="row-fluid">';
				$toReturn .= '<div class="span12">';
					$toReturn .= '<p class="text-left text-info"><b>Notifications</b></p>';					
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="checkbox" id="inputSMSNotification" '.(($profile_info[31] == 1)?'checked':'').'>&nbsp;Receive SMS Notifications';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<input type="checkbox" id="inputEmailNotification" '.(($profile_info[32] == 1)?'checked':'').'>&nbsp;Receive Email Notifications';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
				$toReturn .= '</div>';
			$toReturn .= '</div>';
			/*
			$toReturn .= '<div class="row-fluid">';
				$toReturn .= '<div class="span12">';
					$toReturn .= '<p class="text-left text-info"><b>Images/Photos</b></p>';
					$toReturn .= '<div style="padding-bottom:6px;">';
						$toReturn .= '<label class="control-label" for="inputMyPhotoPath">My Photo</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<div class="input-append span12">';
								$toReturn .= '<input type="text" id="inputMyPhotoPath" placeholder="Browse your photo" class="span4"/>';
								$toReturn .= '<span class="btn btn-file">Browse<input type="file" id="myPhotoPath" name="myPhotoPath"/></span>';
							$toReturn .= '</div>';							
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<BR>';
					$toReturn .= '<div id="divPreviewMyPhoto" style="padding-bottom:6px;display:none;">';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<img id="imgPreviewMyPhoto" src="'.$APPLICATION_PATH.'app/images/photo_rounded.png" class="img-rounded" width="120" height="120">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<BR>';
					$toReturn .= '<div style="padding-top:8px;">';
						$toReturn .= '<label class="control-label" for="inputFamilyPhotoPath">Family Photo</label>';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<div class="input-append span12">';
								$toReturn .= '<input type="text" id="inputFamilyPhotoPath" placeholder="Browse your family photo" class="span4"/>';
								$toReturn .= '<span class="btn btn-file">Browse<input type="file" id="familyPhotoPath" name="familyPhotoPath"/></span>';
							$toReturn .= '</div>';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
					$toReturn .= '<BR>';
					$toReturn .= '<div id="divPreviewFamilyPhoto" style="padding-bottom:6px;display:none;">';
						$toReturn .= '<div class="controls">';
							$toReturn .= '<img id="imgPreviewFamilyPhoto" src="'.$APPLICATION_PATH.'app/images/photo_rounded.png" class="img-rounded" width="120" height="120">';
						$toReturn .= '</div>';
					$toReturn .= '</div>';
				$toReturn .= '</div>';
			$toReturn .= '</div>';
			*/
			$toReturn .= '<div class="row-fluid">';
				$toReturn .= '<div class="span12">';
					$toReturn .= '<p class="text-left text-info"><b>Custom Fields</b></p>';
					if($total_custom_fields > 0)
					{
						$field_info = '';
						for($m=0; $m<$total_custom_fields; $m++)
						{
							//$profile_custom_fields
							$field_id = 'inputCustomField-' . $field_details[$m][0];
							$field_name = $field_details[$m][1];
							$field_type = $field_details[$m][2];
							$field_options = explode(",", $field_details[$m][3]);
							$is_required = $field_details[$m][5];
							$field_value = '';

							if($is_update) {
								$profile_custom_fields = $settings_obj->getProfileCustomFieldDetails($profile_id);
								if(is_array($profile_custom_fields))
								{
									$total_profile_custom_fields = COUNT($profile_custom_fields);
									if($total_profile_custom_fields > 0)
									{
										for($i=0; $i<$total_profile_custom_fields; $i++)
										{
											if($profile_custom_fields[$i][0] == $field_details[$m][0])
											{
												$field_value = $profile_custom_fields[$i][1];
												break;
											}
										}
									}
								}
							}

							$toReturn .= '<div style="padding-bottom:6px;">';
								$toReturn .= '<label class="control-label" for="'.$field_id.'">'.$field_name.'</label>';
								$toReturn .= '<div class="controls">';
								if($field_type == 1) {
									$toReturn .= '<input type="text" id="'.$field_id.'" placeholder="'.$field_name.'" value="'.$field_value.'">';
								} else if($field_type == 2) {
									$toReturn .= '<input type="number" id="'.$field_id.'" placeholder="'.$field_name.'" value="'.$field_value.'">';
								} else if($field_type == 3) {
									$toReturn .= '<input type="password" id="'.$field_id.'" placeholder="'.$field_name.'" value="'.$field_value.'">';
								} else if($field_type == 4) {
									$toReturn .= '<input type="text" data-date-format="dd/mm/yyyy" id="'.$field_id.'" placeholder="'.$field_name.'">';
								} else if($field_type == 5) {
									$toReturn .= '<input type="url" id="'.$field_id.'" placeholder="'.$field_name.'" value="'.$field_value.'">';
								} else if($field_type == 6) {
									$toReturn .= '<select id="'.$field_id.'">';
										for($n=0; $n<COUNT($field_options); $n++)
										{
											$toReturn .= '<option value="'.$n.'" '.(($field_value == $n)?"selected":"").'>'.$field_options[$n].'</option>';
										}
									$toReturn .= '</select>';
								} else if($field_type == 7) {
									$toReturn .= '<input type="checkbox" id="'.$field_id.'">';
								} else if($field_type == 8) {
									$toReturn .= '<textarea id="'.$field_id.'">'.$field_value.'</textarea>';
								}
									
								$toReturn .= '</div>';
							$toReturn .= '</div>';

							if($field_info != '') {
								$field_info .= ",";
							}
							$field_info .= $field_id."::".$field_type."::".$field_name."::".$is_required;
						}
					}
					else
					{
						$toReturn .= '<p class="muted">No custom profile fields are available.</p>';
					}
					$toReturn .= '<input type="hidden" id="hidddenFieldIDAndType" value="'.$field_info.'" />';
				$toReturn .= '</div>';
			$toReturn .= '</div>';

			$toReturn .= '<div class="row-fluid">';
				$toReturn .= '<div class="span12">';
				  $toReturn .= '<div class="form-actions">';
						//$toReturn .= '<div class="controls">';
							$toReturn .= '<button class="btn btn-primary" type="submit" onclick="addOrUpdateProfile('.$is_update.');">'.(($is_update)?'Update':'Add Profile').'</button>&nbsp;';
							if(!$is_update) {
								$toReturn .= '<button class="btn" type="reset">Reset</button>';
							}
							$toReturn .= '<input type="hidden" id="hiddenProfileID" value="'.$profile_id.'">';
							$toReturn .= '<input type="hidden" id="hiddenMaxUniqueID" value="'.$max_unique_id.'">';
							$toReturn .= '<input type="hidden" id="hiddenUniqueID" value="'.$unique_id.'">';
							$toReturn .= '<input type="hidden" id="hiddenIsUpdateReq" value="'.$is_update.'">';
							$toReturn .= '<input type="hidden" id="hiddenIsFamilyHead" value="'.$profile_info[25].'">';
						//$toReturn .= '</div>';
				  $toReturn .= '</div>';
				 $toReturn .= '</div>';
			$toReturn .= '<div>';
		$toReturn .= '</form>';

	echo $toReturn;
	exit;
}
//Request 3 to 5 are moved to dosettings.php
else if($req == 6)
{
	//Add or Update Profile

	$salutation_id = trim($_POST['salutationID']);
	$first_name = trim($_POST['firstName']);
	$middle_name = trim($_POST['middleName']);
	$last_name = trim($_POST['lastName']);
	$name = $first_name.' '.$middle_name.' '.$last_name;
	$parent_profile_id = trim($_POST['parentID']);
	//$unique_id = trim($_POST['uniqueID']);
	$date_of_birth = trim($_POST['dob']);
	$gender_id = trim($_POST['genderID']);
	$relation_ship_id = trim($_POST['relationshipID']);
	$marital_status_id = trim($_POST['maritalStatusID']);
	$marriage_date = trim($_POST['marriageDate']);
	$marriage_place = trim($_POST['marriagePlace']);	
	$address1 = trim($_POST['address1']);
	$address2 = trim($_POST['address2']);
	$address3 = trim($_POST['address3']);
	$area = trim($_POST['area']);
	$pincode = trim($_POST['pincode']);
	$landline = trim($_POST['landline']);
	$work_phone = trim($_POST['workPhone']);
	$mobile1 = trim($_POST['mobile1']);
	//$mobile2 = trim($_POST['mobile2']);
	$email = trim($_POST['email']);	
	$profile_status_id = trim($_POST['profileStatusID']);
	$notes = trim($_POST['notes']);
	$is_babtised = trim($_POST['isBabtised']);
	$is_confirmed = trim($_POST['isConfirmed']);
	$occupation = trim($_POST['occupation']);
	$is_another_church_member = trim($_POST['isAnotherChurchMember']);
	$is_update = trim($_POST['isUpdate']);
	$sms_notification = trim($_POST['smsNotification']);
	$email_notification = trim($_POST['emailNotification']);
	$family_photo_location = '';
	$profile_photo_location = '';
		
	$profile_id = -1;
	$profiles_obj = new Profiles($APPLICATION_PATH);
	if($is_update) {
		$profile_id = trim($_POST['profileID']);
		$status = $profiles_obj->updateProfile($profile_id, $salutation_id, $name, $parent_profile_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $sms_notification, $email_notification);
	} else {
		$unique_id = $profiles_obj->getMaxProfileUniqueID();
		//echo "UniqueID".$unique_id;
		if($unique_id != -1) {
			$unique_id++;
			//$unique_id = $name_append . appendZeroInUniqueID($unique_id);
			$status = $profiles_obj->addNewProfile($salutation_id, $name, $parent_profile_id, $unique_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $middle_name, $last_name, $work_phone,  $family_photo_location, $profile_photo_location, $sms_notification, $email_notification);
			$profile_id = $profiles_obj->profile_id;
		}
	}

	if($profile_id > 0) {
		//custom profile fields
		$custom_fields_list = explode('<:|:>', trim($_POST['customFields']));	
		if(is_array($custom_fields_list))
		{
			$fields_count = COUNT($custom_fields_list);
			if($fields_count > 0)
			{
				for($i=0; $i<$fields_count; $i++)
				{
					$fields = explode('::', $custom_fields_list[$i]);
					$field_id = $fields[0];
					$field_value = $fields[1];
					if($is_update) {
						$status = $profiles_obj->updateCustomProfileFields($profile_id, $field_id, $field_value);						
					} else {
						$status = $profiles_obj->addCustomProfileFields($profile_id, $field_id, $field_value);
					}
				}
			}
		}
	}
	
	unset($profile_obj);
	echo $profile_id;
	exit;
}
else if($req == 7)
{
	//Edit User
}
else if($req == 8)
{
	//Delete User - Moved to dosettings.php file
/*	
	$user_id = trim($_POST['user']);
	$users_obj = new Users($APPLICATION_PATH);
	echo $users_obj->deleteUser($user_id);
	exit;
*/
}
else if($req == 9)
{
	//Delete Profile
	
	$profile_id = trim($_POST['profile']);
	$is_profile_head = trim($_POST['isProfileHead']);
	$profiles_obj = new Profiles($APPLICATION_PATH);
	echo $profiles_obj->deleteProfile($profile_id, $is_profile_head);
	exit;
}
else if($req == 10)
{
	//show profile information

	$profile_id = trim($_POST['profile']);
	//$profiles_obj = new Profiles($APPLICATION_PATH);
	//$profile_details = $profiles_obj->getProfileInformation($profile_id);

	$to_return = '';
	$to_return .= '<div class="tabbable">';
		$to_return .= '<ul class="nav nav-tabs">';
			$to_return .= '<li id="profileTab" class="active" onclick="showProfileSummary('.$profile_id.')"><a href="#profileTab" data-toggle="tab">Summary</a></li>';
			$to_return .= '<li id="subscriptionTab" onclick="listAllSubscriptions('.$profile_id.');"><a href="#subscriptionTab" data-toggle="tab">Subscriptions</a></li>';
			$to_return .= '<li id="harvestTab" onclick="listAllHarvests('.$profile_id.');"><a href="#harvestTab" data-toggle="tab">Harvest Festival</a></li>';
		$to_return .= '</ul>';
		$to_return .= '<div class="tab-content">';
			$to_return .= '<div class="tab-pane active" id="profileDiv">';
			$to_return .= '</div>';
			$to_return .= '<div class="tab-pane" id="subscriptionDiv">';
			$to_return .= '</div>';
			$to_return .= '<div class="tab-pane" id="harvestDiv">';
			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	echo $to_return;
	exit;
}
else if($req == 11)
{
	include_once $APPLICATION_PATH . '/classes/class.subscription.php';
	include_once $APPLICATION_PATH . '/classes/class.harvest.php';
	include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';

	$profile_id = trim($_POST['profile']);
	$profiles_obj = new Profiles($APPLICATION_PATH);
	$profile_details = $profiles_obj->getProfileInformation($profile_id);
	//print_r($profile_details);

	$family_photo_path = $profile_details[29];
	$profile_photo_path = $profile_details[30];

	$is_parent_profile = (($profile_details[25] == -1)?true:false);
	$unique_id = 'STC'.appendZeroInUniqueID($profile_details[3]);

	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	$salutation = $settings_obj->getOptionValue(1, $profile_details[1]);

	$sub_obj = new Subscription($APPLICATION_PATH);
	$subscription_field_id = 4;
	$total_paid_amt = $sub_obj->getProfileSubscriptionTotalAmount($profile_id, $subscription_field_id);

	$harvest_obj = new Harvest($APPLICATION_PATH);
	$total_purchased_amt = $harvest_obj->getProfileHarvestTotal($profile_id);

	if($is_parent_profile)
	{
		$settings_obj = new ProfileSettings($APPLICATION_PATH);
		$option_list = $settings_obj->getOptions($setting_id);

		$dependant_details = $profiles_obj->getProfileDependants($profile_id);

		//print_r($dependant_details);
		$is_dependant_available = false;
		$dependent_info = '';
		if(is_array($dependant_details))
		{
			$total_dependants = COUNT($dependant_details);
			if($total_dependants > 0)
			{
				$is_dependant_available = true;
				for($i=0; $i<$total_dependants; $i++)
				{
					$relation_ship = $settings_obj->getOptionValue(2, $dependant_details[$i][6]);
					$relation_ship = (($relation_ship != '')?$relation_ship:'-');
					$dependent_info .= '<a href="#" onclick="showProfileDetails('.$dependant_details[$i][0].');">'.$dependant_details[$i][2].'</a>&nbsp;<small class="muted">('.($relation_ship).')</small><BR>';
				}
			}
		}
		if(!$is_dependant_available)
		{
			$dependent_info .= 'No dependant is available';
		}
	}
	else
	{
		$parent_profile_id = $profiles_obj->getParentProfileID($profile_details[3]);
		$parent_profile_details = $profiles_obj->getProfileInformation($parent_profile_id);
		$parent_info = '<a href="#" onclick="showProfileDetails('.$parent_profile_id.');">'.$parent_profile_details[2].'</a>';
		$parent_info .= '<BR><BR>';
		$parent_info .= '<b>Actions</b>';
		$parent_info .= '<BR><a href="#" id="existFamilyHeadLink" data-toggle="tooltip" title="You can use this option to migrate this profile as family head if the family head was inactive/expired." onclick="migrateProfile(1, '.$profile_id.', '.$parent_profile_id.','.$parent_profile_details[19].');">Migrate this profile as head of this family</a>';
		$parent_info .= '<BR><a href="#" id="newFamilyHeadLink" data-toggle="tooltip" title="You can use this option to migrate this profile into new family head if he/she separated from this family." onclick="migrateProfile(2, '.$profile_id.', '.$parent_profile_id.','.$parent_profile_details[19].');">Migrate this profile into new family head</a>';
	}	
	
	$to_return = '';
	$to_return .= '<div class="row-fluid">';
		$to_return .= '<div class="span2">';
			$to_return .= '<img id="imgPreviewFamilyPhoto" src="'.(($profile_photo_path != '')?substr($profile_photo_path, 3):'photo_rounded.png').'" class="img-rounded" width="120" height="120">';
			$to_return .= '<BR>';
			//$to_return .= '<span style="text-align:center"><a href="#"><input type="file" id="filePath" name="filePath"/><small>'.(($profile_photo_path == '')?'Add Photo':'Change Photo').'</small></a></span>';
			$to_return .= '<form id="profilePhotoForm" action="server/doserver.php?req=15" method="post" enctype="multipart/form-data">';
			$to_return .= '<input type="file" id="profilePhotoPath" name="profilePhotoPath" style="visibility: hidden; width: 1px; height: 1px" multiple />
<a href="" onclick="document.getElementById(\'profilePhotoPath\').click(); return false">'.(($profile_photo_path == '')?'Add Photo':'Change Photo').'</a>';
			$to_return .= '<input type="hidden" name="profileID" id="profileID" value="'.$profile_id.'" /><BR>';
			$to_return .= '<span id="spanImportBtn"><button class="btn btn-success btn-small" type="submit">Upload</button></span>';
			$to_return .= '</form>';
			$to_return .= '<BR><BR>';
		$to_return .= '</div>';
		$to_return .= '<div class="span4">';
			$to_return .= '<address>';
				$to_return .= '<strong>'.$salutation.". ".$profile_details[2].'</strong>&nbsp;<span class="muted">('.$unique_id.')</span>&nbsp;<a href="#" onclick="getAddOrEditProfileForm(1, '.$profile_details[0].')"><u>Edit</u></a><br>';
				if($profile_details[10] != "") {
					$to_return .= $profile_details[10].'<br>';
				}
				if($profile_details[11] != "") {
					$to_return .= $profile_details[11].'<br>';
				}
				if($profile_details[12] != "") {
					$to_return .= $profile_details[12].'<br>';
				}
				if($profile_details[13] != "") {
					$to_return .= $profile_details[13];
				}
				if($profile_details[14] != "") {
					if($profile_details[13] != "") {
						$to_return .= ', ';
					}
					$to_return .= $profile_details[14].'<br>';
				}
				if($profile_details[15] != "") {
					$to_return .= '<abbr title="Phone">PH:</abbr> '.$profile_details[15].'<br>';
				}
				if($profile_details[16] != "") {
					$to_return .= '<abbr title="Primary Mobile">M1:</abbr> '.$profile_details[16].'<br>';
				}
				if($profile_details[17] != "") {
					$to_return .= '<abbr title="Secondary Mobile">M2:</abbr> '.$profile_details[17].'<br>';
				}
				if($profile_details[18] != "") {
					$to_return .= '<a href="mailto:#">'.$profile_details[18].'</a>';
				}
			$to_return .= '</address>';
		$to_return .= '</div>';
		$to_return .= '<div class="span6">';
			$to_return .= '<strong>'.(($is_parent_profile)?"Individuals":"Family Head").'</strong>';
			if($is_parent_profile) {
				$to_return .= '&nbsp;<span class="badge badge-info">'.$total_dependants.'</span>';
			}
			$to_return .= '<BR>';
			$to_return .= (($is_parent_profile)?$dependent_info:$parent_info);
		$to_return .= '</div>';
	$to_return .= '</div>';

	$to_return .= '<div class="row-fluid">';
		$to_return .= '<div class="span12">';
			$to_return .= '<strong>Other Information</strong><BR>';

			$age = '-';
			$date_of_birth = $profile_details[4];
			if($date_of_birth != '0000-00-00') {
				$date_of_birth = formatDateOfBirth($date_of_birth);
				$dob_arr = explode('-', $profile_details[4]);
				$age = Carbon::createFromDate($dob_arr[0], $dob_arr[1], $dob_arr[2])->age;
			} else {
				$date_of_birth = '-';
			}
			$is_married = $profile_details[7];
			$marriage_date = $profile_details[8];
			if($is_married) {
				if($marriage_date != '0000-00-00')
				{
					$marriage_date = formatDateOfBirth($marriage_date);
				} else {
					$marriage_date = '-';
				}
			}

			$to_return .= 'DOB - <span class="muted">'.$date_of_birth.'</span><BR>';
			$to_return .=  'Age - <span class="muted">'.$age.'</span><BR>';
			if($is_married) {
				$to_return .= 'Marriage Date - <span class="muted">'.$marriage_date.'</span><BR>';
			}
			$to_return .= 'Baptised - <span class="muted">'.(($profile_details[21] == 1)?"Yes":(($profile_details[21] == 0)?"No":"I'm not Sure")).'</span><BR>';
			$to_return .= 'Confirmation - <span class="muted">'.(($profile_details[22] == 1)?"Yes":(($profile_details[22] == 0)?"No":"I'm not Sure")).'</span><BR>';
			$to_return .= 'Occupation - <span class="muted">'.$profile_details[23].'</span><BR>';
			$to_return .= 'Is Another Church Member - <span class="muted">'.(($profile_details[24] == 1)?"Yes":(($profile_details[24] == 0)?"No":"I'm not Sure")).'</span><BR>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	$to_return .= '<BR><div class="row-fluid">';
		$to_return .= '<div class="span12">';
			$to_return .= '<strong>Notes</strong><BR>';
//			$to_return .= '<div class="alert alert-info">';
				$to_return .= (($profile_details[20] == '')?'No Information is available':$profile_details[20]);
//			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>';
/*
	$to_return .= '<BR><div class="row-fluid">';
		$to_return .= '<div class="span12">';
			$to_return .= '<strong>Harvest Festival</strong><BR>';
			$to_return .= 'Total Purchased : '.$total_purchased_amt.'<BR>';
			$to_return .= 'Total Paid : '.$total_paid_amt.'<BR>';
			$to_return .= 'Balance : '.($total_purchased_amt - $total_paid_amt).'<BR>';
		$to_return .= '</div>';
	$to_return .= '</div>';
*/
	echo $to_return;
	exit;
}
else if($req == 12)
{
	//import profiles form

	$to_return .= '<div class="row-fluid" style="height:100px;">';
		$to_return .= '<div class="span12">';
			$to_return .= '<form id="myForm" action="import.php" method="post" enctype="multipart/form-data">';
//				$to_return .= '<fieldset>';
//					$to_return .= '<legend>Import Your Profiles</legend>';
					$to_return .= '<div class="alert alert-info"><b>Heads up!</b> Please make sure you have added the profiles related <a href="settings.php"><u>settings</u></a> before you import the profiles from here. Also, please note that Profilestack currently supports only *.xlxs format and more formats will be supported in future releases.</div>';
					$to_return .= '<div>';						
						$to_return .= '<div class="input-append span12">';
							$to_return .= '<input type="text" id="inputImportFilePath" placeholder="Browse your *.xlsx file" class="span4"/>';
							$to_return .= '<span class="btn btn-file">Browse<input type="file" id="filePath" name="filePath"/></span>';
						$to_return .= '</div>';
					$to_return .= '</div>';
					$to_return .= '<div>';
						$to_return .= '<span id="spanImportBtn"><button class="btn btn-success" type="submit">Import</button></span>';
						$to_return .= '<span style="display:none" id="spanImportProg" class="muted">Upload is in progress. Don\'t refresh the page. Please wait...</span>';
					$to_return .= '</div>';
					$to_return .= '<div class="progress" id="progress" style="display:none">';
						$to_return .= '<div class="bar" id="bar"></div><div id="percent">0%</div >';
					$to_return .= '</div>';
					$to_return .= '<div id="message" style="display:none">';
					$to_return .= '</div>';
//				$to_return .= '</fieldset>';
			$to_return .= '</form>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	echo $to_return;
	exit;
}
else if($req == 13)
{
	//start the import profile process
	
	$to_return = array();
	$filePath = trim($_POST['filePath']);
	if(file_exists($filePath))
	{

	}
	else
	{
		$to_return[0] = 1;
		$to_return[1] = 'File doesn\'t exists. Please check the file path.';
	}

	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;

}
else if($req == 14)
{
	$option = trim($_POST['option']);
	$profile_id = trim($_POST['profileID']);
	$parent_profile_id = trim($_POST['parentProfileID']);
	$parent_profile_status = trim($_POST['parentProfileStatus']);
	$status = false;

	$profiles_obj = new Profiles($APPLICATION_PATH);
	if($option == 1)
	{
		//change this profile as family head
		if($parent_profile_status != 3)
		{
			$profile_status = 3; //change parent status as expired.
			$status = $profiles_obj->updateProfileStatus($parent_profile_id, $profile_status);
		}
		$status = $profiles_obj->updateProfileParentID($profile_id, -1);
		if($status)
		{
			$status = $profiles_obj->updateDependantsProfileID($parent_profile_id, $profile_id);
		}
	}
	else
	{
		//change this profile into new family head
		$status = $profiles_obj->updateProfileParentID($profile_id, -1);
		if($status) {
			$max_unique_id = $profiles_obj->getMaxProfileUniqueID();
			if($max_unique_id >= 0)
			{
				$max_unique_id = $max_unique_id + 1;
				$status = $profiles_obj->updateProfileUniqueID($profile_id, $max_unique_id);
			}
		}
	}

	unset($profiles_obj);
	echo $status;
	exit;
}
else if($req == 15)
{
	//upload images
	$church_id = $_SESSION['churchID'];
	$profile_id = $_POST['profileID'];

	$is_family_photo = false;
	$photo_location = '../uploads/';
	if(!file_exists($photo_location)) {
		mkdir($photo_location, 0777, true);
	}
	
	if(isset($_FILES["profilePhotoPath"]))
	{
		if ($_FILES["profilePhotoPath"]["error"] > 0)
		{
			echo "Error: " . $_FILES["file"]["error"];
		}
		else
		{

			$profiles_obj = new Profiles($APPLICATION_PATH);

			//getting the previous photo location to delete it after upload the new one
			$previous_photo_location = $profiles_obj->getProfilePhotoLocation($profile_id, $is_family_photo);

			//delete the old photo location from file system if exists
			if($previous_photo_location != '') {
				unlink($previous_photo_location);
			}
			
			//Get uploaded file extension
			$ext = pathinfo($_FILES["profilePhotoPath"]["name"], PATHINFO_EXTENSION);

			//construct new photo location
			$photo_location = $photo_location. 'image_'. $profile_id. '.' . $ext;

			echo 'photo_location:'.$photo_location;

			//move the uploaded file to uploads folder;
			move_uploaded_file($_FILES["profilePhotoPath"]["tmp_name"], $photo_location);
			
			//update the new photo location in database
			$profiles_obj->updateProfilePhotoLocation($profile_id, $is_family_photo, $photo_location);			

			echo 'Uploaded successfully.';
		}
	}
}
?>