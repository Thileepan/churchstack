<?php
//reports page
/** /
session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] == '' || !isset($_SESSION['password']) || $_SESSION['password'] == '')
{
	header('Location: signin.php');
	exit;
}
/**/
$APPLICATION_PATH = "./";
include $APPLICATION_PATH."header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/info.js"></script>

<div class="page-header">
    <h4 id="pageHeader">My Church</h4>
</div>
<div class="row-fluid">
	<div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Church Settings</li>
          <li id="myChurch" onclick="getChurchInformation();" class="active"><a href="#">My Church</a></li>
		  <li id="notifications" style="display:none;"><a href="#">Notifications</a></li>
		  <li id="billing" onclick="getBillingDetails();"><a href="#">Billing</a></li>		  
		</ul>
    </div>
    <div class="span10">
		<div class="row-fluid" id="alertRow" style="display:none">
			<div id="alertDiv" class="span12">
			</div>
		</div>
		<div class="row-fluid">
			<div id="pageContent" class="span12">
			</div>
		</div>
	</div>
</div>
<?php
include "footer.php";
?>
<script type='text/javascript'>
	getChurchInformation();
</script>