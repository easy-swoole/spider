<?php
/**
 * @CreateTime:   2020/3/8 下午4:18
 * @Author:       huizhang  <tuzisir@163.com>
 * @Copyright:    copyright(2020) Easyswoole all rights reserved
 * @Description:  爬虫客户端
 */
namespace EasySwoole\Spider;

use EasySwoole\Component\Singleton;
use EasySwoole\Spider\Config\SpiderConfig;
use EasySwoole\Spider\Config\ProductConfig;
use EasySwoole\Spider\Hole\ProductAbstract;

class SpiderClient
{
    use Singleton;

    /**
     * @var SpiderConfig
     */
    private $spiderConfig;

    public function __construct()
    {
        $this->spiderConfig = SpiderServer::getInstance()->getSpiderConfig();
    }

    public function addJob($url, $otherInfo)
    {
        $this->pushJob($url, $otherInfo);
    }

    public function addJobs(array $jobsConfig)
    {
        foreach ($jobsConfig as $jobConfig) {
            [$url, $otherInfo] = $jobConfig;
            $this->pushJob($url, $otherInfo);
        }
    }

    private function pushJob($url, $otherInfo)
    {
        $productConfig = new ProductConfig(
            [
                'url' => $url,
                'otherInfo' => $otherInfo
            ]
        );

        /** @var $productJob ProductAbstract*/
        $product = $this->spiderConfig->getProduct();
        $productJob = new $product();
        $productJob->productConfig = $productConfig;
        $this->spiderConfig
            ->getJobQueue()
            ->producer()
            ->push($productJob);
    }

}
