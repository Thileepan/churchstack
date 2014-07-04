<?php
//class to handle subscription usage

class Subscription
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

	public function getAllSubscriptionFields()
	{
		$sub_fields = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from SUBSCRIPTION_FIELDS');
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $field_id = $result->fields[0];
                        $field_name = $result->fields[1];
                        $hide = $result->fields[2];
                        $sub_fields[] = array($field_id, $field_name, $hide);
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $sub_fields;
	}

	public function getActiveSubscriptionFields()
	{
		$sub_fields = array();
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select * from SUBSCRIPTION_FIELDS where HIDE=0');
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $field_id = $result->fields[0];
                        $field_name = $result->fields[1];
                        $hide = $result->fields[2];
                        $sub_fields[] = array($field_id, $field_name, $hide);
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $sub_fields;
	}

	public function addNewField($field_name)
	{
		if($this->db_conn)
		{
			$query = 'insert into SUBSCRIPTION_FIELDS (FIELD_NAME, HIDE) values (?, ?)';
			$result = $this->db_conn->Execute($query, array($field_name, 0));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateField($field_id, $field_name)
	{
		if($this->db_conn)
		{
			$query = 'update SUBSCRIPTION_FIELDS set FIELD_NAME=? where FIELD_ID=?';
			$result = $this->db_conn->Execute($query, array($field_name, $field_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function hideOrShowField($field_id, $is_hide)
	{
		if($this->db_conn)
		{
			$query = 'update SUBSCRIPTION_FIELDS set HIDE=? where FIELD_ID=?';
			$result = $this->db_conn->Execute($query, array($is_hide, $field_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function addNewSubscription($profile_id, $date_of_sub, $val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $val20, $total_amount)
	{
		if($this->db_conn)
		{
			$query = 'insert into SUBSCRIPTION_DETAILS (PROFILE_ID, DATE_OF_SUBSCRIPTION, SUB_FIELD_1, SUB_FIELD_2, SUB_FIELD_3, SUB_FIELD_4, SUB_FIELD_5, SUB_FIELD_6, SUB_FIELD_7, SUB_FIELD_8, SUB_FIELD_9, SUB_FIELD_10, SUB_FIELD_11, SUB_FIELD_12, SUB_FIELD_13, SUB_FIELD_14, SUB_FIELD_15, SUB_FIELD_16, SUB_FIELD_17, SUB_FIELD_18, SUB_FIELD_19, SUB_FIELD_20, TOTAL_AMOUNT) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($profile_id, $date_of_sub, $val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $val20, $total_amount));
			//echo "SKTG:::".$this->db_conn->ErrorMsg();exit;
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function updateSubscription($subscription_id, $profile_id, $date_of_sub, $val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $val20, $total_amount)
	{
		if($this->db_conn)
		{
			$query = 'update SUBSCRIPTION_DETAILS set PROFILE_ID=?, DATE_OF_SUBSCRIPTION=?, SUB_FIELD_1=?, SUB_FIELD_2=?, SUB_FIELD_3=?, SUB_FIELD_4=?, SUB_FIELD_5=?, SUB_FIELD_6=?, SUB_FIELD_7=?, SUB_FIELD_8=?, SUB_FIELD_9=?, SUB_FIELD_10=?, SUB_FIELD_11=?, SUB_FIELD_12=?, SUB_FIELD_13=?, SUB_FIELD_14=?, SUB_FIELD_15=?, SUB_FIELD_16=?, SUB_FIELD_17=?, SUB_FIELD_18=?, SUB_FIELD_19=?, SUB_FIELD_20=?, TOTAL_AMOUNT=? where SUBSCRIPTION_ID=?';
			$result = $this->db_conn->Execute($query, array($profile_id, $date_of_sub, $val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $val20, $total_amount, $subscription_id));
			//echo "SKTG:::".$this->db_conn->ErrorMsg();exit;
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function deleteSubscription($subscription_id)
	{
		if($this->db_conn)
		{
			$result = $this->db_conn->Execute('delete from SUBSCRIPTION_DETAILS where SUBSCRIPTION_ID=?', array($subscription_id));
//			echo "SKTG:::".$this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getAllSubscriptions($profile_id)
	{
		$subcription_details = array();
		if($this->db_conn)
		{
			$query = 'select a.SUBSCRIPTION_ID, a.PROFILE_ID, b.NAME, a.DATE_OF_SUBSCRIPTION, a.TOTAL_AMOUNT, b.UNIQUE_ID from SUBSCRIPTION_DETAILS as a, PROFILE_DETAILS as b where a.PROFILE_ID=b.PROFILE_ID';
			if($profile_id > 0)
			{
				$query .= ' and a.PROFILE_ID='.$profile_id;
			}
			$query .= ' order by a.DATE_OF_SUBSCRIPTION asc';
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $subscription_id = $result->fields[0];
                        $profile_id = $result->fields[1];
						$profile_name = $result->fields[2];
                        $date = $result->fields[3];
                        $total_amount = $result->fields[4];
						$unique_id = $result->fields[5];
						/*						
						$val2 = $result->fields[4];
						$val3 = $result->fields[5];
						$val4 = $result->fields[6];
                        $val5 = $result->fields[7];
                        $val6 = $result->fields[8];
                        $val7 = $result->fields[9];
                        $val8 = $result->fields[10];
						$val9 = $result->fields[11];
						$val10 = $result->fields[12];
						$val11 = $result->fields[13];
						$val12 = $result->fields[14];
						$val12 = $result->fields[15];
						$val13 = $result->fields[16];
						$val14 = $result->fields[17];
						$val15 = $result->fields[18];
						$val16 = $result->fields[19];
						$val17 = $result->fields[20];
						$val18 = $result->fields[21];
						$val19 = $result->fields[22];
						$val20 = $result->fields[23];
						*/

						//$subcription_details[] = array($subscription_id, $profile_id, $date, $val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $val20); //(0-22)
						$subcription_details[] = array($subscription_id, $profile_id, $profile_name, $date, $total_amount, $unique_id);
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $subcription_details;
	}

	public function getSubscriptionInformation($subscription_id)
	{
		$subcription_details = array();
		if($this->db_conn)
		{
		   $query = 'select * from SUBSCRIPTION_DETAILS where SUBSCRIPTION_ID=?';
		   $result = $this->db_conn->Execute($query, array($subscription_id));
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
						$subscription_id = $result->fields[0];
                        $profile_id = $result->fields[1];
                        $date = $result->fields[2];
                        $val1 = $result->fields[3];
						$val2 = $result->fields[4];
						$val3 = $result->fields[5];
						$val4 = $result->fields[6];
                        $val5 = $result->fields[7];
                        $val6 = $result->fields[8];
                        $val7 = $result->fields[9];
                        $val8 = $result->fields[10];
						$val9 = $result->fields[11];
						$val10 = $result->fields[12];
						$val11 = $result->fields[13];
						$val12 = $result->fields[14];
						$val13 = $result->fields[15];
						$val14 = $result->fields[16];
						$val15 = $result->fields[17];
						$val16 = $result->fields[18];
						$val17 = $result->fields[19];
						$val18 = $result->fields[20];
						$val19 = $result->fields[21];
						$total_amount = $result->fields[22];
						
						//$subcription_details[] = array($subscription_id, $profile_id, $date, $val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $total_amount); //(0-22)
						$subcription_details[] = array($val1, $val2, $val3, $val4, $val5, $val6, $val7, $val8, $val9, $val10, $val11, $val12, $val13, $val14, $val15, $val16, $val17, $val18, $val19, $date); //(0-19)
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $subcription_details;
	}

	public function getMonthlySubscriptionAmount($month)
	{
		$total_amount = 0;
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select SUM(TOTAL_AMOUNT) from SUBSCRIPTION_DETAILS where MONTH(DATE_OF_SUBSCRIPTION) = ?', array($month));
            
           if($result) {
                if(!$result->EOF) {
                    if($result->fields[0] != NULL)
                    {
                        $total_amount = $result->fields[0];
                    }
                }
            }
        }
		return $total_amount;
	}

	public function getProfileSubscriptionTotalAmount($profile_id, $subscription_field_id)
	{
		$total_amount = 0;
		if($this->db_conn)
		{
		   $column_name = 'SUB_FIELD_'.$subscription_field_id;
		   $query = 'select SUM('.$column_name.') from SUBSCRIPTION_DETAILS where PROFILE_ID = ?';
		   $result = $this->db_conn->Execute($query, array($profile_id));
            
           if($result) {
                if(!$result->EOF) {
                    if($result->fields[0] != NULL)
                    {
                        $total_amount = $result->fields[0];
                    }
                }
            }
        }
		return $total_amount;
	}
}