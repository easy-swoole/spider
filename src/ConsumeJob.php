<?php
/**
 * @CreateTime:   2020-02-29 20:13
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  消费任务
 */
namespace EasySwoole\Spider;

class ConsumeJob {

    private $data;

    public function setData($data) : ConsumeJob
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}