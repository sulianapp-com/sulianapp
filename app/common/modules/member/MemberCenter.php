<?php


namespace app\common\modules\member;


class MemberCenter
{
    static $current;
    private $data;
    public function __construct()
    {
        static::$current = $this;
    }

    static public function current()
    {
        if(!isset(self::$current)){
            return new static();
        }
        return self::$current;
    }

    public function set($key,$value)
    {

        return $this->data[$key] = $value;

    }

    public function get($key)
    {
        return $this->data[$key];
    }
    public function all()
    {
        return $this->data;
    }
}