<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-07-08
 * Time: 10:30
 */

namespace app\backend\models\excelRecharge;


use app\common\scopes\UniacidScope;

class RecordsModel extends \app\common\models\excelRecharge\RecordsModel
{
    protected $appends = ['sourceName'];

    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope());
    }

    /**
     * 通过字段 source 输出 sourceName
     *
     * @return string
     * @Author yitian
     */
    public function getSourceNameAttribute()
    {
        return $this->getSourceNameComment($this->attributes['source']);
    }

    /**
     * @param $source
     * @return mixed|string
     */
    public function getSourceNameComment($source)
    {
        return isset($this->sourceComment()[$source]) ? $this->sourceComment()[$source] : '';
    }

    /**
     * @return array
     */
    public function sourceComment()
    {
        return [
            'balance' => '余额',
            'point'   => '积分',
            'love'    => $this->loveName()
        ];
    }

    /**
     * @return string
     */
    private function loveName()
    {
        if (app('plugins')->isEnabled('love')) {
            return LOVE_NAME;
        }
        return "爱心值";
    }


}
