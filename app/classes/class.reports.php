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

	public function generateReports($report_rules, $report_columns, $include_inactive_profile, $req_from)
	{
		include_once $this->APPLICATION_PATH . 'plugins/carbon/src/Carbon/Carbon.php';
		//print_r($report_rules);
		//print_r($report_columns);
		$query_to_execute = 'select';
		$query_table = ' from PROFILE_DETAILS';
		$query_col = ' PROFILE_ID, PARENT_PROFILE_ID, SALUTATION_ID';
		$extra_column_count = 0;
		$birth_marriage_rules = false;
		foreach($report_rules as $key => $value)
		{
			if($value[0] == 'BIRTH_MARRIAGE_DATE')
			{
				$extra_column_count = $extra_column_count + 2;
				$birth_marriage_rules = true;
				$query_col .= ', DOB, MARRIAGE_DATE';
				break;
			}
			else if($value[0] == 'BIRTH_DATE')
			{
				$extra_column_count = $extra_column_count + 1;
				$birth_day_rule = true;
				$query_col .= ', DOB';
				break;
			}
			else if($value[0] == 'MARRIAGE_DATE')
			{
				$extra_column_count = $extra_column_count + 1;
				$marriage_date_rule = true;
				$query_col .= ', MARRIAGE_DATE';
				break;
			}
		}
		$query_where = '';
		
		//print_r($report_columns);
		//echo "<BR>";
		/***** CONSTRUCTING COLUMNS ************/
		$column_names = array('MemberID', 'Family Head', 'Name', 'Date Of Birth', 'Gender', 'Relationship', 'Marital Status', 'Date Of Marriage', 'Place Of Marriage', 'Baptised', 'Confirmation', 'Occupation', 'Is Another Church Member', 'Address', 'Contacts', 'Email', 'Status', 'Notes', 'Age');
		$column_names_in_db = array('UNIQUE_ID', 'NAME', 'NAME', 'DOB', 'GENDER', 'RELATION_SHIP', 'MARITAL_STATUS', 'MARRIAGE_DATE', 'MARRIAGE_PLACE', 'BABTISED', 'CONFIRMATION', 'OCCUPATION', 'IS_ANOTHER_CHURCH_MEMBER', 'ADDRESS1, ADDRESS2, ADDRESS3, AREA, PINCODE', 'LANDLINE, MOBILE1, MOBILE2', 'EMAIL', 'PROFILE_STATUS', 'NOTES', 'DOB');

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
		$marital_values = array('Not sure', 'Single', 'Married', 'Widow');
		$baptised_values = array('Not sure', 'Yes', 'No');
		$confirmation_values = array('Not sure', 'Yes', 'No');
		$is_another_church_values = array('Not sure', 'Yes', 'No');
		$profile_status_values = array('Active', 'InActive', 'Expired');
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
		$relation_ship_values = $settings_obj->getOptions(2);
		//print_r($re_arranged_salutation);

		$column_names_to_display = array();
		$total_columns = COUNT($report_columns);
		if($total_columns > 0)
		{
			foreach($report_rules as $key => $value)
			{
				if($value[0] == 'BIRTH_MARRIAGE_DATE' || $value[0] == 'BIRTH_DATE' || $value[0] == 'MARRIAGE_DATE')
				{
					$column_names_to_display[] = 'Date';
					break;
				}
			}
			for($i=0; $i<$total_columns; $i++)
			{
				$column_name = $column_names[$report_columns[$i]];
//				if($column_name == "Family Head" || 
				$column_names_to_display[] = $column_name;
				
				$query_col .= ',';
				$query_col .= $column_names_in_db[$report_columns[$i]];

				//if($column_names_in_db[$report_columns[$i]] == 'DOB')
				//{
				//	$date_position = $i;
				//	$date_in_report_columns = true;
				//}
			}
			foreach($report_rules as $key => $value)
			{
				if($value[0] == 'BIRTH_MARRIAGE_DATE')
				{
					$column_names_to_display[] = 'BirthDay';
					$column_names_to_display[] = 'Marriage';
					break;
				}
			}
		}
		//echo $query_col;
		/****************************************/

		/***** CONSTRUCTING WHERE CONDITION *****/

		$query_profile_cond = '';
		if(!$include_inactive_profile) {
			$query_profile_cond .= ' where PROFILE_STATUS=1';
		}
		
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
					$query_where .= ' and ';
				}

				if($report_rules[$i][0] == 'PROFILES')
				{
					if($report_rules[$i][2] == 'FAMILY_HEAD') {
						$query_where .= ' PARENT_PROFILE_ID = -1';
					} else if($report_rules[$i][2] == 'INDIVIDUAL'){
						$query_where .= ' PARENT_PROFILE_ID != -1';
					}
				}

				if($report_rules[$i][0] == 'GENDER')
				{
					if($report_rules[$i][2] == 'MALE') {
						$query_where .= ' GENDER = 1';
					} else if($report_rules[$i][2] == 'FEMALE') {
						$query_where .= ' GENDER = 2';
					}
				}

				if($report_rules[$i][0] == 'AGE')
				{
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
					//$date_in_report_rules = true;
					$dates = explode(":", $report_rules[$i][2]);
					$from_date_arr = explode("/", $dates[0]);
					$to_date_arr = explode("/", $dates[1]);
					$from_date = $from_date_arr[2] . "-" .$from_date_arr[1]. "-" .$from_date_arr[0];
					$to_date = $to_date_arr[2] . "-" .$to_date_arr[1]. "-" .$to_date_arr[0];

					//$query_where .= ' DATE_FORMAT(DOB, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d") ORDER BY EXTRACT(MONTH_DAY FROM DOB) asc';
					$birth_date_query = ' DATE_FORMAT(DOB, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d") ORDER BY EXTRACT(MONTH FROM DOB) asc, EXTRACT(DAY FROM DOB) asc';
				}
				
				if($report_rules[$i][0] == 'MARRIAGE_DATE')
				{
					$dates = explode(":", $report_rules[$i][2]);
					$from_date_arr = explode("/", $dates[0]);
					$to_date_arr = explode("/", $dates[1]);
					$from_date = $from_date_arr[2] . "-" .$from_date_arr[1]. "-" .$from_date_arr[0];
					$to_date = $to_date_arr[2] . "-" .$to_date_arr[1]. "-" .$to_date_arr[0];

					$marriage_date_query = ' DATE_FORMAT(MARRIAGE_DATE, "%c-%d") BETWEEN DATE_FORMAT("'.$from_date.'", "%c-%d") and DATE_FORMAT("'.$to_date.'", "%c-%d") ORDER BY EXTRACT(MONTH FROM MARRIAGE_DATE) asc, EXTRACT(DAY FROM MARRIAGE_DATE) asc, UNIQUE_ID';
				}

				if($report_rules[$i][0] == 'BIRTH_MARRIAGE_DATE')
				{
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
					if($report_rules[$i][2] == 'SINGLE') {
						$query_where .= ' MARITAL_STATUS = 1';
					} else if($report_rules[$i][2] == 'MARRIED') {
						$query_where .= ' MARITAL_STATUS = 2';
					} else if($report_rules[$i][2] == 'WIDOW') {
						$query_where .= ' MARITAL_STATUS = 3';
					} else {
						$query_where .= ' MARITAL_STATUS = -1';
					}
				}

				if($report_rules[$i][0] == 'BAPTISM')
				{
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
					if($report_rules[$i][2] == 'YES') {
						$query_where .= ' CONFIRMATION = 1';
					} else if($report_rules[$i][2] == 'NO') {
						$query_where .= ' CONFIRMATION = 2';
					} else {
						$query_where .= ' CONFIRMATION = -1';
					}
				}				
			}

			if($birth_date_query != "")
			{
				if(strlen($query_where) > 0) {
					$query_where .= ' and ';
				}
				$query_where .= $birth_date_query;
			}
			if($marriage_date_query != "")
			{
				if(strlen($query_where) > 0) {
					$query_where .= ' and ';
				}
				$query_where .= $marriage_date_query;
			}
		}

		/****************************************/

		$query_to_execute = $query_to_execute . $query_col . $query_table . $query_profile_cond;
		if(strlen($query_where) > 0) {
			if($include_inactive_profile) {
				$query_to_execute .= ' where ';
			} else {
				$query_to_execute .= ' and';
			}
			$query_to_execute .= $query_where;
		}

	//	echo 'SKTGR:::'.$query_to_execute;
	//	exit;

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
						$parent_profile_id = $result->fields[1];
						$salutation_id = $result->fields[2];
						//echo "SaluationID:::".$salutation_id;

						if($req_from == 2)
						{
							//$extra_column_count++;
							$value[] = '<input type="checkbox" id="inputSelectMemberID-'.$q.'" value="'.$profile_id.'" />';
						}

						if($birth_marriage_rules)
						{
							$date_of_birth = formatDateOfBirth($result->fields[3]); //date of birth
							$marriage_date = formatDateOfBirth($result->fields[4]); //marriage date
							$value[] = $date_of_birth;
							$value[] = $marriage_date;
						}
						else if($birth_day_rule)
						{
							$date_of_birth = formatDateOfBirth($result->fields[3], true); //date of birth							
							$value[] = $date_of_birth;
						}
						else if($marriage_date_rule)
						{
							$marriage_date = formatDateOfBirth($result->fields[3], true); //marriage date
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
								$temp_value = 'STC' . appendZeroInUniqueID($temp_value);
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
						
						$q++;
						$column_values[] = $value;
						$result->MoveNext();
                    }
                }
            }

			//print_r($column_values);
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
		   echo $this->db_conn->ErrorMsg();
            
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
		   echo $this->db_conn->ErrorMsg();
            
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