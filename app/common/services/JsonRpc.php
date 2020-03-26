<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 14/04/2017
 * Time: 20:11
 */

namespace app\common\services;

use JsonRPC\Client;

/**
 * Class JsonRpc
 *
 * @package app\common\services
 */
class JsonRpc
{
    protected $url = '';

    public function __construct($url = '')
    {
        $this->url = $url;
        if(!$this->url){
            $this->url = \Config::get('rpc.client.url');
        }
    }

    public function client($method, $params = [])
    {
        $client = new Client($this->url);
        //$client->authentication('jan','123');
        return $client->execute($method, $params);
    }
}