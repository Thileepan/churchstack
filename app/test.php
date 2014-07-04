<?php

echo md5("admin");
exit;
/*
phpinfo();
exit;

//include_once $APPLICATION_PATH . 'plugins/carbon/src/Carbon/Carbon.php';
require 'plugins/carbon/src/Carbon/Carbon.php';
//use Carbon\Carbon;

if (class_exists('Carbon')) {
	echo "vvvvvvv";
//	$carbon = new Carbon();
	echo 'yyy';
}
if (class_exists('v')) {
//	echo "bbbbbb";
}

echo "aa";
try {
	echo "sktg:::" . Carbon::now(new DateTimeZone('Europe/London'));
	echo "age:::" . Carbon::createFromDate(1987, 10, 05)->age;
} catch(Exception $ex) {
	echo "error" . $ex->getMessage();
}
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

phpinfo();
exit;

class testA
{
	public function printB()
	{
		echo 'BBBB';
		echo $this->x;
	}
}

class testB extends testA
{
	protected $x = 5;
	public function __construct()
	{
		echo 'AAAA';
		$this->printB();

	}
}

$obj = new testB();

?>