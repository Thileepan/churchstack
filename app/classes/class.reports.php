<?php
//class to handle reports usage

class Reports
{
	protected $db_conn;
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		include_once($this->APPLICATION_PATH . 'utils/utilfunctions.php');
		include_once($this->APPLICATION_PATH . 'classes/class.settings.php');
		include_once($this->APPLICATION_PATH . 'classes/class.profiles.php');
		include_once($this->APPLICATION_PATH . 'classes/class.subscription.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
	}

	private function addReportRule()
	{
	}

	private function addReportColumn()
	{
	}

	public function addReportDetails($title, $description, $report_rules, $report_columns)
	{
		if($this->db_conn)
		{
			$query = 'insert into REPORTS (TITLE, DESCRIPTION) values (?, ?)';
			$result = $this->db_conn->Execute($query, array($setting_id, $option_id, $option_value));

			$this->addReportRule($report_rules);
			$this->addReportColumn($report_columns);
		}
	}

	public function generateReports($report_rules, $report_columns, $dateIgnoreYearValues, $include_inactive_profile, $req_from, $arrayCustomFieldIDs, $arrayCustomFieldTypes, $arrayCustomFieldTextboxContains, $arrayCustomFieldNumberSelFilterValue, $arrayCustomFieldNumberValue, $arrayCustomFieldDateFrom, $arrayCustomFieldDateTo, $arrayCustomFieldDateIgnoreYear, $arrayCustomFieldURLContains, $arrayCustomFieldDropboxValue, $arrayCustomFieldTickboxValue, $arrayCustomFieldTextAreaContains, $arraySelectedCusFieldColumnIDs, $arraySelectedCusFieldColumnNames)
	{
		include_once $this->APPLICATION_PATH . 'plugins/carbon/src/Carbon/Carbon.php';
		//print_r($report_rules);
		//print_r($report_columns);
		$query_to_execute = 'select ';
		$query_table = ' from PROFILE_DETAILS';

		$query_col = ' PROFILE_ID, SALUTATION_ID, NAME, UNIQUE_ID, DOB, GENDER, RELATION_SHIP, MARITAL_STATUS, MARRIAGE_DATE, MARRIAGE_PLACE, ADDRESS1, ADDRESS2, ADDRESS3, AREA, PINCODE, LANDLINE, MOBILE1, MOBILE2, EMAIL, PROFILE_STATUS, NOTES, BABTISED, CONFIRMATION, OCCUPATION, IS_ANOTHER_CHURCH_MEMBER, PARENT_PROFILE_ID, MIDDLE_NAME, LAST_NAME, WORK_PHONE, FAMILY_PHOTO_LOCATION, PROFILE_PHOTO_LOCATION, EMAIL_NOTIFICATION, SMS_NOTIFICATION';


//		$query_col = ' PROFILE_ID, PARENT_PROFILE_ID, SALUTATION_ID';
		$extra_column_count = 0;
		$birth_day_rule = false;
		$dob_ignore_year = 0;
		$marriage_date_rule = false;
		$marriage_date_ignore_year = 0;
		$birth_marriage_rules = false;
		foreach($report_rules as $key => $value)
		{
			if($value[0] == 'BIRTH_MARRIAGE_DATE')
			{
				$extra_column_count = $extra_column_count + 2;
				$birth_marriage_rules = true;
//				$query_col .= ', DOB, MARRIAGE_DATE';
				break;
			}
			else if($value[0] == 'BIRTH_DATE')
			{
				$extra_column_count = $extra_column_count + 1;
				$birth_day_rule = true;
//				$query_col .= ', DOB';
				break;
			}
			else if($value[0] == 'MARRIAGE_DATE')
			{
				$extra_column_count = $extra_column_count + 1;
				$marriage_date_rule = true;
//				$query_col .= ', MARRIAGE_DATE';
				break;
			}
		}
		$query_where = '';
		
		//print_r($report_columns);
		//echo "<BR>";
		/***** CONSTRUCTING COLUMNS ************/
		$column_names = array('Profile ID', 'Family Head Name', 'Profile Full Name', 'Date Of Birth', 'Gender', 'Related To Family Head As', 'Marital Status', 'Date Of Marriage', 'Place Of Marriage', 'Baptised', 'Confirmation', 'Occupation', 'Is Another Church Member', 'Full Address', 'Mobile Number', 'Home Number', 'Work Number', 'Email', 'Age', 'Profile Status', 'Notes', 'Family ID');
//		$column_names_in_db = array('UNIQUE_ID', 'NAME', 'NAME', 'DOB', 'GENDER', 'RELATION_SHIP', 'MARITAL_STATUS', 'MARRIAGE_DATE', 'MARRIAGE_PLACE', 'BABTISED', 'CONFIRMATION', 'OCCUPATION', 'IS_ANOTHER_CHURCH_MEMBER', 'ADDRESS1, ADDRESS2, ADDRESS3, AREA, PINCODE', 'LANDLINE, MOBILE1, MOBILE2', 'EMAIL', 'PROFILE_STATUS', 'NOTES', 'DOB');

		$profiles_obj = new Profiles($APPLICATION_PATH);
		$parent_list = $profiles_obj->getAllParentProfiles();
//		print_r($parent_list);
		$parent_list_available = false;
		if(is_array($parent_list) && COUNT($parent_list) > 0)
		{
			$parent_list_available = true;
		}
		
		//values
		$gender_values = array('Not Sure', 'Male', 'Female');
		$marital_values = array('Not sure', 'Single', 'Married', 'Widow', 'Widower');
		$baptised_values = array('Not sure', 'Yes', 'No');
		$confirmation_values = array('Not sure', 'Yes', 'No');
		$is_another_church_values = array('Not sure', 'Yes', 'No');
		$profile_status_values = array('Active', 'Inactive', 'Deleted');
		$settings_obj = new ProfileSettings($this->APPLICATION_PATH);
		$salutation_values = $settings_obj->getOptions(1);
		$relation_ship_values = $settings_obj->getOptions(4);
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
		$relation_ship_results = $settings_obj->getOptions(2);
		$relation_ship_values = array();
		foreach($relation_ship_results as $rel_key=>$rel_details)
		{
			$relation_ship_values[$rel_details[$rel_key][0]] = $rel_details[$rel_key][1];
		}
		//print_r($re_arranged_salutation);

		$column_names_to_display = array();
		$total_columns = COUNT($report_columns);
		if($total_columns > 0)
		{
			/** /
			foreach($report_rules as $key => $value)
			{
				if($value[0] == 'BIRTH_MARRIAGE_DATE' || $value[0] == 'BIRTH_DATE' || $value[0] == 'MARRIAGE_DATE')
				{
					$column_names_to_display[] = 'Date';
					break;
				}
			}
			/**/
			for($i=0; $i<$total_columns; $i++)
			{
				$column_name = $column_names[$report_columns[$i]];
//				if($column_name == "Family Head" || 
				$column_names_to_display[] = $column_name;
				
//				$query_col .= ',';
//				$query_col .= $column_names_in_db[$report_columns[$i]];

				//if($column_names_in_db[$report_columns[$i]] == 'DOB')
				//{
				//	$date_position = $i;
				//	$date_in_report_columns = true;
				//}
			}
			/** /
			foreach($report_rules as $key => $value)
			{
				if($value[0] == 'BIRTH_MARRIAGE_DATE')
				{
					$column_names_to_display[] = 'BirthDay';
					$column_names_to_display[] = 'Marriage';
					break;
				}
			}
			/**/
		}

		for($cfn=0; $cfn < COUNT($arraySelectedCusFieldColumnNames); $cfn++)
		{
			$column_names_to_display[] = $arraySelectedCusFieldColumnNames[$cfn];
		}
		//echo $query_col;
		/****************************************/

		/***** CONSTRUCTING WHERE CONDITION *****/

		/** /
		$query_profile_cond = '';
		$query_profile_cond .= ' where (PROFILE_STATUS=1';
		if($include_inactive_profile)
		{
			$query_profile_cond .= ' or PROFILE_STATUS=2';
		}
		$query_profile_cond .= ') ';
		/**/
		
		//print_r($report_rules);
		$birth_date_query = "";
		$marriage_date_query = "";
		$total_rules = COUNT($report_rules);
		//$add_where_condition = false;
		if($total_rules > 0)
		{
			//$add_where_condition = true;
			for($i=0; $i<$total_rules; $i++)
			{
				if((strlen($query_where) > 0) && $report_rules[$i][0] != 'BIRTH_DATE' && $report_rules[$i][0] != 'MARRIAGE_DATE') {
				//if(strlen($query_where) > 0) {
//					$query_where .= ' and ';
				//AND has been used in all the if conditions to fix some issues that happened here. Do not use 'and' here.
				}

				if($report_rules[$i][0] == 'PROFILE_STATUS')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][2] == 'ACTIVE') {
						$query_where .= ' PROFILE_STATUS = 1';
					} else if($report_rules[$i][2] == 'INACTIVE'){
						$query_where .= ' PROFILE_STATUS = 2';
					} else if($report_rules[$i][2] == 'ALL'){
						$query_where .= ' (PROFILE_STATUS=1 or PROFILE_STATUS=2)';
					} 
				}

				if($report_rules[$i][0] == 'PROFILES')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][2] == 'FAMILY_HEAD') {
						$query_where .= ' PARENT_PROFILE_ID = -1';
					} else if($report_rules[$i][2] == 'INDIVIDUAL'){
						$query_where .= ' PARENT_PROFILE_ID != -1';
					} else if($report_rules[$i][2] == 'ALL'){
						$query_where .= ' PARENT_PROFILE_ID != -1';
					}
				}

				if($report_rules[$i][0] == 'GENDER')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][2] == 'MALE') {
						$query_where .= ' GENDER = 1';
					} else if($report_rules[$i][2] == 'FEMALE') {
						$query_where .= ' GENDER = 2';
					}
				}

				if($report_rules[$i][0] == 'AGE')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][1] == 'IS_LESS_THAN')
					{
						$query_where .= ' YEAR(CURDATE()) - YEAR(DOB) < '. $report_rules[$i][2];
					}
					else if($report_rules[$i][1] == 'IS')
					{
						$query_where .= ' YEAR(CURDATE()) - YEAR(DOB) = '. $report_rules[$i][2];
					}
					if($report_rules[$i][1] == 'IS_GREATER_THAN')
					{
						$query_where .= ' YEAR(CURDATE()) - YEAR(DOB) > '. $report_rules[$i][2];
					}
				}

				if($report_rules[$i][0] == 'BIRTH_DATE')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					//$date_in_report_rules = true;
					$dates = explode(":", $report_rules[$i][2]);
					$from_date_arr = explode("/", $dates[0]);
					$to_date_arr = explode("/", $dates[1]);
					$from_date = $from_date_arr[2] . "-" .$from_date_arr[1]. "-" .$from_date_arr[0];
					$to_date = $to_date_arr[2] . "-" .$to_date_arr[1]. "-" .$to_date_arr[0];

					//$query_where .= ' DATE_FORMAT(DOB, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d") ORDER BY EXTRACT(MONTH_DAY FROM DOB) asc';
					if(trim($dateIgnoreYearValues[$i]) != "" &&  trim($dateIgnoreYearValues[$i]) == 1)
					{
						$dob_ignore_year = 1;
						if(trim($from_date) != "" && strlen(trim($from_date) > 4) &&  trim($to_date) != "" && strlen(trim($to_date) > 4)) {
							$day_of_year_from_date = date('z', strtotime($from_date));
							$day_of_year_to_date = date('z', strtotime($to_date));
							if($day_of_year_from_date <= $day_of_year_to_date) {
								$query_where .= ' DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR("'.$from_date.'") - YEAR(DOB)) YEAR)) >= DAYOFYEAR("'.$from_date.'") and DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR("'.$to_date.'") - YEAR(DOB)) YEAR)) <= DAYOFYEAR("'.$to_date.'") ';
							} else {
								$query_where .= ' DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR("'.$from_date.'") - YEAR(DOB)) YEAR)) >= DAYOFYEAR("'.$from_date.'") or DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR("'.$to_date.'") - YEAR(DOB)) YEAR)) <= DAYOFYEAR("'.$to_date.'") ';
							}
						} else if((trim($from_date) != "" && strlen(trim($from_date) > 4)) && (trim($to_date) == "" || strlen(trim($to_date) <= 4))) {
							$query_where .= ' DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR("'.$from_date.'") - YEAR(DOB)) YEAR)) >= DAYOFYEAR("'.$from_date.'") ';
						} else if((trim($from_date) == "" || strlen(trim($from_date) <= 4)) && (trim($to_date) != "" && strlen(trim($to_date) > 4))) {
							$query_where .= ' DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR("'.$to_date.'") - YEAR(DOB)) YEAR)) <= DAYOFYEAR("'.$to_date.'") ';
						}
					}
					else
					{
						$dob_ignore_year = 0;
						if(trim($from_date) != "" && strlen(trim($from_date) > 4) &&  trim($to_date) != "" && strlen(trim($to_date) > 4)) {
							$query_where .= ' DOB BETWEEN DATE("'.$from_date.'") and DATE("'.$to_date.'")';
						} else if((trim($from_date) != "" && strlen(trim($from_date) > 4)) && (trim($to_date) == "" || strlen(trim($to_date) <= 4))) {
							$query_where .= ' DOB >= DATE("'.$from_date.'") ';
						} else if((trim($from_date) == "" || strlen(trim($from_date) <= 4)) && (trim($to_date) != "" && strlen(trim($to_date) > 4))) {
							$query_where .= ' DOB <= DATE("'.$to_date.'") ';
						}
					}
				}
				
				if($report_rules[$i][0] == 'MARRIAGE_DATE')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					$dates = explode(":", $report_rules[$i][2]);
					$from_date_arr = explode("/", $dates[0]);
					$to_date_arr = explode("/", $dates[1]);
					$from_date = $from_date_arr[2] . "-" .$from_date_arr[1]. "-" .$from_date_arr[0];
					$to_date = $to_date_arr[2] . "-" .$to_date_arr[1]. "-" .$to_date_arr[0];

					//$marriage_date_query = ' DATE_FORMAT(MARRIAGE_DATE, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d") ORDER BY EXTRACT(MONTH FROM MARRIAGE_DATE) asc, EXTRACT(DAY FROM MARRIAGE_DATE) asc, UNIQUE_ID';
					if(trim($dateIgnoreYearValues[$i]) != "" &&  trim($dateIgnoreYearValues[$i]) == 1)
					{
						$marriage_date_ignore_year = 1;
						if(trim($from_date) != "" && strlen(trim($from_date) > 4) &&  trim($to_date) != "" && strlen(trim($to_date) > 4)) {
							$day_of_year_from_date = date('z', strtotime($from_date));
							$day_of_year_to_date = date('z', strtotime($to_date));
							if($day_of_year_from_date <= $day_of_year_to_date) {
								$query_where .= ' DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR("'.$from_date.'") - YEAR(MARRIAGE_DATE)) YEAR)) >= DAYOFYEAR("'.$from_date.'") and DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR("'.$to_date.'") - YEAR(MARRIAGE_DATE)) YEAR)) <= DAYOFYEAR("'.$to_date.'") ';
							} else {
								$query_where .= ' DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR("'.$from_date.'") - YEAR(MARRIAGE_DATE)) YEAR)) >= DAYOFYEAR("'.$from_date.'") or DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR("'.$to_date.'") - YEAR(MARRIAGE_DATE)) YEAR)) <= DAYOFYEAR("'.$to_date.'") ';
							}
						} else if((trim($from_date) != "" && strlen(trim($from_date) > 4)) && (trim($to_date) == "" || strlen(trim($to_date) <= 4))) {
							$query_where .= ' DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR("'.$from_date.'") - YEAR(MARRIAGE_DATE)) YEAR)) >= DAYOFYEAR("'.$from_date.'") ';
						} else if((trim($from_date) == "" || strlen(trim($from_date) <= 4)) && (trim($to_date) != "" && strlen(trim($to_date) > 4))) {
							$query_where .= ' DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR("'.$to_date.'") - YEAR(MARRIAGE_DATE)) YEAR)) <= DAYOFYEAR("'.$to_date.'") ';
						}
					}
					else
					{
						$marriage_date_ignore_year = 0;
						if(trim($from_date) != "" && strlen(trim($from_date) > 4) &&  trim($to_date) != "" && strlen(trim($to_date) > 4)) {
							$query_where .= ' MARRIAGE_DATE BETWEEN DATE("'.$from_date.'") and DATE("'.$to_date.'")';
						} else if((trim($from_date) != "" && strlen(trim($from_date) > 4)) && (trim($to_date) == "" || strlen(trim($to_date) <= 4))) {
							$query_where .= ' MARRIAGE_DATE >= DATE("'.$from_date.'") ';
						} else if((trim($from_date) == "" || strlen(trim($from_date) <= 4)) && (trim($to_date) != "" && strlen(trim($to_date) > 4))) {
							$query_where .= ' MARRIAGE_DATE <= DATE("'.$to_date.'") ';
						}
					}
				}

				if($report_rules[$i][0] == 'BIRTH_MARRIAGE_DATE')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					$dates = explode(":", $report_rules[$i][2]);
					$from_date_arr = explode("/", $dates[0]);
					$to_date_arr = explode("/", $dates[1]);
					$from_date = $from_date_arr[2] . "-" .$from_date_arr[1]. "-" .$from_date_arr[0];
					$to_date = $to_date_arr[2] . "-" .$to_date_arr[1]. "-" .$to_date_arr[0];

					$query_where .= ' DATE_FORMAT(DOB, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d") OR';
					$query_where .= ' DATE_FORMAT(MARRIAGE_DATE, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d")';
				}

				if($report_rules[$i][0] == 'MARITAL_STATUS')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][2] == 'SINGLE') {
						$query_where .= ' MARITAL_STATUS = 1';
					} else if($report_rules[$i][2] == 'MARRIED') {
						$query_where .= ' MARITAL_STATUS = 2';
					} else if($report_rules[$i][2] == 'WIDOW') {
						$query_where .= ' MARITAL_STATUS = 3';
					} else if($report_rules[$i][2] == 'WIDOWER') {
						$query_where .= ' MARITAL_STATUS = 4';
					} else {
						$query_where .= ' MARITAL_STATUS = -1';
					}
				}

				if($report_rules[$i][0] == 'BAPTISM')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][2] == 'YES') {
						$query_where .= ' BABTISED = 1';
					} else if($report_rules[$i][2] == 'NO') {
						$query_where .= ' BABTISED = 2';
					} else {
						$query_where .= ' BABTISED = -1';
					}
				}

				if($report_rules[$i][0] == 'CONFIRMATION')
				{
					if(strlen($query_where) > 0) {
						$query_where .= ' and ';
					}
					if($report_rules[$i][2] == 'YES') {
						$query_where .= ' CONFIRMATION = 1';
					} else if($report_rules[$i][2] == 'NO') {
						$query_where .= ' CONFIRMATION = 2';
					} else {
						$query_where .= ' CONFIRMATION = -1';
					}
				}				
			}

			/** /
			if($birth_date_query != "")
			{
				if(strlen($query_where) > 0) {
					$query_where .= ' and ';
				}
				$query_where .= $birth_date_query;
			}
			/**/
			/** /
			if($marriage_date_query != "")
			{
				if(strlen($query_where) > 0) {
					$query_where .= ' and ';
				}
				$query_where .= $marriage_date_query;
			}
			/**/
		}

		/****************************************/

		//$query_to_execute = $query_to_execute . $query_col . $query_table . $query_profile_cond;
		$query_to_execute = $query_to_execute . $query_col . $query_table;
		if(strlen($query_where) > 0) {
			/** /
			if($include_inactive_profile) {
				$query_to_execute .= ' where ';
			} else {
				$query_to_execute .= ' and';
			}
			/**/
			$query_to_execute .= ' where ';
			$query_to_execute .= $query_where;
			//Constructing "order by"
			if($birth_day_rule) {
				if($dob_ignore_year==1) {
					$query_to_execute .= " order by DAYOFYEAR(DATE_ADD(DOB, INTERVAL (YEAR(NOW()) - YEAR(DOB)) YEAR)) ASC ";
				} else {
					$query_to_execute .= " order by DOB ASC ";
				}
			} else  if($marriage_date_rule) {
				if($marriage_date_ignore_year == 1) {
					$query_to_execute .= " order by DAYOFYEAR(DATE_ADD(MARRIAGE_DATE, INTERVAL (YEAR(NOW()) - YEAR(MARRIAGE_DATE)) YEAR)) ASC ";
				} else {
					$query_to_execute .= " order by MARRIAGE_DATE ASC ";
				}
			}
		}

	//	echo 'SKTGR:::'.$query_to_execute;
	//	exit;

		/******************************************************************************************************************************* /
		Filtering done using the custom fieldvalues

		$arrayCustomFieldIDs, $arrayCustomFieldTypes, $arrayCustomFieldTextboxContains, $arrayCustomFieldNumberSelFilterValue, $arrayCustomFieldNumberValue, $arrayCustomFieldDateFrom, $arrayCustomFieldDateTo, $arrayCustomFieldDateIgnoreYear, $arrayCustomFieldURLContains, $arrayCustomFieldDropboxValue, $arrayCustomFieldTickboxValue, $arrayCustomFieldTextAreaContains
		/*********************************************************************************************************************************/

		$is_custom_filters_enabled = 0;
		$final_unique_prof_id_array = array();
		if(COUNT($arrayCustomFieldIDs) > 0)
		{
			$is_custom_filters_enabled = 1;
			$total_custom_field_filters = COUNT($arrayCustomFieldIDs);
			$complete_cus_fld_filt_prof_id_list = array();
			for($f=0; $f < COUNT($arrayCustomFieldIDs); $f++)
			{
				$curr_custom_field_id = $arrayCustomFieldIDs[$f];
				$curr_prof_id_list = array();
				$custom_field_filter_query = "";
				$custom_filter_result = null;
				if($arrayCustomFieldTypes[$f] == 1)//textbox
				{
					$curr_prof_id_list = array();
					$curr_textbox_contains = trim($arrayCustomFieldTextboxContains[$f]);
					$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and b.FIELD_VALUE like ? COLLATE LATIN1_GENERAL_CI';
					$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query, array("%".$curr_textbox_contains."%"));
					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
				else if($arrayCustomFieldTypes[$f] == 2)//Numbers
				{
					$curr_prof_id_list = array();
					$curr_numbers_filter = trim($arrayCustomFieldNumberSelFilterValue[$f]);
					$curr_numbers_value = trim($arrayCustomFieldNumberValue[$f]);
					if($curr_numbers_filter == "lessorequalto") {
						$custom_field_filter_query = "select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID=".$curr_custom_field_id." and CAST(b.FIELD_VALUE AS SIGNED INTEGER) <=".$curr_numbers_value;
						$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query);
					} else if($curr_numbers_filter == "equalto") {
						$custom_field_filter_query = "select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID=".$curr_custom_field_id." and CAST(b.FIELD_VALUE AS SIGNED INTEGER) =".$curr_numbers_value;
						$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query);
					} else if($curr_numbers_filter == "greaterorequalto") {
						$custom_field_filter_query = "select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID=".$curr_custom_field_id." and CAST(b.FIELD_VALUE AS SIGNED INTEGER) >=".$curr_numbers_value;
						$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query);
					}
					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
				else if($arrayCustomFieldTypes[$f] == 3)//Password
				{
				}
				else if($arrayCustomFieldTypes[$f] == 4)//Date
				{
					//$arrayCustomFieldDateFrom, $arrayCustomFieldDateTo, $arrayCustomFieldDateIgnoreYear
					$curr_prof_id_list = array();
					$curr_date_from = trim($arrayCustomFieldDateFrom[$f]);
					$curr_date_to = trim($arrayCustomFieldDateTo[$f]);
					$curr_ignore_year = trim($arrayCustomFieldDateIgnoreYear[$f]);

					$formated_from_date = "";
					$curr_date_from = trim($curr_date_from);
					if(trim($curr_date_from) != "") {
						$formated_from_date_arr = explode("/", $curr_date_from);
						$formated_from_date = $formated_from_date_arr[2] . "-" .$formated_from_date_arr[1]. "-" .$formated_from_date_arr[0];
					}

					$formated_to_date = "";
					$curr_date_to = trim($curr_date_to);
					if(trim($curr_date_to) != "") {
						$formated_to_date_arr = explode("/", $curr_date_to);
						$formated_to_date = $formated_to_date_arr[2] . "-" .$formated_to_date_arr[1]. "-" .$formated_to_date_arr[0];
					}

					if($curr_ignore_year == 1)
					{
						if(trim($formated_from_date) != "" && trim($formated_to_date) != "")
						{
							$day_of_year_from_date = date('z', strtotime($formated_from_date));
							$day_of_year_to_date = date('z', strtotime($formated_to_date));
							if($day_of_year_from_date <= $day_of_year_to_date) {
								$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and (DAYOFYEAR(DATE_ADD(CAST(b.FIELD_VALUE AS DATE), INTERVAL (YEAR("'.$formated_from_date.'") - YEAR(CAST(b.FIELD_VALUE AS DATE))) YEAR)) >= DAYOFYEAR("'.$formated_from_date.'") and DAYOFYEAR(DATE_ADD(CAST(b.FIELD_VALUE AS DATE), INTERVAL (YEAR("'.$formated_to_date.'") - YEAR(CAST(b.FIELD_VALUE AS DATE))) YEAR)) <= DAYOFYEAR("'.$formated_to_date.'"))';
							} else {
								$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and (DAYOFYEAR(DATE_ADD(CAST(b.FIELD_VALUE AS DATE), INTERVAL (YEAR("'.$formated_from_date.'") - YEAR(CAST(b.FIELD_VALUE AS DATE))) YEAR)) >= DAYOFYEAR("'.$formated_from_date.'") or DAYOFYEAR(DATE_ADD(CAST(b.FIELD_VALUE AS DATE), INTERVAL (YEAR("'.$formated_to_date.'") - YEAR(CAST(b.FIELD_VALUE AS DATE))) YEAR)) <= DAYOFYEAR("'.$formated_to_date.'"))';
							}
							
						}
						else if(trim($formated_from_date) != "" && trim($formated_to_date) == "")
						{
							$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and (DAYOFYEAR(DATE_ADD(CAST(b.FIELD_VALUE AS DATE), INTERVAL (YEAR("'.$formated_from_date.'") - YEAR(CAST(b.FIELD_VALUE AS DATE))) YEAR)) >= DAYOFYEAR("'.$formated_from_date.'"))';
						}
						else if(trim($formated_from_date) == "" && trim($formated_to_date) != "")
						{
							$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and (DAYOFYEAR(DATE_ADD(CAST(b.FIELD_VALUE AS DATE), INTERVAL (YEAR("'.$formated_to_date.'") - YEAR(CAST(b.FIELD_VALUE AS DATE))) YEAR)) <= DAYOFYEAR("'.$formated_to_date.'"))';
						}
					}
					else
					{
						if(trim($formated_from_date) != "" && trim($formated_to_date) != "")
						{
							$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and CAST(b.FIELD_VALUE AS DATE) BETWEEN "'.$formated_from_date.'" and "'.$formated_to_date.'"';
						}
						else if(trim($formated_from_date) != "" && trim($formated_to_date) == "")
						{
							$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and CAST(b.FIELD_VALUE AS DATE) >= "'.$formated_from_date.'"';
						}
						else if(trim($formated_from_date) == "" && trim($formated_to_date) != "")
						{
							$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and CAST(b.FIELD_VALUE AS DATE) <= "'.$formated_to_date.'"';
						}
					}
					$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query);
					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
				else if($arrayCustomFieldTypes[$f] == 5)//Link/URl
				{
					$curr_prof_id_list = array();
					$curr_linkurl_contains = trim($arrayCustomFieldURLContains[$f]);
					$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and b.FIELD_VALUE like ? COLLATE LATIN1_GENERAL_CI';
					$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query, array("%".$curr_linkurl_contains."%"));

					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
				else if($arrayCustomFieldTypes[$f] == 6)//Dropdown
				{
					//$arrayCustomFieldDropboxValue, $arrayCustomFieldTickboxValue, $arrayCustomFieldTextAreaContains
					$curr_prof_id_list = array();
					$curr_dropbox_value = trim($arrayCustomFieldDropboxValue[$f]);
					$custom_field_filter_query = "select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID=".$curr_custom_field_id." and b.FIELD_VALUE=?";
					$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query, array($curr_dropbox_value));
					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
				else if($arrayCustomFieldTypes[$f] == 7)//Tickbox
				{
					$curr_prof_id_list = array();
					$curr_tickbox_value = trim($arrayCustomFieldTickboxValue[$f]);
					$custom_field_filter_query = "select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID=".$curr_custom_field_id." and CAST(b.FIELD_VALUE AS SIGNED INTEGER)=".$curr_tickbox_value;
					$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query);
					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
				else if($arrayCustomFieldTypes[$f] == 8)//Textarea
				{
					$curr_prof_id_list = array();
					$curr_textarea_contains = trim($arrayCustomFieldTextAreaContains[$f]);
					$custom_field_filter_query = 'select distinct b.PROFILE_ID from PROFILE_DETAILS as a, PROFILE_CUSTOM_FIELD_VALUES as b where a.PROFILE_ID=b.PROFILE_ID and b.FIELD_ID='.$curr_custom_field_id.' and b.FIELD_VALUE like ? COLLATE LATIN1_GENERAL_CI';
					$custom_filter_result = $this->db_conn->Execute($custom_field_filter_query, array("%".$curr_textarea_contains."%"));
					if($custom_filter_result) {
						if(!$custom_filter_result->EOF) {
							while(!$custom_filter_result->EOF)
							{
								$curr_prof_id_list[] = $custom_filter_result->fields[0];
								$custom_filter_result->MoveNext();
							}
						}
					}
					$complete_cus_fld_filt_prof_id_list[] = $curr_prof_id_list;
				}
			}

			//Following is done to pass at least 2 arrays as arguments for array_intersect
			if(COUNT($complete_cus_fld_filt_prof_id_list) > 0)
			{
				if(COUNT($complete_cus_fld_filt_prof_id_list) == 1) {
					$complete_cus_fld_filt_prof_id_list[] = $complete_cus_fld_filt_prof_id_list[0];
				}
				$final_unique_prof_id_array = call_user_func_array('array_intersect', $complete_cus_fld_filt_prof_id_list);
			}
		}
		/*********************************************************************************************************************************/

		$all_custom_field_values = array();
		$settings_custom_field_details = array();
		if(COUNT($arraySelectedCusFieldColumnIDs) > 0) {
			$cus_valued_result = $profiles_obj->getAllProfileCustomFieldValues();
			if($cus_valued_result[0] == 1) {
				$all_custom_field_values = $cus_valued_result[1];
			}
			$general_custom_field_details = $settings_obj->getAllCustomProfileFields();
			//$field_id, $field_name, $field_type, $field_options
			foreach($general_custom_field_details as $cus_field_key=>$cus_field_value)
			{
				$settings_custom_field_details[] = array($cus_field_value[0], $cus_field_value[2], $cus_field_value[3]);
			}
		}

		if(strlen($query_to_execute) > 0)
		{
			//$j = 0;
			$q = 0;
			$column_values = array();
			$result = $this->db_conn->Execute($query_to_execute);
			if($result) {
                if(!$result->EOF) {
					//$total_columns = ($result->FieldCount() - 2);
					$extra_column_count = $extra_column_count + 3;
                    while(!$result->EOF)
					{
						$value = array();
						$profile_id = $result->fields[0];

						if($is_custom_filters_enabled == 1)
						{
							if(!in_array($profile_id, $final_unique_prof_id_array)) {
								$result->MoveNext();
								continue;
							}
						}

						if($req_from == 2)
						{
							//$extra_column_count++;
							$value[] = '<input type="checkbox" id="inputSelectMemberID-'.$q.'" value="'.$profile_id.'" />';
						}

						//////////////////////////////////////////////////////////////////////////////////////
						//////////////////////////////////////////////////////////////////////////////////////

						$salutation_id = $result->fields[1];
                        $name = $result->fields[2];
                        $unique_id = $result->fields[3];
						$date_of_birth = $result->fields[4];
						$gender_id = $result->fields[5];
						$relation_ship_id = $result->fields[6];
						$marital_status_id = $result->fields[7];
						$marriage_date = $result->fields[8];
						$marriage_place = $result->fields[9];
                        $address1 = $result->fields[10];
						$address2 = $result->fields[11];
						$address3 = $result->fields[12];
						$area = $result->fields[13];
						$pincode = $result->fields[14];
						$landline = $result->fields[15];
						$mobile1 = $result->fields[16];
						$mobile2 = $result->fields[17];
                        $email = $result->fields[18];
                        $profile_status_id = $result->fields[19];
						$notes = $result->fields[20];
						$is_babtised = $result->fields[21];
						$is_confirmed = $result->fields[22];
						$occupation = $result->fields[23];
						$is_another_chruch_member = $result->fields[24];
						$parent_profile_id = $result->fields[25];
						$middle_name = $result->fields[26];
						$last_name = $result->fields[27];
						$work_phone = $result->fields[28];
						$family_photo_location = $result->fields[29];
						$profile_photo_location = $result->fields[30];
						$email_subscription = $result->fields[31];
						$sms_subscription = $result->fields[32];


						$current_profile_full_name = $re_arranged_salutation[$salutation_id] .". ". $name.((trim($middle_name) != "")? " ".trim($middle_name) : "").((trim($last_name) != "")? " ".trim($last_name) : "");

						for($i=0; $i<$total_columns; $i++)
						{
							if($i == 0) {
								$j = $i + $extra_column_count;
							}
							$temp_value = "";
							if($report_columns[$i] == 0) {
								$temp_value = 'CS' . appendZeroInUniqueID($profile_id);
							} else if($report_columns[$i] == 1) {
								if($parent_profile_id != -1) {
									if($parent_list_available)
									{
										$temp_value = 'FAMILY HEAD NAME';
										foreach($parent_list as $item_row => $item_value)
										{
											if($parent_profile_id == $item_value[0])
											{
												$parent_salutation_id = $parent_list[$item_row][11];
												$parent_prof_full_name = $re_arranged_salutation[$parent_salutation_id] .". ".$item_value[1].((trim($item_value[12]) != "")? " ".trim($item_value[12]) : "").((trim($item_value[13]) != "")? " ".trim($item_value[13]) : "");
												$temp_value = $parent_prof_full_name;
												break;
											}
										}
									}
									else
									{
										$temp_value = 'FAMILY HEAD NAME';
									}
								}
								else
								{
									//profile name itself is family head
									$temp_value = $current_profile_full_name;
								}
							} else if($report_columns[$i] == 2) {
								$temp_value = $re_arranged_salutation[$salutation_id] .". ". $name.((trim($middle_name) != "")? " ".trim($middle_name) : "").((trim($last_name) != "")? " ".trim($last_name) : "");
							} else if($report_columns[$i] == 3) {
								if(trim($date_of_birth) != "" && trim($date_of_birth) != "0000-00-00") {
									$readable_dob = formatDateOfBirth($date_of_birth);
									$dob_time_stamp = strtotime($date_of_birth);
									$dob_time_stamp = $dob_time_stamp+10000000000;//to make it positive
									$dob_time_stamp = prependZeroForUniformLength($dob_time_stamp, 13);
								} else {
									$readable_dob = "-";
									$dob_time_stamp = 0;
									$dob_time_stamp = prependZeroForUniformLength($dob_time_stamp, 13);
								}
								$temp_value = '<span style="display:none;">'.$dob_time_stamp.'</span>'.$readable_dob;
							} else if($report_columns[$i] == 4) {
								if($gender_id == -1) {
									$temp_value = $gender_values[0];
								} else {
									$temp_value = $gender_values[$gender_id];
								}
							} else if($report_columns[$i] == 5) {
								if($relation_ship_id == -1) {
									$temp_value = 'Not sure';
								} else {
									$temp_value = $relation_ship_values[$relation_ship_id];
								}								
							} else if($report_columns[$i] == 6) {
								if($marital_status_id == -1) {
									$temp_value = $marital_values[0];
								} else {
									$temp_value = $marital_values[$marital_status_id];
								}
							} else if($report_columns[$i] == 7) {
								if(trim($marriage_date) != "" && trim($marriage_date) != "0000-00-00") {
									$readable_dom = formatDateOfBirth($marriage_date);
									$dom_time_stamp = strtotime($marriage_date);
									$dom_time_stamp = $dom_time_stamp+10000000000;//to make it positive
									$dom_time_stamp = prependZeroForUniformLength($dom_time_stamp, 13);
								} else {
									$readable_dom = "-";
									$dom_time_stamp = 0;
									$dom_time_stamp = prependZeroForUniformLength($dom_time_stamp, 13);
								}
								$temp_value = '<span style="display:none;">'.$dom_time_stamp.'</span>'.$readable_dom;
							} else if($report_columns[$i] == 8) {
								$temp_value = $marriage_place;
							} else if($report_columns[$i] == 9) {
								if($is_babtised == -1) {
									$temp_value = $baptised_values[0];
								} else {
									$temp_value = $baptised_values[$is_babtised];
								}								
							} else if($report_columns[$i] == 10) {
								if($is_confirmed == -1) {
									$temp_value = $confirmation_values[0];
								} else {
									$temp_value = $confirmation_values[$is_confirmed];
								}
							} else if($report_columns[$i] == 11) {
								$temp_value = $occupation;
							} else if($report_columns[$i] == 12) {
								if($is_another_chruch_member == -1) {
									$temp_value = $is_another_church_values[0];
								} else {
									$temp_value = $is_another_church_values[$is_another_chruch_member];
								}
							} else if($report_columns[$i] == 13) {

								$full_address = $address1;
								if(trim($address2) != "") {
									$full_address .= ',<BR>' . $address2;
								}
								if(trim($address3) != "") {
									$full_address .= ',<BR>' . $address3;
								}
								if(trim($area) != "") {
									$full_address .= ',<BR>' . $area;
								}
								if(trim($pincode) != "") {
									$full_address .= ' - ' . $pincode;
								}
								$temp_value = $full_address;

							} else if($report_columns[$i] == 14) {
								$temp_value = $mobile1;
							} else if($report_columns[$i] == 15) {
								$temp_value = $landline;
							} else if($report_columns[$i] == 16) {
								$temp_value = $work_phone;
							} else if($report_columns[$i] == 17) {
								$temp_value = $email;
							} else if($report_columns[$i] == 18) {
								$age = '-';
								if(trim($date_of_birth) != "" && trim($date_of_birth) != '0000-00-00') {
									$dob_arr = explode('-', $date_of_birth);
									$age = Carbon::createFromDate($dob_arr[0], $dob_arr[1], $dob_arr[2])->age;
								}
								$temp_value = $age;
							} else if($report_columns[$i] == 19) {
								if($profile_status_id == 1) {
									$temp_value = $profile_status_values[0];
								} else if($profile_status_id == 2 || $profile_status_id == 0) {
									$temp_value = $profile_status_values[1];
								} else if($profile_status_id == 3) {
									$temp_value = $profile_status_values[2];
								}
							} else if($report_columns[$i] == 20) {
								$temp_value = $notes;
							} else if($report_columns[$i] == 21) {
								$temp_value = 'FAM' . appendZeroInUniqueID($unique_id);
							}
							$value[] = $temp_value;
							$j++;							
						}

						for($cpf=0; $cpf < COUNT($arraySelectedCusFieldColumnIDs); $cpf++)
						{
							$cus_prof_field_value = "";
							$cus_field_id = $arraySelectedCusFieldColumnIDs[$cpf];
							foreach($all_custom_field_values as $cus_details_key=>$cus_details_value)
							{
								if($cus_details_value[0] == $profile_id && $cus_details_value[1] == $cus_field_id) {
									$cus_prof_field_value = $cus_details_value[2];
									foreach($settings_custom_field_details as $key_1=>$value_1)
									{
										if($value_1[0] == $cus_field_id && $value_1[1] == 4 && trim($cus_prof_field_value) != "") {
											if(trim($cus_prof_field_value) != "" && trim($cus_prof_field_value) != "0000-00-00") {
												$readable_cus_field_date = formatDateOfBirth($cus_prof_field_value);
												$this_date_time_stamp = strtotime($cus_prof_field_value);
												$this_date_time_stamp = $this_date_time_stamp+10000000000;//to make it positive
												$this_date_time_stamp = prependZeroForUniformLength($this_date_time_stamp, 13);
											} else {
												$readable_cus_field_date = "-";
												$this_date_time_stamp = 0;
												$this_date_time_stamp = prependZeroForUniformLength($this_date_time_stamp, 13);
											}
											$cus_prof_field_value = '<span style="display:none;">'.$this_date_time_stamp.'</span>'.$readable_cus_field_date;
											break;
										} else if($value_1[0] == $cus_field_id && $value_1[1] == 6 && trim($cus_prof_field_value) != "") {
											$sel_box_array = explode(",", $value_1[2]);
											if(COUNT($sel_box_array) >= $cus_details_value[2])
											{
												$cus_prof_field_value = $sel_box_array[$cus_details_value[2]];
											}
											break;
										} else if($value_1[0] == $cus_field_id && $value_1[1] == 7) {
											if($cus_details_value[2] == 1) {
												$cus_prof_field_value = "Yes";
											} else {
												$cus_prof_field_value = "No";
											}
											break;
										}
									}
									break;
								}
							}
							$value[] = $cus_prof_field_value;
							$j++;							
						}
						//////////////////////////////////////////////////////////////////////////////////////
						//////////////////////////////////////////////////////////////////////////////////////
						/** /
						if($birth_marriage_rules)
						{
							$date_of_birth = formatDateOfBirth($result->fields[4]); //date of birth
							$marriage_date = formatDateOfBirth($result->fields[8]); //marriage date
							$value[] = $date_of_birth;
							$value[] = $marriage_date;
						}
						else if($birth_day_rule)
						{
							$date_of_birth = formatDateOfBirth($result->fields[4], true); //date of birth							
							$value[] = $date_of_birth;
						}
						else if($marriage_date_rule)
						{
							$marriage_date = formatDateOfBirth($result->fields[8], true); //marriage date
							$value[] = $marriage_date;
						}
						
						for($i=0; $i<$total_columns; $i++)
						{
							if($i == 0) {
								$j = $i + $extra_column_count;
							}
							$temp_value = $result->fields[$j];
							//echo "::: TEMP_VALUE:::" . $temp_value. "<BR>";
							if($report_columns[$i] == 0) {
								$temp_value = 'CS' . appendZeroInUniqueID($temp_value);
							} else if($report_columns[$i] == 1) {
								//find dependant's family head name
								if($parent_profile_id != -1) {
									if($parent_list_available)
									{
										$temp_value = 'FAMILY HEAD NAME';
										foreach($parent_list as $item_row => $item_value)
										{
											if($parent_profile_id == $item_value[0])
											{
												$parent_salutation_id = $parent_list[$item_row][11];
												$temp_value = $item_value[1];
												$temp_value = $re_arranged_salutation[$parent_salutation_id] .". ". $temp_value;
												break;
											}
										}
									}
									else
									{
										$temp_value = 'FAMILY HEAD NAME';
									}
								}
								else
								{
									//profile name itself is family head
									$temp_value = $re_arranged_salutation[$salutation_id] .". ". $temp_value;
								}
							} else if($report_columns[$i] == 2) {
								$temp_value = $re_arranged_salutation[$salutation_id] .". ". $temp_value;
							} else if($report_columns[$i] == 3 || $report_columns[$i] == 7) {
								$temp_value = formatDateOfBirth($temp_value);
							} else if($report_columns[$i] == 4) {
								if($temp_value == -1) {
									$temp_value = $gender_values[0];
								} else {
									$temp_value = $gender_values[$temp_value];
								}
							} else if($report_columns[$i] == 5) {
								if($temp_value == -1) {
									$temp_value = 'Not sure';
								} else {
									$temp_value = $relation_ship_values[$temp_value];
								}								
							} else if($report_columns[$i] == 6) {
								if($temp_value == -1) {
									$temp_value = $marital_values[0];
								} else {
									$temp_value = $marital_values[$temp_value];
								}
							} else if($report_columns[$i] == 9) {
								if($temp_value == -1) {
									$temp_value = $baptised_values[0];
								} else {
									$temp_value = $baptised_values[$temp_value];
								}								
							} else if($report_columns[$i] == 10) {
								if($temp_value == -1) {
									$temp_value = $confirmation_values[0];
								} else {
									$temp_value = $confirmation_values[$temp_value];
								}
							} else if($report_columns[$i] == 12) {
								if($temp_value == -1) {
									$temp_value = $is_another_church_values[0];
								} else {
									$temp_value = $is_another_church_values[$temp_value];
								}
							}else if($report_columns[$i] == 13) {

								$address = '';
								for($k=1; $k<=4; $k++) 
								{
									$j++;
									$addr_value = $result->fields[$j];
									if($addr_value != '')
									{
										$address .= ',<BR>' . $addr_value;
									}
								}
								$temp_value = $temp_value . $address;

							} else if($report_columns[$i] == 14) {
								
								for($m=1; $m<=2; $m++) 
								{
									$j++;
									$contact_value = $result->fields[$j];
									if($contact_value != '')
									{
										if($temp_value != '') {
											$temp_value .= ',<!-- <BR> -->';											
										}
										$temp_value .= $contact_value;
									}
								}

							} else if($report_columns[$i] == 16) {
								if($temp_value == -1) {
									$temp_value = $profile_status_values[0];
								} else {
									$temp_value = $profile_status_values[$temp_value];
								}
							} else if($report_columns[$i] == 18) {
								$age = '-';
								if($temp_value != '0000-00-00') {
									$dob_arr = explode('-', $temp_value);
									$age = Carbon::createFromDate($dob_arr[0], $dob_arr[1], $dob_arr[2])->age;
								}
								$temp_value = $age;
							}
							$value[] = $temp_value;
							$j++;							
						}
						/**/
						
						$q++;
						$column_values[] = $value;
						$result->MoveNext();
                    }
                }
            }

			//print_r($column_values);
			/** /
			if($birth_marriage_rules)
			{
				if(is_array($column_values))
				{
					$report_results = array();
					$total_count = COUNT($column_values);
					if($total_count > 0)
					{
						for($i=0; $i<$total_count; $i++)
						{
							$is_date_available = false;
							$date_def = array('birth', 'marriage');
							for($j=0; $j<2; $j++)
							{
								$date_arr = explode('-', $column_values[$i][$j]);
								$date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
								if($date != '0000-00-00')
								{
									$is_date_available = true;
									$report_results[$i][] = $date;
								}
							}
							if($is_date_available)
							{
								for($k=2; $k<COUNT($column_values[$i]); $k++)
								{
									$report_results[$i][] = $column_values[$i][$k];
								}
								for($j=0; $j<2; $j++)
								{
									$date_arr = explode('-', $column_values[$i][$j]);
									$date = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
									//if($date != '0000-00-00')
									{
										//$report_results[$i][] = str_replace('-', '', $date);
										//$report_results[$i][] = $date;
										$is_birthday = ($j==0)?'Yes':'No';
										$is_marriage = ($j==1)?'Yes':'No';
										if($j == 0)
											$report_results[$i][] = $is_birthday;
										if($j == 1)
										$report_results[$i][] = $is_marriage;
									}
								}
							}
																
						}
					}
				}
				$this->array_sort_by_column($report_results, '0');
				$column_values = $report_results;
				//print_r($column_values);exit;
			}
			/**/
			
			if($req_from == 2)
			{
				array_unshift($column_names_to_display, "<input type='checkbox' id='inputSelectAllMember' onclick='selectAllGroupMembers(this);'><input type='hidden' id='hiddenTotalMembers' value='".$q."' />");
			}
			//print_r($column_names_to_display);
			//print_r($column_values);
			//array_push($column_names_to_display, "<input type='checkbox' id='inputSelectAllMember')>");
			//array_push($column_values, "<input type='checkbox' id='inputSelectAllMembers')>");
			$report_details = array($column_names_to_display, $column_values);
		}
		return $report_details;
		//print_r($report_details);
	}

	public function array_sort_by_column(&$array, $column, $direction = SORT_ASC) {
		$reference_array = array();

		foreach($array as $key => $row) {
			$reference_array[$key] = $row[$column];
		}

		array_multisort($reference_array, $direction, $array);
	}

	public function getReportRules($report_id)
	{
		$report_rules = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from REPORT_RULES where REPORT_ID=?', array($report_id));
		   //echo $this->db_conn->ErrorMsg();
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $report_id = $result->fields[0];
                        $rule_type = $result->fields[1];
                        $rule_sub_type = $result->fields[2];
						$rule_value = $result->fields[3];
						
						$report_rules[] = array($rule_type, $rule_sub_type, $rule_value);
                        $result->MoveNext();                        
                    }
                }
            }
        }
		return $report_rules;
	}

	public function getReportColumns($report_id)
	{
		$report_columns = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from REPORT_COLUMNS where REPORT_ID=?', array($report_id));
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $report_id = $result->fields[0];
                        $column_category = $result->fields[1];
                        $column_data = $result->fields[2];
						$column_heading = $result->fields[3];
						
						$report_columns[] = array($column_category, $column_data, $column_heading);
                        $result->MoveNext();                        
                    }
                }
            }
        }
		return $report_columns;
	}

	public function listAllReports()
	{
		$report_details = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from REPORTS');
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $report_id = $result->fields[0];
                        $title = $result->fields[1];
                        $description = $result->fields[2];
						
						$report_details[] = array($report_id, $title, $description);
                        $result->MoveNext();                        
                    }
                }
            }
        }
		return $report_details;
	}

	public function deleteReportRule()
	{
	}

	public function deleteReportColumn()
	{
	}

	public function upateReportRule()
	{
	}

	public function updateReportColumn()
	{
	}

	public function updateReportDetails()
	{
	}

	public function generateSubscriptionReports($from_date, $to_date, $sub_fields)
	{
		$subscription_obj = new Subscription($APPLICATION_PATH);
		$subscription_fields = $subscription_obj->getAllSubscriptionFields();

		$rearrange_subscription_fields = array();
		foreach($subscription_fields as $key => $value)
		{
			$rearrange_subscription_fields[$value[0]] = $value[1];
		}

		//print_r($sub_fields);
		//print_r($rearrange_subscription_fields);

		$query = '';
		$query .= 'select';
		$total_fields = COUNT($sub_fields);
		$def_col = ' DATE_OF_SUBSCRIPTION,';
		$col = '';			
		if($total_fields > 0)
		{
			foreach($sub_fields as $key => $field_id)
			{
				if($col != '')
				{
					$col .= ',';
				}
				$col .= ' sum(SUB_FIELD_'.$field_id.')';
			}
		}
		$query .= $def_col.$col;
		$query .= ' from SUBSCRIPTION_DETAILS';
		$query .= ' where DATE_OF_SUBSCRIPTION >= ? and DATE_OF_SUBSCRIPTION <= ?';
		$query .= ' group by YEAR(DATE_OF_SUBSCRIPTION), MONTH(DATE_OF_SUBSCRIPTION)';
	
		//constructing reporting columns
		$month = array(1=>'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$from_date_arr = explode("-", $from_date);
		$to_date_arr = explode("-", $to_date);
		$from_month = $from_date_arr[1];
		$to_month = $to_date_arr[1];
		$column_names_to_display = array();
		
		//echo $query.$from_date.$to_date;
		$column_values = array();
		$field_values = array();
		$monthly_total = array();
		$month_available_in_db = array();
//		echo $query.":::".$from_date.":::".$to_date;
		$result = $this->db_conn->Execute($query, array($from_date, $to_date));
		if($result) {
			if(!$result->EOF) {
				while(!$result->EOF)
				{
					$date = $result->fields[0];
					$date_arr = explode("-", $date);
					$month_available_in_db[] = $date_arr[1];

					$total = 0;
					$j = 0;
					for($i=0; $i<$total_fields; $i++)
					{
						$j++;
						$temp = 'SubscriptionFieldID_'.$i;
						if(array_key_exists($sub_fields[$i], $rearrange_subscription_fields)) {
							//echo "IIII:::".$i."<BR>";
							$temp = $rearrange_subscription_fields[$sub_fields[$i]];
						}
						$subscription_amt = $result->fields[$j];
						$field_values[$temp][] = $subscription_amt;
						$total += $subscription_amt;	
//						$j++;	 
					}
					$monthly_total[] = $total;
					$result->MoveNext();
				}
			}
		}

		$column_names_to_display[] = 'Subscription Fields vs Month';
		foreach($month as $key => $value)
		{
			if($key >= $from_month && $key <= $to_month)
			{
				if(in_array($key, $month_available_in_db)) {
					$column_names_to_display[] = $value;
				}
			}				
		}
		$column_names_to_display[] = 'Total';

		//print_r($month_available_in_db);
		//print_r($column_names_to_display);
		
		//print_r($field_values);

/*
		for($i=$from_month; $i<=$to_month; $i++)
		{
			if(!in_array($i, $month_available_in_db)) {
				$field_values
			}
		}
*/
		//format the column values
		foreach($field_values as $key => $value)
		{
			array_push($value, array_sum($value));
			array_unshift($value, $key);			
			$column_values[] = $value;			
		}
		//print_r($monthly_total);
		array_push($monthly_total, array_sum($monthly_total));
		array_unshift($monthly_total, 'Total');
		$column_values[] = $monthly_total;

		//print_r($field_values);
		//print_r($column_names_to_display);
		//print_r($column_values);
		$report_details = array($column_names_to_display, $column_values);
		return $report_details;
	}
}