<?php

namespace App\Socket\Parser;

use App\Socket\Controller\WebSocket\Index;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;

class WebSocket implements ParserInterface
{

    public function decode($raw, $client): ?Caller
    {
        //检查数据是否为JSON
        $commandLine = json_decode($raw, true);
        if (!is_array($commandLine)) {
            return 'unknown command';
        }

        $CommandBean = new Caller();
        $control = isset($commandLine['controller']) ? 'App\\Socket\\Controller\\WebSocket\\' . ucfirst($commandLine['controller']) : '';
        $action = $commandLine['action'] ?? 'none';
        $data = $commandLine['data'] ?? null;
        //找不到类时访问默认Index类
        $CommandBean->setControllerClass(class_exists($control) ? $control : Index::class);
        $CommandBean->setAction(class_exists($control) ? $action : 'controllerNotFound');
        $CommandBean->setArgs($data);

        return $CommandBean;
    }

    public function encode(Response $raw, $client): ?string
    {
        // TODO: Implement encode() method.
        return $raw;
    }
}