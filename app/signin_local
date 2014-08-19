<?php
	$APPLICATION_PATH = "./";
	include_once($APPLICATION_PATH."utils/utilfunctions.php");
	clearSession($APPLICATION_PATH);
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Jquery -->
		<script src="plugins/jquery/jquery.js"></script>
		<!-- Bootstrap -->
		<link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
		<script src="plugins/bootstrap/js/bootstrap.js"></script>
		<!-- Custom CSS-->
		<link href="css/custom.css" rel="stylesheet">
		<!-- JavaScript -->
	    <script src="js/app.js"></script>
	</head>
	<body data-offset="50" data-twttr-rendered="true" onload="document.getElementById('inputUser').focus();" style="background-color:lightyellow;">

		<?php
			session_start();
			$_SESSION['username'] = '';
			$_SESSION['password'] = '';
		?>
		<div class="navbar navbar-fixed-top navbar-inverses">
			<div class="navbar-inner">
			</div>
		</div>
		<div class="row-fluid">
			<div class="span8 loginimg">
			</div>
			<div class="span4" style="padding-top:10%;">
				<div class="loginwrappers">
					<div class="row-fluid" id="alertRow" style="display:none">
						<div id="alertDiv" class="span10">
						</div>
					</div>
					<form onsubmit="return false;">
						<p style="font-size:18px;">
							Sign in to
							<span style="color:#447FC8;">Churchstack</span>
						</p>
						<BR>
						<div>
							<input type="text" id="inputUser" placeholder="Username"><BR>
							<input type="password" id="inputPwd" placeholder="Password">
							<div id="divSignInBtn">
								<span>
									<button id="btnSignIn" class="btn btn-success btn-medium span3 offset1" align="center" onclick="authenticateUser();">Sign in</button>
								</span>
							</div>
							<div id="divLoadingSearchImg"style="display:none">
								<span><img src="images/ajax-loader.gif" />&nbsp;Please wait...</span>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="navbar navbar-fixed-bottom navbar-inverses">
			<div class="navbar-inner">
				<p align="right"><b>www.churchstack.com&nbsp;</b></p>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		var num = Math.floor((Math.random()*6)+1);
//		document.body.style.backgroundImage="url('../images/image-"+num+".jpg')";
//		document.body.style.backgroundImage="url('../images/church.jpg')";
	</script>
</html>