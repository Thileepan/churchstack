<?php

class Church
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
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, false);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
	}

	public function addChurchInformation($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website)
	{
		if($this->db_conn)
		{
			$query = 'insert into CHURCH_DETAILS (CHURCH_NAME, DESCRIPTION, ADDRESS, LANDLINE, MOBILE, EMAIL, WEBSITE, SIGNUP_TIME, LAST_UPDATE_TIME) values (?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website));
			//echo "Error:::".$this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function updateChurchInformation($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $last_modified_time)
	{
		if($this->db_conn)
		{
			$query = 'update CHURCH_DETAILS set CHURCH_NAME=?, DESCRIPTION=?, ADDRESS=?, LANDLINE=?, MOBILE=?, EMAIL=?, WEBSITE=?, LAST_UPDATE_TIME=? where CHURCH_ID=?';
			$result = $this->db_conn->Execute($query, array($church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $last_modified_time, $church_id));
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function getChurchInformation()
	{
		$church_details = array();
		if($this->db_conn)
		{
		   $query = 'select * from CHURCH_DETAILS';
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
					$church_id = $result->fields[0];
					$church_name = $result->fields[1];
					$church_desc = $result->fields[2];
					$church_addr = $result->fields[3];
					$landline = $result->fields[4];
					$mobile = $result->fields[5];
					$email = $result->fields[6];
					$website = $result->fields[7];
					$signup_time = $result->fields[8];
					$last_modified_time = $result->fields[9];
					$sharded_db = $result->fields[10];
					$church_details = array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_modified_time, $sharded_db);
				}
            }
        }
		return $church_details;
	}

}

?>