//global variables
doEventFile = 'server/doevents';

function showMonthlyCalendar(reqFrom)
{
	if(reqFrom == 1) {
		document.getElementById('calendar').className = 'active';
		document.getElementById('addEvent').className = '';
		document.getElementById('listEvents').className = '';
		document.getElementById('alertRow').style.display = 'none';
		document.getElementById('pageContent').innerHTML = '<div id="calendarView" style="height:333px"></div>';
		document.getElementById('pageContent').className = "span10";
	} else {
		document.getElementById('eventContent').innerHTML = '<div id="calendarView"></div>';		
	}
	
	$('#calendarView').fullCalendar({
		// put your options and callbacks here
		//events: eval("(" + response + ")" )
		//events: 'server/doevents?req=1'
		header: {
		  left: 'prevYear,nextYear, today, prev,next',
		  center: 'title',
		  right: 'basicDay,basicWeek,month, agendaDay'
		},
		buttonText: {
			today: 'Today',
			basicDay: 'Day',
			basicWeek: 'Week',
			month: 'Month',
			agendaDay: 'Today Agenda',
			agendaWeek: 'Weekly Agenda',
		},
		displayEventEnd: {
			month: false,
			basicDay: true,
			basicWeek: false,
			agendaDay: true,
			agendaWeek: true,
			'default': false
		},
		eventMouseover: function( event, jsEvent, view ) {
			var content = '';
			content += '<b>When: </b>' + event.start._i;
			content += '<BR><b>Where: </b>' + event.location;
			content += '<BR><b>Organiser: </b>' + event.organiser;
			if(event.info != '') {
				content += '<BR><BR><span class="muted">This event occurs ' + event.info + '</span>';
			}
			
			$(this).attr('data-toggle', 'popover');
			$(this).attr('data-placement', 'top');
			$(this).attr('data-content', content);
			$(this).attr('data-title', event.title);
			$(this).popover({html : true });
			$(this).popover('show');
		},
		eventMouseout: function( event, jsEvent, view ) {
			$(this).popover('hide');
		},
		events: {
			url: 'server/doevents',
			type: 'POST',
			data: {
				req: 1
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

	//load individual and groups
	var reqFrom = 'event';
	getParticipantsList(reqFrom);
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

function addOrUpdateEvents(val, doValidation)
{
	document.getElementById('pageContent').className = "span10";

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

	//if validation is the only purpose here 
	//then do all the validations and return the status.
	if(doValidation) {
		return true;
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

	var emailRemainder = 0;
	var smsRemainder = 0;
	if(document.getElementById('checkEmailReminder').checked)
	{
		emailRemainder = trim(document.getElementById('inputRemainderPeriod1').value);

		if(emailRemainder != '') {
			if(isNaN(emailRemainder)) {
				errMsg = 'Please enter a valid reminder value for email notification';
				var resultToUI = getAlertDiv(2, errMsg);
				document.getElementById('alertRow').style.display = '';
				document.getElementById('alertDiv').innerHTML = resultToUI;
				document.getElementById('inputRemainderPeriod1').select();
				return false;
			} else {
				//isAtleastOneSelected = true;
				emailRemainder = emailRemainder * 60 * 60;
				var index = document.getElementById('inputRemainderType1').selectedIndex;
				if(index == 1) {
					emailRemainder = emailRemainder * 60;
				}
			}
		} else {
				errMsg = 'Please enter a valid reminder value for email notification';
				var resultToUI = getAlertDiv(2, errMsg);
				document.getElementById('alertRow').style.display = '';
				document.getElementById('alertDiv').innerHTML = resultToUI;
				document.getElementById('inputRemainderPeriod1').select();
				return false;
		}
	}
	
	if(document.getElementById('checkSMSReminder').checked)
	{
		smsRemainder = trim(document.getElementById('inputRemainderPeriod2').value);

		if(smsRemainder != '') {
			if(isNaN(smsRemainder)) {
				errMsg = 'Please enter a valid reminder value for sms notification';
				var resultToUI = getAlertDiv(2, errMsg);
				document.getElementById('alertRow').style.display = '';
				document.getElementById('alertDiv').innerHTML = resultToUI;
				return false;
			} else {
				isAtleastOneSelected = true;
				smsRemainder = smsRemainder * 60 * 60;
				var index = document.getElementById('inputRemainderType2').selectedIndex;
				if(index == 1) {
					smsRemainder = smsRemainder * 60;
				}
			}
		} else {
				errMsg = 'Please enter a valid reminder value for sms notification';
				var resultToUI = getAlertDiv(2, errMsg);
				document.getElementById('alertRow').style.display = '';
				document.getElementById('alertDiv').innerHTML = resultToUI;
				document.getElementById('inputRemainderPeriod2').select();
				return false;
		}
	}

	var formPostData = "req=3";
	formPostData += "&title=" + escString(title);
	formPostData += "&desc=" + escString(desc);
	formPostData += "&location=" + escString(loc);
	formPostData += "&organiser=" + escString(organiser);
	formPostData += "&startDate=" + startDate;
	formPostData += "&endDate=" + endDate;
	formPostData += "&startTime=" + startTime;
	formPostData += "&endTime=" + endTime;
	formPostData += "&freq=" + freq;
	formPostData += "&interval=" + interval;
	formPostData += "&day=" + day;
	formPostData += "&monthDay=" + monthDay;
	formPostData += "&month=" + month;
	formPostData += "&participantList=" + document.getElementById('participantList').value;
//	formPostData += "&notifications=" + emailRemainder + ',' + smsRemainder;
	formPostData += "&emailnotificationperiod=" + emailRemainder;
	formPostData += "&smsnotificationperiod=" + smsRemainder;
	

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
	var dataObj = eval("(" + response + ")" );
	var result = dataObj[0];
	var msgToDisplay = dataObj[1];

	if(result) {
		var alertType = 1;
		if(!isUpdate) {
			//getAddOrEditEventForm(0);
			//getAddOrEditEventParticipantForm();
		}
	} else {
		var alertType = 2;
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
	if(result) {
		showEventTabs(1);
	}
}

function showEventTabs(doNotHideAlertDiv)
{
	document.getElementById('calendar').className = '';
	document.getElementById('addEvent').className = '';
	document.getElementById('listEvents').className = 'active';
	dontHideMsgDiv = doNotHideAlertDiv;
	if(doNotHideAlertDiv != 1) {
		document.getElementById('alertRow').style.display = 'none';
	}

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
	listAllEvents(1, dontHideMsgDiv);
}

function listAllEvents(eventStatus, doNotHideAlertDiv)
{
	if(doNotHideAlertDiv != 1) {
		document.getElementById('alertRow').style.display = 'none';
	}
	document.getElementById('hiddenEventTabID').value = eventStatus;
	
	var tableID = 'eventList-' + eventStatus;
	var table = '<table id="'+ tableID +'" class="table table-condensed"><thead><tr><th>Event</th><th>Description</th><th>Start Date</th><th>End Date</th>';
	if(eventStatus == 1) {
		table += '<th>Next Event Date</th>';
	}	
	table += '<th>Location</th><th>Organiser</th><th>Actions</th></tr></thead></table>';

	if(eventStatus == 1) {
		document.getElementById('pageHeader').innerHTML = 'Upcoming Events';
		document.getElementById('upcomingEventsDiv').innerHTML = table;
		document.getElementById('upcomingEventsDiv').className = 'tab-pane active';
		document.getElementById('pastEventsDiv').className = 'tab-pane';
	} else if(eventStatus == 2) {
		document.getElementById('pageHeader').innerHTML = 'Past Events';
		document.getElementById('pastEventsDiv').innerHTML = table;
		document.getElementById('upcomingEventsDiv').className = 'tab-pane';
		document.getElementById('pastEventsDiv').className = 'tab-pane active';
	}
	document.getElementById('pageContent').className = "span12";

	oTable = $('#'+tableID).dataTable( {
/*		"aoColumns": [
			{ "sWidth": "15%" },
			{ "sWidth": "20%"  },
			{ "sWidth": "10%" },
			{ "sWidth": "10%"  },
			{ "sWidth": "10%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "15%"  },
			{ "sWidth": "5%"  }
		],*/
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
		listAllEvents(eventStatus, 0);
	} else {
		var alertType = 2;
		var msgToDisplay = 'Failed to delete the event';
	}
	var resultToUI = getAlertDiv(alertType, msgToDisplay);
	document.getElementById('alertRow').style.display = '';
	document.getElementById('alertDiv').innerHTML = resultToUI;
}

function getParticipantsList(reqFrom, isReqForSMS)
{
	tempReqFrom = reqFrom;
	tempIsReqForSMS = isReqForSMS;
	var formPostData = "req=7";

	$.ajax({
		type:'POST',
		url:doEventFile,
		data:formPostData,
		success:getParticipantsListResponse,
		error:HandleAjaxError
	});
}

function getParticipantsListResponse(response)
{
	var dataObj = eval("(" + response + ")" );
	var individualParticipants = dataObj[0];
	var groupParticipants = dataObj[1];

	var totalIndividualParticipants = individualParticipants.length;
	var totalGroupParticipants = groupParticipants.length;
	var totalParticipant = totalIndividualParticipants + totalGroupParticipants;
	if(totalParticipant > 0)
	{
		var sourceList = new Array();
		var participantType = 1;
		for(i=0; i<totalIndividualParticipants; i++)
		{
			var id = participantType + "<:|:>" + individualParticipants[i][0] + "<:|:>" + individualParticipants[i][1];
			var name = "";

			if(tempIsReqForSMS) {
				if(trim(individualParticipants[i][3]) != "") {
					name = individualParticipants[i][2]+" ("+individualParticipants[i][3]+")";
				} else {
					name = individualParticipants[i][2];
				}
			} else {
				if(trim(individualParticipants[i][1]) != "") {
					name = individualParticipants[i][2]+" ("+individualParticipants[i][1]+")";
				} else {
					name = individualParticipants[i][2];
				}
			}
			
			sourceList.push({"id":id, "name":name});
		}

		var participantType = 2;
		for(i=0; i<totalGroupParticipants; i++)
		{
			var id = participantType + "<:|:>" + groupParticipants[i][0] + "<:|:>" + groupParticipants[i][1];
			var name = groupParticipants[i][1];
			sourceList.push({"id":id, "name":name});
		}

		if(tempReqFrom == 'event') {
			$('#inputAddEventParticipant').typeahead({
				source: sourceList,
				display: 'name',
				val: 'id',
				itemSelected: addNewParticipant
			});
		} else if(tempReqFrom == 'notifications') {
			$('#inputTo').typeahead({
				source: sourceList,
				display: 'name',
				val: 'id',
				itemSelected: onSelectingParticipants
			});

			$("#inputTo").data('typeahead').source = sourceList;
		}
	}	
}

function addNewParticipant(item, val, text)
{
	document.getElementById('spanNoParticipants').style.display = 'none';

	var participantArr = val.split("<:|:>");
	var participantID = participantArr[0];
	var participantType = participantArr[1];
	var participantList = participantID + ":" + participantType;

	document.getElementById('inputAddEventParticipant').value = '';
	var maxRow = document.getElementById('maxParticipantRowID').value;
    var newRowID = parseInt(maxRow) + 1;

	var rowDiv = document.createElement('p');
    var rowDivID = 'divParticipantRow-'+newRowID;
    rowDiv.setAttribute('id', rowDivID);
    document.getElementById('participantsDiv').appendChild(rowDiv);
	document.getElementById(rowDivID).innerHTML = '<i class="icon-user"></i>&nbsp;'+ text + '&nbsp;<i class="icon-remove-sign curHand" onclick="removeParticipant('+ newRowID +');"></i>';
	
	document.getElementById('maxParticipantRowID').value = newRowID;
	if(document.getElementById('participantList').value != '') {
		document.getElementById('participantList').value += ",";
		document.getElementById('participantRowIDList').value += ",";
	}
	document.getElementById('participantList').value += participantList;
	document.getElementById('participantRowIDList').value += newRowID;
}

function removeParticipant(rowID)
{
	var div = document.getElementById('divParticipantRow-'+rowID);
    if (div) {
        div.parentNode.removeChild(div);
    }

	var participantRowIDArr = document.getElementById('participantRowIDList').value.split(',');
	var index = participantRowIDArr.indexOf(rowID.toString());
    participantRowIDArr.splice(index, 1);
	console.log(participantRowIDArr);
	document.getElementById('participantRowIDList').value = participantRowIDArr.join();

	var participantArr = document.getElementById('participantList').value.split(',');
    participantArr.splice(index, 1);
    document.getElementById('participantList').value = participantArr.join();	
}

function showpreviousEventStep()
{
	document.getElementById('btnPreviousStep').style.display = 'none';
	document.getElementById('btnNextStep').style.display = '';
	document.getElementById('btnSaveEvent').style.display = 'none';
	document.getElementById('divEventStep-1').style.display = '';
	document.getElementById('divEventStep-2').style.display = 'none';	
}

function showNextEventStep(isUpdate)
{
	//do validation before move to next step
	var doValidation = 1;
	if(!addOrUpdateEvents(isUpdate, doValidation)) {
		return false;
	}
	document.getElementById('alertRow').style.display = 'none';
	document.getElementById('inputAddEventParticipant').focus();
	document.getElementById('btnPreviousStep').style.display = '';
	document.getElementById('btnNextStep').style.display = 'none';
	document.getElementById('btnSaveEvent').style.display = '';
	document.getElementById('divEventStep-1').style.display = 'none';
	document.getElementById('divEventStep-2').style.display = '';
}

function validateEventRemainder(option)
{
	var remainderPeriod = document.getElementById('inputRemainderPeriod' + option).value;
	var remainderTypeIndex = document.getElementById('inputRemainderType' + option).selectedIndex;
	var remainderType = document.getElementById('inputRemainderType' + option).options[remainderTypeIndex].value;

	if(!isNaN(remainderPeriod))
	{
		remainderPeriod = Math.round(remainderPeriod);
	}
	document.getElementById('inputRemainderPeriod' + option).value = remainderPeriod;

	if(remainderType == 1)
	{
		if(remainderPeriod < 1 || isNaN(remainderPeriod)) {
			document.getElementById('inputRemainderPeriod' + option).value = 1;
		} else if(remainderPeriod > 23) {
			var days = parseInt(remainderPeriod / 24);
			if(days > 7) {
				days = 7;
			}
			document.getElementById('inputRemainderPeriod' + option).value = days;
			document.getElementById('inputRemainderType' + option).selectedIndex = 1;
		}
	}
	else
	{
		if(remainderPeriod < 1 || isNaN(remainderPeriod)) {
			document.getElementById('inputRemainderPeriod' + option).value = 1;
		} else if(remainderPeriod > 7) {
			document.getElementById('inputRemainderPeriod' + option).value = 7;
		}
	}
}