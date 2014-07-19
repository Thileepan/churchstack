<?php

class Funds
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

	public function addFund($fund_name, $fund_description)
	{
		if($this->db_conn)
		{
			$query = 'insert into FUND_DETAILS (FUND_NAME, FUND_DESCRIPTION) values (?, ?)';
			$result = $this->db_conn->Execute($query, array($fund_name, $fund_description));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateFund($fund_id, $fund_name, $fund_description)
	{
		if($this->db_conn)
		{
			$query = 'update FUND_DETAILS set FUND_NAME=?, FUND_DESCRIPTION=? where FUND_ID=?';
			$result = $this->db_conn->Execute($query, array($fund_name, $fund_description, $fund_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function addBatch()
	{
		if($this->db_conn)
		{
			$query = 'insert into BATCH_DETAILS (BATCH_NAME, BATCH_DESCRIPTION) values (?, ?)';
			$result = $this->db_conn->Execute($query, array($batch_name, $batch_description));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateBatch()
	{
		if($this->db_conn)
		{
			$query = 'update BATCH_DETAILS set BATCH_NAME=?, BATCH_DESCRIPTION=? where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_name, $batch_description, $batch_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function addContribution($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $last_update_time, $last_update_user_id, $last_update_user_name)
	{
		if($this->db_conn)
		{
			$query = 'insert into CONTRIBUTION_DETAILS (CONTRIBUTION_DATE, BATCH_ID, PROFILE_ID, TRANSACTION_TYPE, PAYMENT_MODE, REFERENCE_NUMBER, LAST_UPDATE_TIME, LAST_UPDATE_USER_ID, LAST_UPDATE_USER_NAME) values (?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $last_update_time, $last_update_user_id, $last_update_user_name));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateContribution($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $last_update_time, $last_update_user_id, $last_update_user_name, $contribution_id)
	{
		if($this->db_conn)
		{
			$query = 'update CONTRIBUTION_DETAILS set CONTRIBUTION_DATE=?, BATCH_ID=?, PROFILE_ID=?, TRANSACTION_TYPE=?, PAYMENT_MODE=?, REFERENCE_NUMBER=?, LAST_UPDATE_TIME=?, LAST_UPDATE_USER_ID=?, LAST_UPDATE_USER_NAME=? where CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $last_update_time, $last_update_user_id, $last_update_user_name, $contribution_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function addContributionSplit($contribution_id, $fund_id, $amount, $notes)
	{
		if($this->db_conn)
		{
			$query = 'insert into CONTRIBUTION_SPLIT_DETAILS (CONTRIBUTION_ID, FUND_ID, AMOUNT, NOTES) values (?, ?, ?, ?)';
			$result = $this->db_conn->Execute($contribution_id, array($fund_id, $amount, $notes));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateContributionSplit($contribution_split_id, $contribution_id, $fund_id, $amount, $notes)
	{
		if($this->db_conn)
		{
			$query = 'update CONTRIBUTION_SPLIT_DETAILS set CONTRIBUTION_ID=?, FUND_ID=?, AMOUNT=?, NOTES=? where CONTRIBUTION_SPLIT_ID=? and CONTRIBUTION_ID=?;
			$result = $this->db_conn->Execute($query, array($contribution_id, $fund_id, $amount, $notes, $contribution_split_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getAllFunds()
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the funds.';

		if($this->db_conn)
		{
			$fund_details = array();
			$query = 'select FUND_ID, FUND_NAME, FUND_DESCRIPTION from FUND_DETAILS';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $fund_id = $result->fields[0];
                        $fund_name = $result->fields[1];
						$fund_description = $result->fields[2];
						$fund_details[] = array($fund_id, $fund_name, $fund_description);

						$result->MoveNext();

						$return_data[0] = 1;
						$return_data[1] = $fund_details;
                    }
                }
				else
				{
					$return_data[0] = 0;
					$return_data[1] = 'No fund is available.';
				}
            }
		}
		return $return_data;
	}

	public function getFundInformation($fund_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to view the fund information.';
		
		if($this->db_conn)
		{
			$fund_details = array();
			$query = 'select FUND_NAME, FUND_DESCRIPTION from FUND_DETAILS where FUND_ID=?';
			$result = $this->db_conn->Execute($query, array($fund_id));
			
			if($result) {
                if(!$result->EOF) {
                    $fund_name = $result->fields[1];
					$fund_description = $result->fields[2];
					$fund_details = array($fund_id, $fund_name, $fund_description);

					$return_data[0] = 1;
					$return_data[1] = $fund_details;
				}
            }
		}
		return $fund_details;
	}

	public function getAllBatches()
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the contributions.';
		
		if($this->db_conn)
		{
			$batch_details = array();
			$query = 'select BATCH_ID, BATCH_NAME, BATCH_DESCRIPTION from BATCH_DETAILS';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $fund_id = $result->fields[0];
                        $fund_name = $result->fields[1];
						$fund_description = $result->fields[2];
						$batch_details[] = array($fund_id, $fund_name, $fund_description);

						$result->MoveNext();
                    }
					$return_data[0] = 1;
					$return_data[1] = $batch_details;
                }
				else
				{
					$return_data[0] = 0;
					$return_data[1] = 'No batch is available.';
				}
            }
		}
		return $return_data;
	}

	public function getBatchInformation($batch_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to view the batch information.';
		
		if($this->db_conn)
		{
			$batch_details = array();
			$query = 'select BATCH_NAME, BATCH_DESCRIPTION from BATCH_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
			
			if($result) {
                if(!$result->EOF) {
                    $batch_name = $result->fields[0];
					$batch_description = $result->fields[1];
					$batch_details = array($batch_id, $batch_name, $batch_description);
					
					$return_data[0] = 1;
					$return_data[1] = $batch_details;
				}
            }
		}
		return $return_data;
	}

	public function getAllContributions()
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the contributions.';
		
		if($this->db_conn)
		{
			$contribution_details = array();
			$query = 'select CONTRIBUTION_DATE, BATCH_ID, PROFILE_ID, TRANSACTION_TYPE, PAYMENT_MODE, REFERENCE_NUMBER, LAST_UPDATE_TIME, LAST_UPDATE_USER_ID, LAST_UPDATE_USER_NAME from CONTRIBUTION_DETAILS';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
					while(!$result->EOF)
					{
						$contribution_date = $result->fields[0];
						$batch_id = $result->fields[1];
						$profile_id = $result->fields[2];
						$transaction_type = $result->fields[3];
						$payment_mode = $result->fields[4];
						$reference_number = $result->fields[5];
						$last_update_time = $result->fields[6];
						$last_update_user_id = $result->fields[7];
						$last_update_user_name = $result->fields[8];
						$contribution_details[] = array($contribution_id, $contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $last_update_time, $last_update_user_id, $last_update_user_name);

						$result->MoveNext();
					}
					$return_data[0] = 1;
					$return_data[1] = $contribution_details;
				}
            } else {
				$return_data[0] = 0;
				$return_data[1] = 'No contribution is available.';
			}
		}
		return $return_data;
	}

	public function getContributionInformation($contribution_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to view the contribution';
		
		if($this->db_conn)
		{
			$contribution_details = array();
			$query = 'select CONTRIBUTION_DATE, BATCH_ID, PROFILE_ID, TRANSACTION_TYPE, PAYMENT_MODE, REFERENCE_NUMBER, LAST_UPDATE_TIME, LAST_UPDATE_USER_ID, LAST_UPDATE_USER_NAME from BATCH_DESCRIPTION where CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_id));
			
			if($result) {
                if(!$result->EOF) {
                    $contribution_date = $result->fields[0];
					$batch_id = $result->fields[1];
					$profile_id = $result->fields[2];
					$transaction_type = $result->fields[3];
					$payment_mode = $result->fields[4];
					$reference_number = $result->fields[5];
					$last_update_time = $result->fields[6];
					$last_update_user_id = $result->fields[7];
					$last_update_user_name = $result->fields[8];
					$contribution_details = array($contribution_id, $contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $last_update_time, $last_update_user_id, $last_update_user_name);

					$return_data[0] = 1;
					$return_data[1] = $contribution_split_details;
				}
            }
		}
		return $return_data;
	}

	public function getAllContributionSplits()
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the contribution splits';
		
		if($this->db_conn)
		{
			$contribution_split_details = array();
			$query = 'select CONTRIBUTION_SPLIT_ID, CONTRIBUTION_ID, FUND_ID, AMOUNT, NOTES from CONTRIBUTION_SPLIT_DETAILS';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
					while(!$result->EOF)
					{
						$contribution_split_id = $result->fields[0];
						$contribution_id = $result->fields[1];
						$fund_id = $result->fields[2];
						$amount = $result->fields[3];
						$notes = $result->fields[4];
						$contribution_split_details[] = array($contribution_split_id, $contribution_id, $fund_id, $amount, $notes);

						$result->MoveNext();
					}
					$return_data[0] = 1;
					$return_data[1] = $contribution_split_details;
				} else {
					$return_data[0] = 0;
					$return_data[1] = 'No contribution split is available';
				}
            }
		}
		return $return_data;
	}

	public function deleteFund($fund_id)
	{
		if($this->db_conn)
		{
			$query = 'delete * from FUND_DETAILS where FUND_ID=?';
			$result = $this->db_conn->Execute($query, array($fund_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteBatch($batch_id)
	{
		if($this->db_conn)
		{
			$query = 'delete * from BATCH_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteContribution($contribution_id)
	{
		if($this->db_conn)
		{
			$query = 'delete * from CONTRIBUTION_DETAILS where CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteContributionSplits($contribution_split_id)
	{
		if($this->db_conn)
		{
			$query = 'delete * from CONTRIBUTION_SPLIT_DETAILS where CONTRIBUTION_SPLIT_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_split_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function isFundUsedInContribution($fund_id)
	{
		if($this->db_conn)
		{
			$query = 'select FUND_ID CONTRIBUTION_SPLIT_DETAILS where FUND_ID=? limit 1';
			$result = $this->db_conn->Execute($query, array($fund_id));
			if($result) {
				if(!$result->EOF) {
					return true;
				}
			}
		}
		return false;
	}

	public function isBatchUsedInContribution($fund_id)
	{
		if($this->db_conn)
		{
			$query = 'select BATCH_ID CONTRIBUTION_DETAILS where BATCH_ID=? limit 1';
			$result = $this->db_conn->Execute($query, array($batch_id));
			if($result) {
				if(!$result->EOF){
					return true;
				}
			}
		}
		return false;
	}
}
?>