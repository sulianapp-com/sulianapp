<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * 商城基础设置
         */


        /**
         * 商品模块
         */


        /**
         * 会员模块
         */


        /**
         * 订单模块
         */


        /**
         * 分销
         */


        /**
         * 店铺装修
         */


        /**
         * 云币插件
         */


        //配置
//        $this->call(SettingSeeder::class);
        //权限
//        $this->call(YzPermissionSeeder::class);
        //用户角色
        //$this->call(YzUserRoleSeeder::class);
        /**
         * 地址
         */
        //地址(省份,城市,区域)
        //$this->call(YzAddressSeeder::class);
        //地址(街道)
        //$this->call(YzStreetSeeder::class);
        /**
         * 商品
         */
        //商品分类
        //$this->call(YzCategorySeeder::class);
        //商品评论
        //$this->call(YzCommentSeeder::class);
        //商品消息推送
        //$this->call(YzNoticeSeeder::class);
        //商品消息推送
        //$this->call(YzSaleSeeder::class);
        //$this->call(YzOptionsTableSeeder::class);
        //模板消息
        //$this->call(YzTemplateMessageTableSeeder::class);
//        $this->call(YzMenuUpgradeSeeder::class);

//        require "YzUniacidSeeder.php";
//        $this->call(YzUniacidSeeder::class);


//        Log::info(file_get_contents('/data/wwwroot/release.yunzshop.com/addons/yun_shop/vendor/composer/autoload_classmap.php'));
//        Log::info(file_get_contents('/data/wwwroot/release.yunzshop.com/addons/yun_shop/vendor/composer/autoload_static.php'));
//        Log::info(file_get_contents('/data/wwwroot/release.yunzshop.com/addons/yun_shop/database/seeds/YzPluginUniacidSeeder.php'));

//        $this->call(YzpluginSeeder::class);

        require_once "YzPluginUniacidSeeder.php";
        $this->call(YzPluginUniacidSeeder::class);
    }
}
