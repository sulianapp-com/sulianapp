<?php

namespace app\backend\modules\menu;

use app\common\exceptions\ShopException;
use app\common\helpers\Cache;
use app\common\services\PermissionService;

class Menu
{
    private $currentItems;
    private $items;
    private $pluginsMenu;
    private $mainMenu;
    /**
     * @var self
     */
    static $current;

    /**
     * todo route 改为参数
     * Menu constructor.
     * @throws ShopException
     */
    public function __construct()
    {
        self::$current = $this;
    }

    public function setPluginMenu($key, $value = null)
    {
        if (is_array($key)) {
            $this->pluginsMenu = array_merge($this->pluginsMenu, $key);
        } else {

            array_set($this->pluginsMenu, $key, $value);
        }

    }

    private function _getPluginMenus()
    {
        $plugins = app('plugins')->getEnabledPlugins();
        foreach ($plugins as $plugin) {
            $plugin->app()->loadMenuConfig();
        }
        return $this->pluginsMenu;
    }

    public function getPluginMenus()
    {
        if (!isset($this->pluginsMenu)) {
            $this->pluginsMenu = $this->_getPluginMenus();
        }

        return $this->pluginsMenu;
    }

    /**
     * @return Menu
     * @throws ShopException
     */
    public static function flush()
    {
        self::$current = new static();
        return self::current();
    }

    /**
     * @return menu
     * @throws \app\common\exceptions\ShopException
     */
    public static function current()
    {
        if (!isset(self::$current)) {
            return new static();
        }
        return self::$current;
    }

    /**
     * @return bool|mixed
     */
    public function isShowSecondMenu()
    {
        $menu_list = (array)$this->getItems();

        if (count($this->getCurrentItems()) >= 1) {
            return isset($menu_list[$this->getCurrentItems()[0]]['left_second_show']) ? $menu_list[$this->getCurrentItems()[0]]['left_second_show'] : false;
        }
        return false;
    }

    /**
     * 递归菜单项,设置can字段
     * @param array $menuList
     * @return array
     */
    public static function validateMenuPermit(array $menuList)
    {
        foreach ($menuList as $key => &$item) {
            $item['can'] = true;
            $item['permit'] && ($item['can'] = PermissionService::can($key));
            // 父菜单无权限时,不再验证子菜单权限
            if ($item['can'] && $item['child']) {
                $item['child'] = static::validateMenuPermit($item['child']);
            }
        }

        return $menuList;
    }

    private function mainMenu()
    {
        if (!isset($this->mainMenu)) {

            $this->mainMenu = $this->_mainMenu();
        }
        return $this->mainMenu;
    }

    private function _mainMenu()
    {
        return [
            'index' => [
                'name'             => '商城',
                'url'              => 'index.index',
                'urlParams'        => '',
                'permit'           => 0,
                'menu'             => 1,
                'icon'             => 'fa-home',
                'top_show'         => 0,
                'left_first_show'  => 0,
                'left_second_show' => 0,
                'parents'          => [],
                'item'             => 'index',
                'child'            => [

                    'index' => [
                        'name'      => '选择图标',
                        'url'       => 'frame.icon.index',
                        'urlParams' => '',
                        'permit'    => 0,
                        'menu'      => 0,
                        'icon'      => '',
                        'parents'   => [],
                    ],

                    'address_get_address' => [
                        'name'       => '白名单（选择地址）',
                        'url'        => 'address.get-address',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'address_get_address',
                        'parents'    => ['index'],
                    ],
                ],
            ],

//    'Survey' => [
//        'name'             => '概况',
//        'url'              => 'survey.survey.index',
//        'url_params'       => '',
//        'permit'           => 1,
//        'menu'             => 1,
//        'top_show'         => 0,
//        'left_first_show'  => 1,
//        'icon'             => 'fa-archive',
//        'sort'             => '0',
//        'item'             => 'Survey',
//        'parents'       => [],
//        'left_second_show' => 0,
//
//    ],

            'Goods' => [
                'name'             => '商品',
                'url'              => 'goods.goods',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'top_show'         => 0,
                'left_first_show'  => 1,
                'left_second_show' => 1,
                'icon'             => 'fa-archive',
                'sort'             => '2',
                'item'             => 'Goods',
                'parents'          => [],
                'child'            => [

                    //添加白名单
                    'goods_no_permission' => [
                        'name'       => '白名单（不控制权限）',
                        'url'        => '',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'goods_no_permission',
                        'parents'    => ['Goods', 'goods_dispatch',],
                        'child'      => [
                            'goods_search_order' => [
                                'name'       => '白名单（订单商品查询）',
                                'url'        => 'goods.goods.search-order',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_search_order',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            'goods_get_spec_tpl' => [
                                'name'       => '白名单（商品规格操作）',
                                'url'        => 'goods.goods.getSpecTpl',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_get_spec_tpl',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],

                            'goods_get_spec_item_tpl' => [
                                'name'       => '白名单（商品规格操作）',
                                'url'        => 'goods.goods.getSpecItemTpl',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_get_spec_item_tpl',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],

                            'area_area_select_city' => [
                                'name'       => '白名单（选择城市）',
                                'url'        => 'area.area.select-city',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'area_area_select_city',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],

                            'member_member_get_search_member' => [
                                'name'       => '白名单（选择通知人）',
                                'url'        => 'member.member.get-search-member',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_get_search_member',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],

                            'coupon_coupon_get_search_coupons' => [
                                'name'       => '白名单（选择优惠券）',
                                'url'        => 'coupon.coupon.get-search-coupons',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'coupon_coupon_get_search_coupons',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            //优惠券白名单
                            'coupon_no_permission'             => [
                                'name'       => '白名单（指定商品）',
                                'url'        => 'coupon.coupon.add-param',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'coupon_no_permission',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],

                            'goods_category_get_search_category' => [
                                'name'       => '白名单（选择分类）',
                                'url'        => 'goods.category.get-search-categorys',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'goods_category_get_search_category',
                                'parents'    => ['Goods', 'coupon',],
                            ],

                            'comment_no_permission'              => [
                                'name'       => '白名单（搜索商品）',
                                'url'        => 'goods.goods.get-search-goods',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'comment_no_permission',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            'goodsGetSearchGoodsLevel'           => [
                                'name'       => '白名单（搜索商品）',
                                'url'        => 'goods.goods.get-search-goods-level',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goodsGetSearchGoodsLevel',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            'goodsGetSearchGoodsByDividendLevel' => [
                                'name'       => '白名单（搜索商品）',
                                'url'        => 'goods.goods.get-search-goods-by-dividend-level',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goodsGetSearchGoodsByDividendLevel',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            'goods_goods_my_ling_goods'          => [
                                'name'       => '白名单（搜索商品）',
                                'url'        => 'goods.goods.getMyLinkGoods',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_my_ling_goods',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            'member_member_level_search_goods'   => [
                                'name'       => '白名单（搜索商品）',
                                'url'        => 'member.member-level.searchGoods',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_level_search_goods',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],
                            'goods_goods_getSearchGoodsLevel'    => [
                                'name'       => '白名单（推广搜索商品）',
                                'url'        => 'goods.goods.getSearchGoodsLevel',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_my_ling_goods',
                                'parents'    => ['Goods', 'goods_no_permission',],
                            ],

                        ],
                    ],
                    'add_goods'           => [
                        'name'       => '发布商品',
                        'url'        => 'goods.goods.create',
                        'url_params' => '',
                        'permit'     => 1,
                        'style'      => 'pulish',
                        'menu'       => 1,
                        'icon'       => 'fa-cubes',
                        'sort'       => 0,
                        'item'       => 'goods_goods',
                        'parents'    => ['Goods',],
                        'child'      => [

                        ],
                    ],
                    'goods_goods'         => [
                        'name'       => '商品列表',
                        'url'        => 'goods.goods.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-cubes',
                        'sort'       => 0,
                        'item'       => 'goods_goods',
                        'parents'    => ['Goods',],
                        'child'      => [
                            'goods_goods_see'           => [
                                'name'       => '浏览列表',
                                'url'        => 'goods.goods.goods-list',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '22',
                                'item'       => 'goods_goods_see',
                                'parents'    => ['Goods', 'goods_goods'],
                            ],
                            'goods_goods_add'           => [
                                'name'       => '添加商品',
                                'url'        => 'goods.goods.create',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'goods_goods_add',
                                'parents'    => ['Goods', 'goods_goods'],
                            ],
                            'goods_goods_edit'          => [
                                'name'       => '编辑商品',
                                'url'        => 'goods.goods.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_edit',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],
                            'goods_goods_destroy'       => [
                                'name'       => '删除商品',
                                'url'        => 'goods.goods.destroy',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_destroy',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],
                            'goods_goods_copy'          => [
                                'name'       => '复制商品',
                                'url'        => 'goods.goods.copy',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_copy',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],
                            'goods_goods_putaway'       => [
                                'name'       => '商品上架',
                                'url'        => 'goods.goods.setPutaway',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_putaway',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],
                            'goods_goods_property'      => [
                                'name'       => '快捷属性',
                                'url'        => 'goods.goods.setProperty',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_property',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],
                            'goods_goods_display_order' => [
                                'name'       => '修改排序',
                                'url'        => 'goods.goods.displayorder',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-circle',
                                'sort'       => '23',
                                'item'       => 'goods_goods_display_order',
                                'parents'    => ['Goods', 'goods_goods'],
                            ],
                            'goods_goods_batch_destroy' => [
                                'name'       => '批量删除',
                                'url'        => 'goods.goods.batchDestroy',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_batch_destroy',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],

                            'goods_goods_import' => [
                                'name'       => 'excel导入商品',
                                'url'        => 'goods.goods.import',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_import',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],

                            'goods_goods_batch_property' => [
                                'name'       => '批量上下架',
                                'url'        => 'goods.goods.batchSetProperty',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_batch_property',
                                'parents'    => ['Goods', 'goods_goods',],
                            ],
                            'goods_goods_change'         => [
                                'name'       => '列表快捷操作',
                                'url'        => 'goods.goods.change',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_goods_change',
                                'parents'    => ['Goods', 'goods_goods'],
                            ],
                            'goods_goods_search'         => [
                                'name'       => '列表快捷操作',
                                'url'        => 'goods.goods.goods-search',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'goods_goods_search',
                                'parents'    => ['Goods', 'goods_goods'],
                            ],
                        ],
                    ],

                    'goods_div_from' => [
                        'name'       => '商品表单',
                        'url'        => 'from.div-from.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-file-excel-o',
                        'sort'       => '2',
                        'item'       => 'goods_div_from',
                        'parents'    => ['Goods',],
                        'child'      => [
                            'goods_div_from_see' => [
                                'name'       => '查看内容',
                                'url'        => 'from.div-from.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'goods_div_from_see_one',
                                'parents'    => ['Goods', 'goods_div_from'],
                                'child'      => [
                                    'goods_div_from_see'   => [
                                        'name'       => '查看内容',
                                        'url'        => 'from.div-from.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => '2',
                                        'item'       => 'goods_div_from_see',
                                        'parents'    => ['Goods', 'goods_div_from', 'goods_div_from_see_one'],
                                    ],
                                    'goods_div_from_store' => [
                                        'name'       => '保存设置',
                                        'url'        => 'from.div-from.store',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => '2',
                                        'item'       => 'goods_div_from_store',
                                        'parents'    => ['Goods', 'goods_div_from', 'goods_div_from_see_one'],
                                    ],
                                ],
                            ],

                        ],
                    ],

                    'goods_category' => [
                        'name'       => '商品分类',
                        'url'        => 'goods.category.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-sitemap',
                        'sort'       => '2',
                        'item'       => 'goods_category',
                        'parents'    => ['Goods',],
                        'child'      => [

                            'goods_category_see' => [
                                'name'       => '浏览分类',
                                'url'        => 'goods.category.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-plus',
                                'sort'       => 0,
                                'item'       => 'goods_category_see',
                                'parents'    => ['Goods', 'goods_category',],
                            ],

                            'goods_category_add' => [
                                'name'       => '添加分类',
                                'url'        => 'goods.category.add-category',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-plus',
                                'sort'       => 0,
                                'item'       => 'goods_category_add',
                                'parents'    => ['Goods', 'goods_category',],
                            ],

                            'goods_category_edit' => [
                                'name'       => '修改分类',
                                'url'        => 'goods.category.edit-category',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-edit',
                                'sort'       => '2',
                                'item'       => 'goods_category_edit',
                                'parents'    => ['Goods', 'goods_category',]
                            ],

                            'goods_category_delete' => [
                                'name'       => '删除分类',
                                'url'        => 'goods.category.deleted-category',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-sliders',
                                'sort'       => '3',
                                'item'       => 'goods_category_delete',
                                'parents'    => ['Goods', 'goods_category',],
                            ],
                        ],
                    ],

                    'goods_brand' => [
                        'name'       => '品牌管理',
                        'url'        => 'goods.brand.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-briefcase',
                        'sort'       => '3',
                        'item'       => 'goods_brand',
                        'parents'    => ['Goods', 'goods_brand'],
                        'child'      => [
                            'goods_brand_see' => [
                                'name'       => '浏览品牌',
                                'url'        => 'goods.brand.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '3',
                                'item'       => 'goods_brand',
                                'parents'    => ['Goods',],
                            ],

                            'goods_brand_add' => [
                                'name'       => '添加品牌',
                                'url'        => 'goods.brand.add',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_brand_add',
                                'parents'    => ['Goods', 'goods_brand',],
                            ],

                            'goods_brand_edit' => [
                                'name'       => '修改品牌',
                                'url'        => 'goods.brand.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'goods_brand_edit',
                                'parents'    => ['Goods', 'goods_brand',],
                            ],

                            'goods_brand_delete' => [
                                'name'       => '删除品牌',
                                'url'        => 'goods.brand.deleted-brand',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '3',
                                'item'       => 'goods_brand_delete',
                                'parents'    => ['Goods', 'goods_brand',],
                            ],
                        ],
                    ],

                    'goods_dispatch' => [
                        'name'       => '配送模板',
                        'url'        => 'goods.dispatch.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-truck',
                        'sort'       => '4',
                        'item'       => 'goods_dispatch.index',
                        'parents'    => ['Goods',],
                        'child'      => [

                            'goods_dispatch_see' => [
                                'name'       => '浏览列表',
                                'url'        => 'goods.dispatch.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '4',
                                'item'       => 'goods_dispatch_see',
                                'parents'    => ['Goods', 'goods_dispatch']
                            ],

                            'goods_dispatch_sort' => [
                                'name'       => '修改排序',
                                'url'        => 'goods.dispatch.sort',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '4',
                                'item'       => 'goods_dispatch_sort',
                                'parents'    => ['Goods', 'goods_dispatch']
                            ],

                            'goods_dispatch_add_one' => [
                                'name'       => '添加模板',
                                'url'        => 'goods.dispatch.add',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_dispatch_add_one',
                                'parents'    => ['Goods', 'goods_dispatch',],
                            ],

                            'goods_dispatch_alter' => [
                                'name'       => '修改模板',
                                'url'        => 'goods.dispatch.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_dispatch_alter',
                                'parents'    => ['Goods', 'goods_dispatch',],
                            ],

                            'goods_dispatch_delete' => [
                                'name'       => '删除模板',
                                'url'        => 'goods.dispatch.delete',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_dispatch_delete',
                                'parents'    => ['Goods', 'goods_dispatch',],
                            ],
                        ],
                    ],

                    'comment' => [
                        'name'       => '评论管理',
                        'url'        => 'goods.comment.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-columns',
                        'sort'       => '5',
                        'item'       => 'comment',
                        'parents'    => ['Goods',],
                        'child'      => [

                            'goods_comment_add' => [
                                'name'       => '添加评价',
                                'url'        => 'goods.comment.add-comment',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_comment_add',
                                'parents'    => ['Goods', 'comment',],
                            ],


                            'goods_comment_updated' => [
                                'name'       => '修改评价',
                                'url'        => 'goods.comment.updated',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_comment_updated',
                                'parents'    => ['Goods', 'comment',],
                            ],

                            'goods_comment_reply' => [
                                'name'       => '回复评价',
                                'url'        => 'goods.comment.reply',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_comment_reply',
                                'parents'    => ['Goods', 'comment',],
                            ],

                            'goods_comment_update' => [
                                'name'       => '修改评价',
                                'url'        => 'goods.comment.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_comment_update',
                                'parents'    => ['Goods', 'comment',],
                            ],

                            'goods_comment_delete' => [
                                'name'       => '删除评价',
                                'url'        => 'goods.comment.deleted',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_comment_delete',
                                'parents'    => ['Goods', 'comment',],
                            ],
                        ],
                    ],

                    'coupon'           => [
                        'name'       => '优惠券管理',
                        'url'        => 'coupon.coupon.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-tags',
                        'sort'       => '6',
                        'item'       => 'coupon',
                        'parents'    => ['Goods',],
                        'child'      => [

                            'coupon_coupon_set' => [
                                'name'       => '优惠券设置',
                                'url'        => 'coupon.base-set.see',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-ticket',
                                'sort'       => '2',
                                'item'       => 'coupon_coupon_set',
                                'parents'    => ['Goods', 'coupon'],
                                'child'      => [

                                    'coupon_coupon_set_see' => [
                                        'name'       => '查看设置',
                                        'url'        => 'coupon.base-set.see',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-ticket',
                                        'sort'       => '2',
                                        'item'       => 'coupon_coupon_set_see',
                                        'parents'    => ['Goods', 'coupon', 'coupon_coupon_set'],
                                    ],

                                    'coupon_coupon_set_store' => [
                                        'name'       => '保存设置',
                                        'url'        => 'coupon.base-set.store',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-ticket',
                                        'sort'       => '2',
                                        'item'       => 'coupon_coupon_set_store',
                                        'parents'    => ['Goods', 'coupon', 'coupon_coupon_set'],
                                    ],

                                    'coupon_notice_set_see' => [
                                        'name'       => '通知开启',
                                        'url'        => 'setting.default-notice.store',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-ticket',
                                        'sort'       => '2',
                                        'item'       => 'coupon_notice_set_see',
                                        'parents'    => ['Goods', 'coupon', 'coupon_coupon_set'],
                                    ],

                                    'coupon_notice_set_close' => [
                                        'name'       => '通知关闭',
                                        'url'        => 'setting.default-notice.storeCancel',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-ticket',
                                        'sort'       => '2',
                                        'item'       => 'coupon_notice_set_close',
                                        'parents'    => ['Goods', 'coupon', 'coupon_coupon_set'],
                                    ],
                                ],
                            ],

                            'coupon_coupon_create' => [
                                'name'       => '创建优惠券',
                                'url'        => 'coupon.coupon.create',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-ticket',
                                'sort'       => '2',
                                'item'       => 'coupon_coupon_create',
                                'parents'    => ['Goods', 'coupon',],
                            ],

                            'coupon_coupon_edit' => [
                                'name'       => '编辑优惠券',
                                'url'        => 'coupon.coupon.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'coupon_coupon_edit',
                                'parents'    => ['Goods', 'coupon',],
                            ],

                            'coupon_coupon_destroy' => [
                                'name'       => '删除优惠券',
                                'url'        => 'coupon.coupon.destory',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'coupon_coupon_destory',
                                'parents'    => ['Goods', 'coupon',],
                            ],

                            'coupon_send_coupon' => [
                                'name'       => '发放优惠券',
                                'url'        => 'coupon.send-coupon.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'coupon_send_coupon',
                                'parents'    => ['Goods', 'coupon'],
                            ],

                            'coupon_coupon_index' => [
                                'name'       => '优惠券列表',
                                'url'        => 'coupon.coupon.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-list-ul',
                                'sort'       => 1,
                                'item'       => 'coupon_coupon_index',
                                'parents'    => ['Goods', 'coupon',],
                            ],

                            'coupon_coupon_log' => [
                                'name'       => '领取发放记录',
                                'url'        => 'coupon.coupon.log',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-pencil',
                                'sort'       => '3',
                                'item'       => 'coupon_coupon_log',
                                'parents'    => ['Goods', 'coupon',],
                            ],
                            'share_coupon_log'  => [
                                'name'       => '分享领取记录',
                                'url'        => 'coupon.share-coupon.log',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-pencil',
                                'sort'       => '3',
                                'item'       => 'share_coupon_log',
                                'parents'    => ['Goods', 'coupon',],
                            ],
                            'getSearchStore'    => [
                                'name'       => '搜索门店',
                                'url'        => 'goods.goods.get-search-store',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-pencil',
                                'sort'       => '3',
                                'item'       => 'coupon_coupon_log',
                                'parents'    => ['Goods', 'coupon',],
                            ],
                            'getSearchHotel'    => [
                                'name'       => '搜索酒店',
                                'url'        => 'goods.goods.get-search-hotel',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-pencil',
                                'sort'       => '3',
                                'item'       => 'coupon_coupon_log',
                                'parents'    => ['Goods', 'coupon',],
                            ],
                        ],
                    ],

                    /**
                     * 搜索过滤 改名为 商品标签
                     * create 2018/3/26
                     * update 2018/5/14
                     * Author: blank
                     */
                    'search_filtering' => [
                        'name'       => '商品标签',
                        'url'        => 'filtering.filtering.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-sitemap',
                        'sort'       => '6',
                        'item'       => 'search_filtering',
                        'parents'    => ['Goods',],
                        'child'      => [
                            'filtering_search'      => [
                                'name'       => '标签组列表',
                                'url'        => 'filtering.filtering.get-search-label',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => 'fa-sitemap',
                                'sort'       => '2',
                                'item'       => 'filtering_search',
                                'parents'    => ['Goods', 'search_filtering'],
                                'child'      => []
                            ],
                            'filtering_group_index' => [
                                'name'       => '标签组列表',
                                'url'        => 'filtering.filtering.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-sitemap',
                                'sort'       => '2',
                                'item'       => 'filtering_group_index',
                                'parents'    => ['Goods', 'search_filtering'],
                                'child'      => []
                            ],
                            'filtering_value_index' => [
                                'name'       => '标签列表',
                                'url'        => 'filtering.filtering.filter-value',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'filtering_value_index',
                                'parents'    => ['Goods', 'search_filtering'],
                                'child'      => []
                            ],
                            'filtering_create'      => [
                                'name'       => '新增',
                                'url'        => 'filtering.filtering.create',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '4',
                                'item'       => 'goods_return_sort',
                                'parents'    => ['Goods', 'search_filtering']
                            ],

                            'filtering_edit' => [
                                'name'       => '编辑',
                                'url'        => 'filtering.filtering.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_return_add_one',
                                'parents'    => ['Goods', 'search_filtering',],
                            ],

                            'filtering_del' => [
                                'name'       => '删除',
                                'url'        => 'filtering.filtering.del',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_return_alter',
                                'parents'    => ['Goods', 'search_filtering',],
                            ],
                        ],
                    ],

                    'goods_return'  => [
                        'name'       => '退货地址设置',
                        'url'        => 'goods.return-address.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-truck',
                        'sort'       => '6',
                        'item'       => 'coupon',
                        'parents'    => ['Goods',],
                        'child'      => [
                            'goods_return_see' => [
                                'name'       => '浏览列表',
                                'url'        => 'goods.return-address.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '4',
                                'item'       => 'goods_return_see',
                                'parents'    => ['Goods', 'goods_return']
                            ],

                            'goods_return_sort' => [
                                'name'       => '修改排序',
                                'url'        => 'goods.return-address.sort',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '4',
                                'item'       => 'goods_return_sort',
                                'parents'    => ['Goods', 'goods_return']
                            ],

                            'goods_return_add_one' => [
                                'name'    => '添加模板',
                                'url'     => 'goods.return-address.add',
                                'sort'    => '2',
                                'item'    => 'filtering_create',
                                'parents' => ['Goods', 'goods_return'],
                                'child'   => []
                            ],
                            'goods_return_alter'   => [
                                'name'    => '修改模板',
                                'url'     => 'goods.return-address.edit',
                                'sort'    => '2',
                                'item'    => 'filtering_edit',
                                'parents' => ['Goods', 'goods_return'],
                                'child'   => []
                            ],
                            'goods_return_delete'  => [
                                'name'       => '删除模板',
                                'url'        => 'goods.return-address.delete',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'goods_return_delete',
                                'parents'    => ['Goods', 'goods_return',],
                            ],
                        ],

                    ],
                    'enough_reduce' => [
                        'name'       => '满额优惠',
                        'url'        => 'enoughReduce.index.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-gift',
                        'sort'       => '6',
                        'item'       => 'enough_reduce',
                        'parents'    => ['Goods',],
                        'child'      => [
                            'filtering_group_index' => [
                                'name'       => '满额优惠设置',
                                'url'        => 'enoughReduce.index.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-sitemap',
                                'sort'       => '2',
                                'item'       => 'enough_reduce_index',
                                'parents'    => ['Goods'],
                                'child'      => []
                            ],
                            'filtering_group_store' => [
                                'name'       => '保存满额优惠',
                                'url'        => 'enoughReduce.store.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-sitemap',
                                'sort'       => '2',
                                'item'       => 'enough_reduce_store',
                                'parents'    => ['Goods'],
                                'child'      => []
                            ],
                            'area_list'             => [
                                'name'       => '选择地区',
                                'url'        => 'area.list.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-sitemap',
                                'sort'       => '2',
                                'item'       => 'area_list',
                                'parents'    => ['Goods'],
                                'child'      => []
                            ],
                        ],
                    ],

                    'discount_set' => [
                        'name'       => '批量操作',
                        'url'        => 'discount.batch-discount.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-gift',
                        'sort'       => '6',
                        'item'       => 'discount_set',
                        'parents'    => ['Goods',],
                        'child'      => [
                            'goods_discount_set_all' => [
                                'name'       => '折扣全局设置',
                                'url'        => 'discount.batch-discount.allSet',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'goods_discount_set_all',
                                'parents'    => ['Goods', 'discount_set',],
                                'child'      => [
                                    'goods_discount_set_all_index'  => [
                                        'name'       => '折扣设置',
                                        'url'        => 'discount.batch-discount.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_discount_set_all_index',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set_all'],
                                        'child'      => []
                                    ],
                                    'goods_discount_set_all__store' => [
                                        'name'       => '保存设置',
                                        'url'        => 'discount.batch-discount.store',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_discount_set_all__store',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set_all'],
                                        'child'      => []
                                    ],
                                    'goods_discount_set_all_set'    => [
                                        'name'       => '保存折扣全局设置',
                                        'url'        => 'discount.batch-discount.all-set',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_discount_set_all_set',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set_all'],
                                        'child'      => []
                                    ],
                                ],
                            ],

                            'goods_discount_set' => [
                                'name'       => '折扣设置',
                                'url'        => 'discount.batch-discount.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'goods_discount_set',
                                'parents'    => ['Goods', 'discount_set',],
                                'child'      => [
                                    'goods_discount_set_store'       => [
                                        'name'       => '保存设置',
                                        'url'        => 'discount.batch-discount.store',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_discount_set_store',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set'],
                                        'child'      => []
                                    ],
                                    'goods_discount_set_edit'        => [
                                        'name'       => '编辑设置',
                                        'url'        => 'discount.batch-discount.update-set',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_discount_set_edit',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set'],
                                        'child'      => []
                                    ],
                                    'goods_discount_set'             => [
                                        'name'       => '编辑页面',
                                        'url'        => 'discount.discount.set',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_discount_set_update',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set'],
                                        'child'      => []
                                    ],
                                    'goods_dispatch_select_category' => [
                                        'name'       => '分类查询',
                                        'url'        => 'discount.batch-discount.select-category',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_select_category',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set'],
                                        'child'      => []
                                    ],
                                    'goods_dispatch_delete_set'      => [
                                        'name'       => '删除设置',
                                        'url'        => 'discount.batch-discount.delete-set',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_delete_set',
                                        'parents'    => ['Goods', 'discount_set', 'goods_discount_set'],
                                        'child'      => []
                                    ],
                                ],
                            ],

                            'goods_dispatch_freight' => [
                                'name'       => '运费批量设置',
                                'url'        => 'discount.batch-dispatch.freight',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '2',
                                'item'       => 'goods_dispatch_freight-set',
                                'parents'    => ['Goods', 'discount_set',],
                                'child'      => [
                                    'goods_dispatch_set_freight'     => [
                                        'name'       => '折扣设置',
                                        'url'        => 'discount.batch-dispatch.freight-set',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_set_freight',
                                        'parents'    => ['Goods', 'discount_set', 'goods_dispatch_freight'],
                                        'child'      => []
                                    ],
                                    'goods_dispatch_select_category' => [
                                        'name'       => '分类查询',
                                        'url'        => 'discount.batch-dispatch.select-category',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_select_category',
                                        'parents'    => ['Goods', 'discount_set', 'goods_dispatch_freight'],
                                        'child'      => []
                                    ],
                                    'goods_dispatch_freight_save'    => [
                                        'name'       => '运费设置',
                                        'url'        => 'discount.batch-dispatch.freight-save',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_freight_save',
                                        'parents'    => ['Goods', 'discount_set', 'goods_dispatch_freight'],
                                        'child'      => []
                                    ],
                                    'goods_dispatch_update_freight'  => [
                                        'name'       => '修改设置',
                                        'url'        => 'discount.batch-dispatch.update-freight',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_update_freight',
                                        'parents'    => ['Goods', 'discount_set', 'goods_dispatch_freight'],
                                        'child'      => []
                                    ],
                                    'goods_dispatch_freight_delete'  => [
                                        'name'       => '删除设置',
                                        'url'        => 'discount.batch-dispatch.delete-freigh',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-sitemap',
                                        'sort'       => '2',
                                        'item'       => 'goods_dispatch_freight_delete-save',
                                        'parents'    => ['Goods', 'discount_set', 'goods_dispatch_freight'],
                                        'child'      => []
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            'Member' => [
                'name'             => '会员',
                'url'              => 'member.member',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'top_show'         => 0,
                'left_first_show'  => 1,
                'left_second_show' => 1,
                'icon'             => 'fa-users',
                'sort'             => '3',
                'item'             => 'Member',
                'parents'          => [],
                'child'            => [

                    'member_search' => [
                        'name'       => '搜索会员',
                        'url'        => 'member.member.search_member',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => 0,
                        'item'       => 'member_search',
                        'parents'    => ['Member',],
                    ],

                    'member_all' => [
                        'name'       => '全部会员',
                        'url'        => 'member.member.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-users',
                        'sort'       => 0,
                        'item'       => 'member_all',
                        'parents'    => ['Member'],
                        'child'      => [

                            'member_see' => [
                                'name'       => '浏览列表',
                                'url'        => 'member.member.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_see',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_add' => [
                                'name'       => '添加会员',
                                'url'        => 'member.member.add-member',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_add',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_import' => [
                                'name'       => '会员excel导入',
                                'url'        => 'member.member.import',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_import',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_export' => [
                                'name'       => '会员导出',
                                'url'        => 'member.member.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_export',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_detail' => [
                                'name'       => '查看详情',
                                'url'        => 'member.member.detail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_detail',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_income' => [
                                'name'       => '收入详情',
                                'url'        => 'member.member-income.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_income',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_detail_update' => [
                                'name'       => '修改信息',
                                'url'        => 'member.member.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_detail_update',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_change_relation' => [
                                'name'       => '修改关系',
                                'url'        => 'member.member.change_relation',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_change_relation',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_member_agent_old' => [
                                'name'       => '推广下线',
                                'url'        => 'member.member.agent-old',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_agent_old',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_member_agent' => [
                                'name'       => '团队下线',
                                'url'        => 'member.member.agent',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_agent',
                                'parents'    => ['Member', 'member_all',],
                                'child'      => [
                                    'member_agent_export' => [
                                        'name'       => '团队下线导出',
                                        'url'        => 'member.member.agentExport',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 0,
                                        'item'       => 'member_agent_export',
                                        'parents'    => ['Member', 'member_all', 'member_member_agent'],
                                    ],
                                ]
                            ],

                            'member_member_agent_parent' => [
                                'name'       => '推广上线',
                                'url'        => 'member.member.agent-parent',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_agent_parent',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_member_black' => [
                                'name'       => '加入黑名单',
                                'url'        => 'member.member.black',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_black',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_member_delete' => [
                                'name'       => '删除会员',
                                'url'        => 'member.member.delete',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_delete',
                                'parents'    => ['Member', 'member_all',],
                            ],

                            'member_bank_card'      => [
                                'name'       => '银行卡管理',
                                'url'        => 'member.bank-card.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_bank_card',
                                'parents'    => ['Member', 'member_all',],
                            ],
                            'member_member_address' => [
                                'name'       => '收货地址',
                                'url'        => 'member.member-address.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_address',
                                'parents'    => ['Member', 'member_all',],
                            ],
                        ],
                    ],

                    'member_level' => [
                        'name'       => '会员等级',
                        'url'        => 'member.member-level.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-sort-amount-asc',
                        'sort'       => 0,
                        'item'       => 'member_level',
                        'parents'    => ['Member',],
                        'child'      => [

                            'member_member_level_see' => [
                                'name'       => '添加会员等级',
                                'url'        => 'member.member-level.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_level_see',
                                'parents'    => ['Member', 'member_level',],
                            ],

                            'member_member_level_store' => [
                                'name'       => '添加会员等级',
                                'url'        => 'member.member-level.store',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-plus',
                                'sort'       => 0,
                                'item'       => 'member_member_level_store',
                                'parents'    => ['Member', 'member_level',],
                            ],

                            'member_member_level_update' => [
                                'name'       => '编辑会员等级',
                                'url'        => 'member.member-level.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-edit',
                                'sort'       => 0,
                                'item'       => 'member_member_level_update',
                                'parents'    => ['Member', 'member_level',],
                            ],

                            'member_member_level_destroy' => [
                                'name'       => '删除会员等级',
                                'url'        => 'member.member-level.destroy',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-remove',
                                'sort'       => 0,
                                'item'       => 'member_member_level_destroy',
                                'parents'    => ['Member', 'member_level',],
                            ],
                        ],
                    ],

                    'member_group' => [
                        'name'       => '会员分组',
                        'url'        => 'member.member-group.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-sort-alpha-asc',
                        'sort'       => 0,
                        'item'       => 'member_group',
                        'parents'    => ['Member',],
                        'child'      => [
                            'member_member_group_look'  => [
                                'name'       => '查看分组会员',
                                'url'        => 'member.member.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_member_group_look',
                                'parents'    => ['Member', 'member_group',],
                            ],
                            'member_member_group_store' => [
                                'name'       => '添加会员分组',
                                'url'        => 'member.member-group.store',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-plus',
                                'sort'       => 0,
                                'item'       => 'member_member_group_store',
                                'parents'    => ['Member', 'member_group',],
                            ],

                            'member_member_group_update' => [
                                'name'       => '修改会员分组',
                                'url'        => 'member.member-group.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-pencil-square-o',
                                'sort'       => 0,
                                'item'       => 'member_member_group_update',
                                'parents'    => ['Member', 'member_group',],
                            ],

                            'member_member_group_destroy' => [
                                'name'       => '删除会员分组',
                                'url'        => 'member.member-group.destroy',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-remove',
                                'sort'       => 0,
                                'item'       => 'member_member_group_destroy',
                                'parents'    => ['Member', 'member_group',],
                            ],

                        ],

                    ],


                    'user_relation' => [
                        'name'       => '关系设置',
                        'url'        => 'member.member-relation.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-sliders',
                        'sort'       => 0,
                        'item'       => 'user_relation',
                        'parents'    => ['Member',],
                        'child'      => [

                            'user_no_permission' => [
                                'name'       => '搜索商品',
                                'url'        => 'member.member-relation.query',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'user_no_permission',
                                'parents'    => ['Member', 'user_relation'],
                            ],

                            'user_relation_see' => [
                                'name'       => '查看修改',
                                'url'        => 'member.member-relation.save',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'user_relation_see',
                                'parents'    => ['Member', 'user_relation'],
                            ],
                        ],
                    ],

                    'member_agent_apply'   => [
                        'name'       => '资格申请',
                        'url'        => 'member.member-relation.apply',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-sliders',
                        'sort'       => 0,
                        'item'       => 'member_agent_apply',
                        'parents'    => ['Member', 'member_relation',],
                        'child'      => [

                            'agent_apply_chkApply' => [
                                'name'       => '资格审核',
                                'url'        => 'member.member-relation.chkApply',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-sliders',
                                'sort'       => 0,
                                'item'       => 'agent_apply_chkApplye',
                                'parents'    => ['Member', 'member_relation', 'agent_apply'],
                            ],

                            'agent_apply_export' => [
                                'name'       => '导出申请',
                                'url'        => 'member.member-relation.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-sliders',
                                'sort'       => 0,
                                'item'       => 'agent_apply_export',
                                'parents'    => ['Member', 'member_relation', 'agent_apply'],
                            ]
                        ],
                    ],
                    'relation_base'        => [
                        'name'       => '会员设置',
                        'url'        => 'member.member-relation.base',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-circle-o',
                        'sort'       => 0,
                        'item'       => 'relation_base',
                        'parents'    => ['Member',],
                        'child'      => [

                            'relation_base_save' => [
                                'name'       => '查看修改',
                                'url'        => 'member.member.member_record',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-circle-o',
                                'sort'       => 0,
                                'item'       => 'relation_base',
                                'parents'    => ['Member', 'relation_base',],
                            ],
                        ],
                    ],
                    'popularize_page_show' => [
                        'name'             => '推广中心设置',
                        'url'              => 'member.popularize-page-show.wechat-set',
                        'url_params'       => '',
                        'permit'           => 1,
                        'menu'             => 1,
                        'top_show'         => 1,               //顶部导航是否显示
                        'left_first_show'  => 1,           //左侧一级导航是否显示
                        'left_second_show' => 1,
                        'icon'             => '',
                        'sort'             => 0,
                        'item'             => 'popularize_page_show',
                        'parents'          => ['Member'],
                        'child'            => [
                            'popularize_wechat_set' => [
                                'name'       => '微信公众号',
                                'url'        => 'member.popularize-page-show.wechat-set',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'popularize_wechat_set',
                                'parents'    => ['Member', 'popularize_page_show'],
                            ],
                            'popularize_mini_set'   => [
                                'name'       => '微信小程序',
                                'url'        => 'member.popularize-page-show.mini-set',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'popularize_mini_set',
                                'parents'    => ['Member', 'popularize_page_show'],
                            ],
                            'popularize_wap_set'    => [
                                'name'       => 'wap',
                                'url'        => 'member.popularize-page-show.wap-set',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'popularize_wap_set',
                                'parents'    => ['Member', 'popularize_page_show'],
                            ],
                            'popularize_app_set'    => [
                                'name'       => 'APP',
                                'url'        => 'member.popularize-page-show.app-set',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'popularize_app_set',
                                'parents'    => ['Member', 'popularize_page_show'],
                            ],
                            'popularize_alipay_set' => [
                                'name'       => '支付宝',
                                'url'        => 'member.popularize-page-show.alipay-set',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'popularize_alipay_set',
                                'parents'    => ['Member', 'popularize_page_show'],
                            ],
                        ]
                    ],
                    'relation_export'      => [
                        'name'       => '关系链升级',
                        'url'        => 'member.member.exportRelation',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => 'fa-circle-o',
                        'sort'       => 0,
                        'item'       => 'relation_base',
                        'parents'    => ['Member',],
                        'child'      => [
                        ],
                    ],
                    'member_invited'       => [
                        'name'             => '会员邀请码',
                        'url'              => 'member.member_invited.index',
                        'url_params'       => '',
                        'permit'           => 1,
                        'menu'             => 1,
                        'icon'             => 'fa-circle-o',
                        'sort'             => 0,
                        'left_first_show'  => 1,
                        'left_second_show' => 1,
                        'item'             => 'member_invited',
                        'parents'          => ['Member'],
                        'child'            => [
                            'member_invited_list'   => [
                                'name'       => '查看',
                                'url'        => 'member.member_invited.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-circle-o',
                                'sort'       => 0,
                                'item'       => 'member_invited_list',
                                'parents'    => ['Member', 'member_invited',],
                            ],
                            'member_invited_export' => [
                                'name'       => '导出',
                                'url'        => 'member.member_invited.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-circle-o',
                                'sort'       => 0,
                                'item'       => 'member_invited_export',
                                'parents'    => ['Member', 'member_invited',],
                            ],
                        ],
                    ],
                ],
            ],

            'Order' => [
                'name'             => '订单',
                'url'              => 'order.list.index',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'top_show'         => 0,
                'left_first_show'  => 1,
                'left_second_show' => 1,
                'icon'             => 'fa-shopping-cart',
                'sort'             => '4',
                'item'             => 'Order',
                'parents'          => [],
                'child'            => [
                    'order_list' => [
                        'name'       => '全部订单',
                        'url'        => 'order.list.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-clipboard',
                        'sort'       => 0,
                        'item'       => 'order_list',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_see',
                                'parents'    => ['Order', 'order_list'],
                                'child'      => [],
                            ],

                            //订单操作所有订单的共同操作
                            'order_handel'   => [
                                'name'       => '订单操作',
                                'url'        => 'order.handel',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_handel',
                                'parents'    => ['Order', 'order_list'],
                                'child'      => [
                                    'order_list_index'            => [
                                        'name'       => '查看详情',
                                        'url'        => 'order.detail.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'order_list_index',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],
                                    'order_pay_list'              => [
                                        'name'       => '查看订单支付记录',
                                        'url'        => 'order.orderPay.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'order_pay_index',
                                        'parents'    => ['Order', 'order_detail'],
                                    ],
                                    'order_pay_detail'            => [
                                        'name'       => '查看订单支付详情',
                                        'url'        => 'orderPay.detail.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'order_pay_detail',
                                        'parents'    => ['Order', 'order_detail'],
                                    ],
                                    'order_fix_payfail'           => [
                                        'name'       => '修复支付状态',
                                        'url'        => 'order.fix.pay-fail',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'order_fix_payfail',
                                        'parents'    => ['Order', 'order_detail'],
                                    ],
                                    'orderpay_fix_refund'         => [
                                        'name'       => '原路退款',
                                        'url'        => 'orderPay.fix.refund',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'orderpay_fix_refund',
                                        'parents'    => ['Order', 'order_detail'],
                                    ],
                                    'change_order_price_index'    => [
                                        'name'       => '修改价格跳转路由',
                                        'url'        => 'order.change-order-price.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'change_order_price_index',
                                        'parents'    => ['Order', 'order_list'],
                                    ],
                                    'change_order_price_store'    => [
                                        'name'       => '订单改价',
                                        'url'        => 'order.change-order-price.store',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'change_order_price_store',
                                        'parents'    => ['Order', 'order_list'],
                                    ],
                                    'order_operation_pay'         => [
                                        'name'       => '确认付款',
                                        'url'        => 'order.operation.pay',
                                        'url_params' => 'order.operation.send',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_pay',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],
                                    'order_operation_send'        => [
                                        'name'       => '确认发货',
                                        'url'        => 'order.operation.send',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-file-text',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_send',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],
                                    'order_operation_cancel_send' => [
                                        'name'       => '取消发货',
                                        'url'        => 'order.operation.cancel-send',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_cancel_send',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],
                                    'order_operation_receive'     => [
                                        'name'       => '确认收货',
                                        'url'        => 'order.operation.receive',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_receive',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],


                                    'order_operation_close' => [
                                        'name'       => '关闭订单',
                                        'url'        => 'order.operation.close',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_close',
                                        'parents'    => ['Order', 'order_list'],
                                    ],

                                    'order_operation_manualrefund' => [
                                        'name'       => '退款并关闭订单',
                                        'url'        => 'order.operation.manualRefund',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_manualrefund',
                                        'parents'    => ['Order', 'order_list'],
                                    ],

                                    'order_operation_remark' => [
                                        'name'       => '订单备注',
                                        // 'url'               => 'order.remark.index',
                                        'url'        => 'order.operation.remarks',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_remark',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],

                                    'order_operation_revoice' => [
                                        'name'       => '上传发票',
                                        'url'        => 'order.operation.invoice',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 1,
                                        'item'       => 'order_operation_revoice',
                                        'parents'    => ['Order', 'order_list', 'order_handel'],
                                    ],
                                ],
                            ],
                        ],

                    ],

                    'order_list_waitPay' => [
                        'name'       => '待支付订单',
                        'url'        => 'order.list.waitPay',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-credit-card',
                        'sort'       => 1,
                        'item'       => 'order_list_waitPay',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_waitPay_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.waitPay',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-circle-o',
                                'sort'       => 1,
                                'item'       => 'order_list_waitPay',
                                'parents'    => ['Order', 'order_list_waitPay'],
                                'child'      => [],
                            ],
                        ],
                    ],

                    'order_list_waitSend'      => [
                        'name'       => '待发货订单',
                        'url'        => 'order.list.waitSend',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-truck',
                        'sort'       => '2',
                        'item'       => 'order_list_waitSend',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_waitSend_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.waitSend',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_waitSend_see',
                                'parents'    => ['Order', 'order_list_waitSend'],
                                'child'      => [],
                            ],
                        ],
                    ],
                    'order_list_waitReceive'   => [
                        'name'       => '待收货订单',
                        'url'        => 'order.list.waitReceive',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-dropbox',
                        'sort'       => '3',
                        'item'       => 'order_list_waitReceive',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_waitReceive_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.waitReceive',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_waitReceive_see',
                                'parents'    => ['Order', 'order_list_waitReceive'],
                                'child'      => [],
                            ],
                        ],
                    ],
                    'order_list_completed'     => [
                        'name'       => '已完成订单',
                        'url'        => 'order.list.completed',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-check-square-o',
                        'sort'       => '5',
                        'item'       => 'order_list_completed',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_completed_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.completed',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_completed_see',
                                'parents'    => ['Order', 'order_list_completed'],
                                'child'      => [],

                            ],
                        ],
                    ],
                    'order_list_cancelled'     => [
                        'name'       => '已关闭订单',
                        'url'        => 'order.list.cancelled',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bitbucket',
                        'sort'       => '5',
                        'item'       => 'order_list_cancelled',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_cancelled_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.completed',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_cancelled_see',
                                'parents'    => ['Order', 'order_list_cancelled'],
                                'child'      => [],
                            ],
                        ],
                    ],
                    'order_list_pay_fail'      => [
                        'name'       => '支付异常订单',
                        'url'        => 'order.list.pay-fail',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bitbucket',
                        'sort'       => '5',
                        'item'       => 'order_list_pay_fail',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_cancelled_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.pay-fail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_pay_fail_see',
                                'parents'    => ['Order', 'order_list_pay_fail'],
                                'child'      => [],
                            ],
                        ],
                    ],
                    'order_list_callback_fail' => [
                        'name'       => '支付回调异常订单',
                        'url'        => 'order.list.callback-fail',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bitbucket',
                        'sort'       => '5',
                        'item'       => 'order_list_callback_fail',
                        'parents'    => ['Order'],
                        'child'      => [
                            'order_list_cancelled_see' => [
                                'name'       => '浏览',
                                'url'        => 'order.list.callback-fail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'order_list_callback_fail_see',
                                'parents'    => ['Order', 'order_list_callback_fail'],
                                'child'      => [],
                            ],
                        ],
                    ],
                    'refund_list_refund'       => [
                        'name'       => '退换货订单',
                        'url'        => 'refund.list.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-refresh',
                        'sort'       => '6',
                        'item'       => 'refund_list_refund',
                        'parents'    => ['Order'],
                        'child'      => [
                            'refund_order_handel'       => [
                                'name'       => '退换货操作',
                                'url'        => 'order.handel',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'refund_order_handel',
                                'parents'    => ['Order', 'refund_list_refund'],
                                'child'      => [
//                            'refund_detail_index' => [
//                                'name'              => '查看详情',
//                                'url'               => 'order.detail.index',
//                                'url_params'        => '',
//                                'permit'            => 1,
//                                'menu'              => 0,
//                                'icon'              => 'fa-file-text',
//                                'sort'              => 1,
//                                'item'              => 'order_list_index',
//                                'parents'           => ['Order', 'refund_list_refund'],
//                            ],

                                    'refund_operation_reject'               => [
                                        'name'       => '驳回申请',
                                        'url'        => 'refund.operation.reject',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => '4',
                                        'item'       => 'refund_operation_reject',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                    'refund_pay_index'                      => [
                                        'name'       => '同意退款',
                                        'url'        => 'refund.pay.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => '4',
                                        'item'       => 'refund_pay_index',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                    'refund_operation_consensus'            => [
                                        'name'       => '手动退款',
                                        'url'        => 'refund.operation.consensus',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => '4',
                                        'item'       => 'refund_operation_consensus',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                    'refund_operation_pass'                 => [
                                        'name'       => '通过申请(需要客户寄回商品)',
                                        'url'        => 'refund.operation.pass',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-circle-o',
                                        'sort'       => '4',
                                        'item'       => 'refund_operation_pass',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                    'refund_operation_receive_return_goods' => [
                                        'name'       => '商家确认收货',
                                        'url'        => 'refund.operation.receiveReturnGoods',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-circle-o',
                                        'sort'       => '4',
                                        'item'       => 'refund_operation_receive_return_goods',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                    'refund_operation_resend'               => [
                                        'name'       => '商家重新发货',
                                        'url'        => 'refund.operation.resend',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => 'fa-circle-o',
                                        'sort'       => '4',
                                        'item'       => 'refund_operation_resend',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                    'refund_operation_close'                => [
                                        'name'       => '关闭申请(换货完成)',
                                        'url'        => 'refund.operation.close',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => '4',
                                        'item'       => 'refund_operation_close',
                                        'parents'    => ['Order', 'refund_list_refund'],
                                        'child'      => []
                                    ],
                                ],
                            ],
                            'refund_list_refund_all'    => [
                                'name'       => '全部',
                                'url'        => 'refund.list.refund',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-file',
                                'sort'       => 1,
                                'item'       => 'refund_list_refund_all',
                                'parents'    => ['Order', 'refund_list_refund'],
                                'child'      => []
                            ],
                            'refund_list_refundMoney'   => [
                                'name'       => '仅退款',
                                'url'        => 'refund.list.refundMoney',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-money',
                                'sort'       => '2',
                                'item'       => 'refund_list_refundMoney',
                                'parents'    => ['Order', 'refund_list_refund'],
                                'child'      => []
                            ],
                            'refund_list_returnGoods'   => [
                                'name'       => '退货退款',
                                'url'        => 'refund.list.returnGoods',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-location-arrow',
                                'sort'       => '3',
                                'item'       => 'refund_list_returnGoods',
                                'parents'    => ['Order', 'refund_list_refund'],
                                'child'      => []
                            ],
                            'refund_list_exchangeGoods' => [
                                'name'       => '换货',
                                'url'        => 'refund.list.exchangeGoods',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-codepen',
                                'sort'       => '4',
                                'item'       => 'refund_list_exchangeGoods',
                                'parents'    => ['Order', 'refund_list_refund'],
                                'child'      => []
                            ],
                        ],
                    ],

                    'refund_list_refunded' => [
                        'name'       => '已退款',
                        'url'        => 'refund.list.refunded',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-share-square-o',
                        'sort'       => '7',
                        'item'       => 'refund_list_refunded',
                        'parents'    => ['Order'],
                        'child'      => [

                            'refund_list_refunded_see' => [
                                'name'       => '浏览',
                                'url'        => 'refund.list.refunded',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 1,
                                'item'       => 'refund_list_refunded_see',
                                'parents'    => ['Order', 'refund_list_refunded'],
                                'child'      => [],
                            ],
                        ],
                    ],

                    'order_batch_send' => [
                        'name'       => '批量发货',
                        'url'        => 'order.batch-send.index',
                        'url_params' => '',
                        'permit'     => 0,
                        'menu'       => 1,
                        'icon'       => 'fa-send',
                        'sort'       => '8',
                        'item'       => 'order_batch_send',
                        'parents'    => ['Order'],
                        'child'      => [

                            'order_batch_send_get_example' => [
                                'name'       => '下载模版',
                                'url'        => 'order.batch-send.get-example',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'icon'       => '',
                                'item'       => 'order_batch_send_get_example',
                                'parents'    => ['Order', 'order_batch_send'],
                                'child'      => [],
                            ],
                        ],
                    ],
                ],
            ],

            'plugins' => [
                'name'             => '应用',
                'url'              => 'plugins.get-plugin-list',
                'urlParams'        => '',
                'permit'           => 1,
                'menu'             => 1,
                'icon'             => 'fa-cubes',
                'top_show'         => 0,
                'left_first_show'  => 1,
                'left_second_show' => 0,
                'parents'          => [],
                'item'             => 'plugins',
            ],

            'finance' => [
                'name'             => '财务',
                'url'              => 'finance.balance-set.see',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'top_show'         => 0,
                'left_first_show'  => 1,
                'left_second_show' => 1,
                'icon'             => 'fa-rmb',
                'parent_id'        => 0,
                'sort'             => '5',
                'item'             => 'finance',
                'parents'          => [],
                'child'            => [

                    'finance_balance_set' => [
                        'name'       => '余额设置',
                        'url'        => 'finance.balance-set.see',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-gear',
                        'sort'       => 0,
                        'item'       => 'finance_balance_set',
                        'parents'    => ['finance'],
                        'child'      => [

                            'finance_balance_set_see' => [
                                'name'       => '查看设置',
                                'url'        => 'finance.balance-set.see',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-gear',
                                'item'       => 'finance_balance_set_see',
                                'parents'    => ['finance', 'finance_balance_set'],
                            ],

                            'finance_balance_set_store' => [
                                'name'       => '修改设置',
                                'url'        => 'finance.balance-set.store',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-gear',
                                'item'       => 'finance_balance_set_see',
                                'parents'    => ['finance', 'finance_balance_set'],
                            ],
                        ],
                    ],

                    'finance_balance_member' => [
                        'name'       => '会员余额',
                        'url'        => 'finance.balance.member',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-book',
                        'sort'       => 0,
                        'item'       => 'finance_balance_member',
                        'parents'    => ['finance', 'balance'],
                        'child'      => [

                            'finance_balance_member_see' => [
                                'name'       => '浏览记录',
                                'url'        => 'finance.balance.member',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'finance_balance_member_see',
                                'parents'    => ['finance', 'balance', 'finance_balance_member'],
                            ],

                            'finance_balance_member_recharge' => [
                                'name'       => '余额充值',
                                'url'        => 'balance.recharge.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'finance_balance_member_recharge',
                                'parents'    => ['finance', 'balance', 'finance_balance_member'],
                            ],
                        ],
                    ],

                    'finance_balance_rechargeRecord' => [
                        'name'       => '充值记录',
                        'url'        => 'finance.balance-recharge-records.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-download',
                        'sort'       => 0,
                        'item'       => 'finance_balance_rechargeRecord',
                        'parents'    => ['finance', 'balance',],
                    ],

                    //茶余饭后独立开发充值满额统计
                    /*'finance_balance_recharge_statistics' => [
                'name'              => '满额统计',
                'url'               => 'finance.balanceRechargeStatistics.index',
                'url_params'        => '',
                'permit'            => 1,
                'menu'              => 1,
                'icon'              => 'fa-download',
                'sort'              => 0,
                'item'              => 'finance_balance_recharge_statistics',
                'parents'           => ['finance', 'balance',],
            ],*/


                    'finance_balance_tansferRecord' => [
                        'name'       => '转让记录',
                        'url'        => 'finance.balance.transferRecord',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-external-link',
                        'sort'       => 0,
                        'item'       => 'finance_balance_tansferRecord',
                        'parents'    => ['finance', 'balance'],
                    ],

                    'finance_balance_records' => [
                        'name'       => '余额明细',
                        'url'        => 'finance.balance-records.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-file-text-o',
                        'sort'       => 0,
                        'item'       => 'finance_balance_balanceDetail',
                        'parents'    => ['finance', 'balance'],
                        'child'      => [

                            'finance_balance_records_see' => [
                                'name'       => '浏览记录',
                                'url'        => 'finance.balance-records.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'finance_balance_records_see',
                                'parents'    => ['finance', 'balance', 'finance_balance_records'],
                            ],

                            'finance_balance_records_export' => [
                                'name'       => '导出 EXCEL',
                                'url'        => 'finance.balance-records.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'finance_balance_records_export',
                                'parents'    => ['finance', 'balance', 'finance_balance_records'],
                            ],

                            'finance_balance_records_detail' => [
                                'name'       => '明细详情',
                                'url'        => 'finance.balance.lookBalanceDetail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'finance_balance_records_detail',
                                'parents'    => ['finance', 'balance', 'finance_balance_records'],
                            ],
                        ],


                    ],

                    'income_records' => [
                        'name'       => '收入明细',
                        'url'        => 'income.income-records.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-file-text-o',
                        'sort'       => 0,
                        'item'       => 'income_records',
                        'parents'    => ['finance'],
                    ],

                    'withdraw_set' => [
                        'name'       => '提现设置',
                        'url'        => 'finance.withdraw-set.see',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-gear',
                        'sort'       => 0,
                        'item'       => 'withdraw_set',
                        'parents'    => ['finance', 'withdraw'],
                        'child'      => [
                            'withdraw_set_see' => [
                                'name'       => '编辑保存',
                                'url'        => 'finance.withdraw-set.see',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => '0',
                                'item'       => 'withdraw_set_see',
                                'parents'    => ['finance', 'withdraw', 'withdraw_set'],
                            ],
                        ],
                    ],

                    'withdraw_statistics' => [
                        'name'       => '提现统计',
                        'url'        => 'finance.withdraw-statistics.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-line-chart',
                        'sort'       => 0,
                        'item'       => 'withdraw_statistics',
                        'parents'    => ['finance', 'withdraw'],
                    ],

                    'withdrawRecords' => [
                        'name'       => '提现记录',
                        'url'        => 'withdraw.records',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-pencil',
                        'sort'       => 0,
                        'item'       => 'withdrawRecords',
                        'parents'    => ['finance'],
                        'child'      => [

                            'withdrawRecordsIndex'   => [
                                'name'       => '全部记录',
                                'url'        => 'withdraw.records.index',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsIndex',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecordsInitial' => [
                                'name'       => '待审核',
                                'url'        => 'withdraw.records.initial',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsInitial',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecordsAudit'   => [
                                'name'       => '待打款',
                                'url'        => 'withdraw.records.audit',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsAudit',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecordsPaying'  => [
                                'name'       => '打款中',
                                'url'        => 'withdraw.records.paying',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsPaying',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecordsPayed'   => [
                                'name'       => '已打款',
                                'url'        => 'withdraw.records.payed',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsPayed',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecordsRebut'   => [
                                'name'       => '已驳回',
                                'url'        => 'withdraw.records.rebut',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsRebut',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecordsInvalid' => [
                                'name'       => '无效提现',
                                'url'        => 'withdraw.records.invalid',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecordsInvalid',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],

                            'withdrawRecords_balance_detail'  => [
                                'name'       => '余额提现详情',
                                'url'        => 'finance.balance-withdraw.detail',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecords_balance_detail',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],
                            'withdrawRecords_balance_examine' => [
                                'name'       => '余额审核打款',
                                'url'        => 'finance.balance-withdraw.examine',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecords_balance_examine',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],

                            'withdrawRecords_detail' => [
                                'name'       => '收入提现详情',
                                'url'        => 'withdraw.detail.index',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecords_detail',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],

                            'withdrawRecords_examine' => [
                                'name'       => '收入审核打款',
                                'url'        => 'finance.withdraw.dealt',
                                'url_params' => "",
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'withdrawRecords_examine',
                                'parents'    => ['finance', 'withdrawRecords'],
                            ],

                        ],
                    ],


                    'finance_point'        => [
                        'name'       => '积分管理',
                        'url'        => 'finance.point-member.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-database',
                        'sort'       => 0,
                        'item'       => 'finance_point',
                        'parents'    => ['finance',],
                        'child'      => [

                            'point_set' => [
                                'name'       => '基础设置',
                                'url'        => 'finance.point-set.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-gear',
                                'sort'       => 0,
                                'item'       => 'point_set',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_member' => [
                                'name'       => '会员积分',
                                'url'        => 'finance.point-member.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-database',
                                'sort'       => 0,
                                'item'       => 'point_member',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_recharge' => [
                                'name'       => '积分充值',
                                'url'        => 'point.recharge.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'point_recharge',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_recharge_records' => [
                                'name'       => '充值记录',
                                'url'        => 'point.recharge-records.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-file-text-o',
                                'sort'       => 0,
                                'item'       => 'point_recharge_records',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_log' => [
                                'name'       => '积分明细',
                                'url'        => 'finance.point-log.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-file-text-o',
                                'sort'       => 0,
                                'item'       => 'point_log',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_queue' => [
                                'name'       => '积分队列',
                                'url'        => 'point.queue.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-file-text-o',
                                'sort'       => 0,
                                'item'       => 'point_queue',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_queue_log' => [
                                'name'       => '队列明细',
                                'url'        => 'point.queue-log.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-file-text-o',
                                'sort'       => 0,
                                'item'       => 'point_queue_log',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_love_see' => [
                                'name'       => '查看转出设置',
                                'url'        => 'finance.point-love.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'point_love_see',
                                'parents'    => ['finance', 'finance_point',],
                            ],

                            'point_love_update' => [
                                'name'       => '修改转出设置',
                                'url'        => 'finance.point-love.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'point_love_update',
                                'parents'    => ['finance', 'finance_point',],
                            ],
                        ],
                    ],
                    'remittance_audit'     => [
                        'name'       => '转账审核',
                        'url'        => 'finance.remittance-audit.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-file-text-o',
                        'sort'       => 0,
                        'item'       => 'remittance_audit',
                        'parents'    => ['finance',],
                        'child'      => [
                            'remittance_audit_ajax'   => [
                                'name'       => '转账审核全部',
                                'url'        => 'finance.remittance-audit.ajax',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-gear',
                                'sort'       => 0,
                                'item'       => 'remittance_audit_ajax',
                                'parents'    => ['finance', 'remittance_audit',],
                            ],
                            'remittance_audit_detail' => [
                                'name'       => '转账审核详情',
                                'url'        => 'remittanceAudit.detail.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-gear',
                                'sort'       => 0,
                                'item'       => 'remittance_audit_detail',
                                'parents'    => ['finance', 'remittance_audit',],
                            ],
                            'remittance_audit_pass'   => [
                                'name'       => '转账审核通过',
                                'url'        => 'remittanceAudit.operation.pass',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-gear',
                                'sort'       => 0,
                                'item'       => 'remittance_audit_pass',
                                'parents'    => ['finance', 'remittance_audit',],
                            ],
                            'remittance_audit_reject' => [
                                'name'       => '转账审核拒绝',
                                'url'        => 'remittanceAudit.operation.reject',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-gear',
                                'sort'       => 0,
                                'item'       => 'remittance_audit_reject',
                                'parents'    => ['finance', 'remittance_audit',],
                            ],
                        ]
                    ],
                    'profit_advertisement' => [
                        'name'       => '收益广告',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-image',
                        'sort'       => 0,
                        'item'       => 'profit_advertisement',
                        'url'        => 'finance.advertisement.index',
                        'url_params' => '',
                        'parents'    => ['finance', 'profit_advertisement'],
                        'child'      => [
                            'profit_advertisement_advertisement_add'    => [
                                'name'       => '添加广告',
                                'permit'     => 1,
                                'menu'       => '',
                                'icon'       => '',
                                'url'        => 'finance.advertisement.add',
                                'url_params' => '',
                                'parents'    => ['finance', 'profit_advertisement'],
                                'child'      => []
                            ],
                            'profit_advertisement_advertisement_edit'   => [
                                'name'       => '编辑广告',
                                'permit'     => 1,
                                'menu'       => '',
                                'icon'       => '',
                                'url'        => 'finance.advertisement.edit',
                                'url_params' => '',
                                'parents'    => ['finance', 'profit_advertisement'],
                                'child'      => []
                            ],
                            'profit_advertisement_advertisement_del'    => [
                                'name'       => '删除广告',
                                'permit'     => 1,
                                'menu'       => '',
                                'icon'       => '',
                                'url'        => 'finance.advertisement.del',
                                'url_params' => '',
                                'parents'    => ['finance', 'profit_advertisement'],
                                'child'      => []
                            ],
                            'profit_advertisement_advertisement_change' => [
                                'name'       => '切换状态',
                                'permit'     => 1,
                                'menu'       => '',
                                'icon'       => '',
                                'url'        => 'finance.advertisement.setStatus',
                                'url_params' => '',
                                'parents'    => ['finance', 'profit_advertisement'],
                                'child'      => []
                            ],
                        ]
                    ],

                    'excelRecharge' => [
                        'name'       => '批量充值',
                        'url'        => 'excelRecharge.page.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-line-chart',
                        'sort'       => 0,
                        'item'       => 'excelRecharge',
                        'parents'    => ['finance'],
                        'child'      => [
                            'excelRechargeExample' => [
                                'name'       => '下载模版',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'excelRecharge.example.index',
                                'url_params' => '',
                                'item'       => 'excelRechargeExample',
                                'parents'    => ['finance', 'excelRecharge']
                            ],
                            'excelRechargeConfirm' => [
                                'name'       => '确认充值',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'excelRecharge.confirm.index',
                                'url_params' => '',
                                'item'       => 'excelRechargeConfirm',
                                'parents'    => ['finance', 'excelRecharge']
                            ],
                            'excelRechargeRecords' => [
                                'name'       => '充值记录',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'excelRecharge.records.index',
                                'url_params' => '',
                                'item'       => 'excelRechargeRecords',
                                'parents'    => ['finance', 'excelRecharge']
                            ],
                            'excelRechargeDetail'  => [
                                'name'       => '详情记录',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'url'        => 'excelRecharge.detail.index',
                                'url_params' => '',
                                'item'       => 'excelRechargeDetail',
                                'parents'    => ['finance', 'excelRecharge']
                            ],
                        ]
                    ],


                ],

            ],

            'charts' => [
                'name'             => '统计',
                'url'              => 'charts.member.count.index',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'icon'             => 'fa-bar-chart-o',
                'sort'             => 1,
                'top_show'         => 0,               //顶部导航是否显示
                'left_first_show'  => 1,           //左侧一级导航是否显示
                'left_second_show' => 1,           //左侧二级导航是否显示
                'item'             => 'system',
                'parents'          => [],
                'child'            => [

                    'member_count_charts' => [
                        'name'       => '会员数据统计',
                        'url'        => 'charts.member.count.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'member_count_charts',
                        'parents'    => ['charts',],

                    ],

                    'member_offline_charts' => [
                        'name'       => '会员关系统计',
                        'url'        => 'charts.member.offline-count.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'member_offline_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'member_offline_count_charts' => [
                                'name'       => '下线人数排行',
                                'url'        => 'charts.member.offline-count.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_count_charts',
                                'parents'    => ['charts', 'member_offline_charts'],

                            ],

                            'member_offline_order_charts' => [
                                'name'       => '下线订单排行',
                                'url'        => 'charts.member.offline-order.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_order_charts',
                                'parents'    => ['charts', 'member_offline_charts'],

                            ],

                            'member_offline_team_order_charts' => [
                                'name'       => '团队支付订单排行',
                                'url'        => 'charts.member.offline-team-order.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_team_order_charts',
                                'parents'    => ['charts', 'member_offline_charts'],
                            ],

                            'member_offline_commission_order_charts' => [
                                'name'       => '分销订单排行',
                                'url'        => 'charts.member.offline-commission-order.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_commission_order_charts',
                                'parents'    => ['charts', 'member_offline_charts'],
                            ],
                        ]

                    ],

                    /* 'goods_sales_count_charts'     => [
                 'name'          => '商品数据统计',
                 'url'           => 'charts.goods.sales-count.index',
                 'url_params'    => '',
                 'permit'        => 1,
                 'menu'          => 1,
                 'icon'          => 'fa-bar-chart-o',
                 'sort'          => 0,
                 'item'          => 'goods_charts',
                 'parents'       => ['charts',],
             ],

             'order_total_charts'=> [
                 'name'          => '订单数据统计',
                 'url'           => 'charts.order.today-trends.index',
                 'url_params'    => '',
                 'permit'        => 1,
                 'menu'          => 1,
                 'icon'          => 'fa-bar-chart-o',
                 'sort'          => 0,
                 'item'          => 'order_total_charts',
                 'parents'       => ['charts',],
                 'child'         => [

                     'today_order_total_charts'     => [
                         'name'          => '今日订单统计',
                         'url'           => 'charts.order.today-trends.index',
                         'url_params'    => '',
                         'permit'        => 1,
                         'menu'          => 1,
                         'icon'          => '',
                         'sort'          => 0,
                         'item'          => 'today_order_total_charts',
                         'parents'       => ['charts','order_total_charts'],

                     ],

                     'all_order_total_charts'     => [
                         'name'          => '全部订单统计',
                         'url'           => 'charts.order.order-trends.index',
                         'url_params'    => '',
                         'permit'        => 1,
                         'menu'          => 1,
                         'icon'          => '',
                         'sort'          => 0,
                         'item'          => 'all_order_total_charts',
                         'parents'       => ['charts','order_total_charts'],

                     ],


                 ]
             ],*/

                    'order_ranking_charts' => [
                        'name'       => '会员订单排行',
                        'url'        => 'charts.order.order-ranking.count',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [

                            'order_count_ranking_charts' => [
                                'name'       => '订单数量排行',
                                'url'        => 'charts.order.order-ranking.count',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'order_count_ranking_charts',
                                'parents'    => ['charts', 'order_ranking_charts'],

                            ],

                            'order_money_ranking_charts' => [
                                'name'       => '订单金额排行',
                                'url'        => 'charts.order.order-ranking.money',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'order_money_ranking_charts',
                                'parents'    => ['charts', 'order_ranking_charts'],

                            ],

                        ]
                    ],

                    'member_phone_attribution' => [
                        'name'       => '手机归属地统计',
                        'url'        => 'charts.phone.phone-attribution.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => []
                    ],

                    'order_dividend_charts' => [
                        'name'       => '订单分润',
                        'url'        => 'charts.order.order-dividend.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [

                            'order_dividend_charts_export' => [
                                'name'       => '订单分润导出',
                                'url'        => 'charts.order.order-dividend.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'order_dividend_charts_export',
                                'parents'    => ['charts', 'order_dividend_charts'],
                            ],
                        ]
                    ],

                    'transaction_amount_charts' => [
                        'name'       => '交易额统计',
                        'url'        => 'charts.order.transaction-amount.count',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'transaction_amount_charts_export' => [
                                'name'       => '交易额统计导出',
                                'url'        => 'charts.order.transaction-amount.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'transaction_amount_charts_export',
                                'parents'    => ['charts', 'transaction_amount_charts'],
                            ],
                        ]
                    ],

                    'merchant_income_charts' => [
                        'name'       => '商家收入统计',
                        'url'        => 'charts.merchant.supplier-income.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'supplier_charts' => [
                                'name'       => '供应商收入排行',
                                'url'        => 'charts.merchant.supplier-income.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_count_charts',
                                'parents'    => ['charts', 'merchant_income_charts'],
                                'child'      => [
                                    'supplier_charts_export' => [
                                        'name'       => '供应商收入排行导出',
                                        'url'        => 'charts.merchant.supplier-income.export',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 0,
                                        'item'       => 'supplier_charts_export',
                                        'parents'    => ['charts', 'merchant_income_charts', 'supplier_charts'],
                                    ],
                                ]
                            ],
                            'store_charts'    => [
                                'name'       => '门店收入排行',
                                'url'        => 'charts.merchant.store-income.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_order_charts',
                                'parents'    => ['charts', 'merchant_income_charts'],
                                'child'      => [
                                    'store_charts_export' => [
                                        'name'       => '门店收入排行导出',
                                        'url'        => 'charts.merchant.store-income.export',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 0,
                                        'item'       => 'store_charts_export',
                                        'parents'    => ['charts', 'merchant_income_charts', 'store_charts'],
                                    ],
                                ]
                            ],
                            'cashier_charts'  => [
                                'name'       => '收银台收入排行',
                                'url'        => 'charts.merchant.cashier-income.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_order_charts',
                                'parents'    => ['charts', 'merchant_income_charts'],
                                'child'      => [
                                    'cashier_charts_export' => [
                                        'name'       => '收银台收入排行导出',
                                        'url'        => 'charts.merchant.cashier-income.export',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'sort'       => 0,
                                        'item'       => 'cashier_charts_export',
                                        'parents'    => ['charts', 'merchant_income_charts', 'cashier_charts'],
                                    ],
                                ]
                            ],
                        ]
                    ],
                    'shop_income_list'       => [
                        'name'       => '平台收益列表',
                        'url'        => 'charts.income.shop-income-list.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts'],
                        'child'      => [
                            'shop_income_export' => [
                                'name'       => '平台收益列表导出',
                                'url'        => 'charts.income.shop-income-list.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'shop_income_export',
                                'parents'    => ['charts', 'shop_income_list'],
                            ],
                        ]
                    ],
                    'shop_income_charts'     => [
                        'name'       => '平台收益统计',
                        'url'        => 'charts.income.shop-income-statistics.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'shop_income_charts_export' => [
                                'name'       => '平台收益统计导出',
                                'url'        => 'charts.income.shop-income-statistics.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'shop_income_charts_export',
                                'parents'    => ['charts', 'shop_income_charts'],
                            ],
                        ]
                    ],
                    'member_income_charts'   => [
                        'name'       => '会员收入统计',
                        'url'        => 'charts.income.member-income.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'member_income_charts_detail' => [
                                'name'       => '会员收入详情',
                                'url'        => 'charts.income.member-income.detail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'order_ranking_charts',
                                'parents'    => ['charts', 'member_income_charts'],
                            ],
                            'member_income_charts_back'   => [
                                'name'       => '返回按钮',
                                'url'        => 'charts.income.member_income.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'member_income_charts_back',
                                'parents'    => ['charts', 'member_income_charts'],
                            ]
                        ]
                    ],
                    'poundage_income_charts' => [
                        'name'       => '手续费/劳务税汇总',
                        'url'        => 'charts.income.poundage.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts'],
                        'child'      => [
                            'poundage_income_charts' => [
                                'name'       => '手续费明细',
                                'url'        => 'charts.income.poundage.detail',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'order_ranking_charts',
                                'parents'    => ['charts', 'poundage_income_charts'],
                            ],
                            'poundage_income_export' => [
                                'name'       => '导出',
                                'url'        => 'charts.income.poundage.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'poundage_income_export',
                                'parents'    => ['charts', 'poundage_income_charts'],
                            ]
                        ],
                    ],
                    'point_charts'           => [
                        'name'       => '积分数据统计',
                        'url'        => 'charts.finance.point.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'point_charts_export' => [
                                'name'       => '积分数据统计导出',
                                'url'        => 'charts.finance.point.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'point_charts_export',
                                'parents'    => ['charts', 'point_charts'],
                            ]
                        ]
                    ],
                    'money_charts'           => [
                        'name'       => '余额数据统计',
                        'url'        => 'charts.finance.balance.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'money_charts_export' => [
                                'name'       => '余额数据统计导出',
                                'url'        => 'charts.finance.balance.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'money_charts_export',
                                'parents'    => ['charts', 'money_charts'],
                            ]
                        ]
                    ],
                    'coupon_charts'          => [
                        'name'       => '赠送优惠券统计',
                        'url'        => 'charts.finance.coupon.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'coupon_charts_export' => [
                                'name'       => '赠送优惠券统计导出',
                                'url'        => 'charts.finance.coupon.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-bar-chart-o',
                                'sort'       => 0,
                                'item'       => 'coupon_charts_export',
                                'parents'    => ['charts', 'coupon_charts'],
                            ]
                        ]
                    ],
                    'goods_charts'           => [
                        'name'       => '商品销售统计',
                        'url'        => 'charts.goods.sales-volume-count.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'goods_volume_charts' => [
                                'name'       => '商品销量排行',
                                'url'        => 'charts.goods.sales-volume-count.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_count_charts',
                                'parents'    => ['charts', 'goods_charts'],

                            ],
                            'goods_sales_charts'  => [
                                'name'       => '商品销售额排行',
                                'url'        => 'charts.goods.sales-volume-count.sales-price',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'member_offline_order_charts',
                                'parents'    => ['charts', 'goods_charts'],

                            ]
                        ]
                    ],
                    'team_charts'            => [
                        'name'       => '会员一二级团队统计',
                        'url'        => 'charts.team.list.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bar-chart-o',
                        'sort'       => 0,
                        'item'       => 'order_ranking_charts',
                        'parents'    => ['charts',],
                        'child'      => [
                            'team_charts_export' => [
                                'name'       => '会员一二级团队统计导出',
                                'url'        => 'charts.team.list.export',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'team_charts_export',
                                'parents'    => ['charts', 'team_charts'],

                            ]
                        ]
                    ],
                ],
            ],

            'system' => [
                'name'             => '系统',
                'url'              => 'setting.shop.entry',
                'url_params'       => '',
                'permit'           => 1,
                'menu'             => 1,
                'icon'             => 'fa-cogs',
                'sort'             => 1,
                'top_show'         => 0,               //顶部导航是否显示
                'left_first_show'  => 1,               //左侧导航是否显示
                'left_second_show' => 1,
                'item'             => 'system',
                'parents'          => [],
                'child'            => [

                    'shop' => [
                        'name'       => '商城入口',
                        'url'        => 'setting.shop.entry',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-hand-o-right',
                        'sort'       => 0,
                        'item'       => 'shop',
                        'parents'    => ['system',],
                    ],

                    'Setting' => [
                        'name'             => '商城设置',
                        'url'              => 'setting.shop.index',
                        'url_params'       => '',
                        'permit'           => 1,
                        'top_show'         => 1,               //顶部导航是否显示
                        'left_first_show'  => 1,           //左侧一级导航是否显示
                        'left_second_show' => 1,           //左侧二级导航是否显示
                        'menu'             => 1,
                        'icon'             => 'fa-cog',
                        'sort'             => 0,
                        'item'             => 'Setting',
                        'parents'          => ['system'],
                        'child'            => [

                            'setting_shop' => [
                                'name'       => '设置',
                                'url'        => 'setting.shop.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-sliders',
                                'sort'       => 0,
                                'item'       => 'setting_shop',
                                'parents'    => ['system', 'Setting'],
                            ],

                            'setting_member' => [
                                'name'       => '会员',
                                'url'        => 'setting.shop.member',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '3',
                                'item'       => 'setting_member',
                                'parents'    => ['system', 'Setting', 'setting_shop'],
                            ],

                            'setting_order' => [
                                'name'       => '订单',
                                'url'        => 'setting.shop.order',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '3',
                                'item'       => 'setting_order',
                                'parents'    => ['system', 'Setting', 'setting_shop'],
                            ],

                            'setting_category' => [
                                'name'       => '分类',
                                'url'        => 'setting.shop.category',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '4',
                                'item'       => 'setting_category',
                                'parents'    => ['system', 'Setting', 'setting_shop',],
                            ],

                            'setting_contact' => [
                                'name'       => '联系方式',
                                'url'        => 'setting.shop.contact',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '5',
                                'item'       => 'setting_contact',
                                'parents'    => ['system', 'Setting', 'setting_shop'],
                            ],

                            'setting_sms' => [
                                'name'       => '短信',
                                'url'        => 'setting.shop.sms',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '6',
                                'item'       => 'setting_sms',
                                'parents'    => ['system', 'Setting', 'setting_shop'],
                            ],

                            'setting_coupon' => [
                                'name'       => '优惠券',
                                'url'        => 'setting.coupon.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => '',
                                'sort'       => '6',
                                'item'       => 'setting_coupon',
                                'parents'    => ['system', 'Setting', 'setting_shop'],
                            ],

                            'setting_shop_share' => [
                                'name'       => '分享',
                                'url'        => 'setting.shop.share',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-link',
                                'sort'       => '5',
                                'item'       => 'setting_shop_share',
                                'parents'    => ['system', 'Setting',],
                            ],

                            'setting_shop_slide' => [
                                'name'       => '幻灯片',
                                'url'        => 'setting.slide.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bell-o',
                                'sort'       => '6',
                                'item'       => 'setting_shop_slide',
                                'parents'    => ['system', 'Setting',],
                                'child'      => [

                                    'setting_shop_slide_index' => [
                                        'name'       => '浏览列表',
                                        'url'        => 'setting.slide.index',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'item'       => 'setting_shop_slide_index',
                                        'parents'    => ['system', 'Setting', 'setting_shop_slide'],
                                    ],

                                    'setting_shop_slide_add' => [
                                        'name'       => '添加幻灯片',
                                        'url'        => 'setting.slide.create',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'item'       => 'setting_shop_slide_add',
                                        'parents'    => ['system', 'Setting', 'setting_shop_slide'],
                                    ],

                                    'setting_shop_slide_edit' => [
                                        'name'       => '修改幻灯片',
                                        'url'        => 'setting.slide.edit',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'item'       => 'setting_shop_slide_edit',
                                        'parents'    => ['system', 'Setting', 'setting_shop_slide'],
                                    ],

                                    'setting_shop_slide_deleted' => [
                                        'name'       => '删除幻灯片',
                                        'url'        => 'setting.slide.deleted',
                                        'url_params' => '',
                                        'permit'     => 1,
                                        'menu'       => 0,
                                        'icon'       => '',
                                        'item'       => 'setting_shop_slide_deleted',
                                        'parents'    => ['system', 'Setting', 'setting_shop_slide'],
                                    ],
                                ]
                            ],
                            'setting_shop_adv'   => [
                                'name'       => '广告位',
                                'url'        => 'setting.shop-advs.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bell-o',
                                'sort'       => '7',
                                'item'       => 'setting_shop_adv',
                                'parents'    => ['system', 'Setting',],
                            ],

                            'setting_shop_form' => [
                                'name'       => '会员资料表单',
                                'url'        => 'setting.form.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bell-o',
                                'sort'       => '7',
                                'item'       => 'setting_shop_form',
                                'parents'    => ['system', 'Setting',],
                            ],

                            'setting_shop_protocol' => [
                                'name'       => '注册协议',
                                'url'        => 'setting.shop.protocol',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bell-o',
                                'sort'       => '7',
                                'item'       => 'setting_shop_protocol',
                                'parents'    => ['system', 'Setting',],
                            ],
                            'express_info'          => [
                                'name'       => '物流查询',
                                'url'        => 'setting.shop.express-info',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 1,
                                'icon'       => 'fa-bell-o',
                                'sort'       => '7',
                                'item'       => 'setting_shop_protocol',
                                'parents'    => ['system', 'Setting',],
                            ],
                        ],
                    ],

                    'setting_shop_trade' => [
                        'name'       => '交易设置',
                        'url'        => 'setting.shop.trade',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-compress',
                        'sort'       => '7',
                        'item'       => 'setting_shop_trade',
                        'parents'    => ['system'],

                    ],


                    'setting_shop_pay' => [
                        'name'       => '支付方式',
                        'url'        => 'setting.shop.pay',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-facebook-square',
                        'sort'       => '3',
                        'item'       => 'setting_shop_pay',
                        'parents'    => ['system',],
                    ],

                    'setting_shop_notice' => [
                        'name'       => '消息提醒',
                        'url'        => 'setting.shop.notice',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bell-o',
                        'sort'       => '6',
                        'item'       => 'setting_shop_notice',
                        'parents'    => ['system',],
                        'child'      => [

                            'setting_shop_default_notice_open'   => [
                                'name'       => '默认消息模版开启',
                                'url'        => 'setting.default-notice.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_shop_default_notice_open',
                                'parents'    => ['system', 'setting_shop_notice'],
                            ],
                            'setting_shop_default_notice_closed' => [
                                'name'       => '默认消息模版取消',
                                'url'        => 'setting.default-notice.cancel',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_shop_default_notice_closed',
                                'parents'    => ['system', 'setting_shop_notice'],
                            ],
                        ]
                    ],

                    'setting_wechat_notice' => [
                        'name'       => '微信模板管理',
                        'url'        => 'setting.wechat-notice.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-wechat',
                        'sort'       => '6',
                        'item'       => 'setting_wechat_notice',
                        'parents'    => ['system', 'Setting',],
                        'child'      => [

                            'setting_wechat_notice_see' => [
                                'name'       => '查看',
                                'url'        => 'setting.wechat-notice.see',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_wechat_notice_see',
                                'parents'    => ['system', 'setting_wechat_notice'],
                            ],

                            'setting_wechat_notice_del' => [
                                'name'       => '删除',
                                'url'        => 'setting.wechat-notice.del',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_wechat_notice_del',
                                'parents'    => ['system', 'setting_wechat_notice'],
                            ],

                            'setting_wechat_notice_add' => [
                                'name'       => '添加模版',
                                'url'        => 'setting.wechat-notice.addTmp',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_wechat_notice_add',
                                'parents'    => ['system', 'setting_wechat_notice'],
                            ],
                        ]
                    ],
                    'setting_small_program' => [
                        'name'       => '小程序消息模板',
                        'url'        => 'setting.small-program.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bars',
                        'sort'       => '6',
                        'item'       => 'setting_small_program',
                        'parents'    => ['system', 'Setting',],
                        'child'      => [
                            'setting_small_program_choose' => [
                                'name'       => '选择模版（白名单）',
                                'url'        => 'setting.wechat-notice.returnJson',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'item'       => 'setting_small_program_choose',
                                'parents'    => ['system', 'setting_small_program'],
                            ],


                            'setting_small_program_choose2' => [
                                'name'       => '选择模版（白名单）',
                                'url'        => 'setting.small-program.tpl',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'item'       => 'setting_small_program_choose2',
                                'parents'    => ['system', 'setting_small_program'],
                            ],

                            'setting_small_program_index'            => [
                                'name'       => '浏览列表',
                                'url'        => 'setting.small-program.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_small_program_index',
                                'parents'    => ['system', 'setting_small_program'],
                            ],
//                    'setting_small_program_add'  => [
//                        'name'              => '添加模版',
//                        'url'               => 'setting.small-program.add',
//                        'url_params'        => '',
//                        'permit'            => 1,
//                        'menu'              => 0,
//                        'item'              => 'setting_small_program_add',
//                        'parents'           => ['system','setting_small_program'],
//                    ],
                            'setting_small_program_get_template_key' => [
                                'name'       => '选择模板',
                                'url'        => 'setting.small-program.get-template-key',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_small_program_get_template_key',
                                'parents'    => ['system', 'setting_small_program'],
                            ],
                            'setting_small_program_notice'           => [
                                'name'       => '消息通知',
                                'url'        => 'setting.small-program.notice',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_small_program_notice',
                                'parents'    => ['system', 'setting_small_program'],
                            ],
                            'setting_small_program_edit'             => [
                                'name'       => '修改模版',
                                'url'        => 'setting.small-program.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_small_program_edit',
                                'parents'    => ['system', 'setting_small_program'],
                            ],
                            'setting_small_program_delete'           => [
                                'name'       => '删除模版',
                                'url'        => 'setting.small-program.del',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_delete',
                                'parents'    => ['system', 'setting_small_program'],
                            ],
                        ]
                    ],

                    'setting_diy_temp'  => [
                        'name'       => '自定义模板管理',
                        'url'        => 'setting.diy-temp.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bars',
                        'sort'       => '6',
                        'item'       => 'setting_diy_temp',
                        'parents'    => ['system', 'Setting',],
                        'child'      => [

                            'setting_diy_temp_choose' => [
                                'name'       => '选择模版（白名单）',
                                'url'        => 'setting.wechat-notice.returnJson',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_choose',
                                'parents'    => ['system', 'setting_diy_temp'],
                            ],


                            'setting_diy_temp_choose2' => [
                                'name'       => '选择模版（白名单）',
                                'url'        => 'setting.diy-temp.tpl',
                                'url_params' => '',
                                'permit'     => 0,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_choose2',
                                'parents'    => ['system', 'setting_diy_temp'],
                            ],

                            'setting_diy_temp_index'  => [
                                'name'       => '浏览列表',
                                'url'        => 'setting.diy-temp.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_index',
                                'parents'    => ['system', 'setting_diy_temp'],
                            ],
                            'setting_diy_temp_add'    => [
                                'name'       => '添加模版',
                                'url'        => 'setting.diy-temp.add',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_add',
                                'parents'    => ['system', 'setting_diy_temp'],
                            ],
                            'setting_diy_temp_edit'   => [
                                'name'       => '修改模版',
                                'url'        => 'setting.diy-temp.edit',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_edit',
                                'parents'    => ['system', 'setting_diy_temp'],
                            ],
                            'setting_diy_temp_delete' => [
                                'name'       => '删除模版',
                                'url'        => 'setting.diy-temp.del',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'item'       => 'setting_diy_temp_delete',
                                'parents'    => ['system', 'setting_diy_temp'],
                            ],
                        ]
                    ],
                    'setting_shop_lang' => [
                        'name'       => '语言设置',
                        'url'        => 'setting.lang.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-bell-o',
                        'sort'       => '6',
                        'item'       => 'setting_shop_lang',
                        'parents'    => ['system',],
                    ],

                    'role' => [
                        'name'       => '角色管理',
                        'url'        => 'user.role.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-gamepad',
                        'sort'       => 0,
                        'item'       => 'role',
                        'parents'    => ['system',],
                        'child'      => [

                            'role_see' => [
                                'name'       => '浏览角色',
                                'url'        => 'user.role.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'role_see',
                                'parents'    => ['system', 'role',],
                            ],

                            'role_store' => [
                                'name'       => '添加角色',
                                'url'        => 'user.role.store',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'role_store',
                                'parents'    => ['system', 'role',],
                            ],

                            'role_update' => [
                                'name'       => '修改角色',
                                'url'        => 'user.role.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'role_update',
                                'parents'    => ['system', 'role',],
                            ],

                            'role_destroy' => [
                                'name'       => '删除角色',
                                'url'        => 'user.role.destory',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'role_destroy',
                                'parents'    => ['system', 'role'],
                            ],
                        ],
                    ],

                    'user'          => [
                        'name'       => '操作员',
                        'url'        => 'user.user.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-list-ul',
                        'sort'       => 0,
                        'item'       => 'user',
                        'parents'    => ['system',],
                        'child'      => [

                            'user_see' => [
                                'name'       => '浏览操作员',
                                'url'        => 'user.user.index',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'user_see',
                                'parents'    => ['system', 'user',],
                            ],


                            'user_store' => [
                                'name'       => '添加操作员',
                                'url'        => 'user.user.store',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'user_store',
                                'parents'    => ['system', 'user',],
                            ],

                            'user_update' => [
                                'name'       => '修改操作员',
                                'url'        => 'user.user.update',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => '',
                                'sort'       => 0,
                                'item'       => 'user_update',
                                'parents'    => ['system', 'user',],
                            ],

                            'user_destroy' => [
                                'name'       => '删除操作员',
                                'url'        => 'user.user.destroy',
                                'url_params' => '',
                                'permit'     => 1,
                                'menu'       => 0,
                                'icon'       => 'fa-remove',
                                'sort'       => 0,
                                'item'       => 'user_destroy',
                                'parents'    => ['system', 'user',],
                            ],
                        ],
                    ],
                    'operation_log' => [
                        'name'       => '操作日志',
                        'url'        => 'setting.operation-log.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => 'fa-list-ul',
                        'sort'       => '6',
                        'item'       => 'operation_log',
                        'parents'    => ['system',],
                    ],

                ],
            ],
        ];
    }

    private function founderMenu()
    {
        return [
            'founder_plugins' => [
                'name'       => '插件管理',
                'url'        => 'plugins.get-plugin-data',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 1,
                'icon'       => 'fa-puzzle-piece',
                'sort'       => '0',
                'item'       => 'plugins',
                'parents'    => ['system',],
                'child'      => [
                    'plugins_enable' => [
                        'name'       => '启用插件',
                        'url'        => 'plugins.enable',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '1',
                        'item'       => 'plugins_enable',
                        'parents'    => ['system', 'plugins',],
                    ],

                    'plugins_disable' => [
                        'name'       => '禁用插件',
                        'url'        => 'plugins.disable',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '2',
                        'item'       => 'plugins_disable',
                        'parents'    => ['system', 'plugins',],
                    ],

                    'plugins_manage' => [
                        'name'       => '插件安装',
                        'url'        => 'plugins.manage',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '3',
                        'item'       => 'plugins_manage',
                        'parents'    => ['system', 'plugins',],
                    ],

                    'plugins_delete' => [
                        'name'       => '插件卸载',
                        'url'        => 'plugins.delete',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '4',
                        'item'       => 'plugins_delete',
                        'parents'    => ['system', 'plugins',],
                    ],

                    'plugins_update' => [
                        'name'       => '插件升级',
                        'url'        => 'plugins.update',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '5',
                        'item'       => 'plugins_update',
                        'parents'    => ['system', 'plugins',],
                    ],
                ],
            ],
            'supervisor'      => [
                'name'       => '队列管理',
                'url'        => 'supervisord.supervisord.index',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 1,
                'icon'       => 'fa-history',
                'sort'       => '5',
                'item'       => 'supervisor',
                'parents'    => ['system'],
                'child'      => [
                    'supervisord_supervisord_index' => [
                        'name'       => '队列运行状态',
                        'url'        => 'supervisord.supervisord.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'sort'       => '2',
                        'item'       => 'supervisord_supervisord_index',
                        'parents'    => ['system', 'supervisor', 'supervisord_supervisord_index'],
                        'child'      => []
                    ],
                    'supervisord_supervisord_store' => [
                        'name'       => '服务器设置',
                        'url'        => 'supervisord.supervisord.store',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'sort'       => '2',
                        'item'       => 'supervisord_supervisord_store',
                        'parents'    => ['system', 'supervisor', 'supervisord_supervisord_store'],
                        'child'      => []
                    ],
                    'supervisord_supervisord_queue' => [
                        'name'       => '队列设置',
                        'url'        => 'supervisord.supervisord.queue',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 1,
                        'icon'       => '',
                        'sort'       => '2',
                        'item'       => 'supervisord_supervisord_queue',
                        'parents'    => ['system', 'supervisor', 'supervisord_supervisord_queue'],
                        'child'      => []
                    ],
                ],
            ],
            'site_setting'    => [
                'name'       => '站点设置',
                'url'        => 'siteSetting.index',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 1,
                'icon'       => 'fa-cog',
                'sort'       => '5',
                'item'       => 'site_setting_index',
                'parents'    => ['system'],
                'child'      => [
                    'site_setting.index' => [
                        'name'       => '查看设置',
                        'url'        => 'siteSetting.index.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '1',
                        'item'       => 'site_setting_index_index',
                        'parents'    => ['system', 'site_setting', 'system_site_setting_index_index'],
                        'child'      => []
                    ],
                    'site_setting.store' => [
                        'name'       => '保存设置',
                        'url'        => 'site_setting.store.index',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => '',
                        'sort'       => '2',
                        'item'       => 'site_setting_store_index',
                        'parents'    => ['system', 'site_setting', 'system_site_setting_store_index'],
                        'child'      => []
                    ],

                ]
            ],
            'work_order'      => [
                'name'       => '工单管理',
                'url'        => 'setting.work-order.index',
                'url_params' => '',
                'permit'     => 1,
                'menu'       => 1,
                'icon'       => 'fa-list-ul',
                'sort'       => '5',
                'item'       => 'log-viewer',
                'parents'    => ['system',],
                'child'      => [
                    'work_order_store_page' => [
                        'name'       => '工单提交页面',
                        'url'        => 'setting.work-order.store-page',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => 'fa-list-ul',
                        'sort'       => '6',
                        'item'       => 'operation_log',
                        'parents'    => ['system',],
                    ],

                    'work_order_details' => [
                        'name'       => '工单详情页面',
                        'url'        => 'setting.work-order.details',
                        'url_params' => '',
                        'permit'     => 1,
                        'menu'       => 0,
                        'icon'       => 'fa-list-ul',
                        'sort'       => '6',
                        'item'       => 'operation_log',
                        'parents'    => ['system',],
                    ],
                ]
            ],
//    'log_viewer'      => [
//        'name'              => '系统日志',
//        'url'               => 'developer.log-viewer',
//        'url_params'        => '',
//        'permit'            => 1,
//        'menu'              => 1,
//        'icon'              => 'fa-history',
//        'sort'              => '5',
//        'item'              => 'log-viewer',
//        'parents'           => ['system',],
//    ],
            /*    'shop_upgrade'      => [
                    'name'              => '系统升级',
                    'url'               => 'update.index',
                    'url_params'        => '',
                    'permit'            => 1,
                    'menu'              => 1,
                    'icon'              => 'fa-history',
                    'sort'              => '5',
                    'item'              => 'shop_upgrade',
                    'parents'           => ['system',],
                ]*/
        ];

    }

    public function clearMainMenu()
    {
        $this->mainMenu();
        $this->mainMenu = [];
    }

    public function setMainMenu($key, $value)
    {
        $mainMenu = $this->mainMenu();
        array_set($mainMenu, $key, $value);
        $this->mainMenu = $mainMenu;
        return $mainMenu;
    }

    private function _getItems()
    {
        $menuListCacheKey = "menuList_" . \YunShop::app()->uid;

        if (!$this->items) {
            if (!Cache::has($menuListCacheKey)) {
                $pluginMenu = $this->getPluginMenus() ?: [];

                //菜单生成
                $menuList = array_merge($this->mainMenu(), $pluginMenu);

                if (PermissionService::isFounder()) {
                    //创始人私有菜单
                    $menuList['system']['child'] = array_merge($menuList['system']['child'], $this->founderMenu());
                }
                $this->items = $menuList;
                /**
                 * 这里可能会递归调用
                 */
                $this->items = static::validateMenuPermit($this->items);
                Cache::put($menuListCacheKey, $this->items, 3600);
            }
            $this->items = Cache::get($menuListCacheKey);
        }


        return $this->items;
    }

    private function _getCurrentItems()
    {
        $item = \app\common\models\Menu::getCurrentItemByRoute(request()->input('route'), $this->getItems());

        $result = array_merge(\app\common\models\Menu::getCurrentMenuParents($item, $this->getItems()), [$item]);


        // //检测权限
        // if (!PermissionService::can($item)) {
        //     $exception = new ShopException('Sorry,您没有操作无权限，请联系管理员!');
        //     $exception->setRedirect(yzWebUrl('index.index'));
        //     throw $exception;
        // }


        return $result;
    }

    public function getCurrentItems()
    {
        if (!isset($this->currentItems)) {
            $this->currentItems = $this->_getCurrentItems();
        }
        return $this->currentItems;
    }

    public function getItems()
    {
        if (!isset($this->items)) {
            $this->items = $this->_getItems();
        }
        return $this->items;
    }
}