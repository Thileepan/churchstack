<?php

include __DIR__ . '/class.thread.php';

$commands = array();

for ( $i=0; $i<5; $i++ ) {
	$commands[] = "ping localhost";
	
}

$threads = new Multithread( $commands );
$threads->run();

foreach ( $threads->commands as $key=>$command ) {
	echo "Command: ".$command."\n";
	echo "Output: ".$threads->output[$key];
	echo "Error: ".$threads->error[$key]."\n\n";
}
?>