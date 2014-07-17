<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include_once $APPLICATION_PATH . '/classes/class.settings.php';
include_once $APPLICATION_PATH . '/classes/class.users.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	//list options
	$setting_id = $_REQUEST['opt'];

	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	$option_list = $settings_obj->getOptions($setting_id);
	//print_r($option_list);

	$to_return .= '<div id="divAddOptionForm" style="display:none">';
		$to_return .= '<fieldset>';
			$to_return .= '<legend>Add New Option</legend>';
			$to_return .= '<input type="text" id="inputAddOptionValue" placeholder="Option Name"><BR>';
			$to_return .= '<button type="submit" class="btn btn-primary" onclick="addOrUpdateOption(0);">Add</button>&nbsp;';
			$to_return .= '<button type="submit" class="btn" onclick="hideAddOptionForm();">Cancel</button>';
		$to_return .= '</fieldset>';
	$to_return .= '</div>';

	$to_return .= '<div class="pull-right" id="divAddOptionBtn">';
		$to_return .= '<button class="btn btn-small btn-primary" type="button" onclick="showAddOptionForm();">Add New option</button>';
	$to_return .= '</div>';
	$to_return .= '<BR>';
	$to_return .= '<table id="profileSettings" class="table bordered-table table-striped">';
		$to_return .= '<thead>';
			$to_return .= '<tr>';
				$to_return .= '<th>ID</th>';
				$to_return .= '<th>Value</th>';
				$to_return .= '<th>Actions</th>';
			$to_return .= '</tr>';
		$to_return .= '</thead>';
		$to_return .= '<tbody>';

	$is_options_available = false;
	if(is_array($option_list))
	{
		$total_option = COUNT($option_list);
		if($total_option > 0)
		{
			$is_options_available = true;
			for($i=0; $i<$total_option; $i++)
			{
				$to_return .= '<tr>';						
					$to_return .= '<td>'.$option_list[$i][0].'</td>';
					$to_return .= '<td>';
						$to_return .= '<span id="spnShowOptionValueInfo-'.$i.'">'.$option_list[$i][1].'</span>';
						$to_return .= '<span id="spnEditOptionValueInfo-'.$i.'" style="display:none"><input type="hidden" value="'.$option_list[$i][0].'" id="inputEditOptionID-'.$i.'" /><input type="text" value="'.$option_list[$i][1].'" id="inputEditOptionValue-'.$i.'" /></span>';
					$to_return .= '</td>';
					$to_return .= '<td>';
						$to_return .= '<span id="spnActionInfo-'.$i.'"><i class="curHand icon-pencil" onclick="showEditOptionInfoRow('.$i.');"></i>&nbsp;&nbsp;<i class="curHand icon-trash" onclick="deleteOption('.$i.');"></i></span>';
						$to_return .= '<span id="spnSaveButton-'.$i.'" style="display:none"><button class="btn btn-small btn-success" onclick="addOrUpdateOption(1);">Save</button>&nbsp;<button class="btn btn-small" onclick="hideEditOptionInfoRow('.$i.');">Cancel</button></span>';
					$to_return .= '</td>';
				$to_return .= '</tr>';
			}
		}
	}

	if( !$is_options_available )
	{
		$to_return .= '<tr>';
			$to_return .= '<td colspan="3" align="center">'.'No profile settings available'.'</td>';
		$to_return .= '</tr>';
	}
		$to_return .= '<tr style="display:none"><td colspan="3"><input type="hidden" value="" id="hiddenLastEditedRow" /><input type="hidden" value="'.$setting_id.'" id="hiddenSettingID" /></td></tr>';
		$to_return .= '</tbody>';
	$to_return .= '</table>';

	echo $to_return;
	exit;
}
else if($req == 2)
{
	//add/update new option

	$option_value = trim($_POST['optionValue']);
	$option_id = trim($_POST['optionID']);
	$is_update = trim($_POST['isUpdate']);
	$setting_id = trim($_POST['settingID']);

	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	
	if($is_update) {
		$status = $settings_obj->updateOption($setting_id, $option_id, $option_value);		
	} else {
		$status = $settings_obj->addNewOption($setting_id, $option_value);
	}
	echo $status;
	exit;
}
else if($req == 3)
{
	//delete option value

	$setting_id = trim($_POST['settingID']);
	$option_id = trim($_POST['optionID']);

	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	echo $settings_obj->deleteOption($setting_id, $option_id);
	exit;
}
else if($req == 4)
{
	//Add/Edit New User Form
	$is_update = trim($_POST['isEdit']);
	$user_status_list = array('Active', 'Inactive');

	if($is_update) {
		$user_id = trim($_POST['userID']);
		$users_obj = new Users($APPLICATION_PATH);
		$user_info = $users_obj->getUserInformation($user_id);
	}

	$toReturn = '';
	$toReturn .= '<form class="form-horizontal" onsubmit="return false;">';
			$toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputUser">UserName</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="text" id="inputUser" placeholder="UserName" value="'.$user_info[2].'" '.(($is_update)?"disabled":"").'>';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputPwd">Password</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="password" id="inputPwd" placeholder="Password">';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputCPwd">Confirm Password</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="password" id="inputCPwd" placeholder="Confirm Password">';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputUserStatus">Status</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<select id="inputUserStatus">';
						for($i=0; $i<2; $i++) {
							$toReturn .= '<option value="'.($i+1).'" '.(($user_info[5] == ($i+1))?"selected":"").'>'.$user_status_list[$i].'</option>';
						}
					$toReturn .= '</select>';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="form-actions">';
				$toReturn .= '<button class="btn btn-primary" data-loading-text="Loading..." onclick="addOrUpdateUser('.$is_update.');">'.(($is_update)?'Update':'Add User').'</button>&nbsp;';
				if(!$is_update) {
					$toReturn .= '<button class="btn" type="reset">Reset</button>';
				}
				$toReturn .= '<input type="hidden" id="hiddenUserID" value="'.$user_id.'">';
				$toReturn .= '<input type="hidden" id="hiddenUserName" value="'.$user_info[2].'">';
		  $toReturn .= '</div>';
	$toReturn .= '</form>';

	echo $toReturn; exit;
}
else if($req == 5)
{
	session_start();
	//List User
	$users_obj = new Users($APPLICATION_PATH);
	$users = $users_obj->getAllUsers();

	$to_return .= '<table class="table table-striped">';
		$to_return .= '<thead>';
			$to_return .= '<tr>';
				$to_return .= '<th>UserName</th>';
				$to_return .= '<th>Role</th>';
				$to_return .= '<th>Status</th>';
				$to_return .= '<th>Actions</th>';
			$to_return .= '</tr>';
		$to_return .= '</thead>';
		$to_return .= '<tbody>';

	$is_users_available = false;
	if(is_array($users))
	{
		$user_count = COUNT($users);
		if($user_count > 0)
		{
			$is_users_available = true;
			for($i=0; $i<$user_count; $i++) {
				$to_return .= '<tr>';
					$to_return .= '<td>'.$users[$i][2].'</td>';
					$to_return .= '<td>'.(($users[$i][3] == 1)?'Administrator':'-').'</td>';
					if($users[$i][5] == 1) {
						$spn_class = 'label label-success';
						$spn_text = 'Active';
						if($_SESSION['userID'] == $users[$i][0]) {
							$spn_text = 'Logged In';
						}
					} else {
						$spn_class = 'label label-important';
						$spn_text = 'Inactive';
					}
					$to_return .= '<td><span class="'.$spn_class.'">'.$spn_text.'</span></td>';
					$to_return .= '<td>';
						$to_return .= '<div class="dropdown">';
							$to_return .= '<i class="curHand icon-pencil" onclick="GetAddOrEditUserForm(1, '.$users[$i][0].');"></i>&nbsp;&nbsp;';
							if($_SESSION['userID'] != $users[$i][0]) {
								$to_return .= '<i class="curHand icon-trash" onclick="deleteUserConfirmation('.$users[$i][0].', \''.$users[$i][2].'\');"></i>&nbsp;&nbsp;';
							}
						$to_return .= '</div>';
					$to_return .= '</td>';
				$to_return .= '</tr>';
			}
		}
	}
	
	if( !$is_users_available )
	{
		$to_return .= '<tr>';
			$to_return .= '<td colspan="4" align="center">'.'No users available'.'</td>';
		$to_return .= '</tr>';
	}
		$to_return .= '</tbody>';
	$to_return .= '</table>';
	echo $to_return;
	exit;
}
else if($req == 6)
{
	//Add/Edit User
	session_start();
	$user_name = trim($_POST['userName']);
	$password = md5(trim($_POST['password']));
	$is_update = trim($_POST['isUpdate']);
	$prev_user = trim($_POST['prevUser']);
	$user_status = trim($_POST['userStatus']);
	$church_id = $_SESSION['churchID'];
	$role_id = 1;
	
	$users_obj = new Users($APPLICATION_PATH);

	$check_user_exists = true;
	if($is_update && $prev_user === $user_name) {
		$check_user_exists = false;
	}

	if($check_user_exists) {
		if($users_obj->isUserAlreadyExists($user_name, $user_name)) {
			echo 1;
			exit;
		}
	}

	if($is_update) {
		$user_id = trim($_POST['userID']);
		$is_updated = $users_obj->updateUser($user_id, $user_name, $password, $role_id, $user_status);
	} else {
		$is_updated = $users_obj->addNewUser($church_id, $user_name, $user_name, $password, $role_id, $user_status);
	}
	if($is_updated)
		echo 2;
	else
		echo 3;

	exit;
}
else if($req == 7)
{
	//Delete User
	
	session_start();
	$user_id = trim($_POST['user']);
	if($user_id == $_SESSION['userID']) {
		echo 2; exit; //logged in user can't be delete.
	}
	$users_obj = new Users($APPLICATION_PATH);
	echo $users_obj->deleteUser($user_id);
	exit;
}
else if($req == 8)
{
	//list profile custom fields
	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	$field_details = $settings_obj->getAllCustomProfileFields();

	$field_type = array(1=>'Text', 'Number', 'Password', 'Date', 'Link/URL', 'Drop Down', 'Tick Box', 'Text Area');
	
	$to_return .= '<div class="pull-right" id="divAddOptionBtn">';
		$to_return .= '<button class="btn btn-small btn-primary" type="button" onclick="GetAddOrEditCustomFieldForm(0);">Add New Custom Field</button>';
	$to_return .= '</div>';
	$to_return .= '<BR>';
	$to_return .= '<table class="table table-striped">';
		$to_return .= '<thead>';
			$to_return .= '<tr>';
				$to_return .= '<th>Name</th>';
				$to_return .= '<th>Type</th>';
				$to_return .= '<th>Is Required</th>';
//				$to_return .= '<th>Validation Regex</th>';
				$to_return .= '<th>Actions</th>';
			$to_return .= '</tr>';
		$to_return .= '</thead>';
		$to_return .= '<tbody>';

	$is_fields_available = false;
	if(is_array($field_details))
	{
		$field_count = COUNT($field_details);
		if($field_count > 0)
		{
			$is_fields_available = true;
			for($i=0; $i<$field_count; $i++) {
				$to_return .= '<tr>';
					$to_return .= '<td>'.$field_details[$i][1].'</td>';
					$to_return .= '<td>'.$field_type[$field_details[$i][2]].'</td>';
					$to_return .= '<td>'.(($field_details[$i][5] == 1)?"Yes":"No").'</td>';
//					$to_return .= '<td>'.$field_details[$i][6].'</td>';
					$to_return .= '<td>';
						$to_return .= '<div class="dropdown">';
							$to_return .= '<i class="curHand icon-pencil" onclick="GetAddOrEditCustomFieldForm(1, '.$field_details[$i][0].');"></i>&nbsp;&nbsp;';
							$to_return .= '<i class="curHand icon-trash" onclick="deleteCustomFieldConfirmation('.$field_details[$i][0].', \''.$field_details[$i][1].'\');"></i>&nbsp;&nbsp;';
						$to_return .= '</div>';
					$to_return .= '</td>';
				$to_return .= '</tr>';
			}
		}
	}
	
	if( !$is_fields_available )
	{
		$to_return .= '<tr>';
			$to_return .= '<td colspan="5" align="center">'.'No custom profile fields available'.'</td>';
		$to_return .= '</tr>';
	}
		$to_return .= '</tbody>';
	$to_return .= '</table>';
	echo $to_return;
	exit;
}
else if($req == 9)
{
	//get add/edit profile custom field form

	$field_type = array(1=>'Text', 'Number', 'Password', 'Date', 'Link/URL', 'Drop Down', 'Tick Box', 'Text Area');
	$is_update = trim($_POST['isEdit']);
	$field_id = trim($_POST['fieldID']);

	if($is_update) {
		$settings_obj = new ProfileSettings($APPLICATION_PATH);
		$field_details = $settings_obj->getProfileCustomFieldDetails($field_id);
		$field_id = $field_details[0];
		$field_name = $field_details[1];
		$field_type_value = $field_details[2];
		$field_options = $field_details[3];
		$field_help_message = $field_details[4];
		$is_required = $field_details[5];
		$validation_string = $field_details[6];
		$display_order = $field_details[7];
	}

	$toReturn = '';
	$toReturn .= '<form class="form-horizontal" onsubmit="return false;">';
			$toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputFieldName">Field Name</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="text" id="inputFieldName" placeholder="Field Name" value="'.$field_name.'">';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputFieldType">Field Type</label>';
				$toReturn .= '<div class="controls">';
					if(!$is_update)
					{
						$toReturn .= '<select id="inputFieldType" onchange="showOrHideFieldOptions(this);">';
						for($i=1; $i<=COUNT($field_type); $i++)
						{
							$toReturn .= '<option value="'.$i.'" '.(($field_type_value == $i)?"selected":"").'>'.$field_type[$i].'</option>';
						}
						$toReturn .= '</select>';
					}
					else
					{
						$toReturn .= '<b>'.$field_type[$field_type_value].'</b>';
						$toReturn .= '<input type="hidden" id="hiddenFieldType" value="'.$field_type_value.'" />';
					}
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div id="divFieldOptions" class="control-group" style="display:none">';
				$toReturn .= '<label class="control-label" for="inputFieldOptions">Field Options</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="text" id="inputFieldOptions" placeholder="Field Options" value="'.$field_options.'"><BR>';
					$toReturn .= '<span class="muted">For Dropdowns Only - Comma Seperated List</span>';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="control-group" style="display:none;">';
				$toReturn .= '<label class="control-label" for="inputValidationString">Validation</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="text" id="inputValidationString" placeholder="Regular Expression" value="'.$validation_string.'">';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="control-group">';
				$toReturn .= '<label class="control-label" for="inputIsRequired">Is Required</label>';
				$toReturn .= '<div class="controls">';
					$toReturn .= '<input type="checkbox" id="inputIsRequired" '.(($is_required)?"checked":"").'>';
				$toReturn .= '</div>';
		  $toReturn .= '</div>';
		  $toReturn .= '<div class="form-actions">';
				$toReturn .= '<button class="btn btn-primary" data-loading-text="Loading..." onclick="addOrUpdateCustomFields('.$is_update.');">'.(($is_update)?'Update':'Add').'</button>&nbsp;';
				if(!$is_update) {
					$toReturn .= '<button class="btn" type="reset">Reset</button>';
				}
				$toReturn .= '<input type="hidden" id="hiddenFieldID" value="'.$field_id.'">';
		  $toReturn .= '</div>';
	$toReturn .= '</form>';

	echo $toReturn; exit;
}
else if($req == 10)
{
	//add/edit profile custom field values

	$is_update = trim($_POST['isUpdate']);
	$field_id = trim($_POST['fieldID']);
	$field_name = trim($_POST['fieldName']);
	$field_type = trim($_POST['fieldType']);
	$field_options = trim($_POST['fieldOptions']);
	$field_help_message = trim($_POST['fieldHelpMsg']);
	$is_required = trim($_POST['isRequired']);
	$validation_string = trim($_POST['validationString']);
	$display_order = trim($_POST['displayOrder']);	

	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	if($is_update) {
		$is_updated = $settings_obj->updateCustomField($field_id, $field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order);
	} else {
		$is_updated = $settings_obj->addNewCustomField($field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order);
	}

	echo $is_updated;
	exit;
}
else if($req == 11)
{
	//Delete Custom Field
	
	$field_id = trim($_POST['fieldID']);
	$settings_obj = new ProfileSettings($APPLICATION_PATH);
	echo $settings_obj->deleteCustomField($field_id);
	exit;
}
?>