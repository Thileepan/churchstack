<!DOCTYPE html>
<html lang="en">

   <head>
	<meta charset="utf-8">
    <title>Churchstack - Online Church Management Software</title>
	<meta name="viewport" content="width=device-width">
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
	
	<!-- Jquery -->
	<script src="<?php echo $APPLICATION_PATH; ?>plugins/jquery/jquery.js"></script>



	<!-- Bootstrap -->
    <link href="<?php echo $APPLICATION_PATH; ?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="<?php echo $APPLICATION_PATH; ?>plugins/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" media="screen">	
    <script src="<?php echo $APPLICATION_PATH; ?>plugins/bootstrap/js/bootstrap.js"></script>
	
	<!--
	<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>plugins/typeahead/js/typeahead.js"></script>
	<link href="<?php echo $APPLICATION_PATH; ?>plugins/typeahead/css/typeahead.css" rel="stylesheet">
	-->
	<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>plugins/typeahead/bootstrap-typeahead.js"></script>
	
	<!-- Custom CSS-->
	<link href="<?php echo $APPLICATION_PATH; ?>css/custom.css" rel="stylesheet">
	<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>-->

    <!-- DataTables -->
	<script src="<?php echo $APPLICATION_PATH; ?>plugins/datatables/media/js/jquery.dataTables.js"></script>
    <!-- JavaScript -->
    <script src="<?php echo $APPLICATION_PATH; ?>js/app.js"></script>
	<script src="<?php echo $APPLICATION_PATH; ?>js/utils.js"></script>

	<!-- DatePicker -->
    <link href="<?php echo $APPLICATION_PATH; ?>plugins/datepicker/css/datepicker.css" rel="stylesheet" media="screen">
    <script src="<?php echo $APPLICATION_PATH; ?>plugins/datepicker/js/bootstrap-datepicker.js"></script>

	<script type="text/javascript" src="plugins/datatables/extras/RowReordering/reordering.js"></script>

	<!-- Events -->
	<script type="text/javascript" src="plugins/fullcalendar1/fullcalendar.js"></script>
	<link href="<?php echo $APPLICATION_PATH; ?>plugins/fullcalendar1/fullcalendar.css" rel="stylesheet">	
	
  </head>

  <body data-offset="50" data-twttr-rendered="true">
  <?php include_once($APPLICATION_PATH . 'conf/config.php'); ?>
  <!-- ?php include_once($APPLICATION_PATH . 'classes/class.shardeddb.php'); ? -->

            <!-- navigation starts here -->
			<div class="navbar navbar-fixed-top navbar-inverse">
				<div class="navbar-inner">
					<div class="container-fluid">

						<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						</a>

						<!-- Be sure to leave the brand out there if you want it shown -->
						<a class="brand" href="#"><span style="color:white;font-weight:bold;">ChurchStack</span></a>

						<div class="nav-collapse collapse">
							<ul class="nav pull-left">
								<li class="" id="mdashboard" onclick="menulinkClicked(6)"><a href="dashboard.php" id="dashboardLink"><span id="mdashboardText" style="color:white">Dashboard</span></a></li>
								<li class="" id="mhome" onclick="menulinkClicked(1)"><a href="index.php" id="homeLink"><span id="mhomeText" style="color:white">Profiles</span></a></li>
								<li class="" id="msubscription" onclick="menulinkClicked(2)"style="display:nones"><a href="subscription.php" id="subscriptionLink"><span id="msubscriptionText" style="color:white">Subscription</span></a></li>
								<li class="" id="mfunds" onclick="menulinkClicked(9)"><a href="funds.php" id="fundsLink"><span id="mfundsText" style="color:white">Funds</span></a></li>
<!--								<li class="" id="mharvest" onclick="menulinkClicked(7)"><a href="harvest.php"><span id="mharvestText" style="color:white">Harvest</span></a></li> -->
								<li class="" id="mevents" onclick="menulinkClicked(3)"><a href="events.php" id="eventsLink"><span id="meventsText" style="color:white">Events</span></a></li>
								<li class="" id="mgroups" onclick="menulinkClicked(3)"><a href="groups.php" id="groupsLink"><span id="mgroupsText" style="color:white">Groups</span></a></li>
								<li class="" id="msettings" onclick="menulinkClicked(4)"><a href="settings.php" id="settingsLink"><span id="msettingsText" style="color:white;">Settings</span></a></li>
								<li class="" id="mreports" onclick="menulinkClicked(5)"><a href="reports.php" id="reportsLink"><span id="mreportsText" style="color:white">Reports</span></a></li>
							</ul>
							<ul class="nav pull-right">
								<li class="dropdown">
									<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span style="color:white;"><i class="icon-user icon-white"></i>&nbsp;<?php echo $_SESSION['username']; ?><b class="caret"></b></span></a>
									<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
										<!--
										<li><a href="#"><i class="icon-fire"></i>&nbsp;Version</a></li>
										<li><a href="#"><i class="icon-wrench"></i>&nbsp;Settings</a></li>
										-->
										<li><a href="info.php"><i class="icon-info-sign"></i>&nbsp;About Church</a></li>
										<li><a href="signin.php"><i class="icon-off"></i>&nbsp;Logout</a></li>
										<li class="divider"></li>
										<li><a href="#">Email: help@churchstack.com</a></li>
										<li><a href="#">Version: <?php echo APP_VERSION; ?> (Beta)</a></li>
										<li><a href="#">Build Number: <?php echo APP_BUILD_NUMBER; ?></a></li>
									</ul>
								</li>
							<!--
								<li><a href="#" style="color:white">Logged in as:&nbsp;<span class="label label-success"><?php echo $_SESSION['username'];?></span></a></li>
								<li><a href="login" style="color:white">Logout</a></li>
								-->
							</ul>
						</div>
					</div>
				</div>
			</div>
            <!-- navigation ends here -->

            <div class="container-fluid" style="padding-top:50px;">
			<!-- Footer content is included in the separate page -->