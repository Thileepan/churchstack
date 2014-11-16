<?php
	//profCusFieldDefValInsert.php
	$APPLICATION_PATH = __DIR__."/../";//Exclusively for running from command line 
	$APPLICATION_PATH = str_replace("\\", "/", $APPLICATION_PATH);
	
	if(trim($_SERVER['DOCUMENT_ROOT']) != "") {
		@require $APPLICATION_PATH.'error/404';
		exit;
	}

	include_once($APPLICATION_PATH."plugins/thread/class.thread.php");
	include_once($APPLICATION_PATH."classes/class.settings.php");
	include_once($APPLICATION_PATH."conf/config.php");

	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	//DO NOT REMOVE THIS LINE WITHOUT UNDERSTANDING

	$field_id = urldecode($_GET["fieldID"]);
	$field_value = urldecode($_GET["fieldValue"]);

	$prof_settings_obj = new ProfileSettings($APPLICATION_PATH);
	$prof_settings_obj->insertDefCusFldValExistProfiles($field_id, $field_value);
?>
