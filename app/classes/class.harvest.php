<?php
//class to handle harvest usage

class Harvest
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

	public function addNewHarvest($profile_id, $date_of_harvest, $item_desc, $item_amt)
	{
		if($this->db_conn)
		{
			$query = 'insert into HARVEST_DETAILS (PROFILE_ID, DATE_OF_HARVEST, ITEM_DESCRIPTION, ITEM_AMOUNT) values (?, ?, ?, ?)';
			$result = $this->db_conn->Execute($query, array($profile_id, $date_of_harvest, $item_desc, $item_amt));
			//echo "Error:::".$this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function updateHarvest($harvest_id, $profile_id, $date_of_harvest, $item_desc, $item_amt)
	{
		if($this->db_conn)
		{
			$query = 'update HARVEST_DETAILS set PROFILE_ID=?, DATE_OF_HARVEST=?, ITEM_DESCRIPTION=?, ITEM_AMOUNT=? where HARVEST_ID=?';
			$result = $this->db_conn->Execute($query, array($profile_id, $date_of_harvest, $item_desc, $item_amt, $harvest_id));
			if($result) {
				return true;
			}			
		}
		return false;
	}

	public function deleteHarvest($harvest_id)
	{
		if($this->db_conn)
		{
			$result = $this->db_conn->Execute('delete from HARVEST_DETAILS where HARVEST_ID=?', array($harvest_id));
//			echo "SKTG:::".$this->db_conn->ErrorMsg();
			if($result) {
				return true;
			}
		}
		return false;
	}

	public function getAllHarvestDetails($profile_id)
	{
		$harvest_details = array();
		if($this->db_conn)
		{
			$query = 'select a.HARVEST_ID, a.PROFILE_ID, b.NAME, b.UNIQUE_ID, a.DATE_OF_HARVEST, a.ITEM_DESCRIPTION, a.ITEM_AMOUNT from HARVEST_DETAILS as a, PROFILE_DETAILS as b where a.PROFILE_ID=b.PROFILE_ID';
			if($profile_id > 0)
			{
				$query .= ' and a.PROFILE_ID='.$profile_id;
			}
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                if(!$result->EOF) {
                    while(!$result->EOF)
                    {
                        $harvest_id = $result->fields[0];
                        $profile_id = $result->fields[1];
						$profile_name = $result->fields[2];
						$unique_id = $result->fields[3];
                        $date = $result->fields[4];
                        $item_desc = $result->fields[5];
						$item_amt = $result->fields[6];
						$harvest_details[] = array($harvest_id, $profile_id, $profile_name, $unique_id, $date, $item_desc, $item_amt);
                        
						$result->MoveNext();                        
                    }
                }
            }
        }
		return $harvest_details;
	}

	public function getHarvestInformation($harvest_id)
	{
		$harvest_details = array();
		if($this->db_conn)
		{
		   $query = 'select * from HARVEST_DETAILS where HARVEST_ID=?';
		   $result = $this->db_conn->Execute($query, array($harvest_id));
            
           if($result) {
                if(!$result->EOF) {
					$harvest_id = $result->fields[0];
					$profile_id = $result->fields[1];
					$profile_name = $result->fields[2];
					$date = $result->fields[3];
					$item_desc = $result->fields[4];
					$item_amt = $result->fields[5];
					$harvest_details = array($harvest_id, $profile_id, $profile_name, $date, $item_desc, $item_amt);
				}
            }
        }
		return $harvest_details;
	}

	public function getProfileHarvestTotal($profile_id)
	{
		$total = 0;
		if($this->db_conn)
		{
		   $query = 'select sum(ITEM_AMOUNT) from HARVEST_DETAILS where PROFILE_ID=?';
		   $result = $this->db_conn->Execute($query, array($profile_id));
            
           if($result) {
                if(!$result->EOF) {
					if($result->fields[0] != NULL)
					{
						$total = $result->fields[0];
					}
				}
            }
        }
		return $total;
	}
}