<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/03/2017
 * Time: 18:10
 */

namespace app\common\events;


class WechatProcessor extends Event
{

    protected $processor;

    protected $pluginName;

    public $response;

    public function __construct($processor, $pluginName,  &$response)
    {
        $this->processor = $processor;
        $this->pluginName = $pluginName;
        $this->response = &$response;
    }

    /**
     * 获取触发插件名
     * @return mixed
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * 获取微信进程对象
     * @return mixed
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * 设置响应数据
     * @param $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}