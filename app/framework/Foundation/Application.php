<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/6
 * Time: 下午3:30
 */

namespace app\framework\Foundation;

use app\framework\Routing\RoutingServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Log\LogServiceProvider;

class Application extends \Illuminate\Foundation\Application
{
    public $makingRouteList;
    private $configurationIsCached;

    private $timer = LARAVEL_START;
    public function getTime()
    {
        $result = microtime(true) - $this->timer;
        $this->timer = microtime(true);
        return $result;
    }

    /**
     * @param null $file
     * @return string
     */
    public function getRoutesPath($file = null)
    {
        $file = !empty($file) ? DIRECTORY_SEPARATOR . $file : '';
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes' . $file;
    }

    public function getRoutesDataPath($file = null)
    {
        $file = !empty($file) ? DIRECTORY_SEPARATOR . $file : '';
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'data' . $file;
    }

    public function getUrlRoutesPath($file = null)
    {
        $file = !empty($file) ? DIRECTORY_SEPARATOR . $file : '';
        return $this->basePath() . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'urlRoutes' . $file;
    }

    public function getFrontendPath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'frontend';
    }

    public function getBackendPath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'backend';
    }

    public function getPluginsPath()
    {
        return $this->basePath() . DIRECTORY_SEPARATOR . 'plugins';
    }

    public function getPaymentPath()
    {
        return $this->path() . DIRECTORY_SEPARATOR . 'payment';
    }

    public function makingRouteList()
    {
        // todo 暂时解决
        return (bool)$this->makingRouteList;

    }



    /**
     * @return bool
     */
    public function configurationIsCached()
    {
        if (!isset($this->configurationIsCached)) {
            $this->configurationIsCached = parent::configurationIsCached();
        }
        return $this->configurationIsCached;
    }
}