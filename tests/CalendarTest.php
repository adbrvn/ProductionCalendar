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
                '2017-06-10' => 'weekday',
                '2017-06-11' => 'weekday',
                '2017-06-12' => 'holiday',
            ])
        );

        return $mock;
    }

    protected function getTestObject()
    {
        return new \ProductionCalendar\Calendar($this->getProviderMock());
    }

    public function testGetIsHolidayShouldReturnTrueOnHoliday()
    {
        $holiday = new DateTime('2017-06-12');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsHoliday($holiday));
    }

    public function testGetIsHolidayShouldReturnFalseOnWorkingDay()
    {
        $workingday =  new DateTime('2017-06-09');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsHoliday($workingday));
    }

    public function testGetIsHolidayShouldReturnFalseOnWeekday()
    {
        $weekday =  new DateTime('2017-06-10');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsHoliday($weekday));
    }

    public function testGetIsWeekdayShouldReturnTrueOnWeekday()
    {
        $weekday =  new DateTime('2017-06-10');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsWeekday($weekday));
    }

    public function testGetIsWeekdayShouldReturnFalseOnHoliday()
    {
        $holiday =  new DateTime('2017-06-12');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsWeekday($holiday));
    }

    public function testGetIsWorkingdayShouldReturnTrue()
    {
        $workingday = new DateTime('2017-06-09');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsWorkingday($workingday));
    }

    public function testGetIsWorkingdayShouldReturnFalse()
    {
        $holiday = new DateTime('2017-06-12');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsWorkingday($holiday));
    }

    public function testGetIsNonWorkingdayShouldReturnTrueForHoliday()
    {
        $workingday = new DateTime('2017-06-12');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsNonWorkingday($workingday));
    }

    public function testGetIsNonWorkingdayShouldReturnTrueForWeekendDay()
    {
        $workingday = new DateTime('2017-06-11');
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsNonWorkingday($workingday));
    }

    public function testGetIsNonWorkingdayShouldReturnFalse()
    {
        $holiday = new DateTime('2017-06-09');
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsNonWorkingday($holiday));
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
     * @dataProvider firstWorkingdayFixtures
     */
    public function testFindFirstWorkingday($startday, $workingday)
    {
        $startday = new DateTime($startday);
        $workingday = new DateTime($workingday);
        $obj = $this->getTestObject();

        $this->assertEquals($workingday, $obj->findFirstWorkingday($startday));
    }

    public function firstWorkingdayFixtures()
    {
        return [
            ['2017-06-11', '2017-06-13'],
            ['2017-06-12', '2017-06-13'],
            ['2017-06-13', '2017-06-13'],
        ];
    }

    public function testFindFirstWorkingdayShouldNotChangeInput()
    {
        $holiday = new DateTime('2017-06-11');
        $holidayText = $holiday->format(DateTime::W3C);
        $obj = $this->getTestObject();

        $workingday = $obj->findFirstWorkingday($holiday);

        $this->assertNotEquals($holiday, $workingday);
        $this->assertEquals($holidayText, $holiday->format(DateTime::W3C));
    }

    public function testFindFirstHolidayShouldNotChangeInput()
    {
        $workingday = new DateTime('2017-06-08');
        $workingdayText = $workingday->format(DateTime::W3C);
        $obj = $this->getTestObject();

        $holiday = $obj->findFirstHoliday($workingday);

        $this->assertNotEquals($workingday, $holiday);
        $this->assertEquals($workingdayText, $workingday->format(DateTime::W3C));
    }

    public function testThatCalendarCanCreateWithDefaults()
    {
        $obj = \ProductionCalendar\Calendar::create();

        $this->assertInstanceOf(\ProductionCalendar\Calendar::class, $obj);
    }

    public function testGetIsLastWorkingdayOfWeekShouldReturnTrue()
    {
        $obj = $this->getTestObject();

        $this->assertTrue($obj->getIsLastWorkingdayOfWeek(new DateTime('2017-06-09')));
    }

    public function testGetIsLastWorkingdayOfWeekShouldReturnFalseForWorkingday()
    {
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsLastWorkingdayOfWeek(new DateTime('2017-06-08')));

    }

    public function testGetIsLastWorkingdayOfWeekShouldReturnFalseForHoliday()
    {
        $obj = $this->getTestObject();

        $this->assertFalse($obj->getIsLastWorkingdayOfWeek(new DateTime('2017-06-11')));

    }
}
