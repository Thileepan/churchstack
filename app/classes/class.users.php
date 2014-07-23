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

	public function addNewUser($church_id, $user_name, $email, $password, $role_id=1, $user_status=1)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "There was some error while trying to add a new user.";
		if($this->isUserAlreadyExists($user_name, $email)) {
			$to_return[0] = 0;
			$to_return[1] = "The specified username/email already exists in the system.";
			return $to_return;
		}
		$curr_time = time();
		if($this->db_conn)
		{
			$user_unique_hash = strtoupper(md5($curr_time.$church_id.rand(1, 10000).rand(1, 10000).$church_id.$email.$user_name));
			if(trim($password) == "") {
				$random_password = md5($curr_time.rand(1, 10000).rand(1, 10000).$email.$user_name);
				$password = $random_password;
			}
			$query = 'insert into USER_DETAILS (USER_ID, CHURCH_ID, USER_NAME, EMAIL, ROLE_ID, PASSWORD, UNIQUE_HASH, STATUS) values(?,?,?,?,?,?,?,?)';
			$result = $this->db_conn->Execute($query, array(0,$church_id,$user_name,$email,1,$password,$user_unique_hash,$user_status));
			if($result) {
				$query_1 = 'select USER_ID from USER_DETAILS where UNIQUE_HASH=? limit 1';
				$result_1 = $this->db_conn->Execute($query_1, array($user_unique_hash));
				if($result_1) {
					if(!$result_1->EOF) {
						$user_id = $result_1->fields[0];
						if($user_id > 0) {
							$toReturn[0] = 1;
							$toReturn[1] = "User added successfully";
							$toReturn[2] = array("user_id"=>$user_id);
						}
					}
				}
			}
		}
		return $toReturn;
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
	
	public function isUserAlreadyExists($user_name, $email)
	{
		if($this->db_conn)
		{
			$user_found = false;
			$query = 'select USER_ID from USER_DETAILS where EMAIL=? or USER_NAME=? limit 1';
			$result = $this->db_conn->Execute($query, array($user_name, $user_name));
			if($result) {
				if(!$result->EOF) {
					$user_found = true;
				}
			}

			if(!$user_found)
			{
				$query_1 = 'select USER_ID from USER_DETAILS where EMAIL=? or USER_NAME=? limit 1';
				$result_1 = $this->db_conn->Execute($query_1, array($email, $email));
				if($result_1) {
					if(!$result_1->EOF) {
						$user_found = true;
					}
				}
			}

			return $user_found;
		}
		return false;
	}

	public function isAuthenticatedUser($email, $password)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Unable to validate the account credentials, please contact support to resolve this issue.";
		if($this->db_conn)
		{
			$query = 'select USER_ID, CHURCH_ID, USER_NAME, EMAIL, ROLE_ID, PASSWORD, UNIQUE_HASH, PASSWORD_RESET_HASH, PASSWORD_RESET_EXPIRY, STATUS from USER_DETAILS where EMAIL=? and PASSWORD=? limit 1';
			$result = $this->db_conn->Execute($query, array($email, $password));
			if($result) {
				if(!$result->EOF) {
					$user_id = $result->fields[0];
					$church_id = $result->fields[1];
					$user_name = $result->fields[2];
					$email_output = $result->fields[3];
					$role_id = $result->fields[4];
					$password = $result->fields[5];
					$status = $result->fields[9];
					$user_details_array = array($user_id, $church_id, $user_name, $email_output, $role_id, $password, $status);

					$to_return[0] = 1;
					$to_return[1] = "Login successful";
					$to_return[2] = $user_details_array;
				} else {
					$to_return[0] = 0;
					$to_return[1] = "Invalid login credentials, unable to log you in.";
				}
			} else {
				$to_return[0] = 0;
				$to_return[1] = "Invalid authentication details, unable to log you in.";
			}
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to get the system handle to validate the account credentials";
		}
		return $to_return;
	}

//Following were added by Nesan
	public function signUpWithChurchDetails($church_name, $church_location, $first_name, $middle_name, $last_name, $email, $mobile)
	{
        @include_once($this->APPLICATION_PATH . 'db/dbutil.php');
        @include_once($this->APPLICATION_PATH . 'classes/class.church.php');
        @include_once($this->APPLICATION_PATH . 'classes/class.license.php');

		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was some error in signing up the user.";
		//Checking if the user already exists...
		if($this->isUserAlreadyExists($email, $email)) {
			$toReturn[0] = 0;
			$toReturn[1] = "The email you have entered already exists in the system. If you have already signed up earier, you can directly login to your account and get access to it.";
			return $toReturn;
		}

		//Create a new church
		$currency_id = 1;//CHANGE THIS LATER
		$country_id = 226;//Defaulting to USA; CHANGE THIS LATER
		$church_obj = new Church($this->APPLICATION_PATH);
		$church_result = $church_obj->addChurchInformation($church_name, "", $church_location, "", $mobile, $email, "", $currency_id, $country_id);
		if($church_result[0] == 0) {
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to create a dedicated setup. ".$church_result[1];
			return $toReturn;
		}

		//Create a new sharded database
		if($church_result[0] == 1) {
			$church_id = $church_result[2]["church_id"];
			$sharded_db_name = trim($church_result[2]["sharded_database"]);
			$sharded_result = createShardedDB($this->APPLICATION_PATH, $sharded_db_name);
			if($sharded_result[0]==1)
			{
				//Create a new user now
				$user_result = $this->addNewUser($church_id, $email, $email, "", 1, 1);//Adding a church Admin
				if($user_result[0]==1) {
					$user_id = $user_result[2]["user_id"];

					//Put initial trial license entry
					$lic_obj = new License($this->APPLICATION_PATH);
					$lic_obj->setChurchID($church_id);
					$license_result = $lic_obj->putInitialTrialLicenseEntry();
					if($license_result[0]==1)
					{
						$toReturn[0] = 1;
						$toReturn[1] = "Signup is successful!";
						$toReturn[2] = array("user_id"=>$user_id, "church_id"=>$church_id, "sharded_database"=>$sharded_db_name);
						$signup_details = array();
						$signup_details["customer_email"] = $email;
						$signup_details["first_name"] = $first_name;
						$signup_details["last_name"] = $last_name;
						$signup_details["church_name"] = $church_name;
						$signup_details["church_addr"] = $church_location;
						$welcome_email_result = $this->sendSignupWelcomeEmail($signup_details);
						return $toReturn;
					}
					else
					{
						$toReturn[0] = 0;
						$toReturn[1] = "An error occurred while creating the trial account";
					}
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "Unable to create the user. ".$user_result[1];
				}
			}
			else
			{
				$toReturn[0] = 0;
				$toReturn[1] = "Unable to create a dedicated setup. ".$sharded_result[1];
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

						$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_DONOTREPLY);
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

	public function sendSignupWelcomeEmail($signup_details)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$welcome_template_file = $this->APPLICATION_PATH."templates/email/welcome.html";
		$welcome_letter = "";
		if(file_exists($welcome_template_file))
		{
			$welcome_letter = trim(file_get_contents($welcome_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the welcome email";
		}

		//Replacing place holder with values
		$welcome_letter = str_replace("{{CUSTOMER_EMAIL}}", $signup_details["customer_email"], $welcome_letter);
		$welcome_letter = str_replace("{{PRODUCT_WEBSITE}}", PRODUCT_WEBSITE, $welcome_letter);
		$welcome_letter = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $welcome_letter);
		$welcome_letter = str_replace("{{FIRST_NAME}}", $signup_details["first_name"], $welcome_letter);
		$welcome_letter = str_replace("{{LAST_NAME}}", $signup_details["last_name"], $welcome_letter);
		$welcome_letter = str_replace("{{CHURCH_NAME}}", $signup_details["church_name"], $welcome_letter);
		$welcome_letter = str_replace("{{CHURCH_ADDRESS}}", $signup_details["church_addr"], $welcome_letter);
		$welcome_letter = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $welcome_letter);

		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_INFO);
		$recipients = array();
		$recipients['to_address'] = $signup_details["customer_email"];
		$subject = "Welcome to ".PRODUCT_WEBSITE;
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($welcome_letter);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Welcome email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send welcome email to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}
}
?>