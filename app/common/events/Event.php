<?php

namespace app\common\events;

abstract class Event
{
    private $opinion = [];//只有一条
    private $feedback_list = [];//
    private $data = [];
    private $map = [];
    /**
     * 订单操作行为类获取插件反馈
     * @return mixed
     */
    public function getFeedbackList(){
        return $this->feedback_list;
    }
    /**
     * 订单操作行为类获取插件意见
     * @return mixed
     */
    public function getOpinion(){
        return $this->opinion;
    }
    public function getData(){
        return $this->data;
    }
    public function getMap(){
        return $this->map;
    }
    public function hasOpinion(){
        return (bool)count($this->opinion);
    }
    public function hasFeedback(){
        return (bool)count($this->feedback);

    }
    public function hasData(){
        return (bool)count($this->data);

    }
    /**
     * 监听者提交反馈
     * @param $feedback
     * @return bool
     */
    public function addFeedback($feedback){
        $this->feedback_list[] = $feedback;
        return true;
    }
    public function addMap($key,$data){
        $this->map[$key] = $data;
        return true;
    }
    public function addData($data){
        $this->data[] = $data;
        return true;
    }
    public function setData($data_list){
        $this->data = $data_list;
        return true;
    }
    /**
     * 监听者设置意见
     * @param $opinion
     * @return bool
     */
    public function setOpinion($opinion){
        $this->opinion = $opinion;
        return true;
    }

}
