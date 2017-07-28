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
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['source', 'sku', 'last_fetch'];

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
            'last_fetch' => Carbon::yesterday(),
        ];
        $model = (new Sku)->firstOrCreate($attributes);

        return $model ? $model : false;
    }
}
