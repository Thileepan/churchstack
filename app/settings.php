<?php
//settings page
session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] == '' || !isset($_SESSION['password']) || $_SESSION['password'] == '')
{
	header('Location: signin.php');
	exit;
}
include "header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/settings.js"></script>

<div class="page-header">
    <h4 id="pageHeader">Manage Your Profile Settings</h4>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Profile Settings</li>
          <li id="listSalutationOptions" onclick="listProfileOptions(1);" class="active"><a href="#">Salutation</a></li>
		  <li id="listRelationshipOptions" onclick="listProfileOptions(2);" class=""><a href="#">Relationship</a></li>
		  <li id="listMaritalOptions" onclick="listProfileOptions(3);" class=""><a href="#">Marital Status</a></li>
		  <li id="listProfileStatusOptions" onclick="listProfileOptions(4);" class=""><a href="#">Profile Status</a></li>
		  <li id="listProfileCustomFields" onclick="listProfileAllCustomFields();" class=""><a href="#">Custom Profile Fields</a></li>
		  <li class="divider"></li>
		  <li class="nav-header">User Management</li>
          <li id="listUsers" onclick="listAllUsers();" class=""><a href="#">List Users</a></li>
          <li id="addNewUser" onclick="GetAddOrEditUserForm(0);" class=""><a href="#">Add New User</a></li>
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
	menuLinkClicked(4);
	listProfileOptions(1);
</script>