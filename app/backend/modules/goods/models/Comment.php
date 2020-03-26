<?php
namespace app\backend\modules\goods\models;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 下午5:10
 */

class Comment extends \app\common\models\Comment
{
    static protected $needLog = true;

    /**
     * @param $pageSize
     * @return mixed
     */
    public static function getComments($search)
    {
        $commentModdel = self::uniacid();
        if ($search['keyword']) {
            $commentModdel->whereHas('goods', function ($query) use ($search) {
                return $query->searchLike($search['keyword']);
            });
        }
        $commentModdel->with(['goods' => function ($query) {
            return $query->select(['id', 'title', 'thumb']);
        }]);
        $commentModdel->where('comment_id', '0');

        if ($search['fade'] == 1) {
            $commentModdel->where('uid', '>', '0');
        } elseif ($search['fade'] == 2) {
            $commentModdel->where('uid', '=', '0');
        }
        if ($search['searchtime']) {
            if ($search['starttime'] != '请选择' && $search['endtime'] != '请选择') {
                $range = [$search['starttime'], $search['endtime']];
                $commentModdel->whereBetween('created_at', $range);
            }
        }
        $commentModdel->orderBy('created_at', 'desc');

        return $commentModdel;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getComment($id)
    {
        return self::with(['hasManyReply'=>function ($query) {
            return $query->where('type', 2)
                ->orderBy('created_at', 'asc');
        }])
            ->with(['hasManyAppend' => function ($query) {
                return $query->where('type', 3)
                    ->orderBy('created_at', 'asc');
            }])
            ->where('id', $id);
    }


    /**
     * @param $comment
     * @return bool
     */
    public static function saveComment($comment)
    {
        return self::insert($comment);
    }

    /**
     * @param $comment_id
     * @return mixed
     */
    public static function getReplysByCommentId($comment_id)
    {
        return self::where('comment_id', $comment_id)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function daletedComment($id)
    {
        return self::where('id', $id)
            ->delete();
    }

    /**
     *
     * @return mixed
     */
    public function goods()
    {
        return $this->belongsTo(\app\common\models\Goods::class);
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public function atributeNames()
    {
        return [
            'goods_id' => '评论商品',
            'content' => '评论内容',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'goods_id' => 'required',
            'content' => 'required'
        ];
    }
}