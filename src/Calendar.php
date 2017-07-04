<?php

namespace ProductionCalendar;

use ProductionCalendar\DataSource\SJDataSource;
use ProductionCalendar\DataSource\SJDataProvider;

class Calendar
{
    protected $dataSource;
    protected $holidays;

    public static function create()
    {
        return new self(
            new SJDataProvider(
                new SJDataSource()
            )
        );
    }

    public function __construct(IDataProvider $dataSource)
    {
        $this->holidays = [];
        $this->dataSource = $dataSource;
    }

    public function getIsWorkingday(\DateTime $date)
    {
        return $this->isWorkingDay($date);
    }

    public function getIsNonWorkingday(\DateTime $date)
    {
        return $this->isNonWorkingDay($date);
    }

    public function getIsHoliday(\DateTime $date)
    {
        return $this->isDayOfType('holiday', $date);
    }

    public function getIsWeekday(\DateTime $date)
    {
        return $this->isDayOfType('weekday', $date);
    }

    public function findFirstWorkingday(\DateTime $from)
    {
        $interval = new \DateInterval("P1D");
        $date = clone($from);

        while ($this->isNonWorkingDay($date)) {
            $date->add($interval);
        }

        return $date;
    }

    public function findFirstHoliday(\DateTime $from)
    {
        $interval = new \DateInterval("P1D");
        $date = clone($from);

        while ($this->isWorkingDay($date)) {
            $date->add($interval);
        }

        return $date;
    }

    public function getIsLastWorkingdayOfWeek(\DateTime $date)
    {
        $interval = new \DateInterval("P1D");
        $currentDay = clone($date);
        $endOfWeek = clone($date);
        $endOfWeek->modify('Sunday');

        if ($this->isNonWorkingDay($date)) {
            return false;
        }

        while ($endOfWeek > $currentDay) {
            $currentDay->add($interval);

            if ($this->isWorkingDay($currentDay)) {
                return false;
            }
        }

        return true;
    }

	protected function isWorkingDay(\DateTime $date)
	{
		return !$this->isNonWorkingDay($date);
	}

    protected function isNonWorkingDay(\DateTime $date)
    {
		return $this->isDayOfType('holiday', $date) || $this->isDayOfType('weekday', $date);
    }

    protected function isHoliday(\DateTime $date)
	{
		return $this->isDayOfType('holiday', $date);
	}

	protected function isWeekday(\DateTime $date)
	{
		return $this->isDayOfType('weekday', $date);
	}

	protected function isDayOfType($type, \DateTime $date)
	{
        $dateFormat = 'Y-m-d';
        $year = (int) $date->format('Y');
        if (empty($this->holidays[$year])) {
			foreach ($this->dataSource->getHolidayList($year, $dateFormat) as $holidayDate => $holidayType) {
				$this->holidays[$year][$holidayDate] = $holidayType;
			}
        }

        return isset($this->holidays[$year][$date->format($dateFormat)]) ? $this->holidays[$year][$date->format($dateFormat)] == $type : false;
	}
}
