<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:34
 */

namespace EasySwoole\Spider\Utility;


use EasySwoole\Core\AbstractInterface\Singleton;
use EasySwoole\Core\Component\Container;

class Factory extends Container
{
    const QUEUE = 'QUEUE';

    use Singleton;
}