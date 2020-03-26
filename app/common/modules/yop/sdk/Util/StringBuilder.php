<?php

/**
 * Created by PhpStorm.
 * User: yp-tc-7176
 * Date: 17/7/17
 * Time: 11:42
 */

namespace app\common\modules\yop\sdk\Util;

class StringBuilder
{
    const LINE="<br/>";
    protected $list= array('');



    public function __construct( $str=NULL)
    {
        array_push($this->list,$str);

    }

    public function Append($str)
    {
        array_push($this->list,$str);
        return $this;
    }


    public function AppendLine($str)
    {
        array_push($this->list,$str.self::LINE);
        return $this;
    }


    public function AppendFormat($str, $args)
    {
        array_push($this->list, sprintf($str,$args));
        return $this;
    }


    public function ToString()
    {
        return implode("",$this->list);
    }


    public function __destruct()
    {
        unset($this->list);
    }
}