<?php
$APPLICATION_PATH = "../../";
@include $APPLICATION_PATH.'portal/secure.php';
@include $APPLICATION_PATH.'app/utils/JSON.php';
@include $APPLICATION_PATH.'app/utils/utilfunctions.php';

$req = $_REQUEST['req'];
session_start();
if($req == 1)//Login validation
{
	$loginPwd = md5(trim($_REQUEST['loginPwd']));
	$rsno = 0;
	$msg = "Invalid Login Credentials";
	$type = 1;
	if($loginPwd == trim($LOGIN_PWD)) {
		$rsno = 1;
		$msg = "Login is successful, now enter the 'Access' credentials to get access to the system";
		$_SESSION['loginPassword'] = $loginPwd;
	}
	$to_return = array("rsno"=>$rsno, "msg"=>$msg, "type"=>$type, "pwd"=>$loginPwd, "nopw"=>trim($LOGIN_PWD));
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
else if($req == 2)//Access validation
{
	$accessPwd = md5(trim($_REQUEST['accessPwd']));
	$rsno = 0;
	$msg = "Invalid Access Credentials";
	$type = 2;
	if($accessPwd == trim($ACCESS_PWD)) {
		$rsno = 1;
		$msg = "Access is now granted for you to access the system";
		$_SESSION['accessPassword'] = $accessPwd;
	}
	$to_return = array("rsno"=>$rsno, "msg"=>$msg, "type"=>$type);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;
}
?>