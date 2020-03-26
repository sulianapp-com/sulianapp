<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/5/8 下午2:07
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\finance\interfaces;


interface IIncomePage
{
    /**
     * 对应收入唯一标示
     *
     * @return string
     */
    function getMark();


    /**
     * 系统设置是否显示
     *
     * @return bool
     */
    function isShow();


    /**
     * 是否可用状态(属于更多权限或可用权限)
     *
     * @return bool
     */
    function isAvailable();


    /**
     * 对应收入名称
     *
     * @return string
     */
    function getTitle();


    /**
     * 对应收入图标
     *
     * @return string
     */
    function getIcon();


    /**
     * 对应收入 type 字段 value 值 或 该收入的金额值（计算好直接传过来的值）
     *
     * @return string
     */
    function getTypeValue();


    /**
     * 对应收入 等级
     *
     * @return string
     */
    function getLevel();


    /**
     * app 访问url
     *
     * @return string
     */
    function getAppUrl();


    /**
     * 是否需要验证是推客，true 需要，false 不需要
     *
     * @return mixed
     */
    function needIsAgent();


    /**
     * 是否需要验证开启关系链，true 需要，false 不需要
     *
     * @return mixed
     */
    function needIsRelation();

}
