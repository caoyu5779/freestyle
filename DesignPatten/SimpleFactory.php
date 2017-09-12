<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/12
 * Time: 下午3:00
 * DESC: 简单工厂模式
 */

interface people
{
    public function say();
}

class man implements people
{
    public function say()
    {
        echo "I'm a man";
    }
}

class women implements people
{
    public function say()
    {
        // TODO: Implement say() method.
        echo "I'm a woman";
    }
}

class SimpleFactory
{
    static function createMan()
    {
        return new man();
    }

    static function createWomen()
    {
        return new women();
    }
}

$man = SimpleFactory::createMan();
$man->say();

$women = SimpleFactory::createWomen();
$women->say();


