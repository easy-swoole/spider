<?php
/**
 * @CreateTime:   2020/2/16 下午11:02
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  暴露给外部的生产者接口
 */
namespace Easyswoole\Spider\Hole;

use Easyswoole\Spider\Config\ProductResult;

interface ProductInterface
{
    public function product($url):ProductResult;
}
