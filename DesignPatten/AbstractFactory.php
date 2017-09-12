<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/12
 * Time: 下午3:16
 * DESC:
 */
interface people
{
    public function say();
}

class FirstMan implements people
{
    public function say()
    {
        echo 'I love you';
    }
}

class SecondMan implements people
{
    public function say()
    {
        echo 'I need you';
    }
}

class FirstWomen implements people
{
    public function say()
    {
        echo 'leave me alone';
    }
}

class SecondWomen implements people
{
    public function say()
    {
        echo 'f**k you';
    }
}

interface createNewPeople
{
    public function createOne();
    public function createTwo();
}

class FactoryMan implements createNewPeople
{
    public function createOne()
    {
        // TODO: Implement createOne() method.
        return new FirstMan();
    }

    public function createTwo()
    {
        // TODO: Implement createTwo() method.
        return new SecondMan();
    }
}

class FactoryWomen implements createNewPeople
{
    public function createOne()
    {
        // TODO: Implement createOne() method.
        return new FirstWomen();
    }

    public function createTwo()
    {
        // TODO: Implement createTwo() method.
        return new SecondWomen();
    }
}

class Client
{
    public function test()
    {
        $factory = new FactoryMan();
        $man = $factory->createOne();
        $man->say();

        $man = $factory->createTwo();
        $man->say();

        $factory = new FactoryWomen();
        $women = $factory->createOne();
        $women->say();

        $women = $factory->createTwo();
        $women->say();
    }
}


$demo = new Client();
$demo->test();


