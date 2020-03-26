<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 23/02/2017
 * Time: 21:48
 */

namespace app\common\listeners;

class EventListener
{
    protected $event;

    /**
     * 添加反馈
     * @param Feedback $feedback
     * @return mixed
     */

    public function pushFeedback(Feedback $feedback){
        return $this->event->addFeedback($feedback);
    }

    /**
     * 提交意见
     * @param Opinion $opinion
     * @return mixed
     */
    public function sentOpinion(Opinion $opinion){
        return $this->event->setOpinion($opinion);
    }

}