<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:18
 */

namespace EasySwoole\Spider\AbstractInterface;


use EasySwoole\Core\Component\Trigger;
use EasySwoole\Spider\Task\TaskObj;

abstract class AbstractHandler
{
    private $taskObj;
    private $errorHandler = null;
    const ERROR_ACTION_MISS = 'ERROR_ACTION_MISS';
    const ERROR_ACTION_ERROR = 'ERROR_ACTION_ERROR';
    /**
     * @return mixed
     */
    private function getErrorHandler()
    {
        return $this->errorHandler;
    }

    /**
     * @param callable $errorHandler
     */
    public function setErrorHandler(callable $errorHandler): void
    {
        $this->errorHandler = $errorHandler;
    }

    function __construct(TaskObj $taskObj)
    {
        $this->taskObj = $taskObj;
    }

    public function handler($action)
    {
        try{
            $ref = new \ReflectionClass(static::class);
            if($ref->hasMethod($action) && $ref->getMethod( $action)->isPublic()){
                try{
                    $this->$action();
                }catch (\Throwable $exception){
                    if($this->errorHandler != null){
                        $this->hookError(self::ERROR_ACTION_ERROR);
                    }else{
                        Trigger::throwable($exception);
                    }
                }
            }else{
                if($this->errorHandler != null){
                    $this->hookError(self::ERROR_ACTION_MISS);
                }else{
                    Trigger::error("action {$action} for class ".static::class.' not exist' );
                }
            }
        }catch (\Throwable $exception){
            Trigger::throwable($exception);
        }
    }

    private function hookError($errorType)
    {
        if(is_callable($this->errorHandler)){
            try{
                call_user_func($this->errorHandler,$this->taskObj,$errorType);
            }catch (\Throwable $exception){
                Trigger::throwable($exception);
            }
        }
    }

    /**
     * @return TaskObj
     */
    public function getTaskObj(): TaskObj
    {
        return $this->taskObj;
    }

    /**
     * @param TaskObj $taskObj
     */
    public function setTaskObj(TaskObj $taskObj): void
    {
        $this->taskObj = $taskObj;
    }

}