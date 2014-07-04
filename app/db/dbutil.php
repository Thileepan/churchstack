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

?>