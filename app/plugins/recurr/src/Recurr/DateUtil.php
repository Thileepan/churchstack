<?php

/*
 * Copyright 2013 Shaun Simmons
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Based on rrule.js
 * Copyright 2010, Jakub Roztocil and Lars Schoning
 * https://github.com/jkbr/rrule/blob/master/LICENCE
 *
 * Based on python-dateutil - Extensions to the standard Python datetime module.
 * Copyright (c) 2003-2011 - Gustavo Niemeyer <gustavo@niemeyer.net>
 * Copyright (c) 2012 - Tomi Pieviläinen <tomi.pievilainen@iki.fi>
 */

namespace Recurr;

use Recurr\DaySet;
use Recurr\Time;
use Recurr\RecurrenceRule;

/**
 * Class DateUtil is responsible for providing utilities applicable to RecurrenceRules.
 *
 * @package Recurr
 * @author  Shaun Simmons <shaun@envysphere.com>
 */
class DateUtil
{
    public static $leapBug = null;

    public static $monthEndDoY366 = array(
        0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366
    );

    public static $monthEndDoY365 = array(
        0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365
    );

    public static $wDayMask = array(
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6, 0, 1, 2, 3, 4, 5, 6,
        0, 1, 2, 3, 4, 5, 6,
    );

    /**
     * Get an object containing info for a particular date
     *
     * @param \DateTime $dt
     *
     * @return DateInfo
     */
    public static function getDateInfo(\DateTime $dt)
    {
        $i              = new DateInfo();
        $i->dt          = $dt;
        $i->dayOfWeek   = self::getDayOfWeek($dt);
        $i->monthLength = $dt->format('t');
        $i->yearLength  = self::getYearLength($dt);

        $i->mMask       = self::getMonthMask($dt);
        $i->mDayMask    = self::getMonthDaysMask($dt);
        $i->mDayMaskNeg = self::getMonthDaysMask($dt, true);

        if ($i->yearLength == 365) {
            $i->mRanges = self::$monthEndDoY365;
        } else {
            $i->mRanges = self::$monthEndDoY366;
        }

        $tmpDt = clone $dt;
        $tmpDt->setDate($dt->format('Y') + 1, 1, 1);
        $i->nextYearLength = self::getYearLength($tmpDt);

        $tmpDt = clone $dt;
        $tmpDt->setDate($dt->format('Y'), 1, 1);
        $i->dayOfWeekYearDay1 = self::getDayOfWeek($tmpDt);

        $i->wDayMask = array_slice(
            self::$wDayMask,
            $i->dayOfWeekYearDay1
        );

        return $i;
    }

    /**
     * Get an array of DOY (Day of Year) for each day in a particular week.
     *
     * @param \DateTime           $dt
     * @param \DateTime           $start
     * @param null|RecurrenceRule $rule
     * @param null|DateInfo       $dtInfo
     *
     * @return DaySet
     */
    public static function getDaySetOfWeek(
        \DateTime $dt,
        \DateTime $start,
        RecurrenceRule $rule = null,
        DateInfo $dtInfo = null
    )
    {
        $start = clone $dt;
        $start->setDate($start->format('Y'), 1, 1);

        $diff  = $dt->diff($start);
        $start = $diff->days;

        $set = array();
        for ($i = $start, $k = 0; $k < 7; $k++) {
            $set[] = $i;
            ++$i;

            if (null !== $dtInfo && null !== $rule && $dtInfo->wDayMask[$i] == $rule->getWeekStartAsNum()) {
                break;
            }
        }

        $obj = new DaySet($set, $start, $i);

        return $obj;
    }

    /**
     * @param RecurrenceRule $rule
     * @param \DateTime      $dt
     * @param DateInfo       $dtInfo
     * @param \DateTime      $start
     *
     * @return DaySet
     */
    public static function getDaySet(RecurrenceRule $rule, \DateTime $dt, DateInfo $dtInfo, $start)
    {
        switch ($rule->getFreq()) {
            case RecurrenceRule::FREQ_SECONDLY:
                return self::getDaySetOfDay($dt, $start, $rule, $dtInfo);
                break;
            case RecurrenceRule::FREQ_MINUTELY:
                return self::getDaySetOfDay($dt, $start, $rule, $dtInfo);
                break;
            case RecurrenceRule::FREQ_HOURLY:
                return self::getDaySetOfDay($dt, $start, $rule, $dtInfo);
                break;
            case RecurrenceRule::FREQ_DAILY:
                return self::getDaySetOfDay($dt, $start, $rule, $dtInfo);
                break;
            case RecurrenceRule::FREQ_WEEKLY:
                return self::getDaySetOfWeek($dt, $start, $rule, $dtInfo);
            case RecurrenceRule::FREQ_MONTHLY:
                return self::getDaySetOfMonth($dt, $start, $rule, $dtInfo);
            case RecurrenceRule::FREQ_YEARLY:
                return self::getDaySetOfYear($dt, $start, $rule, $dtInfo);
        }
    }

    /**
     * Get an array of DOY (Day of Year) for each day in a particular year.
     *
     * @param \DateTime $dt The datetime
     *
     * @return DaySet
     */
    public static function getDaySetOfYear(\DateTime $dt)
    {
        $yearLen = self::getYearLength($dt);
        $set     = range(0, $yearLen - 1);

        return new DaySet($set, 0, $yearLen);
    }

    /**
     * Get an array of DOY (Day of Year) for each day in a particular month.
     *
     * @param \DateTime $dt The datetime
     *
     * @return DaySet
     */
    public static function getDaySetOfMonth(\DateTime $dt)
    {
        $dateInfo = self::getDateInfo($dt);
        $monthNum = $dt->format('n');

        $start = $dateInfo->mRanges[$monthNum - 1];
        $end   = $dateInfo->mRanges[$monthNum];

        $days = range(1, $dt->format('t'));
        $set  = range($start, $end - 1);
        $set  = array_combine($days, $set);
        $obj  = new DaySet($set, $start, $end - 1);

        return $obj;
    }

    /**
     * Get an array of DOY (Day of Year) for each day in a particular month.
     *
     * @param \DateTime $dt The datetime
     *
     * @return DaySet
     */
    public static function getDaySetOfDay(\DateTime $dt)
    {
        $dayOfYear = $dt->format('z');

        if (self::isLeapYearDate($dt) && self::hasLeapYearBug() && $dt->format('nj') > 229) {
            $dayOfYear -= 1;
        }

        $start = $dayOfYear;
        $end   = $dayOfYear;

        $set = range($start, $end);
        $obj = new DaySet($set, $start, $end + 1);

        return $obj;
    }

    /**
     * @param RecurrenceRule $rule
     * @param \DateTime      $dt
     *
     * @return array
     */
    public static function getTimeSetOfHour(RecurrenceRule $rule, \DateTime $dt)
    {
        $set = array();

        $hour     = $dt->format('G');
        $byMinute = $rule->getByMinute();
        $bySecond = $rule->getBySecond();

        if (empty($byMinute)) {
            $byMinute = array($dt->format('i'));
        }

        if (empty($bySecond)) {
            $bySecond = array($dt->format('s'));
        }

        foreach ($byMinute as $minute) {
            foreach ($bySecond as $second) {
                $set[] = new Time($hour, $minute, $second);
            }
        }

        return $set;
    }

    /**
     * @param RecurrenceRule $rule
     * @param \DateTime      $dt
     *
     * @return array
     */
    public static function getTimeSetOfMinute(RecurrenceRule $rule, \DateTime $dt)
    {
        $set = array();

        $hour     = $dt->format('G');
        $minute   = $dt->format('i');
        $bySecond = $rule->getBySecond();

        if (empty($bySecond)) {
            $bySecond = array($dt->format('s'));
        }

        foreach ($bySecond as $second) {
            $set[] = new Time($hour, $minute, $second);
        }

        return $set;
    }

    /**
     * @param \DateTime      $dt
     *
     * @return array
     */
    public static function getTimeSetOfSecond(\DateTime $dt)
    {
        return array(new Time($dt->format('G'), $dt->format('i'), $dt->format('s')));
    }

    /**
     * @param RecurrenceRule $rule
     * @param \DateTime      $dt
     *
     * @return array
     */
    public static function getTimeSet(RecurrenceRule $rule, \DateTime $dt)
    {
        $set = array();

        if (null === $rule || $rule->getFreq() >= RecurrenceRule::FREQ_HOURLY) {
            return $set;
        }

        $byHour   = $rule->getByHour();
        $byMinute = $rule->getByMinute();
        $bySecond = $rule->getBySecond();

        if (empty($byHour)) {
            $byHour = array($dt->format('G'));
        }

        if (empty($byMinute)) {
            $byMinute = array($dt->format('i'));
        }

        if (empty($bySecond)) {
            $bySecond = array($dt->format('s'));
        }

        foreach ($byHour as $hour) {
            foreach ($byMinute as $minute) {
                foreach ($bySecond as $second) {
                    $set[] = new Time($hour, $minute, $second);
                }
            }
        }

        return $set;
    }

    /**
     * Get a reference array with the day number for each day of each month.
     *
     * @param \DateTime $dt The datetime
     * @param bool      $negative
     *
     * @return array
     */
    public static function getMonthDaysMask(\DateTime $dt, $negative = false)
    {
        if ($negative) {
            $m29 = range(-29, -1);
            $m30 = range(-30, -1);
            $m31 = range(-31, -1);
        } else {
            $m29 = range(1, 29);
            $m30 = range(1, 30);
            $m31 = range(1, 31);
        }

        $mask = array_merge(
            $m31, // Jan (31)
            $m29, // Feb (28)
            $m31, // Mar (31)
            $m30, // Apr (30)
            $m31, // May (31)
            $m30, // Jun (30)
            $m31, // Jul (31)
            $m31, // Aug (31)
            $m30, // Sep (30)
            $m31, // Oct (31)
            $m30, // Nov (30)
            $m31, // Dec (31)
            array_slice(
                $m31,
                0,
                7
            )
        );

        if (self::isLeapYearDate($dt)) {
            return $mask;
        } else {
            if ($negative) {
                $mask = array_merge(array_slice($mask, 0, 31), array_slice($mask, 32));
            } else {
                $mask = array_merge(array_slice($mask, 0, 58), array_slice($mask, 59));
            }

            return $mask;
        }
    }

    public static function getMonthMask(\DateTime $dt)
    {
        if (self::isLeapYearDate($dt)) {
            return array_merge(
                array_fill(0, 31, 1), // Jan (31)
                array_fill(0, 29, 2), // Feb (29)
                array_fill(0, 31, 3), // Mar (31)
                array_fill(0, 30, 4), // Apr (30)
                array_fill(0, 31, 5), // May (31)
                array_fill(0, 30, 6), // Jun (30)
                array_fill(0, 31, 7), // Jul (31)
                array_fill(0, 31, 8), // Aug (31)
                array_fill(0, 30, 9), // Sep (30)
                array_fill(0, 31, 10), // Oct (31)
                array_fill(0, 30, 11), // Nov (30)
                array_fill(0, 31, 12), // Dec (31)
                array_fill(0, 7, 1)
            );
        } else {
            return array_merge(
                array_fill(0, 31, 1), // Jan (31)
                array_fill(0, 28, 2), // Feb (28)
                array_fill(0, 31, 3), // Mar (31)
                array_fill(0, 30, 4), // Apr (30)
                array_fill(0, 31, 5), // May (31)
                array_fill(0, 30, 6), // Jun (30)
                array_fill(0, 31, 7), // Jul (31)
                array_fill(0, 31, 8), // Aug (31)
                array_fill(0, 30, 9), // Sep (30)
                array_fill(0, 31, 10), // Oct (31)
                array_fill(0, 30, 11), // Nov (30)
                array_fill(0, 31, 12), // Dec (31)
                array_fill(0, 7, 1)
            );
        }
    }

    public static function getDateTimeByDayOfYear($dayOfYear, $year, \DateTimeZone $timezone)
    {
        $dtTmp = new \DateTime('now', $timezone);
        $dtTmp->setDate($year, 1, 1);
        $dtTmp->modify("+$dayOfYear day");

        return $dtTmp;
    }

    public static function hasLeapYearBug()
    {
        $leapBugTest = \DateTime::createFromFormat('z Y', '80 2016');
        if ($leapBugTest->format('Y-m-d') == '2016-03-22') {
            return true;
        }

        return false;
    }

    /**
     * closure/goog/math/math.js:modulo
     * Copyright 2006 The Closure Library Authors.
     *
     * The % operator in PHP returns the remainder of a / b, but differs from
     * some other languages in that the result will have the same sign as the
     * dividend. For example, -1 % 8 == -1, whereas in some other languages
     * (such as Python) the result would be 7. This function emulates the more
     * correct modulo behavior, which is useful for certain applications such as
     * calculating an offset index in a circular list.
     *
     * @param int $a The dividend.
     * @param int $b The divisor.
     *
     * @return int $a % $b where the result is between 0 and $b
     *   (either 0 <= x < $b
     *     or $b < x <= 0, depending on the sign of $b).
     */
    public static function pymod($a, $b)
    {
        $x = $a % $b;

        // If $x and $b differ in sign, add $b to wrap the result to the correct sign.
        return ($x * $b < 0) ? $x + $b : $x;
    }

    /**
     * Alias method to determine if a date falls within a leap year.
     *
     * @param \DateTime $dt
     *
     * @return bool
     */
    public static function isLeapYearDate(\DateTime $dt)
    {
        return $dt->format('L') ? true : false;
    }

    /**
     * Alias method to determine if a year is a leap year.
     *
     * @param int $year
     *
     * @return bool
     */
    public static function isLeapYear($year)
    {
        $isDivisBy4   = $year % 4 == 0 ? true : false;
        $isDivisBy100 = $year % 100 == 0? true : false;
        $isDivisBy400 = $year % 400 == 0 ? true : false;

        // http://en.wikipedia.org/wiki/February_29
        if ($isDivisBy100 && !$isDivisBy400) {
            return false;
        }

        return $isDivisBy4;
    }

    /**
     * Method to determine the day of the week from MO-SU.
     *
     * MO = Monday
     * TU = Tuesday
     * WE = Wednesday
     * TH = Thursday
     * FR = Friday
     * SA = Saturday
     * SU = Sunday
     *
     * @param \DateTime $dt
     *
     * @return string
     */
    public static function getDayOfWeekAsText(\DateTime $dt)
    {
        $dayOfWeek = $dt->format('w') - 1;

        if ($dayOfWeek < 0) {
            $dayOfWeek = 6;
        }

        $map = array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU');

        return $map[$dayOfWeek];
    }

    /**
     * Alias method to determine the day of the week from 0-6.
     *
     * 0 = Monday
     * 1 = Tuesday
     * 2 = Wednesday
     * 3 = Thursday
     * 4 = Friday
     * 5 = Saturday
     * 6 = Sunday
     *
     * @param \DateTime $dt
     *
     * @return int
     */
    public static function getDayOfWeek(\DateTime $dt)
    {
        $dayOfWeek = $dt->format('w') - 1;

        if ($dayOfWeek < 0) {
            $dayOfWeek = 6;
        }

        return $dayOfWeek;
    }

    /**
     * Get the number of days in a year.
     *
     * @param \DateTime $dt
     *
     * @return int
     */
    public static function getYearLength(\DateTime $dt)
    {
        return self::isLeapYearDate($dt) ? 366 : 365;
    }
}