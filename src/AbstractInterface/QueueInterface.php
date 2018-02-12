<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:35
 */

namespace EasySwoole\Spider\AbstractInterface;


use EasySwoole\Spider\Task\TaskObj;

interface QueueInterface
{
    public function lPop():?TaskObj;
    public function rPush(TaskObj $obj);
}