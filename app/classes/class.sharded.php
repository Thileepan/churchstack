<?php

class Sharded
{
	protected $db_conn;
	private $APPLICATION_PATH;
	
	public function __construct($APPLICATION_PATH, $shardedDBName="")
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, true, $shardedDBName);
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
		}
	}

	public function cleanupAllTables()
	{
        @include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "There was an error while trying to cleanup the setup.";
		$tables_list = array();
		if($this->db_conn)
		{
			//List all tables first
		   $query = 'show tables';
		   $result = $this->db_conn->Execute($query);
            
           if($result) {
                while(!$result->EOF)
				{
					$tables_list[] = $result->fields[0];
					$result->MoveNext();
				}
            } else {
				$toReturn[0] = 0;
				$toReturn[1] = "There was an error when fetching the account details";
				return $toReturn;
			}

			//Disabling foreign key check
		   $query_1 = 'SET FOREIGN_KEY_CHECKS=0';
		   $result_1 = $this->db_conn->Execute($query_1);
		   if($result_1) {
			   //Going to do something ?????
		   }

			//Deleting all the rows across all the tables
			$failed_count = 0;
			for($i=0; $i < COUNT($tables_list); $i++)
			{
				$result_2 = null;
				$query_2 = 'delete from '.$tables_list[$i];
				$result_2 = $this->db_conn->Execute($query_2);
				if($result_2) {
					//Want to do something...?
				} else {
					$failed_count++;
				}
			}

			//Enabling foreign key check
		   $query_3 = 'SET FOREIGN_KEY_CHECKS=1';
		   $result_3 = $this->db_conn->Execute($query_3);
			if($result_3) {
				//Going to do something
			}
			if($failed_count > 0) {
				$toReturn[0] = 0;
				$toReturn[1] = "Around ".$failed_count." entries could not be cleaned up due to some errors";
			} else {
				$toReturn[0] = 1;
				$toReturn[1] = "The setup has been cleaned up successfully as per the request";
			}

			//Getting the current database name to do some taskss...
		   $query_4 = 'SELECT DATABASE()';
		   $result_4 = $this->db_conn->Execute($query_4);
           if($result_4) {
                if(!$result_4->EOF){
					$curr_db_name = $result_4->fields[0];

					//Creating the tables (if not exists) and inserting default rows again
					$create_sharded_result = createShardedDB($this->APPLICATION_PATH, $curr_db_name, 0);
				}
			}
        }
		else
		{
			$toReturn[0] = 0;
			$toReturn[1] = "Unable to get connection to the system.";
		}
		return $toReturn;
	}
}

?>