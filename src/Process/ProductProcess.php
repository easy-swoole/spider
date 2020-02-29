<?php
/**
 * @CreateTime:   2020/2/16 下午10:44
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  生产者进程
 */
namespace EasySwoole\Spider\Process;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\Spider\Config\Config;
use EasySwoole\Spider\ProductResult;
use Swoole\Coroutine;
use EasySwoole\Spider\ProductJob;
use EasySwoole\Spider\Exception\SpiderException;

class ProductProcess extends AbstractProcess
{

    protected function run($arg)
    {
        go(function (){

            $config = Config::getInstance();

            $config->getProduct()->config = $config;

            $config->getProduct()->init();

            $this->setFirstProductJob();

            for ($i=0;$i<$config->getProductCoroutineNum();$i++) {

                go(function () use ($config){

                    while (true) {

                        $productJob = $config->getQueue()->pop($config->getProductQueueKey());
                        $productJob = unserialize($productJob);

                        if ($productJob instanceof ProductJob) {
                            $productResult = $config->getProduct()->product($productJob);
                            $this->productResultDeal($productResult);
                        } else {
                            throw new SpiderException('ProductJob type error!');
                        }

                    }
                });
            }

        });
    }

    private function setFirstProductJob()
    {
        $config = Config::getInstance();
        $mainHost = $config->getMainHost();
        $firstJob = $config->getProduct()->firstProductJob;
        if (empty($mainHost)) {
            if (!empty($firstJob->getUrl())) {
                $config->getQueue()->push($config->getProductQueueKey(), serialize($firstJob));
            } else {
                throw new SpiderException('FirstJob url error!');
            }
        } else {
            $ip = gethostbyname(gethostname());
            if (!empty($ip) && $config->getMainHost() === $ip) {
                if (!empty($firstJob->getUrl())) {
                    $config->getQueue()->push($config->getProductQueueKey(), serialize($firstJob));
                } else {
                    throw new SpiderException('FirstJob url error!');
                }
            }
        }
    }

    private function productResultDeal($productResult)
    {
        $config = Config::getInstance();
        if ($productResult instanceof ProductResult) {

            $productJob = $productResult->getProductJob();
            if (!empty($productJob->getUrl())) {
                Config::getInstance()->getQueue()->push($config->getProductQueueKey(), serialize($productJob));
            }

            $consumeJob = $productResult->getConsumeJob();
            if (!empty($consumeJob->getData())) {
                Config::getInstance()->getQueue()
                    ->push($config->getConsumeQueueKey(), serialize($consumeJob));
            }
        }
    }
}
