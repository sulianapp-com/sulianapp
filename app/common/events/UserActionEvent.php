<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 23/02/2017
 * Time: 21:32
 */

namespace app\common\events;


use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserActionEvent extends Event
{
    use SerializesModels;
    public $uid,$adminName,$model,$aid,$type,$content;

    /**
     * userActionEvent constructor.
     * @param string $model 被操作的模型
     * @param int $aid 被操作ID
     * @param int $type 类型 1:添加,2:删除,3:修改更新
     * @param string $content   操作详情
     */
    public function __construct($model, $aid, $type, $content)
    {
        $this->uid = session('user_id');
        $this->adminName = session('name');
        $this->model = $model;
        $this->aid = $aid;
        $this->type = $type;
        $this->content = $content;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}