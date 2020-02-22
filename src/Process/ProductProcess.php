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
use EasySwoole\Spider\Config\ProductResult;
use Swoole\Coroutine;

class ProductProcess extends AbstractProcess
{

    protected function run($arg)
    {
        // TODO: Implement run() method.
        go(function (){

            $config = Config::getInstance();

            // 区分分布式和单机
            $mainHost = $config->getMainHost();
            if (empty($mainHost)) {
                $config->getQueue()->push($config->getProductQueueKey(), $config->getStartUrl());
            } else {
                $ip = gethostbyname(gethostname());
                if (!empty($ip) && $config->getMainHost() === $ip) {
                    $config->getQueue()->push($config->getProductQueueKey(), $config->getStartUrl());
                }
            }

            for ($i=0;$i<$config->getProductCoroutineNum();$i++) {
                go(function () use ($config){
                    while (true) {
                        $url = $config->getQueue()->pop($config->getProductQueueKey());
                        if (empty($url)) {
                            Coroutine::sleep(0.1);
                            continue;
                        }

                        $productResult = $config->getProduct()->product($url);

                        if ($productResult instanceof ProductResult) {

                            $nextUrls = $productResult->getNextUrls();
                            if (!empty($nextUrls)) {
                                foreach ($nextUrls as $nextUrl) {
                                    Config::getInstance()->getQueue()->push($config->getProductQueueKey(), $nextUrl);
                                }
                            }

                            $nextUrl = $productResult->getNexturl();
                            if (!empty($nextUrl)) {
                                Config::getInstance()->getQueue()->push($config->getProductQueueKey(), $nextUrl);
                            }

                            $data = $productResult->getConsumeData();
                            if (!empty($nextUrl)) {
                                Config::getInstance()->getQueue()
                                    ->push($config->getConsumeQueueKey(), json_encode($data, JSON_UNESCAPED_UNICODE));
                            }
                        }
                    }
                });
            }

        });
    }
}
