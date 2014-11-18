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
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
	}

	public function addFund($fund_name, $fund_description, $fund_visibility)
	{
		if($this->db_conn)
		{
			$query = 'insert into FUND_DETAILS (FUND_NAME, FUND_DESCRIPTION, VISIBILITY) values (?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($fund_name, $fund_description, $fund_visibility));
//			echo $this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateFund($fund_id, $fund_name, $fund_description, $fund_visibility)
	{
		if($this->db_conn)
		{
			$query = 'update FUND_DETAILS set FUND_NAME=?, FUND_DESCRIPTION=?, VISIBILITY=? where FUND_ID=?';
			$result = $this->db_conn->Execute($query, array($fund_name, $fund_description, $fund_visibility, $fund_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteFund($fund_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to delete the fund.';

		if($this->db_conn)
		{
			$return_data = $this->isFundUsedInContribution($fund_id);
			//print_r($result_data);
			if($return_data[0] == 1)
			{
				if($return_data[2] == 0) {
					$query = 'delete from FUND_DETAILS where FUND_ID=?';
					$result = $this->db_conn->Execute($query, array($fund_id));
					if($result) {
						$return_data[0] = 1;
						$return_data[1] = 'Fund has been deleted successfully.';
					}
				} else {
					$return_data[0] = 0;
					$return_data[1] = 'Fund can\'t be deleted as it is being used in contribution.';
				}				
			}
		}
		return $return_data;
	}

	public function getAllFunds()
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the funds.';

		if($this->db_conn)
		{
			$fund_details = array();
			$query = 'select FUND_ID, FUND_NAME, FUND_DESCRIPTION, VISIBILITY from FUND_DETAILS';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $fund_id = $result->fields[0];
                        $fund_name = $result->fields[1];
						$fund_description = $result->fields[2];
						$fund_visibility = $result->fields[3];
						$fund_details[] = array($fund_id, $fund_name, $fund_description, $fund_visibility);						

						$result->MoveNext();						
                    }
					$return_data[0] = 1;
					$return_data[1] = $fund_details;
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
			$query = 'select FUND_ID, FUND_NAME, FUND_DESCRIPTION, VISIBILITY from FUND_DETAILS where FUND_ID=?';
			$result = $this->db_conn->Execute($query, array($fund_id));
			
			if($result) {
                if(!$result->EOF) {
                    $fund_name = $result->fields[1];
					$fund_description = $result->fields[2];
					$fund_visibility = $result->fields[3];
					$fund_details = array($fund_id, $fund_name, $fund_description, $fund_visibility);

					$return_data[0] = 1;
					$return_data[1] = $fund_details;
				}
            }
		}
		return $return_data;
	}

	public function updateFundVisibility($fund_id, $visibility)
	{
		if($this->db_conn)
		{
			$query = 'update FUND_DETAILS set VISIBILITY=? where FUND_ID=?';
			$result = $this->db_conn->Execute($query, array($visibility, $fund_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function isFundUsedInContribution($fund_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to get the fund information.';

		if($this->db_conn)
		{
			$query = 'select FUND_ID from CONTRIBUTION_SPLIT_DETAILS where FUND_ID=? limit 1';
			$result = $this->db_conn->Execute($query, array($fund_id));
			//echo $this->db_conn->ErrorMsg();
			if($result) {
				if(!$result->EOF) {
					$return_data[0] = 1;
					$return_data[1] = 'Fund is used in contribution.';
					$return_data[2] = 1; //in use;
				} else {
					$return_data[0] = 1;
					$return_data[1] = 'Fund is used in contribution.';
					$return_data[2] = 0; //not in use;
				}
			}
		}
		return $return_data;
	}

	public function addBatch($batch_name, $batch_description, $expected_amount, $batch_created_time)
	{
		if($this->db_conn)
		{
			$query = 'insert into BATCH_DETAILS (BATCH_NAME, BATCH_DESCRIPTION, EXPECTED_AMOUNT, BATCH_CREATED_TIME) values (?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($batch_name, $batch_description, $expected_amount, $batch_created_time));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function updateBatch($batch_id, $batch_name, $batch_description, $expected_amount, $last_updated_time)
	{
		if($this->db_conn)
		{
			$query = 'update BATCH_DETAILS set BATCH_NAME=?, BATCH_DESCRIPTION=?, EXPECTED_AMOUNT=?, LAST_UPDATE_TIME=? where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_name, $batch_description, $expected_amount, $last_updated_time, $batch_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function deleteBatch($batch_id)
	{
		//delete batch releated table entries first before deleting the main table.
		if($this->deleteContributionSplitsUsingBatchID($batch_id))
		{
			if($this->deleteContributionUsingBatchID($batch_id))
			{
				if($this->deleteBatchDetails($batch_id)) {
					return true;
				}
			}
		}
		return false;
	}

	private function deleteBatchDetails($batch_id)
	{
		if($this->db_conn)
		{
			$query = 'delete from BATCH_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getAllBatches()
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the batches.';
		
		if($this->db_conn)
		{
			$batch_details = array();
			//$query = 'select a.BATCH_ID, a.BATCH_NAME, a.BATCH_DESCRIPTION, a.BATCH_CREATED_TIME, a.LAST_UPDATED_TIME, a.EXPECTED_AMOUNT, SUM(c.AMOUNT) from BATCH_DETAILS as a left outer join CONTRIBUTION_DETAILS as b on a.BATCH_ID = b.BATCH_ID, CONTRIBUTION_SPLIT_DETAILS as c where b.CONTRIBUTION_ID=c.CONTRIBUTION_ID';
			$query = 'SELECT A.BATCH_ID, A.BATCH_NAME, A.BATCH_DESCRIPTION, A.BATCH_CREATED_TIME, A.LAST_UPDATE_TIME, A.EXPECTED_AMOUNT, (SELECT SUM(C.TOTAL_AMOUNT)  FROM CONTRIBUTION_DETAILS as C where C.BATCH_ID=A.BATCH_ID) AS TOTAL_RECEIVED_FOR_BATCH FROM BATCH_DETAILS as A left join CONTRIBUTION_DETAILS as B ON A.BATCH_ID=B.BATCH_ID group by A.BATCH_ID;';
			$result = $this->db_conn->Execute($query);
			
			if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $batch_id = $result->fields[0];
                        $batch_name = $result->fields[1];
						$batch_description = $result->fields[2];
						$batch_created_time = $result->fields[3];
						$last_updated_time = $result->fields[4];
						$expected_amount = $result->fields[5];
						$received_amount = (($result->fields[6] != null)? $result->fields[6] : 0);
						$batch_details[] = array($batch_id, $batch_name, $batch_description, $batch_created_time, $last_updated_time, $expected_amount, $received_amount);

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
			$query = 'select BATCH_NAME, BATCH_DESCRIPTION, EXPECTED_AMOUNT, BATCH_CREATED_TIME from BATCH_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
			
			if($result) {
                if(!$result->EOF) {
                    $batch_name = $result->fields[0];
					$batch_description = $result->fields[1];
					$expected_amount = $result->fields[2];
					$batch_created_time = $result->fields[3];
					$batch_details = array($batch_id, $batch_name, $batch_description, $expected_amount, $batch_created_time);
					
					$return_data[0] = 1;
					$return_data[1] = $batch_details;
				}
            }
		}
		return $return_data;
	}

	public function isBatchUsedInContribution($batch_id)
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

	public function addContribution($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_update_time, $last_update_user_id, $last_update_user_name, $fund_id_list, $amount_list, $notes_list)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to add the contribution details.';

		if($this->db_conn)
		{
			$query = 'insert into CONTRIBUTION_DETAILS (CONTRIBUTION_DATE, BATCH_ID, PROFILE_ID, TRANSACTION_TYPE, PAYMENT_MODE, REFERENCE_NUMBER, TOTAL_AMOUNT, LAST_UPDATE_TIME, LAST_UPDATE_USER_ID, LAST_UPDATE_USER_NAME) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_update_time, $last_update_user_id, $last_update_user_name));
			if($result) {				
				$contribution_id = $this->db_conn->Insert_ID();
				$result = $this->addContributionSplit($contribution_id, $batch_id, $fund_id_list, $amount_list, $notes_list);
				if($result[0] == 1) {
					$return_data[0] = 1;
					$return_data[1] = 'Contribution has been added successfully';
				} else {
					$return_data[0] = 0;
					$return_data[1] = 'Contribution has been added successfully but the funds details are not updated.';
				}
			}
		}
		return $return_data;
	}

	public function updateContribution($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_update_time, $last_update_user_id, $last_update_user_name, $contribution_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to update the contribution details.';

		if($this->db_conn)
		{
			$query = 'update CONTRIBUTION_DETAILS set CONTRIBUTION_DATE=?, BATCH_ID=?, PROFILE_ID=?, TRANSACTION_TYPE=?, PAYMENT_MODE=?, REFERENCE_NUMBER=?, TOTAL_AMOUNT=?, LAST_UPDATE_TIME=?, LAST_UPDATE_USER_ID=?, LAST_UPDATE_USER_NAME=? where CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_update_time, $last_update_user_id, $last_update_user_name, $contribution_id));
			if($result) {
				$return_data[0] = 1;
				$return_data[1] = 'Contribution has been updated successfully';
			}
		}
		return false;
	}

	public function addContributionSplit($contribution_id, $batch_id, $fund_id_list, $amount_list, $notes_list)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to update the contribution funds.';

		if($this->db_conn)
		{
			$fund_id_arr = explode('<:|:>', $fund_id_list);
			$amount_arr = explode('<:|:>', $amount_list);
			$notes_arr = explode('<:|:>', $notes_list);
			if(COUNT($fund_id_arr) > 0)
			{
				$query = 'insert into CONTRIBUTION_SPLIT_DETAILS (CONTRIBUTION_ID, BATCH_ID, FUND_ID, AMOUNT, NOTES) values ';
				$query_to_append = '';
				$query_values = array();
				for($i=0; $i<COUNT($fund_id_arr); $i++)
				{
					if($query_to_append != '') {
						$query_to_append .= ',';
					}
					$query_to_append .= '(?, ?, ?, ?, ?)';
					$query_values[] = $contribution_id;
					$query_values[] = $batch_id;
					$query_values[] = $fund_id_arr[$i];
					$query_values[] = $amount_arr[$i];
					$query_values[] = $notes_arr[$i];
				}
				$query .= $query_to_append;
				$result = $this->db_conn->Execute($query, $query_values);
				if($result) {
					$return_data[0] = 1;
					$return_data[1] = 'Successfully added contribution funds.';
				}
			}			
		}
		return $return_data;
	}

	public function updateContributionSplit($contribution_split_id, $contribution_id, $fund_id, $amount, $notes)
	{
		if($this->db_conn)
		{
			$query = 'update CONTRIBUTION_SPLIT_DETAILS set CONTRIBUTION_ID=?, FUND_ID=?, AMOUNT=?, NOTES=? where CONTRIBUTION_SPLIT_ID=? and CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_id, $fund_id, $amount, $notes, $contribution_split_id));
			if($result) {
				return true;
			}
		}
		return false;
	}	

	public function getAllContributions($batch_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the contributions.';
		
		if($this->db_conn)
		{
			$contribution_details = array();
			$query = 'select a.CONTRIBUTION_ID, a.CONTRIBUTION_DATE, a.BATCH_ID, c.BATCH_NAME, a.PROFILE_ID, b.NAME, a.TRANSACTION_TYPE, a.PAYMENT_MODE, a.REFERENCE_NUMBER, a.TOTAL_AMOUNT, a.LAST_UPDATE_TIME, a.LAST_UPDATE_USER_ID, a.LAST_UPDATE_USER_NAME from CONTRIBUTION_DETAILS as a, PROFILE_DETAILS as b, BATCH_DETAILS as c where a.PROFILE_ID=b.PROFILE_ID and a.BATCH_ID=c.BATCH_ID and a.BATCH_ID=? ORDER BY a.CONTRIBUTION_ID desc';
			$result = $this->db_conn->Execute($query, array($batch_id));			
			
			if($result) {
                if(!$result->EOF) {
					while(!$result->EOF)
					{
						$contribution_id = $result->fields[0];
						$contribution_date = $result->fields[1];
						$batch_id = $result->fields[2];
						$batch_name = $result->fields[3];
						$profile_id = $result->fields[4];
						$profile_name = $result->fields[5];
						$transaction_type = $result->fields[6];
						$payment_mode = $result->fields[7];
						$reference_number = $result->fields[8];
						$total_amount = $result->fields[9];
						$last_update_time = $result->fields[10];
						$last_update_user_id = $result->fields[11];
						$last_update_user_name = $result->fields[12];
						$contribution_details[] = array($contribution_id, $contribution_date, $batch_id, $batch_name, $profile_id, $profile_name, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_update_time, $last_update_user_id, $last_update_user_name);

						$result->MoveNext();
					}
					$return_data[0] = 1;
					$return_data[1] = $contribution_details;
				} else {
					$return_data[0] = 0;
					$return_data[1] = 'No contribution is available.';
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
			$query = 'select CONTRIBUTION_DATE, BATCH_ID, PROFILE_ID, TRANSACTION_TYPE, PAYMENT_MODE, REFERENCE_NUMBER, TOTAL_AMOUNT, LAST_UPDATE_TIME, LAST_UPDATE_USER_ID, LAST_UPDATE_USER_NAME from CONTRIBUTION_DETAILS where CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_id));
			
			if($result) {
                if(!$result->EOF) {
                    $contribution_date = $result->fields[0];
					$batch_id = $result->fields[1];
					$profile_id = $result->fields[2];
					$transaction_type = $result->fields[3];
					$payment_mode = $result->fields[4];
					$reference_number = $result->fields[5];
					$total_amount = $result->fields[6];
					$last_update_time = $result->fields[7];
					$last_update_user_id = $result->fields[8];
					$last_update_user_name = $result->fields[9];
					$contribution_details = array($contribution_id, $contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_update_time, $last_update_user_id, $last_update_user_name);

					$return_data[0] = 1;
					$return_data[1] = $contribution_details;
				}
            }
		}
		return $return_data;
	}

	public function getContributionSplitDetails($contribution_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to list the contribution funds';
		
		if($this->db_conn)
		{
			$contribution_split_details = array();
			$query = 'select a.CONTRIBUTION_SPLIT_ID, a.CONTRIBUTION_ID, a.FUND_ID, b.FUND_NAME, a.AMOUNT, a.NOTES from CONTRIBUTION_SPLIT_DETAILS as a, FUND_DETAILS as b where a.CONTRIBUTION_ID=? and a.FUND_ID=b.FUND_ID';
			$result = $this->db_conn->Execute($query, array($contribution_id));
			
			if($result) {
                if(!$result->EOF) {
					while(!$result->EOF)
					{
						$contribution_split_id = $result->fields[0];
						$contribution_id = $result->fields[1];
						$fund_id = $result->fields[2];
						$fund_name = $result->fields[3];
						$amount = $result->fields[4];
						$notes = $result->fields[5];
						$contribution_split_details[] = array($contribution_split_id, $contribution_id, $fund_id, $fund_name, $amount, $notes);

						$result->MoveNext();
					}
					$return_data[0] = 1;
					$return_data[1] = $contribution_split_details;
				} else {
					$return_data[0] = 0;
					$return_data[1] = 'No contribution fund is available';
				}
            }
		}
		return $return_data;
	}

	public function deleteContribution($contribution_id)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Unable to delete the contribution';
		if($this->db_conn)
		{
			$del_cont_splits = $this->deleteContributionSplitsUsingContributionID($contribution_id);
			if($del_cont_splits[0]==1)
			{
				$query = 'delete from CONTRIBUTION_DETAILS where CONTRIBUTION_ID=?';
				$result = $this->db_conn->Execute($query, array($contribution_id));
				if($result) {
					$return_data[0] = 1;
					$return_data[1] = 'Contribution has been deleted successfully';
				}
			}
			else
			{
				$return_data[0] = 0;
				$return_data[1] = $del_cont_splits[1];
			}
		}
		return $return_data;
	}

	private function deleteContributionUsingBatchID($batch_id)
	{
		if($this->db_conn)
		{
			$query = 'delete from CONTRIBUTION_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
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
			$query = 'delete from CONTRIBUTION_SPLIT_DETAILS where CONTRIBUTION_SPLIT_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_split_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	private function deleteContributionSplitsUsingBatchID($batch_id)
	{
		if($this->db_conn)
		{
			$query = 'delete from CONTRIBUTION_SPLIT_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
			if($result) {
				return true;
			}
		}
		return false;
	}

	private function deleteContributionSplitsUsingContributionID($contribution_id)
	{
		$to_return = array();
		$to_return[0]=0;
		$to_return[1]="Unable to delete the contribution splits";
		if($this->db_conn)
		{
			$query = 'delete from CONTRIBUTION_SPLIT_DETAILS where CONTRIBUTION_ID=?';
			$result = $this->db_conn->Execute($query, array($contribution_id));
			if($result) {
				$to_return[0]=1;
				$to_return[1]="Successfully deleted the contribution splits";
			}
		}
		return $to_return;
	}

	public function getBatchTotalReceivedAmount($batch_id)
	{
		$to_return = array();
		$to_return[0] = 0;
		$to_return[1] = 'Unable to get the received amount';
		if($this->db_conn)
		{
			$query = 'select SUM(TOTAL_AMOUNT) from CONTRIBUTION_DETAILS where BATCH_ID=?';
			$result = $this->db_conn->Execute($query, array($batch_id));
			if($result) {
				$to_return[0] = 1;
				$to_return[1] = (($result->fields[0] != NULL)?$result->fields[0]:0);
			}
		}

		return $to_return;
	}

	public function getMonthlyContributionAmount($start_date, $end_date)
	{
		$total_amount = 0;
		if($this->db_conn)
		{
		   $result = $this->db_conn->Execute('select SUM(TOTAL_AMOUNT) from CONTRIBUTION_DETAILS where CONTRIBUTION_DATE >= ? and CONTRIBUTION_DATE <= ?', array($start_date, $end_date));
            
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

	public function importContributionsFromBatch($batch_id, $contribution_date, $contribution_list)
	{
		$return_data = array();
		$return_data[0] = 0;
		$return_data[1] = 'Problem while import the contributions. Please see the <a href="#">error log</a> for more information.';

		$success = 0;
		$failure = 0;
		$contribution_array = explode(',', $contribution_list);
		if(is_array($contribution_array))
		{
			$total_contributions = COUNT($contribution_array);
			if($total_contributions > 0)
			{
				session_start();
				set_time_limit(0);
				$user_id = $_SESSION['userID'];
				$user_name = $_SESSION['username'];
				
				$dt = Carbon::now($_SESSION['churchTimeZone']);
				$last_updated_time = $dt->toDateTimeString();

				for($i=0; $i<$total_contributions; $i++)
				{
					$contribution_id = $contribution_array[$i];
					$contribution_details = $this->getContributionInformation($contribution_id);
					$split_details = $this->getContributionSplitDetails($contribution_id);
					//print_r($split_details);exit;

					//$contribution_split_details[] = array($contribution_split_id, $contribution_id, $fund_id, $fund_name, $amount, $notes);

					if($contribution_details[0] == 1 && $split_details[0] == 1) {
						//$contribution_id = $contribution_details[1][0];
						//$contribution_date = $contribution_details[1][0];
						//$batch_id = $contribution_details[1][0];
						$profile_id = $contribution_details[1][3];
						$transaction_type = $contribution_details[1][4];
						$payment_mode = $contribution_details[1][5];
						$reference_number = $contribution_details[1][6];
						$total_amount = $contribution_details[1][7];

						$total_splits = COUNT($split_details[1]);
						if($total_splits > 0)
						{
							$fund_id_list = '';
							$amount_list = '';
							$notes_list = '';
							for($j=0; $j<$total_splits; $j++)
							{
								if($fund_id_list != '') {
									$fund_id_list .= '<:|:>';
								}
								if($amount_list != '') {
									$amount_list .= '<:|:>';
								}
								if($notes_list != '') {
									$notes_list .= '<:|:>';
								}
								$fund_id_list .= $split_details[1][$j][2];
								$amount_list .= $split_details[1][$j][4];
								$notes_list .= $split_details[1][$j][5];
							}
						}
						
						$result = $this->addContribution($contribution_date, $batch_id, $profile_id, $transaction_type, $payment_mode, $reference_number, $total_amount, $last_updated_time, $user_id, $user_name, $fund_id_list, $amount_list, $notes_list);
						if($result[0] == 1) {
							$success++;
						} else {
							$failure++;
						}
					} else {
						//log error
						$failure++;
					}					
				}
			}
		}

		if($success == $total_contributions) {
			$return_data[0] = 1;
			$return_data[1] = 'Your contributions has been imported successfully.';
		} else if($failure > 0 && $success > 0) {
			$return_data[0] = 0;
			$return_data[1] = 'Your contributions has been imported partially. Few contributions are failed to import and to see the missed contributions list, please <a href="#">Click Here</a>.';
		}
		return $return_data;
	}
}
?>