<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/5
 * Time: 下午5:15
 * DESC:
 */
function createSortArr()
{
    for ($i=0; $i<20; $i++)
    {
        $sourceArr[$i] = rand(1,9999);
    }

    return $sourceArr;
}

function quickSort($sourceArr)
{
    $len = count($sourceArr);
    if ($len <= 1)
    {
        return $sourceArr;
    }

    $left = $right = array();

    $povit = $sourceArr[0];

    for ($i=1; $i<$len; $i++)
    {
        if ($povit < $sourceArr[$i])
        {
            $right[] = $sourceArr[$i];
        }
        else
        {
            $left[] = $sourceArr[$i];
        }
    }

    $left = quickSort($left);
    $right = quickSort($right);

    return array_merge($left, (array)$povit, $right);
}

$sourceArr = createSortArr();

$destArr = quickSort($sourceArr);
var_dump($destArr);exit;
