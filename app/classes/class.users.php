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
	public function signUpWithChurchDetails($church_name, $church_location, $first_name, $middle_name, $last_name, $email, $mobile, $referrer_email_id, $password)
	{
        @include_once($this->APPLICATION_PATH . 'db/dbutil.php');
        @include_once($this->APPLICATION_PATH . 'classes/class.church.php');
        @include_once($this->APPLICATION_PATH . 'classes/class.license.php');
		@include_once($this->APPLICATION_PATH . 'plugins/thread/class.thread.php');

		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was some error in signing up the user.";

		//Checking if the referrer email id is valid
		$referrer_church_id = 0;
		if(trim($referrer_email_id) != "")
		{
			$referrer_result = $this->getChurchIDFromEmailID(trim($referrer_email_id));
			if($referrer_result[0] == 1)
			{
				$referrer_church_id = $referrer_result[1];
			}
			else
			{
				$toReturn[0] = 0;
				$toReturn[1] = "We could not find the referrer email address you have mentioned, kindly check the referrer email address you have specified once again. If you are not sure that the referrer email address is registered with us, leave the box empty and proceed further to signup.";
				return $toReturn;
			}
		}

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
		$church_result = $church_obj->addChurchInformation($church_name, "", $church_location, "", $mobile, $email, "", $currency_id, $country_id, $referrer_church_id);
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
				$user_result = $this->addNewUser($church_id, $email, $email, $password, 1, 1);//Adding a church Admin
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

						//$welcome_email_result = $this->sendSignupWelcomeEmail($signup_details);

						/************************************************************************************** /
						Sending email asynchronously
						/**************************************************************************************/
						$email_sending_file = __DIR__."/../notify/sendemail.php";//Take care of this part
						$email_sending_file = str_replace("\\", "/", $email_sending_file);
						$commands = array();

						$welcome_email_content = $this->sendSignupWelcomeEmail($signup_details, 1);
						$fromAddressType = "info";
						$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.urlencode($welcome_email_content[1][0]).' subject='.urlencode($welcome_email_content[1][1]).' emailBody='.urlencode($welcome_email_content[1][2]).' fromAddressType='.$fromAddressType;
						
						$referral_prog_email_content = $this->sendReferralProgramEmail($signup_details, 1);
						$fromAddressType = "info";
						$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$email_sending_file.' csvToEmails='.urlencode($referral_prog_email_content[1][0]).' subject='.urlencode($referral_prog_email_content[1][1]).' emailBody='.urlencode($referral_prog_email_content[1][2]).' fromAddressType='.$fromAddressType;

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
						/**************************************************************************************/

						return $toReturn;
					}
					else
					{
						//Remove church entries completely
						$this->removeChurchEntryOrderly($church_id, $sharded_db_name);
						$toReturn[0] = 0;
						$toReturn[1] = "An error occurred while creating the account";
					}
				} else {
					//Remove church entries completely
					$this->removeChurchEntryOrderly($church_id, $sharded_db_name);
					$toReturn[0] = 0;
					$toReturn[1] = "Unable to create the user. ".$user_result[1];
				}
			}
			else
			{
				//Remove church entries completely
				$this->removeChurchEntryOrderly($church_id, $sharded_db_name);
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
		$toReturn[0] = 0;
		$toReturn[1] = "The email address specified is not found in the system";
		if($this->db_conn)
		{
			$query = 'select STATUS from USER_DETAILS where EMAIL=? limit 1';
			$result = $this->db_conn->Execute($query, array($email));
			if($result) {
				if(!$result->EOF) {
					$status = $result->fields[0];
					if($status != 1) {
						$toReturn[0] = 0;
						$toReturn[1] = "Access to the system with the specified email address has been deactivated. If you wish to continue using your account using the same email id, please contact support.";
					} else {
						//Constructing a hash to set in URL get value
						$curr_time = time();
						$pwd_rst_expiry_time = $curr_time+259200;//adding 3 days
						$hash_1 = md5($email.time().rand(1,10000).rand(1,10000));
						$hash_2 = md5($email.(time()+100).rand(1,10000).rand(1,10000));
						$hash_for_url = strtoupper($hash_1.$hash_2);
						$hash_to_store = md5(md5($hash_for_url));
						$key_1 = base64_encode($email);
						$key_2 = base64_encode($hash_for_url);
						$pwd_reset_url = CS_LOGIN_WEBSITE;
						$last_character = substr($pwd_reset_url, strlen($pwd_reset_url)-1, 1);
						if($last_character != "/") {
							$pwd_reset_url .= "/";
						}
						$pwd_reset_url .= "user/resetpwd.php?key1=".$key_1."&key2=".$key_2;

						$query_2 = 'update USER_DETAILS set PASSWORD_RESET_HASH=?, PASSWORD_RESET_EXPIRY=? where EMAIL=?';
						$result_2 = $this->db_conn->Execute($query_2, array($hash_to_store, $pwd_rst_expiry_time, $email));
						if(!$result_2) {
							$toReturn[0] = 0;
							$toReturn[1] = "Error occurred while trying to prepare for resetting the password.";
							return $toReturn;
						}

						$email_result = $this->constructAndSendForgotPasswordEmail($email, $pwd_reset_url);
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
					$toReturn[1] = "We could not recognize the email address you have entered. Kindly recheck the email you have entered.";
				}
			}
		}

		return $toReturn;
	}

	public function verifyPasswordResetURLValidity($email, $key)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "The URL is invalid";
		$email = trim($email);
		$key = trim($key);
		$current_time = time();
		if(strlen($email) < 6 || strlen($key) < 64) {
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
		return $toReturn;
	}

	public function sendSignupWelcomeEmail($signup_details, $just_return_contents=0)
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

		$subject = "Welcome to ".PRODUCT_WEBSITE;
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = $signup_details["customer_email"];
			$contents_array[1] = $subject;
			$contents_array[2] = $welcome_letter;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}

		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_INFO);
		$recipients = array();
		$recipients['to_address'] = $signup_details["customer_email"];
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
	
	public function getChurchIDFromEmailID($email)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while trying to get the account info.";
		if($this->db_conn)
		{
		   $query = 'select CHURCH_ID from USER_DETAILS where EMAIL=? and ROLE_ID=1 and STATUS=1';
		   $result = $this->db_conn->Execute($query, array($email));
            
           if($result) {
                if(!$result->EOF) {
					$church_id = $result->fields[0];

					$toReturn[0] = 1;
					$toReturn[1] = $church_id;
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "No details associated with the account could be retrieved.";
				}
            } else {
				$toReturn[0] = 0;
				$toReturn[1] = "There was an error when fetching the account details";
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to get connection to the system.";
		}
		return $toReturn;
	}

	public function getChurchAdminDetails($church_id)
	{
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while trying to get the account info.";
		if($this->db_conn)
		{
		   $query = 'select USER_ID, CHURCH_ID, USER_NAME, EMAIL, ROLE_ID, UNIQUE_HASH, PASSWORD_RESET_HASH, PASSWORD_RESET_EXPIRY, STATUS from USER_DETAILS where CHURCH_ID=? and ROLE_ID=1';
		   $result = $this->db_conn->Execute($query, array($church_id));
            
           if($result) {
                if(!$result->EOF) {
					$admin_details_array = array();
					$user_id = $result->fields[0];
					$church_id = $result->fields[1];
					$user_name = $result->fields[2];
					$email = $result->fields[3];
					$role_id = $result->fields[4];
					$unique_hash = $result->fields[5];
					$password_reset_hash = $result->fields[6];
					$password_reset_expiry = $result->fields[7];
					$status = $result->fields[8];

					$admin_details_array = array($user_id, $church_id, $user_name, $email, $role_id, $unique_hash, $password_reset_hash, $password_reset_expiry, $status);

					$toReturn[0] = 1;
					$toReturn[1] = $admin_details_array;
				} else {
					$toReturn[0] = 0;
					$toReturn[1] = "No details associated with the account could be retrieved.";
				}
            } else {
				$toReturn[0] = 0;
				$toReturn[1] = "There was an error when fetching the account details";
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to get connection to the system.";
		}
		return $toReturn;
	}

	public function getUserInformationUsingEmail($email)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "The email address specified is not found in the system";
		if($this->db_conn)
		{
		   $query = 'select USER_ID, CHURCH_ID, USER_NAME, EMAIL, ROLE_ID, UNIQUE_HASH, PASSWORD_RESET_HASH, PASSWORD_RESET_EXPIRY, STATUS from USER_DETAILS where EMAIL=? limit 1';
		   $result = $this->db_conn->Execute($query, array($user_name));
            
           if($result) {
                if(!$result->EOF) {
					$user_info = array();
					$user_id = $result->fields[0];
					$church_id = $result->fields[1];
					$user_name = $result->fields[2];
					$email = $result->fields[3];
					$role_id = $result->fields[4];
					$unique_hash = $result->fields[5];
					$pwd_reset_hash = $result->fields[6];
					$pwd_reset_expiry = $result->fields[7];
					$status = $result->fields[8];
					$user_info = array($user_id, $church_id, $user_name, $email, $role_id, $unique_hash, $pwd_reset_hash, $pwd_reset_expiry, $status);
					$to_return[0] = 1;
					$to_return[1] = $user_info;
				}
            }
        }
		return $to_return;
	}

	public function constructAndSendForgotPasswordEmail($email, $pwd_reset_url)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$forgot_pwd_template_file = $this->APPLICATION_PATH."templates/email/forgotpassword.html";
		$forgot_pwd_letter = "";
		if(file_exists($forgot_pwd_template_file))
		{
			$forgot_pwd_letter = trim(file_get_contents($forgot_pwd_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the password reset email";
		}

		//Replacing place holder with values
		$forgot_pwd_letter = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $forgot_pwd_letter);
		$forgot_pwd_letter = str_replace("{{PRODUCT_WEBSITE}}", PRODUCT_WEBSITE, $forgot_pwd_letter);
		$forgot_pwd_letter = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $forgot_pwd_letter);
		$forgot_pwd_letter = str_replace("{{PASSWORD_RESET_LINK}}", $pwd_reset_url, $forgot_pwd_letter);
		$forgot_pwd_letter = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $forgot_pwd_letter);

		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_DONOTREPLY);
		$recipients = array();
		$recipients['to_address'] = $email;
		$subject = "Instructions : Resetting the password for your account in ".PRODUCT_WEBSITE;
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($forgot_pwd_letter);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Password reset email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send password reset email to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function changeUserPassword($email, $new_password, $is_from_forgot_password=0)
	{
		$to_return = array() ;
		$to_return[0] = 0;
		$to_return[1] = "Unable to set the password";
		if($this->db_conn)
		{
			$query = 'update USER_DETAILS set PASSWORD=? where EMAIL=?';
			$result = $this->db_conn->Execute($query, array($new_password, $email));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = "Password has been set successfully";

				if($is_from_forgot_password==1)//To make the forgot password link invalid
				{
					$pwd_reset_hash_to_set = md5(time().rand(1,1000).rand(1,1000).$new_password);
					$query_2 = 'update USER_DETAILS set PASSWORD_RESET_HASH=? where EMAIL=?';
					$result_2 = $this->db_conn->Execute($query_2, array($pwd_reset_hash_to_set, $email));
					if($result_2) {
						//Do something if really needed.
					}
				}
			}
		}
		return $to_return;
	}

	public function sendReferralProgramEmail($user_details, $just_return_contents=0)
	{
		@include_once($this->APPLICATION_PATH."classes/class.email.php");
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = "Message sending failed.";
		$referral_prog_template_file = $this->APPLICATION_PATH."templates/email/referralprogram.html";
		$referral_prog_letter = "";
		if(file_exists($referral_prog_template_file))
		{
			$referral_prog_letter = trim(file_get_contents($referral_prog_template_file));
		}
		else
		{
			$to_return[0] = 0;
			$to_return[1] = "Unable to prepare the referral program email";
		}

		//Replacing place holder with values
		$referral_prog_letter = str_replace("{{CUSTOMER_EMAIL}}", $user_details["customer_email"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{PRODUCT_WEBSITE}}", PRODUCT_WEBSITE, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{SUPPORT_EMAIL}}", SUPPORT_EMAIL, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{FIRST_NAME}}", $user_details["first_name"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{LAST_NAME}}", $user_details["last_name"], $referral_prog_letter);
		$referral_prog_letter = str_replace("{{PRODUCT_NAME}}", PRODUCT_NAME, $referral_prog_letter);
		$referral_prog_letter = str_replace("{{CS_LOGIN_WEBSITE}}", CS_LOGIN_WEBSITE, $referral_prog_letter);

		$subject = PRODUCT_NAME."'s Referral Program";
		if($just_return_contents==1)
		{
			$contents_array = array();
			$contents_array[0] = $user_details["customer_email"];
			$contents_array[1] = $subject;
			$contents_array[2] = $referral_prog_letter;
			$to_return[0] = 1;
			$to_return[1] = $contents_array;
			return $to_return;
		}

		//Set and Send Email		
		$email_obj = new Email($this->APPLICATION_PATH, EMAIL_FROM_INFO);
		$recipients = array();
		$recipients['to_address'] = $user_details["customer_email"];
		$email_obj->setRecipients($recipients);
		$email_obj->setSubject($subject);
		$email_obj->setBody($referral_prog_letter);
		$email_result = $email_obj->sendEmail();
		if($email_result[0]==1) {
			$to_return[0] = 1;
			$to_return[1] = "Referral program email sent.";
		} else {
			$to_return[0] = 0;
			$to_return[1] = "Unable to send referral program email to the specified email address. ".$email_result[1];
		}
		return $to_return;
	}

	public function removeChurchEntryOrderly($church_id, $sharded_db_name)
	{
		if($this->db_conn)
		{
			$query = 'delete from LICENSE_DETAILS where CHURCH_ID=?';
			$result = $this->db_conn->Execute($query, array($church_id));
			if($result) {
			}

			$query_1 = 'delete from USER_DETAILS where CHURCH_ID=?';
			$result_1 = $this->db_conn->Execute($query_1, array($church_id));
			if($result_1) {
			}

			$query_2 = 'delete from CHURCH_DETAILS where CHURCH_ID=?';
			$result_2 = $this->db_conn->Execute($query_2, array($church_id));
			if($result_2) {
			}

			if($sharded_db_name != "")
			{
				$query_3 = 'drop database '.$sharded_db_name;
				$result_3 = $this->db_conn->Execute($query_3);
				if($result_3) {
				}
			}
		}
	}
}
?>