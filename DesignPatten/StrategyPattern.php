<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/12
 * Time: 下午5:02
 * DESC:
 */
interface Travel
{
    public function go();
}

class bySelfDriving implements Travel
{
    public function go()
    {
        // TODO: Implement go() method.
        echo "开车";
    }
}

class byBus implements Travel
{
    public function go()
    {
        // TODO: Implement go() method.
        echo "公交";
    }
}

class byMetro implements Travel
{
    public function go()
    {
        // TODO: Implement go() method.
        echo "摩托";
    }
}

class byTrain implements Travel
{
    public function go()
    {
        // TODO: Implement go() method.
        echo "火车";
    }
}

class byAirplane implements Travel
{
    public function go()
    {
        // TODO: Implement go() method.
        echo "飞机";
    }
}

class byShip implements Travel
{
    public function go()
    {
        // TODO: Implement go() method.
        echo "轮船";
    }
}

class Mine
{
    private $_strategy;
    private $_isChange = false;

    public function __construct(Travel $travel)
    {
        $this->_strategy = $travel;
    }

    public function change(Travel $travel)
    {
        $this->_strategy = $travel;
        $this->_isChange = true;
    }

    public function goTravel()
    {
        if ($this->_isChange)
        {
            echo "现在改变主意";
            $this->_strategy->go();
        }
        else
        {
            $this->_strategy->go();
        }
    }
}

$strategy = new Mine(new byBus());
$strategy->goTravel();

$strategy->change(new byAirplane());
$strategy->goTravel();

$strategy->change(new byTrain());
$strategy->goTravel();