<?php
error_reporting(E_ALL);
ini_set("display_errors", "On");

include "Recurr/RecurrenceRule.php";
/* */
include "Recurr/RecurrenceRuleTransformer.php";
include "Recurr/TransformerConfig.php";
include "Recurr/DateUtil.php";
include "Recurr/DateInfo.php";
include "Recurr/DaySet.php";
include "Recurr/Time.php";
include "Recurr/Weekday.php";
/**/
$req = 1;
if($req == 1)
{
	$timezone    = 'America/New_York';
	//date_default_timezone_set($timezone);
	$startDate   = new \DateTime('2014-02-01 20:00:00', new \DateTimeZone($timezone));
	$endDate   = new \DateTime('2015-02-01 20:00:00', new \DateTimeZone($timezone));
	$rule        = new \Recurr\RecurrenceRule('FREQ=MONTHLY;INTERVAL=1;UNTIL=20140331;BYDAY=1SU;WKST=SU;', $startDate, $timezone);
	$transformer = new \Recurr\RecurrenceRuleTransformer($rule);

	$transformerConfig = new \Recurr\TransformerConfig();
	//$transformerConfig->enableLastDayOfMonthFix();
	$transformer->setTransformerConfig($transformerConfig);

	$result = $transformer->getComputedArray();
	print_r($result[1]->format('Y-m-d'));

	echo "<pre>";
	print_r($transformer->getComputedArray());
	echo "</pre>";
}
else
{
	$timezone    = 'America/New_York';
	date_default_timezone_set($timezone);

	$rule = new \Recurr\RecurrenceRule;
	$rule->setFreq('YEARLY');
	$rule->setCount(2);
	$rule->setInterval(2);
	$rule->setBySecond(array(30));
	$rule->setByMinute(array(10));
	$rule->setByHour(array(5, 15));
	$rule->setByDay(array('SU', 'WE'));
	$rule->setByMonthDay(array(16, 22));
	$rule->setByYearDay(array(201, 203));
	$rule->setByWeekNumber(array(29, 32));
	$rule->setByMonth(array(7, 8));
	$rule->setBySetPosition(array(1, 3));
	$rule->setWeekStart('TU');
	echo $rule->getString();
}
?>