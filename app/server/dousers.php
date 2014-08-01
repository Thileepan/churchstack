<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include $APPLICATION_PATH.'utils/utilfunctions.php';
include_once $APPLICATION_PATH . 'classes/class.users.php';

//process request
$req = $_REQUEST['req'];
if($req == 1)
{
	$email = trim($_REQUEST['email']);
	$type = trim($_REQUEST['type']);
	$users_obj = new Users($APPLICATION_PATH);
	$result_data = $users_obj->sendPasswordResetEmail($email);
	$rsno = $result_data[0];
	$msg = $result_data[1];
	$to_return = array("type"=>$type, "rsno"=>$rsno, "msg"=>$msg);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 2)
{
	$email = trim($_REQUEST['email']);
	$type = trim($_REQUEST['type']);
	$password = md5(trim($_REQUEST['pwd']));
	$users_obj = new Users($APPLICATION_PATH);
	$result_data = $users_obj->changeUserPassword($email, $password, 1);
	$rsno = $result_data[0];
	$msg = $result_data[1];
	$to_return = array("type"=>$type, "rsno"=>$rsno, "msg"=>$msg);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
?>