<?php
namespace App\Socket\Logic;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\RedisPool\Redis;


class Room
{
    /**
     * 获取Redis连接实例
     *
     * @return mixed|null
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    protected static function getRedis()
    {
        return Redis::getInstance()->pool('redis')::defer();
    }

    /**
     * 进入房间
     * @param int $roomId 房间id
     * @param int $fd 连接id
     * @param int $userId userId
     * @param string $userName
     * @return void
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function joinRoom(int $roomId, int $fd ,int $userId, string $userName)
    {
        self::getRedis()->hSet("member:{$userId}","name",$userName);
        self::getRedis()->zAdd('rfMap', $roomId, $fd);
        self::getRedis()->hSet("room:{$roomId}", $fd, $userId.','.$userName);
    }

    /**
     * 登录
     * @param int $userId 用户id
     * @param int $fd 连接id
     * @return void
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function login(int $userId, int $fd)
    {
        self::getRedis()->zAdd('online', $userId, $fd);
    }

    /**
     * 获取用户
     * @param int $userId
     * @return array  $user
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function getUserName(int $userId)
    {
        return self::getRedis()->hGet("member:{$userId}", "name");
    }

    /**
     * 获取用户fd
     * @param int $userId
     * @return array         用户fd集
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function getUserFd(int $userId)
    {
        return self::getRedis()->zrangebyscore('online', $userId, $userId);
    }

    /**
     * 获取RoomId
     * @param int $fd
     * @return int    RoomId
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function getRoomId(int $fd)
    {
        return self::getRedis()->zScore('rfMap', $fd);
    }


    /**
     * 获取room中全部fd
     * @param int $roomId roomId
     * @return array         房间中fd
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function selectRoomFd(int $roomId)
    {
        return self::getRedis()->hKeys("room:{$roomId}");
    }

    /**
     * 获取room中全部UserId
     * @param int $roomId roomId
     * @return array         房间中UserId
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function selectRoomAllUser(int $roomId)
    {
        return self::getRedis()->hVals("room:{$roomId}");
    }

    /**
     * 获取room中全部UserId
     * @param int $roomId roomId
     * @param int $fd
     * @return bool
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function selectRoomOneUser(int $roomId,int $fd)
    {
        $user = self::getRedis()->hGet("room:{$roomId}",$fd);
        if(empty($user)){
            return false;
        }
        return explode(",",$user);
    }

    /**
     * 退出room
     * @param int $roomId roomId
     * @param int $fd fd
     * @return void
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function exitRoom(int $roomId, int $fd)
    {
        $userModel = Room::selectRoomOneUser($roomId,$fd);
        TaskManager::async(function ()use($roomId,$userModel){
            $list = Room::selectRoomFd($roomId);
            foreach ($list as $fd) {
                $message = json_encode([
                    'type'=>'system',
                    'action'=>'exit_room',
                    'userId'=>$userModel[0],
                    'message'=>$userModel[1].'退出房间'
                ]);
                ServerManager::getInstance()->getSwooleServer()->push($fd,$message);
            }
        });
        self::getRedis()->hDel("room:{$roomId}", $fd);
        self::getRedis()->zRem('rfMap', $fd);
    }

    /**
     * 关闭连接
     * @param int $fd 链接id
     * @throws \EasySwoole\Component\Pool\Exception\PoolEmpty
     * @throws \EasySwoole\Component\Pool\Exception\PoolException
     */
    public static function close(int $fd)
    {
        $roomId = self::getRoomId($fd);
        self::exitRoom($roomId, $fd);
        self::getRedis()->zRem('online', $fd);
    }
}