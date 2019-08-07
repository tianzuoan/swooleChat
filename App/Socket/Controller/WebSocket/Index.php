<?php
namespace App\Socket\Controller\WebSocket;



use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller {
    /**
     * 访问找不到的action
     * @param string|null $actionName
     * @return string
     */
    public function actionNotFound(?string $actionName)
    {
        $this->response()->write("action call {$actionName} not found");
    }

    public function index()
    {
        $fd = $this->client()->getFd();
        $this->response()->write("you fd is {$fd}");
    }
}