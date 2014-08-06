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
include "header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/harvest.js"></script>

<div class="page-header">
    <h4 id="pageHeader">Add New Harvest</h4>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Harvest Management</li>
          <li id="addHarvest" onclick="getHarvestForm(0);" class="active"><a href="#">Add Harvest</a></li>
		  <li id="listHarvest" onclick="listAllHarvests(0);" class=""><a href="#">List Harvests</a></li>
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
					<h3>Edit Harvest</h3>
				</div>
				<div class="modal-body" id="modalBody">
					
				</div>
				<div class="modal-footer" id="modalFooter">
					<a href="#" class="btn" data-dismiss="modal" aria-hidden="true" id="subModalCloseBtn">Close</a>
					<a href="#" class="btn btn-primary" onclick="addOrUpdateHarvest(1);">Save changes</a>					
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
	menuLinkClicked(7);
	getHarvestForm(0);


</script>