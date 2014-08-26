<?php
class FileLogger
{
	private $APPLICATION_PATH;
	private $log; 

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 
		include_once($APPLICATION_PATH.'plugins/apache-log4php/src/main/php/Logger.php');
		Logger::configure($APPLICATION_PATH.'conf/loggerconfig.xml');
		$this->log = Logger::getLogger(__CLASS__);
	}

	public function trace($message)
	{
		$this->log->trace($message);
	}

	public function debug($message)
	{
		$this->log->debug($message);
	}

	public function info($message)
	{
		$this->log->info($message);
	}

	public function warn($message)
	{
		$this->log->warn($message);
	}

	public function error($message)
	{
		$this->log->error($message);
	}

	public function fatal($message)
	{
		$this->log->fatal($message);
	}
}

?>