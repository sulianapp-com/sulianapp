<?php

namespace app\common\modules\yop\sdk;


/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/22
 * Time: 15:04
 */
class YopResponse{
    /**
     * 状态(SUCCESS/FAILURE)
     */

    public $state;

    /**
     * 业务结果，非简单类型解析后为LinkedHashMap
     */

    public $result;

    /**
     * 时间戳
     */
    public $ts;

    /**
     * 结果签名，签名算法为Request指定算法，示例：SHA(<secret>stringResult<secret>)
     */
    public $sign;

    /**
     * 错误信息
     */
    public $error;

    /**
     * 字符串形式的业务结果
     */
    public $stringResult;

    /**
     * 响应格式，冗余字段，跟Request的format相同，用于解析结果
     */
    public $format;

    /**
     * 业务结果签名是否合法，冗余字段
     */
    public $validSign;

    public $verSign;


    public function __set($name, $value){
        // TODO: Implement __set() method.
        $this->$name = $value;

    }
    public function __get($name){
        // TODO: Implement __get() method.
        return $this->$name;
    }

}