<?php

namespace app\common\components;

use app\common\exceptions\AppException;

use app\common\middleware\BasicInformation;
use app\common\services\Check;
use app\common\services\PermissionService;
use app\common\services\Session;

use app\common\traits\JsonTrait;
use app\common\traits\MessageTrait;
use app\common\traits\PermissionTrait;
use app\common\traits\TemplateTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

/**
 * controller基类
 *
 * Author: 芸众商城 www.yunzshop.com
 * Date: 21/02/2017
 * Time: 21:20
 */
class BaseController extends Controller
{
    use DispatchesJobs, MessageTrait, ValidatesRequests, TemplateTrait, PermissionTrait, JsonTrait;

    const SESSION_EXPIRE = 2160000;

    /**
     * controller中执行报错需要回滚的action数组
     * @var array
     */
    public $transactionActions = [];

    public $apiErrMsg = [];

    public $apiData = [];

    protected $isPublic = false;


    public function __construct()
    {
        $this->setCookie();
        if (strpos(request()->getRequestUri(), '/addons/') !== false &&
            strpos(request()->getRequestUri(), '/api.php') !== false
        ) {
            $this->middleware(BasicInformation::class);
        }
    }

    /**
     * 前置action
     */

    public function preAction()
    {
        //是否为商城后台管理路径
        if (config('app.framework') == 'platform') {
            strpos(request()->getRequestUri(), config('app.isWeb')) === 0 && Check::setKey();
        } else {
            strpos(request()->getBaseUrl(), '/web/index.php') === 0 && Check::setKey();
        }
        if (\YunShop::isWeb()) {
            if (!$this->isPublic) {
                PermissionService::validate();
            }
        }
    }

    protected function formatValidationErrors(Validator $validator)
    {
        return $validator->errors()->all();
    }

    /**
     * url参数验证
     *
     * @param array $rules
     * @param Request|null $request
     * @param array $messages
     * @param array $customAttributes
     *
     * @throws AppException
     */
    public function validate(array $rules, Request $request = null, array $messages = [], array $customAttributes = [])
    {
        if (!isset($request)) {
            $request = request();
        }
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new AppException($validator->errors()->first());
        }
    }

    /**
     * 设置Cookie存储
     *
     * @return void
     */
    protected function setCookie()
    {
        $session_id = '';
        if (isset(\YunShop::request()->state) && !empty(\YunShop::request()->state) && strpos(\YunShop::request()->state, 'yz-')) {
            $pieces = explode('-', \YunShop::request()->state);
            $session_id = $pieces[1];
            unset($pieces);
        }

        if (isset($_COOKIE[session_name()])) {
            $session_id = $_COOKIE[session_name()];
        }

        //h5 app
        if (!empty($_REQUEST['uuid'])) {
            $session_id = md5($_REQUEST['uuid']);
            setcookie(session_name(), $session_id);
        }

        if (\YunShop::request()->type == 2 && \YunShop::request()->session_id
            && \YunShop::request()->session_id != 'undefined' && \YunShop::request()->session_id != 'null'
        ) {
            $session_id = \YunShop::request()->session_id;
            setcookie(session_name(), $session_id);
        }

        if (empty($session_id)) {
            $session_id = md5(\YunShop::app()->uniacid . ':' . random(20));
            setcookie(session_name(), $session_id);
        }

        session_id($session_id);
        Session::factory(\YunShop::app()->uniacid);
    }

    /**
     * 需要回滚
     *
     * @param $action
     *
     * @return bool
     */
    public function needTransaction($action)
    {
        return in_array($action, $this->transactionActions) || in_array('*',
                $this->transactionActions) || $this->transactionActions == '*';
    }

    public function dataIntegrated($data, $flag)
    {
        if ($this->apiErrMsg) {
            return $this->successJson($this->apiErrMsg[0]);
        }

        if (0 == $data['status']) {
            $this->apiErrMsg[] = $data['json'];

            return;
        }

        $this->apiData[$flag] = $data['json'];
    }

}
