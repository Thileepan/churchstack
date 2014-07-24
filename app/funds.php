<?php
//subscriptions page
session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] == '' || !isset($_SESSION['password']) || $_SESSION['password'] == '')
{
	header('Location: signin.php');
	exit;
}
include "header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/funds.js"></script>

<div class="page-header">
    <h4 id="pageHeader">Manage Funds</h4>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Contribution Management</li>
          <li id="listFunds" onclick="listAllFunds();" class="active"><a href="#">Funds</a></li>
		  <li id="listBatches" onclick="listAllBatches();" class=""><a href="#">Batches</a></li>
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
	menuLinkClicked(9);
	listAllFunds();
</script>