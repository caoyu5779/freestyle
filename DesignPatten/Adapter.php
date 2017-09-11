<?php
/**
 * Created by PhpStorm.
 * User: pengcheng8
 * Date: 17/9/11
 * Time: 下午2:39
 * DESC: 适配器模式演示代码。
 *          Target适配目标: IDataBase接口
 *          Adaptee被适配者: mysql和mysqli、postgresql的数据库操作函数
 *          Adapter适配器: mysql类和mysqli类、postgresql类
 */

/*
 * Interface IDatabase 适配目标，规定的接口将被适配对象实现
 * 约定好统一的api行为
 * */
interface IDatabase
{
    //定义数据库连接方法
    public function connect($host, $username, $password, $database);
    //定义数据库查询方法
    public function query($sql);
    //关闭数据库
    public function close();
}

/*
 * Class Mysql 适配器
 * **/
class Mysql implements IDatabase
{
    protected $connect;

    public function connect($host, $username, $password, $database)
    {
        // TODO: Implement connect() method.
        $connect = mysqli_connect($host, $username, $password, $database);
        mysqli_select_db($database, $connect);
        $this->connect = $connect;
    }

    public function query($sql)
    {
        // TODO: Implement query() method.
        return mysqli_query($this->connect,$sql);
    }

    public function close()
    {
        // TODO: Implement close() method.
        mysqli_close($this->connect);
    }
}

class Postgresql implements IDatabase
{
    protected $connect;

    public function connect($host, $username, $password, $database)
    {
        // TODO: Implement connect() method.
        $this->connect = pg_connect(
            "host=$host 
                              dbname=$database
                              user=$username
                              password=$password
                              ");
    }

    public function query($sql)
    {
        // TODO: Implement query() method.
        return pg_query($this->connect, $sql);
    }

    public function close()
    {
        // TODO: Implement close() method.
        pg_close();
    }
}

/*
 * 演示使用实例
 * 以mysqli为例
 * 因为都是同一个接口，所以可以随意切换
 * **/

$host = 'localhost';
$username = 'root';
$password = '123456';
$database = 'test';

$client = new mysqli();
$client->connect($host, $username, $password, $database);
$result = $client->query("SELECT * FROM DB");

while ($rows = mysqli_fetch_array($result))
{
    var_dump($rows);
}
