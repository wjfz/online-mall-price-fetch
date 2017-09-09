<?php

namespace App\Console\Commands;

use App\Sku;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AmazonSkuSpider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amazon:skuSpider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '亚马逊sku爬虫，sku写进数据库。本脚本不抓价格，只负责抓sku';

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
        $skus = Sku::getNeverFetchedAmazonSkus();
        foreach ($skus as $sku) {
            $this->doSpider($sku->sku);
            sleep(1);
        }

        return true;
    }


    /**
     * @param $sku
     *
     * @return bool
     * @throws \Exception
     */
    private function doSpider($sku)
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
            CURLOPT_URL => "https://www.amazon.cn/gp/aw/d/{$sku}",

            CURLOPT_TIMEOUT        => 5,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_AUTOREFERER    => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HEADER         => false,
            CURLOPT_COOKIE         => 'x-wl-uid=1vvIrSlAr+SjgwBAcR3knqrA4dpdjgRWOkkfsAItUksYYMoGNm+MHIjj36cb/pTSvSAyBhcUQKds=; session-token=CQq7bKV6aBaejzXF3kmot2yTPh2murUmcoLqLvTuogI05LO6/6gWdbnxE4cEva35o+XKJUiqm7eKiCFKgHmiGcOaulGJkKsqspkfLLoLc+QO5oA1+Nl6oNSNFFLpzkMdSh+XYpLjU7bD6KMJKUs8gTpyrQHznl235oHnBTgQuJgqZlJtzcxWS1XMQZGc2240+kDp7njRptZIEC7XrNwh6mzpRLEG9Xo/77z7JJb5FHL9jJNUcU0YJQ==; ubid-acbcn=462-7911864-5676230; session-id-time=2082729601l; session-id=457-7603819-0986666',
            CURLOPT_ENCODING       => 'gzip, deflate, br',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
            CURLOPT_HTTPHEADER     => $header
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        preg_match('|<a href="/gp/p13n-mobile/overflow\?ie=UTF8&asins=(.*)&baseAsin=|', $result, $skus);
        if (empty($skus[1])) {
            // throw new \Exception("亚马逊sku爬虫没有爬到数据 sku:".$sku);
            echo "\n亚马逊sku爬虫没有爬到数据 sku:".$sku;

            return false;
        }

        $skusStr = urldecode($skus[1]);

        echo "根据 {$sku} 获取到了 {$skusStr}\n";

        $skusArr = explode(',', $skusStr);
        foreach ($skusArr as $item) {
            Sku::addSourceSku(Sku::SOURCE_AMAZON, $item);
        }

        return true;
    }
}
