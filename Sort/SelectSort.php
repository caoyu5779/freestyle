<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/13
 * Time: 下午3:07
 * DESC: 选择排序就是假定数组的第一个是最小的
 */
function createSortArr()
{
    for ($i=0; $i<20; $i++)
    {
        $sourceArr[$i] = rand(1,9999);
    }

    return $sourceArr;
}

function selectSort($sourceArr)
{
    $len = count($sourceArr);
    //从数组中按顺序拿数据一次一个
    for($i=0; $i< $len -1 ; $i++)
    {
        //找到本次校验的角标
        $p = $i;
        //从下一个开始循环
        for ($j = $i+1; $j<$len; $j++)
        {
            //如果小，就换角标，找到循环中最小的
            if ($sourceArr[$j] < $sourceArr[$p])
            {
                $p = $j;
            }
        }
        //位移
        $tmp = $sourceArr[$p];
        $sourceArr[$p] = $sourceArr[$i];
        $sourceArr[$i] = $tmp;
    }

    return $sourceArr;
}

$sourceArr = createSortArr();
$destinationArr = selectSort($sourceArr);
var_dump($destinationArr);exit;
