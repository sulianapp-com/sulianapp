<?php
/**
 * Created by PhpStorm.
 * User: 芸众网
 * Date: 2019/7/1
 * Time: 13:43
 */

namespace app\backend\modules\setting\controllers;

use app\common\components\BaseController;
use app\common\helpers\ImageHelper;
use app\common\helpers\Url;
use app\common\models\WebSiteInfo;
use Illuminate\Support\Facades\Storage;

class WorkOrderController extends BaseController
{
    // 工单管理
    public function index()
    {
        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(config('auto-update.workOrderUrl') . '/work-order/get-work-order-list/' . $_SERVER['HTTP_HOST'] . '/0' . '/0' . '/0' . '/0' . '/0' . '/0')
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->asJsonResponse(true)
            ->get();
        if ($data && $data['result'] == 1) {
            return view('setting.work-order.list', [
                'data' => json_encode($data['data']),
                'category_list' => json_encode($this->category()),
                'status_list' => json_encode($this->status()),
            ])->render();
        } else {
            return view('setting.work-order.list', [
                'data' => json_encode([]),
                'category_list' => json_encode($this->category()),
                'status_list' => json_encode($this->status()), ])->render();
        }
    }

    /**
     * 工单检索
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $url = config('auto-update.workOrderUrl') . '/work-order/get-work-order-list/' . $_SERVER['HTTP_HOST'];
        $data = request()->input('data');
        if ($data['category_id']) {
            $url = $url . '/' . $data['category_id'];
        } else {
            $url = $url . '/' . '0';
        }
        if ($data['status']) {
            $url = $url . '/' . $data['status'];
        } else {
            $url = $url . '/' . '0';
        }
        if ($data['work_order_sn']) {
            $url = $url . '/' . $data['work_order_sn'];
        } else {
            $url = $url . '/' . '0';
        }
        if ($data['has_time_limit']) {
            //需要搜索时间
            $url = $url . '/' . $data['has_time_limit'] . '/' . $data['start_time'] / 1000 . '/' . $data['end_time'] / 1000;
        } else {
            $url = $url . '/' . '0' . '/' . '0' . '/' . '0';
        }
        if ($data['page']) {
            $url = $url . '?page=' . $data['page'];
        }

        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to($url)
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->asJsonResponse(true)
            ->get();
        if ($data && $data['result'] == 1) {
            return $this->successJson('成功', $data['data']);
        } else {
            return $this->errorJson('失败');
        }
    }

    private function getFileType()
    {
        return ['docx','ai','avi','txt','jpg','png','jpeg','bmp','cdr','doc','eps','gif','html','mp3','mp4','pdf','ppt','pr','psd','rar','svg','gif','xlsx','zip'];
    }

    /**
     * 上传
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile()
    {
        $file = request()->file('file');

        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }

        if ($file->isValid()) {
            $ext = $file->getClientOriginalExtension();
            if(!in_array($ext,$this->getFileType())){
                return $this->errorJson('请检查文件后缀是否支持');exit();
            }

            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $realPath = $file->getRealPath(); //临时文件的绝对路径
            $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;
            Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));

            if (config('APP_Framework') == 'platform') {
                $attachment = 'static/upload/';
            } else {
                $attachment = 'attachment/';
            }

            return $this->successJson('上传成功', [
                'thumb' => \Storage::disk('image')->url($newOriginalName),
                'thumb_url' => ImageHelper::getImageUrl($attachment . substr(\Storage::disk('image')->url($newOriginalName), strripos(\Storage::disk('image')->url($newOriginalName), "image"))),
            ]);
        }
        return $this->errorJson($file->getErrorMessage());
    }

    /**
     * base64图片上传
     * @return \Illuminate\Http\JsonResponse
     */
    public function base64Upload()
    {
        $base_img = request()->input('file');
        $base_img = str_replace('data:image/jpg;base64,', '', $base_img);
        $newOriginalName = time() . rand(100, 999) . '.png';
        Storage::disk('image')->put($newOriginalName, file_get_contents($base_img));
        if (config('APP_Framework') == 'platform') {
            $attachment = 'static/upload/';
        } else {
            $attachment = 'attachment/';
        }
        return $this->successJson('上传成功', [
            'thumb' => \Storage::disk('image')->url($newOriginalName),
            'thumb_url' => ImageHelper::getImageUrl($attachment . substr(\Storage::disk('image')->url($newOriginalName), strripos(\Storage::disk('image')->url($newOriginalName), "image"))),
        ]);
    }

    /**
     * 详情
     * @return mixed|string
     */
    public function details()
    {
        $id = request()->input('id');
        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(config('auto-update.workOrderUrl') . '/work-order/details/' . $id)
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->asJsonResponse(true)
            ->get();
        if ($data && $data['result'] == 1) {
            foreach ($data['data']['has_many_comment'] as $key => $value) {
                $data['data']['has_many_comment'][$key]['thumb_url'] = json_decode($value['thumb_url']);
            }
            return view('setting.work-order.details', ['data' => json_encode($data['data'])])->render();
        } else {
            return $this->message('ID不存在', Url::absoluteWeb('setting.work-order.index'));
        }
    }

    /**
     * 评论接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function comment()
    {
        $postData = request()->input();
        $this->validateCommentParam();
        $data = [
            'work_order_id' => $postData['work_order_id'],
            'content' => $postData['content'],
            'thumb_url' => json_encode($postData['thumb_url']),
            'work_order' => 1,
            'domain' => $_SERVER['HTTP_HOST'],
        ];

        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(config('auto-update.workOrderUrl') . '/work-order/comment')
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->withData($data)
            ->asJsonResponse(true)
            ->post();
        if (!$data) {
            $this->errorJson('远端服务器异常');
        }
        if ($data['result'] == 0) {
            return $this->errorJson('提交失败');
        } else {
            return $this->successJson('提交成功');
        }

    }

    /**
     * 页面
     * @return string
     */
    public function storePage()
    {
        return view('setting.work-order.store-page', [
            'site_url' => json_encode($_SERVER['HTTP_HOST']),
            'category_list' => json_encode($this->category()),
            'first_list' => json_encode($this->firstList()),
        ])->render();
    }

    /**
     * 获取秘钥
     */
    private function getKey()
    {
        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(config('auto-update.workOrderUrl') . '/work-order/get-user-key')
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->withData(['domain' => $_SERVER['HTTP_HOST'], 'work_order' => 1])
            ->asJsonResponse(true)
            ->post();
        if ($data['result'] == 1 && $data) {
            return $data['data'];
        } else {
            return false;
        }
    }

    /**
     * 第一次提交数据
     * @return array
     */
    private function firstList()
    {
        $webSiteInfo = WebSiteInfo::where('website_url', $_SERVER['HTTP_HOST'])->first();
        $key = $this->getKey();

        if ($key == false) {
            return [
                "website_url" => $_SERVER['HTTP_HOST'],
            ];
        }

        return [
            "id" => $webSiteInfo->id,
            "uniacid" => $webSiteInfo->uniacid,
            "website_url" => $webSiteInfo->website_url,
            "founder_account" => $this->decryption($webSiteInfo->founder_account, $key),
            "founder_password" => $this->decryption($webSiteInfo->founder_password, $key),
            "server_ip" => $this->decryption($webSiteInfo->server_ip, $key),
            "root_password" => $this->decryption($webSiteInfo->root_password, $key),
            "ssh_port" => $this->decryption($webSiteInfo->ssh_port, $key),
            "database_address" => $this->decryption($webSiteInfo->database_address, $key),
            "database_username" => $this->decryption($webSiteInfo->database_username, $key),
            "database_password" => $this->decryption($webSiteInfo->database_password, $key),
            "root_directory" => $this->decryption($webSiteInfo->root_directory, $key),
            "qq" => $this->decryption($webSiteInfo->qq, $key),
            "mobile" => $this->decryption($webSiteInfo->mobile, $key),
        ];
    }

    /**
     * 解密
     * @param $string
     * @param $key
     * @return string
     */
    private function encipherment($string, $key)
    {
        return openssl_encrypt($string, 'DES-ECB', $key, 0);
    }

    /**
     * 加密
     * @param $string
     * @param $key
     * @return string
     */
    private function decryption($string, $key)
    {
        return openssl_decrypt($string, 'DES-ECB', $key, 0);
    }

    /**
     * 生成获取秘钥
     * @return bool
     */
    private function getEncryptionKey()
    {
        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(config('auto-update.workOrderUrl') . '/work-order/get-key')
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->withData(['domain' => $_SERVER['HTTP_HOST'], 'work_order' => 1])
            ->asJsonResponse(true)
            ->post();
        if ($data['result'] == 1 && $data) {
            return $data['data'];
        } else {
            return false;
        }
    }

    /**
     * 工单提交接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $encryptionKey = $this->getEncryptionKey();
        if ($encryptionKey == false) {
            $this->errorJson('获取秘钥失败');
        }

        $postData = request()->input('data');
        $this->validateParam();

        $data = [
            'work_order' => '1',
            'domain' => $_SERVER['HTTP_HOST'],
            'category_id' => $postData['category_id'],
            'question_title' => $postData['question_title'],
            'question_describe' => $postData['question_describe'],
            'thumb_url' => json_encode($postData['thumb_url']),
            'user_data' => [
                'website_url' => $_SERVER['HTTP_HOST'],
                'founder_account' => $this->encipherment($postData['first_list']['founder_account'], $encryptionKey),
                'founder_password' => $this->encipherment($postData['first_list']['founder_password'], $encryptionKey),
                'server_ip' => $this->encipherment($postData['first_list']['server_ip'], $encryptionKey),
                'root_password' => $this->encipherment($postData['first_list']['root_password'], $encryptionKey),
                'ssh_port' => $this->encipherment($postData['first_list']['ssh_port'], $encryptionKey),
                'database_address' => $this->encipherment($postData['first_list']['database_address'], $encryptionKey),
                'database_username' => $this->encipherment($postData['first_list']['database_username'], $encryptionKey),
                'database_password' => $this->encipherment($postData['first_list']['database_password'], $encryptionKey),
                'root_directory' => $this->encipherment($postData['first_list']['root_directory'], $encryptionKey),
                'qq' => $this->encipherment($postData['first_list']['qq'], $encryptionKey),
                'mobile' => $this->encipherment($postData['first_list']['mobile'], $encryptionKey),
            ],
        ];

        $userData = $data['user_data'];
        $userData['uniacid'] = \YunShop::app()->uniacid;
        WebSiteInfo::updateOrCreate(['website_url' => $userData['website_url']], $userData);
        $key = \Setting::get('shop.key')['key'];
        $secret = \Setting::get('shop.key')['secret'];
        $data = \Curl::to(config('auto-update.workOrderUrl') . '/work-order/index')
            ->withHeader("Authorization: Basic " . base64_encode("{$key}:{$secret}"))
            ->withData($data)
            ->asJsonResponse(true)
            ->post();
        if (!$data) {
            $this->errorJson('远端服务器异常');
        }
        if ($data['result'] == 0) {
            return $this->errorJson($data['msg']);
        } else {
            return $this->successJson('提交成功');
        }
    }

    /**
     * 状态
     * @return array
     */
    private function status()
    {
        return [
            0 => [
                'id' => 0,
                'name' => '全部',
            ],
            1 => [
                'id' => 1,
                'name' => '未处理',
            ],
            2 => [
                'id' => 2,
                'name' => '处理中',
            ],
            3 => [
                'id' => 3,
                'name' => '已处理',
            ],
        ];
    }

    /**
     * 验证
     */
    protected function validateParam()
    {
        $this->validate([
            'data.category_id' => 'required | integer',
            'data.question_title' => 'required',
            'data.question_describe' => 'required | max:80',
            'data.first_list.founder_account' => 'required | max:80',
            'data.first_list.founder_password' => 'required | max:80',
            'data.first_list.server_ip' => 'required | max:80',
            'data.first_list.ssh_port' => 'required | max:80',
            'data.first_list.database_address' => 'required | max:80',
            'data.first_list.database_username' => 'required | max:80',
            'data.first_list.root_password' => 'required | max:80',
            'data.first_list.qq' => 'required | max:80',
            'data.first_list.mobile' => 'required | max:80',
        ]);
    }

    /**
     * 验证评论参数
     */
    protected function validateCommentParam()
    {
        $this->validate([
            'content' => 'required',
            'work_order_id' => 'required | integer',
        ]);
    }

    /**
     * 分类
     * @return array
     */
    private function category()
    {
        return [
            0 => [
                'id' => 0,
                'name' => '全部',
            ],
            1 => [
                'id' => 1,
                'name' => 'bug提交',
            ],
            2 => [
                'id' => 2,
                'name' => '优化建议',
            ],
            3 => [
                'id' => '3',
                'name' => '开发需求',
            ],
            4 => [
                'id' => 4,
                'name' => '其他',
            ],
        ];
    }
}
