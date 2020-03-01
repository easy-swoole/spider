<?php
/**
 * @CreateTime:   2020-03-01 20:15
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  每个product任务的配置
 */
namespace EasySwoole\Spider\Config;

use EasySwoole\Spl\SplBean;

class ProductConfig extends SplBean
{

    protected $url;

    protected $otherInfo;

    public function getUrl()
    {
        return $this->url;
    }

    public function getOtherInfo()
    {
        return $this->otherInfo;
    }
}