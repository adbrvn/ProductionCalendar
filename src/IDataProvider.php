<?php

namespace ProductionCalendar;

interface IDataProvider
{
    public function getHolidayList(int $year, $dateFormat);
}