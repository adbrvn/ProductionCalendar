<?php

namespace ProductionCalendar\DataSource;

use ProductionCalendar\DateTime;
use ProductionCalendar\DayType;
use ProductionCalendar\IDataProvider;

class SJDataProvider implements IDataProvider
{
    protected $source;

    public function __construct(SJDataSource $source)
    {
        $this->source = $source;
    }

    public function getHolidayList(int $year, $dateFormat)
    {
        $holidays = [];
        $data = $this->source->retrieveData($year);

        foreach ($data['content']['quarters'] as $quarter) {
            foreach ($quarter['months'] as $month) {
                foreach ($month['weeks'] as $week) {
                    foreach ($week['days'] as $day) {
                        switch ($day['type']) {
                            case 'weekend':
                            case 'holiday':
                                $holidays[(new \DateTime($day['date']))->format($dateFormat)] = $day['type'];
                                break;
                        }
                    }
                }
            }
        }

        return $holidays;
    }
}
