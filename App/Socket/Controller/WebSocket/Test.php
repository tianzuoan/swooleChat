<?php

namespace App\Socket\Controller\WebSocket;

use App\Socket\Logic\Room;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

class Test extends Controller
{

    public function client()
    {
        return $this->caller()->getClient();
    }

    /**
     * 访问找不到的action
     * @param  ?string $actionName 找不到的name名
     * @return string
     */
    public function actionNotFound(?string $actionName)
    {
        $message = "action call {$actionName} not found";
        $this->response()->setMessage($message);
//        $this->response()->isFinish();
//        $this->response()->write("action call {$actionName} not found");
        ServerManager::getInstance()->getSwooleServer()->push($this->client()->getFd(), $message);
    }

    public function index()
    {
    }

    /**
     * 进入房间
     */
    public function intoRoom()
    {
        $param = $this->caller()->getArgs();
        $userId = $param['userId'];
        $userName = $param['name'];
        $roomId = $param['roomId'];
        $fd = $this->client()->getFd();
        Room::login($userId, $fd);
        Room::joinRoom($roomId, $fd, $userId, $userName);
        //异步推送
        TaskManager::async(function () use ($userId, $userName, $roomId) {
            $list = Room::selectRoomFd($roomId);
            foreach ($list as $fd) {
                $message = json_encode([
                    'type' => 'system',
                    'action' => 'join_room',
                    'userId' => $userId,
                    'userName' => $userName,
                    'message' => $userName . '进入房间'
                ]);
                ServerManager::getInstance()->getSwooleServer()->push($fd, $message);
            }
        });
    }

    /**
     * 发送信息到房间
     */
    public function sendToRoom()
    {
        $param = $this->caller()->getArgs();
        $message = $param['message'];
        $roomId = $param['roomId'];
        $fromUserId = $param['fromUserId'];
        //异步推送
        TaskManager::async(function () use ($fromUserId, $roomId, $message) {
            $list = Room::selectRoomFd($roomId);
            $resultMessage = json_encode([
                'type' => 'system',
                'action' => 'send_to_room',
                'message' => Room::getUserName($fromUserId) . '说:' . $message
            ]);
            foreach ($list as $fd) {
                ServerManager::getInstance()->getSwooleServer()->push($fd, $resultMessage);
            }
        });
    }

    /**
     * 发送私聊
     */
    public function sendToUser()
    {
        $param = $this->caller()->getArgs();
        $message = $param['message'];
        $fromUserId = $param['fromUserId'];
        $userId = $param['userId'];
        //异步推送
        TaskManager::async(function () use ($fromUserId, $userId, $message) {
            $resultMessage = json_encode([
                'type' => 'system',
                'action' => 'send_to_user',
                'message' => Room::getUserName($fromUserId) . '说:' . $message
            ]);
            $fdList = Room::getUserFd($userId);
            foreach ($fdList as $fd) {
                ServerManager::getInstance()->getSwooleServer()->push($fd, $resultMessage);
            }
            $fdList = Room::getUserFd($fromUserId);
            foreach ($fdList as $fd) {
                ServerManager::getInstance()->getSwooleServer()->push($fd, $resultMessage);
            }
        });
    }
}