<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午4:09
 */

namespace app\frontend\modules\finance\models;

use \app\common\models\finance\Balance as BalanceModel;
use app\common\services\credit\ConstService;
use \app\frontend\modules\finance\models\BalanceTransfer;
use Illuminate\Database\Eloquent\Builder;

class Balance extends BalanceModel
{
    protected $appends = ['service_type_name','type_name','transfer_member_id','transfer_member_name'];


    /**
     * 设置全局作用域
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(
            function(Builder $builder){
                return $builder->where('member_id',\YunShop::app()->getMemberId());
            }
        );
    }


    /**
     * 输出附加字段 transfer_member_id
     * @return string
     */
    public function getTransferMemberIdAttribute()
    {
        $transferModel = $this->getTransferModel();
        if ($this->attributes['type'] == ConstService::TYPE_INCOME) {
            return $transferModel->transferor ?: '';
        }
        return $transferModel->recipient ?: '';
    }

    /**
     * 输出附加字段 transfer_member_name
     * @return string
     */
    public function getTransferMemberNameAttribute()
    {
        $transferModel = $this->getTransferModel();
        if ($this->attributes['type'] == ConstService::TYPE_INCOME) {
            return $transferModel->transferInfo->realname ?: $transferModel->transferInfo->nickname ?: '';
        }
        return $transferModel->recipientInfo->realname ?: $transferModel->recipientInfo->nickname ?: '';
    }

    /**
     * 通过订单号获取
     * @return mixed
     */
    private function getTransferModel()
    {
        return BalanceTransfer::ofOrderSn($this->attributes['serial_number'])->withTransfer()->withRecipient()->first();
    }

}
