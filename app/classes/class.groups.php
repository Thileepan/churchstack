<?php

class Groups
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

	public function addGroup($group_name, $desc)
	{
		if($this->db_conn)
		{
			$query = 'insert into GROUP_DETAILS (GROUP_NAME, DESCRIPTION) values (?, ?)';
			$result = $this->db_conn->Execute($query, array($group_name, $desc));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateGroup($group_id, $group_name, $desc)
	{
		if($this->db_conn)
		{
			$query = 'update GROUP_DETAILS set GROUP_NAME=?, DESCRIPTION=? where GROUP_ID=?';
			$result = $this->db_conn->Execute($query, array($group_name, $desc, $group_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteGroup($group_id)
	{
		$query = 'delete from GROUP_DETAILS where GROUP_ID=?';
		$result = $this->db_conn->Execute($query, array($group_id));
		if($result) {
			return true;
		}
		return false;
	}

	public function addGroupMembers($group_id, $profile_id_list)
	{
		if($this->db_conn)
		{
			$query = 'insert into GROUP_MEMBERS (GROUP_ID, PROFILE_ID) values ';
			$total_profiles = COUNT($profile_id_list);
			$value_list = array();
			for($i=0; $i<$total_profiles; $i++)
			{
				if($i != 0) {
					$query .= ',';
				}
				$query .= ' (?, ?)';

				$value_list[] = $group_id;
				$value_list[] = $profile_id_list[$i];
			}
			$result = $this->db_conn->Execute($query, $value_list);
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateGroupMembers()
	{
		if($this->db_conn)
		{
			$query = 'update GROUP_MEMBERS';
		}
	}
	
	public function getGroupInformation($group_id)
	{
		$group_details = array();
		if($this->db_conn)
		{
			$query = 'select * from GROUP_DETAILS where GROUP_ID = ?';
			$result = $this->db_conn->Execute($query, array($group_id));

			if($result) {
                if(!$result->EOF) {
					$group_id = $result->fields[0];
					$group_name = $result->fields[1];
					$description = $result->fields[2];
					$group_details = array($group_id, $group_name, $description);
				}
            }
		}
		return $group_details;
	}

	public function getListOfGroups()
	{
		$group_details = array();
		if($this->db_conn)
		{
			$query = 'select * from GROUP_DETAILS';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $group_id = $result->fields[0];
                        $group_name = $result->fields[1];
						$description = $result->fields[2];
						$group_details[] = array($group_id, $group_name, $description);
                        
						$result->MoveNext();
                    }
                }
            }
		}
		return $group_details;
	}

	public function getListOfGroupMembers($group_id)
	{
		$group_members = array();
		if($this->db_conn)
		{
			$query = 'select a.PROFILE_ID, b.NAME from GROUP_MEMBERS as a, PROFILE_DETAILS as b where a.PROFILE_ID=b.PROFILE_ID and a.GROUP_ID=?';
			$result = $this->db_conn->Execute($query, array($group_id));
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $profile_id = $result->fields[0];
                        $profile_name = $result->fields[1];
						$group_members[] = array($profile_id, $profile_name);
                        
						$result->MoveNext();
                    }
                }
            }
		}
		return $group_members;
	}
}

?>