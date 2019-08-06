<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2019-01-01
 * Time: 20:06
 */

return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SOCKET_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [// Swoole Server的运行配置（ 完整配置可见[Swoole文档](https://wiki.swoole.com/wiki/page/274.html) ）
            'worker_num' => 8,//运行的  worker进程数量
            'max_request' => 5000,// worsker 完成该数量的请求后将退出，防止内存溢出
            'task_worker_num' => 8,//运行的 task_worker 进程数量
            'task_max_request' => 1000,// task_worker 完成该数量的请求后将退出，防止内存溢出
            'reload_async' => true,//设置异步重启开关。设置为true时，将启用异步安全重启特性，Worker进程会等待异步事件完成后再退出。
            'task_enable_coroutine' => true//开启后自动在onTask回调中创建协程
        ],
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    'DEBUG' => true,
    'EASY_CACHE' => [
        'PROCESS_NUM' => 1,//若不希望开启，则设置为0
        'PERSISTENT_TIME' => 0//如果需要定时数据落地，请设置对应的时间周期，单位为秒
    ],
    'CLUSTER' => [
        'enable' => false,
        'token' => null,
        'broadcastAddress' => ['255.255.255.255:9556'],
        'listenAddress' => '0.0.0.0',
        'listenPort' => '9556',
        'broadcastTTL' => 5,
        'nodeTimeout' => 10,
        'nodeName' => 'easySwoole',
        'nodeId' => null
    ],
    'MYSQL' => [
        //数据库配置
        'host' => '127.0.0.1',//数据库连接ip
        'user' => 'root',//数据库用户名
        'password' => 'root',//数据库密码
        'database' => 'easyswoole',//数据库
        'port' => '3306',//端口
        'timeout' => '30',//超时时间
        'connect_timeout' => '5',//连接超时时间
        'charset' => 'utf8',//字符编码
        'strict_type' => false, //开启严格模式，返回的字段将自动转为数字类型
        'fetch_mode' => false,//开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
        'alias' => '',//子查询别名
        'isSubQuery' => false,//是否为子查询
        'max_reconnect_times ' => '3',//最大重连次数
    ],
    /*################ REDIS CONFIG ##################*/
    'REDIS' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'auth' => '',
        'intervalCheckTime' => 30 * 1000,//定时验证对象是否可用以及保持最小连接的间隔时间
        'maxIdleTime' => 15,//最大存活时间,超出则会每$intervalCheckTime/1000秒被释放
        'maxObjectNum' => 20,//最大创建数量
        'minObjectNum' => 5,//最小创建数量 最小创建数量不能大于等于最大创建
    ]
];
