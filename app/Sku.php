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
     * @param int $lastFetchHoursAgo 获取距上次抓取已经过去N小时的skus
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getNeedFetchAmazonSkus($lastFetchHoursAgo = 4)
    {
        $time = Carbon::now()->subHours($lastFetchHoursAgo)->toDateTimeString();

        return (new Sku())->where('last_fetch', '<', $time)
            ->where('source', self::SOURCE_AMAZON)
            ->get();
    }

    /**
     * @param $title
     *
     * @return bool
     */
    public function saveTitle($title)
    {
        $this->title      = $title;
        $this->last_fetch = Carbon::now()->toDateTimeString();
        $this->count++;

        return $this->save();
    }
}
