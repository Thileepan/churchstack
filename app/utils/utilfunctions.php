<?php

function formatDateOfBirth($dob, $skip_year=false, $show_month_as_number_format=false)
{
	$monthArr = array(1=>'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	if($dob !== '' && $dob !== '0000-00-00')
	{
		$dob_arr = explode("-", $dob);
		if($skip_year)
		{
			return $dob_arr[2]. '/' .(($show_month_as_number_format)?$dob_arr[1]:$monthArr[(int)$dob_arr[1]]);
		}
		return $dob_arr[2]. '/' .(($show_month_as_number_format)?$dob_arr[1]:$monthArr[(int)$dob_arr[1]]). '/' .$dob_arr[0];
	}
	return '-';
}

function appendZeroInUniqueID($unique_id)
{
	$id_length = strlen($unique_id);
	if($id_length < 4)
	{
		if($id_length == 1) {
			$to_append = '000';
		} else if($id_length == 2) {
			$to_append = '00';
		} else if($id_length == 3) {
			$to_append = '0';
		}

		$unique_id = $to_append.$unique_id;
	}
	return $unique_id;
}

function clearSession($APPLICATION_PATH)
{
	session_start();
	foreach($_SESSION as $k => $v)
	{
		unset($k);
	}
	// Unset all of the session variables.
	$_SESSION = array();
	session_destroy();
}

function regenerateGlobalSessionSecurityTokens()
{
	$_SESSION["globalSessionSecuritySalt-1"] = md5(time().(time()+(rand(1, 1000))).rand(1,100000));
	$_SESSION["globalSessionSecuritySalt-2"] = md5(time()/(time()-(rand(1, 1000))).rand(1,100000));
	$_SESSION["globalSessionSecurityToken"] = strtoupper(md5(md5($_SESSION["globalSessionSecuritySalt-1"]).md5($_SESSION["globalSessionSecuritySalt-1"])));
}

function convertRailwayTimeToFullTime($time)
{
	if(strlen($time) > 3) {
		$hour = substr($time, 0, 2);
		$min = substr($time, 2, 2);
	} else {
		$hour = "0".substr($time, 0, 1);
		$min = substr($time, 1, 2);
	}

	return $hour.":".$min.":00";
}

function isImportFieldMatched($text1, $text2)
{
	$text1_arr = explode(' ', strtolower($text1));
	$text2_arr = explode(' ', strtolower($text2));
	$result = array_intersect($text1_arr, $text2_arr);
	if(COUNT($result) > 0) {
		return true;
	}
	return false;
	
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

?>