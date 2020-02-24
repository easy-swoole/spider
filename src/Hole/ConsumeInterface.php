<?php
/**
 * @CreateTime:   2020/2/16 下午11:02
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  暴露给外部的消费者接口
 */
namespace EasySwoole\Spider\Hole;

interface ConsumeInterface
{
    public function consume($data);
}
