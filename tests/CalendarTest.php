<?php

class CalendarTest extends \PHPUnit\Framework\TestCase
{
     protected function getProviderMock()
    {
        $mock = $this->getMockBuilder(\ProductionCalendar\IDataProvider::class)
            ->setMethods(['getHolidayList'])
            ->getMock();

        $mock->expects($this->any())
            ->method('getHolidayList')
            ->with($this->anything(), $this->equalTo('Y-m-d'))
            ->will($this->returnValue([
                '2017-06-10',
                '2017-06-11',
                '2017-06-12',
            ])
        );

        return $mock;
    }

    protected function getTestObject()
    {
        return new \ProductionCalendar\Calendar($this->getProviderMock());
    }

    public function testGetIsHolidayShouldReturnTrue()
    {
        $holiday = new DateTime('2017-06-12');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsHoliday($holiday));
    }

    public function testGetIsHolidayShouldReturnFalse()
    {
        $workday =  new DateTime('2017-06-09');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsHoliday($workday));
    }

    public function testGetIsWorkdayShouldReturnTrue()
    {
        $workday = new DateTime('2017-06-09');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsWorkday($workday));
    }

    public function testGetIsWorkdayShouldReturnFalse()
    {
        $holiday = new DateTime('2017-06-12');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsWorkday($holiday));
    }

    /**
     * @dataProvider firstHolidayFixtures
     */
    public function testFindFirstHoliday($startday, $holiday)
    {
        $startday = new DateTime($startday);
        $holiday = new DateTime($holiday);
        $obj = $this->getTestObject();

        $this->assertEquals($holiday, $obj->findFirstHoliday($startday));
    }

    public function firstHolidayFixtures()
    {
        return [
            ['2017-06-08', '2017-06-10'],
            ['2017-06-09', '2017-06-10'],
            ['2017-06-10', '2017-06-10'],
        ];
    }

    /**
     * @dataProvider firstWorkdayFixtures
     */
    public function testFindFirstWorkday($startday, $workday)
    {
        $startday = new DateTime($startday);
        $workday = new DateTime($workday);
        $obj = $this->getTestObject();

        $this->assertEquals($workday, $obj->findFirstWorkday($startday));
    }

    public function firstWorkdayFixtures()
    {
        return [
            ['2017-06-11', '2017-06-13'],
            ['2017-06-12', '2017-06-13'],
            ['2017-06-13', '2017-06-13'],
        ];
    }

    public function testFindFirstWorkdayShouldNotChangeInput()
    {
        $holiday = new DateTime('2017-06-11');
        $holidayText = $holiday->format(DateTime::W3C);
        $obj = $this->getTestObject();

        $workday = $obj->findFirstWorkday($holiday);

        $this->assertNotEquals($holiday, $workday);
        $this->assertEquals($holidayText, $holiday->format(DateTime::W3C));
    }

    public function testFindFirstHolidayShouldNotChangeInput()
    {
        $workday = new DateTime('2017-06-08');
        $workdayText = $workday->format(DateTime::W3C);
        $obj = $this->getTestObject();

        $holiday = $obj->findFirstHoliday($workday);

        $this->assertNotEquals($workday, $holiday);
        $this->assertEquals($workdayText, $workday->format(DateTime::W3C));
    }

    public function testThatCalendarCanCreateWithDefaults()
    {
        $obj = \ProductionCalendar\Calendar::create();

        $this->assertInstanceOf(\ProductionCalendar\Calendar::class, $obj);
    }

    public function testGetIsLastWorkdayOfWeekShouldReturnTrue()
    {
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsLastWorkdayOfWeek(new DateTime('2017-06-09')));
    }

    public function testGetIsLastWorkdayOfWeekShouldReturnFalseForWorkday()
    {
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsLastWorkdayOfWeek(new DateTime('2017-06-08')));

    }

    public function testGetIsLastWorkdayOfWeekShouldReturnFalseForHoliday()
    {
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsLastWorkdayOfWeek(new DateTime('2017-06-11')));

    }
}