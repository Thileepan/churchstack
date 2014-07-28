<?php
//reports page
session_start();
if(!isset($_SESSION['username']) || $_SESSION['username'] == '' || !isset($_SESSION['password']) || $_SESSION['password'] == '')
{
	header('Location: signin.php');
	exit;
}
include "header.php";
?>
<script src="<?php echo $APPLICATION_PATH; ?>js/dashboard.js"></script>
<script src="<?php echo $APPLICATION_PATH; ?>js/events.js"></script>
<div class="page-header">
    <h4 id="pageHeader">Dashboard <small>Quick stats</small> </h4>
</div>
<div class="row-fluid">
	<div class="span6">
		<div class="span12" id="profileStats">
		</div>
		<div class="span12" id="contributionStats" style="margin-left:0px;">
		</div>
	</div>    
	<div class="span6" id="eventStats">
    </div>    
</div>
<div class="row-fluid">
    
    <div class="span6" id="Stats">
    </div>
</div>
<?php
include "footer.php";
?>
<script type='text/javascript'>
	menuLinkClicked(6);
	getDashboardData(1);
	getDashboardData(2);
	getDashboardData(3);
</script>
<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>plugins/jquery.jqplot/src/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>plugins/jquery.jqplot/src/plugins/jqplot.pieRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $APPLICATION_PATH; ?>plugins/jquery.jqplot/src/plugins/jqplot.donutRenderer.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $APPLICATION_PATH; ?>plugins/jquery.jqplot/src/jquery.jqplot.css" />

<script class="code" type="text/javascript">
$(document).ready(function(){
  var s1 = [['a',6], ['b',8], ['c',14], ['d',20]];
//  var s2 = [['a', 8], ['b', 12], ['c', 6], ['d', 9]];
  
  var plot3 = $.jqplot('chart3', [s1], {
    seriesDefaults: {
      // make this a donut chart.
      renderer:$.jqplot.DonutRenderer,
      rendererOptions:{
        // Donut's can be cut into slices like pies.
        sliceMargin: 3,
        // Pies and donuts can start at any arbitrary angle.
        startAngle: -90,
        showDataLabels: true,
        // By default, data labels show the percentage of the donut/pie.
        // You can show the data 'value' or data 'label' instead.
        dataLabels: 'value'
      }
    }
  });
});
</script>