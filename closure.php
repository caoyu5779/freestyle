<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/10/18
 * Time: 下午2:52
 * DESC:
 */

// 主要逻辑
$meet = function ($name){
    echo "nice to meet you, $name \n";
};
// 前置中间件
$hello = function($handler)
{
    return function ($name) use ($handler)
    {
        echo "hello " . $name . ", may I have your name \n";
        $name = 'Lucy';
        return $handler($name);
    };
};
// 前置中间件
$weather = function ($handler)
{
    return function ($name) use ($handler)
    {
        echo "what a day \n";

        return $handler($name);
    };
};
// 后置中间件
$dinner = function ($handler)
{
    return function ($name) use ($handler)
    {
        $return = $handler($name);
        $name = 'Lucy';
        echo "OK , $name , Will you have dinner with me ? \n";
        return $return;
    };
};
// 中间件栈
$stack = [];
// 打包
function prepare($handler, $stack)
{
    foreach (array_reverse($stack) as $key => $fn)
    {
        $handler = $fn($handler);
    }

    return $handler;
}

/*
 * prepare的方法简述下来应该是下面的这个函数。
 * $closure = $dinner($weather($hello($meet)));
 * $closure('beauty');
 * 闭包这个概念 还是很模糊，但是大体意思差不多了然了。
 * 这里正常了解的顺序应该是$hello($meet)->$weather($hello)->$dinner($weather)
 * 但是$hello($meet)中返回的不是句柄而是一个闭包，所以里层未执行。
 * $dinner($weather)中开始执行代码，因为$weather和$hello中都是返回的闭包。
 * 所以按顺序执行，应该是echo $weather -> echo $hello -> echo $meet -> echo dinner
 *
 * */

//入栈
$stack['dinner'] = $dinner;
$stack['weather'] = $weather;
$stack['hello'] = $hello;
//把所有逻辑打包成一个闭包(closure)
$run = prepare($meet, $stack);

$run('beauty');

