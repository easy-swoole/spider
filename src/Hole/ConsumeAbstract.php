<?php
/**
 * @CreateTime:   2020/2/16 下午11:02
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  暴露给外部的消费者接口
 */
namespace EasySwoole\Spider\Hole;

use EasySwoole\Queue\Job;

abstract class ConsumeAbstract extends Job
{

    abstract public function consume();

}
