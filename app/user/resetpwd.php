<?php
	$APPLICATION_PATH = "../";
	@include_once($APPLICATION_PATH."utils/utilfunctions.php");
	@include_once($APPLICATION_PATH."conf/config.php");
	@include_once($APPLICATION_PATH."classes/class.users.php");
	clearSession($APPLICATION_PATH);
	session_start();

	$users_obj = new Users($APPLICATION_PATH);
	$key1 = $_GET["key1"];
	$email = @base64_decode($key1);
	$key2 = $_GET["key2"];
	$pwd_reset_key = @base64_decode($key2);
	$valididy_result = $users_obj->verifyPasswordResetURLValidity($email, $pwd_reset_key);
	if($valididy_result[0] != 1) {
		@require $APPLICATION_PATH.'error/404.php';
		exit;
	}
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
	<body data-offset="50" data-twttr-rendered="true" onload="document.getElementById('txtPassword').focus();">
		<div class="container">
			<div class="span12 text-center" id="introHeaderDiv">
				<div style="color:brown;"><h4>Set a new password for your account to login to <?php echo PRODUCT_WEBSITE; ?></h4></div>
			</div>
			<div class="span12 text-center" id="finalMsgDiv" style="padding-top:75px; display:none;">
				<div style="color:green;"><h4>You have now set a new password for your account successfully. You can now login to <a href="<?php echo CS_LOGIN_WEBSITE; ?>" target="_parent"><?php echo CS_LOGIN_WEBSITE; ?></a> with your new password to access your account.</h4></div>
			</div>
			<div class="span12 text-center" style="padding-top:20px; height:30px">
				<span class="label label-danger" id="errorDiv"></span>
				<span class="label label-success" id="successDiv"></span>
			</div>
			<div class="offset3 span4 text-center" style="padding-top:20px;" id="emailDiv">
				<div class="row-fluid well text-center">
					<form onsubmit="return false;">
						<!-- div class="control-group">
							<label class="control-label" for="txtEmail">Email</label>
							<div class="controls">
								<input type="text" id="txtEmail" class="span4" name="txtEmail" value="">
							</div>
						</div -->

						<div class="span12 text-center">
							<div class="control-group">
								<div class="controls">
									<input type="text" id="txtEmail" class="span12" name="txtEmail" placeholder="Email Address" value="<?php echo trim($email); ?>" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return forgotPassword(2)}" readonly>
								</div>
								<div class="controls" id="newPwdText" style="display:;">
									<input type="password" id="txtPassword" class="span12" name="txtPassword" placeholder="New Password" value="" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return forgotPassword(2)}">
								</div>
								<div class="controls" id="reenterPwdText" style="display:;">
									<input type="password" id="txtConfirmPassword" class="span12" name="txtConfirmPassword" placeholder="Repeat New Password" value="" onkeypress="Javascript:if(typeof event != 'undefined' && event.keyCode === 13){return changePassword(2)}">
								</div>
							</div>
							<div class="span12" id="continueBtnDiv">
								<span>
									<button id="btnContinue" class="span12 btn btn-primary btn-large btn-block" align="left" onclick="return forgotPassword(2);">Continue</button>
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