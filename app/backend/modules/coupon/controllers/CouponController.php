<?php
namespace app\backend\modules\coupon\controllers;

use app\backend\modules\coupon\models\HotelCoupon;
use app\common\components\BaseController;
use app\backend\modules\coupon\models\Coupon;
use app\common\helpers\Cache;
use app\common\helpers\PaginationHelper;
use app\common\models\MemberCoupon;
use app\common\helpers\Url;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\coupon\models\CouponLog;
use app\backend\modules\goods\models\Goods;
use app\backend\modules\goods\models\Category;
use app\common\facades\Setting;
use app\frontend\modules\coupon\listeners\CouponSend;
use Yunshop\Hotel\common\models\CouponHotel;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 16:20
 */
class CouponController extends BaseController
{
    //优惠券列表
    public function index()
    {
        $keyword = \YunShop::request()->keyword;
        $getType = \YunShop::request()->gettype;
        $timeSearchSwitch = \YunShop::request()->timesearchswtich;
        $timeStart = strtotime(\YunShop::request()->time['start']);
        $timeEnd = strtotime(\YunShop::request()->time['end']);

        $pageSize = 10;
        if (empty($keyword) && empty($getType) && ($timeSearchSwitch == 0)) {
            $list = Coupon::uniacid()->pluginId()->orderBy('display_order', 'desc')->orderBy('updated_at', 'desc')->paginate($pageSize)->toArray();
        } else {
            $list = Coupon::getCouponsBySearch($keyword, $getType, $timeSearchSwitch, $timeStart, $timeEnd)
                ->pluginId()
                ->orderBy('display_order', 'desc')
                ->orderBy('updated_at', 'desc')
                ->paginate($pageSize)
                ->toArray();
        }
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $list['per_page']);
        foreach ($list['data'] as &$item) {
            $item['gettotal'] = MemberCoupon::uniacid()->where("coupon_id", $item['id'])->count();
            $item['usetotal'] = MemberCoupon::uniacid()->where("coupon_id", $item['id'])->where("used", 1)->count();
            $lasttotal = $item['total'] - $item['gettotal'];
            $item['lasttotal'] = ($lasttotal > 0) ? $lasttotal : 0; //考虑到可领取总数修改成比之前的设置小, 则会变成负数
        }

        return view('coupon.index', [
            'list' => $list['data'],
            'pager' => $pager,
            'total' => $list['total'],
        ])->render();
    }

    //添加优惠券
    public function create()
    {
        //获取表单提交的值
        $couponRequest = \YunShop::request()->coupon;
        $couponRequest['uniacid'] = \YunShop::app()->uniacid;
        $couponRequest['time_start'] = strtotime(\YunShop::request()->time['start']);
        $couponRequest['time_end'] = strtotime(\YunShop::request()->time['end']);
        $couponRequest['category_ids'] = \YunShop::request()->category_ids;
        $couponRequest['categorynames'] = \YunShop::request()->category_names;
        $couponRequest['goods_ids'] = \YunShop::request()->goods_id ?: \YunShop::request()->goods_ids;
        $couponRequest['goods_names'] = \YunShop::request()->goods_name ?: \YunShop::request()->goods_names;

        //新增门店
        $couponRequest['storeids'] = \YunShop::request()->store_ids; //去重,去空值
        $couponRequest['storenames'] = \YunShop::request()->store_names;

        $hotel_is_open = app('plugins')->isEnabled('hotel');


        //获取会员等级列表
        $memberLevels = MemberLevel::getMemberLevelList();

        //获取优惠券统一的模板消息 ID (因为是统一的,所以写在 setting)
        //$template_id = Setting::get('coupon_template_id');

        //表单验证
        if ($_POST) {
            if (\YunShop::request()->goods_id) {
                if (count($couponRequest['goods_id']) > 1) {
                   return $this->message('优惠券创建失败,兑换券只能指定一个商品');
                }
            }
            $coupon = new HotelCoupon();
            if ($hotel_is_open) {
                $coupon->widgets['more_hotels'] = \YunShop::request()->hotel_ids;
            }
            $coupon->fill($couponRequest);
            $validator = $coupon->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } elseif ($coupon->save()) {
                //Setting::set('coupon_template_id', \YunShop::request()->template_id); //设置优惠券统一的模板消息ID
                return $this->message('优惠券创建成功', Url::absoluteWeb('coupon.coupon.index'));
            } else {
                $this->error('优惠券创建失败');
            }
        }

        return view('coupon.coupon', [
            'coupon' => $couponRequest,
            'memberlevels' => $memberLevels,
            'timestart' => strtotime(\YunShop::request()->time['start']),
            'timeend' => strtotime(\YunShop::request()->time['end']),
            'hotel_is_open' => $hotel_is_open
            //'template_id' => $template_id,
        ])->render();
    }

    //编辑优惠券
    public function edit()
    {
        $coupon_id = intval(\YunShop::request()->id);
        if (!$coupon_id) {
            $this->error('请传入正确参数.');
        }


        //获取会员等级列表
        $memberLevels = MemberLevel::getMemberLevelList();

        //获取优惠券统一的模板消息 ID (因为是统一的,所以写在 setting)
        //$template_id = Setting::get('coupon_template_id');

        $coupon = HotelCoupon::getCouponById($coupon_id);
        if (!empty($coupon->goods_ids)) {
            $coupon->goods_ids = array_filter(array_unique($coupon->goods_ids)); //去重,去空值
            if (!empty($coupon->goods_ids)) {
                $coupon->goods_names = Goods::getGoodNameByGoodIds($coupon->goods_ids); //因为商品名称可能修改,所以必须以商品表为准 //todo category_names和goods_names是不可靠的, 考虑删除这2个字段
            }
        }
        if (!empty($coupon->category_ids)) {
            $coupon->category_ids = array_filter(array_unique($coupon->category_ids)); //去重,去空值
            $coupon->categorynames = Category::getCategoryNameByIds($coupon->category_ids); //因为商品分类名称可能修改,所以必须以商品表为准
        }
        //新增酒店
        $hotel_is_open = app('plugins')->isEnabled('hotel');

        $couponRequest = \YunShop::request()->coupon;
        if ($couponRequest) {
            if ($couponRequest['use_type'] == 8) {
                if (count(\YunShop::request()->goods_id) > 1) {
                    return $this->message('优惠券修改失败,兑换券只能指定一个商品');
                }
                $goodsIds = \YunShop::request()->goods_id;
                $goodsNames = \YunShop::request()->goods_name;
            }
            $couponRequest['time_start'] = strtotime(\YunShop::request()->time['start']);
            $couponRequest['time_end'] = strtotime(\YunShop::request()->time['end']);
            $coupon->use_type = \YunShop::request()->usetype;
            $coupon->category_ids = array_filter(array_unique(\YunShop::request()->category_ids)); //去重,去空值
            $coupon->categorynames = \YunShop::request()->category_names;
            $coupon->goods_ids = $goodsIds ? $goodsIds : array_filter(array_unique(\YunShop::request()->goods_ids)); //去重,去空值
            $coupon->goods_names = $goodsNames ? $goodsNames : \YunShop::request()->goods_names;
            //新增门店
            $coupon->storeids = array_filter(array_unique(\YunShop::request()->store_ids)); //去重,去空值
            $coupon->storenames = \YunShop::request()->store_names;
            if ($hotel_is_open) {
                $coupon->widgets['more_hotels'] = \YunShop::request()->hotel_ids;
            }


            //表单验证
            $coupon->fill($couponRequest);
            $validator = $coupon->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($coupon->save()) {
                    //店铺装修清除缓存
                    if (app('plugins')->isEnabled('designer')) {
                        Cache::flush();//清除缓存
                    }
                    //Setting::set('coupon_template_id', \YunShop::request()->template_id); //设置优惠券统一的模板消息ID
                    return $this->message('优惠券修改成功', Url::absoluteWeb('coupon.coupon.index'));
                } else {
                    $this->error('优惠券修改失败');
                }
            }
        }

        return view('coupon.coupon', [
            'coupon' => $coupon->toArray(),
            'usetype' => $coupon->use_type,
            'category_ids' => $coupon->category_ids,
            'category_names' => $coupon->categorynames,
            'goods_ids' => $coupon->goods_ids,
            'goods_names' => $coupon->goods_names,
            'memberlevels' => $memberLevels,
            'timestart' => $coupon->time_start->timestamp,
            'timeend' => $coupon->time_end->timestamp,
            'hotel_is_open' => $hotel_is_open,
            'hotels' => $hotel_is_open ? CouponHotel::getHotels($coupon_id) : []
            //'template_id' => $template_id,
        ])->render();
    }

    //删除优惠券
    public function destory()
    {
        $coupon_id = intval(\YunShop::request()->id);
        if (!$coupon_id) {
            $this->error('请传入正确参数.');
        }

        $coupon = Coupon::getCouponById($coupon_id);
        if (!$coupon) {
            return $this->message('无此记录或者已被删除.', '', 'error');
        }

        $usageCount = Coupon::getUsageCount($coupon_id)->first()->toArray();
        if ($usageCount['has_many_member_coupon_count'] > 0) {
            return $this->message('优惠券已被领取且尚未使用,因此无法删除', Url::absoluteWeb('coupon.coupon'), 'error');
        }

        $res = HotelCoupon::deleteCouponById($coupon_id);
        if ($res) {
            //店铺装修清除缓存
            if (app('plugins')->isEnabled('designer')) {
                Cache::flush();//清除缓存
            }
            return $this->message('删除优惠券成功', Url::absoluteWeb('coupon.coupon'));
        } else {
            return $this->message('删除优惠券失败', '', 'error');
        }
    }


    /**
     * 获取搜索优惠券
     * @return html
     */
    public function getSearchCoupons()
    {
        $keyword = \YunShop::request()->keyword;
        $coupons = Coupon::getCouponsByName($keyword);
        return view('coupon.query', [
            'coupons' => $coupons
        ])->render();
    }

    /**
     * 获取搜索优惠券
     * @return html
     */
    public function getVueSearchCoupons()
    {
        $keyword = \YunShop::request()->keyword;
        $coupons = Coupon::getCouponsByName($keyword);
        echo json_encode($coupons);
    }

    //用于"适用范围"添加商品或者分类
    public function addParam()
    {
        $type = \YunShop::request()->type;
        switch ($type) {
            case 'goods':
                return view('coupon.tpl.goods')->render();
                break;
            case 'category':
                return view('coupon.tpl.category')->render();
                break;
            case 'store':
                return view('coupon.tpl.store')->render();
                break;
            case 'hotel':
                return view('coupon.tpl.hotel')->render();
                break;
            case 'goods-exchange':
                return view('coupon.tpl.exchange-goods')->render();
                break;
        }
    }

    //优惠券领取和发放记录
    public function log()
    {
        $couponId = \YunShop::request()->id;
        $couponName = \YunShop::request()->couponname;
        $nickname = \YunShop::request()->nickname;
        $getFrom = \YunShop::request()->getfrom;
        $searchSearchSwitch = \YunShop::request()->timesearchswtich;
        $timeStart = strtotime(\YunShop::request()->time['start']);
        $timeEnd = strtotime(\YunShop::request()->time['end']);

        if (empty($couponId) && empty($couponName) && ($getFrom == null) && empty($nickname) && ($searchSearchSwitch == 0)) {
            $list = CouponLog::getCouponLogs();
        } else {
            $searchData = [];
            if (!empty($couponId)) {
                $searchData['coupon_id'] = $couponId;
            }
            if (!empty($couponName)) {
                $searchData['coupon_name'] = $couponName;
            }
            if (!empty($nickname)) {
                $searchData['nickname'] = $nickname;
            }
            if ($getFrom != '') {
                $searchData['get_from'] = $getFrom;
            }
            if ($searchSearchSwitch == 1) {
                $searchData['time_search_swtich'] = $searchSearchSwitch;
                $searchData['time_start'] = $timeStart;
                $searchData['time_end'] = $timeEnd;
            }
            $list = CouponLog::searchCouponLog($searchData);
        }

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('coupon.log', [
            'list' => $list,
            'pager' => $pager,
            'couponid' => $couponId,
        ])->render();
    }

}