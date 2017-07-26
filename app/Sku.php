<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

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
     * @param string $source
     * @param string $sku
     *
     * @return Sku|bool
     */
    public static function addSourceSku($source, $sku)
    {
        $attributes = [
            'source'     => $source,
            'sku'        => $sku,
            'last_fetch' => Carbon::yesterday(),
        ];
        $model = new self();
        $model->fill($attributes);
        try {
            $saved = $model->save();
        } catch (QueryException $exception) {
            if ($exception->getCode() == 23000) {
                $saved = true;
            } else {
                throw $exception;
            }
        }

        return $saved ? $model : false;
    }
}
