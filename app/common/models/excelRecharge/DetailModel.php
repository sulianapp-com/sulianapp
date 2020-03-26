<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-07-08
 * Time: 10:28
 */

namespace app\common\models\excelRecharge;


use app\common\models\BaseModel;
use app\common\models\Member;

class DetailModel extends BaseModel
{
    /**
     * @var string
     */
    public $table = 'yz_excel_recharge_detail';

    /**
     * @var array
     */
    protected $guarded= [''];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recharge()
    {
        return $this->belongsTo(RecordsModel::class, 'recharge_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'uid');
    }


}
