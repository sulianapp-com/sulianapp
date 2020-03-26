<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/17/017
 * Time: 15:05
 */

namespace app\platform\modules\application\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreAttach extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_core_attachment';
    protected $guarded = [''];
    protected $hidden  = ['deleted_at', 'updated_at'];
    public $timestamps = true;
    protected $datas = ['deleted_at'];

    // 存储在表中type字段的对应的类型
    const IMAGE_TYPE = 1;// 图片 1
    const VOICE_TYPE = 2;// 音频 2
    const VIDEO_TYPE = 3;// 视频 3
    // 存储在表中upload_type字段的对应的类型
    const UPLOAD_LOCAL = 0;     // 本地
    const UPLOAD_OSS = 2;       // 阿里云
    const UPLOAD_COS = 4;       // 腾讯云

    public function scopeSearch($query, $keyword)
    {
        if ($keyword['month'] && $keyword['year']) {

           return $query->whereBetween('created_at', [
                
                mktime(0,0,0, $keyword['month'], 1, $keyword['year']),
                mktime(23,59,59, $keyword['month']+1, 0, $keyword['year'])
            ]);
        }

        if ($keyword['year']) {
            return $query->whereBetween(
                'created_at', 
                [
                    mktime(0,0,0, 1, 1, $keyword['year']),
                    mktime(23,59,59,12, 31, $keyword['year'])
                ]
            );
        }

        if ($keyword['month']) {
            return $query->whereBetween(
                'created_at', 
                [
                    mktime(0,0,0, $keyword['month'], 1, date('Y')), 
                    mktime(23,59,59, $keyword['month']+1, 0, date('Y')) 
                ] 
            );
        }
       
        // dd(
        //     $keyword['month'], 
        //     $keyword['year'], 
        //         //month
        //     date('Y-m-d H:i:s', mktime(0,0,0, $keyword['month'], 1, date('Y')) ),

        //     date('Y-m-d H:i:s', mktime(23,59,59, $keyword['month']+1, 0, date('Y')) ), 
        //         //year
        //     date('Y-m-d H:i:s', mktime(0,0,0, 1, 1, $keyword['year'])),

        //     date('Y-m-d H:i:s', mktime(23,59,59,12, 31, $keyword['year'])),
        //         //all
        //     date('Y-m-d H:i:s', mktime(0,0,0, $keyword['month'], 1, $keyword['year'])),
            
        //     date('Y-m-d H:i:s', mktime(23,59,59, $keyword['month']+1, 0, $keyword['year']))
        // );
    }

    public function atributeNames()
    {
        return [
            'uniacid' => '公众号id',
            'uid' => '用户id',
            'filename' => '原文件名',
            'attachment' => '新文件名',
        ];
    }
    public function rules()
    {
        return [
            'uniacid' => 'integer',
            'uid' => 'integer',
            'filename' => 'string|max:50',
            'attachment' => '',
        ];
    }
}