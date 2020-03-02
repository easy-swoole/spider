<?php
/**
 * @CreateTime:   2020/2/16 下午11:02
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  暴露给外部的消费者接口
 */
namespace EasySwoole\Spider\Hole;

use EasySwoole\JobQueue\JobAbstract;

abstract class ConsumeAbstract extends JobAbstract
{

    public $data;

    abstract public function consume();

    function exec(): bool
    {
        // TODO: Implement exec() method.
        $this->consume();
    }

    function onException(\Throwable $throwable): bool
    {
        // TODO: Implement onException() method.
        return true;
    }

}
