## EasySwoole Spider

EasySwoole-Spider 可以方便用户快速搭建分布式多协程爬虫。

## 快速使用
以百度搜索为例，根据搜索关键词找出爬出每个关键词检索结果前几页的特定数据
`纯属教学目的，如有冒犯贵公司还请及时通知，会及时调整`

#### Product

```php
<?php
namespace App\Spider;

use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Spider\Config\ProductConfig;
use EasySwoole\Spider\Hole\ProductAbstract;
use EasySwoole\Spider\ProductResult;
use QL\QueryList;
use EasySwoole\FastCache\Cache;

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
            Cache::getInstance()->enQueue(self::SEARCH_WORDS, $word);
        }

        $wd = Cache::getInstance()->deQueue(self::SEARCH_WORDS);

        return [
            'url' => "https://www.baidu.com/s?wd={$wd}&pn=0",
            'otherInfo' => [
                'page' => 1,
                'word' => $wd
            ]
        ];
    }

    public function product():ProductResult
    {
        // TODO: Implement product() method.
        // 请求地址数据
        $httpClient = new HttpClient($this->productConfig->getUrl());
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

        $productJobOtherInfo = $this->productConfig->getOtherInfo();

        // 下一批任务
        $productJobConfigs = [];
        if ($productJobOtherInfo['page'] === 1) {
            for($i=1;$i<5;$i++) {
                $pn = $i*10;
                $productJobConfig = [
                    'url' => "https://www.baidu.com/s?wd={$productJobOtherInfo['word']}&pn={$pn}",
                    'otherInfo' => [
                        'word' => $productJobOtherInfo['word'],
                        'page' => $i+1
                    ]
                ];
                $productJobConfigs[] = $productJobConfig;
            }

            $word = Cache::getInstance()->deQueue(self::SEARCH_WORDS);
            if (!empty($word)) {
                $productJobConfigs[] = [
                    'url' => "https://www.baidu.com/s?wd={$word}&pn=0",
                    'otherInfo' => [
                        'word' => $word,
                        'page' => 1
                    ]
                ];
            }

        }

        $result = new ProductResult();
        $result->setProductJobConfigs($productJobConfigs)->setConsumeData($data);
        return $result;
    }

}
```

### Consume

我这里直接存文件了，可按照需求自己定制

```php
<?php
namespace App\Spider;

use EasySwoole\Spider\ConsumeJob;
use EasySwoole\Spider\Hole\ConsumeAbstract;

class ConsumeTest extends ConsumeAbstract
{

    public function consume()
    {
        // TODO: Implement consume() method.
        $data = $this->data;

        $items = '';
        foreach ($data as $item) {
            $items .= implode("\t", $item)."\n";
        }

        file_put_contents('baidu.txt', $items, FILE_APPEND);
    }
}
```

### 注册爬虫组件

```php
public static function mainServerCreate(EventRegister $register)
{
    $config = Config::getInstance()
        ->setProduct(new ProductTest())
        ->setConsume(new ConsumeTest());
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

分布式时指定某台机器为开始机
```php
    public function setMainHost($mainHost): Config
```

设置自定义队列配置(现在只有redis-pool需要这个方法)

```php
    public function setJobQueueKey($jobQueueKey): Config
```

最大可运行任务数
```php
    public function setMaxCurrency($maxCurrency): Config
```
