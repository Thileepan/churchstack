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

	public function downloadHTMLAsPDF($input_html, $target_file, $force_download=0, $paper="a4", $orientation="portrait")
	{
		require_once($this->APPLICATION_PATH."plugins/dompdf/dompdf_config.inc.php");

		$toReturn = array();
		$toReturn[0] = 0;
		$toReturn[1] = "Failed to convert the data to PDF";
		if(trim($target_file) == "") {
			$toReturn[0] = 0;
			$toReturn[1] = "Target file name is empty";
			return $toReturn;
		}
		if(trim($input_html) == "") {
			$toReturn[0] = 0;
			$toReturn[1] = "Input data is empty";
			return $toReturn;
		}
		if ( get_magic_quotes_gpc() ) {
			$input_html = stripslashes($input_html);
		}
		$attachment_value = (($force_download==1)? true : false);
		$paper = ((trim($paper) != "")? trim($paper) : "a4");
		$orientation = ((trim($orientation) != "")? trim($orientation) : "portrait");
		$dompdf = new DOMPDF();
		$dompdf->load_html($input_html);
		$dompdf->set_paper($paper, $orientation);
		$dompdf->render();
		$dompdf->stream($target_file, array("Attachment" => $attachment_value));
		$toReturn[0] = 1;
		$toReturn[1] = "PDF streaming initiated";
		return $toReturn;
	}
}

?>