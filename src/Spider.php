<?php
/**
 * @CreateTime:   2020/2/16 下午10:24
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:
 */
namespace EasySwoole\Spider;

use EasySwoole\Component\Singleton;
use EasySwoole\FastCache\Cache;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Spider\Config\Config;
use EasySwoole\Spider\Process\ConsumeProcess;
use EasySwoole\Spider\Process\ProductProcess;
use EasySwoole\Spider\Queue\FastCacheQueue;
use EasySwoole\Spider\Queue\RedisQueue;

class Spider
{

    use Singleton;

    /**
     * @var $config Config
     */
    private $config;

    /**
     * 设置配置
     *
     * @param Config $config
     * CreateTime: 2020/2/22 下午3:46
     * @return Spider
     */
    public function setConfig(Config $config) : Spider
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 将进程绑定到swooleserver
     *
     * @param null|\swoole_server|\swoole_server_port|\swoole_websocket_server|\swoole_http_server $swooleServer
     * CreateTime: 2020/2/22 下午2:45
     * @return void
     * @throws \EasySwoole\Component\Process\Exception
     * @throws \EasySwoole\FastCache\Exception\RuntimeError
     */
    public function attachProcess($swooleServer)
    {

        switch ($this->config->getQueueType()) {
            case Config::QUEUE_TYPE_FAST_CACHE:
                Cache::getInstance()
                    ->setTempDir(EASYSWOOLE_TEMP_DIR)
                    ->attachToServer($swooleServer);
                $this->config->setQueue(new FastCacheQueue());
                break;
            case Config::QUEUE_TYPE_REDIS:
                $queueConfig =  $this->config->getQueueConfig();
                if (empty($queueConfig)) {
                    $queueConfig = new RedisConfig();
                }
                Redis::getInstance()->register(RedisQueue::REDIS_ALIAS, $queueConfig);
                $this->config->setQueue(new RedisQueue());
                break;
            default:
        }

        $swooleServer->addProcess((new ProductProcess())->getProcess());

        $swooleServer->addProcess((new ConsumeProcess())->getProcess());

    }

}

