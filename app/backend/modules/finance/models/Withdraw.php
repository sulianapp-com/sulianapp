<?php

/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/1/30 上午10:08
 * Email: livsyitian@163.com
 */
namespace app\backend\modules\finance\models;


class Withdraw extends \app\common\models\Withdraw
{

    public function scopeRecords($query)
    {
        $query->with(['hasOneMember' => function ($query) {
            return $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);

        return parent::scopeRecords($query);
    }



    public function scopeSearch($query, $search)
    {
        if($search['member_id']) {
            $query->where('member_id',$search['member_id']);
        }

        if (isset($search['status']) && $search['status'] != "") {
            $query->ofStatus($search['status']);
        }

        if($search['withdraw_sn']) {
            $query->ofWithdrawSn($search['withdraw_sn']);
        }

        if($search['type']) {
            $query->whereType($search['type']);
        }

        if($search['pay_way']) {
            $query->where('pay_way', $search['pay_way']);
        }

        if($search['searchtime']){
            $range = [strtotime($search['time']['start']),  strtotime($search['time']['end'])];
            $query->whereBetween('created_at', $range);
        }

        if($search['member']) {
            $query->whereHas('hasOneMember', function($query)use($search){
                return $query->searchLike($search['member']);
            });
        }

        return $query;
    }

    public static function getTypes()
    {
        $configs = \app\backend\modules\income\Income::current()->getItems();
        
        return $configs;
    }









    protected $appends = ['type_data'];

    public static function getWithdrawList($search = [])
    {

        $Model = self::uniacid();
        if ($search['status'] == '3') {
            $Model->whereNotNull(arrival_at);
        } elseif (isset($search['status'])) {
            $Model->where('status', $search['status']);
        }

        if($search['member']) {
            $Model->whereHas('hasOneMember', function($query)use($search){
                return $query->searchLike($search['member']);
            });
        }
        if($search['withdraw_sn']) {
            $Model->where('withdraw_sn', $search['withdraw_sn']);
        }
        if($search['type']) {
            $Model->where('type', $search['type']);
        }
        if($search['searchtime']){
            if($search['times']){
                $range = [$search['times']['start'], $search['times']['end']];
                $Model->whereBetween('created_at', $range);
            }
        }

        $Model->with(['hasOneMember' => function ($query) {
            return $query->select('uid', 'mobile', 'realname', 'nickname', 'avatar');
        }]);
        return $Model;
    }

    public static function getAllWithdraw($type)
    {
        $ids = '';
        $total = 0;

        $data = self::getWithdrawListForType($type)->get();

        if (!is_null($data)) {
            foreach ($data as $rows) {
                $ids .= $rows->id . ',';
            }
        }

        $ids = rtrim($ids, ',');
        $total = count($data);

        if ($total == 0 && $ids == '') {
            $status = 0;
            $msg    = '暂无数据';
        } elseif ($total != count(explode(',', $ids))) {
            $status = -1;
            $msg     = '数据不符';
        } else {
            $status = 1;
            $msg    = 'ok';
        }

        return ['status' => $status, 'totals' => $total, 'ids' => $ids, 'msg' => $msg];
    }

    public static function getWithdrawListForType($type, $limit=800, $status=1)
    {
        $Model = self::uniacid();

        switch ($type) {
            case 1:
                $Model->whereIn('type', ['balance']);
                break;
            case 2:
                $Model->whereNotIn('type', ['balance']);
                break;
        }

        $Model->where('status', $status)
            ->where('pay_way', 'alipay')
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get();

        return $Model;
    }

    public static function updateWidthdrawOrderStatus($withdrawId)
    {
        return self::uniacid()
            ->whereIn('id', $withdrawId)
            ->update(['status' => 4]);
    }

    public function rules()
    {

        return [
            'poundage'          => 'numeric|min:0|max:999999999|regex:/^\d+(\.\d{1,2})?$/',
            'withdrawmoney'     => 'numeric|min:0|max:999999999',
            'roll_out_limit'    => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
            'poundage_rate'     => 'numeric|min:0|max:100|regex:/^[\d]{1,3}+(\.[0-9]{1,2})?$/',
        ];
    }

    public function atributeNames()
    {
        return [
            'poundage'          => "提现手续费",
            'withdrawmoney'     => "提现限制金额",
            'roll_out_limit'    => "提现额度",
            'poundage_rate'     => "提现手续费"
        ];
    }


}