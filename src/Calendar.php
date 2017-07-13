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

    public function getIsWorkday(\DateTime $date)
    {
        return $this->isWorkDay($date);
    }

    public function getIsNonWorkday(\DateTime $date)
    {
        return $this->isNonWorkDay($date);
    }

    public function getIsHoliday(\DateTime $date)
    {
        return $this->isDayOfType('holiday', $date);
    }

    public function getIsWeekend(\DateTime $date)
    {
        return $this->isDayOfType('weekend', $date);
    }

    public function findFirstWorkday(\DateTime $from)
    {
        $interval = new \DateInterval("P1D");
        $date = clone($from);

        while ($this->isNonWorkDay($date)) {
            $date->add($interval);
        }

        return $date;
    }

    public function findFirstHoliday(\DateTime $from)
    {
        $interval = new \DateInterval("P1D");
        $date = clone($from);

        while ($this->isWorkDay($date)) {
            $date->add($interval);
        }

        return $date;
    }

    public function getIsLastWorkdayOfWeek(\DateTime $date)
    {
        $interval = new \DateInterval("P1D");
        $currentDay = clone($date);
        $endOfWeek = clone($date);
        $endOfWeek->modify('Sunday');

        if ($this->isNonWorkDay($date)) {
            return false;
        }

        while ($endOfWeek > $currentDay) {
            $currentDay->add($interval);

            if ($this->isWorkDay($currentDay)) {
                return false;
            }
        }

        return true;
    }

	protected function isWorkDay(\DateTime $date)
	{
		return !$this->isNonWorkDay($date);
	}

    protected function isNonWorkDay(\DateTime $date)
    {
		return $this->isDayOfType('holiday', $date) || $this->isDayOfType('weekend', $date);
    }

    protected function isHoliday(\DateTime $date)
	{
		return $this->isDayOfType('holiday', $date);
	}

	protected function isDayOfType($type, \DateTime $date)
	{
        $dateFormat = 'Y-m-d';
        $year = (int) $date->format('Y');
        if (empty($this->holidays[$year])) {
			$this->holidays[$year] = $this->dataSource->getHolidayList($year, $dateFormat);
        }

        return isset($this->holidays[$year][$date->format($dateFormat)]) ? $this->holidays[$year][$date->format($dateFormat)] == $type : false;
	}
}
