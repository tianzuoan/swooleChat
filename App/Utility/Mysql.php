<?php
/**
 * Created by PhpStorm.
 * User: anythink
 * Date: 2018/3/29
 * Time: 下午3:30
 */
namespace App\Utility;
//加载配置文件


use EasySwoole\EasySwoole\Config;
use EasySwoole\Mysqli\Mysqli;

class Mysql
{
    private $db;
    function __construct()
    {
        //读取配置文件
        $conf = new \EasySwoole\Mysqli\Config(Config::getInstance()->getConf('MYSQL'));
        $this->db = $db = new Mysqli($conf);
    }
    //返回实例化的对象
    function getDb()
    {
        return $this->db;
    }

    function insert($data){
        return $this->getDb()->insert($this->tableName,$data);
    }

    function findOne($where = [],$fields = '*'){
        $db = $this->getDb();
        if($where){
            foreach($where as $key=>$value){
                $db->where($key,$value);
            }
        }
        return $db->getOne($this->tableName,$fields);
    }

    function findAll($where = [],$fields = '*'){
        $db = $this->getDb();
        if($where){
            foreach($where as $key=>$value){
                $db->where($key,$value);
            }
        }
        return $db->get($this->tableName, null,$fields);
    }
}