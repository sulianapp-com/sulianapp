<?php


namespace app\common\helpers;


class Serializer
{
    /**
     * @var \SuperClosure\Serializer
     */
    static $instance;

    /**
     * @param \Closure $closure
     * @return string
     */
    static public function serialize(\Closure $closure){
        if(!isset($instance)){
            self::$instance = new \SuperClosure\Serializer();
        }
        return self::$instance->serialize($closure);
    }
    /**
     * @param \Closure $closure
     * @return string
     */
    static public function unserialize($closure){
        if(!isset($instance)){
            self::$instance = new \SuperClosure\Serializer();
        }
        return self::$instance->unserialize($closure);
    }
}