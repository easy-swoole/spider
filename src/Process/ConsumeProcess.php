<?php
/**
 * @CreateTime:   2020/2/16 下午10:45
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  消费者进程
 */
namespace Easyswoole\Spider\Process;

use EasySwoole\Component\Process\AbstractProcess;
use Easyswoole\Spider\Config\Config;
use Swoole\Coroutine;

class ConsumeProcess extends AbstractProcess
{

    protected function run($arg)
    {
        // TODO: Implement run() method.
        $config = Config::getInstance();
        for ($i=0;$i<$config->getConsumeCoroutineNum();$i++) {
            go(function () use ($config){
                while (true) {
                    $data = $config->getQueue()->pop($config->getConsumeQueueKey());
                    if (empty($data)) {
                        Coroutine::sleep(0.1);
                        continue;
                    }
                    $config->getConsume()->consume(json_decode($data, true));
                }
            });
        }

    }
}
