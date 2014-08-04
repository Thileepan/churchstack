<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>Signin | Online church management software</title>
    <meta name="description" content="Churchstack is simple and easy to use online church management software. It has many cool features like Profile management, Subscription management, Event management, Harvest management, Reports and many more."/>
	<meta name="keywords" content="Churchstack, Church, Church Software, Online Church Management Software" />
	<meta name="author" content="Churchstack Team"/>
    <meta name="viewport" content="width=device-width">
    
    <link href="../css/bootstrap-custom.min.css" rel="stylesheet">                
    <link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet">
    <link href="../css/font-awesome.min.css" rel="stylesheet">   
       
    <link href="../css/launched-amethyst.css" rel="stylesheet">
    <link href="../css/launched-responsive.css" rel="stylesheet">

    <!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- JavaScript -->
	<script src="js/app.js"></script>

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-47479249-1', 'churchstack.com');
	  ga('send', 'pageview');

	</script>
    
</head>

<body class="fixed-header">

	<?php
		session_start();
		$_SESSION['username'] = '';
		$_SESSION['password'] = '';
	?>
        
	<div class="navbar">
      <div class="navbar-pad">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="../index.html"><img src="../img/cslogo.png" alt="Logo" /></a>
          <div class="nav-collapse collapse">
            
          	<ul class="nav pull-left">
				<li class=""><a href="../index.html" class="action">Home</a></li>
				<li class=""><a href="../about.html" class="action">About Us</a></li>
				<li class=""><a href="../contact.html" class="action">Contact Us</a></li>
				<li class=""><a href="../pricing.html" class="action">Pricing</a></li>
			</ul>

			
		    <ul class="nav pull-right">	
				<li class="divider-vertical"></li>	      
		        <li><a href="signup.html" class="action action-primary">Start Free Trial!</a></li>
		        <li><a href="signin.php" class="action">Demo</a></li>
		    </ul>



          </div> <!-- /.nav-collapse -->
        </div> <!-- /.container -->
      </div> <!-- /.navbar-pad -->
    </div> <!-- /.navbar -->





<section id="masthead" class="masthead-sub">
	
	<div class="container">
		
		<h2>Sign In</h2>
		
	</div> <!-- /.container -->
	
</section> <!-- /#masthead -->        





<section id="content">

    <div class="container">
    	
    	<div class="container">
    		
    		<br />
    		
			<div class="row">
				<div class="span4 offset4 well">
					<h3>Please Sign In</h3>

					<br />
					<div class="row-fluid" id="alertRow" style="display:none">
						<div id="alertDiv" class="span12">
						</div>
					</div>
					
					<form onsubmit="return false;">
						
						<div class="control-group">
							<label class="control-label" for="inputUser">Username</label>
							<div class="controls">
								<input type="text" id="inputUser" class="span4" name="inputUser" placeholder="Username" value="">
							</div>
						</div> <!-- /.control-group -->
						
						<div class="control-group">
							<label class="control-label" for="inputPwd">Password</label>
							<div class="controls">
								<input type="password" id="inputPwd" class="span4" name="inputPwd" placeholder="Password" value="">
							</div>
						</div> <!-- /.control-group -->
						<div id="divSignInBtn">
							<span>
								<button id="btnSignIn" class="btn btn-primary btn-large btn-block" align="center" onclick="authenticateUser();">Sign in</button>
							</span>
						</div>
					
					
		            <label class="checkbox" style="display:none">
		            	<input type="checkbox" name="remember" value="1"> Remember Me
		            </label>
		            <br />
					<div id="divLoadingSearchImg"style="display:none">
						<span><img src="images/ajax-loader.gif" />&nbsp;Please wait...</span>
					</div>
					</form>    
				</div>
			</div>
		</div> <!-- /.row -->
        	
    </div> <!-- /container -->
            
</section> <!-- /#content -->





<section id="extra">
	
	<div class="container">
		
		<div class="row">
			
			<div class="span3">
			  	
				<span style="font-size:16px;font-weight:bold;">Contact Us</span>
				<br />
				<br />
				
				<ul class="mark-list small">
					<li>
						<span class="mark">
							<i class="icon-money"></i>
						</span>

						Email: help@churchstack.com
					</li>

				</ul>
				
			</div> <!-- /.span3 -->
			  
			<div class="offset6 span3">
			
				<h4>We're Social</h4>
					
				<ul class="mark-list small">
					<li>
						<span class="mark">
							<i class="icon-facebook-sign"></i>
						</span> 

						<a href="http://facebook.com/churchstack" target="_blank">Facebook</a>
					</li>

					<li>
						<span class="mark">
							<i class="icon-twitter"></i>
						</span>

						<a href="http://twitter.com/church_stack" target="_blank">Twitter</a>
					</li>
					
				</ul>		
				  
			</div> <!-- /.span3 -->
				
			
		
		</div> <!-- /.row -->
		
	</div> <!-- /.container -->
	
</section> <!-- /#extra -->





<footer>
	
	<div class="container">
		
		<ul class="pull-left">
			<li>&copy; 2013 <a href="javascript:;">Churchstack</a></li>
    	</ul>
    		
		<ul class="pull-right">
			
			<li>help@churchstack.com</a></li>
		</ul>
    		
	</div> <!-- /.container -->
	
</footer>


<script src="../js/vendor/jquery-1.9.1.min.js"></script>

<script src="../js/vendor/bootstrap.min.js"></script>

<script src="../js/plugins/bootstrap-dropdown/twitter-bootstrap-hover-dropdown.min.js"></script>

<script src="../js/main.js"></script>

<script type="text/javascript">
	document.getElementById('inputUser').focus();
</script>

</body>

</html>