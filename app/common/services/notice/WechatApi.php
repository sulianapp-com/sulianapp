<?php

/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/7
 * Time: 下午5:10
 */

namespace app\common\services\notice;


use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\models\AccountWechats;
use app\common\traits\JsonTrait;

class WechatApi
{
    use JsonTrait;
    private $account;

    public function __construct()
    {
        $this->account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);
    }

    /**
     * @name 通过模板编号从行业模板库选择模板到帐号后台
     * @author
     * @param $templateIdShort
     * @return array
     */
    public function getTmpByTemplateIdShort($templateIdShort)
    {
        $param = '{"template_id_short":"' . $templateIdShort . '"}';
        $http_result = $this->ihttp_request($this->getTmpUrl(), $param);
        $result = @json_decode($http_result['content'], true);
        return $this->commonReturn($result);
    }

    /**
     * @name 通过模板编号从行业模板库获取模版id
     * @author
     * @param $templateIdShort
     * @return array
     */
    public function getTemplateIdByTemplateIdShort($templateIdShort)
    {
        $param = '{"template_id_short":"' . $templateIdShort . '"}';
        $http_result = $this->ihttp_request($this->getTmpUrl(), $param);
        $result = @json_decode($http_result['content'], true);
        return $result['template_id'];
    }

    /**
     * @name 通过模板ID删除
     * @author
     * @param $templateId
     * @return array
     */
    public function delTmpByTemplateId($templateId)
    {
        $param = '{"template_id":"' . $templateId . '"}';
        $http_result = $this->ihttp_request($this->delTmpUrl(), $param);
        $result = @json_decode($http_result['content'], true);
        return $this->commonReturn($result);
    }

    /**
     * @name 获取公众号模板列表
     * @author
     * @return \Illuminate\Http\JsonResponse
     * @throws ShopException
     */
    public function getTmpList()
    {
        $http_result = $this->ihttp_request($this->getTmpListUrl());
        $result = @json_decode($http_result['content'], true);
        if (!is_array($result)) {
            throw new ShopException('请求失败');
        }
        if (!(empty($result['errcode']))) {
            throw new ShopException($result['errmsg']);
        }
        foreach ($result['template_list'] as $key => &$value )
        {
            preg_match_all('{{(.)*?}}', $value['content'], $matches);
            foreach ($matches[0] as &$v )
            {
                $v = str_replace(array('{', '}', '.DATA'), '', $v);
            }
            unset($v);
            $value['contents'] = $matches[0];
            $result['template_list'][$key]['content'] = str_replace(array("\n\n", "\n"), '<br />', $value['content']);
        }
        unset($value);
        return $result;
    }

    /**
     * @name 获取公众平台模板消息目前所属行业
     * @author
     * @return bool|mixed
     */
    public function getIndustry()
    {
        $http_result = $this->ihttp_request($this->getIndustryUrl());
        $result = @json_decode($http_result['content'], true);
        if (!is_array($result)) {
            return false;
        }
        if (!(empty($result['errcode']))) {
            return false;
        }
        return $result;
    }

    /**
     * @name 获取公众平台模板消息目前所属行业文本
     * @author
     * @param $industry
     * @return string
     */
    public function getIndustryText($industry)
    {
        $industrytext = '';
        if ($industry && is_array($industry)) {
            foreach ($industry as $item) {
                $industrytext .= $item['first_class'] . '/' . $item['second_class'] . '&nbsp;&nbsp;&nbsp;';
            }
        }
        return $industrytext;
    }

    private function commonReturn($result)
    {
        if (!is_array($result)) {
            return [
                'status' => 0,
                'msg'    => '请求失败'
            ];
        }
        if (!(empty($result['errcode']))) {
            return [
                'status' => 0,
                'msg'    => $result['errcode']
            ];
        }
        return [
            'status' => 1,
            'msg'    => '成功'
        ];
    }

    private function getAccessToken()
    {
        $global_access_token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->account->key . '&secret=' . $this->account->secret;
        $global_token = \Curl::to($global_access_token_url)
            ->asJsonResponse(true)
            ->get();

        if (isset($global_token['errcode']) && isset($global_token['errmsg'])) {
           throw new AppException($global_token['errmsg']);
        }

        return $global_token['access_token'];
    }

    private function getTmpUrl()
    {
        return 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=' . $this->getAccessToken();
    }

    private function delTmpUrl()
    {
        return 'https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=' . $this->getAccessToken();
    }

    private function getTmpListUrl()
    {
        return 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=' . $this->getAccessToken();
    }

    private function getIndustryUrl()
    {
        return 'https://api.weixin.qq.com/cgi-bin/template/get_industry?access_token=' . $this->getAccessToken();
    }

    private function ihttp_request($url, $post = '', $extra = array(), $timeout = 60) {
        $urlset = parse_url($url);
        if (empty($urlset['path'])) {
            $urlset['path'] = '/';
        }
        if (!empty($urlset['query'])) {
            $urlset['query'] = "?{$urlset['query']}";
        }
        if (empty($urlset['port'])) {
            $urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
        }
        if (strexists($url, 'https://') && !extension_loaded('openssl')) {
            if (!extension_loaded("openssl")) {
                message('请开启您PHP环境的openssl');
            }
        }
        if (function_exists('curl_init') && function_exists('curl_exec')) {
            $ch = curl_init();
            if (ver_compare(phpversion(), '5.6') >= 0) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
            if (!empty($extra['ip'])) {
                $extra['Host'] = $urlset['host'];
                $urlset['host'] = $extra['ip'];
                unset($extra['ip']);
            }
            curl_setopt($ch, CURLOPT_URL, $urlset['scheme'] . '://' . $urlset['host'] . ($urlset['port'] == '80' ? '' : ':' . $urlset['port']) . $urlset['path'] . $urlset['query']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            @curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            if ($post) {
                if (is_array($post)) {
                    $filepost = false;
                    foreach ($post as $name => $value) {
                        if ((is_string($value) && substr($value, 0, 1) == '@') || (class_exists('CURLFile') && $value instanceof CURLFile)) {
                            $filepost = true;
                            break;
                        }
                    }
                    if (!$filepost) {
                        $post = http_build_query($post);
                    }
                }
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
            if (!empty($GLOBALS['_W']['config']['setting']['proxy'])) {
                $urls = parse_url($GLOBALS['_W']['config']['setting']['proxy']['host']);
                if (!empty($urls['host'])) {
                    curl_setopt($ch, CURLOPT_PROXY, "{$urls['host']}:{$urls['port']}");
                    $proxytype = 'CURLPROXY_' . strtoupper($urls['scheme']);
                    if (!empty($urls['scheme']) && defined($proxytype)) {
                        curl_setopt($ch, CURLOPT_PROXYTYPE, constant($proxytype));
                    } else {
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
                    }
                    if (!empty($GLOBALS['_W']['config']['setting']['proxy']['auth'])) {
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS['_W']['config']['setting']['proxy']['auth']);
                    }
                }
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
            if (defined('CURL_SSLVERSION_TLSv1')) {
                curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            }
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
            if (!empty($extra) && is_array($extra)) {
                $headers = array();
                foreach ($extra as $opt => $value) {
                    if (strexists($opt, 'CURLOPT_')) {
                        curl_setopt($ch, constant($opt), $value);
                    } elseif (is_numeric($opt)) {
                        curl_setopt($ch, $opt, $value);
                    } else {
                        $headers[] = "{$opt}: {$value}";
                    }
                }
                if (!empty($headers)) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }
            }
            $data = curl_exec($ch);
            $status = curl_getinfo($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            if ($errno || empty($data)) {
                return error(1, $error);
            } else {
                return $this->ihttp_response_parse($data);
            }
        }
        $method = empty($post) ? 'GET' : 'POST';
        $fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
        $fdata .= "Host: {$urlset['host']}\r\n";
        if (function_exists('gzdecode')) {
            $fdata .= "Accept-Encoding: gzip, deflate\r\n";
        }
        $fdata .= "Connection: close\r\n";
        if (!empty($extra) && is_array($extra)) {
            foreach ($extra as $opt => $value) {
                if (!strexists($opt, 'CURLOPT_')) {
                    $fdata .= "{$opt}: {$value}\r\n";
                }
            }
        }
        $body = '';
        if ($post) {
            if (is_array($post)) {
                $body = http_build_query($post);
            } else {
                $body = urlencode($post);
            }
            $fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
        } else {
            $fdata .= "\r\n";
        }
        if ($urlset['scheme'] == 'https') {
            $fp = fsockopen('ssl://' . $urlset['host'], $urlset['port'], $errno, $error);
        } else {
            $fp = fsockopen($urlset['host'], $urlset['port'], $errno, $error);
        }
        stream_set_blocking($fp, true);
        stream_set_timeout($fp, $timeout);
        if (!$fp) {
            return error(1, $error);
        } else {
            fwrite($fp, $fdata);
            $content = '';
            while (!feof($fp))
                $content .= fgets($fp, 512);
            fclose($fp);
            return $this->ihttp_response_parse($content, true);
        }
    }

    private function ihttp_response_parse($data, $chunked = false) {
        $rlt = array();
        $headermeta = explode('HTTP/', $data);
        if (count($headermeta) > 2) {
            $data = 'HTTP/' . array_pop($headermeta);
        }
        $pos = strpos($data, "\r\n\r\n");
        $split1[0] = substr($data, 0, $pos);
        $split1[1] = substr($data, $pos + 4, strlen($data));

        $split2 = explode("\r\n", $split1[0], 2);
        preg_match('/^(\S+) (\S+) (\S+)$/', $split2[0], $matches);
        $rlt['code'] = $matches[2];
        $rlt['status'] = $matches[3];
        $rlt['responseline'] = $split2[0];
        $header = explode("\r\n", $split2[1]);
        $isgzip = false;
        $ischunk = false;
        foreach ($header as $v) {
            $pos = strpos($v, ':');
            $key = substr($v, 0, $pos);
            $value = trim(substr($v, $pos + 1));
            if (is_array($rlt['headers'][$key])) {
                $rlt['headers'][$key][] = $value;
            } elseif (!empty($rlt['headers'][$key])) {
                $temp = $rlt['headers'][$key];
                unset($rlt['headers'][$key]);
                $rlt['headers'][$key][] = $temp;
                $rlt['headers'][$key][] = $value;
            } else {
                $rlt['headers'][$key] = $value;
            }
            if(!$isgzip && strtolower($key) == 'content-encoding' && strtolower($value) == 'gzip') {
                $isgzip = true;
            }
            if(!$ischunk && strtolower($key) == 'transfer-encoding' && strtolower($value) == 'chunked') {
                $ischunk = true;
            }
        }
        if($chunked && $ischunk) {
            $rlt['content'] = ihttp_response_parse_unchunk($split1[1]);
        } else {
            $rlt['content'] = $split1[1];
        }
        if($isgzip && function_exists('gzdecode')) {
            $rlt['content'] = gzdecode($rlt['content']);
        }

        $rlt['meta'] = $data;
        if($rlt['code'] == '100') {
            return ihttp_response_parse($rlt['content']);
        }
        return $rlt;
    }
}