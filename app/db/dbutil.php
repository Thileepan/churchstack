<?php

function getDatabaseConnection($APPLICATION_PATH, $connectShardDB=false)
{
	//error_reporting(E_ALL);
	//ini_set("display_errors", "On");

	include_once($APPLICATION_PATH . 'db/adodb/adodb.inc.php');
	include_once($APPLICATION_PATH . 'conf/config.php');

	$os = PHP_OS;
	$odbcStrValue = (($os=='Linux' || $os=='Darwin' || $os=='FreeBSD')?'mysql':'odbc');
	$dsName = (($os=='Linux' || $os=='Darwin' || $os=='FreeBSD')? $dbinfo[0]:$dbinfo[1]);
	$conn = &ADONewConnection('mysql'); //create a connection
	
	if(!$conn)
	{
		$return_data[0] = 1;
		$return_data[1] = 'Unable to connect the database';
	}
	else
	{
		$conn->autoRollback = true;
		if($odbcStrValue == 'mysql')
		{
			$dsName = (($dsName == 'localhost')? php_uname('n'):$dsName);
		}
		session_start();
		$db_name = (($connectShardDB)?$_SESSION['shardedDB']:DB_NAME);
		$returnValue = $conn->PConnect('127.0.0.1', DB_USER_NAME, DB_PASSWORD, $db_name);
		if($conn->IsConnected())
		{
			$return_data[0] = 0;
			$return_data[1] = $conn;
		}
		else
		{
			$return_data[0] = 1;
			$return_data[1] = 'Unable to connect the database';
		}
	}
	return $return_data;
}

function createShardedDB($APPLICATION_PATH, $db_name)
{
	$to_return = array();
	if(trim($db_name) == "") {
		$to_return[0] = 0;
		$to_return[1] = "Empty details received, unable to create a dedicated setup";
		return $to_return;
	}
	$to_return[0] = 0;
	$to_return[1] = "An error occurred while trying to create a dedicated setup, errors have been logged for analyzing.";
	$username = DB_USER_NAME;
	$password = DB_PASSWORD;
	$host = "127.0.0.1";
	$sharded_sql_file = $APPLICATION_PATH."sql/sharded.sql";
	$db_output_file = $APPLICATION_PATH."log/sharded_".$db_name.".log";
	$data_written_size = file_put_contents($db_output_file, "");
	$db_output_file_content = "";
	$create_db_command = 'mysql -u '.$username.' -p'.$password.' -h '.$host.' -A  -e "create database '.$db_name.' collate latin1_general_cs;"; > '.$db_output_file.' 2>&1 ';

	$outputs = array();
	$ret_val = "";
	$last_line_output = exec($create_db_command, $outputs, $ret_val);

	if(file_exists($db_output_file))
	{
		$db_output_file_content = trim(file_get_contents($db_output_file));
	}
	if(trim($db_output_file_content) != "")
	{
		$to_return[0] = 0;
		$to_return[1] = "Some error occurred while trying to create a dedicated setup, errors have been logged for analyzing.";
		return $to_return;
	}

	$create_tables_command = 'mysql -u '.$username.' -p'.$password.' -h '.$host.' -D '.$db_name.' -A  -e "source '.$sharded_sql_file.';"; > '.$db_output_file.' 2>&1 ';
	$outputs = array();
	$ret_val = "";
	$last_line_output = exec($create_tables_command, $outputs, $ret_val);
	$db_output_file_content = "";
	if(file_exists($db_output_file))
	{
		$db_output_file_content = trim(file_get_contents($db_output_file));
	}
	if(trim($db_output_file_content) != "")
	{
		$to_return[0] = 0;
		$to_return[1] = "Some error occurred while trying to create a dedicated setup, errors have been logged for analyzing.";
		return $to_return;
	}
	@unlink($db_output_file);//Delete the log file if the operation is successful.
	$to_return[0] = 1;
	$to_return[1] = "A dedicated setup has been successfully created for the user.";

	return $to_return;
}

?>