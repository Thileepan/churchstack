<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include_once $APPLICATION_PATH . '/classes/class.groups.php';

//process request
$req = $_REQUEST['req'];

//error_reporting(E_ALL);
//ini_set("display_errors", "On");

if($req == 1)
{
	//add/edit group form
	$is_update = trim($_POST['isEdit']);
	if($is_update) {
		$group_id = trim($_POST['groupID']);
		$groups_obj = new Groups($APPLICATION_PATH);
		$group_details = $groups_obj->getGroupInformation($group_id);
		if(is_array($group_details) && COUNT($group_details) > 0)
		{
			$group_id = $group_details[0];
			$group_name = $group_details[1];
			$group_desc = $group_details[2];
		}
	}

	$to_return = '';
	$to_return .= '<div id="harvestFormDiv" class="span6">';
		$to_return .= '<form class="form-horizontal" onsubmit="return false;">';			
			$to_return .= '<div class="control-group">';
				$to_return .= '<label class="control-label" for="inputGroupName">Group Name</label>';
					$to_return .= '<div class="controls">';
						$to_return .= '<input type="text" class="span12" id="inputGroupName" value="'.$group_name.'" placeholder="Group Name">';
					$to_return .= '</div>';
			$to_return .= '</div>';
			$to_return .= '<div class="control-group">';
				$to_return .= '<label class="control-label" for="inputGroupDesc">Group Description</label>';
					$to_return .= '<div class="controls">';
						$to_return .= '<textarea rows="3" class="span12" id="inputGroupDesc" placeholder="Group Description">'.$group_desc.'</textarea>';
					$to_return .= '</div>';
			$to_return .= '</div>';
			$to_return .= '<div class="form-actions">';
				$to_return .= '<button class="btn btn-primary" onclick="addOrUpdateGroup('.$is_update.');">'.(($is_update)?'Update Group':'Add Group').'</button>&nbsp;';
				$to_return .= '<button class="btn" type="reset">Reset</button>';
				$to_return .= '<input type="hidden" id="hidInputGroupID" value="'.$group_id.'" />';
			$to_return .= '</div>';
		$to_return .= '</form>';
	$to_return .= '</div>';

	echo $to_return;
	exit;
}
else if($req == 2)
{
	//add or update group

	$is_update = trim($_POST['isEdit']);
	$group_name = trim($_POST['name']);
	$desc = trim($_POST['desc']);
	$group_id = trim($_POST['groupID']);

	$group_obj = new Groups($APPLICATION_PATH);
	if(!$is_update) {
		$status = $group_obj->addGroup($group_name, $desc);
	} else {
		$status = $group_obj->updateGroup($group_id, $group_name, $desc);
	}

	echo $status;
	exit;
}
else if($req == 3)
{
	//list all groups

	$group_obj = new Groups($APPLICATION_PATH);
	$group_details = $group_obj->getListOfGroups();
	//print_r($group_details);

	$is_results_available = false;
	if(is_array($group_details))
	{
		$total_groups = COUNT($group_details);
		if($total_groups > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_groups; $i++) {
				
				$actions = '<div class="dropdown">';
					$actions .= '<i class="curHand icon-user" onclick="showAddGroupMemberScreen('.$group_details[$i][0].')" title="Add Member"></i>&nbsp;&nbsp;';
					$actions .= '<i class="curHand icon-pencil" onclick="getAddOrEditGroupForm(1,'.$group_details[$i][0].')" title="Edit Group"></i>&nbsp;&nbsp;';
					$actions .= '<i class="curHand icon-trash" onclick="deleteGroupConfirmation('.$group_details[$i][0].',\''.$group_details[$i][1].'\')" title="Delete Group"></i>&nbsp;&nbsp;';
				$actions .= '</div>';
				
				$to_return['aaData'][] = array('<a href="#" onclick="listAllGroupMembers('.$group_details[$i][0].');">'.$group_details[$i][1].'</a>', $group_details[$i][2], $actions);
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
else if($req == 4)
{
	//delete group

	$group_id = trim($_POST['groupID']);
	
	$group_obj = new Groups($APPLICATION_PATH);
	$status = $group_obj->deleteGroup($group_id);
	
	echo $status;
	exit;
}
else if($req == 5)
{
	//add members to group
	
	$group_id = trim($_POST['groupID']);
	$profile_id_list = explode(",", trim($_POST['profileID']));
	
	$group_obj = new Groups($APPLICATION_PATH);
	$status = $group_obj->addGroupMembers($group_id, $profile_id_list);
	echo $status;
	exit;
}
else if($req == 6)
{
	//list all member of the group

	$group_id = trim($_POST['groupID']);
	
	$group_obj = new Groups($APPLICATION_PATH);
	$group_members = $group_obj->getListOfGroupMembers($group_id);

	$is_results_available = false;
	if(is_array($group_members))
	{
		$total_members = COUNT($group_members);
		if($total_members > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_members; $i++) {
				
				$actions = '<div class="dropdown">';
					$actions .= '<i class="curHand icon-user" onclick="showAddGroupMemberScreen('.$group_members[$i][0].')" title="Add Member"></i>&nbsp;&nbsp;';
					$actions .= '<i class="curHand icon-pencil" onclick="getAddOrEditGroupForm(1,'.$group_members[$i][0].')" title="Edit Group"></i>&nbsp;&nbsp;';
					$actions .= '<i class="curHand icon-trash" onclick="deleteGroupConfirmation('.$group_members[$i][0].',\''.$group_members[$i][1].'\')" title="Delete Group"></i>&nbsp;&nbsp;';
				$actions .= '</div>';
				
				$to_return['aaData'][] = array(($i+1), $group_members[$i][0], $group_members[$i][1], $group_members[$i][2], $actions);
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