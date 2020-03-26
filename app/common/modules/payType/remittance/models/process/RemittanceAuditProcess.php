<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/19
 * Time: 下午5:00
 */

namespace app\common\modules\payType\remittance\models\process;

use app\common\models\Process;
use app\common\models\RemittanceRecord;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class RemittanceProcess
 * @package app\common\modules\payType\remittance\models
 * @property RemittanceRecord $remittanceRecord
 */
class RemittanceAuditProcess extends Process
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function remittanceRecord()
    {
        return $this->belongsTo(RemittanceRecord::class,'model_id');
    }

    /**
     * @param Builder $query
     * @return Builder
     */

    public function scopeDetail(Builder $query)
    {
        return $query->with(['remittanceRecord'=> function ($query) {
            $query->with('orderPay');
        },'member']);
    }
}