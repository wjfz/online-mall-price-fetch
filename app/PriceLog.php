<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceLog extends Model
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'price_logs';

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * @param $skuID
     * @param $price
     *
     * @return $this|Model
     */
    public static function createSkuPrice($skuID, $price)
    {
        $attributes = [
            'sku_id' => $skuID,
            'price'  => $price,
        ];

        $model = (new self)->create($attributes);

        return $model;
    }
}
