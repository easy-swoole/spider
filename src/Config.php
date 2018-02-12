<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:46
 */

namespace EasySwoole\Spider;


use EasySwoole\Core\AbstractInterface\Singleton;

class Config
{
    use Singleton;

    protected $queueCheckInterval = 1000;
    /**
     * @return int
     */
    public function getQueueCheckInterval(): int
    {
        return $this->queueCheckInterval;
    }

    /**
     * @param int $queueCheckInterval
     */
    public function setQueueCheckInterval(int $queueCheckInterval): void
    {
        $this->queueCheckInterval = $queueCheckInterval;
    }
}