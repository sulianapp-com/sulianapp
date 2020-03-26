<?php

namespace app\common\services;

use ArrayAccess;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable;
use app\common\helpers\Url;

/**
 * @property string $name
 * @property string $description
 * @property string $title
 * @property array $author
 */
class Plugin implements Arrayable, ArrayAccess
{
    /**
     * @var PluginApplication
     */
    protected $pluginApp;
    /**
     * The full directory of this plugin.
     *
     * @var string
     */
    protected $path;

    /**
     * The directory name where the plugin installed.
     *
     * @var string
     */
    protected $dirname;

    /**
     * package.json of the package.
     *
     * @var array
     */
    protected $packageInfo;

    /**
     * Whether the plugin is installed.
     *
     * @var bool
     */
    protected $installed = true;


    /**
     * Whether the plugin is enabled.
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * @param       $path
     * @param array $packageInfo
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->packageInfo = $this->getPackageInfo();
    }
    public function getPackageInfo(){

        return json_decode(app('files')->get($this->path . "/package.json"), true);
    }
    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        return $this->packageInfoAttribute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        return isset($this->{$name}) || $this->packageInfoAttribute(snake_case($name, '-'));
    }

    /**
     * Dot notation getter for composer.json attributes.
     *
     * @see https://laravel.com/docs/5.1/helpers#arrays
     *
     * @param $name
     * @return mixed
     */
    public function packageInfoAttribute($name)
    {
        if(!array_key_exists($name,$this->packageInfo)){
            return null;
        }
        return $this->packageInfo[$name];
    }

    public function assets($relativeUri)
    {
        return Url::shopUrl("plugins/{$this->getDirname()}/$relativeUri");
    }

    /**
     * @param bool $installed
     * @return Plugin
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;

        return $this;
    }

    public function getId()
    {
        if(!isset($this->id)){
            $idConfig =  array_first(\app\common\modules\shop\ShopConfig::current()->get('plugin'), function ($v) {
                return $v['name'] == $this->name;
            });
            if($idConfig){
                return $idConfig['id'];
            }else{
                return '';
            }
        }
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    public function getDirname()
    {
        return $this->name;
    }

    public function setDirname($dirname)
    {
        $this->dirname = $dirname;

        return $this;
    }

    public function getNameSpace()
    {
        return $this->namespace;
    }

    public function setNameSpace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getViewPath($name)
    {
        return $this->getViewPathByFileName("$name.tpl");
    }

    public function getViewPathByFileName($filename)
    {
        return $this->path . "/views/$filename";
    }

    public function getConfigView()
    {
        return $this->hasConfigView() ? view()->file($this->getViewPathByFileName(Arr::get($this->packageInfo, 'config'))) : null;
    }

    public function hasConfigView()
    {
        $filename = Arr::get($this->packageInfo, 'config');

        return $filename && file_exists($this->getViewPathByFileName($filename));
    }

    /**
     * @param string $version
     * @return Plugin
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param bool $enabled
     * @return Plugin
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Determine if the given option option exists.
     *
     * @param  string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return Arr::has($this->packageInfo, $key);
    }

    /**
     * Get a option option.
     *
     * @param  string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->packageInfoAttribute($key);
    }

    /**
     * Set a option option.
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    public function offsetSet($key, $value)
    {
        return Arr::set($this->packageInfo, $key, $value);
    }

    /**
     * Unset a option option.
     *
     * @param  string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->packageInfo[$key]);
    }

    /**
     * Generates an array result for the object.
     *
     * @return array
     */
    public function toArray()
    {
        return (array)array_merge([
            'name' => $this->name,
            'version' => $this->getVersion(),
            'path' => $this->path
        ], $this->packageInfo);
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function app()
    {
        if (!isset($this->pluginApp)) {

            $pluginApplicationClass = $this->namespace . '\\' . 'PluginApplication';

            if (class_exists($pluginApplicationClass)) {

                $this->pluginApp = new $pluginApplicationClass($this);
            } else {

                $this->pluginApp = new PluginApplication($this);
            }
        }
        return $this->pluginApp;
    }
}
