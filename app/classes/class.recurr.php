<?php

/*
 * Interface class for Recurr library
 * Created on 23-Feb-2014 00:26 AM
 */

//error_reporting(E_ALL);
//ini_set("display_errors", "On");
//namespace Recurr;

use Recurr\Rule;
//use Recurr\Recurrence;
use Recurr\RecurrenceCollection;

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
		//autoloading the vendor classes
		require $this->APPLICATION_PATH."plugins/recurr/vendor/autoload.php";
		include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Rule.php";		
		
		if($type == 1) {
			$this->rule = new Rule;
		} else if($type == 2) {
//			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Recurrence.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/RecurrenceCollection.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Transformer/ArrayTransformer.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/DateUtil.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/DateInfo.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/DaySet.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Time.php";
			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Weekday.php";

			$this->transformer = new \Recurr\Transformer\ArrayTransformer();			
		}		
	}

	public function setStartDate($start_date)
	{
		$this->start_date = $start_date;
	}

	public function setEndDate($end_date)
	{
		$this->end_date = $end_date;
	}

	public function setTimeZone($timezone)
	{
		$this->timezone = $timezone;
	}

	public function setFreq($freq)
	{
		$this->rule->setFreq($freq);
	}

	public function setCount($count)
	{
		$this->rule->setCount($count);
	}

	public function setInterval($interval)
	{
		$this->rule->setInterval($interval);
	}

	public function setBySecond($second)
	{
		$this->rule->setBySecond($second);
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

	public function setUntil($until)
	{
		$this->rule->setUntil($until);
	}

	public function setVirtualLimit($limit)
	{
		$this->virtualLimit = $limit;
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
		$start_date   = new \DateTime($this->start_date, new \DateTimeZone($this->timezone));
		if($this->end_date != '0000-00-00') {
			$end_date   = new \DateTime($this->end_date, new \DateTimeZone($this->timezone)); //optional
		} else {
			//$end_date = null;
		}
		$rule = new \Recurr\Rule($this->rrule, $start_date, $end_date, $this->timezone);
		if($this->virtualLimit != '' || $this->virtualLimit != null) {
			$this->transformer->setVirtualLimit($this->virtualLimit);
		}
		
		return $this->transformer->transform($rule)->toArray();
	}
}

?>