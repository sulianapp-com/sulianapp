<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 21:33
 */

namespace app\common\models;


use app\common\observers\TestMemberObserver;
use Eloquent;

class TestMember extends  BaseModel
{
    public $table = 'mc_members';

    /**
     * 可填充字段
     * $member = TestMember::create(['name' => 'janpan']);.
     *
     * @var array
     */
    //protected $fillable = [''];

    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    /**
     * 定义结果中隐藏字段.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * 可显示的字段.
     *
     * @var array
     */
    protected $visible = ['first_name', 'last_name'];

    public $goodsForm = [];

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public  function atributeNames()
    {
        return [
            'title'=> trans('member.title'),
            'body'=>'内容'
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public  function rules()
    {

        return [
            'title' => 'required|string|max:255',
            'body' => 'required|image|Min:3',
        ];
    }

    /**
     * 在boot()方法里注册下模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();
        // 开始事件的绑定...
        //creating, created, updating, updated, saving, saved,  deleting, deleted, restoring, restored.
        static::creating(function (Eloquent $model) {
            if ( ! $model->isValid()) {
                // Eloquent 事件监听器中返回的是 false ，将取消 save / update 操作
                return false;
            }
        });

        //注册观察者
        static::observe(new TestMemberObserver());
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }


}