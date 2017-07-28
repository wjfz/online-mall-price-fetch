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
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['sku_id', 'price'];


    /**
     * @param $skuID
     * @param $price
     *
     * @return bool|Model|PriceLog
     */
    public static function createSkuPrice($skuID, $price)
    {
        $attributes = [
            'sku_id' => $skuID,
            'price'  => $price,
        ];

        $model = (new self)->firstOrCreate($attributes);

        return $model ?: false;
    }
}
