<?php

$APPLICATION_PATH = "../../";
session_start();
@include_once($APPLICATION_PATH."portal/utils/auth.php");
validateSession($APPLICATION_PATH);

@include $APPLICATION_PATH.'app/utils/JSON.php';
@include $APPLICATION_PATH.'app/utils/utilfunctions.php';
@include_once $APPLICATION_PATH . 'app/classes/class.users.php';

//process request
$req = $_REQUEST['req'];
if($req == 1)//List All Users
{
	$users_obj = new Users($APPLICATION_PATH."app/");
	$users = $users_obj->getAllUsers();
	$to_return = array();
	$to_return['aaData'] = array();
	for($c=0; $c < COUNT($users); $c++)
	{
		$curr_user = $users[$c];
		$to_return['aaData'][] = array($curr_user[0], $curr_user[1], $curr_user[2], $curr_user[3], $curr_user[4], $curr_user[9]);
	}
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
?>