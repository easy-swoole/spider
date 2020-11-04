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
use EasySwoole\FastCache\Cache;
use EasySwoole\Queue\Job;
use EasySwoole\Queue\Queue;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Spider\Config\ProductConfig;
use EasySwoole\Spider\Config\SpiderConfig;
use EasySwoole\Spider\Exception\SpiderException;
use EasySwoole\Spider\Hole\ConsumeAbstract;
use EasySwoole\Spider\Hole\ProductAbstract;
use EasySwoole\Spider\Queue\FastCacheQueue;
use EasySwoole\Spider\Queue\RedisPoolQueue;
use Swoole\Coroutine;

class SpiderServer extends AbstractProcess
{

    use Singleton;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        libxml_use_internal_errors(true);
    }

    /**
     * @var $spiderConfig SpiderConfig
     */
    protected $spiderConfig;

    public function setSpiderConfig(array $config) : SpiderServer
    {
        $this->spiderConfig = new SpiderConfig($config);
        return $this;
    }

    public function getSpiderConfig():SpiderConfig
    {
        return $this->spiderConfig;
    }

    protected function run($arg)
    {
        Coroutine::create(function (){
            $this->spiderConfig->getJobQueue()->consumer()->listen(function (Job $job){
                if ($job instanceof ProductAbstract) {
                    $productResult = $job->product();
                    $this->productResultDeal($productResult);
                } elseif ($job instanceof ConsumeAbstract) {
                    $job->consume();
                } else {
                    throw new SpiderException('Job type error!');
                }
            }, 0.01, 3.0, $this->spiderConfig->getMaxCurrency());
        });
    }

    private function productResultDeal($productResult)
    {
        if ($productResult instanceof ProductResult) {

            Coroutine::create(function () use($productResult) {
                $productJobConfigs = $productResult->getProductJobConfigs();
                if (!empty($productJobConfigs)) {
                    foreach ($productJobConfigs as $productJobConfig) {
                        $productConfig = new ProductConfig($productJobConfig);
                        $product = $this->spiderConfig->getProduct();
                        /** @var $productJob ProductAbstract*/
                        $productJob = new $product();
                        $productJob->productConfig = $productConfig;
                        $this->spiderConfig
                            ->getJobQueue()
                            ->producer()
                            ->push($productJob);
                    }
                }
            });

            Coroutine::create(function () use($productResult) {
                $consumeData = $productResult->getConsumeData();
                if (!empty($consumeData)) {
                    /** @var $consumeJob ConsumeAbstract*/
                    $consume = $this->spiderConfig->getConsume();
                    $consumeJob = new $consume();
                    $consumeJob->setJobData($consumeData);
                    $this->spiderConfig
                        ->getJobQueue()
                        ->producer()
                        ->push($consumeJob);
                }
            });
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
     * @throws \EasySwoole\RedisPool\RedisPoolException
     */
    public function attachProcess($swooleServer)
    {

        switch ($this->spiderConfig->getQueueType()) {
            case SpiderConfig::QUEUE_TYPE_FAST_CACHE:
                Cache::getInstance()
                    ->setTempDir(EASYSWOOLE_TEMP_DIR)
                    ->attachToServer($swooleServer);
                $this->spiderConfig->setQueue(new FastCacheQueue());
                break;
            case SpiderConfig::QUEUE_TYPE_REDIS:
                $queueConfig =  $this->spiderConfig->getQueueConfig();
                if (empty($queueConfig)) {
                    $queueConfig = new RedisConfig();
                }
                Redis::getInstance()->register(RedisPoolQueue::REDIS_JOB_QUEUE_KEY, $queueConfig);
                $this->spiderConfig->setQueue(new RedisPoolQueue());
                break;
            default:
        }

        $this->spiderConfig->setJobQueue(new Queue($this->spiderConfig->getQueue()));

        $swooleServer->addProcess($this->getProcess());

    }

}

