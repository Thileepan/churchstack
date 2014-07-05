<?php
	$APPLICATION_PATH = "../";
?>
<!DOCTYPE html>
<html lang="en">

   <head>
	<meta charset="utf-8">
    <title>Churchstack - Online Church Management Software</title>
	<meta name="viewport" content="width=device-width">
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
	
	<!-- Jquery -->
	<script src="<?php echo $APPLICATION_PATH; ?>app/plugins/jquery/jquery.js"></script>



	<!-- Bootstrap -->
    <link href="<?php echo $APPLICATION_PATH; ?>app/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="<?php echo $APPLICATION_PATH; ?>app/plugins/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" media="screen">	
    <script src="<?php echo $APPLICATION_PATH; ?>app/plugins/bootstrap/js/bootstrap.js"></script>
	
	<!--
	<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>plugins/typeahead/js/typeahead.js"></script>
	<link href="<?php echo $APPLICATION_PATH; ?>plugins/typeahead/css/typeahead.css" rel="stylesheet">
	-->
	<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>app/plugins/typeahead/bootstrap-typeahead.js"></script>
	
	<!-- Custom CSS-->
	<link href="<?php echo $APPLICATION_PATH; ?>app/css/custom.css" rel="stylesheet">
	<!--<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>-->

    <!-- DataTables -->
	<script src="<?php echo $APPLICATION_PATH; ?>app/plugins/datatables/media/js/jquery.dataTables.js"></script>
    <!-- JavaScript -->
    <!-- script src="<?php echo $APPLICATION_PATH; ?>app/js/app.js"></script -->
	<!-- script src="<?php echo $APPLICATION_PATH; ?>app/js/utils.js"></script -- >

	<!-- DatePicker -->
    <link href="<?php echo $APPLICATION_PATH; ?>app/plugins/datepicker/css/datepicker.css" rel="stylesheet" media="screen">
    <script src="<?php echo $APPLICATION_PATH; ?>app/plugins/datepicker/js/bootstrap-datepicker.js"></script>

	<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>app/plugins/datatables/extras/RowReordering/reordering.js"></script>

	<!-- Events -->
	<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>app/plugins/fullcalendar1/fullcalendar.js"></script>
	<link href="<?php echo $APPLICATION_PATH; ?>app/plugins/fullcalendar1/fullcalendar.css" rel="stylesheet">	
	
  </head>

  <body data-offset="50" data-twttr-rendered="true">

<div class="container-fluid" style="padding-top:8px;">
	<div>
		<ul class="nav nav-pills">
			<li<?php echo (($page_id==1)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."admin/church.php"; ?>">Churches</a></li>
			<li<?php echo (($page_id==2)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."admin/user.php"; ?>">Users</a></li>
			<li<?php echo (($page_id==3)?' class="active"' : '');?>><a href="<?php echo $APPLICATION_PATH."admin/payment.php"; ?>">Payments</a></li>
			<!-- li class="dropdown open">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					Dropdown <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#">Action</a></li>
					<li><a href="#">Another action</a></li>
					<li><a href="#">Something else here</a></li>
					<li class="divider"></li>
					<li><a href="#">Separated link</a></li>
				</ul>
			</li-->
		</ul>
	</div>
<?php
?>