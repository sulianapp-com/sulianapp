<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2018/11/29
 * Time: 10:25
 */

namespace app\common\services\popularize;

use app\common\exceptions\AppException;
use app\common\models\Income;
use app\frontend\modules\finance\interfaces\IIncomePage;


class PopularizePageShowFactory
{
    /**
     * @var IIncomePage
     */
    private $_income;


    /**
     * 会员是否是推客
     *
     * @var bool
     */
    private $is_agent;


    /**
     * 是否开启关系链
     *
     * @var bool
     */
    private $is_relation;



    private $lang_set;


    public function __construct(IIncomePage $income, $lang_set, $is_relation = false, $is_agent = false)
    {
        $this->_income = $income;
        $this->is_agent = $is_agent;
        $this->is_relation = $is_relation;
        $this->lang_set = $lang_set;
    }


    public function getMark()
    {
        return $this->_income->getMark();
    }

    /**
     * 收入模型是否显示
     *
     * @return bool
     */
    public function isShow()
    {
        return $this->_income->isShow();
    }

    /**
     * 收入页的前端路由
     * @return string 前端路由名
     */
    public function getAppUrl()
    {
        return  $this->_income->getAppUrl();
    }


    public function getTitle()
    {
        $mark = $this->_income->getMark();

        if (isset($this->lang_set[$mark]['title']) && !empty($this->lang_set[$mark]['title'])) {
            return $this->lang_set[$mark]['title'];
        }
        return $this->_income->getTitle();
    }

}