<?php
/**
 * @CreateTime:   2020/2/22 下午3:24
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  生产结果
 */

namespace EasySwoole\Spider;

use EasySwoole\Spider\ConsumeJob;
use EasySwoole\Spider\ProductJob;

class ProductResult
{

    private $productJob;

    private $consumeJob;

    public function setProductJob(ProductJob $productJob): ProductResult
    {
        $this->productJob = $productJob;
        return $this;
    }

    public function getProductJob() : ProductJob
    {
        return $this->productJob;
    }

    public function setConsumeJob(ConsumeJob $consumJob): ProductResult
    {
        $this->consumeJob = $consumJob;
        return $this;
    }

    public function getConsumeJob() : ConsumeJob
    {
        return $this->consumeJob;
    }

}
