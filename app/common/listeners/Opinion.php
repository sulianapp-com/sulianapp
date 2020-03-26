<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 下午2:33
 */

namespace app\common\listeners;


class Opinion
{
    public $result;
    public $data;
    public $message;
    public $source;
    public function __construct($result,$message='',$data=[],$source='')
    {
        $this->result = $result;
        isset($message) && $this->message = $message;
        isset($data) && $this->data = $data;
        isset($source) && $this->source = $source;
    }
}