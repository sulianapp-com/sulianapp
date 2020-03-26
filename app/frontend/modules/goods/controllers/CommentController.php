<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午2:29
 */

namespace app\frontend\modules\goods\controllers;

use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\models\Member;
use app\common\models\OrderGoods;
use app\frontend\models\Order;
use app\frontend\modules\goods\models\Comment;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class CommentController extends ApiController
{
    // 前端评论时，图片不能超过5张
    const COMMENT_IMAGE_COUNT = 5;

    public function getComment()
    {
        $goodsId = \YunShop::request()->goods_id;
        $pageSize = 20;
        $list = Comment::getCommentsByGoods($goodsId)->paginate($pageSize);//

        if ($list) {
            foreach ($list as &$item) {
                $item->reply_count = $item->hasManyReply->count('id');
                $item->head_img_url = $item->head_img_url ? replace_yunshop(yz_tomedia($item->head_img_url)) : yz_tomedia(\Setting::get('shop.shop.logo'));
            }
            //对评论图片进行处理，反序列化并组装完整图片url
            $list = $list->toArray();
            foreach ($list['data'] as &$item) {
                self::unSerializeImage($item);
            }
//            $list['favorable_rate'] = $this->favorableRate($goodsId);
            return $this->successJson('获取评论数据成功!', $list);
        }
        return $this->errorJson('未检测到评论数据!', $list);
    }

    /*
     * 获取商品好评率
     */
    public function favorableRate($id)
    {
        $total = OrderGoods::where('goods_id',$id)->sum('id');
        $level_comment = \app\common\models\Comment::where(['goods_id' => $id])->sum('level');
        $comment = \app\common\models\Comment::where(['goods_id' => $id])->sum('id');
        $mark = bcmul($total,5,2);//总评分
        $no_comment = bcmul(bcsub($total,$comment,2) ,5,2);//未评分
        $have_comment = bcmul(bcdiv(bcadd($level_comment,$no_comment,2),$mark,2),100,2);//最终好评率
        return $have_comment.'%';

    }

    public function createComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if (!$member) {
            return $this->errorJson('评论失败!未检测到会员数据!');
        }
        $commentStatus = '1';

        $comment = [
            'order_id' => \YunShop::request()->order_id,
            'goods_id' => \YunShop::request()->goods_id,
            'content' => \YunShop::request()->content,
            'level' => \YunShop::request()->level,
        ];
        if (!$comment['order_id']) {
            return $this->errorJson('评论失败!未检测到订单ID!');
        }
        if (!$comment['goods_id']) {
            return $this->errorJson('评论失败!未检测到商品ID!');
        }
        if (!$comment['content']) {
            return $this->errorJson('评论失败!未检测到评论内容!');
        }
        if (!$comment['level']) {
            return $this->errorJson('评论失败!未检测到评论等级!');
        }

        if (\YunShop::request()->images) {
            $comment['images'] = json_decode(\YunShop::request()->images);
            if (is_array($comment['images'])) {
                if (count($comment['images']) > self::COMMENT_IMAGE_COUNT) {
                    return $this->errorJson('追加评论失败!评论图片不能多于5张!');
                }
                $comment['images'] = serialize($comment['images']);
            } else {
                return $this->errorJson('追加评论失败!评论图片数据不正确!');
            }
        } else {
            $comment['images'] = serialize([]);
        }

        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        $commentModel->type = '1';
        return $this->insertComment($commentModel, $commentStatus);

    }

    public function appendComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if (!$member) {
            return $this->errorJson('追加评论失败!未检测到会员数据!');
        }
        $commentStatus = '2';
        $id = \YunShop::request()->id;
        $append = $commentModel::find($id);
        if (!$append) {
            return $this->errorJson('追加评论失败!未检测到评论数据!');
        }

        $comment = [
            'order_id' => $append->order_id,
            'goods_id' => $append->goods_id,
            'content' => \YunShop::request()->content,
            'comment_id' => $append->id,
        ];
        if (!$comment['content']) {
            return $this->errorJson('追加评论失败!未检测到评论内容!');
        }

        if (\YunShop::request()->images) {
            $comment['images'] = json_decode(\YunShop::request()->images);
            if (is_array($comment['images'])) {
                if (count($comment['images']) > self::COMMENT_IMAGE_COUNT) {
                    return $this->errorJson('追加评论失败!评论图片不能多于5张!');
                }
                $comment['images'] = serialize($comment['images']);
            } else {
                return $this->errorJson('追加评论失败!评论图片数据不正确!');
            }
        } else {
            $comment['images'] = serialize([]);
        }

        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        $commentModel->reply_id = $append->uid;
        $commentModel->reply_name = $append->nick_name;
        $commentModel->type = '3';

        return $this->insertComment($commentModel, $commentStatus);

    }

    public function replyComment()
    {
        $commentModel = new \app\common\models\Comment();
        $member = Member::getUserInfos(\YunShop::app()->getMemberId())->first();
        if (!$member) {
            return $this->errorJson('回复评论失败!未检测到会员数据!');
        }

        $id = \YunShop::request()->id;
        $reply = $commentModel::find($id);
        if (!$reply) {
            return $this->errorJson('回复评论失败!未检测到评论数据!');
        }

        $comment = [
            'order_id' => $reply->order_id,
            'goods_id' => $reply->goods_id,
            'content' => \YunShop::request()->content,
            'comment_id' => $reply->comment_id ? $reply->comment_id : $reply->id,
        ];
        if (!$comment['content']) {
            return $this->errorJson('回复评论失败!未检测到评论内容!');
        }

//        if (isset($comment['images'] ) && is_array($comment['images'])) {
//            $comment['images'] = serialize($comment['images']);
//        } else {
//            $comment['images'] = serialize([]);
//        }
        if (\YunShop::request()->images) {
            $comment['images'] = json_decode(\YunShop::request()->images);
            if (is_array($comment['images'])) {
                if (count($comment['images']) > self::COMMENT_IMAGE_COUNT) {
                    return $this->errorJson('追加评论失败!评论图片不能多于5张!');
                }
                $comment['images'] = serialize($comment['images']);
            } else {
                return $this->errorJson('追加评论失败!评论图片数据不正确!');
            }
        } else {
            $comment['images'] = serialize([]);
        }

        $commentModel->setRawAttributes($comment);

        $commentModel->uniacid = \YunShop::app()->uniacid;
        $commentModel->uid = $member->uid;
        $commentModel->nick_name = $member->nickname;
        $commentModel->head_img_url = $member->avatar;
        $commentModel->reply_id = $reply->uid;
        $commentModel->reply_name = $reply->nick_name;
        $commentModel->type = '2';
        return $this->insertComment($commentModel);

    }

    public function insertComment($commentModel, $commentStatus = '')
    {
        $validator = $commentModel->validator($commentModel->getAttributes());
        if ($validator->fails()) {
            //检测失败
            return $this->errorJson($validator->messages());
        } else {
            //数据保存
            if ($commentModel->save()) {
                Goods::updatedComment($commentModel->goods_id);

                if ($commentStatus) {
                    OrderGoods::where('order_id', $commentModel->order_id)
                        ->where('goods_id', $commentModel->goods_id)
                        ->update(['comment_status' => $commentStatus, 'comment_id' => $commentModel->id]);
                }

                return $this->successJson('评论成功!',$commentModel);
            } else {
                return $this->errorJson('评论失败!');
            }
        }
    }


    public function getOrderGoodsComment()
    {
        $orderId = \YunShop::request()->order_id ?: 0;
        $goodsId = \YunShop::request()->goods_id;
        $uid = intval(\YunShop::request()->uid) ? \YunShop::request()->uid : \YunShop::app()->getMemberId();

//        if (!$orderId) {
//            return $this->errorJson('获取评论失败!未检测到订单ID!');
//        }
        if (!$goodsId) {
            return $this->errorJson('获取评论失败!未检测到商品ID!');
        }
        $comment = Comment::getOrderGoodsComment()
            ->with(['hasOneOrderGoods'=>function($query) use($goodsId) {
                $query->where('goods_id', $goodsId);
            }])
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->where('type', 1)
            ->where('uid', $uid)
            ->first();
        if ($comment) {
            // 将图片字段反序列化
            $arrComment = $comment->toArray();
            self::unSerializeImage($arrComment);
            return $this->successJson('获取评论数据成功!', $arrComment);
        }
        return $this->errorJson('未检测到评论数据!');


    }

    // 反序列化图片
    public static function unSerializeImage(&$arrComment)
    {
        $arrComment['images'] = unserialize($arrComment['images']);
        foreach ($arrComment['images'] as &$image) {
            $image = yz_tomedia($image);
        }
        if ($arrComment['append']) {
            foreach ($arrComment['append'] as &$comment) {
                $comment['images'] = unserialize($comment['images']);
                foreach ($comment['images'] as &$image) {
                    $image = yz_tomedia($image);
                }
            }
        }
        if ($arrComment['has_many_reply']) {
            foreach ($arrComment['has_many_reply'] as &$comment) {
                $comment['images'] = unserialize($comment['images']);
                foreach ($comment['images'] as &$image) {
                    $image = yz_tomedia($image);
                }
            }
        }
    }

    /**
     * 添加评论上传图片
     * @author
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload()
    {
        $file = request()->file('file');
        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $ext = $file->getClientOriginalExtension();
            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

            \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));

            return $this->successJson('上传成功', [
                'img'    => \Storage::disk('image')->url($newOriginalName),
            ]);
        } else {
            return $this->errorJson('上传失败!');
        }

    }


}