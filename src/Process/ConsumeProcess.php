<?php
/**
 * @CreateTime:   2020/2/16 下午10:45
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  消费者进程
 */
namespace EasySwoole\Spider\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Spider\Config\Config;
use EasySwoole\Spider\ConsumeJob;
use EasySwoole\Spider\Exception\SpiderException;
use Swoole\Coroutine;

class ConsumeProcess extends AbstractProcess
{

    protected function run($arg)
    {
        $config = Config::getInstance();
        for ($i=0;$i<$config->getConsumeCoroutineNum();$i++) {
            go(function () use ($config){
                while (true) {
                    $consumeJob = $config->getQueue()->pop($config->getConsumeQueueKey());
                    $consumeJob = unserialize($consumeJob);

                    if ($consumeJob instanceof ConsumeJob) {
                        $config->getConsume()->consume($consumeJob);
                    } else {
                        throw new SpiderException('ConsumeJob type error!');
                    }
                }
            });
        }

    }
}
