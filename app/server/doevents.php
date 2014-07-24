<?php
$APPLICATION_PATH = "../";
include $APPLICATION_PATH.'utils/JSON.php';
include_once $APPLICATION_PATH . '/classes/class.events.php';
include_once $APPLICATION_PATH . '/classes/class.profiles.php';
include_once $APPLICATION_PATH . '/classes/class.groups.php';

//process request
$req = $_REQUEST['req'];

if($req == 1)
{
	//get the list of events for the particular period (month)

	include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';
	
	$time_zone = 'Asia/Calcutta';
//	$dt = Carbon::now($time_zone)->startOfMonth();
//	$start_date = $dt->year."-".$dt->month."-".$dt->day;	
//	$dt = Carbon::now($time_zone)->lastOfMonth();
//	$end_date = $dt->year."-".$dt->month."-".$dt->day;

	$start_time_stamp = $_REQUEST['start'];
	$end_time_stamp = $_REQUEST['end'];
	$start_date = date("Y-m-d", $start_time_stamp);
	$end_date = date("Y-m-d", $end_time_stamp);
//	echo $start_date;
//	$start_date = "2014-03-05";
//	$end_date = "2014-03-25";

	$events_obj = new Events($APPLICATION_PATH);
	$event_occurrences = $events_obj->getEventOccurrences($start_date, $end_date, $time_zone);
	//print_r($event_occurrences);
	
	$json = new Services_JSON();
	$encode_obj = $json->encode($event_occurrences);
	unset($json);

	echo $encode_obj;
}
else if($req == 2)
{
	$date_list = array('1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th', '15th', '16th', '17th', '18th', '19th', '20th', '21th', '22nd', '23rd', '24th', '25th', '26th', '27th', '28th', '29th', '30th', '31th');
	$day_list = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
	$week_list = array('First', 'Second', 'Third', 'Fourth', 'Last');
	$month_list = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$event_repeat_list = array('No', 'Daily', 'Weekly', 'Monthly', 'Yearly');

	$is_update = trim($_POST['isEdit']);
	if($is_update) {
		$event_id = trim($_POST['eventID']);
		$events_obj = new Events($APPLICATION_PATH);
		$event_details = $events_obj->getEventInformation($event_id);
		if(is_array($event_details) && COUNT($event_details) > 0)
		{
			$event_title = $event_details[1];
			$event_desc = $event_details[2];
			$event_location = $event_details[3];
			$start_date = $event_details[4];
			$end_date = $event_details[5];

			if($start_date != '') {
				$start_date = explode("-", $start_date);
				$start_date = $start_date[2].'/'.$start_date[1].'/'.$start_date[0];
			}
			if($end_date != '') {
				$end_date = explode("-", $end_date);
				$end_date = $end_date[2].'/'.$end_date[1].'/'.$end_date[0];
			}

			$start_time = $event_details[6];
			$end_time = $event_details[7];

			if(strlen($start_time) > 3) {
				$start_hour = substr($start_time, 0, 2);
				$start_min = substr($start_time, 2, 2);
			} else {
				$start_hour = "0".substr($start_time, 0, 1);
				$start_min = substr($start_time, 1, 2);
			}
			if(strlen($end_time) > 3) {
				$end_hour = substr($end_time, 0, 2);
				$end_min = substr($end_time, 2, 2);
			} else {
				$end_hour = "0".substr($end_time, 0, 1);
				$end_min = substr($end_time, 1, 2);
			}

			$rrule = $event_details[8];
			if($rrule == '') {
				$rrule_params = explode(";", $rrule);
				foreach($rrule_params as $key => $value)
				{
//					$rrule_keyexplode("=", $rrule_params);
				}
			}
			$priority = $event_details[9];
			$organiser = $event_details[10];
			$access_level = $event_details[11];
		}
	}
	
	$to_return = '';

	$to_return .= '<ul class="breadcrumb">';
	  $to_return .= '<li id="eventStepLink-1" class="active">STEP 1: Event Information <span class="divider">/</span></li>';
	  $to_return .= '<li id="eventStepLink-2"><a href="#">STEP 2: Participants/Notifications</a></li>';
	  //$to_return .= '<li id="eventStepLink-3"><a href="#">STEP 3: Notification</a></li>';
	$to_return .= '</ul>';
	$to_return .= '<div class="row-fluid" id="divEventStep-1">';
		$to_return .= '<div class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';
				$to_return .= '<p class="text-left muted">Event Information</p>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventTitle">Title</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span10" id="inputEventTitle" placeholder="Event Title" value="'.$event_title.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventDesc">Description</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<textarea class="span10" id="inputEventDesc" placeholder="Event Description">'.$event_desc.'</textarea>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventLocation">Location</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span10" id="inputEventLocation" placeholder="Event Location" value="'.$event_location.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventOrangiser">Orangiser</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span10" id="inputEventOrangiser" placeholder="Event Orangiser" value="'.$event_organiser.'">';
						$to_return .= '</div>';
				$to_return .= '</div>';			
			$to_return .= '</form>';
		$to_return .= '</div>';

		$to_return .= '<div class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';
				$to_return .= '<p class="text-left muted">Event Occurrence</p>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventStartDate">Start Date</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="text" class="span8" id="inputEventStartDate" placeholder="Start Date" value="'.$start_date.'" data-date-format="dd/mm/yyyy" >';
						$to_return .= '</div>';
				$to_return .= '</div>';				
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventFromTimeHour">From Time</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<select class="span4" id="inputEventFromTimeHour">';
								for($i=0; $i<=23; $i++) 
								{
									$text_to_show = ((strlen($i) == 1)?"0".$i:$i);
									$is_selected = '';
									if($is_update && $start_hour == $i) {
										$is_selected = 'selected';
									} else if($i == 10) {
										$is_selected = 'selected';										
									}
									$to_return .= '<option value="'.$i.'" '.$is_selected.'>'.$text_to_show.'</option>';
								}
							$to_return .= '</select>';
							$to_return .= '&nbsp;&nbsp;&nbsp;';
							$to_return .= '<select class="span4" id="inputEventFromTimeMin">';
								for($i=0; $i<=59; $i++) 
								{
									$text_to_show = ((strlen($i) == 1)?"0".$i:$i);
									$is_selected = '';
									if($is_update && $start_min == $i) {
										$is_selected = 'selected';
									} else if($i == 0) {
										$is_selected = 'selected';										
									}
									$to_return .= '<option value="'.$i.'" '.$is_selected.'>'.$text_to_show.'</option>';
								}
							$to_return .= '</select>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventToTimeHour">To Time</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<select class="span4" id="inputEventToTimeHour">';
								for($i=0; $i<=23; $i++) 
								{
									$text_to_show = ((strlen($i) == 1)?"0".$i:$i);
									$is_selected = '';
									if($is_update && $end_hour == $i) {
										$is_selected = 'selected';
									} else if($i == 11) {
										$is_selected = 'selected';										
									}
									$to_return .= '<option value="'.$i.'" '.$is_selected.'>'.$text_to_show.'</option>';
								}
							$to_return .= '</select>';
							$to_return .= '&nbsp;&nbsp;&nbsp;';
							$to_return .= '<select class="span4" id="inputEventToTimeMin">';
								for($i=0; $i<=59; $i++) 
								{
									$is_selected = '';
									if($is_update && $end_min == $i) {
										$is_selected = 'selected';										
									} else if($i == 0) {
										$is_selected = 'selected';
									}
									$text_to_show = ((strlen($i) == 1)?"0".$i:$i);
									$to_return .= '<option value="'.$i.'" '.$is_selected.'>'.$text_to_show.'</option>';
								}
							$to_return .= '</select>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputEventRepeats">Repeats</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<select class="span8" id="inputEventRepeats" onchange="onchangeEventRepeats(this);">';
							for($i=0; $i<COUNT($event_repeat_list); $i++) {
								$to_return .= '<option value="'.($i+1).'"'. (($is_upadte)?'':'').'>'.$event_repeat_list[$i].'</option>';
							}
							$to_return .= '</select>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group" id="divOuterEventEvery" style="display:none">';
					$to_return .= '<label class="control-label" for="inputEventEvery">Every</label>';
						$to_return .= '<div class="controls" id="divInnerEventEvery">';
							$to_return .= '<input type="text" class="span4" placeholder="" id="inputEventEvery" value="1"/>&nbsp;<span id="spanEventEveryText"></span>';
						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group" id="divOuterEventOccursOn" style="display:none">';
					$to_return .= '<label class="control-label" for="inputEventOccursOn">Occurs On</label>';
						$to_return .= '<div class="controls">';
							
							$to_return .= '<div class="row-fluid" id="divInnerEventOccursOn_1" style="display:none">';
								$to_return .= '<div class="span12">';
									for($i=0; $i<COUNT($day_list); $i++)
									{
										$to_return .= '<input type="checkbox" id="day'.$i.'">&nbsp;'.$day_list[$i].'<BR>';
									}									
								$to_return .= '</div>';
							$to_return .= '</div>';

							$to_return .= '<div class="row-fluid" id="divInnerEventOccursOn_2" style="display:none">';
								$to_return .= '<div class="span12">';
									$to_return .= '<table>';
										$to_return .= '<tr id="rowDateList">';
											$to_return .= '<td><input type="radio" name="radOccursOn" id="radOccursOn_1" checked onclick="OnChangeOccursOn(1);"></td>';
											$to_return .= '<td><select class="span4" id="monthDay">';
												for($i=0; $i<COUNT($date_list); $i++)
												{
													$to_return .= '<option value="'.($i+1).'">'.$date_list[$i]	.'</option>';
												}
											$to_return .= '</select></td>';
										$to_return .= '</tr>';
										$to_return .= '<tr id="rowWeekList">';
											$to_return .= '<td><input type="radio" name="radOccursOn" id="radOccursOn_2" onclick="OnChangeOccursOn(2);"></td>';
											$to_return .= '<td><select id="weekNumber" class="span6" disabled>';
												for($i=0; $i<COUNT($week_list); $i++)
												{
													$to_return .= '<option value="'.($i+1).'">'.$week_list[$i]	.'</option>';
												}
											$to_return .= '</select>';
											$to_return .= '&nbsp;<select id="weekDay" class="span6" disabled>';
												for($i=0; $i<COUNT($day_list); $i++)
												{
													$to_return .= '<option value="'.($i+1).'">'.$day_list[$i]	.'</option>';
												}
											$to_return .= '</select>';
											$to_return .= '</td>';
										$to_return .= '</tr>';
										$to_return .= '<tr id="rowMonthList">';
											$to_return .= '<td></td>';
											$to_return .= '<td colspan="2"><select id="month">';
												for($i=0; $i<COUNT($month_list); $i++)
												{
													$to_return .= '<option value="'.($i+1).'">'.$month_list[$i]	.'</option>';
												}
											$to_return .= '</select></td>';
										$to_return .= '</tr>';
									$to_return .= '</table>';
								$to_return .= '</div>';
							$to_return .= '</div>';

						$to_return .= '</div>';
				$to_return .= '</div>';
				$to_return .= '<div class="control-group" id="divOuterEventEndDate" style="display:none">';
					$to_return .= '<label class="control-label" for="inputEventEndDate">End Date</label>';
						$to_return .= '<div class="controls" id="divInnerEventEndDate">';
							$to_return .= '<input type="text" class="span8" id="inputEventEndDate" placeholder="End Date" value="'.$end_date.'" data-date-format="dd/mm/yyyy" /><BR>';
							$to_return .= '<input type="checkbox" id="inputNoEventEndDate" onclick="((this.checked == true)?(document.getElementById(\'inputEventEndDate\').disabled = true):(document.getElementById(\'inputEventEndDate\').disabled = false))"/>&nbsp;No End Date';
						$to_return .= '</div>';
				$to_return .= '</div>';
				/*
				$to_return .= '<div class="control-group">';
					//$to_return .= '<label class="control-label" for="inputEventEndDate">End Date</label>';
						$to_return .= '<div class="controls">';
							$to_return .= '<input type="checkbox" id="" />&nbsp;No End Date';
						$to_return .= '</div>';
				$to_return .= '</div>';
				*/
				$to_return .= '</form>';
			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>';
		
	$to_return .= '<div class="row-fluid" id="divEventStep-2" style="display:none;">';
		$to_return .= '<div class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';				
				$to_return .= '<p class="text-left muted">Event Participants</p>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputAddEventParticipant">Participants</label>';
					$to_return .= '<div class="controls">';
						$to_return .= '<input type="text" id="inputAddEventParticipant" data-provide="typeahead" autocomplete="off" value="" placeholder="Start type profile/group name" />';
						//$to_return .= '<HR>';
						$to_return .= '<input type="hidden" id="maxParticipantRowID" value="0" />';
						$to_return .= '<input type="hidden" id="participantRowIDList" value="" />';
						$to_return .= '<input type="hidden" id="participantList" value="" />';
					$to_return .= '</div>';	
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<div class="controls" id="participantsDiv">';
						$to_return .= '<span class="muted" id="spanNoParticipants">No participants added yet.</span>';
					$to_return .= '</div>';	
				$to_return .= '</div>';
			$to_return .= '</form>';
		$to_return .= '</div>';
		$to_return .= '<div class="span6">';
			$to_return .= '<form class="form-horizontal" onsubmit="return false;">';				
				$to_return .= '<p class="text-left muted">Event Notification</p>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<label class="control-label" for="inputRemainderPeriod">Reminder</label>';
					$to_return .= '<div class="controls">';
						$to_return .= '<input type="text" id="inputRemainderPeriod" class="input-mini" onblur="validateEventRemainder();" />&nbsp;';
						$to_return .= '<select id="inputRemainderType" class="input-small">';
							$to_return .= '<option value="1">Hours</option>';
							$to_return .= '<option value="2">Days</option>';
						$to_return .= '</select>';
					$to_return .= '</div>';	
				$to_return .= '</div>';
				$to_return .= '<div class="control-group">';
					$to_return .= '<div class="controls" id="participantsDiv">';					
					$to_return .= '</div>';	
				$to_return .= '</div>';
			$to_return .= '</form>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	$to_return .= '<div class="row-fluid">';
		$to_return .= '<div class="span12">';
			$to_return .= '<div class="form-actions" align="center">';
				$to_return .= '<button id="btnPreviousStep" class="btn btn-primary" onclick="showpreviousEventStep();" style="display:none;">Previous</button>&nbsp;';
				$to_return .= '<button id="btnNextStep" class="btn btn-primary" onclick="showNextEventStep();">Next</button>&nbsp;';
				$to_return .= '<button id="btnSaveEvent" class="btn btn-primary" onclick="addOrUpdateEvents(0);" style="display:none;">Save</button>&nbsp;';
				$to_return .= '<input type="hidden" id="currentEventStep" value="1" />';
			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>';
	
	echo $to_return;
	exit;
}
else if($req == 3)
{
	$title		= trim($_POST['title']);
	$description= trim($_POST['desc']);
	$location	= trim($_POST['location']);
	$start_date	= trim($_POST['startDate']);
	$end_date	= trim($_POST['endDate']);
	$start_time	= trim($_POST['startTime']);
	$end_time	= trim($_POST['endTime']);
	$organiser	= trim($_POST['organiser']);
	$priority	= -1;
	$access_level= -1;

	$freq		= trim($_POST['freq']);
	$interval	= trim($_POST['interval']);
	$day		= trim($_POST['day']);
	$month_day	= trim($_POST['monthDay']);
	$month		= trim($_POST['month']);

	if($freq == 1)
	{
		//no repeat
		$rrule = '';
	}
	else if($freq == 2)
	{
		//daily
		$rrule = 'FREQ=DAILY;';
	}
	else if($freq == 3)
	{
		//monthly
		$rrule = 'FREQ=WEEKLY;';
	}
	else if($freq == 4)
	{
		//yearly
		$rrule = 'FREQ=MONTHLY;';
	}
	else if($freq == 5)
	{
		//yearly
		$rrule = 'FREQ=YEARLY;';
	}

	if($freq != 1) {
		$until = explode("-", $end_date);
		//print_r($end_date);
		$until = $until[0].$until[1].$until[2];
		$rrule .= 'INTERVAL='.$interval.';UNTIL='.$until.';';
		//$rrule .= 'INTERVAL='.$interval.';COUNT=10;';
	}
	
	if($freq == 3 || $freq == 4 || $freq == 5) {
		if($freq == 3) {
			$rrule .= 'BYDAY='.$day.';';
		} else {
			if($month_day == "") {
				$rrule .= 'BYDAY='.$day.';';
				if($freq == 5) {
					$rrule .= 'BYMONTH='.$month.';';
				}
			} else {
				$rrule .= 'BYMONTHDAY='.$month_day.';';
			}
		}
	}

	echo $rrule;

	$events_obj = new Events($APPLICATION_PATH);
	$status = $events_obj->addEventDetails($title, $description, $location, $start_date, $end_date, $start_time, $end_time, $rrule, $priority, $organiser, $access_level);

	echo $status;
	exit;
}
else if($req == 4)
{
	//show events tab

	$to_return = '';
	$to_return .= '<div class="tabbable">';
		$to_return .= '<ul class="nav nav-tabs">';
			$to_return .= '<li id="upcomingEventsTab" class="active" onclick="listAllEvents(1);"><a href="#upcomingEventsTab" data-toggle="tab">Upcoming Events</a></li>';
			$to_return .= '<li id="pastEventsTab" onclick="listAllEvents(2);"><a href="#pastEventsTab" data-toggle="tab">Past Events</a></li>';
		$to_return .= '</ul>';
		$to_return .= '<div class="tab-content">';
			$to_return .= '<div class="tab-pane active" id="upcomingEventsDiv">';
			$to_return .= '</div>';
			$to_return .= '<div class="tab-pane" id="pastEventsDiv">';
			$to_return .= '</div>';
		$to_return .= '</div>';
	$to_return .= '</div>';

	echo $to_return;
	exit;
}
else if($req == 5)
{
	//list all events

	$listAllEvents = true;
	$start_date = "";
	//$end_date = "";

	//upcoming events - 1;
	//past events - 2;
	$event_status = trim($_POST['eventStatus']);

	include_once $APPLICATION_PATH . '/plugins/carbon/src/Carbon/Carbon.php';

	$time_zone = 'Asia/Calcutta';
	$dt = Carbon::now($time_zone);
	$today = $dt->year."-".$dt->month."-".$dt->day;
	//echo $today;

	$events_obj = new Events($APPLICATION_PATH);
	$event_details = $events_obj->getAllEvents($listAllEvents, $start_date, $today, $start_time, $end_time, $event_status);
	//print_r($event_details);

	$is_results_available = false;
	if(is_array($event_details))
	{
		$total_events = COUNT($event_details);
		if($total_events > 0)
		{
			$is_results_available = true;
			for($i=0; $i<$total_events; $i++) {
				
				$actions = '<div class="dropdown">';
					$actions .= '<i class="curHand icon-pencil" onclick="getAddOrEditEventForm(1, '.$event_details[$i][0].')"></i>&nbsp;';
					$actions .= '<i class="curHand icon-trash" onclick="deleteEventConfirmation('.$event_details[$i][0].', \''.$event_details[$i][1].'\')"></i>&nbsp;&nbsp;';
				$actions .= '</div>';

				$to_return['aaData'][] = array($event_details[$i][1], $event_details[$i][2], $event_details[$i][4], $event_details[$i][5], $event_details[$i][3], $event_details[$i][10], $actions);
			}
		}
	}

	if( !$is_results_available )
	{
		$to_return['aaData'] = array();
	}

	$json = new Services_JSON();
	$encode_obj = $json->encode($to_return);
	unset($json);

	echo $encode_obj;
	exit;	
}
else if($req == 6)
{
	$event_id = trim($_POST['eventID']);

	$events_obj = new Events($APPLICATION_PATH);
	$status = $events_obj->deleteEventDetails($event_id);
	echo $status;
	exit;
}
else if($req == 7)
{
	//load individual participants and groups

	$participants_list = array();
	$profiles_obj = new Profiles($APPLICATION_PATH);
	$email_list = $profiles_obj->getAllProfileNameAndEmailIDs();

	$groups_obj = new Groups($APPLICATION_PATH);
	$groups = $groups_obj->getAllGroups();
	
	$participants_list = array($email_list, $groups);
	$json = new Services_JSON();
	$encode_obj = $json->encode($participants_list);
	unset($json);

	echo $encode_obj;
	exit;	
}
?>