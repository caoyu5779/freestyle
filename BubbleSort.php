<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/5
 * Time: 下午3:08
 * DESC:
 */

$sourceArr = [123,234,21,2,5,3,2,1,241324,51,2,4,23,5,2,6,1,2,4,435,563,675,745,34234,43,68,122];
$destinationArr = [];



function bubbleSort($sourceArr)
{
    //求出循环长度
    $len = count($sourceArr);

    //数组中每一个都循环参与比较
    for ($i=1; $i<$len; $i++)
    {
        //每次比较，会有一个结果，比较出最小的，下一次执行可以减少一次循环
        for ($j=0;$j<$len-$i;$j++)
        {
            //数组值大小比较
            if ($sourceArr[$j] > $sourceArr[$j+1])
            {
                //移动
                $temp = $sourceArr[$j+1];
                $sourceArr[$j+1] = $sourceArr[$j];
                $sourceArr[$j] = $temp;
            }
        }
    }

    return $sourceArr;
}

$destinationArr = bubbleSort($sourceArr);
var_dump($destinationArr);exit;




