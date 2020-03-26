<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/18
 * Time: 下午5:56
 */

namespace app\common\events;


class GoodsWidget extends Event
{
    protected $widgets;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array &$widgets)
    {
        // pass array by reference
        $this->widgets = &$widgets;
    }

    public function addWidget($widget)
    {
        if ($widget) {
            if (!is_string($widget)) {
                throw new \Exception("Can not add non-string widget", 1);
            }

            $this->widgets[] = $widget;
        }
    }
}