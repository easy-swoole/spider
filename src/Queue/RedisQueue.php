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
use EasySwoole\RedisPool\Redis;
use EasySwoole\JobQueue\JobQueueInterface;
use EasySwoole\JobQueue\JobAbstract;

class RedisQueue implements JobQueueInterface
{

    use Singleton;

    function push($key, JobAbstract $job): bool
    {
        $res = $redis = Redis::defer($key);
        $res = $redis->lPush($key, serialize($job));
        if (empty($res)) {
            return false;
        }
        return true;
    }

    function pop($key): ?JobAbstract
    {
        $redis = Redis::defer($key);
        $job = $redis->lPop($key);
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
