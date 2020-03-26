<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 03/03/2017
 * Time: 12:21
 */

namespace app\common\traits;

use app\common\helpers\Url;
use app\common\helpers\StringHelper;

trait TemplateTrait
{
    //当前模块名数组
    public $modules = [];
    //当前控制器
    public $controller = '';
    //当前action
    public $action = '';
    //当前路由
    public $route = '';

    public $title = '';

    public $breadcrumbs = [];

    /**
     * 渲染视图
     *
     * ```php
     * $this->render('index',['list'=>$list]);
     *
     * 模板名可直接写 当前action名，方法会自动补全路径，如果名称里有/则不补全
     *
     * ```
     * @param $filename     模板名
     * @param array $data 模板变量
     * @return mixed
     */
    public function render($filename, $data = [])
    {
        if (strpos($filename, '/') === false) {
            $this->controller && $filename = strtolower(StringHelper::camelCaseToSplit($this->controller)) . '/' . $filename;
            $this->modules && $filename = StringHelper::camelCaseToSplit(implode('/', $this->modules)) . '/' . $filename;
        }

        $dataVar = ['var' => objectArray(\YunShop::app()), 'request' => (\YunShop::request())];
        is_array($data) && $dataVar = array_merge($data, $dataVar);
        extract($dataVar);
        $var =array_shift($var);
        /*$request =array_shift($request);*/

        include $this->template($filename, $data);
        return ;
    }

    /**
     * 编译并获取模板路径
     * @param $filename
     * @return string
     */
    public function template($filename)
    {
        strpos($filename, 'web/') !== false && $filename = str_replace('web/', '', $filename);
        $compile = base_path() . "/data/tpl/{$filename}.tpl.php";
        $source = base_path() . "/template/web/{$filename}.html";
        if (DEVELOPMENT || !is_file($compile) || filemtime($source) > filemtime($compile)) {
            shop_template_compile($source, $compile, true);
        }
        return $compile;
    }


    public function viewWebUrl($url, $params = [])
    {
        if(empty($url)){
            return 'javascript:void(0)';
        }
        if(strpos($url, 'http://') === 0 ||
            strpos($url, 'https://') === 0 ||
            strpos($url, '/web/') === 0){
            return $url;
        }
        return $this->createWebUrl($url, is_array($params) ? $params : []);
    }

    /**
     * 生成后台url
     * @param $route  路由
     * @param $params 参数
     * @return string
     */
    public function createWebUrl($route, $params = [])
    {
        return Url::absoluteWeb($route, $params);
    }

    /**
     * 生成插件url
     * @param $route  路由
     * @param $params 参数
     * @return string
     */
    public function createPluginWebUrl($route, $params = [])
    {
        return Url::absoluteWeb($route, $params);
    }

    /**
     * 生成插件url
     * @param $route  路由
     * @param $params 参数
     * @return string
     */
    public function createPluginMobileUrl($route, $params = [])
    {
        return Url::absoluteApp($route, $params);
    }

    /**
     * 生成前台Url
     * @param $route  路由
     * @param $params 参数
     * @return string
     */
    public function createMobileUrl($route, $params = [])
    {
        return Url::absoluteApp($route, $params);
    }
}