<?php
//events page
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
<script src="<?php echo $APPLICATION_PATH; ?>js/groups.js"></script>
<script src="<?php echo $APPLICATION_PATH; ?>js/reports.js"></script>

<div class="page-header">
    <h4 id="pageHeader">Add New Group</h4>
</div>
<div class="pull-right" id="divOptionBtn" style="display:none">
	<button class="btn btn-small btn-primary" type="button" onclick="showListReportsDiv();">List Report Templates</button>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Group Management</li>
		  <li id="addGroup" onclick="getAddOrEditGroupForm(0);"><a href="#">Add New Group</a></li>
		  <li id="listGroup" onclick="listAllGroups();"><a href="#">List Groups</a></li>		  
        </ul>
    </div>
	<div class="span10">
		<p id="pageTitle" class="lead muted"></p>
		<div class="row-fluid" id="alertRow" style="display:none">
			<div id="alertDiv" class="span12">
			</div>
		</div>
		<div class="row-fluid">
			<div id="pageContent" class="span12">				
			</div>
			<input type="hidden" id="hiddenGroupID" value="1" />
		</div>
	</div>
</div>
<?php
include "footer.php";
?>

<script type='text/javascript'>
	menuLinkClicked(8);
	getAddOrEditGroupForm(0);
</script>