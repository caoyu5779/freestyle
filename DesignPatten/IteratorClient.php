<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/11
 * Time: 下午5:53
 * DESC: PHP 自带了迭代器
 */

class IteratorClient implements Iterator
{
    private $position = 0;
    private $array = [];

    public function __construct($array)
    {
        $this->array = $array;
        $this->position = 0;
    }

    function rewind()
    {
        $this->position = 0;
    }

    function current()
    {
        // TODO: Implement current() method.
        return $this->array[$this->position];
    }

    function key()
    {
        return $this->position;
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        // TODO: Implement valid() method.
        return isset($this->array[$this->position]);
    }
}

class ConcreteAggregate implements IteratorAggregate
{
    public $property;

    /*
     * 添加属性
     * **/
    public function addProperty($property)
    {
        $this->property[] = $property;
    }

    public function getIterator()
    {
        // TODO: Implement getIterator() method.
        return new IteratorClient($this->property);
    }
}

class Client
{
    public static function test()
    {
        //创建容器
        $concreteAggregate = new ConcreteAggregate();
        //添加属性
        $concreteAggregate->addProperty('属性1');
        //添加属性
        $concreteAggregate->addProperty('属性2');
        //给容器创建迭代器
        $iterator = $concreteAggregate->getIterator();
        //遍历
        while($iterator->valid())
        {
            $key = $iterator->key();
            $value = $iterator->current();
            echo '键:' . $key . '值: ' . $value . "<hr>";
            $iterator->next();
        }

    }
}

client::test();
