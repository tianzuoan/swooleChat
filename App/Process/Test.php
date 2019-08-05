<?php
/**
 * Created by PhpStorm.
 * User: tianzuoan
 * Date: 2019/7/6
 * Time: 下午12:14
 */

namespace App\Process;


use EasySwoole\Component\Process\AbstractProcess;
use Swoole\Process;

class Test extends AbstractProcess
{

    public function run($process)
    {
        // TODO: Implement run() method.
        $this->addTick(30000,function (){
            var_dump('this is '.$this->getProcessName().' process tick');
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
        var_dump('process rec'.$str);
    }
}