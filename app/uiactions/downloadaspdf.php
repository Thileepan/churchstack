<?php
	$APPLICATION_PATH = "../";
	include_once($APPLICATION_PATH."classes/class.utility.php");
	$util_obj = new Utility($APPLICATION_PATH);
	$input_html = trim($_POST["pdfInputHtml"]);
	$target_file = trim($_POST["pdfTargetFile"]);
	$force_download = trim($_POST["pdfForceDownload"]);
	$paper_size = ((isset($_POST["pdfPaperSize"]) && trim($_POST["pdfPaperSize"]) != "")? trim($_POST["pdfPaperSize"]) : "");//default value will be taken safely
	$orientation = ((isset($_POST["pdfOrientation"]) && trim($_POST["pdfOrientation"]) != "")? trim($_POST["pdfOrientation"]) : "");//default value will be taken safely
	$res = $util_obj->downloadHTMLAsPDF($input_html, $target_file, $force_download, $paper_size, $orientation);
	/**/
	$message = $res[1];
	if($res[0]==0) {
		echo "<h2 style='font-size:17px;font-weight:bold;font-family:Arial,sans-serif;line-height:23px;margin:0 0 10px 0;color:#FF0000;'>".$message."</h2>";
	} else {
		echo "<h2 style='font-size:17px;font-weight:bold;font-family:Arial,sans-serif;line-height:23px;margin:0 0 10px 0;color:#088A29'>".$message."</h2>";
	}
	/**/
?>