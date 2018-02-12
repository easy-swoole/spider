# EasySwoole Spider
方便用户快速搭建一个多进程爬虫，若有集群需求，请实现QueueInterface，例如利用redis队列，可以非常快速的实现
一个分布式的爬虫。
## 例子
以爬取京东商品为例子
#### 创建一个京东的处理器
```
namespace App\Handler;


use EasySwoole\Core\Component\Spl\SplString;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Spider\AbstractInterface\AbstractHandler;
use EasySwoole\Spider\Task\TaskObj;
use EasySwoole\Spider\Utility\Factory;

class Jd extends AbstractHandler
{
    function search()
    {
        //本例子借助simple html dom解析，请自己引入
        $content = $this->getTaskObj()->getResponse()->getBody();
        $html = new \simple_html_dom();
        $html->load($content);
        $all = [];
        foreach ($html->find('li[class=gl-item]') as $item){
            $all[] = 'http:'.$item->find('a',0)->href;
        }
        var_dump('all goods is '.json_encode($all));
        //再次创建一个新任务
        $task = new TaskObj(new Request($all[0]));
        $task->setCallBack(self::class,'detail');
        $task->setArgs([
            'addTime'=>time()
        ]);
        $task->setPreCall(self::class,'preCall');
        Factory::getInstance()->get(Factory::QUEUE)->rPush($task);
    }

    function detail()
    {
        $content = $this->getTaskObj()->getResponse()->getBody();
        $html = new \simple_html_dom();
        $html->load($content);
        var_dump($html->find('div[class=sku-name]',0)->innertext);
        var_dump($this->getTaskObj()->getArg('addTime'));
    }

    function preCall()
    {
        var_dump('you can do some preCall here,for example you can add a proxy to your request');
    }
}
```

#### 建立测试投递任务的控制器
```
namespace App\HttpController;


use App\Handler\Jd;
use EasySwoole\Core\Http\AbstractInterface\Controller;
use EasySwoole\Core\Utility\Curl\Request;
use EasySwoole\Spider\Task\TaskObj;
use EasySwoole\Spider\Utility\Factory;

class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $task = new TaskObj(new Request('https://search.jd.com/Search?keyword=iPhone&enc=utf-8&wq=iPhone'));
        $task->setCallBack(Jd::class,'search');
        Factory::getInstance()->get(Factory::QUEUE)->rPush($task);
        $this->response()->write('task add !');
    }
}
```

#### 注册爬虫服务
```
public function mainServerCreate(ServerManager $server,EventRegister $register): void
{
    // TODO: Implement mainServerCreate() method.
    Spider::getInstance(false,function (){
            Factory::getInstance()->set(Factory::QUEUE,new CacheQueue());
    })->run(2);
}
```


## 流程解释
你可以在任意位置投递一个任务，该任务可能指定了该任务执行完CURL后的需要执行的回调。而Spider的runner会定时去队列中获取任务回调执行。
其余的，代码很简单，可以自己阅读，没有多少代码。

本项目依赖于[easySwoole](https://www.easyswoole.com/)

如果您有任何的建议或者更好想法，可以直接联系 291323003@qq.com

官方交流群：633921431

## 捐赠

您的捐赠是对Swoole项目开发组最大的鼓励和支持。我们会坚持开发维护下去。 您的捐赠将被用于：

    持续和深入地开发
    文档和社区的建设和维护

[捐赠链接](https://www.easyswoole.com/Manual/2.x/Cn/_book/donate.html)
 

