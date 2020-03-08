<?php
/**
 * @CreateTime:   2020/2/22 下午2:55
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫组件默认fastcache为通信队列
 */
namespace EasySwoole\Spider\Queue;

use EasySwoole\FastCache\Cache;
use EasySwoole\Queue\QueueDriverInterface;
use EasySwoole\Queue\Job;

class FastCacheQueue implements QueueDriverInterface
{

    private const FASTCACHE_JOB_QUEUE_KEY='FASTCACHE_JOB_QUEUE_KEY';

    function pop(float $timeout = 3):?Job
    {
        // TODO: Implement pop() method.
        $job =  Cache::getInstance()->deQueue(self::FASTCACHE_JOB_QUEUE_KEY);
        if (empty($job)) {
            return null;
        }
        $job = unserialize($job);
        if (empty($job)) {
            return null;
        }
        return $job;
    }

    function push(Job $job):bool
    {
        // TODO: Implement push() method.
        $res = Cache::getInstance()->enQueue(self::FASTCACHE_JOB_QUEUE_KEY, serialize($job));
        if (empty($res)) {
            return false;
        }
        return true;
    }

    public function size(): ?int
    {
        // TODO: Implement size() method.
    }
}
