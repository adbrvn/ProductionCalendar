<?php

namespace ProductionCalendar\DataSource;

class SJDataSource
{
    public function retrieveData($year)
    {
        $url = sprintf('https://services.superjob.ru/calendar/months/%s', $year);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data, true);
    }
}