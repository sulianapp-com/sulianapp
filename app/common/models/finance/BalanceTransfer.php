<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/2
 * Time: 下午2:25
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/*
 * 余额转让记录
 *
 * */
class BalanceTransfer extends BaseModel
{
    public $table = 'yz_balance_transfer';

    protected $guarded = [''];

    const TRANSFER_STATUS_SUCCES = 1;

    const TRANSFER_STATUS_ERROR =-1;


    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function(Builder $builder){
                return $builder->uniacid();
            }
        );
    }



    /**
     * 关联会员数据表，一对一
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transferInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'transferor');
    }
    /**
     * 关联会员数据表，一对一
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recipientInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'recipient');
    }


    /**
     * 检索条件 单号／流水号
     * @param $query
     * @param $orderSn
     * @return mixed
     */
    public function scopeOfOrderSn($query,$orderSn)
    {
        return $query->where('order_sn',$orderSn);
    }


    /**
     * @param $query
     * @return mixed
     */
    public function scopeWithTransfer($query)
    {
        return $query->with(['transferInfo' => function($transferInfo) {
            return $transferInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
        }]);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeWithRecipient($query)
    {
        return $query->with(['recipientInfo' => function($recipientInfo) {
            return $recipientInfo->select('uid', 'nickname', 'realname', 'avatar', 'mobile');
        }]);
    }

    /**
     * 记录检索
     * @param $query
     * @return mixed
     */
    public function scopeRecords($query)
    {
        return $query->withTransfer()->withRecipient();
    }

    /**
     * 条件检索
     * @param $query
     * @param $search
     * @return mixed
     */
    public function scopeSearch($query,$search)
    {
        if ($search['transfer']) {
            $query = $query->whereHas('transferInfo',function($query)use($search) {
                $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile')
                    ->where('uid',$search['transfer'])
                    ->orWhere('nickname','like', '%'.$search['transfer']. '%')
                    ->orWhere('mobile','like','%'.$search['transfer']. '%')
                    ->orWhere('realname','like','%'.$search['transfer']. '%');
            });
        }
        if ($search['recipient']) {
            $query = $query->whereHas('recipientInfo',function($query)use($search) {
                $query->select('uid', 'nickname', 'realname', 'avatar', 'mobile')
                    ->where('uid',$search['recipient'])
                    ->orWhere('nickname','like', '%'.$search['recipient']. '%')
                    ->orWhere('mobile','like','%'.$search['recipient']. '%')
                    ->orWhere('realname','like','%'.$search['recipient']. '%');
            });
        }

        return $query;
    }








/////////////////////////////////

// 以下废弃使用， 慢慢移除


    /**
     * 关联会员数据表，一对一
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transferorInfo()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'transferor');
    }

    /**
     * @param $recordId
     *
     * @return mixed */
    public static function getTransferRecordByRecordId($recordId)
    {
        return self::where('id', $recordId)->first();
    }

    /*
     * 获取会员余额转让记录
     *
     * @params int $transferId
     *
     * @return object
     * @Author yitian */
    public static function getMemberTransferRecord($transferId) {
        return self::uniacid()
            ->select('recipient', 'money', 'created_at', 'status')
            ->where('transferor', $transferId)
            ->with(['recipientInfo' => function($query) {
                return $query->select('uid', 'nickname', 'realname');
            }])
            ->get();
    }

    /*
     * 获取会员被转让记录
     *
     * @params int $recipientId
     *
     * @return object
     * @Author yitian */
    public static function getMemberRecipientRecord($recipientId) {
        return self::uniacid()
            ->select('transferor', 'money', 'created_at', 'status')
            ->where('recipient', $recipientId)
            ->with(['transferorInfo' => function($query) {
                return $query->select('uid', 'nickname', 'realname');
            }])
            ->get();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'uniacid'   => "公众号ID不能为空",
            'transferor'=> "转让者ID不能为空",
            'recipient' => '被转让者ID不能为空',
            'money'     => '转让金额必须是有效的数字，允许两位小数',
            'status'    => '状态不能为空'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'   => "required",
            'transferor'=> "required",
            'recipient' => 'required',
            'money'     => 'numeric|regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'status'    => 'required'
        ];
    }

}
