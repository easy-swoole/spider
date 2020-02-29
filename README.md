## EasySwoole Spider

EasySwoole-Spider 可以方便用户快速搭建分布式多协程爬虫。

## 快速使用
以百度搜索为例，根据搜索关键词找出爬出每个关键词检索结果前三页的特定数据
`纯属教学目的，如有冒犯贵公司还请及时通知，会及时调整`

#### Product

```php
<?php
namespace App\Spider;

use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Spider\ConsumeJob;
use EasySwoole\Spider\Hole\ProductAbstract;
use EasySwoole\Spider\ProductResult;
use EasySwoole\Spider\ProductJob;
use QL\QueryList;

class ProductTest extends ProductAbstract
{

    private const SEARCH_WORDS = 'SEARCH_WORDS';

    public function init()
    {
        // TODO: Implement init() method.
        $words = [
            'php',
            'java',
            'go'
        ];
        foreach ($words as $word) {
            $this->config->getQueue()->push(self::SEARCH_WORDS, $word);
        }

        $wd = $this->config->getQueue()->pop(self::SEARCH_WORDS);

        $productJob = new ProductJob();
        $productJob->setUrl("https://www.baidu.com/s?wd={$wd}&pn=0");
        $productJob->setOtherInfo(['page'=>0, 'word'=>$wd]);
        $this->firstProductJob = $productJob;
    }

    public function product(ProductJob $productJob):ProductResult
    {
        // TODO: Implement product() method.
        // 请求地址数据
        $httpClient = new HttpClient($productJob->getUrl());
        $httpClient->setHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36');
        $body = $httpClient->get()->getBody();

        // 先将每个搜索结果的a标签内容拿到
        $rules = [
            'search_result' => ['.c-container .t', 'text', 'a']
        ];
        $searchResult = QueryList::rules($rules)->html($body)->query()->getData();

        $data = [];
        foreach ($searchResult as $result) {
            $item = [
                'href' => QueryList::html($result['search_result'])->find('a')->attr('href'),
                'text' => QueryList::html($result['search_result'])->find('a')->text()
            ];
            $data[] = $item;
        }

        $productJobOtherInfo = $productJob->getOtherInfo();

        // 下一个任务
        $nextProductJob = new ProductJob();
        if ($productJobOtherInfo['page'] === 3) {
            $word = $this->config->getQueue()->pop(self::SEARCH_WORDS);
            $pn = 0;
            $nextOtherInfo = [
                'page' => 0,
                'word' => $word
            ];
        } else {
            $word = $productJobOtherInfo['word'];
            $pn = $productJobOtherInfo['page']*10;
            $nextOtherInfo = [
                'page' => ++$productJobOtherInfo['page'],
                'word' => $word
            ];
        }

        $nextProductJob->setUrl("https://www.baidu.com/s?wd={$word}&pn={$pn}");
        $nextProductJob->setOtherInfo($nextOtherInfo);

        // 消费任务
        $consumeJob = new ConsumeJob();
        $consumeJob->setData($data);

        $result = new ProductResult();
        $result->setProductJob($nextProductJob)->setConsumeJob($consumeJob);
        return $result;
    }

}
```

### Consume

我这里直接存文件了，可按照需求自己定制

```php
namespace App\Spider;

use EasySwoole\Spider\ConsumeJob;
use EasySwoole\Spider\Hole\ConsumeAbstract;

class ConsumeTest extends ConsumeAbstract
{

    public function consume(ConsumeJob $consumeJob)
    {
        // TODO: Implement consume() method.
        $data = $consumeJob->getData();

        $items = '';
        foreach ($data as $item) {
            $items .= implode("\t", $item)."\n";
        }

        file_put_contents('baidu.txt', $items);
    }
}
```

### 注册爬虫组件

```php
public static function mainServerCreate(EventRegister $register)
{
    $config = Config::getInstance()
        ->setProduct(new ProductTest())
        ->setConsume(new ConsumeTest())
        ->setProductCoroutineNum(1)
        ->setConsumeCoroutineNum(1);
    Spider::getInstance()
        ->setConfig($config)
        ->attachProcess(ServerManager::getInstance()->getSwooleServer());
}
```

## 配置

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

