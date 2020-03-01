<?php
/**
 * @CreateTime:   2020/2/22 下午2:55
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫组件默认fastcache为通信队列
 */
namespace EasySwoole\Spider\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\FastCache\Cache;
use EasySwoole\JobQueue\JobAbstract;
use EasySwoole\JobQueue\JobQueueInterface;

class FastCacheQueue implements JobQueueInterface
{

    use Singleton;

    function pop($key): ?JobAbstract
    {
        // TODO: Implement pop() method.
        $job =  Cache::getInstance()->deQueue($key);
        if (empty($job)) {
            return null;
        }
        $job = unserialize($job);
        if (empty($job)) {
            return null;
        }
        return $job;
    }

    function push($key, JobAbstract $job): bool
    {
        // TODO: Implement push() method.
        $res = Cache::getInstance()->enQueue($key, serialize($job));
        if (empty($res)) {
            return false;
        }
        return true;
    }

}
