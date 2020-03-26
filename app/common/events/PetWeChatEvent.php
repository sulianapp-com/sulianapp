<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/6
 * Time: 9:50
 */

namespace app\common\events;

class PetWeChatEvent extends \app\common\events\Event
{


    /**
     * @var 用户信息
     */
    public $Info;

    function __construct($Info)
    {
        $this->info = $Info;
    }

    public function getInfo()
    {
        return $this->info;
    }




}