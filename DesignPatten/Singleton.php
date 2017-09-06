<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/5
 * Time: 下午6:34
 * DESC:
 */
class Singleton
{
    private static $_db;
    private static $_instance;

    private function __construct()
    {
        $this->_db = mysqli_connect('localhost:8888');
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function instance()
    {
        if (!(self::$_instance instanceof self))
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function addUserInfo()
    {

    }

    public function updateUserInfo()
    {

    }
}