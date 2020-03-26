<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/1/23
 * Time: 10:03 AM
 */

namespace app\frontend\modules\order;


use app\common\exceptions\AppException;
use Illuminate\Support\Collection;

trait PriceNodeTrait
{
    /**
     * @var Collection
     */
    public $priceCache;
    /**
     * @var Collection
     */
    private $priceNodes;

    /**
     * @return Collection
     */
    public function getPriceNodes()
    {
        if (!isset($this->priceNodes)) {
            $this->priceNodes = $this->_getPriceNodes();
        }
        return $this->priceNodes;
    }

    /**
     * 获取某个节点之后的价格
     * @param $key
     * @return mixed
     * @throws AppException
     */
    public function getPriceAfter($key)
    {
        if (!isset($this->priceCache[$key])) {
            // 找到对应的节点
            $priceNode = $this->getPriceNodes()->first(function (PriceNode $priceNode) use ($key) {
                return $priceNode->getKey() == $key;
            });
            if (!$priceNode) {
                throw new AppException("不存在的价格节点{$key}");
            }
            $this->priceCache[$key] = $priceNode->getPrice();
        }
        return $this->priceCache[$key];
    }

    public function getCurrentPrice()
    {
        if(!is_array($this->priceCache)){
            return $this->getPriceNodes()->first()->getPrice();
        }
        return array_last($this->priceCache);
    }

    /**
     * 获取某个节点之前的价格
     * @param $key
     * @return mixed
     * @throws AppException
     */
    public function getPriceBefore($key)
    {
        $nodeKey = '';

        foreach ($this->getPriceNodes() as $priceNode) {
            if ($priceNode->getKey() == $key) {
                break;
            }
            $nodeKey = $priceNode->getKey();
        }

        if (empty($nodeKey)) {
            throw new AppException("没有比{$key}更先计算的节点了");
        }
        return $this->getPriceAfter($nodeKey);
    }

    public function getPriceBeforeWeight($key)
    {
        $weight = 0;
        foreach ($this->getPriceNodes() as $priceNode) {
            if ($priceNode->getKey() == $key) {
                break;
            }
            $weight = $priceNode->getWeight();
        }

        $nodeKey = $this->getPriceNodes()->filter(function ($priceNode) use ($weight) {
            return $priceNode->getWeight() < $weight;
        })->last()->getKey();

        if (empty($nodeKey)) {
            throw new AppException("没有比{$weight}更权重更小节点了");
        }
        return $this->getPriceAfter($nodeKey);
    }
}