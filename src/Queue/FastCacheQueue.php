<?php
/**
 * @CreateTime:   2020/2/22 下午2:55
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫组件默认fastcache为通信队列
 */
namespace Easyswoole\Spider\Queue;

use EasySwoole\FastCache\Cache;
use Easyswoole\Spider\Hole\QueueInterface;

class FastCacheQueue implements QueueInterface
{

    public function push($key, $value)
    {
        // TODO: Implement push() method.
        Cache::getInstance()->enQueue($key,$value);
    }

    public function pop($key)
    {
        // TODO: Implement pop() method.
        return Cache::getInstance()->deQueue($key);
    }

}
