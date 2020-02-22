<?php
/**
 * @CreateTime:   2020/2/22 下午2:55
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  基于redis-pool
 */
namespace Easyswoole\Spider\Queue;

use EasySwoole\RedisPool\Redis;
use Easyswoole\Spider\Hole\QueueInterface;

class RedisQueue implements QueueInterface
{

    public const REDIS_ALIAS='Easyswoole-redis';

    public function push($key, $value)
    {
        // TODO: Implement push() method.
        $redis = Redis::defer(self::REDIS_ALIAS);
        $redis->lPush($key, $value);
    }

    public function pop($key)
    {
        // TODO: Implement pop() method.
        $redis = Redis::defer(self::REDIS_ALIAS);
        return $redis->lPop($key);
    }

}
