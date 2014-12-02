<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	@include_once($APPLICATION_PATH."app/classes/class.church.php");
	validateSession($APPLICATION_PATH);

	//$page_id=5;
	@include($APPLICATION_PATH."portal/header.php");
	@include($APPLICATION_PATH."portal/includes.php");
	$submenu_id = (isset($_REQUEST["si"])? trim($_REQUEST["si"]) : 1);

	$church_id_list = array();
	$church_name_list = array();
	$church_country_list = array();
	$DELIMITER = "-:-";
	$church_obj = new Church($APPLICATION_PATH."app/");
	$churches_res = $church_obj->getAllChurchesList(0);//List all churches
	if($churches_res[0]==1) {
		for($i=0; $i < COUNT($churches_res[1]); $i++)
		{
			//array($church_id, $church_name, $church_desc, $church_addr, $landline, $mobile, $email, $website, $signup_time, $last_update_time, $sharded_database, $currency_id, $unique_hash, $status, $country_id);
			$church_id_list[] = $churches_res[1][$i][0];
			$church_name_list[] = $churches_res[1][$i][1];
			$church_address_list[] = $churches_res[1][$i][3];
			$church_db_list[] = $churches_res[1][$i][10];
			$church_country_list[] = $churches_res[1][$i][14];
		}
	}

	$upgrade_file_path = "../../app/sql/upgrade/sharded.upgrade.sql";
?>
<script src="<?php echo $APPLICATION_PATH; ?>portal/js/upgrade.js"></script>
<script>
</script>
	<div class="row-fluid">
		<div class="span12" style="display:;">
			<div class="row-fluid" id="alertRow" style="display:;">
				<div class="span12">
					<div class="alert alert-warning">The upgrade will be run for all the churches listed in the table below. Scroll down all the way to the end to see the button to initiate uprgade process.</div>
					<div class="alert alert-info">
						<ul>
							<li>SQL file going to be used : <b><?php echo $upgrade_file_path; ?></b></li>
							<li>Total databases going to be upgraded : <b><?php echo COUNT($church_db_list); ?></b></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<form id="upgradeForm" class="form-horizontal" action="server/doupgrade.php" method="post" enctype="multipart/form-data" onsubmit="return false;">
					<div class="row-fluid">
						<div class="span12">
							<?php
								$church_data_html = "";
								$church_data_html .= '<div class="table-responsive">';
									$church_data_html .= '<table class="table table-bordered">';
										$church_data_html .= '<thead>';
											$church_data_html .= '<tr>';
												$church_data_html .= '<th style="background: #eee;"><b>Church ID</b></th>';
												$church_data_html .= '<th style="background: #eee;"><b>Church Name</b></th>';
												$church_data_html .= '<th style="background: #eee;"><b>Address</b></th>';
												$church_data_html .= '<th style="background: #eee;"><b>Database</b></th>';
												$church_data_html .= '<th style="background: #eee;"><b>Country</b></th>';
											$church_data_html .= '</tr>';
										$church_data_html .= '</thead>';
										$church_data_html .= '<tbody>';
										for($c=0; $c < COUNT($church_id_list); $c++)
										{
											$church_data_html .= '<tr>';
												$church_data_html .= '<td>'.$church_id_list[$c].'</td>';
												$church_data_html .= '<td>'.$church_name_list[$c].'</td>';
												$church_data_html .= '<td>'.$church_address_list[$c].'</td>';
												$church_data_html .= '<td>'.$church_db_list[$c].'</td>';
												$church_data_html .= '<td>'.$church_country_list[$c].'</td>';
											$church_data_html .= '</tr>';
										}
										$church_data_html .= '</tbody>';
									$church_data_html .= '</table>';
								$church_data_html .= '<div>';

								echo $church_data_html;
							?>
						</div>
					</div>
					<!-- div class="row-fluid">
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtDiscountPerc">Discount Percentage</label><div class="controls"><input type="text" id="txtDiscountPerc" placeholder="Discount Percentage" value=""> %</div>
							</div>
						</div>
						<div class="span6">
							<div style="padding-bottom:6px;"><label class="control-label" for="txtDiscountFlat">Discount Flat Amt (USD)</label><div class="controls"><input type="text" id="txtDiscountFlat" placeholder="Discount Flat Amount" value=""></div>
							</div>
						</div>
					</div -->
					<div class="row-fluid">
						<div id="alertDiv" class="span12" style="display:none;">
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="form-actions"><button class="btn btn-primary" type="submit" onclick="return runUpgrade();" id="btnUpgrade">Run Upgrade Now</button><span id="progText" style="display:none;">Upgrade in progress. Please wait... It might take some minutes to complete the upgrade.</span>
							<input type="hidden" id="txtUpgradeFilePath" value="<?php echo $upgrade_file_path; ?>">
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="alert alert-info" role="alert" id="upgradeInfoDiv" style="display:none;"></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<!-- Button trigger modal -->
<!--button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#churchDetailsModal">
  Launch demo modal
</button -->

<!-- Modal -->
	<script type="text/javascript">
	</script>
<?php
	@include($APPLICATION_PATH."portal/footer.php")
?>