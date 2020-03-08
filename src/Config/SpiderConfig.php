<?php
/**
 * @CreateTime:   2020/2/16 下午10:28
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫配置
 */
namespace EasySwoole\Spider\Config;

use EasySwoole\Queue\Queue;
use EasySwoole\Queue\QueueDriverInterface;
use EasySwoole\Spider\Hole\ConsumeAbstract;
use EasySwoole\Spl\SplBean;

class SpiderConfig extends SplBean
{

    // 用户自定义生产者
    protected $product;

    // 用户自定义消费者
    protected $consume;

    // 通信队列类型
    protected $queueType=1;

    // 队列
    protected $queue;

    // 队列配置
    protected $queueConfig;

    // jobQueue
    protected $jobQueue;

    // jobQueue 最大协程并发数
    protected $maxCurrency=128;

    public const QUEUE_TYPE_FAST_CACHE = 1;
    public const QUEUE_TYPE_REDIS = 2;

    public function getProduct()
    {
        return $this->product;
    }

    public function setMaxCurrency(int $maxCurrency): SpiderConfig
    {
        $this->maxCurrency = $maxCurrency;
        return $this;
    }

    public function getMaxCurrency():int
    {
        return $this->maxCurrency;
    }

    public function setProduct($product): SpiderConfig
    {
        $this->product = $product;
        return $this;
    }

    public function getConsume()
    {
        return $this->consume;
    }

    /**
     * @param ConsumeAbstract $consume
     * @return SpiderConfig
     */
    public function setConsume(ConsumeAbstract $consume): SpiderConfig
    {
        $this->consume = $consume;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueueType()
    {
        return $this->queueType;
    }

    /**
     * @param mixed $queueType
     * @return SpiderConfig
     */
    public function setQueueType($queueType): SpiderConfig
    {
        $this->queueType = $queueType;
        return $this;
    }

    /**
     * @return QueueDriverInterface
     */
    public function getQueue():QueueDriverInterface
    {
        return $this->queue;
    }

    /**
     * @param QueueDriverInterface $queue
     * @return SpiderConfig
     */
    public function setQueue(QueueDriverInterface $queue): SpiderConfig
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueueConfig()
    {
        return $this->queueConfig;
    }

    /**
     * @param mixed $queueConfig
     * @return SpiderConfig
     */
    public function setQueueConfig($queueConfig): SpiderConfig
    {
        $this->queueConfig = $queueConfig;
        return $this;
    }

    /**
     * @return Queue
     */
    public function getJobQueue() : Queue
    {
        return $this->jobQueue;
    }

    /**
     * @param Queue $jobQueue
     * @return SpiderConfig
     */
    public function setJobQueue(Queue $jobQueue): SpiderConfig
    {
        $this->jobQueue = $jobQueue;
        return $this;
    }
}
