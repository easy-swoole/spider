<?php
/**
 * @CreateTime:   2020/2/22 下午3:24
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  生产结果
 */
namespace EasySwoole\Spider\Config;

class ProductResult
{

    protected $nexturl;

    protected $nextUrls;

    protected $consumeData;

    /**
     * @return mixed
     */
    public function getNextUrls()
    {
        return $this->nextUrls;
    }

    /**
     * @param mixed $nextUrls
     */
    public function setNextUrls($nextUrls): void
    {
        $this->nextUrls = $nextUrls;
    }

    /**
     * @return mixed
     */
    public function getConsumeData()
    {
        return $this->consumeData;
    }

    /**
     * @param mixed $consumeData
     * @return ProductResult
     */
    public function setConsumeData($consumeData): ProductResult
    {
        $this->consumeData = $consumeData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNexturl()
    {
        return $this->nexturl;
    }

    /**
     * @param mixed $nexturl
     * @return ProductResult
     */
    public function setNexturl($nexturl): ProductResult
    {
        $this->nexturl = $nexturl;
        return $this;
    }
}
