<?php
namespace app\backend\modules\goods\services;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 下午5:10
 */

class CommentService
{

    public static function getTypeName($type)
    {

        switch ($type) {
            case '1':
                return "评论";
                break;
            case '2':
                return "回复";
                break;
            case '3':
                return "追加评论";
                break;
            default:
                return "追加回复";

        }
    }

    /**
     * @param array $search
     * @return mixed
     */
    public static function Search($search = [])
    {

        $data = [
            'keyword' => '',
            'fade' => '',
            'searchtime' => '',
            'starttime' => strtotime('-1 month'),
            'endtime' => time()
        ];
        if ($search) {

            $data['keyword'] = $search['keyword'];
            $data['fade'] = $search['fade'];
            $data['searchtime'] = $search['searchtime'];

            if ($search['searchtime']) {
                if ($search['time']['start'] != '请选择' && $search['time']['end'] != '请选择') {
                    $data['starttime'] = strtotime($search['time']['start']);
                    $data['endtime'] = strtotime($search['time']['end']);
                }

            }
        }
        return $data;
    }

    /**
     * @param $comment
     * @return mixed
     */
    public static function comment($comment)
    {
        $comment->created_at = time();
        if (isset($comment->images) && is_array($comment->images)) {
            $comment->images = serialize($comment->images);
        } else {
            $comment->images = serialize([]);
        }
        return $comment;
    }


    public static function reply($reply, $member)
    {
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'order_id' => $reply['order_id'],
            'goods_id' => $reply['goods_id'],
            'nick_name' => $reply['nick_name'],
            'content' => $reply['reply_content'],
            'created_at' => time(),
            'comment_id' => $reply['comment_id'],
            'reply_id' => $reply['reply_id'],
            'reply_name' => $member->nickname,
            'type' => $reply['type'],
        ];
        if (isset($reply['reply_images']) && is_array($reply['reply_images'])) {
            $data['images'] = serialize($reply['reply_images']);
        } else {
            $data['images'] = serialize([]);
        }
        return $data;
    }


}