<?php
	header('HTTP/1.1 404 Not Found');
	$_GET['e'] = 404;
	echo "<h2>ERROR 404 : The page you are looking for is not found</h2>";
?>