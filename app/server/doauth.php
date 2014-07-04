<?php
$APPLICATION_PATH = "../";
include_once $APPLICATION_PATH . '/classes/class.users.php';
//include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';

//process request
$req = trim($_REQUEST['req']);

if($req == 'authenticate')
{
	$user_name = trim($_POST['username']);
	$password = md5(trim($_POST['password']));
	
	$users_obj = new Users($APPLICATION_PATH);
	if( $users_obj->isAuthenticatedUser($user_name, $password) ) {

		$user_details = $users_obj->getUserInformationUsingName($user_name);

		session_start();
		$_SESSION['username'] = $user_name;
		$_SESSION['password'] = $password;
		$_SESSION['userID'] = $user_details[0]; //user_id
		$_SESSION['churchID'] = $user_details[1]; //church_id
		$_SESSION['loginTime'] = time();
		$_SESSION['shardedDB'] = 'cs01_churchstack'; //'cs' . $_SESSION['churchID'] .'_database';
		echo 1;
	} else {
		echo 0;
	}
	exit;
}
?>