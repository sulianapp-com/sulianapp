<?php

use Illuminate\Support\Str;
use app\common\services\PermissionService;
use app\common\models\Menu;
use app\common\services\Session;
use app\common\exceptions\NotFoundException;


//商城根目录
define('SHOP_ROOT', dirname(__FILE__));


class YunShop
{
    private static $_req;
    private static $_app;
    private static $_plugin;
    private static $_notice;

    public function __construct()
    {

    }

    public static function run($namespace, $modules, $controllerName, $action, $currentRoutes)
    {
        //检测命名空间
        if (!class_exists($namespace)) {
            throw new NotFoundException(" 路由错误:不存在类: " . $namespace);
        }
        //检测controller继承
        $controller = new $namespace;
        if (!$controller instanceof \app\common\components\BaseController) {
            if (config('app.debug')) {
                throw new NotFoundException($controller . ' 没有继承\app\common\components\BaseController: ' . $namespace);
            }
            throw new NotFoundException(" 路由错误:不存在控制器: " . $namespace);

        }

        //设置默认方法
        if (empty($action)) {
            $action = 'index';
            self::app()->action = $action;
            $currentRoutes[] = $action;
        }

        //检测方法是否存在并可执行
        if (!method_exists($namespace, $action) || !is_callable([$namespace, $action])) {
            throw new NotFoundException('路由错误:操作方法不存在: ' . $action);
        }
        $controller->modules = $modules;
        $controller->controller = $controllerName;
        $controller->action = $action;
        $controller->route = implode('.', $currentRoutes);

        request()->setRoute($controller->route);

        //执行方法
        $controller->preAction();

        if (method_exists($controller, 'needTransaction') && $controller->needTransaction($action)) {
            // action设置了需要回滚
            $content = \Illuminate\Support\Facades\DB::transaction(function () use ($action, $controller) {
               return self::getContent($controller,$action);
            });
        } else {
            $content = self::getContent($controller,$action);
        }

        $time = app()->getTime();
        if($time > 1){
            (new \app\framework\Log\SlowApiLog())->add($time,request()->input());
        }
        return $content;
    }

    /**
     * Configures an object with the initial property values.
     * @param object $object the object to be configured
     * @param array $properties the property initial values given in terms of name-value pairs.
     * @return object the object itself
     */
    public static function configure($object, $properties)
    {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    public static function getAppNamespace()
    {
        $rootName = 'app';
        if (self::isWeb()) {
            $rootName .= '\\backend';
        }
        if (self::isApp() || self::isApi()) {
            $rootName .= '\\frontend';
        }
        return $rootName;
    }

    public static function getAppPath()
    {
        $path = dirname(__FILE__);
        if (self::isWeb()) {
            $path .= '/backend';
        }
        if (self::isApp() || self::isApi()) {
            $path .= '/frontend';
        }
        return $path;
    }

    public static function isPHPUnit()
    {
        return strpos($_SERVER['PHP_SELF'], 'phpunit') !== false ? true : false;

    }

    public static function isWeb()
    {
       return request()->isBackend();
    }

    public static function isApp()
    {
        if (self::isPHPUnit()) {
            return true;
        }
        return strpos($_SERVER['PHP_SELF'], '/app/index.php') !== false ? true : false;
    }

    public static function isApi()
    {
        return (strpos($_SERVER['PHP_SELF'], '/addons/') !== false &&
            strpos($_SERVER['PHP_SELF'], '/api.php') !== false) ? true : false;
    }

    /**
     *
     * @return bool
     */
    public static function isWechatApi()
    {
        if (config('app.framework') == 'platform') {
            return (strpos($_SERVER['REQUEST_URI'], '/wechat') !== false &&
                strpos($_SERVER['REQUEST_URI'], '/api') !== false) ? true : false;
        } else {
            return (strpos($_SERVER['PHP_SELF'], '/addons/') === false &&
                strpos($_SERVER['PHP_SELF'], '/api.php') !== false) ? true : false;
        }
    }

    /**
     * 是否插件
     * @return bool
     */
    public static function isPlugin()
    {
        if (config('app.framework') == 'platform') {
            return (strpos(request()->getRequestUri(), config('app.isWeb')) !== false &&
                strpos(request()->getRequestUri(), '/plugin') !== false) ? true : false;
        } else {
            return (strpos($_SERVER['PHP_SELF'], '/web/') !== false &&
                strpos($_SERVER['PHP_SELF'], '/plugin.php') !== false) ? true : false;
        }
    }

    /**
     * @name 验证是否商城操作员
     * @author
     * @return array|bool|null|stdClass
     */
    public static function isRole()
    {
        global $_W;

        if (app('plugins')->isEnabled('supplier')) {
            $res = \Illuminate\Support\Facades\DB::table('yz_supplier')->where('uid', $_W['uid'])->first();
            if (!$res) {
                return false;
            }
            return $res;
        }
        return false;
    }

    public static function isPayment()
    {
        return strpos($_SERVER['PHP_SELF'], '/payment/') > 0 ? true : false;
    }

    public static function request()
    {
        if (self::$_req !== null) {
            return self::$_req;
        } else {
            self::$_req = new YunRequest();
            return self::$_req;
        }
    }

    /**
     * @return YunApp
     */
    public static function app()
    {
        if (self::$_app !== null) {
            return self::$_app;
        } else {
            self::$_app = new YunApp();
            return self::$_app;
        }
    }

    /**
     * 解析路由
     *
     * 后台访问  /web/index.php?c=site&a=entry&m=sz_yi&do=xxx&route=module.controller.action
     * 前台      /app/index.php....
     *
     * 多字母的路由用中划线隔开 比如：
     *      TestCacheController
     *          function testClean()
     * 路由写法为   test-cache.test-clean
     *
     */
    public static function parseRoute($requestRoute)
    {
        try {
            $vers = [];
            $routes_params = explode('.', $requestRoute);


            if (preg_match('/(v\d+)\./', $requestRoute, $vers)) {
                foreach ($routes_params as $key => $item) {
                    if ($item != $vers[1]) {
                        $routes[] = $item;
                    }
                }
            } else {
                $routes = $routes_params;
            }


            $path = self::getAppPath();

            $namespace = self::getAppNamespace();
            $action = '';
            $controllerName = '';
            $currentRoutes = [];
            $modules = [];
            if ($routes) {
                $length = count($routes);
                $routeFirst = array_first($routes);
                $countRoute = count($routes);
                if ($routeFirst === 'plugin' || self::isPlugin()) {
                    if (self::isPlugin()) {
                        $currentRoutes[] = 'plugin';
                        $countRoute += 1;
                    } else {
                        $currentRoutes[] = $routeFirst;
                        array_shift($routes);
                    }
                    $namespace = 'Yunshop';
                    $pluginName = array_shift($routes);
                    if ($pluginName || plugin($pluginName)) {
                        $currentRoutes[] = $pluginName;
                        $namespace .= '\\' . ucfirst(Str::camel($pluginName));
                        $path = base_path() . '/plugins/' . $pluginName . '/src';
                        $length = $countRoute;

                        self::findRouteFile($controllerName, $action, $routes, $namespace, $path, $length, $currentRoutes, $requestRoute, true, $vers);

                        if (!app('plugins')->isEnabled($pluginName)) {
                            throw new NotFoundException("{$pluginName}插件已禁用");

                        }
                    } else {
                        throw new NotFoundException('无此插件');

                    }
                } else {

                    self::findRouteFile($controllerName, $action, $routes, $namespace, $path, $length, $currentRoutes, $requestRoute, false, $vers);

                }
            }
        } catch (Exception $exception) {
//            dd($exception);
//            exit;

        }
        //执行run
        return static::run($namespace, $modules, $controllerName, $action, $currentRoutes);

    }

    /**
     * 定位路由相关文件
     * @param $controllerName
     * @param $action
     * @param $routes
     * @param $namespace
     * @param $path
     * @param $length
     * @param $requestRoute
     * @param $isPlugin
     */
    public static function findRouteFile(&$controllerName, &$action, $routes, &$namespace, &$path, $length, &$currentRoutes, $requestRoute, $isPlugin, $vers)
    {

        foreach ($routes as $k => $r) {
            $ucFirstRoute = ucfirst(Str::camel($r));

            if (empty($vers)) {
                $controllerFile = $path . ($isPlugin ? '/' : '/controllers/') . $ucFirstRoute . 'Controller.php';
            } else {
                $controllerFile = $path . ($isPlugin ? '/' : '/controllers/') . 'vers/' . $vers[1] . '/' . $ucFirstRoute . 'Controller.php';
            }

            if (is_file($controllerFile)) {

                if (empty($vers)) {
                    $namespace .= ($isPlugin ? '' : '\\controllers') . '\\' . $ucFirstRoute . 'Controller';
                } else {
                    $namespace .= ($isPlugin ? '\\' : '\\controllers\\') . 'vers\\' . $vers[1] . '\\' . $ucFirstRoute . 'Controller';
                }

                $controllerName = $ucFirstRoute;
                $path = $controllerFile;
                $currentRoutes[] = $r;
            } elseif (is_dir($path .= ($isPlugin ? '' : '/modules') . '/' . $r)) {
                $namespace .= ($isPlugin ? '' : '\\modules') . '\\' . $r;
                $modules[] = $r;
                $currentRoutes[] = $r;
            } else {
                if ($length !== ($isPlugin ? $k + 3 : $k + 1)) {
                    throw new NotFoundException('路由长度有误:' . $requestRoute);

                }
                $action = strpos($r, '-') === false ? $r : Str::camel($r);
                $currentRoutes[] = $r;
            }
        }

    }

    public static function getUcfirstName($name)
    {
        if (strpos($name, '-')) {
            $names = explode('-', $name);
            $name = '';
            foreach ($names as $v) {
                $name .= ucfirst($v);
            }
        }
        return ucfirst($name);
    }

    public static function plugin()
    {
        self::$_plugin = new YunPlugin();
        return self::$_plugin;
    }

    public static function notice()
    {
        self::$_notice = new YunNotice();
        return self::$_notice;
    }

    private static function getContent($controller,$action){

        return (new \Illuminate\Pipeline\Pipeline(new \Illuminate\Container\Container()))
            ->send(Illuminate\Http\Request::capture())
            ->through(collect($controller->getMiddleware())->pluck('middleware')->all())
            ->then(function ($request) use ($controller,$action) {
                return $controller->$action($request);
            });
    }
}

class YunComponent implements ArrayAccess
{
    protected $values = [];

    public function __set($name, $value)
    {
        return $this->values[$name] = $value;
    }

    public function __get($name)
    {
        if (!array_key_exists($name, $this->values)) {
            $this->values[$name] = null;
        }
        return $this->values[$name];
    }

    function __isset($name) {
        return array_key_exists($name, $this->values);
    }

    public function set($name, $value)
    {
        $this->values[$name] = $value;
        return $this;
    }

    public function get($key = null)
    {
        if (isset($key)) {
            $result = json_decode(array_get($this->values, $key, null), true);
            if (@is_array($result)) {
                return $result;
            }
            return array_get($this->values, $key, null);
        }
        return $this->values;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    public function offsetGet($offset)
    {
        if (isset($this->values[$offset])) {
            return $this->values[$offset];
        }
        return null;
    }

    public function offsetExists($offset)
    {
        if (isset($this->values[$offset])) {
            return true;
        }
        return false;
    }
}

class YunRequest extends YunComponent
{

    public function __construct()
    {
        global $_GPC;

        $this->values = !YunShop::isWeb() && !YunShop::isWechatApi() ? request()->input() : $_GPC;
    }


}

/**
 * Class YunApp
 * @property int uniacid
 * @property int uid
 */
class YunApp extends YunComponent
{
    protected $values;
    public $currentItems = [];

    public function __construct()
    {
        global $_W;

        $this->values = !YunShop::isWeb() && !YunShop::isWechatApi() ? $this->getW() : (array)$_W;


    }

    public function getW()
    {
        $account = \app\common\models\AccountWechats::getAccountByUniacid(request()->get('i'));
        return [
            'uniacid' => trim(request()->get('i')),
            'weid' => trim(request()->get('i')),
            'acid' => trim(request()->get('i')),
            'account' => $account ? $account->toArray() : '',
        ];
    }



    /**
     * @todo set member id from session
     * @return int
     */
    public function getMemberId()
    {
        if(isset($_GET['test_uid'])){
            return $_GET['test_uid'];
        }
        $member_id = 0;
        $type = \Yunshop::request()->type ?: '';
        $token = \Yunshop::request()->yz_token ?: '';

         switch ($type) {
             case 9:
                 $native_app = new \app\frontend\modules\member\services\MemberNativeAppService();

                 $member_id = $native_app->getMemberId($token);

                 break;
             default:
                 if (Session::get('member_id')) {
                     $member_id = Session::get('member_id');
                 }
         }

         return $member_id;
    }

}

class YunPlugin
{
    protected $values;

    public function __construct()
    {
        $this->values = false;
    }

    /**
     * @param null $key
     * @return bool
     */
    public function get($key = null)
    {
        if (isset($key)) {

            if (app('plugins')->isEnabled($key)) {
                return true;
            }
        }
        return $this->values;
    }

}

class YunNotice
{
    protected $key;
    protected $value;

    public function __construct()
    {
        $this->key = 'shop';
    }

    /**
     * @param null $key
     * @return bool
     */
    public function getNotSend($routes = null)
    {
        $this->value = $routes;
        $routesData = explode('.', $routes);
        if (count($routesData) > 1) {
            $this->key = $routesData[0];
            $this->value = $routesData[1];
        }

        $noticeConfig = Config::get('notice.' . $this->key);

        return in_array($this->value, $noticeConfig) ? 0 : 1;
    }

}
