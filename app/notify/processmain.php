<?php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	
	//print_r( $_SERVER)."--";exit;
	if(trim($_SERVER['DOCUMENT_ROOT']) != "") {
		@require $APPLICATION_PATH.'error/404.php';
		exit;
	}

	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.church.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	$sharded_db_processing_file = $APPLICATION_PATH."notify/processsharded.php";
	$church_obj = new Church($APPLICATION_PATH);
	$churches_result = $church_obj->getAllChurchesList(5);//List On-Trial or Paid+Active Churches alone; For others, we need not send notifications.
	/** /
	$churches_result[0]=1;
	$churches_result[1] = array(0=>array(10=>"DB_1"), 1=>array(10=>"DB_2"), 2=>array(10=>"DB_3"));
	/**/
	if($churches_result[0]==1)
	{
		$commands = array();
		for($i=0; $i < COUNT($churches_result[1]); $i++)
		{
			$shardedDB = $churches_result[1][$i][10];
			$commands[] = '"C:/Program Files (x86)/php/php.exe" '.$sharded_db_processing_file.' shardedDB='.urlencode($shardedDB);
		}
		$threads = new Multithread( $commands );
		$threads->run();
		/** /
		foreach ( $threads->commands as $key=>$command )
		{
			echo "Command: ".$command."\n";
			echo "Output: ".$threads->output[$key];
			echo "Error: ".$threads->error[$key]."\n\n";
		}
		/**/
	}
?>