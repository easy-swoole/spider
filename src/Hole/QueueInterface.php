<?php
/**
 * @CreateTime:   2020/2/16 下午11:37
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  暴露给外部的队列接口
 */
namespace EasySwoole\Spider\Hole;

interface QueueInterface
{
    public function push($key, $value);

    public function pop($key);

}
