<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/12
 * Time: 下午3:05
 * DESC: 工厂方法模式
 */
interface people
{
    public function say();
}

class man implements people
{
    function say()
    {
        echo "I'm a man";
    }
}

class women implements people
{
    function say()
    {
        echo "I'm a women";
    }
}

/*
 * 此处与简单工厂对比，本质区别在于，此处将对象的创建抽象成一个接口
 * **/
interface createPeople
{
    public function create();
}

class FactoryMan implements createPeople
{
    public function create()
    {
        // TODO: Implement create() method.
        return new man();
    }
}

class FactoryWomen implements createPeople
{
    public function create()
    {
        // TODO: Implement create() method.
        return new women();
    }
}

class Client
{
    public function test()
    {
        $factory = new FactoryMan();
        $man = $factory->create();
        $man->say();

        $factory = new FactoryWomen();
        $women = $factory->create();
        $women->say();
    }
}

$demo = new Client();
$demo->test();