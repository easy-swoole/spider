<?php
/**
 * @CreateTime:   2020-02-29 19:59
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  生产任务
 */
namespace EasySwoole\Spider;

class ProductJob
{

    private $url;

    private $otherInfo;

    public function setUrl($url) : ProductJob
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setOtherInfo($otherInfo) : ProductJob
    {
        $this->otherInfo = $otherInfo;
        return $this;
    }

    public function getOtherInfo()
    {
        return $this->otherInfo;
    }
}