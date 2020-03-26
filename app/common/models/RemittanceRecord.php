<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午4:15
 */

namespace app\common\models;

use app\common\traits\HasProcessTrait;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TransferRecord
 * @package app\common\models
 * @property string report_url
 * @property int process_id
 * @property Process process
 * @property OrderPay orderPay
 */
class RemittanceRecord extends BaseModel
{
    use HasProcessTrait;
    protected $table = 'yz_remittance_record';
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function orderPay()
    {
        return $this->belongsTo(OrderPay::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'uid');
    }
}