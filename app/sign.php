<?php
	$APPLICATION_PATH = "./";
	include_once($APPLICATION_PATH."conf/config.php");
	@include_once($APPLICATION_PATH."utils/utilfunctions.php");
	clearSession($APPLICATION_PATH);
	session_start();
?>
<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge">-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>ChurchStack - Login & Sign Up</title>

    <!-- Bootstrap core CSS -->
    <link href="css/mist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/mist/css/color-styles.css" rel="stylesheet">
    <link href="css/mist/css/ui-elements.css" rel="stylesheet">
    <link href="css/mist/css/custom.css" rel="stylesheet">
	
	<!-- Resources -->
	<link href="css/mist/css/animate.css" rel="stylesheet">
	<link href="css/mist/css/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body class="body-green">

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div>
			  <a href="<?php echo PRODUCT_WEBSITE; ?>" target="_blank"><img src="images/cs-web-logo.png" align="middle"></a>
		</div>
      <!-- div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
			  <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Home <b class="caret"></b></a>
				<ul class="dropdown-menu">
				  <li><a href="index.html">Home: Default</a></li>
				  <li><a href="index-alt.html">Home: Alternative</a></li>
				</ul>
			  </li>
			  <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages <b class="caret"></b></a>
				<ul class="dropdown-menu">
				  <li><a href="about-us.html">About Us</a></li>
				  <li><a href="blog.html">Blog</a></li>
				  <li><a href="blog-story.html">Blog Item</a></li>
				  <li><a href="contact-us.html">Contact Us</a></li>
				  <li><a href="error-page.html">Error Page</a></li>
				  <li><a href="faqs.html">FAQs</a></li>
				  <li><a href="gallery.html">Gallery</a></li>
				  <li><a href="gallery-item.html">Gallery Item</a></li>
				  <li><a href="pricing.html">Pricing</a></li>
				  <li><a href="responsive-video.html">Responsive Video</a></li>
				  <li><a href="sign-in.html">Sign In & Sign Up</a></li>
				</ul>
			  </li>
			  <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Blog <b class="caret"></b></a>
				<ul class="dropdown-menu">
				  <li><a href="blog.html">Blog</a></li>
				  <li><a href="blog-story.html">Blog Item</a></li>
				</ul>
			  </li>
			  <li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Gallery <b class="caret"></b></a>
				<ul class="dropdown-menu">
				  <li><a href="gallery.html">Gallery</a></li>
				  <li><a href="gallery-item.html">Gallery Item</a></li>
				</ul>
			  </li>
			  <li class="visible-xs"><a href="sign-in.html">Sign In & Sign Up</a></li>
			</ul>
			<form class="navbar-form navbar-left visible-xs" role="search">
			  <div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
			  </div>
			  <button type="submit" class="btn btn-default">Go!</button>
			</form>
			<ul class="nav navbar-nav navbar-right hidden-xs">
			  <li class="hidden" id="sign-in"><a href="#">Sign In</a></li>
			  <li class="hidden" id="sign-up"><a href="sign-in.html">Sign Up</a></li>
			  <li class="show animated flipInX" id="user-bar">
			    <span class="user-bar-avatar pull-right">
				  <img src="img/client-1.jpg" alt="...">
				</span>
				<a href="#" class="pull-right">user@mysite.com</a>
				<span class="pull-right user-bar-icons">
				  <a href="#"><i class="fa fa-sign-out" id="sign-out"></i></a>
				  <a href="#"><i class="fa fa-cog"></i></a>
				</span>
			  </li>
			  <li id="search">
				<a href="#" id="search-btn"><i class="fa fa-search" id="search-icon"></i> Search</a>
				<div class="search-box hidden" id="search-box">
				  <div class="input-group">
					<input type="text" class="form-control" placeholder="Search">
					<span class="input-group-btn">
					  <button class="btn btn-default" type="button">Go!</button>
					</span>
				  </div>
				</div>
			  </li>
			</ul>
        </div>
      </div -->
    </div>

    <!-- Body -->
	<div class="wrapper body-inverse"> <!-- wrapper -->
	  <div class="container">
	    <div class="row">
		  <!-- Sign In form -->
		  <div class="col-sm-5 col-sm-offset-1">
		    <h3>Login to your church account</h3>
			<!-- p class="text-muted">
			  Login to your account.
			</p -->
			<div class="form-white">
			  <form role="form">
			    <div class="form-group">
				  <label for="email">Email address</label>
				  <input type="email" class="form-control" id="inputUser" name="inputUser" placeholder="Enter email" onblur="javascript: this.value = trim(this.value.toLowerCase());">
			    </div>
			    <div class="form-group">
				  <label for="password">Password</label>
				  <input type="password" class="form-control" id="inputPwd" name="inputPwd" placeholder="Password">
			    </div>
			    <!-- div class="checkbox">
				  <label>
				    <input type="checkbox"> Remember me
				  </label>
			    </div -->
			    <button type="button" class="btn btn-block btn-color btn-xxl" onclick="authenticateUser();" data-loading-text="Authenticating..." id="btnSignIn" name="btnSignIn">Login</button>
			  </form>
			  <hr>
			  <p><a href="#" id="lost-btn">Lost your password?</a></p>
			  <div class="hidden" id="lost-form">
			  <p>Enter your email address and we will send you a link to reset your password.</p>
				<form role="form">
			      <div class="form-group">
				    <label for="email-lost">Email address</label>
				    <input type="email" class="form-control" id="email-lost" placeholder="Enter email">
			      </div>
				  <button type="submit" class="btn btn-default">Send</button>
				</form>
			  </div>
			  <div class="form-avatar hidden-xs">
				<span class="fa-stack fa-4x">
				  <i class="fa fa-circle fa-stack-2x"></i>
				  <i class="fa fa-user fa-stack-1x"></i>
				</span>
			  </div>
			</div>
		  </div>
		  <!-- Sign Up form -->
		  <div class="col-sm-5">
		    <h3 class="text-right-xs">Sign Up for a new account</h3>
			<!-- p class="text-muted text-right-xs">
			  Please fill out the form below to create a new account.
			</p -->
			<div class="form-white">
				<form role="form">
				  <div class="form-group">
				    <label for="church">Church Name</label>
					<input type="text" class="form-control" name="church" id="church" placeholder="Name of your church">
				  </div>
				  <div class="form-group">
				    <label for="referrerEmail">Referrer's Email Address</label>
					<input type="email" class="form-control" name="referrerEmail" id="referrerEmail" placeholder="Your Referrer's Email Address (if any)">
				  </div>
				  <div class="form-group">
				    <label for="name">Your Name</label>
					<input type="text" class="form-control" name="name" id="name" placeholder="Your Full Name">
				  </div>
				  <div class="form-group">
				    <label for="email">Your Email Address</label>
					<input type="email" class="form-control" name="email" id="email" placeholder="Enter your email address" onblur="javascript: this.value = trim(this.value.toLowerCase());">
				  </div>
				  <div class="form-group">
					<div class="row">
					  <div class="col-sm-6">
					  <label for="password">Password</label>
					  <input type="password" class="form-control" name="password" id="password" placeholder="Password">
					  </div>
					  <div class="col-sm-6">
					  <label for="confirmPassword">Repeat password</label>
					  <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Same Password Again">
					  </div>
					</div>
				  </div>
				<div class="form-group">
					<label class="control-label" for="securityText">Type the characters you see below</label>
					<div class="controls">
						<table border="0" cellpadding="1" cellspacing="0" height="42">
							<tr>
								<td width="25%"><span id="captchaSpan"><img src="plugins/simplecaptcha/image" alt="security image" /></span>&nbsp;</td>
								<td width="30%"><button class="btn btn-small" onclick="Javascript: return reloadSignupCaptcha();"><span nowrap><img src="images/blue-refresh.png" alt="Reload Security Image" /> Refresh</span></button></td>
								<td width="45%"><input type="text" name="securityText" id="securityText" class="form-control" placeholder="Type here"></td>
							</tr>
						</table>
					</div>
					<!-- div class="controls" style="padding-top:5px;">
					</div -->
				</div>
				  <div class="checkbox">
					<label>
					  <input type="checkbox" name="chkTOS" id="chkTOS"> I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
					</label>
				  </div>
				  <button type="button" class="btn btn-block btn-color btn-xxl" id="btnSignUp" name="btnSignUp" data-loading-text="Creating your account..." onclick="signUpNewAccount();">Create an account</button>
				</form>
			</div>
		  </div>
		</div>
	  </div>
	</div> <!-- / wrapper -->
    
	<div class="footer-wrapper"> <!-- footer wrapper -->
      <hr>
		<div class="container">
		  <footer>
			  <ul class="list-inline pull-left">
				<li><a href="about-us.html">About Us</a></li>
				<li><a href="#">Privacy Policy</a></li>
				<li><a href="#">Terms and Conditions</a></li>
				<li><a href="contact-us.html">Contact Us</a></li>
			  </ul>
			  <span class="pull-right-xs text-muted">&copy; The Mist Template</span>
			<div class="clearfix"></div>
		  </footer>
		</div> <!-- /container -->
	</div> <!-- / footer wrapper -->

	<!-- Style Toggle -->
	  <!-- i class="fa fa-gears fa-lg style-toggle-btn show hidden-xs"></i>
	  <div class="style-toggle text-center hidden">
	    <i class="fa fa-times-circle fa-2x style-toggle-close hidden-xs"></i>
		<ul>
		  <li class="green"></li>
		  <li class="blue"></li>
		  <li class="red"></li>
		  <li class="amethyst"></li>
		</ul>
	  </div -->
	
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="css/mist/js/bootstrap.min.js"></script>
    <script src="css/mist/js/custom.js"></script>
	<script src="css/mist/js/scrolltopcontrol.js"></script>
	<script src="js/app.js"></script>
	<script src="js/utils.js"></script>
	<script type="text/javascript">
		document.getElementById('inputUser').focus();
	</script>
</body></html>