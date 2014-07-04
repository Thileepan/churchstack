//global variables
doEventFile = 'server/doevents.php';

function showMonthlyCalendar(reqFrom)
{
	if(reqFrom == 1) {
		document.getElementById('calendar').className = 'active';
		document.getElementById('addEvent').className = '';
		document.getElementById('listEvents').className = '';
		document.getElementById('alertRow').style.display = 'none';
		document.getElementById('pageContent').innerHTML = '<div id="calendarView" style="height:333px"></div>';
	} else {
		document.getElementById('eventContent').innerHTML = '<div id="calendarView"></div>';
	}

	
	$('#calendarView').fullCalendar({
		// put your options and callbacks here
		//events: eval("(" + response + ")" )
		//events: 'server/doevents.php?req=1'

		events: {
			url: 'server/doevents.php',
			type: 'POST',
			data: {
				req: 1,
				custom_param2: 'somethingelse'
			},
			error: function() {
				alert('there was an error while fetching events!');
			},
//			color: 'yellow',   // a non-ajax option
//			textColor: 'black' // a non-ajax option
		}	
	});
}

function showMonthlyCalendarResponse(response)
{
	// page is now ready, initialize the calendar...
	document.getElementById('pageContent').innerHTML = '<div id="calendarView" style="height:333px"></div>';

	$('#calendarView').fullCalendar({
		// put your options and callbacks here
		events: eval("(" + response + ")" )
		/** /
		events: [
			{
				title  : 'This is the first event created by Thileepan Sivanandham',
				start  : '2014-03-01'
			},
			{
				title  : 'This is the first event created by Thileepan Sivanandham',
				start  : '2014-03-01',
				end    : '2014-03-01'
			},
			{
				title  : 'event3',
				start  : '2014-03-04 12:30:00',
				end	   : '2014-03-05 13:30:00',
				url	   : 'http://www.churchstack.com', 
				allDay : false // will make the time show
			}
		]
		/**/
	});
}

function getAddOrEditEventForm(isEdit, eventID)
{
	document.getElementById('calendar').className = '';
	document.getElementById('addEvent').className = 'active';
	document.getElementById('listEvents').className = '';

	var formPostData = "req=2&isEdit=" + isEdit;
	if(isEdit) {
		formPostData += "&eventID=" + eventID;
	}

	$.ajax({
		type:'POST',
		url:doEventFile,
		data:formPostData,
		success:getAddOrEditEventFormResponse,
		error:HandleAjaxError
	});
}

function getAddOrEditEventFormResponse(response)
{
	document.getElementById('pageContent').innerHTML = response;
	document.getElementById('inputEventTitle').focus();
	$('#inputEventStartDate').datepicker({
		autoclose: true
	});
	$('#inputEventEndDate').datepicker({
		autoclose: true
	});
}

function onchangeEventRepeats(obj)
{
	var index = obj.selectedIndex;
	var repeatValue = obj.options[index].value;

	if(repeatValue > 1) {
		document.getElementById('divOuterEventEvery').style.display = '';
		document.getElementById('divOuterEventEndDate').style.display = '';
	} else {
		document.getElementById('divOuterEventEvery').style.display = 'none';
		document.getElementById('divOuterEventEndDate').style.display = 'none';
		document.getElementById('divOuterEventOccursOn').style.display = 'none';
		document.getElementById('divInnerEventOccursOn_1').style.display = 'none';
	}
	
	if(repeatValue == 2) { //daily
		document.getElementById('spanEventEveryText').innerHTML = 'Day(s)';
		document.getElementById('divOuterEventOccursOn').style.display = 'none';
		document.getElementById('divInnerEventOccursOn_1').style.display = 'none';
	} else if(repeatValue == 3) { //weekly
		document.getElementById('spanEventEveryText').innerHTML = 'Week(s)';
		document.getElementById('divOuterEventOccursOn').style.display = '';
		document.getElementById('divInnerEventOccursOn_1').style.display = '';
		document.getElementById('divInnerEventOccursOn_2').style.display = 'none';
		document.getElementById('rowMonthList').style.display = 'none';
	} else if(repeatValue == 4) { //monthly
		document.getElementById('spanEventEveryText').innerHTML = 'Month(s)';
		document.getElementById('divOuterEventOccursOn').style.display = '';
		document.getElementById('divInnerEventOccursOn_1').style.display = 'none';
		document.getElementById('divInnerEventOccursOn_2').style.display = '';
		document.getElementById('rowMonthList').style.display = 'none';
	} else if(repeatValue == 5) { //yearly
		document.getElementById('spanEventEveryText').innerHTML = 'Year(s)';
		document.getElementById('divOuterEventOccursOn').style.display = '';
		document.getElementById('divInnerEventOccursOn_1').style.display = 'none';
		document.getElementById('divInnerEventOccursOn_2').style.display = '';
		document.getElementById('rowMonthList').style.display = '';
	} 
}

function OnChangeOccursOn(opt)
{
	if(opt == 1) {
		document.getElementById('monthDay').disabled = false;
		document.getElementById('weekNumber').disabled = true;
		document.getElementById('weekDay').disabled = true;		
	} else {
		document.getElementById('monthDay').disabled = true;
		document.getElementById('weekNumber').disabled = false;
		document.getElementById('weekDay').disabled = false;
	}
}

function addOrUpdateEvents(val)
{
	isUpdate = val;

	//Setting default values
	var freq = 1;
	var interval = 0;
	var day = '';
	var monthDay = '';
	var month = '';
	var endDate = 0;
	var weekDayArray = Array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');

	var title = document.getElementById('inputEventTitle').value;
	var desc = document.getElementById('inputEventDesc').value;
	var loc = document.getElementById('inputEventLocation').value;
	var organiser = document.getElementById('inputEventOrangiser').value;
	var startDate = document.getElementById('inputEventStartDate').value;
	var endDate = document.getElementById('inputEventEndDate').value;
	var startHour = document.getElementById('inputEventFromTimeHour').selectedIndex;
	var startMin = document.getElementById('inputEventFromTimeMin').selectedIndex;
	var endHour = document.getElementById('inputEventToTimeHour').selectedIndex;
	var endMin = document.getElementById('inputEventToTimeMin').selectedIndex;
	var startTime = (startHour * 100) + startMin;
	var endTime = (endHour * 100) + endMin;
	var freqIndex = document.getElementById('inputEventRepeats').selectedIndex;
	freq = document.getElementById('inputEventRepeats').options[freqIndex].value;
	var interval = document.getElementById('inputEventEvery').value;

	startDate = convertDateToDBFormat(startDate);
	endDate = convertDateToDBFormat(endDate);
	var isNoEventEndDateChecked = document.getElementById('inputNoEventEndDate').checked;
	if(isNoEventEndDateChecked) {
		endDate = "0000-00-00";
	}

	var errMsg = '';
	if(title == '') {
		errMsg = 'Event title can\'t be empty.';
	}
	else if(startDate == '' || startDate == '0000-00-00') {
		errMsg = 'Event start date can\'t be empty.';
	}
	else if(freq != 1 && !isNoEventEndDateChecked && endDate == '') {
		errMsg = 'Event end date can\'t be empty.';
	}

	if(errMsg != '')
	{
		var resultToUI = getAlertDiv(2, errMsg);
		document.getElementById('alertRow').style.display = '';
		document.getElementById('alertDiv').innerHTML = resultToUI;
		return false;
	}

	if(freq == 1) 
	{
		//No Repeat
		interval = 0;
		endDate = startDate;
	}
	else if(freq == 2) 
	{
		//Daily		
	}
	else if(freq == 3) 
	{
		//Weekly		
		for(i=0; i<7; i++) {
			var weekDayID = 'day' + i;
			if(document.getElementById(weekDayID).checked) {
				if(day != '') {
					day += ',';
				}
				day += weekDayArray[i];
			}
		}
		if(day == '') {
			var resultToUI = getAlertDiv(2, 'Please select atleast one day');
			document.getElementById('alertRow').style.display = '';
			document.getElementById('alertDiv').innerHTML = resultToUI;
			return false;			
		}
	}
	else if(freq == 4 || freq == 5) 
	{
		//Monthly
		if(document.getElementById('radOccursOn_1').checked) {
			var monthIndex = document.getElementById('monthDay').selectedIndex;
			var monthDay = document.getElementById('monthDay').options[monthIndex].value;
		} else {
			var weekNoIndex = document.getElementById('weekNumber').selectedIndex;
			var weekNo = document.getElementById('weekNumber').options[weekNoIndex].value;
			var weekDayIndex = document.getElementById('weekDay').selectedIndex;
			var weekDay = document.getElementById('weekDay').options[weekDayIndex].value;
			if(weekNo == 5) {
				day = -1;
			} else {
				day = weekNo;
			}
			day += weekDayArray[weekDay - 1]; // -1 to match the array index.
		}
	}
	if(freq == 5) 
	{
		//Yearly
		var monthIndex = document.getElementById('month').selectedIndex;
		var month = document.getElementById('month').options[monthIndex].value;
	}

	var formPostData = "req=3";
	formPostData += "&title=" + title;
	formPostData += "&desc=" + desc;
	formPostData += "&location=" + loc;
	formPostData += "&organiser=" + organiser;
	formPostData += "&startDate=" + startDate;
	formPostData += "&endDate=" + endDate;
	formPostData += "&startTime=" + startTime;
	formPostData += "&endTime=" + endTime;
	formPostData += "&freq=" + freq;
	formPostData += "&interval=" + interval;
	formPostData += "&day=" + day;
	formPostData += "&monthDay=" + monthDay;
	formPostData += "&month=" + month;

	$.ajax({
		type:'POST',
		url:doEventFile,
		data:formPostData,
		success:addOrUpdateEventsResponse,
		error:HandleAjaxError
	});
}

function addOrUpdateEventsResponse(response)
{
	if(response) {
		var alertType = 1;
		var msgToDisplay = (isUpdate)?'Event has been updated successfully!':'Event has been created successfully';
		if(!isUpdate) {
			getAddOrEditEventForm(0);
		}
	} else {
		var alertType = 2;
		var msgToDisplay = (isUpdate)?'Event failed to update.':'Event failed to create.';		
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function showEventTabs()
{
	document.getElementById('calendar').className = '';
	document.getElementById('addEvent').className = '';
	document.getElementById('listEvents').className = 'active';
	document.getElementById('alertRow').style.display = 'none';

	var formPostData = 'req=4';
	
	$.ajax({
		type:'POST',
		url:doEventFile,
		data:formPostData,
		success:showEventTabsResponse,
		error:HandleAjaxError
	});
}

function showEventTabsResponse(response)
{
	document.getElementById('pageHeader').innerHTML = 'Upcoming Events';
	document.getElementById('pageContent').innerHTML = response;
	//upcoming events
	listAllEvents(1);
}

function listAllEvents(eventStatus)
{
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('hiddenEventTabID').value = eventStatus;
	
	var table = '<table id="eventList" class="table table-condensed"><thead><tr><th>Event Title ID</th><th>Description</th><th>Start Date</th><th>End Date</th><th>Location</th><th>Organiser</th><th>Actions</th></tr></thead></table>';

	if(eventStatus == 1) {
		document.getElementById('pageHeader').innerHTML = 'Upcoming Events';
		document.getElementById('upcomingEventsDiv').innerHTML = table;
	} else if(eventStatus == 2) {
		document.getElementById('pageHeader').innerHTML = 'Past Events';
		document.getElementById('pastEventsDiv').innerHTML = table;
	}

	oTable = $('#eventList').dataTable( {
		"aoColumns": [
			{ "sWidth": "15%" },
			{ "sWidth": "20%"  },
			{ "sWidth": "15%" },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "5%"  }
		],
		"bFilter":false,
        "bProcessing": true,
		"bDestroy": true,
        "sAjaxSource": doEventFile,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": "req=5&eventStatus="+eventStatus,
                "success": fnCallback
            } );
		
        }
	});
}

function deleteEventConfirmation(eventID, eventTitle)
{
	var msgToDisplay = 'Please confirm to delete the event \'' + eventTitle + '\'';
	var actionTakenCallBack = "deleteEventRequest(" + eventID + ")";
	var actionCancelCallBack = "cancelEventDeleteRequest()";
	var resultToUI = getAlertDiv(4, msgToDisplay, 1, "Proceed", "Cancel", actionTakenCallBack, actionCancelCallBack);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	$('html,body').scrollTop(0);
}

function cancelEventDeleteRequest()
{
	document.getElementById('alertDiv').innerHTML = '';
	document.getElementById('alertRow').style.display = 'none';
}

function deleteEventRequest(eventID)
{
	var formPostData = 'req=6&eventID=' + eventID;
	
	$.ajax({
		type:'POST',
		url:doEventFile,
		data:formPostData,
		success:deleteEventResponse,
		error:HandleAjaxError
	});
}

function deleteEventResponse(response)
{
	if(response == 1) {
		var alertType = 1;
		var msgToDisplay = 'Event has been deleted successfully';
		var eventStatus = document.getElementById('hiddenEventTabID').value;
		listAllEvents(eventStatus);
	} else {
		var alertType = 2;
		var msgToDisplay = 'Failed to delete the event';
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}
