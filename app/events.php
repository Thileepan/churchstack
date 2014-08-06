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
include "header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/events.js"></script>

<div class="page-header">
    <h4 id="pageHeader">Scheduled Events</h4>
</div>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-list">
          <li class="nav-header">Event Management</li>
          <li id="calendar" onclick="showMonthlyCalendar(1);" class="active"><a href="#">Calendar</a></li>
		  <li id="addEvent" onclick="getAddOrEditEventForm(0);"><a href="#">Add New Event</a></li>
		  <li id="listEvents" onclick="showEventTabs();"><a href="#">List Events</a></li>
        </ul>
    </div>
	<div class="span10">
		<p id="pageTitle" class="lead muted"></p>
		<div class="row-fluid" id="alertRow" style="display:none">
			<div id="alertDiv" class="span12">
			</div>
		</div>
		<div class="row-fluid">
			<div id="pageContent" class="span10">				
			</div>
			<input type="hidden" id="hiddenEventTabID" value="1" />
		</div>
	</div>
</div>
<?php
include "footer.php";
?>

<script type='text/javascript'>
	menuLinkClicked(3);
	showMonthlyCalendar(1);
</script>