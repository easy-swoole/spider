<?php
/**
 * @CreateTime:   2020/2/16 下午11:02
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  暴露给外部的生产者接口
 */
namespace EasySwoole\Spider\Hole;

use EasySwoole\JobQueue\AbstractJob;
use EasySwoole\JobQueue\JobProcess;
use EasySwoole\Spider\Config\ProductConfig;
use EasySwoole\Spider\ProductResult;
use EasySwoole\Spider\ProductJob;
use Swoole\Coroutine;
use EasySwoole\Spider\Config\Config;

abstract class ProductAbstract extends AbstractJob
{

    /**
     * @var $productConfig ProductConfig
     */
    public $productConfig;

    abstract public function init();

    abstract public function product(): ProductResult;

    public function exec(): bool
    {
        // TODO: Implement exec() method.
        $productResult = $this->product();
        $this->productResultDeal($productResult);
        return true;
    }

    private function productResultDeal($productResult)
    {

        if ($productResult instanceof ProductResult) {

            go(function () use($productResult) {
                $productJobConfigs = $productResult->getProductJobConfigs();
                if (!empty($productJobConfigs)) {
                    $config = Config::getInstance();
                    foreach ($productJobConfigs as $productJobConfig) {
                        $productConfig = new ProductConfig($productJobConfig);
                        $config->getProduct()->productConfig = $productConfig;
                        $config->getQueue()->push($config->getProduct());
                    }
                }
            });

            go(function () use($productResult) {
                $consumeData = $productResult->getConsumeData();
                if (!empty($consumeData)) {
                    $config = Config::getInstance();
                    $config->getConsume()->data = $consumeData;
                    $config->getQueue()
                        ->push($config->getConsume());
                }
            });
        }

    }

    function onException(\Throwable $throwable): bool
    {
        // TODO: Implement onException() method.

        return true;
    }

}
