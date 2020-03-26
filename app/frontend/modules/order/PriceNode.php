<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:41 AM
 */

namespace app\frontend\modules\order;

abstract class PriceNode
{
    protected $weight;

    public function __construct($weight)
    {
        $this->weight = $weight;
    }

    abstract public function getKey();

    abstract public function getPrice();

    public function getWeight()
    {
        return $this->weight;
    }
}