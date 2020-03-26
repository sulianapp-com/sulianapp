<?php
namespace app\common\helpers;
/**
 * Url生成类
 *
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 18:02
 */
class Url
{
    public static function shopUrl($uri)
    {
        if(empty($uri) || self::isHttp($uri)){
            return $uri;
        }
        //$domain = request()->getSchemeAndHttpHost();
        $module = request()->get('m','yun_shop');
        return self::getPath($module) . (strpos($uri,'/') === 0 ? '':'/') . $uri;
    }

    public static function shopSchemeUrl($uri)
    {
        if(empty($uri) || self::isHttp($uri)){
            return $uri;
        }
        $domain = request()->getSchemeAndHttpHost();
        $module = request()->get('m','yun_shop');

        return $domain . self::getPath($module) . (strpos($uri,'/') === 0 ? '':'/') . $uri;
    }

    /**
     * 生成后台相对Url
     *      路由   api.v1.test.index  为  app/backend/moduels/api/modules/v1/TestController   index
     * @param $route
     * @param array $params
     * @return string
     */
    public static function web($route, $params = [])
    {
        if(self::isHttp($route)){
            return $route;
        }
        $defaultParams = ['c'=>'site','a'=>'entry','m'=>'yun_shop','do'=>rand(1000,9999),'route'=>$route];
        $params = array_merge($defaultParams, $params);

        if (config('app.framework') == 'platform') {
            return  config('app.isWeb'). '?'. http_build_query($params);
        } else {
            return  '/web/index.php?'. http_build_query($params);
        }

    }

    /**
     * 生成前台相对Url
     *      路由   api.v1.test.index  为  app/frontend/moduels/api/modules/v1/TestController   index
     * @param $route
     * @param array $params
     * @return string
     */
    public static function app($route, $params = [])
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        if(strpos($route, '/') !== 0){
            $route = '/' . $route;
        }
        if(!isset($params['i'])){
            $params['i'] = \YunShop::app()->uniacid;
        }
        $module = request()->get('m','yun_shop');
        return   '/addons/' . $module . '/?menu#'.$route .  ($params ? '?'.http_build_query($params) : '');
    }

    public static function appDiy($route, $params = [])
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        if(strpos($route, '/') !== 0){
            $route = '/' . $route;
        }
        if(!isset($params['i'])){
            $params['i'] = \YunShop::app()->uniacid;
        }
        $module = request()->get('m','yun_shop');
        return   '/addons/' . $module . '/?menu#'.$route .  ($params ? '/'. $params['page_id'] . '/?i=' . $params['i'] : '');
    }

    /**
     *  前端api接口相对Url
     *
     * @param $route
     * @param array $params
     * @return string
     */
    public static function api($route, $params = [])
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        $defaultParams = ['i'=>\YunShop::app()->uniacid,'route'=>$route];
        $params = array_merge($defaultParams, $params);

        return  '/addons/yun_shop/api.php?'. http_build_query($params);
    }

    /**
     *  前端api接口相对Url
     *
     * @param $route
     * @param array $params
     * @return string
     */
    public static function plugin($route, $params = [])
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        $defaultParams = ['i'=>\YunShop::app()->uniacid,'route'=>$route];
        $params = array_merge($defaultParams, $params);

        if (config('app.framework') == 'platform') {
            return   config('app.isWeb') . '/plugig?'. http_build_query($params);
        } else {
            return   '/web/plugin.php?'. http_build_query($params);
        }
    }

    /**
     * 生成后台绝对地址
     *  路由   api.v1.test.index  为  app/backend/moduels/api/modules/v1/TestController   index
     *
     * @param $route
     * @param array $params
     * @param string $domain
     * @return string
     */
    public static function absoluteWeb($route, $params = [], $domain = '')
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        empty($domain) && ($domain = request()->getSchemeAndHttpHost());

        return $domain . self::web($route,$params);
    }

    /**
     * 生成前台绝对地址
     *      路由   /home
     * @param $route
     * @param array $params
     * @param string $domain
     * @return string
     */
    public static function absoluteApp($route, $params = [], $domain = '')
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::app($route,$params);
    }

    public static function absoluteDiyApp($route, $params = [], $domain = '')
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::appDiy($route,$params);
    }

    /**
     * 生成Api绝对URL地址
     * @param $route
     * @param array $params
     * @param string $domain
     * @return string
     */
    public static function absoluteApi($route, $params = [], $domain = '')
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();
        return $domain . self::api($route,$params);
    }

    /**
     * 生成插件绝对URL地址
     * @param $route
     * @param array $params
     * @param string $domain
     * @return string
     */
    public static function absolutePlugin($route, $params = [], $domain = '')
    {
        if(empty($route) || self::isHttp($route)){
            return $route;
        }
        empty($domain) && $domain = request()->getSchemeAndHttpHost();

        return $domain . self::plugin($route,$params);
    }

    public static function isHttp($url)
    {
        return (strpos($url,'http://') === 0 || strpos($url,'https://') === 0);
    }

    public static function getPath($module)
    {
        if (config('app.framework') == 'platform') {
            return '';
        }

        return '/addons/' . $module;
    }
}