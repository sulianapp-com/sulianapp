<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace  app\backend\modules\upload\models;

use app\common\models\BaseModel;


class CoreAttach extends BaseModel
{


    protected $table = 'core_attachment';
    protected $guarded = [''];
    protected $hidden  = [];
    public $timestamps = false;

    const PAGE_SIZE = 33;
    // 存储在表中type字段的对应的类型
    const IMAGE_TYPE = 1;// 图片 1
    const VOICE_TYPE = 2;// 音频 2
    const VIDEO_TYPE = 3;// 视频 3


    public function scopeSearch($query, $keyword)
    {
        if ($keyword['month'] && $keyword['year']) {

           return $query->whereBetween('createtime', [
                
                mktime(0,0,0, $keyword['month'], 1, $keyword['year']),
                mktime(23,59,59, $keyword['month']+1, 0, $keyword['year'])
            ]);
        }

        if ($keyword['year']) {
            return $query->whereBetween(
                'createtime',
                [
                    mktime(0,0,0, 1, 1, $keyword['year']),
                    mktime(23,59,59,12, 31, $keyword['year'])
                ]
            );
        }

        if ($keyword['month']) {
            return $query->whereBetween(
                'createtime',
                [
                    mktime(0,0,0, $keyword['month'], 1, date('Y')), 
                    mktime(23,59,59, $keyword['month']+1, 0, date('Y')) 
                ] 
            );
        }
       
    }

    public function atributeNames()
    {
        
    }
    public function rules()
    {
       
    }
}