<?php
/**
 * Created by PhpStorm.
 * User: liuyifan
 * Date: 2019/2/28
 * Time: 10:06
 */

namespace app\platform\modules\system\controllers;


use app\platform\controllers\BaseController;
use app\platform\modules\system\models\Attachment;
use app\platform\modules\system\models\SystemSetting;

class AttachmentController extends BaseController
{
    public $remote;

    public function __construct()
    {
        $this->remote = SystemSetting::settingLoad('remote', 'system_remote');
    }

    /**
     * 保存及显示全局设置
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function globals()
    {
        $post_max_size = ini_get('post_max_size');
        $post_max_size = $post_max_size > 0 ? bytecount($post_max_size) / 1024 : 0;
        $upload_max_filesize = ini_get('upload_max_filesize');
        $global = SystemSetting::settingLoad('global', 'system_global');
        $set_data = request()->upload;

        if ($set_data) {
            $validate = $this->validate($this->rules(''), $set_data, $this->message());
            if ($validate) {
                return $validate;
            }
            $attach = Attachment::saveGlobal($set_data, $post_max_size);

            if ($attach['result']) {
                return $this->successJson('成功');
            } else {
                return $this->errorJson($attach['msg']);
            }
        }

        $global['thumb_width'] = intval($global['thumb_width']);

        if ($global['image_extentions']['0']) {
            $global['image_extentions'] = implode("\n", $global['image_extentions']);
        }

        if ($global['audio_extentions']['0']) {
            $global['audio_extentions'] = implode("\n", $global['audio_extentions']);
        }

        return $this->successJson('成功', [
            'global' => $global,
            'post_max_size' => $post_max_size,
            'upload_max_filesize' => $upload_max_filesize
        ]);
    }

    /**
     * 保存及显示远程设置
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function remote()
    {
        $alioss = request()->alioss;
        $cos = request()->cos;

        if ($alioss || $cos) {
            if ($alioss['key']) {
                $validate  = $this->validate($this->rules(1), $alioss, $this->message());
            } else {
                $validate  = $this->validate($this->rules(2), $cos, $this->message());
            }
            if ($validate) {
                return $validate;
            }

            $attach = Attachment::saveRemote($alioss, $cos, $this->remote);

            if ($attach['result']) {
                return $this->successJson('成功');
            } else {
                return $this->errorJson($attach['msg']);
            }
        }

        $this->remote['alioss']['internal'] ? $this->remote['alioss']['internal'] = intval($this->remote['alioss']['internal']) : null;

        switch($this->remote['cos']['local']) {
            case 'tj':
                $this->remote['cos']['local'] = '华北';
                break;
            case 'sh':
                $this->remote['cos']['local'] = '华东';
                break;
            case 'gz':
                $this->remote['cos']['local'] = '华南';
                break;
            case 'cd':
                $this->remote['cos']['local'] = '西南';
                break;
            case 'bj':
                $this->remote['cos']['local'] = '北京';
                break;
            case 'sgp':
                $this->remote['cos']['local'] = '新加坡';
                break;
            case 'hk':
                $this->remote['cos']['local'] = '香港';
                break;
            case 'ca':
                $this->remote['cos']['local'] = '多伦多';
                break;
            case 'ger':
                $this->remote['cos']['local'] = '法兰克福';
                break;
        }
        
        return $this->successJson('成功', $this->remote);
    }

    /**
     * 验证数据
     *
     * @param array $rules
     * @param \Request|null $request
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(array $rules, $request = null, array $messages = [], array $customAttributes = [])
    {
        if (!isset($request)) {
            $request = request();
        }
        $validator = $this->getValidationFactory()->make($request, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            return $this->errorJson('失败', $validator->errors()->all());
        }
    }

    /**
     * 配置验证规则
     *
     * @param $param
     * @return array
     */
    public function rules($param)
    {
        $rules = [];
        if (request()->path() == "admin/system/globals") {
            $rules = [
                'image_extentions' => 'required',
                'image_limit' => 'required',
                'audio_extentions' => 'required',
                'audio_limit' => 'required',
            ];
        }

        if ($param == 1) {
            $rules = [
                'key' => 'required',
                'secret' => 'required',
            ];
        } elseif ($param == 2) {
            $rules = [
                'appid' => 'required',
                'secretid' => 'required',
                'secretkey' => 'required',
                'bucket' => 'required',
            ];
        }

        return $rules;
    }

    /**
     * 自定义错误信息
     *
     * @return array
     */
    public function message()
    {
        return [
            'image_extentions.required' => '图片后缀不能为空.',
            'image_limit.required' => '图片上传大小不能为空.',
            'audio_extentions.required' => '音频视频后缀不能为空.',
            'audio_limit.required' => '音频视频大小不能为空.',
            'key' => '阿里云OSS-Access Key ID不能为空',
            'secret' => '阿里云OSS-Access Key Secret不能为空',
            'appid' => '请填写APPID',
            'secretid' => '请填写SECRETID',
            'secretkey' => '请填写SECRETKEY',
            'bucket' => '请填写BUCKET'
        ];
    }

    /**
     * 阿里云搜索 bucket
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bucket()
    {
        $key = request()->key;
        $secret = request()->secret;

        $buckets = attachment_alioss_buctkets($key, $secret);
        if (is_error($buckets)) {
            return $this->errorJson($buckets['message']);
        }

        $bucket_datacenter = attachment_alioss_datacenters();
        $bucket = array();
        foreach ($buckets as $key => $value) {
            $value['loca_name'] = $key. '@@'. $bucket_datacenter[$value['location']];
            $value['value'] = $key. '@@'. $value['location'];
            $bucket[] = $value;
        }

        return $this->successJson('成功', $bucket);
    }

    /**
     * 测试阿里云配置是否成功
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function oss()
    {
        $alioss = request()->alioss;

        $secret = strexists($alioss['secret'], '*') ? $this->remote['alioss']['secret'] : $alioss['secret'];
        $buckets = attachment_alioss_buctkets($alioss['key'], $secret);
        list($bucket, $url) = explode('@@', $alioss['bucket']);

        $result = attachment_newalioss_auth($alioss['key'], $secret, $bucket, $alioss['internal']);
        if (is_error($result)) {
            return $this->errorJson('OSS-Access Key ID 或 OSS-Access Key Secret错误，请重新填写');
        }
        $ossurl = $buckets[$bucket]['location'].'.aliyuncs.com';
        if ($alioss['url']) {
            if (!strexists($alioss['url'], 'http://') && !strexists($alioss['url'],'https://')) {
                $url = 'http://'. trim($alioss['url']);
            } else {
                $url = trim($alioss['url']);
            }
            $url = trim($url, '/').'/';
        } else {
            $url = 'http://'.$bucket.'.'.$buckets[$bucket]['location'].'.aliyuncs.com/';
        }
        $filename = 'logo.png';
        $response = \Curl::to($url. '/'.$filename)->get();
        if (!$response) {
            return $this->errorJson('配置失败，阿里云访问url错误');
        }
        $image = getimagesizefromstring($response);
        if ($image && strexists($image['mime'], 'image')) {
            return $this->successJson('配置成功', request()->alioss);
        } else {
            return $this->errorJson('配置失败，阿里云访问url错误');
        }
    }

    /**
     * 测试腾讯云配置是否成功
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cos()
    {
        $cos = request()->cos;
        switch($cos['local']) {
            case '华北':
                $cos['local'] = 'tj';
                break;
            case '华东':
                $cos['local'] = 'sh';
                break;
            case '华南':
                $cos['local'] = 'gz';
                break;
            case '西南':
                $cos['local'] = 'cd';
                break;
            case '北京':
                $cos['local'] = 'bj';
                break;
            case '新加坡':
                $cos['local'] = 'sgp';
                break;
            case '香港':
                $cos['local'] = 'hk';
                break;
            case '多伦多':
                $cos['local'] = 'ca';
                break;
            case '法兰克福':
                $cos['local'] = 'ger';
                break;
        }

        $secretkey = strexists($cos['secretkey'], '*') ? $this->remote['cos']['secretkey'] : trim($cos['secretkey']);
        $bucket =  str_replace("-{$cos['appid']}", '', trim($cos['bucket']));

        if (!$cos['url']) {
            $cos['url'] = sprintf('https://%s-%s.cos%s.myqcloud.com', $bucket, $cos['appid'], $cos['local']);
        }
        $cos['url'] = rtrim($cos['url'], '/');
        $auth = attachment_cos_auth($bucket, $cos['appid'], $cos['secretid'], $secretkey, $cos['local']);

        if (is_error($auth)) {
            return $this->errorJson('配置失败，请检查配置' . $auth['message']);
        }
        $filename = 'logo.png';
        $response = \Curl::to($cos['url']. '/'. $filename)->get();
        if (!$response) {
            return $this->errorJson('配置失败，腾讯cos访问url错误');
        }
        $image = getimagesizefromstring($response);
        if ($image && strexists($image['mime'], 'image')) {
            return $this->successJson('配置成功', request()->cos);
        } else {
            return $this->errorJson('配置失败，腾讯cos访问url错误');
        }
    }

    public function sms()
    {
        $type = request()->type;

        if (request()->input()) {
            
            $data = request()->input();

            if ($data) {
                
                $res = SystemSetting::settingSave($data, 'sms', 'system_sms');

                if ($res) {
                    return $this->successJson('短信设置成功');
                } else {
                    return $this->errorJson('短信设置失败');
                }
            }
        }
    }
}