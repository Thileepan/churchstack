<?php
//class to handle users usage

class Users
{
	protected $db_conn;
	private $APPLICATION_PATH;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, false);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
	}

	public function getAllUsers()
	{
		$user_details = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from USER_DETAILS');
		   
		   if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $user_id = $result->fields[0];
						$church_id = $result->fields[1];
                        $user_name = $result->fields[2];
                        $role_id = $result->fields[3];
                        $password = $result->fields[4];
						$user_status = $result->fields[5];
						$user_details[] = array($user_id, $church_id, $user_name, $role_id, $password, $user_status);
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $user_details;
	}

	public function getUserInformation($user_id)
	{
		$user_info = array();
		if($this->db_conn)
		{
		   $query = 'select * from USER_DETAILS where USER_ID=?';
		   $result = $this->db_conn->Execute($query, array($user_id));
            
           if($result) {
                if(!$result->EOF) {
					$user_id = $result->fields[0];
					$church_id = $result->fields[1];
					$user_name = $result->fields[2];
					$role_id = $result->fields[3];
					$password = $result->fields[4];
					$user_status = $result->fields[5];
					$user_info = array($user_id, $church_id, $user_name, $role_id, $password, $user_status);
				}
            }
        }
		return $user_info;
	}

	public function getUserInformationUsingName($user_name)
	{
		$user_info = array();
		if($this->db_conn)
		{
		   $query = 'select * from USER_DETAILS where USER_NAME=?';
		   $result = $this->db_conn->Execute($query, array($user_name));
            
           if($result) {
                if(!$result->EOF) {
					$user_id = $result->fields[0];
					$church_id = $result->fields[1];
					$user_name = $result->fields[2];
					$role_id = $result->fields[3];
					$password = $result->fields[4];
					$user_status = $result->fields[5];
					$user_info = array($user_id, $church_id, $user_name, $role_id, $password, $user_status);
				}
            }
        }
		return $user_info;
	}

	public function addNewUser($church_id, $user_name, $password, $role_id=1, $user_status=1)
	{
		if($this->db_conn)
		{
			$query = 'insert into USER_DETAILS (CHURCH_ID, USER_NAME, ROLE_ID, PASSWORD, STATUS) values (?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($church_id, $user_name, $role_id, $password, $user_status));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateUser($user_id, $user_name, $password, $role_id=1, $user_status=1)
	{
		if($this->db_conn)
		{
			$query = 'update USER_DETAILS set USER_NAME=?, ROLE_ID=?, PASSWORD=?, STATUS=? where USER_ID=?';
			$result = $this->db_conn->Execute($query, array($user_name, $role_id, $password, $user_status, $user_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteUser($user_id)
	{
		if($this->db_conn)
		{
			$query = 'delete from USER_DETAILS where USER_ID=?';
			$result = $this->db_conn->Execute($query, array($user_id));
			if($result) {
				return true;
			}
		}
		return false;
	}
	
	public function isUserAlreadyExists($user_name)
	{
		if($this->db_conn)
		{
			$query = 'select * from USER_DETAILS where USER_NAME=?';
			$result = $this->db_conn->Execute($query, array($user_name));
			if($result) {
				if(!$result->EOF) {
					return true;
				}
			}
		}
		return false;
	}

	public function isAuthenticatedUser($user_name, $password)
	{
		if($this->db_conn)
		{
			$query = 'select * from USER_DETAILS where USER_NAME=? and PASSWORD=? and STATUS=1';
			//$password = $this->db_conn->qstr($password);
			$result = $this->db_conn->Execute($query, array($user_name, $password));
//			$result = $this->db_conn->Execute($query);
			//echo 'SKTGR:::'.$this->db_conn->ErrorMsg();
			if($result) {
				if(!$result->EOF) {
					return true;
				}
			}
		}
		return false;
	}
}
?>