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
                        $email = $result->fields[3];
                        $role_id = $result->fields[4];
                        $password = $result->fields[5];
                        $unique_hash = $result->fields[6];
                        $password_reset_hash = $result->fields[7];
                        $password_reset_expiry = $result->fields[8];
						$user_status = $result->fields[9];
						$user_details[] = array($user_id, $church_id, $user_name, $email, $role_id, $password, $unique_hash, $password_reset_hash, $password_reset_expiry, $user_status);
                        
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

//Following were added by Nesan
	public function signUpWithChurchDetails($church_name, $church_location, $first_name, $middle_name, $last_name, $email, $mobile)
	{
		$toReturn = array();
		if($this->db_conn)
		{
			$query = 'select * from USER_DETAILS where EMAIL=? or USER_NAME=? limit 1';
			$result = $this->db_conn->Execute($query, array($email, $email));
			if($result) {
				if(!$result->EOF) {
					$toReturn[0] = 0;
					$toReturn[1] = "The email you have entered already exists in the system. If you have already signed up earier, you can directly login to your account and access your church data.";
					return $toReturn;
				}
			}

			//insert into church-details
			$curr_time = time();
			$currency_id = 1;//CHANGE THIS LATER
			$church_unique_hash = strtoupper(md5($curr_time.$church_name.rand(1, 10000).rand(1, 10000)));
			
			$church_id = -1;
			$query_2 = 'insert into CHURCH_DETAILS values(?,?,?,?,?,?,?,?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,?,?,?)';
			$result_2 = $this->db_conn->Execute($query_2, array(0,$church_name,'',$church_location,'','','','',$curr_time,$curr_time,'',$currency_id,$church_unique_hash,1));
			if($result_2) {
				$query_3 = 'select CHURCH_ID from CHURCH_DETAILS where UNIQUE_HASH=?';
				$result_3 = $this->db_conn->Execute($query_3, array($church_unique_hash));
				if($result_3) {
					if(!$result_3->EOF) {
						$church_id = $result_3->fields[0];
						if($church_id > 0)
						{
							$sharded_database = 'CS_'.$church_id.'_CHURCHSTACK';
							$up_query = 'update CHURCH_DETAILS set SHARDED_DATABASE=? where CHURCH_ID=?';
							$up_result = $this->db_conn->Execute($up_query, array($sharded_database, $church_id));
							if($up_result) {
								//Successful
							}
							$user_unique_hash = strtoupper(md5($curr_time.$church_name.rand(1, 10000).rand(1, 10000).$church_id.$email));
							$random_password = md5($curr_time.rand(1, 10000).rand(1, 10000).$email);
							$query_4 = 'insert into USER_DETAILS values(?,?,?,?,?,?,?,?)';
							$result_4 = $this->db_conn->Execute($query_4, array(0,$church_id,$email,$email,1,$random_password,$user_unique_hash,1));
							if($result_4) {
								$query_5 = 'select USER_ID from USER_DETAILS where UNIQUE_HASH=?';
								$result_5 = $this->db_conn->Execute($query_5, array($user_unique_hash));
								if($result_5) {
									if(!$result_5->EOF) {
										$user_id = $result_5->fields[0];
										if($user_id > 0) {
											$toReturn[0] = 1;
											$toReturn[1] = "Signup is successful";
											$toReturn[2] = array("user_id"=>$user_id,"sharded_database"=>$sharded_database);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $toReturn;
	}

	public function sendPasswordResetEmail($email)
	{
		$email = trim($email);
		$toReturn = array();
		if($this->db_conn)
		{
			$query = 'select STATUS from USER_DETAILS where EMAIL=?';
			$result = $this->db_conn->Execute($query, array($email));
			if($result) {
				if(!$result->EOF) {
					$status = $result->fields[0];
					if($status != 1) {
						$toReturn[0] = 0;
						$toReturn[1] = "Your account is not active. If you wish to continue using your account using the same email id, please contact the support.";
					} else {
						//Constructing a hash to set in URL get value
						$curr_time = time();
						$pwd_rst_expiry_time = $curr_time+259200;//adding 3 days
						$hash_1 = md5($email.time().rand(1,10000).rand(1,10000));
						$hash_2 = md5($email.(time()+100).rand(1,10000).rand(1,10000));
						$hash_3 = md5($email.(time()+1000).rand(1,10000).rand(1,10000));
						$hash_for_url = strtoupper($hash_1.$hash_2.$hash_3);
						$hash_to_store = md5(md5($hash_for_url));
						$pwd_reset_url = "http://churchstack.com/account/verify.php?email=".$email."&key=".$hash_for_url;

						$query_2 = 'update USER_DETAILS set PASSWORD_RESET_HASH=?, PASSWORD_RESET_EXPIRY=? where EMAIL=?';
						$result_2 = $this->db_conn->Execute($query_2, array($hash_to_store, $pwd_rst_expiry_time, $email));
						if(!$result_2) {
							$toReturn[0] = 0;
							$toReturn[1] = "Error occurred while trying to prepare for resetting the password.";
							return $toReturn;
						}

						$email_obj = new Email($this->APPLICATION_PATH, "user_password_reset");
						$email_result = $email_obj->sendPasswordResetEmail($email, $pwd_reset_url);
						if($email_result[0]==1) {
							$toReturn[0] = 1;
							$toReturn[1] = "An email has been sent to <b>".$email."</b> which has the instructions to reset your password. Follow the instructions given in the mail to proceed further to reset the password";
						} else {
							$toReturn[0] = 0;
							$toReturn[1] = "An error occurred while sending the password reset email. Error : ".$email_result[1];
						}
					}
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "We could not recognize the email address you have entered. Kindly recheck the eamil you have entered.";
				}
			}
		}

		return $toReturn;
	}

	public function verifyPasswordResetURLValidity($email, $key)
	{
		$toReturn = array();
		$email = trim($email);
		$key = trim($key);
		$current_time = time();
		if(strlen($email) < 6 || strlen($key) < 96) {
			$toReturn[0] = 0;
			$toReturn[1] = "Invalid email or key specified";
		}
		$hash_stored = md5(md5($key));
		if($this->db_conn)
		{
			$query = 'select PASSWORD_RESET_EXPIRY from USER_DETAILS where EMAIL=? and PASSWORD_RESET_HASH=?';
			$result = $this->db_conn->Execute($query, array($email, $hash_stored));
			if($result) {
				if(!$result->EOF) {
					$pwd_expiry_time = $result->fields[0];
					if($current_time <= $pwd_expiry_time) {
						$toReturn[0] = 1;
						$toReturn[1] = "Successful email and key pair, proceed further to reset the password";
					} else {
						$toReturn[0] = 0;
						$toReturn[1] = "The password reset url has expired. Please create another request to reset the password and follow the instructions given in the email (which will be sent after submitting the request).";
					}
				}
				else
				{
					$toReturn[0] = 0;
					$toReturn[1] = "Invalid email & key pair. Please recheck the URL you have loaded.";
				}
			}
			else
			{
				$toReturn[0] = 0;
				$toReturn[1] = "Invalid email & key pair. Please recheck the URL you have loaded.";
			}
		}
	}
}
?>