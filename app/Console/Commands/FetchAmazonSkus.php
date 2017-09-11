<?php

namespace App\Console\Commands;

use App\Events\PriceReduce;
use App\PriceLog;
use App\Sku;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FetchAmazonSkus extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amazon:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从数据库获取亚马逊待抓列表，抓取亚马逊商品价格。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $i = 0;
        do {
            // 取出待抓取的sku集合
            $skus = Sku::getFetchCount0AmazonSkus();
            if ($skus->count() == 0) {
                $skus = Sku::getNeedFetchAmazonSkus();
            }

            if ($skus->count() == 0) {
                echo date("Y-m-d H:i:s")." 没有要抓取的商品。\n";

                return true;
            }

            $skuArr = [];
            foreach ($skus as $sku) {
                array_push($skuArr, $sku->sku);
            }
            $skusStr = urlencode(implode(',', $skuArr));

            $fetchedData = $this->doFetch($skusStr);

            foreach ($skus as $sku) {
                if (isset($fetchedData[$sku->sku])) {
                    $this->saveLog($sku, $fetchedData[$sku->sku]);
                }
            }

            var_dump("数据库查到了".count($skus)."条记录");
            var_dump("抓到了".count($fetchedData)."条数据");
            var_dump($skus->first()->id);
            var_dump($skus->last()->id);

            $i++;
        } while ($i < 26);

        return true;
    }

    /**
     * @param Sku $sku
     * @param array $fetchedData
     *
     * @return bool
     */
    private function saveLog(Sku $sku, $fetchedData)
    {
        $title    = str_replace('?', '？', $fetchedData['title']);
        $newPrice = $fetchedData['price'];
        $rate     = str_replace(',', '', $fetchedData['rate']);
        $img      = $fetchedData['img'];

        // 一些基础信息更进sku表
        $sku->saveInfo($title, $rate, $img);

        // 价格更进log表
        $cacheKey = $sku->source.$sku->sku;
        $lastPrice = Cache::get($cacheKey, 0);
        if ($newPrice != $lastPrice) {
            // 如果价格产生变化，插入数据库，写入缓存
            (new PriceLog())->createSkuPrice($sku->id, $newPrice);

            Cache::forever($cacheKey, $newPrice);
        }

        if ($newPrice < $lastPrice) {
            event(new PriceReduce($sku->sku, $title, $lastPrice, $newPrice));
        }

        echo date("Y-m-d H:i:s")." {$sku->sku} newPrice:{$newPrice} oldPrice:{$lastPrice} rate:{$rate} title:{$title}  {$sku->id}\n";

        return true;
    }

    /**
     * @param $skusStr
     *
     * @return array
     */
    private function doFetch($skusStr)
    {
        $header[] = "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
        $header[] = "Accept-Language:zh-CN,zh;q=0.8,zh-TW;q=0.6,en;q=0.4";
        $header[] = "Cache-Control:max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "DNT: 1";
        $header[] = "Host:www.amazon.cn";
        $header[] = "Upgrade-Insecure-Requests:1";

        $ch      = curl_init();
        $options = array(
            CURLOPT_URL => "https://www.amazon.cn/gp/p13n-shared/faceout-partial?featureId=SimilaritiesCarousel&reftagPrefix=pd_sim_14&widgetTemplateClass=PI%3A%3ASimilarities%3A%3AViewTemplates%3A%3ACarousel%3A%3ADesktop&imageHeight=160&faceoutTemplateClass=PI%3A%3AP13N%3A%3AViewTemplates%3A%3AProduct%3A%3ADesktop%3A%3ACarouselFaceout&auiDeviceType=desktop&imageWidth=160&schemaVersion=2&productDetailsTemplateClass=PI%3A%3AP13N%3A%3AViewTemplates%3A%3AProductDetails%3A%3ADesktop%3A%3ABase&forceFreshWin=0&relatedRequestID=PSFBSTXHNBTJVNJYK066&maxLineCount=6&count=14&offset=7&asins={$skusStr}&_=1505108147223",

            CURLOPT_TIMEOUT        => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_AUTOREFERER    => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER         => false,
            CURLOPT_COOKIE         => 'x-wl-uid=1vvIrSlAr+SjgwBAcR3knqrA4dpdjgRWOkkfsAItUksYYMoGNm+MHIjj36cb/pTSvSAyBhcUQKds=; session-token=CQq7bKV6aBaejzXF3kmot2yTPh2murUmcoLqLvTuogI05LO6/6gWdbnxE4cEva35o+XKJUiqm7eKiCFKgHmiGcOaulGJkKsqspkfLLoLc+QO5oA1+Nl6oNSNFFLpz    kMdSh+XYpLjU7bD6KMJKUs8gTpyrQHznl235oHnBTgQuJgqZlJtzcxWS1XMQZGc2240+kDp7njRptZIEC7XrNwh6mzpRLEG9Xo/77z7JJb5FHL9jJNUcU0YJQ==; ubid-acbcn=462-7911864-5676230; session-id-time=2082729601l; session-id=457-7603819-0986666',
            CURLOPT_ENCODING       => 'gzip, deflate, br',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
            CURLOPT_HTTPHEADER     => $header
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);


        $returnData = [];

        $result = json_decode($result, true);
        if (!empty($result['data'])) {
            foreach ($result['data'] as $item) {
                $item = html_entity_decode($item);

                $title = '';
                $img   = '';
                $price = 0;
                $rate  = 0;

                preg_match('|alt="(.*)"|U', $item, $titleArr);
                preg_match('|<span class=\'p13n-sc-price\'>￥(.*)</span>|U', $item, $priceArr);
                preg_match('|data-a-dynamic-image="{"(.*)"|U', $item, $imgArr);
                preg_match('|<a class="a-size-small a-link-normal" href="/product-reviews/.*">(.*)</a>|U', $item, $rateArr);
                preg_match('|"asin":"(.*)"|U', $item, $skuArr);

                if (isset($skuArr[1])) {
                    $sku = $skuArr[1];
                }
                if (isset($titleArr[1])) {
                    $title = $titleArr[1];
                }
                if (isset($imgArr[1])) {
                    $img = $imgArr[1];
                }
                if (isset($priceArr[1])) {
                    $price = $priceArr[1];
                }
                if (isset($rateArr[1])) {
                    $rate = $rateArr[1];
                }

                if (!empty($sku)) {
                    $data = [
                        'title' => $title,
                        'img'   => $img,
                        'price' => $price,
                        'rate'  => $rate,
                    ];
                    $returnData[$sku] = $data;
                }
            }
        }


        return $returnData;
    }

}
