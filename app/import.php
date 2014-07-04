<?php

$APPLICATION_PATH = '';
//Usage: To Import profiles from xls/xlsx.

/*************** upload starts ***************/
//uploading file to uploads/ folder before start importing
$upload_dir = "uploads/";
if(isset($_FILES["filePath"]))
{
	//Filter the file types , if you want.
	if ($_FILES["filePath"]["error"] > 0)
	{
	  echo "Error: " . $_FILES["file"]["error"] . "<br>";
	}
	else
	{
		//move the uploaded file to uploads folder;
		//$file_path = $upload_dir. $_FILES["filePath"]["name"];
		$file_path = $upload_dir. "profilelist.xlsx";
    	move_uploaded_file($_FILES["filePath"]["tmp_name"], $file_path);
		echo "Uploaded File :".$_FILES["filePath"]["name"];
	}
}
/*************** upload completes ***************/

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
set_time_limit(0);
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Asia/Calcutta');

/** PHPExcel_IOFactory */
require_once 'plugins/PHPExcel/Classes/PHPExcel/IOFactory.php';
require_once 'plugins/PHPExcel/Classes/PHPExcel/Cell/AdvancedValueBinder.php';

if (!file_exists($file_path)) {
	exit("Uploaded file doesn't exists." . EOL);
}

//initialize the profile class
include_once $APPLICATION_PATH . 'classes/class.profiles.php';
$profiles_obj = new Profiles($APPLICATION_PATH);

//initialize the settings class
include_once $APPLICATION_PATH . 'classes/class.settings.php';
$settings_obj = new ProfileSettings($APPLICATION_PATH);

//salution list
$salution_list = $settings_obj->getOptions(1);
//print_r($salution_list);

//relationship list
$relationship_list = $settings_obj->getOptions(2);

//marital list
$marital_list = $settings_obj->getOptions(3);

//profile status list
$profile_status_list = $settings_obj->getOptions(4);

if(is_array($salution_list) && COUNT($salution_list) > 0 || is_array($relationship_list) && COUNT($relationship_list) > 0 || is_array($marital_list) && COUNT($marital_list) > 0 || is_array($profile_status_list) && COUNT($profile_status_list) > 0)
{
	$objReader = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("uploads/profilelist.xlsx");
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) 
	{
		$total_profiles = 0;
		$success_profiles = 0;
		$failure_profiles = 0;
		$failed_row_index = '';
		foreach ($worksheet->getRowIterator() as $row)
		{
			$proceed = true;
			//echo '    Row number - ' , $row->getRowIndex() , EOL;
			if($row->getRowIndex() == 1) {// || $row->getRowIndex() == 2) {
				continue;
			}
			$total_profiles++;

			$cellIterator = $row->getCellIterator();
			$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
			
			$profile_info = array();
			foreach ($cellIterator as $cell)
			{
				if (!is_null($cell)) {
					//echo '        Cell - ' , $cell->getCoordinate() , ' - ' , $cell->getCalculatedValue() , EOL;

					$profile_info[] = $cell->getCalculatedValue();
				}
			}

			$unique_id = $profile_info[0];
			foreach($salution_list as $item_row => $item_value)
			{
				if(strtolower($profile_info[1]) == strtolower($item_value[1]))
				{
					$salutation_id = $item_value[0];
					break;
				}
			}
			$name = $profile_info[3];

			/* converting date of birth to readable format */
			$date_of_birth = '0000-00-00';
			$excel_date = $profile_info[14];
			if($excel_date != '')
			{
				$unix_date = ($excel_date - 25569) * 86400;
				$excel_date = 25569 + ($unix_date / 86400);
				$unix_date = ($excel_date - 25569) * 86400;
				$date_of_birth = gmdate("Y-m-d", $unix_date);
			}
			/**/

			$gender_id = ((strtolower($profile_info[12]) == 'male')?1:((strtolower($profile_info[12]) == 'female')?2:-1));
			$relation_ship_id = -1;
			$is_parent = false;
			foreach($relationship_list as $item_row => $item_value)
			{
				if(strtolower($profile_info[13]) == strtolower($item_value[1]))
				{
					if(strtolower($profile_info[13]) == 'self')
					{
						$is_parent = true;
					}
					$relation_ship_id = $item_value[0];
					break;
				}
			}
			$marital_status_id = -1;
			foreach($marital_list as $item_row => $item_value)
			{
				if(strtolower($profile_info[19]) == strtolower($item_value[1]))
				{
					$marital_status_id = $item_value[0];
					break;
				}
			}
			
			/* converting marriage date to readable format */
			$marriage_date = '0000-00-00';
			$excel_date = $profile_info[20];
			if($excel_date != '')
			{
				$unix_date = ($excel_date - 25569) * 86400;
				$excel_date = 25569 + ($unix_date / 86400);
				$unix_date = ($excel_date - 25569) * 86400;
				$marriage_date = gmdate("Y-m-d", $unix_date);
			}
			/**/

			$address1 = (($profile_info[4] != '')?$profile_info[4]:'');
			$address2 = (($profile_info[5] != '')?$profile_info[5]:'');
			$address3 = (($profile_info[6] != '')?$profile_info[6]:'');
			$area = (($profile_info[7] != '')?$profile_info[7]:'');
			$pincode = (($profile_info[8] != '')?$profile_info[8]:'');
			$landline = (($profile_info[9] != '')?$profile_info[9]:'');
			$mobile1 = (($profile_info[10] != '')?$profile_info[10]:'');
			$mobile2 = '';
			$notes = (($profile_info[25] != '')?$profile_info[25]:'');
			$profile_status_id = 1;
			if(!$is_parent) {
				$parent_profile_id = $profiles_obj->getParentProfileID($unique_id);
				if($parent_profile_id == 0) {
					$proceed = false;
				}
			} else {
				$parent_profile_id = -1;
			}
			$email = (($profile_info[11] != '')?$profile_info[11]:'');
			$is_babtised = ((strtolower($profile_info[16]) == 'yes')?1:((strtolower($profile_info[16]) == 'no')?0:-1));
			$is_confirmed = ((strtolower($profile_info[17]) == 'yes')?1:((strtolower($profile_info[17]) == 'no')?0:-1));
			$marriage_place = (($profile_info[22] != '')?$profile_info[22]:'');
			$occupation = (($profile_info[23] != '')?$profile_info[23]:'');
			$is_another_church_member = ((strtolower($profile_info[24]) == 'yes')?1:((strtolower($profile_info[24]) == 'no')?0:-1));
			
			$is_failed = false;
			if($proceed)
			{
				$status = $profiles_obj->addNewProfile($salutation_id, $name, $parent_profile_id, $unique_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member);
				if($status) {
					$success_profiles++;
				} else {
					$is_failed = true;
					$failure_profiles++;
				}
			}
			else
			{
				$is_failed = true;
				$failure_profiles++;
			}
			
			if($is_failed)
			{
				if(strlen($failed_row_index) > 0)
				{
					$failed_row_index .= PHP_EOL;
				}
				$failed_row_index .= $row->getRowIndex();
			}
		}
	}
	// Echo memory peak usage
	echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;
	echo "Total Profiles :".$total_profiles. " Success Profiles :".$success_profiles. " Failed Profiles :". $failure_profiles."<BR>";
	if($failure_profiles > 0)
	{
		echo "Log file is available in log/log.txt";
	}

	if($failure_profiles > 0)
	{
		//log the failure profiles
		$file = $APPLICATION_PATH.'log/log.txt';
		file_put_contents($file, $failed_row_index, FILE_APPEND | LOCK_EX);
	}
}
else
{
	echo 'Something is missing';
	exit;
}
