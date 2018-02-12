<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/12
 * Time: 上午11:01
 */

namespace EasySwoole\Spider\Utility;


use EasySwoole\Core\Component\Cache\Cache;
use EasySwoole\Spider\AbstractInterface\QueueInterface;
use EasySwoole\Spider\Task\TaskObj;

class CacheQueue implements QueueInterface
{

    public function lPop(): ?TaskObj
    {
        // TODO: Implement lPop() method.
        $data = Cache::getInstance()->deQueue('__spider');
        if($data instanceof TaskObj){
            return $data;
        }else{
            return null;
        }
    }

    public function rPush(TaskObj $obj)
    {
        // TODO: Implement rPush() method.
        Cache::getInstance()->enQueue('__spider',$obj);
    }
}