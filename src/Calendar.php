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
        return !$this->isHoliday($date);
    }

    public function getIsHoliday(\DateTime $date)
    {
        return $this->isHoliday($date);
    }

    public function findFirstWorkday(\DateTime $from)
    {
        $interval = new \DateInterval("P1D");
        $date = clone($from);

        while ($this->isHoliday($date)) {
            $date->add($interval);
        }

        return $date;
    }

    public function findFirstHoliday(\DateTime $from)
    {
        $interval = new \DateInterval("P1D");
        $date = clone($from);

        while (!$this->isHoliday($date)) {
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

        if ($this->isHoliday($date)) {
            return false;
        }

        while ($endOfWeek > $currentDay) {
            $currentDay->add($interval);
            
            if (!$this->isHoliday($currentDay)) {
                return false;
            }
        }

        return true;
    }

    protected function isHoliday(\DateTime $date)
    {
        $dateFormat = 'Y-m-d';
        $year = (int) $date->format('Y');
        if (empty($this->holidays[$year])) {
            $this->holidays[$year] = $this->dataSource->getHolidayList($year, $dateFormat);
        }

        return in_array($date->format($dateFormat), $this->holidays[$year]);
    }
}