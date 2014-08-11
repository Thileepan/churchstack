<?php
//subscriptions page
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
if(!SHOW_SUBSCRIPTION_MENU) {
	@require $APPLICATION_PATH.'error/404.php';
	exit;
}
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/subscriptions.js"></script>

<div class="page-header">
    <h4 id="pageHeader">Manage Subscription Fields</h4>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Subscription Management</li>
          <li id="listSubscriptionFields" onclick="listSubscriptionFields();" class="active"><a href="#">Subscription Fields</a></li>
		  <li id="addSubscription" onclick="getSubscriptionForm(0);" class=""><a href="#">Add Subscription</a></li>
		  <li id="listSubscriptions" onclick="listAllSubscriptions(0);" class=""><a href="#">List Subscriptions</a></li>
        <!--  <li class="divider"></li> -->
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
			
			<div id="subModal" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-header" id="modalHeader">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>Edit Subscription</h3>
				</div>
				<div class="modal-body" id="modalBody">
					
				</div>
				<div class="modal-footer" id="modalFooter">
					<a href="#" class="btn" data-dismiss="modal" aria-hidden="true" id="subModalCloseBtn">Close</a>
					<a href="#" class="btn btn-primary" onclick="addOrUpdateNewSubscription(1);">Save changes</a>					
				</div>
			</div>
			<input type="hidden" id="screenID" value="2" />
		</div>
	</div>
</div>

<?php
include "footer.php";
?>

<script type='text/javascript'>
	menuLinkClicked(2);
	listSubscriptionFields();
</script>