<?php

/*
 * Interface class for Recurr library
 * Created on 23-Feb-2014 00:26 AM
 */

//error_reporting(E_ALL);
//ini_set("display_errors", "On");

use Recurr\RecurrenceRule;
use Recurr\RecurrenceRuleTransformer;
use Recurr\TransformerConfig;

class RecurrInterface
{
	private $APPLICATION_PATH;
	protected $rule;
	protected $transformer;

	public function __construct($APPLICATION_PATH)
	{
		$this->APPLICATION_PATH = $APPLICATION_PATH; 
	}

	public function setUp($type)
	{
		include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/RecurrenceRule.php";		
		
		if($type == 1) {
			$this->rule = new RecurrenceRule;
		} else if($type == 2) {
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/RecurrenceRuleTransformer.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/TransformerConfig.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/DateUtil.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/DateInfo.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/DaySet.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Time.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Weekday.php";

			$this->transformer = new RecurrenceRuleTransformer;
		}
		
	}

	public function setStartDate($start_date)
	{
		$this->start_date = $start_date;
	}

	public function setTimeZone($timezone)
	{
		$this->timezone = $timezone;
	}

	public function setFreq($freq)
	{
		$this->rule->setFreq($freq);
	}

	public function setInterval($interval)
	{
		$this->rule->setInterval($interval);
	}

	public function setByMinute($minute)
	{
		$this->rule->setByMinute($minute);
	}

	public function setByHour($hour)
	{
		$this->rule->setByHour($hour);
	}

	public function setByDay($day)
	{
		$this->rule->setByDay($day);
	}

	public function setByMonthDay($month_day)
	{
		$this->rule->setByMonthDay($month_day);
	}

	public function setByYearDay($year_day)
	{
		$this->rule->setByYearDay($year_day);
	}

	public function setByWeekNumber($week_number)
	{
		$this->rule->setByWeekNumber($week_number);
	}

	public function setByMonth($month)
	{
		$this->rule->setByMonth($month);
	}

	public function setBySetPosition($position)
	{
		$this->rule->setBySetPosition($position);
	}

	public function setWeekStart($week_start)
	{
		$this->rule->setWeekStart($week_start);
	}
	
	public function setRRule($rrule)
	{
		$this->rrule = $rrule;
	}

	public function getRRule()
	{
		return $this->rule->getString();
	}

	public function getOccurrences()
	{
		//$timezone    = 'America/New_York';
		//date_default_timezone_set($timezone);
		$start_date   = new \DateTime($this->start_date, new \DateTimeZone($this->timezone));
		$rule        = new \Recurr\RecurrenceRule($this->rrule, $start_date, $this->timezone);
		//$transformer = new \Recurr\RecurrenceRuleTransformer($rule);

/*
		$transformerConfig = new \Recurr\TransformerConfig();
		//$transformerConfig->enableLastDayOfMonthFix();
		$this->transformer->setTransformerConfig($transformerConfig);
*/
		$this->transformer->setRule($rule);
		return $this->transformer->getComputedArray();
	}
}

?>