<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/10/18
 * Time: 上午11:35
 * DESC:
 */

$result = [];

$dates = [
    '2017-10-01',
    '2017-10-02',
    '2017-10-03',
    '2017-10-04',
    '2017-10-05',
];

$values = [
    10000,
    1000,
    100,
];

foreach ($dates as $date)
{
    $result[$date] = strtotime($date) / 43200;
}


foreach ($values as $value)
{
    $result[$value] = sprintf('%.5f', log10($value));
}


var_dump($result);