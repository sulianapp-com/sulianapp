<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/18
 * Time: 上午9:28
 */

namespace app\common\helpers\FormParam;

use app\common\exceptions\AppException;
use Illuminate\Database\Eloquent\Model;
use app\framework\Database\Eloquent\Builder;

/**
 * 表单参数助手
 * Class FromParam
 * @package app\common\helpers
 */
class FromParam
{
    private $params;
    private $format;
    private $isFormatted;

    public function __construct($params, $format = [])
    {
        $this->params = $params;
        $this->format = $format;
    }

    /**
     * @param $key
     * @return string
     * @throws AppException
     */
    private function getTypeClass($key)
    {
        if (isset($this->format[$key])) {
            $className = ucfirst($this->format[$key]);
        } else {
            $className = 'Equal';
        }
        $className = __NAMESPACE__ . '\\' . $className;
        if (!class_exists($className)) {
            throw new AppException('不存在的参数类型'.$className);
        }
        return $className;
    }

    /**
     * 根据参数生成where条件
     * todo 这个方法应该定义在哪个类中 $builder 怎么传递
     * @param Model $builder
     * @return Model
     * @throws AppException
     */
    public function toWhere($builder)
    {
        // 过滤调空的
        $result = array_filter($this->params, function ($value) {
            return isset($value) && $value !== '';
        });
        //根据key的类型
        foreach ($result as $key => $value) {
            $className = $this->getTypeClass($key);
            (new $className($builder))->format($key, $value);
        };

        return $builder;
    }

//    /**
//     * 输出所有参数
//     * @return mixed
//     */
//    public function toBuilder()
//    {
//        if (!$this->isFormatted) {
//            $this->params = $this->format($this->params);
//        }
//        return $this->params;
//    }
}