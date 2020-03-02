<?php
/**
 * @CreateTime:   2020/2/22 下午3:24
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  生产结果
 */

namespace EasySwoole\Spider;

use EasySwoole\Spider\ConsumeJob;
use EasySwoole\Spider\Hole\ProductAbstract;
use EasySwoole\Spider\ProductJob;

class ProductResult
{

    private $productJobs;

    private $consumeData;

    public function setProductJobConfigs(array $productJobs): ProductResult
    {
        $this->productJobs = $productJobs;
        return $this;
    }

    public function getProductJobConfigs()
    {
        return $this->productJobs;
    }

    public function setConsumeData($consumeData): ProductResult
    {
        $this->consumeData = $consumeData;
        return $this;
    }

    public function getConsumeData()
    {
        return $this->consumeData;
    }

}
