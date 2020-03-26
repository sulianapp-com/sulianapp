<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/6
 * Time: 下午4:11
 */

namespace app\common\models;

use app\common\modules\status\StatusObserverDispatcher;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * 状态
 * Class State
 * @package app\common\models\statusFlow
 * @property int id
 * @property int order
 * @property string code
 * @property string name
 * @property Flow flow
 */
class Status extends BaseModel
{
    use SoftDeletes;
    public $table = 'yz_status';

    protected $guarded = ['id'];
    protected $fillable = ['name', 'code', 'order', 'plugin_id'];
    const ORDER_CLOSE = -2;
    const ORDER_CANCEL = -1;

    /**
     * 包含此状态的流程
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flow()
    {
        return $this->belongsTo(Flow::class, 'flow_id');
    }

    /**
     * @return string
     */
    public function getFullCodeAttribute()
    {
        return $this->flow->code . '.' . $this->code;
    }

}
