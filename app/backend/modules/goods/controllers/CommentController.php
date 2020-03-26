<?php
namespace app\backend\modules\goods\controllers;


use app\common\helpers\Url;
use app\common\models\Goods;
use app\common\models\Member;

use app\backend\modules\goods\models\Comment;
use app\backend\modules\goods\services\CommentService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 下午5:09
 */
class CommentController extends BaseController
{
    /**
     * 评论列表
     */
    public function index()
    {
        $pageSize = 10;

        $search = CommentService::Search(\YunShop::request()->search);

        $list = Comment::getComments($search)->paginate($pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);

        return view('goods.comment.list', [
            'list' => $list['data'],
            'total' => $list['total'],
            'pager' => $pager,
            'search' => $search,
        ])->render();

    }


    /**
     * 添加评论
     */
    public function addComment()
    {
        $goods_id = \YunShop::request()->goods_id;
        $goods = [];
        if (!empty($goods_id)) {
            $goods = Goods::getGoodsById($goods_id);
            if (!$goods) {
                return $this->message('未找到此商品或该商品已被删除', Url::absoluteWeb('goods.comment.index'));
            }
            $goods = $goods->toArray();
        }


        $commentModel = new Comment();
        $commentModel->goods_id = $goods_id;

        $requestComment = \YunShop::request()->comment;

        if ($requestComment) {
            //将数据赋值到model
            $commentModel->setRawAttributes($requestComment);
            //其他字段赋值
            $commentModel->uniacid = \YunShop::app()->uniacid;
            if (empty($commentModel->nick_name)) {
                $commentModel->nick_name = Member::getRandNickName()->nickname;
            }
            if (empty($commentModel->head_img_url)) {
                $commentModel->head_img_url = Member::getRandAvatar()->avatar;
            }
            $commentModel = CommentService::comment($commentModel);
            //字段检测
            $validator = $commentModel->validator($commentModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($commentModel->save()) {
                    Goods::updatedComment($commentModel->goods_id);
                    //显示信息并跳转
                    return $this->message('评论创建成功', Url::absoluteWeb('goods.comment.index'));
                } else {
                    $this->error('评论创建失败');
                }
            }
        }

        return view('goods.comment.info', [
            'comment' => $commentModel,
            'goods' => $goods
        ])->render();
    }

    /**
     * 修改评论
     */
    public function updated()
    {
        $id = \YunShop::request()->id;
        $commentModel = Comment::getComment($id)->first();
        if (!$commentModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }

        if (!empty($commentModel->goods_id)) {
            $goods = Goods::getGoodsById($commentModel->goods_id);
        }
        $requesComment = \YunShop::request()->comment;

        if ($requesComment) {
            //将数据赋值到model
            $commentModel->setRawAttributes($requesComment);

            if (empty($commentModel->nick_name)) {
                $commentModel->nick_name = Member::getRandNickName()->nick_name;
            }
            if (empty($commentModel->head_img_url)) {
                $commentModel->head_img_url = Member::getRandAvatar()->avatar;
            }
            $commentModel = CommentService::comment($commentModel);
            //字段检测
            $validator = $commentModel->validator($commentModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($commentModel->save()) {
                    //显示信息并跳转
                    return $this->message('评论保存成功', Url::absoluteWeb('goods.comment.index'));
                } else {
                    $this->error('评论保存失败');
                }
            }
        }

        return view('goods.comment.info', [
            'id' => $id,
            'comment' => $commentModel,
            'goods' => $goods
        ])->render();
    }

    /**
     * 评论回复
     */
    public function reply()
    {
        $id = intval(\YunShop::request()->id);
        $commentModel = Comment::getComment($id)->first();
        if (!$commentModel) {
            return $this->message('无此记录或已被删除', '', 'error');
        }

        if (\YunShop::request()->reply) {
            return $this->createReply();
        }


        $commentModel = $commentModel->toArray();
        $goods = Goods::getGoodsById($commentModel['goods_id']);
        return view('goods.comment.reply', [
            'comment' => $commentModel,
            'goods' => $goods
        ])->render();
    }

    public function createReply()
    {
        $id = intval(\YunShop::request()->id);
        $commentModel = new Comment;

        $requestReply = \YunShop::request()->reply;
        if ($requestReply) {
            $member = Member::getMemberById($requestReply['reply_id']);
            $requestReply = CommentService::reply($requestReply, $member);
            //将数据赋值到model
            $commentModel->setRawAttributes($requestReply);
            $validator = $commentModel->validator($commentModel->getAttributes());
            //字段检测
            if ($validator->fails()) {
                return $this->message($validator->messages(), '', 'error');
            } else {
                //数据保存
                if (Comment::saveComment($commentModel->getAttributes())) {
                    //显示信息并跳转
                    return $this->message('评论回复保存成功', Url::absoluteWeb('goods.comment.reply', ['id' => $id]));
                } else {
                    return $this->message('评论回复保存失败', '', 'error');

                }
            }
        }
    }


    /**
     * 删除评论
     */
    public function deleted()
    {
        $comment = Comment::getComment(\YunShop::request()->id);
        if (!$comment) {
            return $this->message('无此评论或已经删除', '', 'error');
        }

        $result = Comment::daletedComment(\YunShop::request()->id);
        if ($result) {
            return $this->message('删除评论成功', Url::absoluteWeb('goods.comment.index'));
        } else {
            return $this->message('删除评论失败', '', 'error');
        }

    }


}