<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/9
 * Time: 下午3:09
 */

namespace app\common\models\notice;


use app\common\models\BaseModel;
use app\common\scopes\UniacidScope;
use Illuminate\Database\Eloquent\Builder;

class MessageTemp extends BaseModel
{
    public $table = 'yz_message_template';


    protected $guarded = [''];
    protected $fillable = [];


    public $timestamps = true;


    public static $template_id = null;


    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new UniacidScope);
    }



    protected $casts = [
        'data' => 'json'
    ];

    public static function getList()
    {
        return self::select('id', 'title')->where('is_default',0)->get();
    }

    public function getTempIdByNoticeType($notice_type)
    {
        return self::where('notice_type',$notice_type)->first();
    }

    public static function delTempDataByTempId($temp_id)
    {
        return self::where('template_id',$temp_id)->delete();
    }

    public static function getIsDefaultById($temp_id)
    {
        return self::whereId($temp_id)->where('is_default',1)->first();
    }

    public static function getTempById($temp_id)
    {
        return self::select()->whereId($temp_id);
    }

    public static function fetchTempList($kwd)
    {
        return self::select()->where('is_default',0)->likeTitle($kwd);
    }

    public function scopeLikeTitle($query, $kwd)
    {
        return $query->where('title', 'like', '%' . $kwd . '%');
    }

    public static function handleArray($data)
    {
        $data['uniacid'] = \YunShop::app()->uniacid;
        $data['data'] = [];
        foreach ($data['tp_kw'] as $key => $val )
        {
            $data['data'][] = [
                'keywords' => $data['tp_kw'][$key],
                'value' => $data['tp_value'][$key],
                'color' => $data['tp_color'][$key]
            ];
        }
        return array_except($data, ['tp_kw', 'tp_value', 'tp_color']);
    }



    public static function getSendMsg($temp_id, $params)
    {
            if (!intval($temp_id)) {
                return false;
            }
            $temp = self::withoutGlobalScopes(['uniacid'])->whereId($temp_id)->first();
            if (!$temp) {
                return false;
            }
            self::$template_id = $temp->template_id;
            $msg = [
                'first' => [
                    'value' => self::replaceTemplate($temp->first, $params),
                    'color' => $temp->first_color
                ],
                'remark' => [
                    'value' => self::replaceTemplate($temp->remark, $params),
                    'color' => $temp->remark_color
                ]
            ];
            foreach ($temp->data as $row) {
                $msg[$row['keywords']] = [
                    'value' => self::replaceTemplate($row['value'], $params),
                    'color' => $row['color']
                ];
            }

        return $msg;
    }

    private static function replaceTemplate($str, $datas = array())
    {
        foreach ($datas as $row ) {
            $str = str_replace('[' . $row['name'] . ']', $row['value'], $str);
        }
        return $str;
    }
}