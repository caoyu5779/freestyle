<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/6
 * Time: 上午11:14
 * DESC:
 */
class Comm_Db
{
    private $_db;
    private static $_instance;
    private static $_connectSource;

    private $_dbConfig = [
        'host' => '127.0.0.1',
        'port' => '8888',
        'user' => 'root',
        'password' => '123456',
        'database' => 'user'
    ];

    private function __construct(){

    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


    public static function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function connect()
    {
        if (!self::$_connectSource)
        {
            self::$_connectSource = mysqli_connect(
                $this->_dbConfig['host'],
                $this->_dbConfig['user'],
                $this->_dbConfig['password'],
                $this->_dbConfig['database'],
                $this->_dbConfig['port']
            );

            if (!self::$_connectSource)
            {
                die("mysql connect error " . mysqli_error($this->_dbConfig['database']));
            }

            mysqli_select_db(self::$_connectSource, $this->_dbConfig['database']);
            mysqli_query(self::$_connectSource, "set names UTF8 ");
        }

        return self::$_connectSource;
    }


}