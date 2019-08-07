<?php
/**
 * Created by PhpStorm.
 * User: tianzuoan
 * Date: 2019/7/18
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Socket\Parser\WebSocket;
use App\Utility\SysTools;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Socket\Dispatcher;
use Swoole\Server;
use Swoole\WebSocket\Server as WebSocketServer;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
        $configData = Config::getInstance()->getConf('REDIS');
        $config = new \EasySwoole\RedisPool\Config($configData);
//        $config->setOptions(['serialize' => true]);
        /**
         * 这里注册的名字叫redis，你可以注册多个，比如redis2,redis3
         */
        $poolConf = Redis::getInstance()->register('redis', $config);
        $poolConf->setMaxObjectNum($configData['maxObjectNum']);
        $poolConf->setMinObjectNum($configData['minObjectNum']);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        $register->add(EventRegister::onStart, function () {
            cli_set_process_title(Config::getInstance()->getConf('SERVER_NAME') . '-Master');
        });

        $register->add(EventRegister::onManagerStart, function () {
            cli_set_process_title(Config::getInstance()->getConf('SERVER_NAME') . '-Manager');
        });

        $serverType = Config::getInstance()->getConf('MAIN_SERVER.SERVER_TYPE');
        $register->add(EventRegister::onClose, [self::class, 'onClose']);//关闭连接回调事件

        switch ($serverType) {
            case EASYSWOOLE_SERVER:
            {
                $register->add(EventRegister::onConnect, [self::class, 'onConnect']);
                $register->add(EventRegister::onReceive, [self::class, 'onReceive']);
                break;
            }
            case EASYSWOOLE_WEB_SERVER:
            {
                //request回调事件已经在核心注册了
                break;
            }
            case EASYSWOOLE_WEB_SOCKET_SERVER:
            {
                $register->add(EventRegister::onMessage, [self::class, 'onMessage']);//必选
                $register->add(EventRegister::onOpen, [self::class, 'onOpen']);
                $register->add(EventRegister::onHandShake, [self::class, 'onHandShake']);
                break;
            }
            case EASYSWOOLE_REDIS_SERVER:
            {
                break;
            }
            default:
            {
                Trigger::getInstance()->error('"unknown server type :{$type}"');
                return false;
            }
        }

    }

    public static function onConnect(Server $server, int $fd, int $reactorId)
    {
        echo "client {$fd} connected!" . PHP_EOL;
        $server->send($fd, "server:welcome to connect my service,it is my pleasure!reactor_id for this time:{$reactorId}" . PHP_EOL);
    }

    public static function onReceive(Server $server, int $fd, int $reactorId, string $data)
    {
        echo 'receive:' . $data;
//        var_dump($server->connection_info($fd));
        foreach ($server->connections as $clientFd) {
            if ($clientFd !== $fd) {
                $server->send($clientFd, "广播一下!" . PHP_EOL);
            }
        }
        $server->send($fd, "reactor_id:{$reactorId},server:{$data}");
    }

    public static function onOpen(WebSocketServer $server, $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    public static function onHandShake(\swoole_http_request $request, \swoole_http_response $response)
    {
        // print_r( $request->header );
        // if (如果不满足我某些自定义的需求条件，那么返回end输出，返回false，握手失败) {
        //    $response->end();
        //     return false;
        // }


        /*if (!isset($request->cookie['token']) || !isset($request->cookie['user_id'])) {
            var_dump('shake fai1 1');
            $response->end();
            return false;
        }
        if (!SysTools::authToken($request->cookie['user_id'], $request->cookie['token'])) {
            var_dump('shake fai1 2');
            $response->end();
            return false;
        }

        if (!isset($request->header['sec-websocket-key'])) {
            // 需要 Sec-WebSocket-Key 如果没有拒绝握手
            var_dump('shake fai1 3');
            $response->end();
            return false;
        }

        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            var_dump('shake fai1 4');
            $response->end();
            return false;
        }*/
//        echo $request->header['sec-websocket-key'];
        $key = base64_encode(sha1(
            $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
            true
        ));

        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();

    }

    /**
     * @param WebSocketServer $server
     * @param $frame
     * @throws \EasySwoole\Socket\Exception\Exception
     */
    public static function onMessage(WebSocketServer $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//        $data = ['type' => 'system', 'action' => 'join_room', 'message' => json_decode($frame->data)->data->message];
        $webScoketConfig = new \EasySwoole\Socket\Config();
        $webScoketConfig->setType($webScoketConfig::WEB_SOCKET);
        $webScoketConfig->setParser(WebSocket::class);
        $disPatcher = new Dispatcher($webScoketConfig);
        $disPatcher->dispatch($server, $frame->data, $frame);
//        $server->push($frame->fd, json_encode($data));
    }

    public static function onClose(Server $server, $fd)
    {
        echo "client {$fd} closed\n";
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        $origin = $request->getSwooleRequest()->server['HTTP_ORIGIN'] ?? '*';
        $allow_origin = [
            'http://localhost:9501',
        ];
//        if (in_array($origin, $allow_origin)) {
        $response->withHeader('Access-Control-Allow-Origin', $origin);
        $response->withHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
        $response->withHeader('Access-Control-Expose-Headers', 'Authorization, authenticated');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
//        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}