<?php

use \ProductionCalendar\DayType;
use \ProductionCalendar\DataSource\SJDataProvider;
use \ProductionCalendar\DataSource\SJDataSource;

class SJDataSourceTest extends \PHPUnit\Framework\TestCase
{
    public function testLoadHolidays()
    {
        $year = 2033;

        $dataSource = $this->getMockBuilder(SJDataSource::class)
            ->setMethods(['retrieveData'])
            ->getMock();

        $dataSource->expects($this->any())
            ->method('retrieveData')
            ->with($year)
            ->will($this->returnCallback(function(){
                return json_decode(file_get_contents(__DIR__.'/calendar.json'), true);
            }));

        $expectedList = include __DIR__.'/expected_holidays.php';

        $obj = new SJDataProvider($dataSource);

        $result = $obj->getHolidayList($year, 'Y-m-d');
        $this->assertEquals($expectedList, $result);
    }
}