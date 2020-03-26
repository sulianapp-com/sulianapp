<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 9:55
 */

namespace app\common\services\operation;


use app\backend\modules\member\models\Member;
use app\common\models\MemberLevel;

class ShopMemberLog extends OperationBase
{
    public $modules = 'member';

    public $type = 'update';

    protected function modifyDefault()
    {
        if (is_array($this->model)) {
            $this->setLog('mark', $this->model['uid']);
        } else {
            $this->setLog('mark', $this->model->getOriginal('member_id'));
        }
    }

    protected function special()
    {
        $nick_name = Member::getMemberByUid($this->model['uid'])->value('nickname');
        $this->setLog('field_name', '会员黑名单');
        $this->setLog('field', 'is_black');
        if ($this->model['is_black']) {
            $this->setLog('old_content',$nick_name.'取消黑名单');
            $this->setLog('new_content', $nick_name.'加入黑名单');
        } else {
            $this->setLog('old_content', $nick_name.'加入黑名单');
            $this->setLog('new_content',$nick_name.'取消黑名单');
        }
    }

    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        return [
            'level_id'      => '会员等级',
            'group_id'      => '会员分组ID',
            'wechat'        => '会员微信号',
            'alipayname'    => '会员支付宝姓名',
            'alipay'        => '会员支付宝账号',
            'is_agent'         => '会员推广员权限',
            'is_black'      => '会员黑名单',
            'parent_id'     => '会员上线ID',


        ];
    }

    /**
     * 获取模型修改了哪些字段
     * @param object array
     * @return array
     */
    protected function modifyField()
    {
        $model = $this->model;

         if (is_null($model->getOriginal())) {
             return [];
         }

        $nick_name = Member::getMemberByUid($model->member_id)->value('nickname');

        foreach ($this->recordField() as $key => $item) {
            if ($model->isDirty($key)) {

                if ($key == 'level_id') {
                    $old_level_name = MemberLevel::where('id', $model->getOriginal('level_id'))->value('level_name');
                    $new_level_name = MemberLevel::where('id', $model->level_id)->value('level_name');
                    $old_level_name = is_null($old_level_name) ? '普通会员': $old_level_name;
                    $new_level_name = is_null($new_level_name) ? '普通会员': $new_level_name;
                    $this->modify_fields[$key]['old_content'] = $nick_name.'旧等级:'. $old_level_name;
                    $this->modify_fields[$key]['new_content'] = $nick_name.'新等级:'. $new_level_name;
                } elseif ($key == 'is_agent') {
                    $this->modify_fields[$key]['old_content'] = ['key'=> $model->getOriginal($key),0=>$nick_name.'推广员权限关闭',1=>$nick_name.'推广员权限开启'];
                    $this->modify_fields[$key]['new_content'] = ['key'=> $model->{$key},0=>$nick_name.'推广员权限关闭',1=>$nick_name.'推广员权限开启'];
                } elseif ($key == 'is_black') {
                    $this->modify_fields[$key]['old_content'] = ['key'=> $model->getOriginal($key),0=>$nick_name.'取消黑名单',1=>$nick_name.'加入黑名单'];
                    $this->modify_fields[$key]['new_content'] = ['key'=> $model->{$key},0=>$nick_name.'取消黑名单',1=>$nick_name.'加入黑名单'];
                } else {
                    $this->modify_fields[$key]['old_content'] = $nick_name.':'.$model->getOriginal($key);
                    $this->modify_fields[$key]['new_content'] = $nick_name.':'.$model->{$key};
                }

            }
        }

        return $this->modify_fields;
    }
}