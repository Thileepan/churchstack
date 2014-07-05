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
	if($id_length < 3)
	{
		if($id_length == 1) {
			$to_append = '00';
		} else if($id_length == 2) {
			$to_append = '0';
		}
		$unique_id = $to_append.$unique_id;
	}
	return $unique_id;
}

?>