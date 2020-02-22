## EasySwoole Spider

EasySwoole-Spider 可以方便用户快速搭建分布式多协程爬虫。

## 快速使用

#### Product

实现ProductInterface接口

```php
namespace App\Spider;

use EasySwoole\HttpClient\HttpClient;
use Easyswoole\Spider\Hole\ProductInterface;
use Easyswoole\Spider\Spider;
use Easyswoole\Spider\Config\ProductResult;

class ProductTest implements ProductInterface
{

    public function product($url):ProductResult
    {
        // TODO: Implement product() method.
        // 通过http协程客户端拿到地址内容
        $httpClient = new HttpClient($url);
        $body = $httpClient->get()->getBody();
        // 可以借助第三方dom解析，如Querylist
        $nextUrl = 'xxx';
        $data = 'xxx';
        $result = new ProductResult();
        return $result->setNexturl($nextUrl)->setConsumeData($data);
    }
}
```

### Consume

实现ConsumeInterface接口

```php
namespace App\Spider;

use Easyswoole\Spider\Hole\ConsumeInterface;

class ConsumeTest implements ConsumeInterface
{

    public function consume($data)
    {
        // TODO: Implement consume() method.
        $urls = '';
        foreach ($data as $item) {
            if (!empty($item)) {
                $urls .= $item."\n";
            }
        }
        file_put_contents('xx.txt', $urls);
    }
}
```

### 注册爬虫组件

```php
public static function mainServerCreate(EventRegister $register)
{
    // TODO: Implement mainServerCreate() method.
    $config = Config::getInstance()
            ->setStartUrl('https://www.doutula.com/article/detail/1704295') // 爬虫开始地址
            ->setProduct(new ProductTest()) // 设置生产端
            ->setConsume(new ConsumeTest()) // 设置消费端
            ->setProductCoroutineNum(1) // 生产端协程数
            ->setConsumeCoroutineNum(1); // 消费端协程数

    Spider::getInstance()
        ->setConfig($config)
        ->attachProcess(ServerManager::getInstance()->getSwooleServer());
}
```

## 配置

爬虫开始地址
```php
    public function setStartUrl($startUrl): Config
```

设置生产端
```php
    public function setProduct(ProductInterface $product): Config
```

设置消费端
```php
    public function setConsume(ConsumeInterface $consume): Config
```

设置队列类型
```php
    public function setQueueType($queueType): Config
```

设置自定义队列
```php
    public function setQueue($queue): Config
```

设置生产端协程数
```php
    public function setProductCoroutineNum($productCoroutineNum): Config
```

设置消费端协程数
```php
    public function setConsumeCoroutineNum($consumeCoroutineNum): Config
```

设置生产队列key,默认Easyswoole-product
```php
    public function setProductQueueKey($productQueueKey): Config
```

设置消费队列key,默认Easyswoole-consume
```php
    public function setConsumeQueueKey($consumeQueueKey): Config
```

分布式时指定某台机器为开始机
```php
    public function setMainHost($mainHost): Config
```

