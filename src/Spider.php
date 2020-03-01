<?php
/**
 * @CreateTime:   2020/2/16 下午10:24
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫组件
 */
namespace EasySwoole\Spider;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\FastCache\Cache;
use EasySwoole\JobQueue\JobProcess;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Spider\Config\Config;
use EasySwoole\Spider\Config\ProductConfig;
use EasySwoole\Spider\Exception\SpiderException;
use EasySwoole\Spider\Queue\FastCacheQueue;
use EasySwoole\Spider\Queue\RedisQueue;

class Spider extends AbstractProcess
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

    protected function run($arg)
    {
        go(function (){

            $config = Config::getInstance();

            $config->getProduct()->productConfig = new ProductConfig($config->getProduct()->init());

            $this->setFirstProductJob();

        });
    }

    private function setFirstProductJob()
    {
        $config = Config::getInstance();
        $mainHost = $config->getMainHost();
        $firstJob = $config->getProduct();
        if (empty($mainHost)) {
            if (!empty($firstJob->productConfig->getUrl())) {
                $config->getQueue()->push($config->getJobQueueKey(), $firstJob);
            } else {
                throw new SpiderException('FirstJob url error!');
            }
        } else {
            $ip = gethostbyname(gethostname());
            if (!empty($ip) && $config->getMainHost() === $ip) {
                if (!empty($firstJob->productConfig->getUrl())) {
                    $config->getQueue()->push($config->getJobQueueKey(), $firstJob);
                } else {
                    throw new SpiderException('FirstJob url error!');
                }
            }
        }
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

        // 队列通信
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

        // 将spider绑定到主进程
        $swooleServer->addProcess($this->getProcess());

        // Job-queue 组件配置
        if (empty($this->config->getJobQueueProcessConfig())) {
            $jobQueueProcessConfig = new \EasySwoole\Component\Process\Config();
            $jobQueueProcessConfig->setProcessName('SpiderJobQueue');
        } else {
            $jobQueueProcessConfig = $this->config->getJobQueueProcessConfig();
        }

        $jobQueueProcessConfig->setArg([
            'queue' => $this->config->getQueue(),
            'maxCurrency' => $this->config->getMaxCoroutineNum(),
            'jobKey' => $this->config->getJobQueueKey()
        ]);

        $swooleServer->addProcess((new JobProcess($jobQueueProcessConfig))->getProcess());

    }

}

