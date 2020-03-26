<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/24
 * Time: 15:34
 */

namespace app\common\models;

use app\framework\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperationLog extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_operation_log';

    protected $guarded = ['id'];

    protected $appends = ['modules_name', 'type_name'];

    protected $attributes = [
    ];

    public function scopeSearch(Builder $query, $search)
    {
        $model = $query->uniacid();

        if ($search['user_name']) {
            $model->where('user_name', 'like', '%' . $search['user_name'] . '%');
        }

        if ($search['is_time']) {
                if ($search['time']['start'] != '请选择' && $search['time']['end'] != '请选择') {
                    $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                    $model->whereBetween('created_at', $range);
                }
        }


        return $model;

    }

    static public function del($start, $end)
    {
        $range = [strtotime($start), strtotime($end)];
        return static::whereBetween('created_at', $range);
    }

    public function getModulesNameAttribute()
    {
        switch ($this->modules) {
            case 'goods':
                $modules_name = '商品';
                break;
            case 'member':
                $modules_name = '会员';
                break;
            case 'finance':
                $modules_name = '财务';
                break;
            case 'order':
                $modules_name = '订单';
                break;
            case 'shop':
                $modules_name = '系统';
                break;
            default:
                $modules_name = '';
                break;
        }

        return  $modules_name;
    }

    public function getTypeNameAttribute()
    {
        switch ($this->type) {
            case 'update':
                $type_name = '修改';
                break;
            case 'create':
                $type_name = '创建';
                break;
            case 'balance':
                $type_name = '余额设置';
                break;
            case 'withdraw_balance':
                $type_name = '余额提现设置';
                break;
            case 'income':
                $type_name = '收入提现设置';
                break;
            case 'dank_card':
                $type_name = '银行卡管理';
                break;
            case 'operating':
                $type_name = '订单操作';
                break;
            case 'point':
                $type_name = '积分设置';
                break;
            case 'relation':
                $type_name = '关系设置';
                break;
            case 'pay':
                $type_name = '支付方式';
                break;
            default:
                $type_name = '';
                break;
        }

        return  $type_name;
    }
}