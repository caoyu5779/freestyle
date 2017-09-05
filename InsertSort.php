<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/5
 * Time: 下午4:35
 * DESC:
 */
$sourceArr = [];
$tmp = '';
$destinationArr = [];

function createSortArr()
{
    for ($i=0; $i<20; $i++)
    {
        $sourceArr[$i] = rand(1,9999);
    }

    return $sourceArr;
}

function insertSort($sourceArr)
{
    $len = count($sourceArr);

    for ($i=1; $i<$len; $i++)
    {
        $tmp = $sourceArr[$i];
        for ($j=$i-1; $j>=0; $j--)
        {
            if ($tmp < $sourceArr[$j])
            {
                $sourceArr[$j+1] = $sourceArr[$j];
                $sourceArr[$j] = $tmp;
            }
            else
            {
                break;
            }
        }
    }

    return $sourceArr;
}
$sourceArr = createSortArr();
$destinationArr = insertSort($sourceArr);
var_dump($destinationArr);exit;