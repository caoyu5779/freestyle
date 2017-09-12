<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/12
 * Time: 下午4:18
 * DESC: 代理模式
 */
interface Subject
{
    public function say();
    public function run();
}

class RealSubject implements Subject
{
    private $_name;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public function say()
    {
        echo $this->_name . "在说话";
    }

    public function run()
    {
        echo $this->_name . "在跑步";
    }
}

class Proxy implements Subject
{
    //真实主题对象
    private $_realSubject = null;

    public function __construct(RealSubject $realSubject = null)
    {
        if (empty($realSubject))
        {
            $this->_realSubject = new RealSubject();
        }
        else
        {
            $this->_realSubject = $realSubject;
        }
    }

    public function say()
    {
        $this->_realSubject->say();
    }

    public function run()
    {
        $this->_realSubject->run();
    }
}

class Client
{
    public static function test()
    {
        $subject = new RealSubject("Lion");
        $proxy = new Proxy($subject);

        $proxy->say();
        $proxy->run();
    }
}

client::test();
