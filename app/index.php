<?php

// ***********************************************************/
//
// Author		:	Thileepan Sivanandham
// Application	:	Churchstack
// Website		:	www.churchstack.com
// Email		:	help@churchstack.com
// Usage		:	Online Church Management Software
//
// ***********************************************************/

session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] == '' || !isset($_SESSION['password']) || $_SESSION['password'] == '')
{
	header('Location: signin.php');
	exit;
}
include "header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/subscriptions.js"></script>
<script src="<?php echo $APPLICATION_PATH; ?>js/harvest.js"></script>
<script src="<?php echo $APPLICATION_PATH; ?>plugins/jquery/jquery.form.js"></script>

<div class="page-header">
    <h4 id="pageHeader">List of Profiles</h4>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Profile Management</li>
          <li id="listProfiles" onclick="listAllProfiles(1);" class="active"><a href="#">List Profiles</a></li>
          <li id="addNewProfile" onclick="GetAddOrEditProfileForm(0);"><a href="#">Add New Profile</a></li>
		  <li id="importProfiles" onclick="getImportProfileForm();"><a href="#">Import Profiles</a></li>
	</div>
	<div class="span10">
		<div class="row-fluid" id="loadingDiv" style="display:none">
			<div class="offset5 span6">
				<span  style="padding:6px;background:none repeat scroll 0 0 #FAFBD2;border-color: #E8EAC0 #F5F7CE #F5F7CE #E8EAC0;border-image: none;border-radius: 4px 4px 4px 4px;border-style: solid;border-width: 1px;font-weight:bold;padding:10px;">Loading...</span>
			</div>
		</div>
		<div id="backBtnDiv" onclick="goBackProfilePage();" class="pull-right" style="display:none"><button class="btn btn-small btn-primary">Back</button><BR><BR></div>

		<div class="row-fluid" id="alertRow" style="display:none">
			<div id="alertDiv" class="span12">
			</div>
		</div>
		<div class="row-fluid">
			
			<div id="listProfilesContent" class="span12">
                <table id="listProfilesTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Member ID</th>
							<th></th>
                            <th>Name</th>
                            <th>Date Of Birth</th>
							<th>Age</th>
							<th>Landline</th>
                            <th>Mobile Number</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
			<div id="pageContent" class="span12" style="display:none">
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
					<a id="subscriptionSaveBtn" href="#" class="btn btn-primary" onclick="addOrUpdateNewSubscription(1);">Save changes</a>
					<a id="harvestSaveBtn" href="#" class="btn btn-primary" onclick="addOrUpdateHarvest(1);">Save changes</a>
				</div>
			</div>
			<input type="hidden" id="screenID" value="1" />

        </div>
    </div>
</div>

<script type="text/javascript">
	menuLinkClicked(1);
	listAllProfiles(0);
	$('.datepicker').datepicker();
</script>

<?php
include "footer.php";
?>