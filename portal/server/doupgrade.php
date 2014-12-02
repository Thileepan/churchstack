<?php
//error_reporting(-1);
//ini_set("display_errors", "1");
$APPLICATION_PATH = "../../";
@include_once($APPLICATION_PATH."portal/utils/auth.php");
validateSession($APPLICATION_PATH);

include $APPLICATION_PATH.'app/utils/JSON.php';
include $APPLICATION_PATH.'app/utils/utilfunctions.php';
include_once $APPLICATION_PATH . 'app/classes/class.church.php';
include_once $APPLICATION_PATH . 'app/classes/class.license.php';
include_once $APPLICATION_PATH . 'app/db/dbutil.php';

//process request
$req = $_REQUEST['req'];
if($req == 1)
{
	$upgrade_file = $_POST["upgradeFile"];
	$church_obj = new Church($APPLICATION_PATH."app/");
	$churches_result = $church_obj->getAllChurchesList(0);//List all churches
	$upgrade_results = array();
	if($churches_result[0]==1)
	{
		$rsno = 1;
		$msg = "Success";
		$commands = array();
		for($i=0; $i < COUNT($churches_result[1]); $i++)
		{
			$churchID = $churches_result[1][$i][0];
			$churchName = $churches_result[1][$i][1];
			$churchAddress = $churches_result[1][$i][3];
			$shardedDB = $churches_result[1][$i][10];
			$churchCountry = $churches_result[1][$i][14];

			$curr_upgrade_result = upgradeShardedDB($APPLICATION_PATH."app/", $shardedDB, $upgrade_file);
			$upgrade_results[] = array($churchID, $churchName, $churchAddress, $shardedDB, $churchCountry, $curr_upgrade_result[0], $curr_upgrade_result[1]);
		}
		$church_data_html = "";
		$church_data_html .= '<div class="row-fluid">';
			$church_data_html .= '<div class="table-responsive">';
				$church_data_html .= '<table class="table table-bordered">';
					$church_data_html .= '<thead>';
						$church_data_html .= '<tr>';
							$church_data_html .= '<th style="background: #eee;"><b>Church ID</b></th>';
							$church_data_html .= '<th style="background: #eee;"><b>Church Name</b></th>';
							$church_data_html .= '<th style="background: #eee;"><b>Address</b></th>';
							$church_data_html .= '<th style="background: #eee;"><b>Database</b></th>';
							$church_data_html .= '<th style="background: #eee;"><b>Country</b></th>';
							$church_data_html .= '<th style="background: #eee;"><b>Upgrade Result</b></th>';
							$church_data_html .= '<th style="background: #eee;"><b>Message</b></th>';
						$church_data_html .= '</tr>';
					$church_data_html .= '</thead>';
					$church_data_html .= '<tbody>';
					for($c=0; $c < COUNT($upgrade_results); $c++)
					{
						$church_data_html .= '<tr>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">'.$upgrade_results[$c][0].'</font>': '<font color="red">'.$upgrade_results[$c][0].'</font>').'</td>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">'.$upgrade_results[$c][1].'</font>': '<font color="red">'.$upgrade_results[$c][1].'</font>').'</td>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">'.$upgrade_results[$c][2].'</font>': '<font color="red">'.$upgrade_results[$c][2].'</font>').'</td>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">'.$upgrade_results[$c][3].'</font>': '<font color="red">'.$upgrade_results[$c][3].'</font>').'</td>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">'.$upgrade_results[$c][4].'</font>': '<font color="red">'.$upgrade_results[$c][4].'</font>').'</td>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">Success</font>': '<font color="red">Failed</font>').'</td>';
							$church_data_html .= '<td style="background: #eee;">'.(($upgrade_results[$c][5]==1)? '<font color="green">'.$upgrade_results[$c][6].'</font>': '<font color="red">'.$upgrade_results[$c][6].'</font>').'</td>';
						$church_data_html .= '</tr>';
					}
					$church_data_html .= '</tbody>';
				$church_data_html .= '</table>';
			$church_data_html .= '<div>';
		$church_data_html .= '<div>';

		$rslt = $church_data_html;
	}
	else
	{
		$rsno = 0;
		$msg = $coupon_result[1];
	}

	$to_return = array("rsno"=>$rsno, "msg"=>$msg, "rslt"=>$rslt);
	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;

}
?>