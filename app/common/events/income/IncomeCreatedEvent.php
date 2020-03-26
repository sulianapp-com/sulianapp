<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/12/30
 * Time: 5:06 PM
 */

namespace app\common\events\income;


use app\common\events\Event;
use app\common\models\Income;

class IncomeCreatedEvent extends Event
{
    /**
     * @var Income
     */
    private $incomeModel;

    public function __construct(Income $incomeModel)
    {
        $this->setIncomeModel($incomeModel);
    }

    /**
     * @return Income
     */
    public function getIncomeModel()
    {
        return $this->incomeModel;
    }

    /**
     * @param Income $incomeModel
     */
    private function setIncomeModel($incomeModel)
    {
        $this->incomeModel = $incomeModel;
    }

}
