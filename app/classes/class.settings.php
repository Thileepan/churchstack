<?php
//class to handle profile settings usage

class ProfileSettings
{
	protected $db_conn;
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
	}

	public function getOptions($setting_id)
	{
		$options = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from PROFILE_SETTINGS where SETTINGS_ID=?', array($setting_id));
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $option_id = $result->fields[1];
                        $option_value = $result->fields[2];
                        $options[] = array($option_id, $option_value);
                        
						$result->MoveNext();                        
                    }
                }
				$result->Close();
            }
		}
		return $options;
	}

	public function addNewOption($setting_id, $option_value)
	{
		if($this->db_conn)
		{
			$option_id = $this->getMaxOptionID($setting_id);
			if($option_id < 0) {
				$option_id = 1;
			} else {
				$option_id++;
			}			

			$query = 'insert into PROFILE_SETTINGS (SETTINGS_ID, OPTION_ID, OPTION_VALUE) values (?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($setting_id, $option_id, $option_value));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateOption($setting_id, $option_id, $option_value)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_SETTINGS set OPTION_VALUE=? where OPTION_ID=? and SETTINGS_ID=?';
			$result = $this->db_conn->Execute($query, array($option_value, $option_id, $setting_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteOption($setting_id, $option_id)
	{
		if($this->db_conn)
		{
			//TODO::: Delete the profile associated data
			
			$query = 'delete from PROFILE_SETTINGS where OPTION_ID=? and SETTINGS_ID=?';
			//echo "Query:::".$query.$option_id, $setting_id;
			$result = $this->db_conn->Execute($query, array($option_id, $setting_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getMaxOptionID($setting_id)
	{
		$max_id = -1;
		if($this->db_conn)
		{
			$result = $this->db_conn->Execute('select max(OPTION_ID) from PROFILE_SETTINGS where SETTINGS_ID=?', array($setting_id));
			if($result) {
				if(!$result->EOF) {
					if($result->fields[0] != NULL) {
						$max_id = $result->fields[0];
					}
				}
				$result->Close();
			}
		}
		return $max_id;
	}

	public function getOptionValue($setting_id, $option_id)
	{
		$option_value = '';
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select OPTION_VALUE from PROFILE_SETTINGS where SETTINGS_ID=? and OPTION_ID=?', array($setting_id, $option_id));
           if($result) {
                if(!$result->EOF) {
                    $option_value = $result->fields[0];
                }
				$result->Close();
            }
		}
		return $option_value;
	}

	public function getAllCustomProfileFields()
	{
		$options = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select FIELD_ID, FIELD_NAME, FIELD_TYPE, FIELD_OPTIONS, FIELD_HELP_MESSAGE, IS_REQUIRED, VALIDATION, DISPLAY_ORDER from PROFILE_CUSTOM_FIELDS');
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $field_id = $result->fields[0];
                        $field_name = $result->fields[1];
						$field_type = $result->fields[2];
                        $field_options = $result->fields[3];
						$field_help_message = $result->fields[4];
                        $is_required = $result->fields[5];
						$validation_string = $result->fields[6];
                        $display_order = $result->fields[7];
                        $options[] = array($field_id, $field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order);
                        
						$result->MoveNext();
                    }
                }
				$result->Close();
            }
		}
		return $options;
	}

	public function getProfilesCustomFieldDetails($profile_id)
	{
		$field_details = array();
		if($this->db_conn)
		{
			$result = $this->db_conn->Execute('select FIELD_ID, FIELD_VALUE from PROFILE_CUSTOM_FIELD_VALUES where PROFILE_ID=?', array($profile_id));
           if($result) {
                if(!$result->EOF) {
					while(!$result->EOF)
					{
						$field_id = $result->fields[0];
						$field_value = $result->fields[1];
						$field_details[] = array($field_id, $field_value);
						
						$result->MoveNext();
					}
                }
				$result->Close();
            }
		}
		return $field_details;
	}

	public function getCustomProfileFieldDetails($field_id)
	{
		$field_details = array();
		if($this->db_conn)
		{
			$result = $this->db_conn->Execute('select FIELD_ID, FIELD_NAME, FIELD_TYPE, FIELD_OPTIONS, FIELD_HELP_MESSAGE, IS_REQUIRED, VALIDATION, DISPLAY_ORDER from PROFILE_CUSTOM_FIELDS where FIELD_ID=?', array($field_id));
			if($result) {
                if(!$result->EOF) {
					$field_id = $result->fields[0];
					$field_value = $result->fields[1];
					$field_type = $result->fields[2];
					$field_options = $result->fields[3];
					$field_help_message = $result->fields[4];
					$is_required = $result->fields[5];
					$validation_string = $result->fields[6];
					$display_order = $result->fields[7];
					$field_details = array($field_id, $field_value, $field_type, $field_options, $field_help_message, $is_required, $validation, $display_order);
				}
				$result->Close();
            }
		}
		return $field_details;
	}

	public function addNewCustomField($field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order)
	{
		$to_ret = array();
		$to_ret[0] = 0;
		$to_ret[1] = "Unable to save custom field";
		if($this->db_conn)
		{
			$query = 'insert into PROFILE_CUSTOM_FIELDS (FIELD_NAME, FIELD_TYPE, FIELD_OPTIONS, FIELD_HELP_MESSAGE, IS_REQUIRED, VALIDATION, DISPLAY_ORDER) values (?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				$to_ret[0] = 1;
				$to_ret[1] = "Custom field saved successfully";
				$result_2 = $this->db_conn->Execute('select MAX(FIELD_ID) from PROFILE_CUSTOM_FIELDS where FIELD_NAME=?', array($field_name));
				if($result_2) {
					if(!$result_2->EOF) {
						$field_id = $result_2->fields[0];
						$to_ret[2] = $field_id;
					}
				}
			}
		}
		return $to_ret;
	}

	public function updateCustomField($field_id, $field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_CUSTOM_FIELDS set FIELD_NAME=?, FIELD_TYPE=?, FIELD_OPTIONS=?, FIELD_HELP_MESSAGE=?, IS_REQUIRED=?, VALIDATION=?, DISPLAY_ORDER=? where FIELD_ID=?';
			$result = $this->db_conn->Execute($query, array($field_name, $field_type, $field_options, $field_help_message, $is_required, $validation_string, $display_order, $field_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteCustomField($field_id)
	{
		if($this->db_conn)
		{
			//TODO::: Delete the profile associated data
			
			$query = 'delete from PROFILE_CUSTOM_FIELDS where FIELD_ID=?';
			$result = $this->db_conn->Execute($query, array($field_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function insertDefCusFldValExistProfiles($new_field_id, $field_default_value)
	{
		include_once($this->APPLICATION_PATH."classes/class.profiles.php");
		$prof_obj = new Profiles($this->APPLICATION_PATH);
		$active_profiles = $prof_obj->getAllProfiles(1);

		for($p=0; $p < COUNT($active_profiles); $p++)
		{
			$curr_profile_id = $active_profiles[0];
			$query = 'insert into PROFILE_CUSTOM_FIELD_VALUES (PROFILE_ID, FIELD_ID, FIELD_VALUE) values (?,?,?) ON DUPLICATE KEY UPDATE FIELD_VALUE=VALUES(FIELD_VALUE) where PROFILE_ID=? and FIELD_ID=?';
			$result = $this->db_conn->Execute($query, array($curr_profile_id, $new_field_id, $field_default_value, $curr_profile_id, $new_field_id));
		}
	}

	public function asyncInsertDefCusFldValExistProfiles($new_field_id, $field_default_value)
	{
		@include_once($this->APPLICATION_PATH . 'plugins/thread/class.thread.php');
		/************************************************************************************** /
		Insert asynchronously
		/**************************************************************************************/
		$inserting_file = __DIR__."/../notify/profCusFieldDefValInsert.php";//Take care of this part
		$inserting_file = str_replace("\\", "/", $inserting_file);
		$commands = array();

		$commands[] = '"'.PHP_EXE_PATH.'" '.$inserting_file.' fieldID='.urlencode($new_field_id).' fieldValue='.urlencode($field_default_value).' > /dev/null 2>/dev/null &';

		$threads = new Multithread( $commands );
		$threads->run();

		/** /
		foreach ( $threads->commands as $key=>$command )
		{
			//echo "Command: ".$command."\n";
			//echo "\nOutput: ".$threads->output[$key]."\n";
			//echo "Error: ".$threads->error[$key]."\n\n";
		}
		/**/
	}
}

?>