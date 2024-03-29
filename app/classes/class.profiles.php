<?php
//class to handle profile usage

class Profiles
{
	private $db_conn;
	private $APPLICATION_PATH;
	public $profile_id;

	public function __construct($APPLICATION_PATH, $shardedDBName="")
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true, $shardedDBName);
		
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
	}

	public function getAllParentProfiles()
	{
		$profile_details = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select PROFILE_ID, NAME, UNIQUE_ID, ADDRESS1, ADDRESS2, ADDRESS3, AREA, PINCODE, LANDLINE, MARRIAGE_DATE, MARRIAGE_PLACE, SALUTATION_ID, MIDDLE_NAME, LAST_NAME from PROFILE_DETAILS where PARENT_PROFILE_ID=-1 ORDER BY NAME asc');
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $profile_id = $result->fields[0];
                        $name = $result->fields[1];
                        $unique_id = $result->fields[2];
						$address1 = $result->fields[3];
						$address2 = $result->fields[4];
						$address3 = $result->fields[5];
						$area = $result->fields[6];
						$pincode = $result->fields[7];
						$landline = $result->fields[8];
						$marriage_date = $result->fields[9];
						$marriage_place = $result->fields[10];
						$salutation_id = $result->fields[11];
						$middle_name = $result->fields[12];
						$last_name = $result->fields[13];
						
						$profile_details[] = array($profile_id, $name, $unique_id, $address1, $address2, $address3, $area, $pincode, $landline, $marriage_date, $marriage_place, $salutation_id, $middle_name, $last_name);
                        $result->MoveNext();                        
                    }
                }
            }
        }
		return $profile_details;
	}

	public function getAllProfiles($profile_status=1)
	{
		$profile_details = array();
		if($this->db_conn)
		{
			//always don't show deleted profiles
			$query = 'select pd.PROFILE_ID, pd.SALUTATION_ID, pd.NAME, pd.UNIQUE_ID, pd.DOB, pd.GENDER, pd.RELATION_SHIP, pd.MARITAL_STATUS, pd.MARRIAGE_DATE, pd.MARRIAGE_PLACE, pd.ADDRESS1, pd.ADDRESS2, pd.ADDRESS3, pd.AREA, pd.PINCODE, pd.LANDLINE, pd.MOBILE1, pd.MOBILE2, pd.EMAIL, pd.PROFILE_STATUS, pd.NOTES, pd.BABTISED, pd.CONFIRMATION, pd.OCCUPATION, pd.IS_ANOTHER_CHURCH_MEMBER, pd.PARENT_PROFILE_ID, pd.MIDDLE_NAME, pd.LAST_NAME, pd.WORK_PHONE, pd.FAMILY_PHOTO_LOCATION, pd.PROFILE_PHOTO_LOCATION, pd.EMAIL_NOTIFICATION, pd.SMS_NOTIFICATION, inner_pd.SALUTATION_ID as FAM_HEAD_SALUTATION_ID, inner_pd.NAME AS FAM_HEAD_FIRST_NAME, inner_pd.MIDDLE_NAME AS FAM_HEAD_MIDDLE_NAME, inner_pd.LAST_NAME AS FAM_HEAD_LAST_NAME from PROFILE_DETAILS as pd INNER JOIN PROFILE_DETAILS as inner_pd where ((pd.PARENT_PROFILE_ID != -1 and inner_pd.PROFILE_ID=pd.PARENT_PROFILE_ID) or (pd.PARENT_PROFILE_ID = -1 and inner_pd.PROFILE_ID=pd.PROFILE_ID)) and pd.PROFILE_STATUS != 3';
			if($profile_status != 3) {
				$query .= ' and pd.PROFILE_STATUS=' . $profile_status;
			}

		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $profile_id = $result->fields[0];
						$salution_id = $result->fields[1];
                        $name = $result->fields[2];
                        $unique_id = $result->fields[3];
						$dob = $result->fields[4];
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
						$fam_head_salutation_id = $result->fields[33];
						$fam_head_first_name = $result->fields[34];
						$fam_head_middle_name = $result->fields[35];
						$fam_head_last_name = $result->fields[36];

						$profile_details[] = array($profile_id, $salution_id, $name, $unique_id, $dob, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $parent_profile_id, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $email_subscription, $sms_subscription, $fam_head_salutation_id, $fam_head_first_name, $fam_head_middle_name, $fam_head_last_name); //(0-36)
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $profile_details;
	}

	public function getProfileNameAndEmailID($profile_id)
	{
		$profile_details = array();
		if($this->db_conn)
		{
		   $query = 'select NAME, EMAIL from PROFILE_DETAILS where PROFILE_ID=?';
		   $result = $this->db_conn->Execute($query, array($profile_id));
            
           if($result) {
                if(!$result->EOF) {
					$name = $result->fields[0];
					$email_id = $result->fields[1];

					$profile_details = array($name, $email_id);
				}
            }
        }
		return $profile_details;
	}

	public function getProfileInformation($profile_id)
	{
		$profile_info = array();
		if($this->db_conn)
		{
		   $query = 'select * from PROFILE_DETAILS where PROFILE_ID=? limit 1';
		   $result = $this->db_conn->Execute($query, array($profile_id));
            
           if($result) {
                if(!$result->EOF) {
						$profile_id = $result->fields[0];
						$salution_id = $result->fields[1];
                        $name = $result->fields[2];
                        $unique_id = $result->fields[3];
						$dob = $result->fields[4];
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
						$is_another_church_member = $result->fields[24];
                        $parent_profile_id = $result->fields[25];	
						$middle_name = $result->fields[26];
						$last_name = $result->fields[27];
						$work_phone = $result->fields[28];
						$family_photo_location = $result->fields[29];
						$profile_photo_location = $result->fields[30];
						$sms_subscription = $result->fields[31];
						$email_subscription = $result->fields[32];

						$profile_info = array($profile_id, $salution_id, $name, $unique_id, $dob, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $parent_profile_id, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $sms_subscription, $email_subscription); //(0-32)
				}
            }
        }
		return $profile_info;
	}

	public function addNewProfile($salutation_id, $name, $parent_profile_id, $unique_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $sms_notification, $email_notification)
	{
		$this->profile_id = -1;//To work with the existing code in doserver if add fails
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to add a new profile";
		if(isset($_SESSION["maxProfileCount"]) && $_SESSION["maxProfileCount"] > 0 && isset($_SESSION["churchCurrentActiveProfilesCount"]) && $_SESSION["churchCurrentActiveProfilesCount"] >= $_SESSION["maxProfileCount"])
		{
			$to_return[0] = 0;
			$to_return[1] = "Your current plan does not allow you to add anymore new profiles to your church account. If you wish to add new profiles, please upgrade your subscription plan. You may also contact the support to get assistance on this.";

			return $to_return;
		}
		if($this->db_conn)
		{
			//echo $salutation_id, $name, $parent_profile_id, $unique_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member;
			$query = 'insert into PROFILE_DETAILS (SALUTATION_ID, NAME, UNIQUE_ID, DOB, GENDER, RELATION_SHIP, MARITAL_STATUS, MARRIAGE_DATE, MARRIAGE_PLACE, ADDRESS1, ADDRESS2, ADDRESS3, AREA, PINCODE, LANDLINE, MOBILE1, MOBILE2, EMAIL, PROFILE_STATUS, NOTES, BABTISED, CONFIRMATION, OCCUPATION, IS_ANOTHER_CHURCH_MEMBER, PARENT_PROFILE_ID, MIDDLE_NAME, LAST_NAME, WORK_PHONE, FAMILY_PHOTO_LOCATION, PROFILE_PHOTO_LOCATION, SMS_NOTIFICATION, EMAIL_NOTIFICATION) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($salutation_id, $name, $unique_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $parent_profile_id, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $sms_notification, $email_notification));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				$this->profile_id = $this->db_conn->Insert_ID();
				$to_return[0] = 1;
				$to_return[1] = "New profile has been added successfully";
			}			
		}
		return $to_return;
	}

	public function updateProfile($profile_id, $salutation_id, $name, $parent_profile_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $sms_notification, $email_notification, $unique_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to update the profile";
		if($this->db_conn)
		{
			$query = 'update PROFILE_DETAILS set SALUTATION_ID=?, NAME=?, UNIQUE_ID=?, DOB=?, GENDER=?, RELATION_SHIP=?, MARITAL_STATUS=?, MARRIAGE_DATE=?, MARRIAGE_PLACE=?, ADDRESS1=?, ADDRESS2=?, ADDRESS3=?, AREA=?, PINCODE=?, LANDLINE=?, MOBILE1=?, MOBILE2=?, EMAIL=?, PROFILE_STATUS=?, NOTES=?, BABTISED=?, CONFIRMATION=?, OCCUPATION=?, IS_ANOTHER_CHURCH_MEMBER=?, PARENT_PROFILE_ID=?, MIDDLE_NAME=?, LAST_NAME=?, WORK_PHONE=?, FAMILY_PHOTO_LOCATION=?, PROFILE_PHOTO_LOCATION=?, SMS_NOTIFICATION=?, EMAIL_NOTIFICATION=? where PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($salutation_id, $name, $unique_id, $date_of_birth, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $parent_profile_id, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $sms_notification, $email_notification, $profile_id));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Profile has been updated successfully";
			}
		}
		return $to_return;
	}

	public function deleteProfile($profile_id, $is_profile_head)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_DETAILS set PROFILE_STATUS=3 where PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($profile_id));
			if($result) {
				if($is_profile_head)
				{
					$query = 'update PROFILE_DETAILS set PROFILE_STATUS=3 where PARENT_PROFILE_ID=?';
					$result = $this->db_conn->Execute($query, array($profile_id));
					if($result) {
						return true;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function getMaxProfileUniqueID()
	{
		$max_unique_id = -1;
		if($this->db_conn)
		{
			$query = 'select max(UNIQUE_ID) from PROFILE_DETAILS';
			$result = $this->db_conn->Execute($query);
			if($result) {
				$max_unique_id = 0;
				if(!$result->EOF) {
					if($result->fields[0] != NULL) {
						$max_unique_id = $result->fields[0];
					}
				}
			}
		}
		return $max_unique_id;
	}

	public function getProfileDependants($parent_profile_id)
	{
		$dependant_details = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from PROFILE_DETAILS where PARENT_PROFILE_ID=?', array($parent_profile_id));
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $profile_id = $result->fields[0];
						$salution_id = $result->fields[1];
                        $name = $result->fields[2];
                        $unique_id = $result->fields[3];
						$dob = $result->fields[4];
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

						$dependant_details[] = array($profile_id, $salution_id, $name, $unique_id, $dob, $gender_id, $relation_ship_id, $marital_status_id, $marriage_date, $marriage_place, $address1, $address2, $address3, $area, $pincode, $landline, $mobile1, $mobile2, $email, $profile_status_id, $notes, $is_babtised, $is_confirmed, $occupation, $is_another_church_member, $parent_profile_id, $middle_name, $last_name, $work_phone, $family_photo_location, $profile_photo_location, $email_subscription, $sms_subscription); //(0-32)
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $dependant_details;
	}

	public function getProfilesCount($profile_status, $profile_type)
	{
		// profile_status = 0 (ALL)
		// profile_status = 1 (ACTIVE)
		// profile_status = 2 (INACTIVE)
		// profile_status = 3 (DELETED)
		
		// profile_type = 0 (ALL)
		// profile_type = 1 (FAMILYHEAD)
		// profile_type = 2 (DEPENDANT)

		$total_count = 0;
		if($this->db_conn)
		{
			$query = 'select count(*) from PROFILE_DETAILS';
			if($profile_status == 1) {
				$query .= ' where PROFILE_STATUS = 1';
			} else if($profile_status == 2) {
				$query .= ' where PROFILE_STATUS = 2';
			} else if($profile_status == 3) {
				$query .= ' where PROFILE_STATUS = 3';
			}

			if($profile_type != 0 )
			{
				if($profile_status == 0) {
					$query .= ' where';
				} else {
					$query .= ' and';
				}
			}

			if($profile_type == 1) {
				$query .= ' PARENT_PROFILE_ID = -1';
			} else if($profile_type == 2) {
				$query .= ' PARENT_PROFILE_ID != -1';
			}

			$result = $this->db_conn->Execute($query);
			if($result) {
				if(!$result->EOF) {
					$total_count = $result->fields[0];
				}
			}
		}
		return $total_count;
	}

	public function getParentProfileID($unique_id)
	{
		$profile_id = 0;
		if($this->db_conn)
		{
			$query = 'select PROFILE_ID from PROFILE_DETAILS where UNIQUE_ID=? and PARENT_PROFILE_ID = -1 and PROFILE_STATUS=1';
			$result = $this->db_conn->Execute($query, array($unique_id));
			if($result) {
				if(!$result->EOF) {
					$profile_id = $result->fields[0];
				}
			}
		}
		return $profile_id;
	}

	public function getProfileStatus($profile_id)
	{
		$status = -1;
		if($this->db_conn)
		{
			$query = 'select PROFILE_STATUS from PROFILE_DETAILS where PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($profile_id));
			if($result) {
				if(!$result->EOF) {
					$status = $result->fields[0];
				}
			}
		}
		return $status;
	}

	public function updateProfileUniqueID($profile_id, $unique_id)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_DETAILS set UNIQUE_ID=? where PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($unique_id, $profile_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateProfileParentID($profile_id, $parent_profile_id)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_DETAILS set PARENT_PROFILE_ID=? where PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($parent_profile_id, $profile_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateDependantsProfileID($old_parent_profile_id, $new_parent_profile_id)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_DETAILS set PARENT_PROFILE_ID=? where PARENT_PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($new_parent_profile_id, $old_parent_profile_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateProfileStatus($profile_id, $profile_status)
	{
		if($this->db_conn)
		{
			$query = 'update PROFILE_DETAILS set PROFILE_STATUS=? where PROFILE_ID=?';
			$result = $this->db_conn->Execute($query, array($profile_status, $profile_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getProfCountGroupedByStatus()
	{
		$to_return[0] = 0;
		$to_return[1] = "Unable to get the profiles count";
		if($this->db_conn)
		{
			$counts_array = array();
			$query = 'select PROFILE_STATUS, count(PROFILE_ID) from PROFILE_DETAILS GROUP BY PROFILE_STATUS';
			$result = $this->db_conn->Execute($query);
			if($result) {
				while(!$result->EOF) {
					$profile_status = $result->fields[0];
					$profile_count = $result->fields[1];
					$counts_array[] = array($profile_status, $profile_count);
					$result->MoveNext();                        
				}
				$to_return[0] = 1;
				$to_return[1] = $counts_array;
			}
		}
		return $to_return;
	}

	public function addCustomProfileFields($profile_id, $field_id, $field_value)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Problem while updating the custom fields";
		if($this->db_conn)
		{
			$query = 'insert into PROFILE_CUSTOM_FIELD_VALUES (PROFILE_ID, FIELD_ID, FIELD_VALUE) values (?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($profile_id, $field_id, $field_value));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Custom fields updated successfully";
			}
		}
		return $to_return;
	}

	public function updateCustomProfileFields($profile_id, $field_id, $field_value)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Problem while updating the custom fields";
		if($this->db_conn)
		{
			$query = 'update PROFILE_CUSTOM_FIELD_VALUES set FIELD_VALUE=? where PROFILE_ID=? and FIELD_ID=?';
			$result = $this->db_conn->Execute($query, array($field_value, $profile_id, $field_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Custom fields updated successfully";
			}
		}
		return $to_return;
	}

	public function isCustomProfileFieldsAvailable($profile_id, $field_id)
	{
		if($this->db_conn)
		{
			$query = 'select FIELD_ID from PROFILE_CUSTOM_FIELD_VALUES where PROFILE_ID=? and FIELD_ID=? limit 1';
			$result = $this->db_conn->Execute($query, array($profile_id, $field_id));
			if($result) {
				if(!$result->EOF){
					return true;
				}
			}
		}
		return false;
	}

	public function isCustomProfileFieldUsed($field_id)
	{
		if($this->db_conn)
		{
			$query = 'select FIELD_ID from PROFILE_CUSTOM_FIELD_VALUES where FIELD_ID=? limit 1';
			$result = $this->db_conn->Execute($query, array($field_id));
			if($result) {
				if(!$result->EOF){
					return true;
				}
			}
		}
		return false;
	}

	public function updateProfilePhotoLocation($profile_id, $is_family_photo, $photo_location)
	{
		if($this->db_conn)
		{
			if($is_family_photo) {
				$query = 'update PROFILE_DETAILS set FAMILY_PHOTO_LOCATION=? where PROFILE_ID=?';
			} else {
				$query = 'update PROFILE_DETAILS set PROFILE_PHOTO_LOCATION=? where PROFILE_ID=?';
			}
			$result = $this->db_conn->Execute($query, array($photo_location, $profile_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getProfilePhotoLocation($profile_id, $is_family_photo)
	{
		$photo_location = '';
		if($this->db_conn)
		{
			if($is_family_photo) {
				$query = 'select FAMILY_PHOTO_LOCATION from PROFILE_DETAILS where PROFILE_ID=?';
			} else {
				$query = 'select PROFILE_PHOTO_LOCATION from PROFILE_DETAILS where PROFILE_ID=?';
			}
			$result = $this->db_conn->Execute($query, array($profile_id));
			if($result) {
				if(!$result->EOF) {
					$photo_location = $result->fields[0];
				}
			}
		}
		return $photo_location;
	}

	public function getAllProfileNameAndEmailIDs($ignore_email_id=false)
	{
		$profile_emails = array();
		if($this->db_conn)
		{
		   $query = 'select PROFILE_ID, EMAIL, CONCAT_WS(" ", NAME, MIDDLE_NAME, LAST_NAME), MOBILE1, MOBILE2, NAME, MIDDLE_NAME, LAST_NAME from PROFILE_DETAILS';
		   $query .= ' where PROFILE_STATUS=1 ';
		   if(!$ignore_email_id) {
			   $query .= ' and EMAIL != ""';
		   }
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
						$profile_id = $result->fields[0];
                        $email = $result->fields[1];
						$name = trim($result->fields[2]);
						$mobile1 = $result->fields[3];
						$mobile2 = $result->fields[4];
						$first_name = $result->fields[5];
						$middle_name = $result->fields[6];
						$last_name = $result->fields[7];
						$profile_emails[] = array($profile_id, $email, $name, $mobile1, $mobile2, $first_name, $middle_name, $last_name);

						$result->MoveNext();                        
                    }
                }
            }
        }
		return $profile_emails;
	}

	public function deactivateProfile($profile_id)
	{
		return $this->updateProfileStatus($profile_id, 2);//Deactivate
	}

	public function activateProfile($profile_id)
	{
		return $this->updateProfileStatus($profile_id, 1);//Activate
	}
	
	public function getAllProfileCustomFieldValues()
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Unable to get the custom field values";
		$all_field_values = array();
		if($this->db_conn)
		{
		   $query = 'select PROFILE_ID, FIELD_ID, FIELD_VALUE from PROFILE_CUSTOM_FIELD_VALUES order by PROFILE_ID';
		   $result = $this->db_conn->Execute($query);

           if($result) {
			   if(!$result->EOF)
			   {
					while(!$result->EOF) {
						$curr_cus_field_details = array();
						$profile_id = $result->fields[0];
						$field_id = $result->fields[1];
						$field_value = $result->fields[2];
						$all_field_values[] = array($profile_id, $field_id, $field_value);

						$result->MoveNext();
					}
					$toReturn[0] = 1;
					$toReturn[1] = $all_field_values;
			   }
            }
        }
		return $toReturn;
	}
}
?>