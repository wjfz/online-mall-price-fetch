<?php

namespace App\Console\Commands;

use App\AmazonPriceLog;
use App\AmazonSku;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        $startTime = time();

        $time = Carbon::now()->subHours(4)->toDateTimeString();
        $time = Carbon::now()->toDateTimeString();

        $products = (new AmazonSku)->where('last_fetch', '<', $time)->get();
        if ($products->count() == 0) {
            echo date("Y-m-d H:i:s")." 没有要抓取的商品。\n";

            return true;
        }

        $failedCount  = 0;

        foreach ($products as $product) {
            $fetched = $this->fetch($product);
            if ($fetched == false) {
                $failedCount++;
            }

            if ($failedCount >= 3) {
                echo "本次抓取失败达到3个，退出\n";
                return false;
            }

            if ((time() - $startTime) >= 45) {
                echo "本次抓取时长达到45秒，退出\n";
                return true;
            }

            sleep(1);
        }

        return true;
    }

    private function fetch(AmazonSku $product)
    {
        $doFetched = $this->doFetch($product->sku);
        if (!$doFetched) {
            echo "{$product->sku} fetch error.\n";

            return false;
        }

        $product->title = $doFetched['title'];
        $product->count++;
        $product->save();

        (new AmazonPriceLog)->create(['sku_id' => $product->id, 'price' => $doFetched['price']]);

        echo "{$doFetched['title']} 在 ".date("Y-m-d H:i:s")." 的价格是 {$doFetched['price']}\n";

        return true;
    }

    private function doFetch($sku)
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
            CURLOPT_URL => "https://www.amazon.cn/gp/cart/desktop/ajax-mini-detail.html?asin={$sku}&offeringsku={$sku}",

            CURLOPT_TIMEOUT        => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_AUTOREFERER    => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER         => false,
            CURLOPT_COOKIE         => 'x-wl-uid=1vvIrSlAr+SjgwBAcR3knqrA4dpdjgRWOkkfsAItUksYYMoGNm+MHIjj36cb/pTSvSAyBhcUQKds=; session-token=CQq7bKV6aBaejzXF3kmot2yTPh2murUmcoLqLvTuogI05LO6/6gWdbnxE4cEva35o+XKJUiqm7eKiCFKgHmiGcOaulGJkKsqspkfLLoLc+QO5oA1+Nl6oNSNFFLpzkMdSh+XYpLjU7bD6KMJKUs8gTpyrQHznl235oHnBTgQuJgqZlJtzcxWS1XMQZGc2240+kDp7njRptZIEC7XrNwh6mzpRLEG9Xo/77z7JJb5FHL9jJNUcU0YJQ==; ubid-acbcn=462-7911864-5676230; session-id-time=2082729601l; session-id=457-7603819-0986666',
            CURLOPT_ENCODING       => 'gzip, deflate, br',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',
            CURLOPT_HTTPHEADER     => $header
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        preg_match('|<span class="a-size-medium sc-product-title">
(.*)</span>|', $result, $title);
        //        var_dump($title);

        preg_match('|<span class="a-size-medium a-color-price sc-price">￥ (.*)</span>|', $result, $price);
        //        var_dump($price);

        if (isset($title[1]) && isset($price[1])) {
            return [
                'title' => html_entity_decode($title[1]),
                'price' => $price[1],
            ];
        } else {
            return false;
        }
    }
}
