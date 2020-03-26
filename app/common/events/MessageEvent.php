<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/22 下午5:36
 * Email: livsyitian@163.com
 */

namespace app\common\events;


class MessageEvent extends Event
{
    /**
     * 会员ID，需要发送消息的会员ID
     *
     * @var int
     */
    public $member_id;


    /**
     * 消息模版ID
     *
     * @var int
     */
    public $template_id;


    /**
     * 消息参数，需要替换的参数、对应参数值
     *
     * @var array
     */
    public $params;


    /**
     * 消息链接，消息跳转链接
     *
     * @var string
     */
    public $url;


    /**
     * 公众号ID （队列执行，需要记录公众号 ID）
     *
     * @var int
     */
    public $uniacid;



    public function __construct($member_id, $template_id, array $params, $url='')
    {
        $this->template_id = $template_id;


        $this->member_id = $member_id;


        $this->params = $params;


        $this->url = $url;


        $this->uniacid = \YunShop::app()->uniacid;
    }


}
