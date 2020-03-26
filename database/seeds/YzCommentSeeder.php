<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
use app\common\models\Member;

class YzCommentSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_order_comment';
    protected $newTable = 'yz_comment';

    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if ($newList->isNotEmpty()) {
            echo "yz_comment 已经有数据了跳过\n";
            return;
        }
        $list = \Illuminate\Support\Facades\DB::table($this->oldTable)->get();
        if ($list) {
            foreach ($list as $v) {
                $uid = Member::getUidByOpenID($v['openid']);
                //迁移主评论
                $cid = \Illuminate\Support\Facades\DB::table($this->newTable)->insertGetId([
                    'uniacid' => $v['uniacid'],
                    'order_id' => $v['orderid'],
                    'goods_id' => $v['goodsid'],
                    'uid' => $uid,
                    'nick_name' => $v['nickname'],
                    'head_img_url' => $v['headimgurl'],
                    'content' => $v['content'],
                    'level' => $v['level'],
                    'images' => $v['images'],
                    'comment_id' => 0,
                    'reply_id' => 0,
                    'reply_name' => Null,
                    'created_at' => $v['createtime'],
                    'updated_at' => NULL,
                    'deleted_at' => $v['deleted'] ? time() : NULL
                ]);
                //迁移管理员回复主评论
                if (!empty($v['reply_content'])) {
                    \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                        'uniacid' => $v['uniacid'],
                        'order_id' => $v['orderid'],
                        'goods_id' => $v['goodsid'],
                        'uid' => 0,
                        'nick_name' => '',
                        'head_img_url' => '',
                        'content' => $v['reply_content'],
                        'level' => 0,
                        'images' => $v['reply_images'],
                        'comment_id' => $cid,
                        'reply_id' => $uid,
                        'reply_name' => $v['nickname'],
                        'created_at' => $v['createtime'] + 7200,
                        'updated_at' => NULL,
                        'deleted_at' => $v['deleted'] ? time() : NULL,
                    ]);
                }
                //迁移追加评论
                if (!empty($v['append_content'])) {
                    \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                        'uniacid' => $v['uniacid'],
                        'order_id' => $v['orderid'],
                        'goods_id' => $v['goodsid'],
                        'uid' => $uid,
                        'nick_name' => $v['nickname'],
                        'head_img_url' => $v['headimgurl'],
                        'content' => $v['append_content'],
                        'level' => 0,
                        'images' => $v['append_images'],
                        'comment_id' => $cid,
                        'reply_id' => 0,
                        'reply_name' => Null,
                        'created_at' => $v['createtime'] + 14000,
                        'updated_at' => NULL,
                        'deleted_at' => $v['deleted'] ? time() : NULL,
                    ]);
                }
                //迁移管理员回复追加评论
                if (!empty($v['append_reply_content'])) {
                    \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                        'uniacid' => $v['uniacid'],
                        'order_id' => $v['orderid'],
                        'goods_id' => $v['goodsid'],
                        'uid' => 0,
                        'nick_name' => '',
                        'head_img_url' => '',
                        'content' => $v['append_reply_content'],
                        'level' => 0,
                        'images' => $v['append_reply_images'],
                        'comment_id' => $cid,
                        'reply_id' => $uid,
                        'reply_name' => $v['nickname'],
                        'created_at' => $v['createtime'] + 21200,
                        'updated_at' => NULL,
                        'deleted_at' => $v['deleted'] ? time() : NULL,
                    ]);
                }
            }
        }

        // TODO: Implement run() method.
    }

}