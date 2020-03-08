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
        $data = $this->getJobData();

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
        $spiderConfig = [
            'product' => ProductTest::class, // 必须
            'consume' => ConsumeTest::class, // 必须
            'queueType' => SpiderConfig::QUEUE_TYPE_FAST_CACHE,
            'queue' => '自定义队列，如使用组件自带则不需要',
            'queueConfig' => '自定义队列配置，目前只有SpiderConfig::QUEUE_TYPE_REDIS需要',
            'maxCurrency' => 128 // 最大协程并发数
        ];
        SpiderServer::getInstance()
            ->setSpiderConfig($spiderConfig)
            ->attachProcess(ServerManager::getInstance()->getSwooleServer());
}
```

### 投递任务
````php
$words = [
    'php',
    'java',
    'go'
];

foreach ($words as $word) {
    Cache::getInstance()->enQueue('SEARCH_WORDS', $word);
}

$wd = Cache::getInstance()->deQueue('SEARCH_WORDS');

SpiderClient::getInstance()->addJob(
                'https://www.baidu.com/s?wd=php&pn=0',
                [
                    'page' => 1,
                    'word' => $wd
                ]
);
````
