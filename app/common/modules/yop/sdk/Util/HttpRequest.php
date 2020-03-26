<?php

/**
 * Created by PhpStorm.
 * User: wilson
 * Date: 16/7/7
 * Time: 11:31
 */

namespace app\common\modules\yop\sdk\Util;

use app\common\modules\yop\sdk\Util\HttpUtils;

abstract class HttpRequest{


    /**
     * 加密
     * @param string $str	    需加密的字符串
     * @param string $key	    密钥
     * @param string $CIPHER	算法
     * @param string $MODE	    模式
     * @return type
     */


    static public function curl_request($url,$post, $timeout=120, $json=false, $Multipart =false,$headers=null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'YOP_PHP_Client_API');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);


        if($headers!=null) {
            $headerArray=array();
            foreach ($headers as  $key => $value) {
                array_push($headerArray,$key.":".$value);
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER,  $headerArray);
        }

        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($post)?http_build_query($post):$post);
        }



        if($Multipart){
            curl_setopt ($curl, CURLOPT_SAFE_UPLOAD, false);
            curl_setopt($curl, CURLOPT_POST, 1);

            $file_name = str_replace("%2F", "/",$post["_file"]);

            // 从php5.5开始,反对使用"@"前缀方式上传,可以使用CURLFile替代;
            // 据说php5.6开始移除了"@"前缀上传的方式
            if (class_exists('CURLFile')) {
                $file = new \CURLFile($file_name);
                // 禁用"@"上传方法,这样就可以安全的传输"@"开头的参数值
                curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            } else {
                $file = "@{$file_name}";
            }

            $fields = $post;
            //$fields ['_file'] = $file;
            unset($fields['_file']);
            $fields ['merQual'] = $file;

            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);

        }
        $TLS = substr($url, 0, 8) == "https://" ? true : false;
        if($TLS) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }

        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);


        if ($json){
            return json_decode($data,true);
        }
        return $data;

    }
}
