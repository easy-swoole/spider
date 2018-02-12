<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午8:11
 */

namespace EasySwoole\Spider\Task;



use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Core\Utility\Curl\Response;

class TaskObj
{
    private $request;
    private $callBack = null;
    private $preCall = null;
    private $args = [];
    private $response = null;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest():?Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return null
     */
    public function getCallBack()
    {
        return $this->callBack;
    }

    /*
     * 设置静态类方法
     */
    public function setCallBack(string $callBackClass,string $action)
    {
        $this->callBack = [
            'callBackClass'=>$callBackClass,
            'action'=>$action
        ];
    }

    /**
     * @return null
     */
    public function getPreCall()
    {
        return $this->preCall;
    }

    public function setPreCall( string $callBackClass,string $action): void
    {
        $this->preCall = [
            'callBackClass'=>$callBackClass,
            'action'=>$action
        ];
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function setArgs(array $args)
    {
        $this->args = $args;
    }

    public function getArg($key)
    {
        if(isset($this->args[$key])){
            return $this->args[$key];
        }
        return null;
    }


    /**
     * @return Response
     */
    public function getResponse():?Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

}