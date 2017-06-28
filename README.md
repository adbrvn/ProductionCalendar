Production Calendar
====================

Производственный календарь с праздниками РФ

powered by Superjob.ru

#### Usage

```
<?php

$calendar = \ProductionCalendar\Calendar::create(); 

$calendar->getIsWorkday(new \DateTime('2016-05-09')) // False
$calendar->getIsHolyday(new \DateTime('2016-06-12')) // True

$calendar->findFirstHoliday(new \DateTime('2017-06-08')) // DateTime<2017-06-10>
$calendar->findFirstWorkday(new \DateTime('2017-06-10')) // DateTime<2017-06-13>

```
