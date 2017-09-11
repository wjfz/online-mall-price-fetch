<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'skus';


    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    const SOURCE_AMAZON = 'amazon';
    const SOURCE_JD     = 'jd';

    public static $sources = [
        self::SOURCE_AMAZON => '亚马逊',
        self::SOURCE_JD     => '京东',
    ];

    /**
     * @param $source
     * @param $sku
     *
     * @return bool|Model|Sku
     */
    public static function addSourceSku($source, $sku)
    {
        $attributes = [
            'source'     => $source,
            'sku'        => $sku,
        ];
        $model = (new Sku)->firstOrCreate($attributes);

        return $model ? $model : false;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getFetchCount0AmazonSkus()
    {
        return (new Sku())->where('count', '=', 0)
            ->where('source', self::SOURCE_AMAZON)
            ->orderBy('id')
            ->limit(100)
            ->get();
    }

    /**
     * @param int $lastFetchHoursAgo 获取距上次抓取已经过去N小时的skus
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getNeedFetchAmazonSkus($lastFetchHoursAgo = 4)
    {
        $time = Carbon::now()->subHours($lastFetchHoursAgo)->toDateTimeString();

        return (new Sku())->where('last_fetch', '<', $time)
            ->where('source', self::SOURCE_AMAZON)
            ->orderBy('id')
            ->limit(100)
            ->get();
    }

    /**
     * 获取抓取次数小于2次的sku，以此去获得这些sku的推荐商品
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getNeverFetchedAmazonSkus()
    {
        return (new Sku())->where('source', self::SOURCE_AMAZON)
            ->orderByRaw('RAND()')
            ->limit(15)
            ->get();
    }

    /**
     * @param $title
     * @param $rate
     * @param $img
     *
     * @return bool
     */
    public function saveInfo($title, $rate, $img)
    {
        if ($rate < 10) {
            // 少于10个人评论的随机好几个月后再抓取
            $lastFetch = Carbon::now()->addMonth(rand(1,3))->addDays(rand(1,30))->toDateTimeString();
        } elseif ($rate < 30) {
            // 10到30个人评论的随机1-10天后再抓取
            $lastFetch = Carbon::now()->addDays(rand(1,10))->toDateTimeString();
        } else {
            // 大于30个人的，四小时后再抓取
            $lastFetch = Carbon::now()->toDateTimeString();
        }

        $data = [
            'title'      => $title,
            'rate'       => $rate,
            'img'        => $img,
            'last_fetch' => $lastFetch,
            'count'      => ++$this->count
        ];

        return $this->update($data);
    }
}
