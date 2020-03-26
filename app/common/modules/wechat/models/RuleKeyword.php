<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/6/6
 * Time: 下午9:09
 */

namespace app\common\modules\wechat\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuleKeyword extends BaseModel
{
    //public $table = 'rule_keyword';
    //public $timestamps = false;

    public $table = 'yz_wechat_rule_keyword';
    protected $guarded = [''];
    use SoftDeletes;

    // module字段从微擎兼容下来，新框架中它的意义在于，海报插件使用了该字段，仅仅是为了兼容微擎
    // 由于海报使用了module字段，海报会检查module字段是否为yun_shop，才进行处理微信消息
    // 沿用这个想法，则公众号插件也对该字段赋值为wechat，以此判断是否公众号插件进行处理
    // 为兼容海报插件，该字段在新框架也是需要的
    // 目前只有两种方式对该字段赋值，1.海报插件对其赋值为yun_shop 2.公众号插件对其赋值为wechat
    protected static $module = 'yun_shop';//海报需要的字段，海报插件进行处理

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rid' => 'required|numeric',
            'uniacid' => 'required|numeric',
            'module' => 'required',
            'content' => 'required',
            'type' => 'numeric|required',
            'displayorder' => 'numeric',
            'status' => 'numeric',
        ];
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'rid' => '规则id',
            'uniacid' => '公众号id',
            'module' => '模块',
            'content' => '关键字内容',
            'type' => '触发类型',
            'displayorder' => '回复优先级',
            'status' => '是否开启',
        ];
    }

    public static function destroyKeywordByRuleId($roleId)
    {
        return static::uniacid()
            ->where('rid', $roleId)
            ->where('module', static::$module)
            ->delete();
    }

    public static function updateKeywordByRoleId($roleId, $keyword)
    {
        return static::uniacid()
            ->where('rid', $roleId)
            ->where('module', static::$module)
            ->update(['content' => trim($keyword)]);
    }

    public function hasOneRule()
    {
        return $this->hasOne(Rule::class,'id','rid')->select('id','name');
    }

    // 通过关键字获取规则
    public static function getRuleKeywordByKeywords($keywords)
    {
        // 先找精准触发
        $accurate = static::uniacid()->where('status','=',1)
            ->where('content','=',$keywords)
            ->where('type','=',1)
            ->orderBy('displayorder','desc')
            ->first();

        // 再找模糊查询,正则匹配先不考虑
        if (empty($accurate)) {
            return static::uniacid()->where('status','=',1)
                ->where('content','like',$keywords.'%')
                ->where('type','!=',1)
                ->orderBy('displayorder','desc')
                ->first();
        } else {
            return $accurate;
        }
    }

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

}