<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:43
 */

namespace EasySwoole\Spider\Task;


use EasySwoole\Core\Component\Trigger;
use EasySwoole\Core\Swoole\Process\AbstractProcess;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Spider\AbstractInterface\AbstractHandler;
use EasySwoole\Spider\AbstractInterface\QueueInterface;
use EasySwoole\Spider\Config;
use EasySwoole\Spider\Utility\Factory;
use Swoole\Process;

class Runner extends AbstractProcess
{

    public function run(Process $process)
    {
        // TODO: Implement run() method.
        $this->addTick(Config::getInstance()->getQueueCheckInterval(),function (){
            //稍后记得处理计数器和暂停逻辑
           $queue = Factory::getInstance()->get(Factory::QUEUE);
           if($queue instanceof QueueInterface){
               $task = $queue->lPop();
               if($task){
                   $handler = $task->getPreCall();
                   //不论预处理是否出现异常，该任务都会被执行，如果有设置预处理，请自己实现try，
                   //若预处理的时候发现该任务不想被执行，请清空Request
                   if(!empty($handler)){
                       try{
                           $ref = new \ReflectionClass($handler['callBackClass']);
                           if($ref->isSubclassOf(AbstractHandler::class)){
                               $ins = $ref->newInstanceArgs([$task]);
                               $ins->handler($handler['action']);
                           }else{
                               Trigger::error("{$handler['callBackClass']} class not a handler");
                           }
                       }catch (\Throwable $throwable){
                           Trigger::throwable($throwable);
                       }
                   }

                   $request = $task->getRequest();
                   if($request instanceof Request){
                       $response = $request->exec();
                       $task->setResponse($response);
                       $handler = $task->getCallBack();
                       if(!empty($handler)){
                           try{
                                $ref = new \ReflectionClass($handler['callBackClass']);
                                if($ref->isSubclassOf(AbstractHandler::class)){
                                    $ins = $ref->newInstanceArgs([$task]);
                                    $ins->handler($handler['action']);
                                }else{
                                    Trigger::error("{$handler['callBackClass']} class not a handler");
                                }
                           }catch (\Throwable $throwable){
                               Trigger::throwable($throwable);
                           }
                       }
                   }
               }
           }
        });
    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args)
    {
        // TODO: Implement onReceive() method.
    }
}