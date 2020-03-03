<?php
/**
 * @CreateTime:   2020/2/22 下午2:55
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  基于redis-pool
 */
namespace EasySwoole\Spider\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\FastCache\Cache;
use EasySwoole\JobQueue\AbstractJob;
use EasySwoole\JobQueue\QueueDriverInterface;
use EasySwoole\RedisPool\Redis;

class RedisQueue implements QueueDriverInterface
{

    use Singleton;

    public const REDIS_JOB_QUEUE_KEY='REDIS_JOB_QUEUE_KEY';

    function push(AbstractJob $job):bool
    {
        $redis = Redis::defer(self::REDIS_JOB_QUEUE_KEY);
        $res = $redis->lPush(self::REDIS_JOB_QUEUE_KEY, serialize($job));
        if (empty($res)) {
            return false;
        }
        return true;
    }

    function pop(float $timeout = 3):?AbstractJob
    {
        $redis = Redis::defer(self::REDIS_JOB_QUEUE_KEY);
        $job = $redis->lPop(self::REDIS_JOB_QUEUE_KEY);
        if (empty($job)) {
            return null;
        }
        $job = unserialize($job);
        if (empty($job)) {
            return null;
        }
        return $job;
    }

}
