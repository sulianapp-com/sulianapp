<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 09/03/2017
 * Time: 11:00
 */

namespace app\backend\controllers;

use app\backend\models\Menu;
use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\models\MenuSelect;
use app\frontend\modules\finance\services\BalanceService;
use Ixudra\Curl\Facades\Curl;

class MenuController extends BaseController
{
    /**
     * 菜单功能停止对外使用，（菜单表示控制权限js，随意修改会导致js失效，同时路由比对权限）
     *
     * @Author YiTian
     * @Date 2017_06_08
     */

    public function index()
    {
        /*if(request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production') {*/
            $menu = new Menu();
            $menuList = $menu->getDescendants(0);

            return view('menu.index', [
                'menuList' => $menuList
            ])->render();
        /*}*/
    }

    public function add()
    {
        /*if(request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production') {*/
            $model = new MenuSelect();

            $parentId = intval(\YunShop::request()->parent_id);
            $data = \YunShop::request()->menu;

            if ($data) {
                $model->fill($data);

                $validator = $model->validator();
                if ($validator->fails()) {
                    $this->error($validator->messages());
                } else {
                    if ($model->save()) {
                        \Cache::forget('db_menu');
                        return $this->message('添加菜单成功', Url::absoluteWeb('menu.index'));
                    } else {
                        $this->error('添加菜单失败');
                    }
                }
            }

            $parentId && $model->setAttribute('parent_id', $parentId);
            $parentMenu = [0 => '请选择上级'] + $model->toSelectArray(0);

            return view('menu.form', [
                'parentMenu' => $parentMenu,
                'model' => $model
            ])->render();
        /*}*/
    }

    public function edit()
    {
        /*if(request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production') {*/
            $id = \YunShop::request()->id;
            $data = \YunShop::request()->menu;

            $model = MenuSelect::getMenuInfoById($id);
            if (!$model) {
                return $this->message('无此记录', '', 'error');
            }

            if ($data) {
                $model->fill($data);

                $validator = $model->validator();
                if ($validator->fails()) {
                    $this->error($validator->messages());
                } else {
                    if ($model->save()) {
                        \Cache::forget('db_menu');
                        return $this->message('菜单修改成功', Url::absoluteWeb('menu.index'));
                    } else {
                        $this->error('菜单修改失败');
                    }
                }
            }

            $parentMenu = [0 => '请选择上级'] + $model->toSelectArray(0);

            return view('menu.form', [
                'model' => $model,
                'parentMenu' => $parentMenu,
            ])->render();
        /*}*/
    }

    public function del()
    {
        /*if(request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production') {*/
            $id = \YunShop::request()->id;

            $model = Menu::getMenuInfoById($id);
            if (empty($model)) {
                return $this->message('菜单不存在', '', 'error');
            }
            if ($model->childs->count() > 0) {
                return $this->message('存在子菜单不可删除', '', 'error');
            }

            if ($model->delete()) {
                \Cache::forget('db_menu');
                return $this->message('菜单删除成功', Url::absoluteWeb('menu.index'));
            } else {
                $this->error('菜单删除失败');
            }
       /* }*/
    }

    public function getRemoteUpdate()
    {
        /*if (request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production') {*/
            $url = "http://test.yunzshop.com" . config('app.webPath') . "/api.php?i=2&route=menu.to-list";
            $responseData = Curl::to($url)->get();

            if ($responseData) {
                $data = json_decode($responseData);
                if ($data->data && $menu = objectArray($data->data)) {
                    try {
                        (new Menu())->where('id', '>', 0)->forceDelete();
                        foreach ($menu as $v) {
                            Menu::create($v);
                        }
                        //菜单生成
                        \Cache::forget('db_menu');

                    } catch (\Exception $e) {
                        throw new \Exception($e);
                    }
                }
            }
            return $this->message('更新远程菜单成功');
        }
    /*}*/

}