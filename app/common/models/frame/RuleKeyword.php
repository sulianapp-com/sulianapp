<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:32
 */

namespace app\common\models\frame;


use app\common\models\BaseModel;

class RuleKeyword extends BaseModel
{
    public $table = 'rule_keyword';

    public $timestamps = false;

    protected $guarded = [''];

    public $attributes = array(
        'module'        => 'yun_shop',
        'type'          => 1,
        'displayorder'  => 0,
        'status'        => 1
    );


    protected static $module = 'yun_shop';

    public function __construct()
    {
        // 新框架兼容微擎
        if (config('app.framework') == 'platform') {
            $this->table = 'yz_wechat_rule_keyword';
        } else {
            $this->table = 'rule_keyword';
        }
        parent::__construct();
    }


    /*
     * 关键字是否存在，存在返回id，不存在返回false；
     *
     * @param string $keyword
     *
     * @return mixed   $id or false*/
    public static function hasKeyword($keyword)
    {
        $id = self::uniacid()->where('module', static::$module)->where('content', $keyword)->value('id');

        return empty($id) ? false : $id;
    }

    public static function delKeyword($keyword)
    {
        return self::uniacid()
            ->where('module', static::$module)
            ->where('content', $keyword)
            ->delete();
    }

    /*
     * 通过 roleId 修改关键字
     *
     * @param int $roleId
     * @param string $keyrord
     *
     * @return mixed   $id or false*/
    public static function updateKeywordByRoleId($roleId, $keyword)
    {
        return static::uniacid()
            ->where('rid', $roleId)
            ->where('module', static::$module)
            ->update(['content' => trim($keyword)]);
    }

    public static function destroyKeywordByRuleId($roleId)
    {
        return static::uniacid()
            ->where('rid', $roleId)
            ->where('module', static::$module)
            ->delete();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function attributeNames() {
        return [
            'uniacid'       => 'uniacid字段不能为空',
            'module'        => 'module字段不能为空',
            'displayorder'  => 'displayorder字段不能为空',
            'status'        => 'status字段不能为空',
            'rid'           => 'rid字段不能为空',
            'content'       => 'content字段不能为空',
            'type'          => 'type字段不能为空'
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'uniacid'       => 'required',
            'module'        => 'required',
            'displayorder'  => 'required',
            'status'        => 'required',
            'rid'           => 'required',
            'content'       => 'required',
            'type'          => 'required'
        ];
    }

}
