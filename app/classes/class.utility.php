<?php

class Utility
{
	//protected $db_conn;
	private $APPLICATION_PATH;
	private $geoLiteCityDatFile;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 

		//intialize database connection
        //include_once($this->APPLICATION_PATH . 'db/dbutil.php');
		include_once($this->APPLICATION_PATH . 'utils/utilfunctions.php');
		$this->geoLiteCityDatFile = $this->APPLICATION_PATH."plugins/geoip-api-php/GeoLiteCity/GeoLiteCity.dat";
		//$conn_obj = getDatabaseConnection($this->APPLICATION_PATH, false);
		/** /
		if($conn_obj[0] == 0) {
            $this->db_conn = $conn_obj[1];
        }
		/**/
	}

	public function getCountryCodeFromIP($ip_address)
	{
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoip.inc");
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoipcity.inc");

		$gi = geoip_open($this->geoLiteCityDatFile, GEOIP_STANDARD);
		$country_code = geoip_country_code_by_addr($gi, $ip_address);
		geoip_close($gi);
		return $country_code;
	}

	public function getRecordsFromIP($ip_address)
	{
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoip.inc");
		include_once($this->APPLICATION_PATH."plugins/geoip-api-php/src/geoipcity.inc");

		$gi = geoip_open($this->geoLiteCityDatFile, GEOIP_STANDARD);
		$record = geoip_record_by_addr($gi, $ip_address);
		geoip_close($gi);
		return $record;
	}
}

?>