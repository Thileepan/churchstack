//global variables
doDashboardFile = 'server/dodashboard';

function getDashboardData(option)
{
	//option = opt;
	var formPostData = 'req='+option;
	$.ajax({
		type:'POST',
		url:doDashboardFile,
		data:formPostData,
		success:getDashboardDataResponse,
		error:HandleAjaxError
	});
}

function getDashboardDataResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var req = dataObj.reqType;
	var res = dataObj.response;
	var html = '';

	if(req == 1)	{
		document.getElementById('profileStats').innerHTML = res;
		/*
		html += '<div class="widget">';
			html += '<div class="widget-header">';
				html += '<i class="icon-asterisk"></i>';
				html += '<h3>Profile Stats</h3>';
				html += '<span class="pull-right" style="padding-right:10px;"><i class="icon-refresh curHand" onclick="getDashboardData(1);"></i></span>';
			html += '</div>';
			html += '<div class="widget-content">';
				html += '<div class="stats>';
					html += '<div class="stat>';
						html += '<div class="span4" id="chart1" style="height:200px; width:300px;"></div>';
					html += '</div>';
					html += '<div class="stat>';
						html += '<div class="span4" id="chart2" style="height:200px; width:300px;"></div>';
					html += '</div>';
					html += '<div class="stat>';
						html += '<div class="span4" id="chart3" style="height:200px; width:300px;"></div>';
					html += '</div>';
				html += '</div>';
			html += '</div>';
		html += '</div>';
		document.getElementById('profileStats').innerHTML = html;

		var s1 = [['a',6], ['b',8], ['c',14], ['d',20]];
		var s2 = [['a', 8], ['b', 12], ['c', 6], ['d', 9]];
		var s3 = [['a', 3], ['b', 5], ['c', 7], ['d', 11]];

		var plot1 = $.jqplot('chart1', [s1], {
		
			grid: {
					drawBorder: false, 
					drawGridlines: false,
					background: '#ffffff',
					shadow:false
				},
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

		var plot2 = $.jqplot('chart2', [s2], {
		
			grid: {
					drawBorder: false, 
					drawGridlines: false,
					background: '#ffffff',
					shadow:false
				},
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

		var plot3 = $.jqplot('chart3', [s3], {
		
			grid: {
					drawBorder: false, 
					drawGridlines: false,
					background: '#ffffff',
					shadow:false
				},
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
		*/

		
	} else if (req == 2)	{
		document.getElementById('contributionStats').innerHTML = res;
	} else if (req == 3)	{
		document.getElementById('eventStats').innerHTML = res;
		var reqFrom = 2;
		showMonthlyCalendar(reqFrom);
		return true;
	}
}