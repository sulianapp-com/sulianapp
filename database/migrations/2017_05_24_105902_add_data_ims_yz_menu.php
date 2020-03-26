<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \app\common\models\Menu;

class AddDataImsYzMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (\Schema::hasTable('yz_menu')) {

            $_menu = Menu::where('url','finance.balance.balanceDetail')->update(['url'=>'finance.balance-records.index']);

            $_menu = Menu::select('id')->where('item', 'goods.goods')->first();

            if ($_menu->id) {
                $modelOne = Menu::select('id')->where('item', 'goods.goods.edit')->first();
                if ($modelOne) {
                    Menu::where('id', $modelOne->id)->update(['menu' => 0]);
                } else {
                    Menu::insert([
                        'name'              => '编辑商品',
                        'item'              => 'goods.goods.edit',
                        'url'               => 'goods.goods.edit',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 0,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelTwo = Menu::select('id')->where('item', 'goods.goods.create')->first();
                if ($modelOne) {
                    Menu::where('id', $modelTwo->id)->update(['menu' => 0]);
                } else {
                    Menu::insert([
                        'name'              => '添加商品',
                        'item'              => 'goods.goods.create',
                        'url'               => 'goods.goods.create',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 0,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelThree = Menu::select('id')->where('item', 'goods.goods.destroy')->first();
                if ($modelOne) {
                    Menu::where('id', $modelThree->id)->update(['menu' => 0]);
                } else {
                    Menu::insert([
                        'name'              => '添加商品',
                        'item'              => 'goods.goods.destroy',
                        'url'               => 'goods.goods.destroy',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 0,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelFour = Menu::select('id')->where('item', 'goods.goods.copy')->first();
                if ($modelOne) {
                    Menu::where('id', $modelFour->id)->update(['menu' => 0]);
                } else {
                    Menu::insert([
                        'name'              => '添加商品',
                        'item'              => 'goods.goods.copy',
                        'url'               => 'goods.goods.copy',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 0,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
            }

           /* $_menu = Menu::select('id')->where('url', 'order.list')->first();
            if ($_menu->id) {
                $modelOne = Menu::select('id')->where('url', 'refund.list.refund')->first();
                if ($modelOne) {
                    Menu::where('id', $modelOne->id)->update([
                        'name'              => '退换货订单',
                        'item'              => 'refund_list_refund',
                        'url'               => 'refund.list.refund',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                } else {
                    Menu::insert([
                        'name'              => '退换货订单',
                        'item'              => 'refund_list_refund',
                        'url'               => 'refund.list.refund',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelTwo = Menu::select('id')->where('url', 'refund.list.refunded')->first();
                if ($modelTwo) {
                    Menu::where('id', $modelTwo->id)->update([
                        'name'              => '已退款',
                        'item'              => 'refund_list_refunded',
                        'url'               => 'refund.list.refunded',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                } else {
                    Menu::insert([
                        'name'              => '已退款',
                        'item'              => 'refund_list_refunded',
                        'url'               => 'refund.list.refunded',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
            }

            $_menu = Menu::select('id')->where('url', 'refund.list.refund')->first();
            if ($_menu->id) {
                $modelOne = Menu::select('id')->where('url', 'refund.list.refundMoney')->first();
                if ($modelOne) {
                    Menu::where('id', $modelOne->id)->update([
                        'name'              => '仅退款',
                        'item'              => 'refund_list_refundMoney',
                        'url'               => 'refund.list.refundMoney',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                } else {
                    Menu::insert([
                        'name'              => '仅退款',
                        'item'              => 'refund_list_refundMoney',
                        'url'               => 'refund.list.refundMoney',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelTwo = Menu::select('id')->where('url', 'refund.list.returnGoods')->first();
                if ($modelTwo) {
                    Menu::where('id', $modelTwo->id)->update([
                        'name'              => '退货退款',
                        'item'              => 'refund_list_returnGoods',
                        'url'               => 'refund.list.returnGoods',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                } else {
                    Menu::insert([
                        'name'              => '退货退款',
                        'item'              => 'refund_list_returnGoods',
                        'url'               => 'refund.list.returnGoods',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelThree = Menu::select('id')->where('url', 'refund.list.exchangeGoods')->first();
                if ($modelThree) {
                    Menu::where('id', $modelThree->id)->update([
                        'name'              => '换货',
                        'item'              => 'refund_list_exchangeGoods',
                        'url'               => 'refund.list.exchangeGoods',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                } else {
                    Menu::insert([
                        'name'              => '换货',
                        'item'              => 'refund_list_exchangeGoods',
                        'url'               => 'refund.list.exchangeGoods',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
                $modelFour = Menu::select('id')->where('url', 'refund.list.refund')->first();
                if ($modelFour) {
                    Menu::where('id', $modelFour->id)->update([
                        'name'              => '全部',
                        'item'              => 'refund_list_refund_all',
                        'url'               => 'refund.list.refund',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                } else {
                    Menu::insert([
                        'name'              => '全部',
                        'item'              => 'refund_list_refund_all',
                        'url'               => 'refund.list.refund',
                        'url_params'        => '',
                        'permit'            => 1,
                        'menu'              => 1,
                        'icon'              => 'fa-circle-o',
                        'parent_id'         => $_menu->id,
                        'sort'              => 1,
                        'status'            => 1,
                        'created_at'        => time(),
                        'updated_at'        => time(),
                        'deleted_at'        => NULL
                    ]);
                }
            }*/



        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
