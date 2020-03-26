<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/02/2017
 * Time: 16:36
 */

namespace app\common\models;


use app\backend\modules\goods\observers\SettingObserver;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\traits\ValidatorTrait;
use app\framework\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use app\framework\Database\Eloquent\Collection;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class BaseModel
 * @package app\common\models
 * @property int id
 * @property int created_at
 * @property int updated_at
 * @property int deleted_at
 * @method static self uniacid()
 * @method static Collection get()
 * @method static self find(string $id)
 * @method static self first()
 * @method static self select(...$fields)
 * @method static self where(...$where)
 * @method static self whereNot(...$where)
 * @method static self orWhere(...$where)
 * @method static self whereBetween(array $where)
 * @method static self with($with)
 * @method static self whereIn(...$where)
 * @method static self whereNotIn(...$where)
 * @method static self whereHas(...$where)
 * @method static Collection pluck($field)
 * @method static int count()
 * @method static float sum()
 * @method static self join(...$join)
 * @method static self insert()
 * @method static self set()
 * @method static self exclude($fields)
 * @method static self orderBy(...$field)
 * @method static self whereRaw(...$field)
 * @method static self getModel()
 * @method static string value($fields)
 * @method static self groupBy(...$field)
 * @method static self delete()
 */
class BaseModel extends Model
{
    use ValidatorTrait;
    use ValidatesRequests;

    protected $search_fields;
    static protected $needLog = false;

    public function getTableName()
    {
        return app('db')->getTablePrefix() . $this->getTable();
    }

    /**
     * 模糊查找
     * @param $query
     * @param $params
     * @return mixed
     */
    public function scopeSearchLike(Builder $query, $params)
    {
        $search_fields = $this->search_fields;
        $query->where(function (Builder $query) use ($params, $search_fields) {
            foreach ($search_fields as $search_field) {
                $query->orWhere($search_field, 'like', '%' . $params . '%');
            }
        });
        return $query;
    }

    /**
     * 默认使用时间戳戳功能
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 获取当前时间
     *
     * @return int
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 避免转换时间戳为时间字符串
     *
     * @param \DateTime|int $value
     * @return \DateTime|int
     */
    public function fromDateTime($value)
    {
        return $value;
    }

    /**
     * select的时候避免转换时间为Carbon
     *
     * @param mixed $value
     * @return mixed
     */
//  protected function asDateTime($value) {
//	  return $value;
//  }


    /**
     * 从数据库获取的为获取时间戳格式
     */
    //public function getDateFormat() {
    //     return 'U';
    // }

    /**
     * 后台全局筛选统一账号scope
     * @param Builder $query
     * @return $this|Builder
     */
    public function scopeUniacid(Builder $query)
    {
        if (\YunShop::app()->uniacid === null) {
            return $query;
        }
        return $query->where($this->getTable() . '.uniacid', \YunShop::app()->uniacid);
    }

    /**
     * 递归获取$class 相对路径的 $findClass
     * @param $class
     * @param $findClass
     * @return null|string
     */
    public static function recursiveFindClass($class, $findClass)
    {
        $result = substr($class, 0, strrpos($class, "\\")) . '\\' . $findClass;

        if (class_exists($result)) {
            return $result;
        }

        if (class_exists(get_parent_class($class))) {
            return self::recursiveFindClass(get_parent_class($class), $findClass);
        }
        return null;

    }

    /**
     * 获取与子类 继承关系最近的 $model类
     * @param $model
     * @return null|string
     * @throws ShopException
     */
    public function getNearestModel($model)
    {
        $result = self::recursiveFindClass(static::class, $model);

        if (isset($result)) {
            return $result;
        }
        throw new ShopException('获取关联模型失败');
    }

    /**
     * 用来区分订单属于哪个.当插件需要查询自己的订单时,复写此方法
     * @param $query
     * @param int $pluginId
     * @return mixed
     */
    public function scopePluginId(Builder $query, $pluginId = 0)
    {
        return $query->where('plugin_id', $pluginId);
    }

    /**
     * 用来区分订单属于哪个.当插件需要查询自己的订单时,复写此方法
     * @param $query
     * @param null $uid
     * @return mixed
     */
    public function scopeUid(Builder $query, $uid = null)
    {
        if (!isset($uid)) {
            $uid = \YunShop::app()->getMemberId();
        }
        return $query->where($this->getTable() . '.uid', $uid);
    }

    public function scopeMine(Builder $query)
    {
        return $query->where('uid', \YunShop::app()->getMemberId());
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        if (static::$needLog) {
            static::observe(new SettingObserver);
        }

    }

    private function getCommonModelClass($class)
    {
        if (get_parent_class($class) == self::class) {

            return $class;
        }
        return $this->getCommonModelClass(get_parent_class($class));
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return $this->getCommonModelClass(parent::getMorphClass());

    }


    public function pushAppends($appends)
    {
        $this->appends = array_merge($this->appends, $appends);
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function columns()
    {
        $cacheKey = 'model_' . $this->getTable() . '_columns';

        if (!\Cache::has($cacheKey)) {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing($this->getTable());
            cache([$cacheKey => $columns], Carbon::now()->addSeconds(3600));
            return $columns;
        }

        return cache($cacheKey);

    }

    /**
     * @param $column
     * @return bool
     * @throws \Exception
     */
    public function hasColumn($column)
    {
        return in_array($column, $this->columns());
    }

    /**
     * @param $query
     * @param array $excludeFields
     * @return mixed
     * @throws \Exception
     */
    public function scopeExclude($query, $excludeFields)
    {
        if (!is_array($excludeFields)) {
            $excludeFields = explode(',', $excludeFields);
        }
        $fields = array_diff($this->columns(), $excludeFields) ?: [];
        return $query->select($fields);
    }

    public function getRelationValue($key)
    {

        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (method_exists($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }

        return $this->getRelationshipFromExpansions($key, static::class);
    }

    /**
     * 从模型扩展中获取关联模型
     * @param $method
     * @param $class
     * @return mixed
     */
    public function expansionMethod($method, $class)
    {
        if (!isset(static::$expansions)) {
            static::$expansions = \app\common\modules\shop\ShopConfig::current()->get('shop-foundation.model-expansions') ?: [];
        }
        if (!empty(static::$expansions)) {

            foreach ($this->getExpansions($class) as $expansion) {

                if (method_exists($expansion, $method)) {

                    return (new $expansion)->$method($this);
                }
            }
        }
        // 递归到此类为止避免死循环
        if (get_parent_class($class) !== self::class) {
            return $this->expansionMethod($method, get_parent_class($class));
        }
    }

    /**
     * 从模型扩展中载入
     * @param $key
     * @param $class
     * @return mixed
     */
    private function getRelationshipFromExpansions($key, $class)
    {
        if (isset(static::$expansions)) {
            foreach ($this->getExpansions($class) as $expansion) {
                if (method_exists($expansion, $key)) {
                    return (new $expansion)->getRelationshipFromExpansion($key, $this);
                }
            }
        }
        // 递归到此类为止避免死循环
        if (get_parent_class($class) !== self::class) {
            return $this->getRelationshipFromExpansions($key, get_parent_class($class));
        }
    }

    /**
     * 模型扩展
     * @var Collection
     */
    protected static $expansions;

    /**
     * 设置扩展
     * @param $expansions
     */
    public static function setExpansions($expansions)
    {
        static::$expansions = $expansions;
    }

    /**
     * 获取扩展设置
     * @return mixed
     */
    private function getExpansions($class)
    {
        return static::$expansions[$class];
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return Builder|\Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    public function beforeSaving()
    {
        return true;
    }

    public function afterSaving()
    {
        return true;
    }

    /**
     * 递归格式化金额字段
     * @param $attributes
     * @return array
     */
    protected function formatAmountAttributes($attributes)
    {
        // 格式化价格字段,将key中带有price,amount的属性,转为保留2位小数的字符串
        $attributes = array_combine(array_keys($attributes), array_map(function ($value, $key) {
            if (is_array($value)) {
                $value = $this->formatAmountAttributes($value);
            } else {
                if (str_contains($key, 'price') || str_contains($key, 'amount')) {
                    $value = sprintf('%.2f', $value);
                }
            }
            return $value;
        }, $attributes, array_keys($attributes)));
        return $attributes;
    }

    /**
     * 校验参数
     * @param $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @throws AppException
     */
    public function validate($request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);
        //$validator->errors();

        if ($validator->fails()) {
            throw new AppException($validator->errors()->first());
        }
    }

    public function getPlugin()
    {
        if (isset($this->plugin_id) && $this->plugin_id > 0) {
            return app('plugins')->findPlugin($this->plugin_id);
        }
        return null;
    }
}