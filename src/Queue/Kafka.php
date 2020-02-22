<?php
/**
 * @CreateTime:   2020/2/22 下午4:04
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  kafka
 */
namespace EasySwoole\Spider\Queue;

use EasySwoole\Spider\Hole\QueueInterface;

class Kafka implements QueueInterface
{

    public function push($key, $value)
    {
        // TODO: Implement push() method.
    }

    public function pop($key)
    {
        // TODO: Implement pop() method.
    }
}
