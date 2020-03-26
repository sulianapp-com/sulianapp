<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/03/2017
 * Time: 16:26
 */

namespace app\common\models;


class TemplateMessage extends BaseModel
{
    public $table = 'yz_template_message';

    //可用
    const STATUS_ENABLED = 1;
    //不可用
    const STATUS_DISABLED = 0;

    /**
     * 初始化模板消息
     */
    public function init()
    {
        //array getAllTemplates() 获取所有模板列表

        //已经设置的则更新status 为 可用

        //未更新则string addTemplate($shortId) 添加模板并获取模板ID；

        //将模板ID更新到数据库
    }

    public function getAllTemplates()
    {
        return static::where('parent_item','')->get();
    }


    /**
     * 发送模板消息
     */
    public function send($item, $openid, $data)
    {

    }

    public function getTemplateByItem($item)
    {

    }

    /**
     * 添加模板消息
     */
    public function add()
    {

    }

    /**
     * 修改模板消息
     */
    public function edit()
    {

    }
}