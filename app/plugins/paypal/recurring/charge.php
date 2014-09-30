<?php
	require_once ("paypalfunctions.php");

	session_start();
	$agreement_id = 'B-7LE83446DP2293543';
	$_SESSION['billing_agreemenet_id'] = $agreement_id;
	$resArray = DoReferenceTransaction(7.5, "USD");
	print_r($resArray);
?>