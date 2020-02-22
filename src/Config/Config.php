<?php
/**
 * @CreateTime:   2020/2/16 下午10:28
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫配置
 */
namespace EasySwoole\Spider\Config;

use EasySwoole\Component\Singleton;
use EasySwoole\Spider\Hole\ConsumeInterface;
use EasySwoole\Spider\Hole\ProductInterface;
use EasySwoole\Spider\Hole\QueueInterface;

class Config
{

    use Singleton;

    // 爬虫开始地址
    protected $startUrl;

    // 用户自定义生产者
    protected $product;

    // 用户自定义消费者
    protected $consume;

    // 通信队列类型
    protected $queueType=1;

    // 队列
    protected $queue;

    // 生产者协程数
    protected $productCoroutineNum=3;

    // 消费者协程数
    protected $consumeCoroutineNum=3;

    // 生产者队列key(也就是爬出来的地址)
    protected $productQueueKey='Easyswoole-product';

    // 消费者队列key(爬出来的内容)
    protected $consumeQueueKey='Easyswoole-consume';

    // 分布式时指定一台机器为开始机器
    protected $mainHost;

    // 队列配置
    protected $queueConfig;

    public const QUEUE_TYPE_FAST_CACHE = 1;
    public const QUEUE_TYPE_REDIS = 2;
    public const QUEUE_TYPE_RABBITMQ = 3;
    public const QUEUE_TYPE_KAFKA = 4;

    /**
     * @return mixed
     */
    public function getStartUrl()
    {
        return $this->startUrl;
    }

    /**
     * @param mixed $startUrl
     * @return Config
     */
    public function setStartUrl($startUrl): Config
    {
        $this->startUrl = $startUrl;
        return $this;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct():ProductInterface
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     * @return Config
     */
    public function setProduct(ProductInterface $product): Config
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return ConsumeInterface
     */
    public function getConsume():ConsumeInterface
    {
        return $this->consume;
    }

    /**
     * @param ConsumeInterface $consume
     * @return Config
     */
    public function setConsume(ConsumeInterface $consume): Config
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
     * @return Config
     */
    public function setQueueType($queueType): Config
    {
        $this->queueType = $queueType;
        return $this;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue():QueueInterface
    {
        return $this->queue;
    }

    /**
     * @param mixed $queue
     * @return Config
     */
    public function setQueue($queue): Config
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductCoroutineNum()
    {
        return $this->productCoroutineNum;
    }

    /**
     * @param mixed $productCoroutineNum
     * @return Config
     */
    public function setProductCoroutineNum($productCoroutineNum): Config
    {
        $this->productCoroutineNum = $productCoroutineNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConsumeCoroutineNum()
    {
        return $this->consumeCoroutineNum;
    }

    /**
     * @param mixed $consumeCoroutineNum
     * @return Config
     */
    public function setConsumeCoroutineNum($consumeCoroutineNum): Config
    {
        $this->consumeCoroutineNum = $consumeCoroutineNum;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProductQueueKey()
    {
        return $this->productQueueKey;
    }

    /**
     * @param mixed $productQueueKey
     * @return Config
     */
    public function setProductQueueKey($productQueueKey): Config
    {
        $this->productQueueKey = $productQueueKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConsumeQueueKey()
    {
        return $this->consumeQueueKey;
    }

    /**
     * @param mixed $consumeQueueKey
     * @return Config
     */
    public function setConsumeQueueKey($consumeQueueKey): Config
    {
        $this->consumeQueueKey = $consumeQueueKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMainHost()
    {
        return $this->mainHost;
    }

    /**
     * @param mixed $mainHost
     * @return Config
     */
    public function setMainHost($mainHost): Config
    {
        $this->mainHost = $mainHost;
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
     * @return Config
     */
    public function setQueueConfig($queueConfig): Config
    {
        $this->queueConfig = $queueConfig;
        return $this;
    }
}
