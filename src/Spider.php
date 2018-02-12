<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:04
 */

namespace EasySwoole\Spider;


use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Trigger;
use EasySwoole\Core\Swoole\Process\ProcessManager;
use EasySwoole\Spider\Task\Runner;

class Spider
{
    use Singleton;

    function __construct(callable $preCall = null)
    {
        if(is_callable($preCall)){
            try{
                call_user_func($preCall);
            }catch (\Throwable $throwable){
                Trigger::throwable($throwable);
            }
        }
    }

    function run(int $processNum = 1)
    {
        for($i = 1;$i <= $processNum;$i++){
            ProcessManager::getInstance()->addProcess("__Spider{$i}",Runner::class);
        }
    }
}