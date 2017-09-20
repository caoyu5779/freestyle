<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/20
 * Time: 上午10:35
 * DESC: php 中所有用到的sort 使用方法
 */
define('STR_LENGTH', 20);
define('ARR_LENGTH', 5);

function randString($type = 'string')
{
    $str = '';
    $strBase = ($type == 'string') ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' : '0123456789';
    $strMax = strlen($strBase) - 1;

    for ($i=0;$i<STR_LENGTH;$i++)
    {
        $str .= $strBase[rand(0, $strMax)];
    }

    return $str;
}

function createArray($type = 'string')
{
    $i = 0;
    $destArr = array();
    do
    {
        $destArr[] = randString($type);

        $i++;
    }while($i<ARR_LENGTH);

    return $destArr;
}

function sortArrayMultisort($twoDimenArray = [], $twoDimenSortArray = [])
{
    array_multisort($twoDimenArray[0], SORT_ASC,SORT_NUMERIC,
                    $twoDimenArray[1], SORT_DESC, SORT_NUMERIC);

    foreach ($twoDimenSortArray as $key => $value)
    {
        $volume[$key] = $value['volume'];
        $edition[$key] = $value['edition'];
    }

    array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $twoDimenSortArray);

    return [
        'twoDimen' => $twoDimenArray,
        'twoSortDimen' => $twoDimenSortArray
    ];
}

function compare($a, $b)
{
    if ($a == $b)
    {
        return 0;
    }
    else if ($a > $b)
    {
        return -1;
    }
    else
    {
        return 1;
    }
}

function usortCompare($a, $b)
{
    return strcmp($a["fruit"], $b["fruit"]);
}

try
{
    $sortArray = createArray();

    $twoDimenArray = [
        createArray('int'),
        createArray('int')
    ];

    $twoDimenSortArray[] = array('volume' => 67, 'edition' => 2);
    $twoDimenSortArray[] = array('volume' => 86, 'edition' => 1);
    $twoDimenSortArray[] = array('volume' => 85, 'edition' => 6);
    $twoDimenSortArray[] = array('volume' => 98, 'edition' => 2);
    $twoDimenSortArray[] = array('volume' => 86, 'edition' => 6);
    $twoDimenSortArray[] = array('volume' => 67, 'edition' => 7);

    $fruits[0]["fruit"] = "lemons";
    $fruits[1]["fruit"] = "apples";
    $fruits[2]["fruit"] = "grapes";

    //array_multisort()
    $arrayMultiSortRes = sortArrayMultisort($twoDimenArray, $twoDimenSortArray);

    //asort() 根据值排序 由低到高 int string 都可以排序
    asort($sortArray);

    //arsort() 根据值排序 由高到低 int string 都可以排序
    arsort($sortArray);

    //krsort() 根据键排序 由高到低
    krsort($sortArray);
    //ksort() 根据键排序 由低到高
    ksort($sortArray);
    //natcasesort() 根据值排序 自然排序 对大小写不敏感
    natcasesort($sortArray);
    //natsort() 根据值排序 自然排序
    natsort($sortArray);
    //rsort() 根据值排序 由高到低
    rsort($sortArray);
    //shuffle() 根据值排序 随机排序
    shuffle($sortArray);
    //sort() 根据值排序 由高到低
    sort($sortArray);
    //uasort() 自定义比较函数 对数组中的值进行排序 并保证索引关联
    $sortArray = createArray('int');
    uasort($sortArray, 'compare');
    //uksort() 自定义比较函数 对数组中的键进行排序
    //usort() 自定义比较函数 对数组中的值进行排序
    usort($fruits, "usortCompare");
}
catch (Exception $e)
{
    echo $e->getMessage();
}

