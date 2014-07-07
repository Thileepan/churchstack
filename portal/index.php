<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."portal/utils/auth.php");
	clearSession($APPLICATION_PATH);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Jquery -->
		<script src="<?php echo $APPLICATION_PATH;?>app/plugins/jquery/jquery.js"></script>
		<!-- Bootstrap -->
		<link href="<?php echo $APPLICATION_PATH;?>app/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
		<script src="<?php echo $APPLICATION_PATH;?>app/plugins/bootstrap/js/bootstrap.js"></script>
		<!-- Custom CSS-->
		<link href="<?php echo $APPLICATION_PATH;?>app/css/custom.css" rel="stylesheet">
		<!-- JavaScript -->
	    <script src="<?php echo $APPLICATION_PATH;?>app/js/app.js"></script>
		<script src="<?php echo $APPLICATION_PATH; ?>portal/js/utils.js"></script>
		<script src="<?php echo $APPLICATION_PATH; ?>portal/js/login.js"></script>
	</head>
	<body data-offset="50" data-twttr-rendered="true" onload="document.getElementById('loginPwd').focus();" style="background-color:lightyellow;">
		<div class="container">
			<div class="span12 text-center" style="padding-top:80px;">
				<div style="color:blue;height:30px;"><h2>Welcome to ChurchStack Admin Portal</h2></div>
			<div>
			<div class="span12 text-center" style="padding-top:20px;">
				<div id="errorDiv" style="color:red;height:20px;"></div>
				<div id="successDiv" style="color:green;height:20px;"></div>
			<div>
			<div class="offset3 span4 text-center" style="padding-top:20px;">
				<div class="row-fluid well text-center">
					<form onsubmit="return false;">
						<!-- div class="control-group">
							<label class="control-label" for="inputUser">Username</label>
							<div class="controls">
								<input type="text" id="inputUser" class="span4" name="inputUser" placeholder="Username" value="admin">
							</div>
						</div-->

						<div class="span12 text-center">
							<div class="control-group">
								<!-- label class="control-label" for="inputPwd">Password</label -->
								<div class="controls" id="loginPwdText">
									<input type="password" id="loginPwd" class="span12" name="loginPwd" placeholder="Login Password" value="" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return validateLogin(1)}">
								</div>
								<div class="controls" id="accessPwdText" style="display:none;">
									<input type="password" id="accessPwd" class="span12" name="accessPwd" placeholder="Access Password" value="" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return validateLogin(2)}">
								</div>
							</div>
							<div class="span12" id="loginBtnDiv">
								<span>
									<button id="btnLogIn" class="span12 btn btn-primary btn-large btn-block" align="left" onclick="return validateLogin(1);">Login</button>
								</span>
							</div>
							<div class="span12" id="accessBtnDiv" style="display:none;">
								<span>
									<button id="btnAccess" class="span12 btn btn-primary btn-large btn-block" align="left" onclick="return validateLogin(2);">Get Access</button>
								</span>
							</div>
							<div id="loginProgDiv" style="display:none;">
								<span><img src="<?php echo $APPLICATION_PATH;?>app/images/ajax-loader.gif">&nbsp;Please wait...</span>
							</div>
						<div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>