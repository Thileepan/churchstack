<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."utils/utilfunctions.php");
	clearSession($APPLICATION_PATH);
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Jquery -->
		<script src="<?php echo $APPLICATION_PATH;?>plugins/jquery/jquery.js"></script>
		<!-- Bootstrap -->
		<link href="<?php echo $APPLICATION_PATH;?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
		<script src="<?php echo $APPLICATION_PATH;?>plugins/bootstrap/js/bootstrap.js"></script>
		<!-- Custom CSS-->
		<link href="<?php echo $APPLICATION_PATH;?>css/custom.css" rel="stylesheet">
		<!-- JavaScript -->
	    <script src="<?php echo $APPLICATION_PATH;?>js/app.js"></script>
	    <script src="<?php echo $APPLICATION_PATH;?>js/users.js"></script>
	</head>
	<body data-offset="50" data-twttr-rendered="true" onload="document.getElementById('txtEmail').focus();">
		<div class="container">
			<div class="span12 text-center">
				<div style="color:brown;"><h4>Forgot your password?<BR/><BR/>Just enter the email address with which you would usually login to your ChurchStack account. An email will be sent to you which will have the instructions to reset your password.</h4></div>
			<div>
			<div class="span12 text-center" style="padding-top:20px; height:30px">
				<span class="label label-danger" id="errorDiv"></span>
				<span class="label label-success" id="successDiv"></span>
			<div>
			<div class="offset3 span4 text-center" style="padding-top:20px;" id="emailDiv">
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
								<div class="controls">
									<input type="text" id="txtEmail" class="span12" name="txtEmail" placeholder="Email Address" value="" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return forgotPassword(1)}">
								</div>
								<!-- div class="controls" id="accessPwdText" style="display:none;">
									<input type="password" id="accessPwd" class="span12" name="accessPwd" placeholder="Access Password" value="" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return validateLogin(2)}">
								</div-->
							</div>
							<div class="span12" id="continueBtnDiv">
								<span>
									<button id="btnContinue" class="span12 btn btn-primary btn-large btn-block" align="left" onclick="return forgotPassword(1);">Continue</button>
								</span>
							</div>
							<!-- div class="span12" id="accessBtnDiv" style="display:none;">
								<span>
									<button id="btnAccess" class="span12 btn btn-primary btn-large btn-block" align="left" onclick="return validateLogin(2);">Get Access</button>
								</span>
							</div-->
							<div id="continueProgDiv" style="display:none;">
								<span><img src="<?php echo $APPLICATION_PATH;?>images/ajax-loader.gif">&nbsp;Please wait...</span>
							</div>
						<div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>