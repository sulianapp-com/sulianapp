<?php
namespace app\common\models;

use app\backend\modules\goods\services\CommentService;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 下午5:07
 */

use Illuminate\Support\Facades\DB;

class Comment extends BaseModel
{
    public $attributes = ['type' => 1];
    
    use SoftDeletes;
    public $table = 'yz_comment';


    public $TypeName;

    protected $appends = ['type_name'];

    protected $guarded = [''];
    protected $fillable = [''];


    public static function getOrderGoodsComment()
    {
        return self::uniacid();
    }



    public static function getReplyById($id)
    {
        return self::uniacid()
            ->where('comment_id', $id)
            ->where('uid', '<>', DB::raw('reply_id'))
            ->orderBy('created_at', 'asc')
            ->get();
    }


//    public function getReplyAttribute()
//    {
//        if (!isset($this->Reply)) {
//            $reply['data'] = static::getReplyById($this->id);
//            $reply['count'] = $reply['data']->count('id');
//            $this->Reply = $reply;
//        }
//        return $this->Reply;
//    }
//
//    public function getAppendAttribute()
//    {
//        if (!isset($this->Append)) {
//            $append['data'] = static::getAppendById($this->id);
//            $append['count'] = $append['data']->count('id');
//            $this->Append = $append;
//        }
//        return $this->Append;
//    }

    public static function getAppendById($id)
    {
        return self::uniacid()
            ->where('comment_id', $id)
            ->where('uid', DB::raw('reply_id'))
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getTypeNameAttribute()
    {
        if (!isset($this->TypeName)) {

            $this->TypeName = CommentService::getTypeName($this->type);
        }
        return $this->TypeName;
    }

    public function hasManyReply()
    {
        return $this->hasMany(self::class);
    }

    public function hasManyAppend()
    {
        return $this->hasMany(self::class);
    }

}