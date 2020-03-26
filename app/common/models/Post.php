<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:23
 */

namespace app\common\models;


use app\backend\modules\goods\observers\PostObserver;

class Post   extends  BaseModel
{


    /**
     * 在boot()方法里注册下模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();

        //注册观察者
        static::observe(new PostObserver());
    }


    public function comments()
    {
        return $this->hasMany('app\backend\goods\models\Comment','post_id','id');
    }
}