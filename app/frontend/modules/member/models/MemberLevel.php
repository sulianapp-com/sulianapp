<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/8
 * Time: 上午11:54
 */


namespace app\frontend\modules\member\models;

use app\common\models\Goods;

class MemberLevel extends \app\common\models\MemberLevel
{

    protected $hidden = ['uniacid'];

    /**
     * 获取会员等级信息
     * @return array $data 等级升级的信息
     * 
     */
    public function getLevelData($type)
    {

        $content = ($type == 1) ? 'order_count' : 'order_money';

        $data = self::select('id', 'level_name', 'discount', 'freight_reduction', $content, 'description')
            ->uniacid()
            ->orderBy('level')
            ->get()->toArray();

        return $data;
    }

      /**
     * 等级升级依据为购买指定商品
     * @return array $data 等级升级的信息
     * 
     */
    public function getLevelGoods()
    {

        $data = self::select('id', 'level_name','goods_id', 'discount', 'freight_reduction', 'description')->uniacid()->orderBy('level')->get()->toArray();

        foreach ($data as $k => $v) {
            
            if ($v['goods_id']) {
                
                $goods_ids = array_unique(explode(',', $v['goods_id']));   
               
                foreach ($goods_ids as $key => $value) {
                    
                    $goods = Goods::where('uniacid', \YunShop::app()->uniacid)->where('id', $value)->select(['id', 'thumb', 'price', 'title'])->first();
                    $data[$k]['goods'][$key]['id'] = $goods['id'];
                    $data[$k]['goods'][$key]['thumb'] = yz_tomedia($goods['thumb']);
                    $data[$k]['goods'][$key]['price'] = $goods['price'];
                    $data[$k]['goods'][$key]['title'] = $goods['title'];
                }
            }
        }
        return $data;
    }

     //模型关联 关联商品
    public function goods()
    {
        return $this->hasOne('app\common\models\Goods', 'id', 'goods_id');
    }

    //关联会员
    public function member()
    {
        return $this->hasMany('app\common\models\MemberShopInfo', 'level_id', 'id'); //注意yz_member数据表记录和关联的是member_level表的主键id, 而不是level值
    }
}
