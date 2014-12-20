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
//			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Transformer/Constraint.php";
//			include $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Transformer/ConstraintInterface.php";
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

	public function setStartTime($start_time)
	{
		$this->start_time = $start_time;
	}

	public function setEndTime($end_time)
	{
		$this->end_time = $end_time;
	}

	public function setAfterConstraintDate($after_date, $inc)
	{
		//$inc defines what happens if $after or $before are themselves recurrences. 
		//If $inc = true, they will be included in the collection
		$after_date   = new \DateTime($after_date, new \DateTimeZone($this->timezone));
		$this->afterConstraint = new \Recurr\Transformer\Constraint\AfterConstraint($after_date, $inc);
	}

	public function setBeforeConstraintDate($before_date, $inc)
	{
		$before_date   = new \DateTime($before_date, new \DateTimeZone($this->timezone));
		$this->afterConstraint = new \Recurr\Transformer\Constraint\BeforeConstraint($before_date, $inc);
	}

	public function setBetweenConstraintDate($after_date, $before_date, $inc)
	{
		$after_date   = new \DateTime($after_date, new \DateTimeZone($this->timezone));
		$before_date   = new \DateTime($before_date, new \DateTimeZone($this->timezone));
		$this->afterConstraint = new \Recurr\Transformer\Constraint\BetweenConstraint($after_date, $before_date, $inc);
	}

	public function setTimeZone($timezone)
	{
		$this->timezone = $timezone;
	}

	public function setFreq($freq)
	{
		$this->rule->setFreq($freq);
	}

	public function getFreq()
	{
		/*
		YEARLY   = 0;
		MONTHLY  = 1;
		WEEKLY   = 2;
		DAILY    = 3;
		HOURLY   = 4;
		MINUTELY = 5;
		SECONDLY = 6;
		*/
		$freq = array('YEARLY', 'MONTHLY', 'WEEKLY', 'DAILY', 'HOURLY', 'MINUTELY', 'SECONDLY');
		return $freq[$this->rule->getFreq()];
	}

	public function setCount($count)
	{
		$this->rule->setCount($count);
	}

	public function getCount()
	{
		return $this->rule->getCount();
	}

	public function setInterval($interval)
	{
		$this->rule->setInterval($interval);
	}

	public function getInterval()
	{
		return $this->rule->getInterval();
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

	public function getByDay()
	{
		return $this->rule->getByDay();
	}

	public function setByMonthDay($month_day)
	{
		$this->rule->setByMonthDay($month_day);
	}

	public function getByMonthDay()
	{
		return $this->rule->getByMonthDay();
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

	public function getByMonth($month)
	{
		return $this->rule->getByMonth();
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

	public function getFromRRule()
	{
		$this->rule->loadFromString($this->rrule);
	}

	public function getOccurrences()
	{
		if(isset($this->start_time) && $this->start_time != '') {
			$this->start_date = $this->start_date . $this->start_time;
		}
		if(isset($this->end_time) && $this->end_time != '') {
			$this->end_date = $this->end_date . $this->end_time;
		}

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
		
		return $this->transformer->transform($rule, null, $this->afterConstraint)->toArray();
	}

	public function getRRuleText()
	{
		try
		{
			include_once $this->APPLICATION_PATH."plugins/recurr/src/Recurr/Transformer/TextTransformer.php";
			$rule = new \Recurr\Rule($this->rrule, new \DateTime($this->timezone));
			
			$textTransformer = new \Recurr\Transformer\TextTransformer();
			return $textTransformer->transform($rule);
		}
		catch(Exception $e) {
			//Few RRULE's can't be transformed by library yet.
			return '';
		}
	}
}

?>