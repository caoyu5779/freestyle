<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/19
 * Time: 下午2:12
 * DESC: instance
 */
class Database
{
    private static $_instance;
    private static $_connectSource;

    const MSG_QUEUE_TABLE = 'msg_queue';

    private $_dbConfig = [
        'host' => '***',
        'port' => '***',
        'user' => '***',
        'password' => '***',
        'database' => '***',
    ];

    private function __construct()
    {
        if (!self::$_connectSource)
        {
            self::$_connectSource = new PDO(
                'mysql:host=' . $this->_dbConfig['host'] .';port=' .
                $this->_dbConfig['port'].';dbname='.
                $this->_dbConfig['database'] . ';charset=utf8;',
                $this->_dbConfig['user'],
                $this->_dbConfig['password']
            );

            if (!self::$_connectSource)
            {
                die('mysql connect failed' . mysqli_error($this->_dbConfig['database']));
            }
        }

        return self::$_connectSource;
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

    public function getResult()
    {
        $sql = 'select * from table limit 1';
        $exec = self::$_connectSource->prepare($sql);
        $exec->execute();
        return $exec->fetchAll();
    }

    /*
     * 插入数据
     * **/
    public function insert($array, $table)
    {
        $keys = join(',', array_keys($array));
        $values = "'" . join("','", array_values($array)) . "'";
        $sql = "INSERT INTO " . $table . "(" . $keys . ") VALUES ( " . $values . ")";
        $res = self::$_connectSource->prepare($sql);
        $res->execute();
        return self::$_connectSource->lastInsertId();
    }

}