<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 13/04/2017
 * Time: 16:35
 */
class ChangeModuleNameSeeder extends \Illuminate\Database\Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        return;
        /*
         *
         *

        UPDATE ims_uni_account_modules SET module = REPLACE(module, 'sz_yi', 'yun_shop');

        UPDATE ims_modules SET name = REPLACE(name, 'sz_yi', 'yun_shop');

        UPDATE ims_modules_bindings SET module = REPLACE(module, 'sz_yi', 'yun_shop');

        UPDATE ims_rule SET name = REPLACE(name, 'sz_yi', 'yun_shop'),module = REPLACE(module, 'sz_yi', 'yun_shop');

        UPDATE ims_rule_keyword SET module = REPLACE(module, 'sz_yi', 'yun_shop');

        UPDATE ims_users_permission SET type = REPLACE(type, 'sz_yi', 'yun_shop');

        UPDATE ims_cover_reply SET module = REPLACE(module, 'sz_yi', 'yun_shop');

       */

    }
}